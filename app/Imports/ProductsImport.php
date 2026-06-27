<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Unit;
use App\Services\CumpCalculator;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsImport
{
    public function import(string $path): array
    {
        $spreadsheet = IOFactory::load($path);

        $processedSheets = 0;
        $processedProducts = 0;

        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $headerMap = $this->buildHeaderMap($worksheet);

            if (!$this->isStockSheet($headerMap)) {
                continue;
            }

            $processedSheets++;
            $processedProducts += $this->importWorksheet($worksheet, $headerMap);
        }

        return [
            'sheets' => $processedSheets,
            'products' => $processedProducts,
        ];
    }

    private function importWorksheet(Worksheet $worksheet, array $headerMap): int
    {
        $sheetName = trim($worksheet->getTitle());
        $highestRow = $worksheet->getHighestDataRow();
        $imported = 0;
        $defaultUnitId = $this->resolveDefaultUnitId();

        for ($row = 2; $row <= $highestRow; $row++) {
            $reference = $this->text($this->cell($worksheet, $row, $headerMap, 'reference_article'));
            $designation = $this->text($this->cell($worksheet, $row, $headerMap, 'designation_article'));

            if (!$reference || !$designation) {
                continue;
            }

            $familyCode = $this->text($this->cell($worksheet, $row, $headerMap, 'famille_article'));
            $familyName = $this->text($this->cell($worksheet, $row, $headerMap, 'designation_famille')) ?: $sheetName;
            $supplierName = $this->text($this->cell($worksheet, $row, $headerMap, 'fournisseur'));

            $entryQuantity = (int) $this->number($this->cell($worksheet, $row, $headerMap, 'entry_quantity'));
            $purchasePriceEur = $this->nullableNumber($this->cell($worksheet, $row, $headerMap, 'purchase_price_eur'));
            $unitPrHt = $this->number($this->cell($worksheet, $row, $headerMap, 'unit_pr_ht'));
            $sheetTotalHt = $this->nullableNumber($this->cell($worksheet, $row, $headerMap, 'total_ht')) ?? 0;
            $totalHt = $this->resolveTotalHt($sheetTotalHt, $entryQuantity, $unitPrHt);

            $newUnitPrHt = $this->nullableNumber($this->cell($worksheet, $row, $headerMap, 'new_unit_pr_ht'));
            $exitQuantity = (int) $this->number($this->cell($worksheet, $row, $headerMap, 'exit_quantity'));
            $reservedQuantity = (int) $this->number($this->cell($worksheet, $row, $headerMap, 'reserved_quantity'));
            $stockRemaining = $this->nullableNumber($this->cell($worksheet, $row, $headerMap, 'stock_remaining'));
            $observation = $this->text($this->cell($worksheet, $row, $headerMap, 'observation'));
            $availableQuantity = $stockRemaining !== null
                ? (int) round($stockRemaining)
                : max($entryQuantity - $exitQuantity - $reservedQuantity, 0);

            $category = $this->resolveCategory($familyName, $familyCode, $sheetName);

            $categoryUpdates = [];
            if ($familyCode && $category->code !== $familyCode) {
                $categoryUpdates['code'] = $familyCode;
            }
            if ($sheetName && $category->worksheet_name !== $sheetName) {
                $categoryUpdates['worksheet_name'] = $sheetName;
            }
            if ($categoryUpdates !== []) {
                $category->update($categoryUpdates);
            }

            $supplierId = null;
            if ($supplierName) {
                $supplier = Supplier::firstOrCreate(
                    ['name' => $supplierName],
                    ['contact_person' => null]
                );

                $supplierId = $supplier->id;
            }

            $existingProduct = Product::where('reference', $reference)->first();
            $previousEntryQuantity = (int) ($existingProduct?->entry_quantity ?? 0);
            $previousUnitPrHt = (float) ($existingProduct?->unit_pr_ht ?? $unitPrHt);
            $cumpAfterEntry = $this->nullableNumber($this->cell($worksheet, $row, $headerMap, 'cump_after_entry'))
                ?? CumpCalculator::fromTotalQuantity($previousEntryQuantity, $entryQuantity, $previousUnitPrHt, $newUnitPrHt)
                ?? $unitPrHt;
            $customFields = array_merge(
                $existingProduct?->custom_fields ?? [],
                $this->customFields($worksheet, $row, $headerMap)
            );

            Product::updateOrCreate(
                ['reference' => $reference],
                [
                    'ref_fr' => $this->text($this->cell($worksheet, $row, $headerMap, 'ref_fr')),
                    'designation' => $designation,
                    'designation_2' => $this->text($this->cell($worksheet, $row, $headerMap, 'designation_2')),
                    'category_id' => $category->id,
                    'supplier_id' => $supplierId,
                    'unit_id' => $defaultUnitId,
                    'purchase_price_eur' => $purchasePriceEur,
                    'unit_pr_ht' => $unitPrHt,
                    'new_unit_pr_ht' => $newUnitPrHt,
                    'cump_after_entry' => $cumpAfterEntry,
                    'selling_price' => $unitPrHt,
                    'entry_quantity' => $entryQuantity,
                    'quantity' => $availableQuantity,
                    'exit_quantity' => $exitQuantity,
                    'reserved_quantity' => $reservedQuantity,
                    'total_ht' => $totalHt,
                    'source_sheet_name' => $sheetName,
                    'notes' => $observation,
                    'custom_fields' => $customFields !== [] ? $customFields : null,
                    'is_active' => true,
                ]
            );

            $imported++;
        }

        return $imported;
    }

    private function buildHeaderMap(Worksheet $worksheet): array
    {
        $highestColumnIndex = Coordinate::columnIndexFromString($worksheet->getHighestDataColumn());
        $map = [];

        for ($column = 1; $column <= $highestColumnIndex; $column++) {
            $normalized = $this->normalizeHeader($worksheet->getCellByColumnAndRow($column, 1)->getValue());

            if ($normalized !== '') {
                $map[$normalized] = $column;
            }
        }

        return $map;
    }

    private function customFields(Worksheet $worksheet, int $row, array $headerMap): array
    {
        $fields = [];
        $knownHeaders = $this->knownHeaders();

        foreach ($headerMap as $normalizedHeader => $column) {
            if (in_array($normalizedHeader, $knownHeaders, true)) {
                continue;
            }

            $rawValue = $worksheet->getCellByColumnAndRow($column, $row)->getCalculatedValue();

            if ($rawValue === null || trim((string) $rawValue) === '') {
                continue;
            }

            $label = trim((string) $worksheet->getCellByColumnAndRow($column, 1)->getValue());
            $fields[$label !== '' ? $label : $normalizedHeader] = is_numeric($rawValue)
                ? round((float) $rawValue, 3)
                : trim((string) $rawValue);
        }

        return $fields;
    }

    private function isStockSheet(array $headerMap): bool
    {
        return $this->hasHeader($headerMap, 'reference_article')
            && $this->hasHeader($headerMap, 'designation_article')
            && $this->hasHeader($headerMap, 'famille_article')
            && $this->hasHeader($headerMap, 'designation_famille')
            && $this->hasHeader($headerMap, 'pr_unitaire_ht');
    }

    private function cell(Worksheet $worksheet, int $row, array $headerMap, string $field): mixed
    {
        $aliases = $this->fieldAliases();

        foreach ($aliases[$field] ?? [$field] as $alias) {
            if (isset($headerMap[$alias])) {
                return $worksheet->getCellByColumnAndRow($headerMap[$alias], $row)->getCalculatedValue();
            }
        }

        return null;
    }

    private function normalizeHeader(mixed $value): string
    {
        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return '';
        }

        $value = str_replace(['€', 'â‚¬'], ' eur ', $value);
        $value = strtr($value, [
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
            'Æ' => 'AE', 'æ' => 'ae',
            'Ç' => 'C', 'ç' => 'c',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'Ñ' => 'N', 'ñ' => 'n',
            'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'Œ' => 'OE', 'œ' => 'oe',
            'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'Ý' => 'Y', 'Ÿ' => 'Y', 'ý' => 'y', 'ÿ' => 'y',
            'Ã©' => 'e', 'Ã¨' => 'e', 'Ãª' => 'e', 'Ã«' => 'e',
            'Ã ' => 'a', 'Ã¢' => 'a', 'Ã¤' => 'a',
            'Ã¹' => 'u', 'Ã»' => 'u', 'Ã¼' => 'u',
            'Ã®' => 'i', 'Ã¯' => 'i',
            'Ã´' => 'o', 'Ã¶' => 'o',
            'Ã§' => 'c',
            'Å“' => 'oe',
        ]);

        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        $value = $ascii === false ? $value : $ascii;
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/', '_', $value);

        return trim((string) $value, '_');
    }

    private function hasHeader(array $headerMap, string $field): bool
    {
        $aliases = $this->fieldAliases();

        foreach ($aliases[$field] ?? [$field] as $alias) {
            if (isset($headerMap[$alias])) {
                return true;
            }
        }

        return false;
    }

    private function fieldAliases(): array
    {
        return [
            'reference_article' => ['reference_article', 'r_f_rence_article'],
            'ref_fr' => ['ref_fr'],
            'designation_article' => ['designation_article', 'd_signation_article'],
            'designation_2' => ['designation_2'],
            'famille_article' => ['famille_article'],
            'designation_famille' => ['designation_famille', 'd_signation_famille'],
            'fournisseur' => ['fournisseur'],
            'entry_quantity' => ['qte', 'entre_en_qte', 'entr_en_qt'],
            'purchase_price_eur' => ['prix_d_achat', 'prix_d_achat_eur', 'prix_dachat', 'prix_dachat_eur'],
            'unit_pr_ht' => ['pr_unitaire_ht'],
            'total_ht' => ['total_ht', 'prix_total'],
            'new_unit_pr_ht' => ['nouveau_pr_unitaire_ht'],
            'cump_after_entry' => ['cump_apres_chaque_entree', 'cump_apr_s_chaque_entr_e', 'cump_pr_ht_dossier'],
            'exit_quantity' => ['sortie_en_qte', 'sortie_en_qt'],
            'reserved_quantity' => ['reservation', 'r_servation'],
            'stock_remaining' => ['stock_restant'],
            'observation' => ['observation', 'observations'],
        ];
    }

    private function knownHeaders(): array
    {
        return array_values(array_unique(array_merge(...array_values($this->fieldAliases()))));
    }

    private function text(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }

    private function number(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return round((float) $value, 3);
        }

        $normalized = str_replace([' ', "\xc2\xa0"], '', (string) $value);
        $normalized = str_replace(',', '.', $normalized);

        return is_numeric($normalized) ? round((float) $normalized, 3) : 0;
    }

    private function nullableNumber(mixed $value): ?float
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        return $this->number($value);
    }

    private function resolveTotalHt(float $sheetTotal, int $quantity, float $unitPrHt): float
    {
        if ($sheetTotal > 0) {
            return $sheetTotal;
        }

        return round($quantity * $unitPrHt, 3);
    }

    private function resolveCategory(string $familyName, ?string $familyCode, string $sheetName): Category
    {
        $category = null;

        if ($familyCode) {
            $category = Category::where('code', $familyCode)->first();
        }

        if (!$category) {
            $category = Category::where('name', $familyName)->first();
        }

        if ($category) {
            return $category;
        }

        return Category::create([
            'code' => $familyCode,
            'worksheet_name' => $sheetName,
            'name' => $familyName,
            'description' => null,
        ]);
    }

    private function resolveDefaultUnitId(): ?int
    {
        $unit = Unit::firstOrCreate(
            ['symbol' => 'pc'],
            ['name' => 'Piece']
        );

        return $unit->id;
    }
}
