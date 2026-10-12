<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('unit');
            $table->decimal('minimum_stock', 12, 3)->default(0);
            $table->decimal('calories', 10, 2)->nullable();
            $table->decimal('protein', 10, 2)->nullable();
            $table->decimal('fat', 10, 2)->nullable();
            $table->decimal('carbohydrates', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
