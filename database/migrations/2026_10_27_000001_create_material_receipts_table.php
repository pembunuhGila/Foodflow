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
        if (! Schema::hasTable('material_receipts')) {
            Schema::create('material_receipts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
                $table->foreignId('received_by')->constrained('users')->restrictOnDelete();
                $table->string('receipt_number', 50)->unique();
                $table->date('receipt_date');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('material_receipt_details')) {
            Schema::create('material_receipt_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('material_receipt_id')->constrained('material_receipts')->cascadeOnDelete();
                $table->foreignId('ingredient_id')->constrained('ingredients')->restrictOnDelete();
                $table->decimal('quantity', 12, 3);
                $table->string('unit', 50);
                $table->string('condition', 50)->default('baik');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_receipt_details');
        Schema::dropIfExists('material_receipts');
    }
};
