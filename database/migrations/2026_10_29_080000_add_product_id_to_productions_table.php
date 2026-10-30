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
        if (Schema::hasTable('productions')) {
            Schema::table('productions', function (Blueprint $table) {
                // Add nullable product_id column to link production to a product
                $table->unsignedBigInteger('product_id')->nullable()->after('production_plan_detail_id');
                $table->foreign('product_id')
                    ->references('id')
                    ->on('products')
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('productions')) {
            Schema::table('productions', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            });
        }
    }
};
