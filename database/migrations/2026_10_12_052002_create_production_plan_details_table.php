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
        Schema::create('production_plan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_plan_id')
                ->constrained('production_plans')
                ->cascadeOnDelete();
            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();
            $table->unsignedInteger('planned_quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_plan_details');
    }
};
