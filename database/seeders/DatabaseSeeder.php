<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test user if it doesn't already exist
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
            $this->command->info('Test user created.');
        } else {
            $this->command->info('Test user already exists, skipping creation.');
        }

        // Run user and role seeders first
        $this->call([
            AdminUserSeeder::class,
            UpdateUsersRolesPermissionsSeeder::class,
        ]);

        // Check if categories and products tables exist
        if (!Schema::hasTable('categories') || !Schema::hasTable('products')) {
            $this->command->info('Categories or products tables do not exist. Skipping product seeding.');
        } else {
            // Create Categories if none exist
            if (Category::count() === 0) {
                $categories = [
                    [
                        'name' => 'Skincare',
                        'description' => 'Luxurious skincare products for radiant skin',
                        'image' => '1 (4).jpg'
                    ],
                    [
                        'name' => 'Makeup',
                        'description' => 'Beautiful makeup for a celestial glow',
                        'image' => '1 (5).jpg'
                    ],
                    [
                        'name' => 'Body Care',
                        'description' => 'Indulgent body care products for total relaxation',
                        'image' => '1 (6).jpg'
                    ]
                ];

                foreach ($categories as $category) {
                    Category::create([
                        'name' => $category['name'],
                        'slug' => Str::slug($category['name']),
                        'description' => $category['description'],
                        'image' => $category['image']
                    ]);
                }
                $this->command->info('Categories created.');
            } else {
                $this->command->info('Categories already exist, skipping creation.');
            }

            // Create Products if none exist
            if (Product::count() === 0) {
                $products = [
                    [
                        'name' => 'Celestial Glow Serum',
                        'description' => 'A lightweight serum that brings out your inner radiance',
                        'price' => 49.99,
                        'category_id' => 1,
                        'is_featured' => true,
                        'stock' => 100,
                        'image' => '1 (7).jpg',
                        'ingredients' => 'Hyaluronic Acid, Vitamin C, Niacinamide',
                        'how_to_use' => 'Apply 2-3 drops to clean skin morning and night.',
                        'additional_images' => [
                            '1 (8).jpg',
                            '1 (9).jpg'
                        ]
                    ],
                    [
                        'name' => 'Starlight Shimmer Highlighter',
                        'description' => 'A celestial-inspired highlighter for an otherworldly glow',
                        'price' => 35.99,
                        'category_id' => 2,
                        'is_featured' => true,
                        'stock' => 75,
                        'image' => '1 (10).jpg',
                        'ingredients' => 'Mica, Titanium Dioxide, Iron Oxides',
                        'how_to_use' => 'Apply to high points of face for a celestial glow.',
                        'additional_images' => [
                            '1 (11).jpg',
                            '1 (12).jpg'
                        ]
                    ],
                    [
                        'name' => 'Cosmic Bath Elixir',
                        'description' => 'Transform your bath into a cosmic experience',
                        'price' => 29.99,
                        'category_id' => 3,
                        'is_featured' => true,
                        'stock' => 50,
                        'image' => '1 (14).jpg',
                        'ingredients' => 'Essential Oils, Sea Salts, Botanical Extracts',
                        'how_to_use' => 'Add to warm bath water and soak for 20 minutes.',
                        'additional_images' => [
                            '1 (17).jpg',
                            '1 (18).jpg'
                        ]
                    ]
                ];

                foreach ($products as $productData) {
                    $additionalImages = $productData['additional_images'] ?? [];
                    unset($productData['additional_images']);
                    
                    $product = Product::create([
                        'name' => $productData['name'],
                        'slug' => Str::slug($productData['name']),
                        'description' => $productData['description'],
                        'price' => $productData['price'],
                        'category_id' => $productData['category_id'],
                        'is_featured' => $productData['is_featured'],
                        'stock' => $productData['stock'],
                        'image' => $productData['image'],
                        'ingredients' => $productData['ingredients'],
                        'how_to_use' => $productData['how_to_use']
                    ]);

                    // Create primary product image
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $productData['image'],
                        'is_primary' => true
                    ]);

                    // Create additional product images
                    foreach ($additionalImages as $image) {
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image' => $image,
                            'is_primary' => false
                        ]);
                    }
                }
                $this->command->info('Products created.');
            } else {
                $this->command->info('Products already exist, skipping creation.');
            }
        }
        
        // Run remaining seeders after products are created
        $this->call([
            SettingsSeeder::class,
            TestimonialsSeeder::class,
            ShippingSettingsSeeder::class,
            MissingSettingsSeeder::class,
            UpdatePermissionsSeeder::class,
            OfferSeeder::class,
            AdditionalEmailTemplatesSeeder::class,
            HomepageSettingsSeeder::class,
            HomepageSectionsSeeder::class,
            OurStoryContentSeeder::class,
            LegalPagesSeeder::class,
            AboutPageSeeder::class,
            CurrencyConfigSeeder::class,
            ShippingConfigSeeder::class,
            SeoDefaultsSeeder::class,
            // New seeders
            GeneralSettingsSeeder::class,
            PaymentConfigSeeder::class,
            ContactMessagesSeeder::class,
            ThemeSeeder::class,
            FooterSeeder::class,
            StoreHoursSeeder::class,
        ]);
    }
}
