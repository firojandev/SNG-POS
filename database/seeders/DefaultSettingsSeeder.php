<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Option;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default currencies
        $currencies = [
            ['name' => 'USD', 'symbol' => '$'],
            ['name' => 'EUR', 'symbol' => '€'],
            ['name' => 'BDT', 'symbol' => '৳'],
            ['name' => 'GBP', 'symbol' => '£'],
            ['name' => 'JPY', 'symbol' => '¥'],
            ['name' => 'INR', 'symbol' => '₹'],
        ];

        foreach ($currencies as $currency) {
            Currency::firstOrCreate(
                ['name' => $currency['name']],
                ['symbol' => $currency['symbol']]
            );
        }

        // Create default application settings
        $defaultSettings = [
            'app_name' => 'SNG POS',
            'app_address' => '',
            'app_phone' => '',
            'date_format' => 'Y-m-d',
            'app_logo' => '',
            'app_favicon' => '',
            'app_currency' => '$',
        ];

        foreach ($defaultSettings as $key => $value) {
            Option::firstOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->command->info('Default settings and currencies created successfully!');
    }
}
