<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->unsignedInteger('total_returned')->nullable()->after('total_amount')
                ->comment('Total units returned when completed');
            $table->timestamp('credited_at')->nullable()->after('notes')
                ->comment('When the return value was credited back');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->dropColumn(['total_returned', 'credited_at']);
        });
    }
};
