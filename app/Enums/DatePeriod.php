<?php

namespace App\Enums;

enum DatePeriod: string
{
    case TODAY = 'today';
    case YESTERDAY = 'yesterday';
    case THIS_WEEK = 'this_week';
    case THIS_MONTH = 'this_month';
    case LAST_MONTH = 'last_month';
    case CUSTOM = 'custom';

    public function label(): string
    {
        return match($this) {
            self::TODAY => 'Aujourd hui',
            self::YESTERDAY => 'Hier',
            self::THIS_WEEK => 'Cette semaine',
            self::THIS_MONTH => 'Ce mois',
            self::LAST_MONTH => 'Mois dernier',
            self::CUSTOM => 'Periode personnalisee',
        };
    }
}
