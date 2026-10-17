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
        if (Schema::hasTable('ingredients')) {
            Schema::table('ingredients', function (Blueprint $table) {
                if (! Schema::hasColumn('ingredients', 'current_stock')) {
                    $table->decimal('current_stock', 12, 2)->default(0)->after('minimum_stock');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ingredients')) {
            Schema::table('ingredients', function (Blueprint $table) {
                if (Schema::hasColumn('ingredients', 'current_stock')) {
                    $table->dropColumn('current_stock');
                }
            });
        }
    }
};
