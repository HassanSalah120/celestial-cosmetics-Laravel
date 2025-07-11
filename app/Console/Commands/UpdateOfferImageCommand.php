<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Offer;
use App\Models\Product;
use App\Facades\Settings;
use Carbon\Carbon;

class UpdateOfferImageCommand extends Command
{
    protected $signature = 'offers:update-image';
    protected $description = 'Update the first offer image path and ensure its visibility';

    public function handle()
    {
        $offer = Offer::first();
        
        if (!$offer) {
            $this->error('No offers found in the database.');
            return 1;
        }
        
        // Get a random product to associate with the offer
        $product = Product::where('is_visible', true)->inRandomOrder()->first();
        
        $offer->image = 'images/offers/offer1.jpg';
        $offer->is_active = true;
        $offer->starts_at = Carbon::now()->subDay();
        $offer->expires_at = Carbon::now()->addDays(30);
        
        if ($product) {
            $offer->product_id = $product->id;
            $this->info('Associated with product: ' . $product->name);
        }
        
        $offer->save();
        
        // Ensure offers section is available in homepage settings
        Settings::set('homepage_offers_title', 'Special Offers');
        Settings::set('homepage_offers_description', 'Take advantage of these limited-time special offers and exclusive deals.');
        Settings::set('homepage_offers_title_ar', 'عروض خاصة');
        Settings::set('homepage_offers_description_ar', 'استفد من هذه العروض الخاصة المحدودة والصفقات الحصرية.');
        
        // Make sure offers section is in the homepage sections order
        $sectionOrder = json_decode(Settings::get('homepage_sections_order', '[]'), true);
        if (!in_array('offers', $sectionOrder)) {
            array_splice($sectionOrder, 1, 0, 'offers'); // Insert after hero section
            Settings::set('homepage_sections_order', json_encode($sectionOrder));
        }
        
        // Set the currency symbol if not already set
        if (!Settings::get('currency_symbol')) {
            Settings::set('currency_symbol', '$');
        }
        
        $this->info('Offer updated: ' . $offer->title);
        if ($offer->expires_at) {
            $this->info('Offer is now active until: ' . $offer->expires_at->format('Y-m-d'));
        }
        $this->info('Homepage settings updated');
        return 0;
    }
} 