<?php

namespace App\Services;

use App\Http\Controllers\SaveController;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Builds the "Saving tracker" report for one user.
 * Money values are kept to 6 decimals so riel totals convert back exactly.
 *
 * Entries keep the currency they were typed in; plans and goals are stored in
 * USD (see the saving tracker page). Everything is converted to USD here and
 * the view formats it in the currency the reader picked.
 */
class SavingReportService
{
    public function build(User $user, CarbonInterface $from, CarbonInterface $to, string $currency = 'USD'): array
    {
        $rate = SaveController::KHR_PER_USD;
        $toUsd = fn ($amount, $code) => (float) $amount / (($code ?? 'USD') === 'KHR' ? $rate : 1);

        $state = json_decode((string) DB::table('saving_tracker_states')->where('user_id', $user->id)->value('data'), true) ?: [];
        // Older data stored amounts in the currency selected at the time.
        $legacyCurrency = ($state['amountBase'] ?? null) === 'USD' ? null : ($state['currency'] ?? 'USD');

        $all = DB::table('saving_entries')
            ->where('user_id', $user->id)
            ->get(['client_id', 'kind', 'amount', 'currency', 'category', 'date', 'note', 'source'])
            ->map(function ($e) use ($toUsd, $legacyCurrency) {
                $code = $e->currency ?: ($legacyCurrency ?? 'USD');
                $e->currency = $code;
                $e->usd = $toUsd($e->amount, $code);
                $e->date = substr((string) $e->date, 0, 10);
                return $e;
            });

        $inRange = fn (CarbonInterface $a, CarbonInterface $b) => $all->filter(fn ($e) => $e->date >= $a->toDateString() && $e->date <= $b->toDateString());
        $entries = $inRange($from, $to)->values();

        // Same-length window just before the selected one, for trend chips.
        $days = (int) round($from->copy()->startOfDay()->diffInDays($to->copy()->startOfDay())) + 1;
        $prevTo = $from->copy()->subDay();
        $prevFrom = $prevTo->copy()->subDays($days - 1);
        $previous = $inRange($prevFrom, $prevTo);

        $sum = fn (Collection $c, string $kind) => round($c->where('kind', $kind)->sum('usd'), 6);
        $saved = $sum($entries, 'saved');
        $spent = $sum($entries, 'spent');
        $flow = $saved + $spent;

        return [
            'currency' => $currency === 'KHR' ? 'KHR' : 'USD',
            'rate' => $rate,
            'from' => $from,
            'to' => $to,
            'days' => $days,
            'totals' => [
                'saved' => $saved,
                'spent' => $spent,
                'net' => round($saved - $spent, 6),
                'savingsRate' => $flow > 0 ? round($saved / $flow * 100, 1) : 0,
                'entries' => $entries->count(),
                'avgSavedPerDay' => round($saved / max(1, $days), 6),
                'activeDays' => $entries->where('kind', 'saved')->pluck('date')->unique()->count(),
            ],
            'previous' => [
                'saved' => $sum($previous, 'saved'),
                'spent' => $sum($previous, 'spent'),
                'net' => round($sum($previous, 'saved') - $sum($previous, 'spent'), 6),
            ],
            'series' => $this->series($entries, $from, $to),
            'savedByCategory' => $this->byCategory($entries, 'saved'),
            'spentByCategory' => $this->byCategory($entries, 'spent'),
            'currencyMix' => [
                'USD' => ['count' => $entries->where('currency', 'USD')->count(), 'usd' => round($entries->where('currency', 'USD')->sum('usd'), 6)],
                'KHR' => ['count' => $entries->where('currency', 'KHR')->count(), 'usd' => round($entries->where('currency', 'KHR')->sum('usd'), 6), 'riel' => round($entries->where('currency', 'KHR')->sum('amount'))],
            ],
            'bestDay' => $this->bestDay($entries),
            'goals' => $this->goals($state, $all, $legacyCurrency, $rate),
            'plans' => $this->plans($state, $legacyCurrency, $rate),
            'recent' => $entries->sortByDesc(fn ($e) => $e->date . $e->client_id)->take(8)->values(),
        ];
    }

