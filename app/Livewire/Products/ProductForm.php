<?php

namespace App\Livewire\Products;

use App\DTOs\ProductData;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;
use App\Services\ProductService;
use App\Services\CumpCalculator;
use App\Exceptions\ProductException;

class ProductForm extends Component
{
    public bool $isEditing = false;
    public ?Product $product = null;

    public ?string $reference = null;
    public ?string $ref_fr = null;
    public string $designation = '';
    public string $designation_2 = '';
    public ?int $category_id = null;
    public ?int $supplier_id = null;
    public ?int $unit_id = null;
    public ?string $source_sheet_name = null;
    public ?string $purchase_price_eur = null;
    public float $unit_pr_ht = 0;
    public ?float $new_unit_pr_ht = null;
    public ?float $cump_after_entry = null;
    public float $selling_price = 0;
    public int $entry_quantity = 0;
    public int $quantity = 0;
    public int $exit_quantity = 0;
    public int $reserved_quantity = 0;
    public int $min_stock = 0;
    public float $total_ht = 0;
    public bool $is_active = true;
    public string $description = '';
    public string $notes = '';
    public array $custom_fields = [];

    public ?string $categoryName = null;
    public ?string $supplierName = null;
    public ?string $unitName = null;

    public function render()
    {
        return view('livewire.products.product-form');
    }

    #[On('create-product')]
    public function create(): void
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $this->reset([
            'reference',
            'ref_fr',
            'designation',
            'designation_2',
            'category_id',
            'supplier_id',
            'unit_id',
            'source_sheet_name',
            'purchase_price_eur',
            'unit_pr_ht',
            'new_unit_pr_ht',
            'cump_after_entry',
            'selling_price',
            'entry_quantity',
            'quantity',
            'exit_quantity',
            'reserved_quantity',
            'min_stock',
            'total_ht',
            'description',
            'notes',
            'product',
            'isEditing',
            'categoryName',
            'supplierName',
            'unitName',
            'custom_fields',
        ]);

