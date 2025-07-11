<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Product;
use App\Facades\Settings;
use Illuminate\Http\Request;

class OffersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $isArabic = is_rtl();
        
        // Get active offers
        $offers = Offer::active()
            ->orderBy('sort_order')
            ->paginate(10);  // Add pagination support
            
        // Process offers for localization
        $offers->getCollection()->transform(function($offer) use ($isArabic) {
            // Use Arabic fields if in Arabic mode and they exist
            if ($isArabic) {
                if (!empty($offer->title_ar)) {
                    $offer->title = $offer->title_ar;
                }
                if (!empty($offer->subtitle_ar)) {
                    $offer->subtitle = $offer->subtitle_ar;
                }
                if (!empty($offer->description_ar)) {
                    $offer->description = $offer->description_ar;
                }
                if (!empty($offer->tag_ar)) {
                    $offer->tag = $offer->tag_ar;
                }
                if (!empty($offer->button_text_ar)) {
                    $offer->button_text = $offer->button_text_ar;
                }
            }
            
            return $offer;
        });
        
        // Get the page title and meta info from settings or default
        $title = Settings::get($isArabic ? 'offers_title_ar' : 'offers_title', $isArabic ? 'العروض الخاصة' : 'Special Offers');
        $description = Settings::get($isArabic ? 'offers_description_ar' : 'offers_description', 
            $isArabic ? 'استفد من عروضنا المحدودة والصفقات الحصرية.' : 'Take advantage of these limited-time special offers and exclusive deals.');
        $keywords = Settings::get('offers_page_keywords', 'offers, special deals, promotions, discounts, special offers');
        
        // Set currency symbol based on locale
        $currencySymbol = Settings::get('currency_symbol', $isArabic ? 'ج.م' : 'EGP');
        
        return view('offers.index', compact('offers', 'title', 'description', 'keywords', 'currencySymbol'));
    }
} 