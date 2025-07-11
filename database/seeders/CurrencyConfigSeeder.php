<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencyConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('currency_config')->insert([
            'symbol' => 'ج.م',
            'position' => 'right',
            'thousand_separator' => ',',
            'decimal_separator' => '.',
            'decimal_digits' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
} 