<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HeaderNavigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if tables exist before trying to seed
        if (!Schema::hasTable('header_navigation_items') || !Schema::hasTable('header_settings')) {
            $this->command->info('Header navigation tables do not exist. Skipping header navigation seeder.');
            return;
        }
        
        // Clear existing entries before seeding if tables exist
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('header_navigation_items')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Create default header settings
        $headerSettings = [
            [
                'key' => 'logo',
                'value' => 'logo.svg', 
                'display_name' => 'Header Logo',
                'type' => 'file',
                'description' => 'The logo displayed in the header',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'show_search',
                'value' => '1',
                'display_name' => 'Show Search',
                'type' => 'boolean',
                'description' => 'Whether to show the search box in the header',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'show_language_switcher',
                'value' => '1',
                'display_name' => 'Show Language Switcher',
                'type' => 'boolean',
                'description' => 'Whether to show the language switcher in the header',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'show_auth_links',
                'value' => '1',
                'display_name' => 'Show Auth Links',
                'type' => 'boolean',
                'description' => 'Whether to show login/register links in the header',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'show_cart',
                'value' => '1',
                'display_name' => 'Show Cart',
                'type' => 'boolean',
                'description' => 'Whether to show the cart icon in the header',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];
        
        foreach ($headerSettings as $setting) {
            DB::table('header_settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
        
        // Create main navigation items
        $now = now();
        $navigationItems = [
            [
                'name' => 'Home',
                'name_ar' => 'الرئيسية',
                'route' => 'home',
                'url' => null,
                'translation_key' => null,
                'open_in_new_tab' => false,
                'sort_order' => 1,
                'is_active' => true,
                'has_dropdown' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Products',
                'name_ar' => 'المنتجات',
                'route' => 'products.index',
                'url' => null,
                'translation_key' => null,
                'open_in_new_tab' => false,
                'sort_order' => 2,
                'is_active' => true,
                'has_dropdown' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Special Offers',
                'name_ar' => 'عروض خاصة',
                'route' => 'offers.index',
                'url' => null,
                'translation_key' => 'navigation.special_offers',
                'open_in_new_tab' => false,
                'sort_order' => 3,
                'is_active' => true,
                'has_dropdown' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'About Us',
                'name_ar' => 'من نحن',
                'route' => 'about',
                'url' => null,
                'translation_key' => null,
                'open_in_new_tab' => false,
                'sort_order' => 4,
                'is_active' => true,
                'has_dropdown' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Contact',
                'name_ar' => 'اتصل بنا',
                'route' => 'contact',
                'url' => null,
                'translation_key' => null,
                'open_in_new_tab' => false,
                'sort_order' => 5,
                'is_active' => true,
                'has_dropdown' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        
        DB::table('header_navigation_items')->insert($navigationItems);
        
        // Add child items for Products
        $parentId = DB::table('header_navigation_items')->where('name', 'Products')->value('id');

        $childItems = [
            [
                'parent_id' => $parentId,
                'name' => 'All Products',
                'name_ar' => 'جميع المنتجات',
                'route' => 'products.index',
                'url' => null,
                'translation_key' => null,
                'open_in_new_tab' => false,
                'sort_order' => 1,
                'is_active' => true,
                'has_dropdown' => false,
            ],
            [
                'parent_id' => $parentId,
                'name' => 'Categories',
                'name_ar' => 'الفئات',
                'route' => null,
                'url' => '/products/categories',
                'translation_key' => null,
                'open_in_new_tab' => false,
                'sort_order' => 2,
                'is_active' => true,
                'has_dropdown' => false,
            ],
            [
                'parent_id' => $parentId,
                'name' => 'New Arrivals',
                'name_ar' => 'وصل حديثاً',
                'route' => null,
                'url' => '/products?sort=newest',
                'translation_key' => null,
                'open_in_new_tab' => false,
                'sort_order' => 3,
                'is_active' => true,
                'has_dropdown' => false,
            ],
        ];

        DB::table('header_navigation_items')->insert($childItems);
        
        $this->command->info('Header navigation seeded successfully.');
    }
}
