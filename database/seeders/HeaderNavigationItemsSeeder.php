<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HeaderNavigationItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing items
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('header_navigation_items')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Add default navigation items
        $items = [
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
            ],
            [
                'name' => 'About Us',
                'name_ar' => 'من نحن',
                'route' => 'about',
                'url' => null,
                'translation_key' => null,
                'open_in_new_tab' => false,
                'sort_order' => 3,
                'is_active' => true,
                'has_dropdown' => false,
            ],
            [
                'name' => 'Contact',
                'name_ar' => 'اتصل بنا',
                'route' => 'contact',
                'url' => null,
                'translation_key' => null,
                'open_in_new_tab' => false,
                'sort_order' => 4,
                'is_active' => true,
                'has_dropdown' => false,
            ],
        ];

        DB::table('header_navigation_items')->insert($items);

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
    }
} 