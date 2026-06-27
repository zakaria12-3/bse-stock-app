<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q') ?? $request->input('search');
        $inStockOnly = $request->boolean('in_stock_only', false);

        $products = Product::query()
            ->select(['id', 'unit_id', 'designation', 'reference', 'ref_fr', 'unit_pr_ht', 'cump_after_entry', 'quantity'])
            ->with(['unit:id,name,symbol'])
            ->where('is_active', true)
            ->when($inStockOnly, fn ($q) => $q->where('quantity', '>', 0))
            ->when($query, function ($q) use ($query) {
                $q->where(function ($inner) use ($query) {
                    $inner->where('designation', 'like', "%{$query}%")
                        ->orWhere('reference', 'like', "%{$query}%")
                        ->orWhere('ref_fr', 'like', "%{$query}%");
                });
            })
            ->orderBy('designation')
            ->limit(50)
            ->get()
            ->map(function ($product) {
                $buildUnitPrice = (float) ($product->cump_after_entry ?? $product->unit_pr_ht);

                return [
                    'value' => $product->id,
                    'id' => $product->id,
                    'text' => $product->name,
                    'name' => $product->name,
                    'price' => $buildUnitPrice,
                    'selling_price' => $buildUnitPrice,
                    'unit_pr_ht' => $buildUnitPrice,
                    'cump_after_entry' => $product->cump_after_entry !== null ? (float) $product->cump_after_entry : null,
                    'sku' => $product->sku,
                    'quantity' => (int) $product->quantity,
                    'unit' => $product->unit ? [
                        'symbol' => $product->unit->symbol,
                        'name' => $product->unit->name
                    ] : null,
                ];
            });

        return response()->json($products);
    }
}
