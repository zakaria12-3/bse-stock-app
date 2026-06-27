<?php

namespace App\Services;

use Exception;
use App\Models\Product;
use App\DTOs\ProductData;
use App\Exceptions\ProductException;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function createProduct(ProductData $data): Product
    {
        return DB::transaction(function () use ($data) {
            try {
                return Product::create([
                    'category_id' => $data->category_id,
                    'supplier_id' => $data->supplier_id,
                    'unit_id' => $data->unit_id,
                    'source_sheet_name' => $data->source_sheet_name,
                    'reference' => $data->reference ?: Product::generateReference(),
                    'ref_fr' => $data->ref_fr,
                    'designation' => $data->designation,
                    'designation_2' => $data->designation_2,
                    'purchase_price_eur' => $data->purchase_price_eur,
                    'unit_pr_ht' => $data->unit_pr_ht,
                    'new_unit_pr_ht' => $data->new_unit_pr_ht,
                    'cump_after_entry' => $data->cump_after_entry,
                    'selling_price' => $data->selling_price ?: $data->unit_pr_ht,
                    'entry_quantity' => $data->entry_quantity,
                    'quantity' => $data->quantity,
                    'exit_quantity' => $data->exit_quantity,
                    'reserved_quantity' => $data->reserved_quantity,
                    'min_stock' => $data->min_stock,
                    'total_ht' => $data->total_ht,
                    'is_active' => $data->is_active,
                    'description' => $data->description,
                    'notes' => $data->notes,
                    'custom_fields' => $data->custom_fields,
                ]);
            } catch (Exception $e) {
                throw ProductException::creationFailed($e->getMessage(), [
                    'data' => $data->toArray(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        });
    }

    public function updateProduct(Product $product, ProductData $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            try {
                $product->update([
                    'category_id' => $data->category_id,
                    'supplier_id' => $data->supplier_id,
                    'unit_id' => $data->unit_id,
                    'source_sheet_name' => $data->source_sheet_name,
                    'reference' => $data->reference ?: $product->reference,
                    'ref_fr' => $data->ref_fr,
                    'designation' => $data->designation,
                    'designation_2' => $data->designation_2,
                    'purchase_price_eur' => $data->purchase_price_eur,
                    'unit_pr_ht' => $data->unit_pr_ht,
                    'new_unit_pr_ht' => $data->new_unit_pr_ht,
                    'cump_after_entry' => $data->cump_after_entry,
                    'selling_price' => $data->selling_price ?: $data->unit_pr_ht,
                    'entry_quantity' => $data->entry_quantity,
                    'quantity' => $data->quantity,
                    'exit_quantity' => $data->exit_quantity,
                    'reserved_quantity' => $data->reserved_quantity,
                    'min_stock' => $data->min_stock,
                    'total_ht' => $data->total_ht,
                    'is_active' => $data->is_active,
                    'description' => $data->description,
                    'notes' => $data->notes,
                    'custom_fields' => $data->custom_fields,
                ]);

                return $product->refresh();
            } catch (Exception $e) {
                throw ProductException::updateFailed($e->getMessage(), [
                    'id' => $product->id,
                    'data' => $data->toArray(),
                ]);
            }
        });
    }

    public function deleteProduct(Product $product): void
    {
        DB::transaction(function () use ($product) {
            try {
                $this->detachTransactionItems($product);
                $product->delete();
            } catch (Exception $e) {
                throw ProductException::deletionFailed($e->getMessage(), ['id' => $product->id]);
            }
        });
    }

    public function deleteAllProducts(): int
    {
        return DB::transaction(function () {
            Product::query()
                ->select(['id', 'reference', 'designation'])
                ->chunkById(500, function ($products) {
                    foreach ($products as $product) {
                        $this->detachTransactionItems($product);
                    }
                });

            return Product::query()->delete();
        });
    }

    private function detachTransactionItems(Product $product): void
    {
        $snapshot = [
            'product_reference' => $product->reference,
            'product_name' => $product->designation,
            'product_id' => null,
        ];

        $product->purchaseItems()->update($snapshot);
        $product->saleItems()->update($snapshot);
    }
}
