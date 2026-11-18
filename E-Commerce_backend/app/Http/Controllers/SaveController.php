<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaveController extends Controller
{
    /** Show the signed-in user's saving tracker and load its database state. */
    public function saving(): View
    {
        $storedData = DB::table('saving_tracker_states')
            ->where('user_id', auth()->id())
            ->value('data');
        $trackerState = $storedData ? json_decode($storedData, true) : [];
        $trackerState = is_array($trackerState) ? $trackerState : [];
        $trackerState['entries'] = DB::table('saving_entries')
            ->where('user_id', auth()->id())
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get()
            ->map(fn ($entry) => [
                'id' => $entry->client_id,
                'kind' => $entry->kind,
                'amount' => (float) $entry->amount,
                'category' => $entry->category,
                'date' => $entry->date,
                'note' => $entry->note,
                'source' => $entry->source,
                'planId' => $entry->plan_id,
            ])->values()->all();

        return view('admin.saveing.index', [
            'categories' => ['General', 'Emergency fund', 'Food', 'Transport', 'Bills', 'Shopping', 'Health', 'Travel', 'Other'],
            'planTypes' => ['Car', 'Motorcycle', 'Bike', 'Clothes', 'Trip', 'Other'],
            'currencies' => ['USD' => 'US Dollar ($)', 'KHR' => 'Cambodian Riel (៛)', 'EUR' => 'Euro (€)', 'GBP' => 'British Pound (£)', 'THB' => 'Thai Baht (฿)', 'VND' => 'Vietnamese Dong (₫)', 'JPY' => 'Japanese Yen (¥)', 'AUD' => 'Australian Dollar (A$)', 'CAD' => 'Canadian Dollar (C$)'],
            'trackerState' => $storedData || $trackerState['entries'] ? $trackerState : null,
        ]);
    }

    /** Save the complete tracker state for the signed-in account. */
    public function saveState(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'data' => ['required', 'array'],
            'data.entries' => ['present', 'array', 'max:10000'],
            'data.entries.*.id' => ['required', 'string', 'max:100'],
            'data.entries.*.kind' => ['required', 'in:saved,spent'],
            'data.entries.*.amount' => ['required', 'numeric', 'min:0.01'],
            'data.entries.*.category' => ['required', 'string', 'max:100'],
            'data.entries.*.date' => ['required', 'date'],
            'data.entries.*.note' => ['nullable', 'string', 'max:1000'],
            'data.entries.*.source' => ['nullable', 'in:manual,plan'],
            'data.entries.*.planId' => ['nullable', 'string', 'max:100'],
            'data.plans' => ['present', 'array', 'max:1000'],
            'data.plans.*.id' => ['required', 'string', 'max:100'],
            'data.plans.*.name' => ['required', 'string', 'max:70'],
            'data.plans.*.type' => ['required', 'in:Car,Motorcycle,Bike,Clothes,Trip,Other'],
            'data.plans.*.price' => ['required', 'numeric', 'min:0.01'],
            'data.plans.*.saved' => ['required', 'numeric', 'min:0'],
            'data.plans.*.deadline' => ['required', 'date'],
            'data.monthlyTargets' => ['present', 'array'],
            'data.monthlyTargets.*' => ['numeric', 'min:0'],
            'data.yearlyTargets' => ['present', 'array'],
            'data.yearlyTargets.*' => ['numeric', 'min:0'],
            'data.notes' => ['present', 'array', 'max:10000'],
            'data.notes.*.id' => ['required', 'string', 'max:100'],
            'data.notes.*.title' => ['required', 'string', 'max:100'],
            'data.notes.*.content' => ['nullable', 'string', 'max:100000'],
            'data.notes.*.savedAt' => ['required', 'date'],
            'data.notes.*.updatedAt' => ['nullable', 'date'],
            'data.notes.*.revisions' => ['nullable', 'array', 'max:1000'],
            'data.notes.*.revisions.*.title' => ['required', 'string', 'max:100'],
            'data.notes.*.revisions.*.content' => ['nullable', 'string', 'max:100000'],
            'data.notes.*.revisions.*.savedAt' => ['required', 'date'],
            'data.currency' => ['required', 'in:USD,KHR,EUR,GBP,THB,VND,JPY,AUD,CAD'],
        ]);

        $entries = $validated['data']['entries'];
        $snapshot = $validated['data'];
        unset($snapshot['entries']);

        $allData = json_encode($validated['data'], JSON_THROW_ON_ERROR);
        if (strlen($allData) > 5_000_000) {
            throw ValidationException::withMessages([
                'data' => 'Tracker data is too large to save. Please remove some old notes or entries.',
            ]);
        }
        $encoded = json_encode($snapshot, JSON_THROW_ON_ERROR);

        DB::transaction(function () use ($request, $entries, $encoded): void {
            $userId = $request->user()->id;
            $entryIds = [];

            foreach ($entries as $entry) {
                $clientId = $entry['id'];
                $entryIds[] = $clientId;
                $createdAt = DB::table('saving_entries')
                    ->where('user_id', $userId)
                    ->where('client_id', $clientId)
                    ->value('created_at') ?? now();

                DB::table('saving_entries')->updateOrInsert(
                    ['user_id' => $userId, 'client_id' => $clientId],
                    [
                        'kind' => $entry['kind'],
                        'amount' => $entry['amount'],
                        'category' => $entry['category'],
                        'date' => $entry['date'],
                        'note' => $entry['note'] ?? null,
                        'source' => $entry['source'] ?? 'manual',
                        'plan_id' => $entry['planId'] ?? null,
                        'created_at' => $createdAt,
                        'updated_at' => now(),
                    ]
                );
            }

            $entriesToDelete = DB::table('saving_entries')->where('user_id', $userId);
            if ($entryIds) {
                $entriesToDelete->whereNotIn('client_id', $entryIds);
            }
            $entriesToDelete->delete();

            $state = DB::table('saving_tracker_states')->where('user_id', $userId)->first();
            if ($state) {
                DB::table('saving_tracker_states')->where('id', $state->id)->update([
                    'data' => $encoded,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('saving_tracker_states')->insert([
                    'user_id' => $userId,
                    'data' => $encoded,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return response()->json(['saved' => true]);
    }
}
