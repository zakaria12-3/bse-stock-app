<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // The stock workbook leaves "Prix d'achat €" empty when there is no value.
        // Existing placeholder zeros should therefore be stored as NULL.
        DB::table('products')
            ->where('purchase_price_eur', 0)
            ->update(['purchase_price_eur' => null]);
    }

    public function down(): void
    {
        DB::table('products')
            ->whereNull('purchase_price_eur')
            ->update(['purchase_price_eur' => 0]);
    }
};
