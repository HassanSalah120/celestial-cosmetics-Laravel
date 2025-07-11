<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeoDefaultsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if the table exists
        if (!Schema::hasTable('seo_defaults')) {
            $this->command->info('seo_defaults table does not exist. Skipping SeoDefaultsSeeder.');
            return;
        }

        // Check if we already have SEO defaults
        if (DB::table('seo_defaults')->count() > 0) {
            $this->command->info('SeoDefaults already exist. Skipping...');
            return;
        }

        // Get the column names from the table
        $columns = Schema::getColumnListing('seo_defaults');
        
        // Prepare the data
        $data = [
            'default_meta_title' => 'Celestial Cosmetics - Beauty Products',
            'default_meta_description' => 'Discover high-quality beauty products from Celestial Cosmetics.',
            'default_meta_keywords' => 'cosmetics, beauty, skincare',
            'og_site_name' => 'Celestial Cosmetics',
            'twitter_site' => '@celestialcosm',
            'twitter_creator' => '@celestialcosm',
            'default_robots_content' => 'User-agent: *
Allow: /
Disallow: /admin/
Disallow: /checkout/
Disallow: /cart/
Disallow: /account/
Sitemap: ' . url('sitemap.xml'),
            'enable_structured_data' => true,
            'enable_robots_txt' => true,
            'enable_sitemap' => true,
            'sitemap_change_frequency' => 'weekly',
            'created_at' => now(),
            'updated_at' => now()
        ];
        
        // Only include schema fields if they exist in the table
        if (in_array('default_schema_product', $columns)) {
            $data['default_schema_product'] = json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => '{{name}}',
                'description' => '{{description}}',
                'image' => '{{image}}',
                'brand' => [
                    '@type' => 'Brand',
                    'name' => 'Celestial Cosmetics'
                ],
                'offers' => [
                    '@type' => 'Offer',
                    'priceCurrency' => '{{currency}}',
                    'price' => '{{price}}',
                    'availability' => '{{availability}}'
                ]
            ]);
        }
        
        if (in_array('default_schema_breadcrumbs', $columns)) {
            $data['default_schema_breadcrumbs'] = json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'Home',
                        'item' => '{{home_url}}'
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => '{{category_name}}',
                        'item' => '{{category_url}}'
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => '{{product_name}}',
                        'item' => '{{product_url}}'
                    ]
                ]
            ]);
        }
        
        // Filter data to only include columns that exist in the table
        $filteredData = array_intersect_key($data, array_flip($columns));
        
        // Insert the data
        DB::table('seo_defaults')->insert($filteredData);

        $this->command->info('SeoDefaults created successfully.');
    }
}
