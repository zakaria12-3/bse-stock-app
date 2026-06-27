<?php

namespace App\DTOs;

class ProductData
{
    public function __construct(
        public readonly int $category_id,
        public readonly ?int $supplier_id,
        public readonly ?int $unit_id,
        public readonly ?string $source_sheet_name,
        public readonly ?string $reference,
        public readonly ?string $ref_fr,
        public readonly string $designation,
        public readonly ?string $designation_2,
        public readonly ?float $purchase_price_eur,
        public readonly float $unit_pr_ht,
        public readonly ?float $new_unit_pr_ht,
        public readonly ?float $cump_after_entry,
        public readonly float $selling_price,
        public readonly int $entry_quantity,
        public readonly int $quantity,
        public readonly int $exit_quantity,
        public readonly int $reserved_quantity,
        public readonly int $min_stock,
        public readonly float $total_ht,
        public readonly bool $is_active,
        public readonly ?string $description,
        public readonly ?string $notes,
        public readonly array $custom_fields = [],
    ) {}

    public static function fromArray(array $data): self
    {
        $entryQuantity = (int) ($data['entry_quantity'] ?? $data['quantity'] ?? 0);
        $exitQuantity = (int) ($data['exit_quantity'] ?? 0);
        $reservedQuantity = (int) ($data['reserved_quantity'] ?? 0);
        $quantity = isset($data['quantity']) && $data['quantity'] !== ''
            ? (int) $data['quantity']
            : max($entryQuantity - $exitQuantity - $reservedQuantity, 0);
        $unitPrHt = (float) ($data['unit_pr_ht'] ?? 0);

        return new self(
            category_id: (int) $data['category_id'],
            supplier_id: isset($data['supplier_id']) && $data['supplier_id'] !== '' ? (int) $data['supplier_id'] : null,
            unit_id: isset($data['unit_id']) && $data['unit_id'] !== '' ? (int) $data['unit_id'] : null,
            source_sheet_name: !empty($data['source_sheet_name']) ? $data['source_sheet_name'] : null,
            reference: !empty($data['reference']) ? $data['reference'] : null,
            ref_fr: !empty($data['ref_fr']) ? $data['ref_fr'] : null,
            designation: $data['designation'],
            designation_2: !empty($data['designation_2']) ? $data['designation_2'] : null,
            purchase_price_eur: isset($data['purchase_price_eur']) && $data['purchase_price_eur'] !== ''
                ? round((float) $data['purchase_price_eur'], 3)
                : null,
            unit_pr_ht: $unitPrHt,
            new_unit_pr_ht: isset($data['new_unit_pr_ht']) && $data['new_unit_pr_ht'] !== ''
                ? round((float) $data['new_unit_pr_ht'], 3)
                : null,
            cump_after_entry: isset($data['cump_after_entry']) && $data['cump_after_entry'] !== ''
                ? round((float) $data['cump_after_entry'], 3)
                : null,
            selling_price: (float) ($data['selling_price'] ?? $unitPrHt),
            entry_quantity: $entryQuantity,
            quantity: $quantity,
            exit_quantity: $exitQuantity,
            reserved_quantity: $reservedQuantity,
            min_stock: (int) ($data['min_stock'] ?? 0),
            total_ht: (float) ($data['total_ht'] ?? ($entryQuantity * $unitPrHt)),
            is_active: (bool) ($data['is_active'] ?? true),
            description: empty($data['description']) ? null : $data['description'],
            notes: empty($data['notes']) ? null : $data['notes'],
            custom_fields: $data['custom_fields'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'category_id' => $this->category_id,
            'supplier_id' => $this->supplier_id,
            'unit_id' => $this->unit_id,
            'source_sheet_name' => $this->source_sheet_name,
            'reference' => $this->reference,
            'ref_fr' => $this->ref_fr,
            'designation' => $this->designation,
            'designation_2' => $this->designation_2,
            'purchase_price_eur' => $this->purchase_price_eur,
            'unit_pr_ht' => $this->unit_pr_ht,
            'new_unit_pr_ht' => $this->new_unit_pr_ht,
            'cump_after_entry' => $this->cump_after_entry,
            'selling_price' => $this->selling_price,
            'entry_quantity' => $this->entry_quantity,
            'quantity' => $this->quantity,
            'exit_quantity' => $this->exit_quantity,
            'reserved_quantity' => $this->reserved_quantity,
            'min_stock' => $this->min_stock,
            'total_ht' => $this->total_ht,
            'is_active' => $this->is_active,
            'description' => $this->description,
            'notes' => $this->notes,
            'custom_fields' => $this->custom_fields,
        ];
    }
}
