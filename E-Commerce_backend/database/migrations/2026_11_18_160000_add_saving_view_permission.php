<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('permissions')->updateOrInsert(
            ['name' => 'saving.view'],
            [
                'display_name' => 'View Saving Tracker',
                'group' => 'Saving Tracker',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('permissions')->where('name', 'saving.view')->delete();
    }
};
