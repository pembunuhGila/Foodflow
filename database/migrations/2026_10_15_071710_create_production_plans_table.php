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
        if (! Schema::hasTable('production_plans')) {
            Schema::create('production_plans', function (Blueprint $table) {
                $table->id();
                $table->string('plan_number', 50)->unique();
                $table->unsignedBigInteger('catering_order_id')->nullable();
                $table->date('plan_date');
                $table->date('production_date');
                $table->string('status', 30)->default('draft');
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('production_plan_details')) {
            Schema::create('production_plan_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('production_plan_id')->constrained('production_plans')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->unsignedInteger('planned_quantity');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('production_plan_details', function (Blueprint $table) {
                if (! Schema::hasColumn('production_plan_details', 'notes')) {
                    $table->text('notes')->nullable()->after('planned_quantity');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop existing tables in production/shared Supabase if they pre-existed
        if (Schema::hasTable('production_plan_details') && Schema::hasColumn('production_plan_details', 'notes')) {
            Schema::table('production_plan_details', function (Blueprint $table) {
                $table->dropColumn('notes');
            });
        }
    }
};
