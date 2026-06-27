<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $indexes = [
            'CREATE INDEX IF NOT EXISTS idx_products_stock_lookup ON products (is_active, designation, reference)',
            'CREATE INDEX IF NOT EXISTS idx_products_family_supplier_unit ON products (category_id, supplier_id, unit_id)',
            'CREATE INDEX IF NOT EXISTS idx_products_quantity_min_stock ON products (quantity, min_stock)',
            'CREATE INDEX IF NOT EXISTS idx_sales_status_date ON sales (status, sale_date)',
            'CREATE INDEX IF NOT EXISTS idx_sales_customer_date ON sales (customer_id, sale_date)',
            'CREATE INDEX IF NOT EXISTS idx_sale_items_sale_product ON sale_items (sale_id, product_id)',
            'CREATE INDEX IF NOT EXISTS idx_sale_items_product ON sale_items (product_id)',
            'CREATE INDEX IF NOT EXISTS idx_purchases_status_date ON purchases (status, purchase_date)',
            'CREATE INDEX IF NOT EXISTS idx_purchases_supplier_date ON purchases (supplier_id, purchase_date)',
            'CREATE INDEX IF NOT EXISTS idx_finance_transactions_date_category ON finance_transactions (transaction_date, finance_category_id)',
            'CREATE INDEX IF NOT EXISTS idx_finance_transactions_category_date ON finance_transactions (finance_category_id, transaction_date)',
        ];

        foreach ($indexes as $index) {
            DB::statement($index);
        }
    }

    public function down(): void
    {
        $indexes = [
            'idx_products_stock_lookup',
            'idx_products_family_supplier_unit',
            'idx_products_quantity_min_stock',
            'idx_sales_status_date',
            'idx_sales_customer_date',
            'idx_sale_items_sale_product',
            'idx_sale_items_product',
            'idx_purchases_status_date',
            'idx_purchases_supplier_date',
            'idx_finance_transactions_date_category',
            'idx_finance_transactions_category_date',
        ];

        foreach ($indexes as $index) {
            DB::statement("DROP INDEX IF EXISTS {$index}");
        }
    }
};
