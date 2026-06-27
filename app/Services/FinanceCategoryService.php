<?php

namespace App\Services;

use App\Models\FinanceCategory;
use App\DTOs\FinanceCategoryData;
use Illuminate\Support\Facades\DB;
use App\Exceptions\FinanceCategoryException;

class FinanceCategoryService
{
    public function createCategory(FinanceCategoryData $data): FinanceCategory
    {
        try {
            return DB::transaction(function () use ($data) {
                return FinanceCategory::create([
                    'name' => $data->name,
                    'slug' => $data->slug,
                    'type' => $data->type,
                    'description' => $data->description,
                ]);
            });
        } catch (\Exception $e) {
            throw FinanceCategoryException::creationFailed($e->getMessage());
        }
    }

    public function updateCategory(FinanceCategory $category, FinanceCategoryData $data): FinanceCategory
    {
        try {
            return DB::transaction(function () use ($category, $data) {
                $category->update([
                    'name' => $data->name,
                    'slug' => $data->slug,
                    'type' => $data->type,
                    'description' => $data->description,
                ]);

                return $category->fresh();
            });
        } catch (\Exception $e) {
            throw FinanceCategoryException::updateFailed($e->getMessage());
        }
    }

    public function deleteCategory(FinanceCategory $category): void
    {
        try {
            DB::transaction(function () use ($category) {
                // Protect System Categories
                if (in_array($category->name, ['Product Sales', 'Product Purchases', 'Ventes de pieces', 'Achats de pieces'])) {
                    throw new \Exception('Les categories systeme ne peuvent pas etre supprimees.');
                }

                if ($category->transactions()->exists()) {
                    throw new \Exception('Impossible de supprimer cette categorie car elle contient des transactions.');
                }
                $category->delete();
            });
        } catch (\Exception $e) {
            throw FinanceCategoryException::deletionFailed($e->getMessage());
        }
    }
}
