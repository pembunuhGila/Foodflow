<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_number', 50)->unique();
                $table->foreignId('vendor_id')->constrained('vendors')->restrictOnDelete();
                $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
                $table->date('order_date');
                $table->date('expected_date')->nullable();
                $table->string('status', 30)->default('draft');
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        } elseif (Schema::hasColumn('purchase_orders', 'po_number')
            && ! Schema::hasColumn('purchase_orders', 'order_number')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->renameColumn('po_number', 'order_number');
            });
        }

        if (! Schema::hasTable('purchase_order_details')) {
            Schema::create('purchase_order_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
                $table->foreignId('ingredient_id')->constrained('ingredients')->restrictOnDelete();
                $table->decimal('quantity', 10, 2);
                $table->string('unit', 50);
                $table->decimal('price', 15, 2);
                $table->decimal('subtotal', 15, 2);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        } elseif (Schema::hasColumn('purchase_order_details', 'unit_price')
            && ! Schema::hasColumn('purchase_order_details', 'price')) {
            Schema::table('purchase_order_details', function (Blueprint $table) {
                $table->renameColumn('unit_price', 'price');
            });
        }
    }

    public function down(): void
    {
        // Sengaja dikosongkan: tabel PO dipakai bersama tim dan tidak boleh dihapus lewat rollback.
    }
};
