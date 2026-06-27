<?php

namespace App\Services;

use Exception;
use App\Models\Sale;
use App\DTOs\SaleData;
use App\Models\Product;
use App\Enums\SaleStatus;
use App\Enums\PaymentMethod;
use App\Exceptions\SaleException;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(
        protected FinanceTransactionService $financeService
    ) {
    }

    /**
     * Create a new sale with items and deduction of stock.
     */
    public function createSale(SaleData $data): Sale
    {
        return DB::transaction(function () use ($data) {
            try {
                // Lock products for update
                $productIds = collect($data->items)->pluck('product_id')->sort()->values()->all();

                $products = Product::whereIn('id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $sale = Sale::create([
                    'invoice_number' => $this->generateInvoiceNumber(),
                    'build_name' => $data->build_name,
                    'customer_id' => $data->customer_id,
                    'created_by' => $data->created_by,
                    'sale_date' => $data->sale_date,
                    'status' => $data->status,
                    'payment_method' => $data->payment_method,
                    'notes' => $data->notes,
                    'cash_received' => $data->cash_received,
                    'change' => $data->change,
                    'subtotal' => 0,
                    'global_discount' => $data->global_discount,
                    'total_discount' => 0,
                    'total' => 0,
                ]);

                $totalSubtotal = 0;
                $totalDiscount = 0;
                $timestamp = now();
                $saleItems = [];

                foreach ($data->items as $itemData) {
                    $product = $products->get($itemData->product_id);

                    if (!$product) {
                        throw SaleException::productNotFound($itemData->product_id);
                    }

                    if ($product->quantity < $itemData->quantity) {
                        throw SaleException::insufficientStock(
                            $product->name,
                            $itemData->quantity,
                            $product->quantity
                        );
                    }

                    // Update stock
                    $product->exit_quantity = (int) ($product->exit_quantity ?? 0) + $itemData->quantity;
                    $product->save();

                    // Ambulance dossiers consume stock at the current CUMP/PR HT basis, not a retail selling price.
                    $unitPrice = (float) ($product->cump_after_entry ?? $product->unit_pr_ht);
                    $quantity = $itemData->quantity;
                    $discount = $itemData->discount;
                    $laborHours = max(0, $itemData->labor_hours);
                    $laborRate = max(0, $itemData->labor_rate);
                    $laborTotal = round($laborHours * $laborRate, 3);
                    $installedUnitPrice = $unitPrice + $laborTotal;

                    if ($discount > $installedUnitPrice) {
                        throw SaleException::invalidDiscount("La remise piece (" . format_money($discount) . ") ne peut pas depasser le prix installe (" . format_money($installedUnitPrice) . ") pour '{$product->name}'.");
                    }

                    $finalPrice = $installedUnitPrice - $discount;
                    $subtotal   = $finalPrice * $quantity;

                    $saleItems[] = [
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'product_reference' => $product->reference,
                        'product_name' => $product->designation,
                        'quantity' => $quantity,
                        'cost_price' => $product->purchase_price,
                        'unit_price' => $unitPrice,
                        'discount' => $discount,
                        'labor_hours' => $laborHours,
                        'labor_rate' => $laborRate,
                        'labor_total' => $laborTotal,
                        'final_price' => $finalPrice,
                        'subtotal' => $subtotal,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];

                    $totalSubtotal += $subtotal;
                    $totalDiscount += $discount * $quantity;
                }

                // Batch insert items
                if (!empty($saleItems)) {
                    \App\Models\SaleItem::insert($saleItems);
                }

                if ($data->global_discount > $totalSubtotal) {
                    throw SaleException::invalidDiscount("La remise globale (" . format_money($data->global_discount) . ") ne peut pas depasser le sous-total (" . format_money($totalSubtotal) . ").");
                }

                $total = $totalSubtotal - $data->global_discount;

                if ($data->status === SaleStatus::COMPLETED) {
                    if ($data->payment_method === \App\Enums\PaymentMethod::CASH && $data->cash_received < $total) {
                        throw SaleException::insufficientPayment($total, $data->cash_received);
                    }
                }
                $change = 0;

                // Calculate change if payment method is cash
                if ($data->payment_method === \App\Enums\PaymentMethod::CASH && $data->cash_received >= $total) {
                    $change = $data->cash_received - $total;
                }

                $sale->update([
                    'subtotal' => $totalSubtotal + $totalDiscount,
                    'total_discount' => $totalDiscount + $data->global_discount,
                    'global_discount' => $data->global_discount,
                    'total' => $total,
                    'change' => $change,
                ]);

                if ($sale->status === SaleStatus::COMPLETED) {
                    $this->financeService->recordIncomeFromSale($sale);
                }

                return $sale;

            } catch (Exception $e) {
                if ($e instanceof SaleException)
                    throw $e;
                throw SaleException::creationFailed($e->getMessage(), ['data' => $data]);
            }
        });
    }

    /**
     * Cancel a sale and restore stock.
     */
    public function cancelSale(Sale $sale, ?string $reason = null): Sale
    {
        return DB::transaction(function () use ($sale, $reason) {
            try {
                if ($sale->status === SaleStatus::CANCELLED) {
                    throw SaleException::invalidStatus('cancel', $sale->status->label(), ['id' => $sale->id]);
                }

                // Restore stock for completed or pending sales
                if (in_array($sale->status, [SaleStatus::COMPLETED, SaleStatus::PENDING])) {
                    $sale->loadMissing('items.product');

                    foreach ($sale->items as $item) {
                        if ($item->product) {
                            $currentExitQuantity = (int) ($item->product->exit_quantity ?? 0);
                            $item->product->exit_quantity = max($currentExitQuantity - $item->quantity, 0);
                            $item->product->save();
                        }
                    }
                }

                $updateData = ['status' => SaleStatus::CANCELLED];

                if ($reason) {
                    $updateData['notes'] = ($sale->notes ? $sale->notes . "\n" : '') . "[Annule]: " . $reason;
                }

                $sale->update($updateData);

                // Void Finance
                $this->financeService->voidTransaction($sale);

                return $sale;

            } catch (Exception $e) {
                if ($e instanceof SaleException)
                    throw $e;
                throw SaleException::cancellationFailed($e->getMessage(), ['id' => $sale->id]);
            }
        });
    }

    /**
     * Mark a pending sale as completed.
     */
    public function completeSale(Sale $sale, array $paymentData = []): Sale
    {
        return DB::transaction(function () use ($sale, $paymentData) {
            if ($sale->status !== SaleStatus::PENDING) {
                throw SaleException::invalidStatus('complete', $sale->status->label(), ['id' => $sale->id]);
            }

            $updateData = ['status' => SaleStatus::COMPLETED];

            if (!empty($paymentData)) {
                $updateData['cash_received'] = $paymentData['cash_received'] ?? $sale->cash_received;

                if ($sale->payment_method === PaymentMethod::CASH && $updateData['cash_received'] < $sale->total) {
                    throw SaleException::insufficientPayment($sale->total, $updateData['cash_received']);
                }

                // Calculate Change
                if ($sale->payment_method === PaymentMethod::CASH && $updateData['cash_received'] >= $sale->total) {
                    $updateData['change'] = $updateData['cash_received'] - $sale->total;
                } else {
                    $updateData['change'] = 0;
                }
            }

            $sale->update($updateData);

            // Sync Finance
            $this->financeService->recordIncomeFromSale($sale);

            return $sale;
        });
    }

    /**
     * Restore a cancelled sale to pending (must reserve stock again).
     */
    public function restoreSale(Sale $sale): Sale
    {
        return DB::transaction(function () use ($sale) {
            if ($sale->status !== SaleStatus::CANCELLED) {
                throw SaleException::invalidStatus('restore', $sale->status->label(), ['id' => $sale->id]);
            }

            // Must re-deduct stock
            $sale->loadMissing('items.product');

            foreach ($sale->items as $item) {
                $product = $item->product()->lockForUpdate()->find($item->product_id);

                if (!$product) {
                    throw SaleException::productNotFound($item->product_id);
                }

                if ($product->quantity < $item->quantity) {
                    throw SaleException::insufficientStock(
                        $product->name,
                        $item->quantity,
                        $product->quantity
                    );
                }

                $product->exit_quantity = (int) ($product->exit_quantity ?? 0) + $item->quantity;
                $product->save();
            }

            // Restore to PENDING
            $sale->update(['status' => SaleStatus::PENDING]);

            // No Finance Sync needed as it goes to PENDING

            return $sale;
        });
    }

    /**
     * Permanently delete a cancelled sale.
     *
     * @param Sale $sale
     * @return void
     * @throws Exception
     */
    public function deleteSale(Sale $sale): void
    {
        DB::transaction(function () use ($sale) {
            if ($sale->status !== SaleStatus::CANCELLED) {
                throw SaleException::invalidStatus('delete', $sale->status->label(), ['id' => $sale->id]);
            }

            // Void Finance (Just in case)
            $this->financeService->voidTransaction($sale);

            // Manually delete items first due to restrictOnDelete constraint
            $sale->items()->delete();
            $sale->delete();
        });
    }

    /**
     * Generate unique invoice number.
     * Format: BSE-AMB-YYYYMM-0001
     */
    private function generateInvoiceNumber(): string
    {
        $prefix = 'BSE-AMB-' . date('Ym') . '-';

        $latest = Sale::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$latest) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($latest->invoice_number, -4);
        return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}