    /** Daily buckets for short ranges (≤ 62 days), monthly otherwise. */
    private function series(Collection $entries, CarbonInterface $from, CarbonInterface $to): array
    {
        $daily = $from->diffInDays($to) <= 62;
        $keyFormat = $daily ? 'Y-m-d' : 'Y-m';
        $buckets = [];
        $cursor = $daily ? $from->copy() : $from->copy()->startOfMonth();
        while ($cursor->lte($to)) {
            $buckets[$cursor->format($keyFormat)] = ['label' => $cursor->format($daily ? 'M d' : 'M Y'), 'saved' => 0.0, 'spent' => 0.0];
            $daily ? $cursor->addDay() : $cursor->addMonth();
        }
        foreach ($entries as $e) {
            $key = $daily ? $e->date : substr($e->date, 0, 7);
            if (isset($buckets[$key])) {
                $buckets[$key][$e->kind === 'spent' ? 'spent' : 'saved'] += $e->usd;
            }
        }
        $running = 0.0;
        $out = ['labels' => [], 'saved' => [], 'spent' => [], 'net' => [], 'balance' => [], 'daily' => $daily];
        foreach ($buckets as $b) {
            $net = $b['saved'] - $b['spent'];
            $running += $net;
            $out['labels'][] = $b['label'];
            $out['saved'][] = round($b['saved'], 6);
            $out['spent'][] = round($b['spent'], 6);
            $out['net'][] = round($net, 6);
            $out['balance'][] = round($running, 6);
        }
        return $out;
    }

    private function byCategory(Collection $entries, string $kind): array
    {
        $rows = $entries->where('kind', $kind)->groupBy(fn ($e) => $e->category ?: 'Other')
            ->map(fn ($g, $name) => ['name' => $name, 'usd' => round($g->sum('usd'), 6), 'count' => $g->count()])
            ->sortByDesc('usd')->values();
        $total = max(0.01, $rows->sum('usd'));
        return $rows->map(fn ($r) => $r + ['share' => round($r['usd'] / $total * 100, 1)])->all();
    }

    private function bestDay(Collection $entries): ?array
    {
        $day = $entries->where('kind', 'saved')->groupBy('date')->map(fn ($g) => $g->sum('usd'))->sortDesc();
        return $day->isEmpty() ? null : ['date' => $day->keys()->first(), 'usd' => round($day->first(), 6)];
    }

    private function goals(array $state, Collection $all, ?string $legacyCurrency, int $rate): array
    {
        $fix = fn ($v) => (float) $v / ($legacyCurrency === 'KHR' ? $rate : 1);
        $month = now()->format('Y-m');
        $year = now()->format('Y');
        $monthTarget = isset($state['monthlyTargets'][$month]) ? $fix($state['monthlyTargets'][$month]) : 0;
        $yearTarget = isset($state['yearlyTargets'][$year]) ? $fix($state['yearlyTargets'][$year]) : 0;
        $monthSaved = $all->filter(fn ($e) => $e->kind === 'saved' && str_starts_with($e->date, $month))->sum('usd');
        $yearSaved = $all->filter(fn ($e) => $e->kind === 'saved' && str_starts_with($e->date, $year))->sum('usd');

        return [
            'month' => ['label' => now()->format('F Y'), 'target' => round($monthTarget, 6), 'saved' => round($monthSaved, 6), 'pct' => $monthTarget > 0 ? min(100, round($monthSaved / $monthTarget * 100, 1)) : null],
            'year' => ['label' => $year, 'target' => round($yearTarget, 6), 'saved' => round($yearSaved, 6), 'pct' => $yearTarget > 0 ? min(100, round($yearSaved / $yearTarget * 100, 1)) : null],
        ];
    }

    private function plans(array $state, ?string $legacyCurrency, int $rate): array
    {
        $fix = fn ($v) => (float) $v / ($legacyCurrency === 'KHR' ? $rate : 1);
        return collect($state['plans'] ?? [])->map(function ($p) use ($fix) {
            $price = $fix($p['price'] ?? 0);
            $saved = $fix($p['saved'] ?? 0);
            $deadline = isset($p['deadline']) ? Carbon::parse($p['deadline']) : null;
            return [
                'name' => $p['name'] ?? 'Plan',
                'type' => $p['type'] ?? 'Other',
                'price' => round($price, 6),
                'saved' => round($saved, 6),
                'pct' => $price > 0 ? min(100, round($saved / $price * 100, 1)) : 0,
                'deadline' => $deadline?->toDateString(),
                'daysLeft' => $deadline ? (int) now()->startOfDay()->diffInDays($deadline, false) : null,
            ];
        })->sortBy('pct')->values()->all();
    }
}
