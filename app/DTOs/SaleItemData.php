<?php

namespace App\DTOs;

readonly class SaleItemData
{
    public function __construct(
        public int $product_id,
        public int $quantity,
        public float $unit_price = 0,
        public float $discount = 0,
        public float $labor_hours = 0,
        public float $labor_rate = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            product_id: (int) $data['product_id'],
            quantity: (int) $data['quantity'],
            unit_price: round((float) ($data['unit_price'] ?? 0), 3),
            discount: round((float) ($data['discount'] ?? 0), 3),
            labor_hours: round((float) ($data['labor_hours'] ?? 0), 3),
            labor_rate: round((float) ($data['labor_rate'] ?? 0), 3),
        );
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'discount' => $this->discount,
            'labor_hours' => $this->labor_hours,
            'labor_rate' => $this->labor_rate,
        ];
    }
}
