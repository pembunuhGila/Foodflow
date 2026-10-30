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
        if (! Schema::hasTable('material_requests')) {
            Schema::create('material_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('production_plan_id')->nullable()->constrained('production_plans')->restrictOnDelete();
                $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                $table->date('request_date');
                $table->string('status', 255)->default('pending');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('material_request_details')) {
            Schema::create('material_request_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('material_request_id')->constrained('material_requests')->cascadeOnDelete();
                $table->foreignId('ingredient_id')->constrained('ingredients')->restrictOnDelete();
                $table->decimal('requested_quantity', 12, 3);
                $table->decimal('approved_quantity', 12, 3)->default(0);
                $table->decimal('issued_quantity', 12, 3)->default(0);
                $table->string('unit', 255);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('ingredient_stock_transactions')) {
            Schema::create('ingredient_stock_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ingredient_id')->constrained('ingredients')->restrictOnDelete();
                $table->string('transaction_type', 255);
                $table->decimal('quantity', 12, 3);
                $table->date('transaction_date');
                $table->string('reference_type', 255)->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
                $table->timestamps();

                $table->index(['reference_type', 'reference_id']);
                $table->index(['ingredient_id', 'transaction_date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Sengaja dikosongkan: tabel digunakan bersama tim dan tidak boleh dihapus saat rollback.
    }
};
