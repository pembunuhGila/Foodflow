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
        if (Schema::hasTable('ingredients') && ! Schema::hasColumn('ingredients', 'current_stock')) {
            Schema::table('ingredients', function (Blueprint $table) {
                $table->decimal('current_stock', 12, 2)->nullable()->default(null)->after('minimum_stock');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ingredients') && Schema::hasColumn('ingredients', 'current_stock')) {
            Schema::table('ingredients', function (Blueprint $table) {
                $table->dropColumn('current_stock');
            });
        }
    }
};
