<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Services\CumpCalculator;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductStockIncrease extends Component
{
    public ?Product $product = null;
    public int $amount = 1;
    public ?float $new_unit_pr_ht = null;

    public function render()
    {
        return view('livewire.products.product-stock-increase');
    }

    #[On('increase-product-stock')]
    public function open(Product $product): void
    {
        $this->resetErrorBag();
        $this->product = $product->load('unit');
        $this->amount = 1;
        $this->new_unit_pr_ht = null;

        $this->dispatch('open-modal', name: 'product-stock-increase-modal');
    }

    public function save(): void
    {
        $validated = $this->validate([
            'amount' => ['required', 'integer', 'min:1'],
            'new_unit_pr_ht' => ['nullable', 'numeric', 'min:0'],
        ]);

        if (!$this->product) {
            $this->dispatch('toast', message: 'Article introuvable.', type: 'error');
            return;
        }

        $product = $this->product->fresh();

        if (!$product) {
            $this->dispatch('toast', message: 'Article introuvable.', type: 'error');
            return;
        }

        $previousEntryQuantity = (int) ($product->entry_quantity ?? $product->quantity ?? 0);
        $previousUnitPrHt = (float) ($product->cump_after_entry ?? $product->unit_pr_ht ?? 0);
        $newUnitPrHt = $validated['new_unit_pr_ht'] ?? null;

        $product->entry_quantity = $previousEntryQuantity + (int) $validated['amount'];
        $product->new_unit_pr_ht = $newUnitPrHt;
        $product->cump_after_entry = CumpCalculator::fromEntry(
            $previousEntryQuantity,
            $previousUnitPrHt,
            (int) $validated['amount'],
            $newUnitPrHt
        ) ?? $previousUnitPrHt;
        $product->save();

        $this->product = $product->refresh()->load('unit');

        $this->dispatch('close-modal', name: 'product-stock-increase-modal');
        $this->dispatch('pg:eventRefresh-product-table');
        $this->dispatch('toast', message: 'Quantite ajoutee avec succes.', type: 'success');
    }
}
