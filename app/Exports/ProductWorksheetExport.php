<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProductWorksheetExport implements FromArray, ShouldAutoSize, WithTitle
{
    public const SUMMARY_COLUMNS = [
        'Reference Article',
        'REF FR',
        'Designation Article',
        'Designation 2',
        'Famille Article',
        'Designation Famille',
        'Fournisseur',
        'Qte',
        "Prix d'achat EUR",
        'PR unitaire HT',
        'Total HT',
    ];

    public const DETAIL_COLUMNS = [
        'Reference Article',
        'REF FR',
        'Designation Article',
        'Designation 2',
        'Famille Article',
        'Designation Famille',
        'Fournisseur',
        'Entre en Qte',
        "Prix d'achat EUR",
        'PR unitaire HT',
        'Total HT',
        'Nouveau PR unitaire HT',
        'CUMP / PR HT dossier',
        'Sortie en Qte',
        'Reservation',
        'Stock restant',
        'Observation',
    ];

    public function __construct(
        private readonly string $sheetTitle,
        private readonly Collection $products,
        private readonly array $columns,
        private readonly array $customColumns = []
    ) {
    }

    public function array(): array
    {
        $rows = [array_merge($this->columns, $this->customColumns)];

        foreach ($this->products as $product) {
            /** @var Product $product */
            if ($this->columns === self::SUMMARY_COLUMNS) {
                $rows[] = array_merge([
                    $product->reference,
                    $product->ref_fr,
                    $product->designation,
                    $product->designation_2,
                    $product->category?->code,
                    $product->category?->name,
                    $product->supplier?->name,
                    (int) $product->quantity,
                    $this->nullableDecimal($product->purchase_price_eur),
                    (float) $product->unit_pr_ht,
                    (float) $product->total_ht,
                ], $this->customFieldValues($product));

                continue;
            }

            $rows[] = array_merge([
                $product->reference,
                $product->ref_fr,
                $product->designation,
                $product->designation_2,
                $product->category?->code,
                $product->category?->name,
                $product->supplier?->name,
                (int) ($product->entry_quantity ?? $product->quantity),
                $this->nullableDecimal($product->purchase_price_eur),
                (float) $product->unit_pr_ht,
                (float) $product->total_ht,
                $this->nullableDecimal($product->new_unit_pr_ht),
                $this->nullableDecimal($product->cump_after_entry),
                (int) $product->exit_quantity,
                (int) $product->reserved_quantity,
                (int) $product->quantity,
                $product->notes,
            ], $this->customFieldValues($product));
        }

        return $rows;
    }

    public function title(): string
    {
        return mb_substr($this->sheetTitle, 0, 31);
    }

    private function nullableDecimal(mixed $value): mixed
    {
        if (blank($value)) {
            return '';
        }

        return (float) $value;
    }

    private function customFieldValues(Product $product): array
    {
        return array_map(
            fn (string $column) => $product->custom_fields[$column] ?? '',
            $this->customColumns
        );
    }
}
