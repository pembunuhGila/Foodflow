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
        if (! Schema::hasTable('productions')) {
            Schema::create('productions', function (Blueprint $table) {
                $table->id();
                $table->string('production_number', 50)->nullable()->unique();
                $table->foreignId('production_plan_detail_id')
                    ->constrained('production_plan_details')
                    ->cascadeOnDelete();
                $table->foreignId('created_by')
                    ->constrained('users')
                    ->restrictOnDelete();
                $table->date('production_date');
                $table->unsignedInteger('actual_quantity')->default(0);
                $table->string('status', 30)->default('partial');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['production_plan_detail_id', 'status']);
            });
        } else {
            Schema::table('productions', function (Blueprint $table) {
                if (! Schema::hasColumn('productions', 'notes')) {
                    $table->text('notes')->nullable();
                }
                if (! Schema::hasColumn('productions', 'production_number')) {
                    $table->string('production_number', 50)->nullable()->unique();
                }
                if (! Schema::hasColumn('productions', 'status')) {
                    $table->string('status', 30)->default('partial');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Sengaja dikosongkan: tabel dan data productions digunakan bersama tim dan tidak boleh dihapus saat rollback.
    }
};
