<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Theme;

class ThemesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('themes')->truncate();

        Theme::create([
            'name' => 'Celestial Teal',
            'is_active' => true,
            'colors' => [
                'primary' => '#1f5964',
                'primary-light' => '#2d6e7e',
                'primary-dark' => '#174853',
                'secondary' => '#312e43',
                'secondary-light' => '#423f5a',
                'secondary-dark' => '#272536',
                'accent' => '#d4af37',
                'accent-light' => '#dbba5d',
                'accent-dark' => '#b3932e',
            ]
        ]);

        Theme::create([
            'name' => 'Golden Radiance',
            'is_active' => false,
            'colors' => [
                'primary' => '#d4af37',
                'primary-light' => '#dbba5d',
                'primary-dark' => '#b3932e',
                'secondary' => '#312e43',
                'secondary-light' => '#423f5a',
                'secondary-dark' => '#272536',
                'accent' => '#1f5964',
                'accent-light' => '#2d6e7e',
                'accent-dark' => '#174853',
            ]
        ]);
    }
}
