<?php

namespace App\Http\Controllers\Api;

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Enums\SaleStatus;
use App\Http\Controllers\Controller;

class SaleBuildController extends Controller
{
    public function search(Request $request)
    {
        $query = trim((string) ($request->input('q') ?? $request->input('search') ?? ''));

        $builds = Sale::query()
            ->select(['id', 'customer_id', 'build_name', 'invoice_number', 'sale_date', 'status'])
            ->with('customer:id,name')
            ->whereNotNull('build_name')
            ->where('build_name', '!=', '')
            ->where('status', '!=', SaleStatus::CANCELLED)
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('build_name', 'like', "%{$query}%")
                        ->orWhere('invoice_number', 'like', "%{$query}%");
                });
            })
            ->latest('sale_date')
            ->limit(20)
            ->get()
            ->map(function (Sale $sale) {
                return [
                    'value' => $sale->id,
                    'text' => $sale->build_name,
                    'name' => $sale->build_name,
                    'invoice_number' => $sale->invoice_number,
                    'sale_date' => optional($sale->sale_date)->format('Y-m-d'),
                    'customer_name' => $sale->customer?->name,
                ];
            });

        return response()->json($builds->values());
    }

    public function show(Sale $sale)
    {
        $sale->load([
            'customer:id,name,phone',
            'items:id,sale_id,product_id,quantity,discount,labor_hours,labor_rate',
            'items.product:id,unit_id,designation,reference,unit_pr_ht,quantity',
            'items.product.unit:id,name,symbol',
        ]);

        abort_if(blank($sale->build_name), 404);

        return response()->json([
            'id' => $sale->id,
            'build_name' => $sale->build_name,
            'invoice_number' => $sale->invoice_number,
            'sale_date' => optional($sale->sale_date)->format('Y-m-d'),
            'customer' => $sale->customer ? [
                'id' => $sale->customer->id,
                'name' => $sale->customer->name,
                'phone' => $sale->customer->phone,
            ] : null,
            'items' => $sale->items->map(function ($item) {
                $product = $item->product;

                return [
                    'product_id' => $item->product_id,
                    'template_quantity' => (int) $item->quantity,
                    'template_discount' => (float) $item->discount,
                    'template_labor_hours' => (float) $item->labor_hours,
                    'template_labor_rate' => (float) $item->labor_rate,
                    'product' => $product ? [
                        'id' => $product->id,
                        'name' => $product->designation,
                        'sku' => $product->reference,
                        'price' => (float) $product->unit_pr_ht,
                        'unit_pr_ht' => (float) $product->unit_pr_ht,
                        'quantity' => (int) $product->quantity,
                        'unit' => $product->unit ? [
                            'symbol' => $product->unit->symbol,
                            'name' => $product->unit->name,
                        ] : null,
                    ] : null,
                ];
            })->values(),
        ]);
    }
}
