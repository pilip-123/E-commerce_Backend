<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Each entry remembers the currency it was typed in, so a riel amount
     * stays exact (the amount column only keeps two decimals). Left nullable:
     * older rows get their currency from the tracker's saved setting on the
     * next page load.
     */
    public function up(): void
    {
        Schema::table('saving_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('saving_entries', 'currency')) {
                $table->string('currency', 3)->nullable()->after('amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('saving_entries', function (Blueprint $table) {
            if (Schema::hasColumn('saving_entries', 'currency')) {
                $table->dropColumn('currency');
            }
        });
    }
};
