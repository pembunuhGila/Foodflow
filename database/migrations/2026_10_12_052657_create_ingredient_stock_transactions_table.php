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
        Schema::create('ingredient_stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')
                ->constrained('ingredients')
                ->restrictOnDelete();
            $table->string('transaction_type');
            $table->decimal('quantity', 12, 3);
            $table->date('transaction_date');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
            $table->index(['ingredient_id', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredient_stock_transactions');
    }
};
