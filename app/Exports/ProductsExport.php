<?php

namespace App\Exports;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductsExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $products = Product::query()
            ->with(['category', 'supplier'])
            ->orderBy('reference')
            ->get();
        $customColumns = $this->customColumns($products);

        $categories = Category::query()
            ->orderByRaw('CASE WHEN worksheet_name IS NULL OR worksheet_name = "" THEN 1 ELSE 0 END')
            ->orderBy('worksheet_name')
            ->orderBy('name')
            ->get();

        $sheets = [
            new ProductWorksheetExport(
                'Stock au ' . now()->format('dmY'),
                $products,
                ProductWorksheetExport::SUMMARY_COLUMNS,
                $customColumns
            ),
        ];

        foreach ($categories as $category) {
            $categoryProducts = $products
                ->where('category_id', $category->id)
                ->values();

            if ($categoryProducts->isEmpty()) {
                continue;
            }

            $title = $category->worksheet_name ?: $category->name;

            $sheets[] = new ProductWorksheetExport(
                $title,
                $categoryProducts,
                ProductWorksheetExport::DETAIL_COLUMNS,
                $this->customColumns($categoryProducts)
            );
        }

        return $sheets;
    }

    private function customColumns(Collection $products): array
    {
        return $products
            ->flatMap(fn (Product $product) => array_keys($product->custom_fields ?? []))
            ->unique()
            ->values()
            ->all();
    }
}
