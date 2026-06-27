<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case TRANSFER = 'transfer';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Especes',
            self::TRANSFER => 'Virement bancaire',
        };
    }
}
