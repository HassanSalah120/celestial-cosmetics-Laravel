<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Theme;
use Illuminate\Support\Facades\DB;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing themes
        DB::table('themes')->truncate();

        // Default Theme (Celestial Blue)
        Theme::create([
            'name' => 'Celestial Blue',
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
            ],
            'is_active' => true,
        ]);

        // Modern Purple
        Theme::create([
            'name' => 'Modern Purple',
            'colors' => [
                'primary' => '#6a1b9a',
                'primary-light' => '#8e24aa',
                'primary-dark' => '#4a148c',
                'secondary' => '#283593',
                'secondary-light' => '#3949ab',
                'secondary-dark' => '#1a237e',
                'accent' => '#ffc107',
                'accent-light' => '#ffca28',
                'accent-dark' => '#ffb300',
            ],
            'is_active' => false,
        ]);

        // Emerald Green
        Theme::create([
            'name' => 'Emerald Green',
            'colors' => [
                'primary' => '#2e7d32',
                'primary-light' => '#388e3c',
                'primary-dark' => '#1b5e20',
                'secondary' => '#37474f',
                'secondary-light' => '#455a64',
                'secondary-dark' => '#263238',
                'accent' => '#ff8f00',
                'accent-light' => '#ffa000',
                'accent-dark' => '#ff6f00',
            ],
            'is_active' => false,
        ]);

        // Midnight Blue
        Theme::create([
            'name' => 'Midnight Blue',
            'colors' => [
                'primary' => '#0d47a1',
                'primary-light' => '#1565c0',
                'primary-dark' => '#002171',
                'secondary' => '#212121',
                'secondary-light' => '#424242',
                'secondary-dark' => '#000000',
                'accent' => '#e64a19',
                'accent-light' => '#ff5722',
                'accent-dark' => '#bf360c',
            ],
            'is_active' => false,
        ]);

        // Rose Gold
        Theme::create([
            'name' => 'Rose Gold',
            'colors' => [
                'primary' => '#c48b9f',
                'primary-light' => '#d9a3b8',
                'primary-dark' => '#a86e81',
                'secondary' => '#5d4037',
                'secondary-light' => '#795548',
                'secondary-dark' => '#3e2723',
                'accent' => '#ffb74d',
                'accent-light' => '#ffc77d',
                'accent-dark' => '#c88719',
            ],
            'is_active' => false,
        ]);

        // Dark Mode
        Theme::create([
            'name' => 'Dark Mode',
            'colors' => [
                'primary' => '#2d3748',
                'primary-light' => '#4a5568',
                'primary-dark' => '#1a202c',
                'secondary' => '#718096',
                'secondary-light' => '#a0aec0',
                'secondary-dark' => '#4a5568',
                'accent' => '#9f7aea',
                'accent-light' => '#b794f4',
                'accent-dark' => '#805ad5',
            ],
            'is_active' => false,
        ]);

        // Cosmic Dark
        Theme::create([
            'name' => 'Cosmic Dark',
            'colors' => [
                'primary' => '#1e1e2f',
                'primary-light' => '#2d2d44',
                'primary-dark' => '#0f0f1a',
                'secondary' => '#6f42c1',
                'secondary-light' => '#8557d7',
                'secondary-dark' => '#5a32a3',
                'accent' => '#00d0ff',
                'accent-light' => '#33daff',
                'accent-dark' => '#00a6cc',
            ],
            'is_active' => false,
        ]);

        // Nebula Glow
        Theme::create([
            'name' => 'Nebula Glow',
            'colors' => [
                'primary' => '#271b38',
                'primary-light' => '#372952',
                'primary-dark' => '#1a1225',
                'secondary' => '#ff3d71',
                'secondary-light' => '#ff6694',
                'secondary-dark' => '#c8005e',
                'accent' => '#36f1cd',
                'accent-light' => '#74ffea',
                'accent-dark' => '#00c4a1',
            ],
            'is_active' => false,
        ]);

        // Sunset Orange
        Theme::create([
            'name' => 'Sunset Orange',
            'colors' => [
                'primary' => '#e64a19',
                'primary-light' => '#ff7d47',
                'primary-dark' => '#ac0800',
                'secondary' => '#455a64',
                'secondary-light' => '#718792',
                'secondary-dark' => '#1c313a',
                'accent' => '#ffeb3b',
                'accent-light' => '#ffff72',
                'accent-dark' => '#c8b900',
            ],
            'is_active' => false,
        ]);

        // Mint Fresh
        Theme::create([
            'name' => 'Mint Fresh',
            'colors' => [
                'primary' => '#00897b',
                'primary-light' => '#4ebaaa',
                'primary-dark' => '#005b4f',
                'secondary' => '#26a69a',
                'secondary-light' => '#64d8cb',
                'secondary-dark' => '#00766c',
                'accent' => '#ffd54f',
                'accent-light' => '#ffff81',
                'accent-dark' => '#c8a415',
            ],
            'is_active' => false,
        ]);

        // Coral Reef
        Theme::create([
            'name' => 'Coral Reef',
            'colors' => [
                'primary' => '#ff7043',
                'primary-light' => '#ffa270',
                'primary-dark' => '#c63f17',
                'secondary' => '#29b6f6',
                'secondary-light' => '#73e8ff',
                'secondary-dark' => '#0086c3',
                'accent' => '#ffab40',
                'accent-light' => '#ffdd71',
                'accent-dark' => '#c77c02',
            ],
            'is_active' => false,
        ]);

        // Nordic Blue
        Theme::create([
            'name' => 'Nordic Blue',
            'colors' => [
                'primary' => '#37474f',
                'primary-light' => '#62727b',
                'primary-dark' => '#102027',
                'secondary' => '#78909c',
                'secondary-light' => '#a7c0cd',
                'secondary-dark' => '#4b636e',
                'accent' => '#b3e5fc',
                'accent-light' => '#e6ffff',
                'accent-dark' => '#82b3c9',
            ],
            'is_active' => false,
        ]);

        // Starlight Galaxy
        Theme::create([
            'name' => 'Starlight Galaxy',
            'colors' => [
                'primary' => '#0b0b1a',
                'primary-light' => '#191932',
                'primary-dark' => '#050510',
                'secondary' => '#e83e8c',
                'secondary-light' => '#f16ba6',
                'secondary-dark' => '#c21f6b',
                'accent' => '#7df9ff',
                'accent-light' => '#a5fbff',
                'accent-dark' => '#00e5f7',
            ],
            'is_active' => false,
        ]);

        // Forest Green
        Theme::create([
            'name' => 'Forest Green',
            'colors' => [
                'primary' => '#2e7d32',
                'primary-light' => '#60ad5e',
                'primary-dark' => '#005005',
                'secondary' => '#66bb6a',
                'secondary-light' => '#98ee99',
                'secondary-dark' => '#338a3e',
                'accent' => '#ffca28',
                'accent-light' => '#fffd61',
                'accent-dark' => '#c79a00',
            ],
            'is_active' => false,
        ]);

        // Berry Bliss
        Theme::create([
            'name' => 'Berry Bliss',
            'colors' => [
                'primary' => '#ad1457',
                'primary-light' => '#e35183',
                'primary-dark' => '#78002e',
                'secondary' => '#ec407a',
                'secondary-light' => '#ff77a9',
                'secondary-dark' => '#b4004e',
                'accent' => '#7e57c2',
                'accent-light' => '#b085f5',
                'accent-dark' => '#4d2c91',
            ],
            'is_active' => false,
        ]);
    }
} 