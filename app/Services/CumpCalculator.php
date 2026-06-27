<?php

namespace App\Services;

class CumpCalculator
{
    public static function fromEntry(
        int|float $oldQuantity,
        int|float $oldUnitPrice,
        int|float $entryQuantity,
        int|float|null $entryUnitPrice
    ): ?float {
        $oldQuantity = max((float) $oldQuantity, 0);
        $oldUnitPrice = max((float) $oldUnitPrice, 0);
        $entryQuantity = max((float) $entryQuantity, 0);

        if ($entryUnitPrice === null || $entryQuantity <= 0) {
            return $oldUnitPrice > 0 ? round($oldUnitPrice, 3) : null;
        }

        $entryUnitPrice = max((float) $entryUnitPrice, 0);
        $totalQuantity = $oldQuantity + $entryQuantity;

        if ($totalQuantity <= 0) {
            return null;
        }

        return round((($oldQuantity * $oldUnitPrice) + ($entryQuantity * $entryUnitPrice)) / $totalQuantity, 3);
    }

    public static function fromTotalQuantity(
        int|float $oldQuantity,
        int|float $newTotalQuantity,
        int|float $oldUnitPrice,
        int|float|null $entryUnitPrice
    ): ?float {
        $entryQuantity = max((float) $newTotalQuantity - (float) $oldQuantity, 0);

        return self::fromEntry($oldQuantity, $oldUnitPrice, $entryQuantity, $entryUnitPrice);
    }
}
