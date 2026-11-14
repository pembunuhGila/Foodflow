<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Daftar seluruh tabel modul operasional yang dipasangi soft deletes.
     *
     * @var list<string>
     */
    protected array $tables = [
        'users',
        'customers',
        'vendors',
        'ingredients',
        'products',
        'product_ingredients',
        'catering_orders',
        'catering_order_details',
        'purchase_orders',
        'purchase_order_details',
        'material_receipts',
        'material_receipt_details',
        'material_requests',
        'material_request_details',
        'production_plans',
        'production_plan_details',
        'productions',
        'production_material_usages',
        'ingredient_stock_transactions',
        'password_reset_requests',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};
