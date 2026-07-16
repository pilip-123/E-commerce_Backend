<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_code_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_code_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['discount_code_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_code_user');
    }
};
