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
        Schema::create('material_request_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_request_id')
                ->constrained('material_requests')
                ->cascadeOnDelete();
            $table->foreignId('ingredient_id')
                ->constrained('ingredients')
                ->restrictOnDelete();
            $table->decimal('requested_quantity', 12, 3);
            $table->decimal('approved_quantity', 12, 3)->default(0);
            $table->decimal('issued_quantity', 12, 3)->default(0);
            $table->string('unit');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_request_details');
    }
};
