<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed']);
            $table->decimal('discount_value', 8, 2);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('product_promotion', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('promotion_id')->constrained()->cascadeOnDelete();
            $table->primary(['product_id', 'promotion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_promotion');
        Schema::dropIfExists('promotions');
    }
};
