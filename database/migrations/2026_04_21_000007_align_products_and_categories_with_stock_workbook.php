<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'worksheet_name')) {
                $table->string('worksheet_name', 100)->nullable()->after('code');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'source_sheet_name')) {
                $table->string('source_sheet_name', 100)->nullable()->after('category_id');
            }

            if (!Schema::hasColumn('products', 'new_unit_pr_ht')) {
                $table->decimal('new_unit_pr_ht', 15, 3)->nullable()->after('unit_pr_ht');
            }

            if (!Schema::hasColumn('products', 'cump_after_entry')) {
                $table->decimal('cump_after_entry', 15, 3)->nullable()->after('new_unit_pr_ht');
            }

            if (!Schema::hasColumn('products', 'exit_quantity')) {
                $table->integer('exit_quantity')->default(0)->after('quantity');
            }

            if (!Schema::hasColumn('products', 'reserved_quantity')) {
                $table->integer('reserved_quantity')->default(0)->after('exit_quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            foreach (['source_sheet_name', 'new_unit_pr_ht', 'cump_after_entry', 'exit_quantity', 'reserved_quantity'] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'worksheet_name')) {
                $table->dropColumn('worksheet_name');
            }
        });
    }
};
