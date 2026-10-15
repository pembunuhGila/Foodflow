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
        if (! Schema::hasTable('product_ingredients')) {
            Schema::create('product_ingredients', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('ingredient_id')->constrained('ingredients')->cascadeOnDelete();
                $table->decimal('quantity_per_unit', 10, 3)->default(0);
                $table->string('unit')->default('gram');
                $table->timestamps();

                $table->unique(['product_id', 'ingredient_id']);
            });
        } else {
            Schema::table('product_ingredients', function (Blueprint $table) {
                if (! Schema::hasColumn('product_ingredients', 'quantity_per_unit')) {
                    $table->decimal('quantity_per_unit', 10, 3)->default(0)->after('ingredient_id');
                }
                if (! Schema::hasColumn('product_ingredients', 'unit')) {
                    $table->string('unit')->default('gram')->after('quantity_per_unit');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_ingredients');
    }
};
