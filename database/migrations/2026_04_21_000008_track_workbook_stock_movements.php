<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'entry_quantity')) {
                $table->integer('entry_quantity')->default(0)->after('selling_price');
            }
        });

        DB::statement('UPDATE products SET entry_quantity = quantity WHERE COALESCE(entry_quantity, 0) = 0');

        $products = DB::table('products')
            ->select('id', 'entry_quantity', 'quantity', 'exit_quantity', 'reserved_quantity')
            ->get();

        foreach ($products as $product) {
            $entryQuantity = (int) ($product->entry_quantity ?? $product->quantity ?? 0);
            $exitQuantity = (int) ($product->exit_quantity ?? 0);
            $reservedQuantity = (int) ($product->reserved_quantity ?? 0);
            $availableQuantity = max($entryQuantity - $exitQuantity - $reservedQuantity, 0);

            DB::table('products')
                ->where('id', $product->id)
                ->update([
                    'entry_quantity' => $entryQuantity,
                    'quantity' => $availableQuantity,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'entry_quantity')) {
                $table->dropColumn('entry_quantity');
            }
        });
    }
};
