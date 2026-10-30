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
        if (! Schema::hasTable('ingredients')) {
            Schema::create('ingredients', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('category')->nullable();
                $table->string('unit', 50);
                $table->decimal('minimum_stock', 12, 2)->default(0);
                $table->decimal('calories', 8, 2)->nullable()->default(0);
                $table->decimal('protein', 8, 2)->nullable()->default(0);
                $table->decimal('fat', 8, 2)->nullable()->default(0);
                $table->decimal('carbohydrates', 8, 2)->nullable()->default(0);
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
