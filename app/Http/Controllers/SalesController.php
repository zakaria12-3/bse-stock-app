<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\DTOs\SaleData;
use Illuminate\Http\Request;
use App\Services\SaleService;
use App\Exceptions\SaleException;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreSaleRequest;

class SalesController extends Controller
{
    public function index()
    {
        return view('sales.index');
    }

    public function create()
    {
        return view('sales.create');
    }

    public function store(StoreSaleRequest $request, SaleService $saleService)
    {
        try {
            $validated = $request->validated();
            $validated['created_by'] = Auth::id();

            $saleData = SaleData::fromArray($validated);

            $sale = $saleService->createSale($saleData);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dossier cree avec succes',
                    'data' => $sale,
                    'print_url' => route('sales.print', $sale->id),
                    'redirect' => route('sales.create')
                ], 201);
            }

            return redirect()->route('sales.create')
                ->with('success', 'Dossier cree avec succes.');

        } catch (SaleException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.product.unit', 'customer', 'creator']);
        return view('sales.show', compact('sale'));
    }

    public function destroy(Request $request, Sale $sale, SaleService $saleService)
    {
        try {
            $reason = $request->input('reason');
            $saleService->cancelSale($sale, $reason);
            return redirect()->route('sales.index')->with('success', 'Dossier annule avec succes.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function print(Sale $sale)
    {
        $sale->load(['items.product.unit', 'customer', 'creator']);
        return view('sales.print', compact('sale'));
    }

    public function restore(Sale $sale, SaleService $saleService)
    {
        try {
            $saleService->restoreSale($sale);
            return redirect()->back()->with('success', 'Dossier remis en attente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function complete(Request $request, Sale $sale, SaleService $saleService)
    {
        try {
            $paymentData = $request->only(['cash_received', 'change']);

            $saleService->completeSale($sale, $paymentData);

            return redirect()->back()->with('success', 'Dossier marque comme termine.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
