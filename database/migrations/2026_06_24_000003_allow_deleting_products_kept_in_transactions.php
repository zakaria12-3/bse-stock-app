<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildSqliteItemTables();
            return;
        }

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->string('product_reference', 50)->nullable()->after('product_id');
            $table->string('product_name', 150)->nullable()->after('product_reference');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->string('product_reference', 50)->nullable()->after('product_id');
            $table->string('product_name', 150)->nullable()->after('product_reference');
        });

        $this->backfillProductSnapshot('purchase_items');
        $this->backfillProductSnapshot('sale_items');

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreignId('product_id')->nullable()->change();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreignId('product_id')->nullable()->change();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreignId('product_id')->nullable(false)->change();
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
            $table->dropColumn(['product_reference', 'product_name']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreignId('product_id')->nullable(false)->change();
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
            $table->dropColumn(['product_reference', 'product_name']);
        });
    }

    private function backfillProductSnapshot(string $table): void
    {
        DB::table($table)
            ->whereNull('product_name')
            ->orderBy('id')
            ->chunkById(500, function ($items) use ($table) {
                $products = DB::table('products')
                    ->whereIn('id', $items->pluck('product_id')->filter()->unique()->values())
                    ->get(['id', 'reference', 'designation'])
                    ->keyBy('id');

                foreach ($items as $item) {
                    $product = $products->get($item->product_id);

                    if ($product) {
                        DB::table($table)
                            ->where('id', $item->id)
                            ->update([
                                'product_reference' => $product->reference,
                                'product_name' => $product->designation,
                            ]);
                    }
                }
            });
    }

    private function rebuildSqliteItemTables(): void
    {
        DB::statement('PRAGMA foreign_keys=OFF');

        DB::statement(<<<'SQL'
            CREATE TABLE purchase_items_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                purchase_id INTEGER NOT NULL,
                product_id INTEGER NULL,
                product_reference VARCHAR(50) NULL,
                product_name VARCHAR(150) NULL,
                quantity INTEGER NOT NULL,
                unit_price NUMERIC NOT NULL,
                selling_price NUMERIC NULL,
                subtotal NUMERIC NOT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
            )
        SQL);

        DB::statement(<<<'SQL'
            INSERT INTO purchase_items_new (
                id, purchase_id, product_id, product_reference, product_name, quantity,
                unit_price, selling_price, subtotal, created_at, updated_at
            )
            SELECT
                purchase_items.id,
                purchase_items.purchase_id,
                purchase_items.product_id,
                products.reference,
                products.designation,
                purchase_items.quantity,
                purchase_items.unit_price,
                purchase_items.selling_price,
                purchase_items.subtotal,
                purchase_items.created_at,
                purchase_items.updated_at
            FROM purchase_items
            LEFT JOIN products ON products.id = purchase_items.product_id
        SQL);

        DB::statement('DROP TABLE purchase_items');
        DB::statement('ALTER TABLE purchase_items_new RENAME TO purchase_items');
        DB::statement('CREATE INDEX purchase_items_purchase_id_product_id_index ON purchase_items (purchase_id, product_id)');

        DB::statement(<<<'SQL'
            CREATE TABLE sale_items_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                sale_id INTEGER NOT NULL,
                product_id INTEGER NULL,
                product_reference VARCHAR(50) NULL,
                product_name VARCHAR(150) NULL,
                quantity INTEGER NOT NULL,
                cost_price NUMERIC NOT NULL,
                unit_price NUMERIC NOT NULL,
                discount NUMERIC NOT NULL DEFAULT '0',
                labor_hours NUMERIC NOT NULL DEFAULT '0',
                labor_rate NUMERIC NOT NULL DEFAULT '0',
                labor_total NUMERIC NOT NULL DEFAULT '0',
                final_price NUMERIC NOT NULL,
                subtotal NUMERIC NOT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE RESTRICT,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
            )
        SQL);

        DB::statement(<<<'SQL'
            INSERT INTO sale_items_new (
                id, sale_id, product_id, product_reference, product_name, quantity,
                cost_price, unit_price, discount, labor_hours, labor_rate, labor_total,
                final_price, subtotal, created_at, updated_at
            )
            SELECT
                sale_items.id,
                sale_items.sale_id,
                sale_items.product_id,
                products.reference,
                products.designation,
                sale_items.quantity,
                sale_items.cost_price,
                sale_items.unit_price,
                sale_items.discount,
                sale_items.labor_hours,
                sale_items.labor_rate,
                sale_items.labor_total,
                sale_items.final_price,
                sale_items.subtotal,
                sale_items.created_at,
                sale_items.updated_at
            FROM sale_items
            LEFT JOIN products ON products.id = sale_items.product_id
        SQL);

        DB::statement('DROP TABLE sale_items');
        DB::statement('ALTER TABLE sale_items_new RENAME TO sale_items');
        DB::statement('CREATE INDEX idx_sale_items_sale_product ON sale_items (sale_id, product_id)');
        DB::statement('CREATE INDEX idx_sale_items_product ON sale_items (product_id)');

        DB::statement('PRAGMA foreign_keys=ON');
    }
};