        $this->is_active = true;
        $this->dispatch('open-modal', name: 'product-form-modal');
    }

    #[On('edit-product')]
    public function edit(Product $product): void
    {
        $this->resetValidation();
        $this->resetErrorBag();

        if (!\Illuminate\Support\Facades\Auth::user()->isAdmin()) {
            $this->dispatch('toast', message: 'Vous n avez pas l autorisation de modifier les pieces existantes. Contactez un administrateur.', type: 'error');
            return;
        }

        $this->product = $product;
        $this->reference = $product->reference;
        $this->ref_fr = $product->ref_fr;
        $this->designation = $product->designation;
        $this->designation_2 = $product->designation_2 ?? '';
        $this->category_id = $product->category_id;
        $this->supplier_id = $product->supplier_id;
        $this->unit_id = $product->unit_id;
        $this->source_sheet_name = $product->source_sheet_name;
        $purchasePriceEur = $product->purchase_price_eur;
        $this->purchase_price_eur = blank($purchasePriceEur) || (float) $purchasePriceEur === 0.0
            ? null
            : (string) $purchasePriceEur;
        $this->unit_pr_ht = (float) $product->unit_pr_ht;
        $this->new_unit_pr_ht = $product->new_unit_pr_ht !== null ? (float) $product->new_unit_pr_ht : null;
        $this->cump_after_entry = $product->cump_after_entry !== null ? (float) $product->cump_after_entry : null;
        $this->selling_price = (float) ($product->selling_price ?: $product->unit_pr_ht);
        $this->entry_quantity = (int) ($product->entry_quantity ?? $product->quantity);
        $this->quantity = $product->quantity;
        $this->exit_quantity = (int) $product->exit_quantity;
        $this->reserved_quantity = (int) $product->reserved_quantity;
        $this->min_stock = $product->min_stock;
        $this->total_ht = (float) $product->total_ht;
        $this->is_active = $product->is_active;
        $this->description = $product->description ?? '';
        $this->notes = $product->notes ?? '';
        $this->categoryName = $product->category?->name;
        $this->supplierName = $product->supplier?->name;
        $this->unitName = $product->unit ? "{$product->unit->name} ({$product->unit->symbol})" : null;

        $this->custom_fields = [];
        if (is_array($product->custom_fields)) {
            foreach ($product->custom_fields as $key => $value) {
                $this->custom_fields[] = ['key' => $key, 'value' => $value];
            }
        }

        $this->isEditing = true;
        $this->dispatch('open-modal', name: 'product-form-modal');
    }

    public function updatedEntryQuantity(): void
    {
        $this->syncStockWorkbookFields();
    }

    public function updatedUnitPrHt(): void
    {
        $this->syncStockWorkbookFields();
    }

    public function updatedNewUnitPrHt(): void
    {
        $this->syncStockWorkbookFields();
    }

    public function updatedExitQuantity(): void
    {
        $this->syncStockWorkbookFields();
    }

    public function updatedReservedQuantity(): void
    {
        $this->syncStockWorkbookFields();
    }

    public function rules(): array
    {
        return [
            'reference' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('products', 'reference')->ignore($this->product?->id),
            ],
            'ref_fr' => ['nullable', 'string', 'max:50'],
            'designation' => ['required', 'string', 'max:150'],
            'designation_2' => ['nullable', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'source_sheet_name' => ['nullable', 'string', 'max:100'],
            'purchase_price_eur' => ['nullable', 'numeric', 'min:0'],
            'unit_pr_ht' => ['required', 'numeric', 'min:0'],
            'new_unit_pr_ht' => ['nullable', 'numeric', 'min:0'],
            'cump_after_entry' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'entry_quantity' => ['required', 'integer', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'exit_quantity' => ['nullable', 'integer', 'min:0'],
            'reserved_quantity' => ['nullable', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'total_ht' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
            'description' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'custom_fields' => ['nullable', 'array'],
            'custom_fields.*.key' => ['required', 'string'],
            'custom_fields.*.value' => ['required', 'string'],
        ];
    }

    public function addCustomField(): void
    {
        $this->custom_fields[] = ['key' => '', 'value' => ''];
    }

    public function removeCustomField($index): void
    {
        unset($this->custom_fields[$index]);
        $this->custom_fields = array_values($this->custom_fields);
    }

    public function save(ProductService $service): void
    {
        $this->syncStockWorkbookFields();
        $validated = $this->validate();

        $formattedCustomFields = [];
        foreach ($this->custom_fields as $field) {
            if (!empty($field['key'])) {
                $formattedCustomFields[$field['key']] = $field['value'];
            }
        }
        $validated['custom_fields'] = $formattedCustomFields;

        $data = ProductData::fromArray($validated);

        try {
            if ($this->isEditing && $this->product) {
                $service->updateProduct($this->product, $data);
                $message = 'Article mis a jour avec succes.';
            } else {
                $service->createProduct($data);
                $message = 'Article cree avec succes.';
            }

            $this->dispatch('close-modal', name: 'product-form-modal');
            $this->dispatch('pg:eventRefresh-product-table');
            $this->dispatch('toast', message: $message, type: 'success');
            $this->resetValidation();
            $this->resetErrorBag();
        } catch (ProductException $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        } catch (\Throwable $e) {
            $this->dispatch('toast', message: 'Une erreur inattendue est survenue.', type: 'error');
        }
    }

    private function syncStockWorkbookFields(): void
    {
        $entryQuantity = max((int) $this->entry_quantity, 0);
        $exitQuantity = max((int) $this->exit_quantity, 0);
        $reservedQuantity = max((int) $this->reserved_quantity, 0);
        $previousEntryQuantity = $this->isEditing && $this->product
            ? max((int) ($this->product->entry_quantity ?? $this->product->quantity ?? 0), 0)
            : 0;
        $previousUnitPrHt = $this->isEditing && $this->product
            ? (float) ($this->product->cump_after_entry ?? $this->product->unit_pr_ht ?? 0)
            : (float) $this->unit_pr_ht;

        $this->quantity = max($entryQuantity - $exitQuantity - $reservedQuantity, 0);
        $this->selling_price = (float) $this->unit_pr_ht;
        $this->cump_after_entry = CumpCalculator::fromTotalQuantity(
            $previousEntryQuantity,
            $entryQuantity,
            $previousUnitPrHt,
            $this->new_unit_pr_ht
        ) ?? (float) $this->unit_pr_ht;
        $this->total_ht = round(((float) $this->unit_pr_ht) * $entryQuantity, 3);
    }
}
