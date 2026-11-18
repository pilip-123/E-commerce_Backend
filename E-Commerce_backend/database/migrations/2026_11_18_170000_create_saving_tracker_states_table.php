<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('saving_tracker_states')) {
            Schema::create('saving_tracker_states', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
                $table->json('data');
                $table->timestamps();
            });
        }

        // Carry forward any entries saved by the earlier entry-only table.
        if (Schema::hasTable('saving_entries')) {
            DB::table('saving_entries')->orderBy('id')->get()->groupBy('user_id')->each(function ($rows, $userId) {
                $entries = $rows->map(fn ($row) => [
                    'id' => (string) $row->id,
                    'kind' => $row->type,
                    'amount' => (float) $row->amount,
                    'category' => $row->category,
                    'date' => $row->entry_date,
                    'note' => $row->note,
                    'source' => 'manual',
                ])->values()->all();

                DB::table('saving_tracker_states')->updateOrInsert(
                    ['user_id' => $userId],
                    [
                        'data' => json_encode([
                            'entries' => $entries,
                            'plans' => [],
                            'monthlyTargets' => [],
                            'yearlyTargets' => [],
                            'notes' => [],
                            'currency' => 'USD',
                        ], JSON_THROW_ON_ERROR),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('saving_tracker_states');
    }
};
