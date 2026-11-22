<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SavingReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SavingReportTest extends TestCase
{
    use RefreshDatabase;

    private function entry(User $user, string $id, string $kind, float $amount, string $currency, string $date, string $category = 'General'): void
    {
        DB::table('saving_entries')->insert([
            'user_id' => $user->id, 'client_id' => $id, 'kind' => $kind, 'amount' => $amount, 'currency' => $currency,
            'category' => $category, 'date' => $date, 'source' => 'manual', 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function test_report_converts_currencies_and_compares_with_previous_period(): void
    {
        Carbon::setTestNow('2026-11-22 10:00:00');
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->entry($user, 'a', 'saved', 20, 'USD', '2026-11-10', 'Food');
        $this->entry($user, 'b', 'saved', 41000, 'KHR', '2026-11-12', 'General'); // = $10
        $this->entry($user, 'c', 'spent', 8200, 'KHR', '2026-11-15', 'Transport'); // = $2
        $this->entry($user, 'd', 'saved', 5, 'USD', '2026-10-25'); // previous window
        DB::table('saving_tracker_states')->insert([
            'user_id' => $user->id,
            'data' => json_encode([
                'currency' => 'USD', 'amountBase' => 'USD', 'notes' => [],
                'monthlyTargets' => ['2026-11' => 60], 'yearlyTargets' => ['2026' => 300],
                'plans' => [['id' => 'p1', 'name' => 'Bike', 'type' => 'Bike', 'price' => 100, 'saved' => 25, 'deadline' => '2026-12-22']],
            ]),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $report = app(SavingReportService::class)->build($user, Carbon::parse('2026-11-01'), Carbon::parse('2026-11-22'), 'KHR');

        $this->assertSame('KHR', $report['currency']);
        $this->assertEquals(30, $report['totals']['saved']);
        $this->assertEquals(2, $report['totals']['spent']);
        $this->assertEquals(28, $report['totals']['net']);
        $this->assertEquals(93.8, $report['totals']['savingsRate']);
        $this->assertEquals(5, $report['previous']['saved']);
        $this->assertSame(22, $report['days']);
        $this->assertCount(22, $report['series']['labels']);
        $this->assertEquals(28, end($report['series']['balance']));
        $this->assertSame('Food', $report['savedByCategory'][0]['name']);
        $this->assertSame(1, $report['currencyMix']['USD']['count']);
        $this->assertEquals(49200, $report['currencyMix']['KHR']['riel']);
        $this->assertEquals(50.0, $report['goals']['month']['pct']);   // $30 of $60
        $this->assertEquals(11.7, $report['goals']['year']['pct']);    // $35 of $300
        $this->assertEquals(25.0, $report['plans'][0]['pct']);
        $this->assertSame(30, $report['plans'][0]['daysLeft']);
    }
}
