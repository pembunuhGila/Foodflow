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
        Schema::create('material_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_plan_id')
                ->constrained('production_plans')
                ->restrictOnDelete();
            $table->foreignId('requested_by')
                ->constrained('users')
                ->restrictOnDelete();
            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->date('request_date');
            $table->string('status')->default('Pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_requests');
    }
};
