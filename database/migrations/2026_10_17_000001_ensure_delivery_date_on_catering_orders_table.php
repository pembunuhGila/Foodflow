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
        if (Schema::hasTable('catering_orders')) {
            Schema::table('catering_orders', function (Blueprint $table) {
                if (! Schema::hasColumn('catering_orders', 'delivery_date')) {
                    if (Schema::hasColumn('catering_orders', 'required_date')) {
                        $table->renameColumn('required_date', 'delivery_date');
                    } else {
                        $table->date('delivery_date')->nullable();
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('catering_orders') && Schema::hasColumn('catering_orders', 'delivery_date')) {
            Schema::table('catering_orders', function (Blueprint $table) {
                if (! Schema::hasColumn('catering_orders', 'required_date')) {
                    $table->renameColumn('delivery_date', 'required_date');
                }
            });
        }
    }
};
