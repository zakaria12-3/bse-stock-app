<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'product_reference',
        'product_name',
        'quantity',
        'cost_price',
        'unit_price',
        'discount',
        'labor_hours',
        'labor_rate',
        'labor_total',
        'final_price',
        'subtotal',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'cost_price' => 'decimal:3',
        'unit_price' => 'decimal:3',
        'discount' => 'decimal:3',
        'labor_hours' => 'decimal:3',
        'labor_rate' => 'decimal:3',
        'labor_total' => 'decimal:3',
        'final_price' => 'decimal:3',
        'subtotal' => 'decimal:3',
        'quantity' => 'integer',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
