<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class UpdateHeroSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:update-hero';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update or create hero settings for the homepage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating hero settings...');

        $settings = [
            [
                'key' => 'hero_title',
                'display_name' => 'Hero Title',
                'value' => 'Discover Your<br>Celestial Beauty',
                'group' => 'hero',
                'type' => 'textarea',
                'options' => null,
                'description' => 'Main heading for the homepage hero section',
                'is_public' => true,
            ],
            [
                'key' => 'hero_description',
                'display_name' => 'Hero Description',
                'value' => 'Experience luxury skincare inspired by the cosmos. Each product is crafted to bring out your inner radiance.',
                'group' => 'hero',
                'type' => 'textarea',
                'options' => null,
                'description' => 'Description text for the homepage hero section',
                'is_public' => true,
            ],
            [
                'key' => 'shop_now_text',
                'display_name' => 'Shop Now Button Text',
                'value' => 'Shop Now',
                'group' => 'hero',
                'type' => 'text',
                'options' => null,
                'description' => 'Text for the shop now button on the homepage',
                'is_public' => true,
            ],
            [
                'key' => 'learn_more_text',
                'display_name' => 'Learn More Button Text',
                'value' => 'Learn More',
                'group' => 'hero',
                'type' => 'text',
                'options' => null,
                'description' => 'Text for the learn more button on the homepage',
                'is_public' => true,
            ],
            
            [
                'key' => 'hero_product_image',
                'display_name' => 'Hero Product Image',
                'value' => 'hero-product.png',
                'group' => 'hero',
                'type' => 'file',
                'options' => null,
                'description' => 'Featured product image in the hero section',
                'is_public' => true,
            ],
            [
                'key' => 'scroll_indicator_text',
                'display_name' => 'Scroll Indicator Text',
                'value' => 'Scroll to explore',
                'group' => 'hero',
                'type' => 'text',
                'options' => null,
                'description' => 'Text for the scroll indicator at the bottom of the hero section',
                'is_public' => true,
            ],
            [
                'key' => 'hero_shop_button_url',
                'display_name' => 'Shop Button URL',
                'value' => '/products',
                'group' => 'hero',
                'type' => 'text',
                'options' => null,
                'description' => 'URL for the Shop Now button',
                'is_public' => true,
            ],
            [
                'key' => 'hero_learn_more_url',
                'display_name' => 'Learn More Button URL',
                'value' => '/about',
                'group' => 'hero',
                'type' => 'text',
                'options' => null,
                'description' => 'URL for the Learn More button',
                'is_public' => true,
            ],
        ];

        foreach ($settings as $setting) {
            // Try to find existing setting
            $existingSetting = Setting::where('key', $setting['key'])->first();
            
            if ($existingSetting) {
                $this->info("Updating existing setting: {$setting['key']}");
                $existingSetting->update([
                    'display_name' => $setting['display_name'],
                    'group' => $setting['group'],
                    'type' => $setting['type'],
                    'description' => $setting['description'],
                    'options' => $setting['options'],
                    'is_public' => $setting['is_public'],
                ]);
            } else {
                $this->info("Creating new setting: {$setting['key']}");
                Setting::create($setting);
            }
            
            // Clear cache for this setting
            Cache::forget('setting_' . $setting['key']);
        }
        
        // Clear group caches
        Cache::forget('general_settings');
        Cache::forget('settings_group_hero');
        Cache::forget('settings_all');
        
        $this->info('Hero settings have been updated successfully!');
        
        return Command::SUCCESS;
    }
} 