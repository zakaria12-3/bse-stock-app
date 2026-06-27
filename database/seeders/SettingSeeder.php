<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::set('store_name', 'BSE Industrie');
        Setting::set('store_address', 'Route de Souss GP1 km 5,5, 2033 Megrine, Tunisie');
        Setting::set('store_phone', '+216 79 297 450');
        Setting::set('opening_balance_date', now()->startOfYear()->toDateString());
        Setting::set('opening_balance_amount', '10000000');
        Setting::set('currency_symbol', 'DT');
        Setting::set('currency_position', 'left');
        Setting::set('currency_fraction_digits', '0');
        Setting::set('currency_thousand_separator', '.');
        Setting::set('currency_decimal_separator', ',');
    }
}
