<?php

namespace App\Models;

use App\Services\CumpCalculator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'source_sheet_name',
        'supplier_id',
        'unit_id',
        'reference',
        'ref_fr',
        'designation',
        'designation_2',
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
        'is_active',
        'description',
        'notes',
        'custom_fields',
        // Legacy aliases kept fillable so older code paths still hydrate correctly.
        'sku',
        'name',
        'purchase_price',
    ];

    protected $casts = [
        'purchase_price_eur' => 'decimal:3',
        'unit_pr_ht' => 'decimal:3',
        'new_unit_pr_ht' => 'decimal:3',
        'cump_after_entry' => 'decimal:3',
        'selling_price' => 'decimal:3',
        'total_ht' => 'decimal:3',
        'entry_quantity' => 'integer',
        'quantity' => 'integer',
        'exit_quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'min_stock' => 'integer',
        'is_active' => 'boolean',
        'custom_fields' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $product) {
            $entryQuantity = (int) ($product->entry_quantity ?? $product->quantity ?? 0);
            $exitQuantity = (int) ($product->exit_quantity ?? 0);
            $reservedQuantity = (int) ($product->reserved_quantity ?? 0);
            $unitPrHt = (float) ($product->unit_pr_ht ?? 0);
            $oldEntryQuantity = $product->exists
                ? (int) ($product->getOriginal('entry_quantity') ?? $product->getOriginal('quantity') ?? 0)
                : 0;
            $oldUnitPrHt = $product->exists
                ? (float) ($product->getOriginal('cump_after_entry') ?? $product->getOriginal('unit_pr_ht') ?? $unitPrHt)
                : $unitPrHt;

            if (!$product->reference) {
                $product->reference = self::generateReference();
            }

            if ($product->entry_quantity === null) {
                $product->entry_quantity = $entryQuantity;
            }

            $product->quantity = max($entryQuantity - $exitQuantity - $reservedQuantity, 0);

            if ((float) ($product->selling_price ?? 0) === 0.0) {
                $product->selling_price = $unitPrHt;
            }

            if (
                blank($product->cump_after_entry)
                || $product->isDirty('new_unit_pr_ht')
                || ($product->isDirty('entry_quantity') && filled($product->new_unit_pr_ht))
            ) {
                $product->cump_after_entry = CumpCalculator::fromTotalQuantity(
                    $oldEntryQuantity,
                    $entryQuantity,
                    $oldUnitPrHt,
                    $product->new_unit_pr_ht
                ) ?? $unitPrHt;
            }

            $product->total_ht = round($entryQuantity * $unitPrHt, 3);
        });
    }

    public static function generateReference(): string
    {
        $prefix = 'ART-' . now()->format('ymd') . '-';

        do {
            $reference = $prefix . strtoupper(str()->random(4));
        } while (self::where('reference', $reference)->exists());

        return $reference;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function getSkuAttribute(): ?string
    {
        return $this->reference;
    }

    public function setSkuAttribute(?string $value): void
    {
        $this->attributes['reference'] = $value;
    }

    public function getNameAttribute(): string
    {
        return $this->designation;
    }

    public function setNameAttribute(string $value): void
    {
        $this->attributes['designation'] = $value;
    }

    public function getPurchasePriceAttribute(): float
    {
        return (float) ($this->purchase_price_eur ?? 0);
    }

    public function setPurchasePriceAttribute(float|int|string $value): void
    {
        $this->attributes['purchase_price_eur'] = $value;
    }
}
