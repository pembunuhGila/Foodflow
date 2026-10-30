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
        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('category')->nullable();
                $table->text('description')->nullable();
                $table->string('unit')->default('porsi');
                $table->decimal('selling_price', 12, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->decimal('calories', 8, 2)->default(0);
                $table->decimal('protein', 8, 2)->default(0);
                $table->decimal('fat', 8, 2)->default(0);
                $table->decimal('carbohydrates', 8, 2)->default(0);
                $table->decimal('dietary_fiber', 8, 2)->default(0);
                $table->timestamps();
            });
        } else {
            Schema::table('products', function (Blueprint $table) {
                if (! Schema::hasColumn('products', 'calories')) {
                    $table->decimal('calories', 8, 2)->default(0)->after('is_active');
                }
                if (! Schema::hasColumn('products', 'protein')) {
                    $table->decimal('protein', 8, 2)->default(0)->after('calories');
                }
                if (! Schema::hasColumn('products', 'fat')) {
                    $table->decimal('fat', 8, 2)->default(0)->after('protein');
                }
                if (! Schema::hasColumn('products', 'carbohydrates')) {
                    $table->decimal('carbohydrates', 8, 2)->default(0)->after('fat');
                }
                if (! Schema::hasColumn('products', 'dietary_fiber')) {
                    $table->decimal('dietary_fiber', 8, 2)->default(0)->after('carbohydrates');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $columns = ['calories', 'protein', 'fat', 'carbohydrates', 'dietary_fiber'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('products', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
