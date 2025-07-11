<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FooterSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if tables exist before trying to seed
        if (!Schema::hasTable('footer_links') || !Schema::hasTable('footer_sections') || !Schema::hasTable('footer_settings')) {
            $this->command->info('Footer tables do not exist. Skipping footer section seeder.');
            return;
        }
        
        // Check if tables already have data
        $hasFooterLinks = DB::table('footer_links')->count() > 0;
        $hasFooterSections = DB::table('footer_sections')->count() > 0;
        $hasFooterSettings = DB::table('footer_settings')->count() > 0;
        
        if ($hasFooterLinks && $hasFooterSections && $hasFooterSettings) {
            $this->command->info('Footer data already exists. Skipping footer section seeder.');
            return;
        }
        
        // Clear existing entries only if tables are empty
        if (!$hasFooterLinks) {
            DB::table('footer_links')->truncate();
        }
        
        if (!$hasFooterSections) {
            DB::table('footer_sections')->truncate();
        }
        
        if (!$hasFooterSettings) {
            DB::table('footer_settings')->truncate();
        }
        
        // Create default footer settings if empty
        if (!$hasFooterSettings) {
            $footerSettings = [
                [
                    'key' => 'copyright_text',
                    'value' => '© 2025 Celestial Cosmetics. All rights reserved.',
                    'display_name' => 'Footer Copyright Text',
                    'type' => 'text',
                    'description' => 'The copyright text displayed in the footer',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'key' => 'copyright_text_ar',
                    'value' => '© 2025 سيليستيال كوزمتكس. جميع الحقوق محفوظة.',
                    'display_name' => 'Footer Copyright Text (Arabic)',
                    'type' => 'text',
                    'description' => 'The copyright text displayed in the footer (Arabic)',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'key' => 'show_newsletter',
                    'value' => '1',
                    'display_name' => 'Show Newsletter',
                    'type' => 'boolean',
                    'description' => 'Whether to show the newsletter signup in the footer',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'key' => 'show_social_icons',
                    'value' => '1',
                    'display_name' => 'Show Social Icons',
                    'type' => 'boolean',
                    'description' => 'Whether to show social media icons in the footer',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'key' => 'facebook_url',
                    'value' => 'https://facebook.com',
                    'display_name' => 'Facebook URL',
                    'type' => 'text',
                    'description' => 'Facebook page URL',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'key' => 'twitter_url',
                    'value' => 'https://twitter.com',
                    'display_name' => 'Twitter URL',
                    'type' => 'text',
                    'description' => 'Twitter profile URL',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'key' => 'instagram_url',
                    'value' => 'https://instagram.com',
                    'display_name' => 'Instagram URL',
                    'type' => 'text',
                    'description' => 'Instagram profile URL',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
            ];
            
            foreach ($footerSettings as $setting) {
                try {
                    DB::table('footer_settings')->insert($setting);
                } catch (\Exception $e) {
                    $this->command->error("Error inserting footer setting '{$setting['key']}': " . $e->getMessage());
                }
            }
        }
        
        // Create footer sections if empty
        if (!$hasFooterSections) {
            $sections = [
                [
                    'title' => 'Quick Links',
                    'title_ar' => 'روابط سريعة',
                    'type' => 'links',
                    'sort_order' => 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'links' => [
                        [
                            'title' => 'Home',
                            'url' => '/',
                            'sort_order' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        [
                            'title' => 'Products',
                            'url' => '/products',
                            'sort_order' => 2,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        [
                            'title' => 'New Arrivals',
                            'url' => '/products?sort=newest',
                            'sort_order' => 3,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        [
                            'title' => 'About Us',
                            'url' => '/about',
                            'sort_order' => 4,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        [
                            'title' => 'Contact',
                            'url' => '/contact',
                            'sort_order' => 5,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                    ]
                ],
                [
                    'title' => 'Categories',
                    'title_ar' => 'الفئات',
                    'type' => 'links',
                    'sort_order' => 2,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'links' => [
                        [
                            'title' => 'Skincare',
                            'url' => '/products/category/skincare',
                            'sort_order' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        [
                            'title' => 'Makeup',
                            'url' => '/products/category/makeup',
                            'sort_order' => 2,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        [
                            'title' => 'Body Care',
                            'url' => '/products/category/body-care',
                            'sort_order' => 3,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                    ]
                ],
                [
                    'title' => 'Newsletter',
                    'title_ar' => 'النشرة الإخبارية',
                    'type' => 'newsletter',
                    'sort_order' => 3,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'links' => []
                ],
                [
                    'title' => 'Contact',
                    'title_ar' => 'اتصل بنا',
                    'type' => 'contact',
                    'sort_order' => 4,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'links' => []
                ],
            ];
            
            foreach ($sections as $sectionData) {
                $links = $sectionData['links'] ?? [];
                unset($sectionData['links']);
                
                try {
                    // Insert section and get the ID
                    DB::table('footer_sections')->insert($sectionData);
                    $sectionId = DB::getPdo()->lastInsertId();
                    
                    // Check if footer_links table has a section_id or column_id field
                    $columnName = 'column_id';
                    if (Schema::hasColumn('footer_links', 'section_id')) {
                        $columnName = 'section_id';
                    }
                    
                    // Insert links for this section
                    foreach ($links as $link) {
                        $link[$columnName] = $sectionId;
                        try {
                            DB::table('footer_links')->insert($link);
                        } catch (\Exception $e) {
                            $this->command->error("Error inserting footer link: " . $e->getMessage());
                        }
                    }
                } catch (\Exception $e) {
                    $this->command->error("Error inserting footer section '{$sectionData['title']}': " . $e->getMessage());
                }
            }
        }
        
        $this->command->info('Footer sections seeded successfully.');
    }
}
