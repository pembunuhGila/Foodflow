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
        if (! Schema::hasTable('production_material_usages')) {
            Schema::create('production_material_usages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('production_plan_id')->constrained('production_plans')->cascadeOnDelete();
                $table->foreignId('ingredient_id')->constrained('ingredients')->restrictOnDelete();
                $table->decimal('planned_quantity', 12, 3)->default(0);
                $table->decimal('actual_quantity', 12, 3)->default(0);
                $table->decimal('difference_quantity', 12, 3)->default(0);
                $table->string('unit', 50);
                $table->text('notes')->nullable();
                $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
                $table->timestamps();

                $table->unique(['production_plan_id', 'ingredient_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_material_usages');
    }
};
