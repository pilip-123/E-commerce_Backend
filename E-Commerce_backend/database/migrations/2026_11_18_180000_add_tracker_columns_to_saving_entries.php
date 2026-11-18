<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saving_entries', function (Blueprint $table) {
            if (Schema::hasColumn('saving_entries', 'type') && ! Schema::hasColumn('saving_entries', 'kind')) {
                $table->renameColumn('type', 'kind');
            }
            if (Schema::hasColumn('saving_entries', 'entry_date') && ! Schema::hasColumn('saving_entries', 'date')) {
                $table->renameColumn('entry_date', 'date');
            }
            if (! Schema::hasColumn('saving_entries', 'client_id')) {
                $table->string('client_id', 100)->nullable();
            }
            if (! Schema::hasColumn('saving_entries', 'source')) {
                $table->string('source', 20)->default('manual');
            }
            if (! Schema::hasColumn('saving_entries', 'plan_id')) {
                $table->string('plan_id', 100)->nullable();
            }
        });

        DB::table('saving_entries')->whereNull('client_id')->orderBy('id')->get()->each(function ($entry) {
            DB::table('saving_entries')->where('id', $entry->id)->update([
                'client_id' => 'legacy-'.$entry->id,
            ]);
        });

        Schema::table('saving_entries', function (Blueprint $table) {
            $table->unique(['user_id', 'client_id'], 'saving_entries_user_client_unique');
        });

        // Move any state-snapshot entries into real database columns, then leave
        // the snapshot for plans, goals, currency, and formatted note history.
        DB::table('saving_tracker_states')->orderBy('id')->get()->each(function ($record) {
            $data = json_decode($record->data, true) ?: [];
            $oldRows = DB::table('saving_entries')->where('user_id', $record->user_id)->get()->keyBy('id');

            foreach (($data['entries'] ?? []) as $entry) {
                $entryId = (string) ($entry['id'] ?? uniqid('entry-', true));
                $clientId = ctype_digit($entryId) && isset($oldRows[(int) $entryId])
                    ? 'legacy-'.$entryId
                    : $entryId;

                DB::table('saving_entries')->updateOrInsert(
                    ['user_id' => $record->user_id, 'client_id' => $clientId],
                    [
                        'kind' => $entry['kind'] ?? 'saved',
                        'amount' => $entry['amount'] ?? 0,
                        'category' => $entry['category'] ?? 'General',
                        'date' => $entry['date'] ?? now()->toDateString(),
                        'note' => $entry['note'] ?? null,
                        'source' => $entry['source'] ?? 'manual',
                        'plan_id' => $entry['planId'] ?? null,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }

            unset($data['entries']);
            DB::table('saving_tracker_states')->where('id', $record->id)->update([
                'data' => json_encode($data, JSON_THROW_ON_ERROR),
                'updated_at' => now(),
            ]);
        });
    }

    public function down(): void
    {
        DB::table('saving_tracker_states')->orderBy('id')->get()->each(function ($record) {
            $data = json_decode($record->data, true) ?: [];
            $data['entries'] = DB::table('saving_entries')->where('user_id', $record->user_id)->get()->map(fn ($entry) => [
                'id' => $entry->client_id,
                'kind' => $entry->kind,
                'amount' => (float) $entry->amount,
                'category' => $entry->category,
                'date' => $entry->date,
                'note' => $entry->note,
                'source' => $entry->source,
                'planId' => $entry->plan_id,
            ])->values()->all();
            DB::table('saving_tracker_states')->where('id', $record->id)->update(['data' => json_encode($data, JSON_THROW_ON_ERROR)]);
        });

        Schema::table('saving_entries', function (Blueprint $table) {
            $table->dropUnique('saving_entries_user_client_unique');
            $table->dropColumn(['client_id', 'source', 'plan_id']);
            $table->renameColumn('kind', 'type');
            $table->renameColumn('date', 'entry_date');
        });
    }
};
