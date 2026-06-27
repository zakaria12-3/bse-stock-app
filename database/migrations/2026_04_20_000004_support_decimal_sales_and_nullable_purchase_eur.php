<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('purchase_price_eur', 15, 3)->nullable()->default(null)->change();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 3)->default(0)->change();
            $table->decimal('global_discount', 15, 3)->default(0)->change();
            $table->decimal('total_discount', 15, 3)->default(0)->change();
            $table->decimal('total', 15, 3)->default(0)->change();
            $table->decimal('cash_received', 15, 3)->default(0)->change();
            $table->decimal('change', 15, 3)->default(0)->change();
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('cost_price', 15, 3)->default(0)->change();
            $table->decimal('unit_price', 15, 3)->default(0)->change();
            $table->decimal('discount', 15, 3)->default(0)->change();
            $table->decimal('final_price', 15, 3)->default(0)->change();
            $table->decimal('subtotal', 15, 3)->default(0)->change();
        });

        Schema::table('finance_transactions', function (Blueprint $table) {
            $table->decimal('amount', 15, 3)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('finance_transactions', function (Blueprint $table) {
            $table->bigInteger('amount')->default(0)->change();
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->bigInteger('cost_price')->default(0)->change();
            $table->bigInteger('unit_price')->default(0)->change();
            $table->bigInteger('discount')->default(0)->change();
            $table->bigInteger('final_price')->default(0)->change();
            $table->bigInteger('subtotal')->default(0)->change();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->bigInteger('subtotal')->default(0)->change();
            $table->bigInteger('global_discount')->default(0)->change();
            $table->bigInteger('total_discount')->default(0)->change();
            $table->bigInteger('total')->default(0)->change();
            $table->bigInteger('cash_received')->default(0)->change();
            $table->bigInteger('change')->default(0)->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('purchase_price_eur', 15, 3)->default(0)->nullable(false)->change();
        });
    }
};
