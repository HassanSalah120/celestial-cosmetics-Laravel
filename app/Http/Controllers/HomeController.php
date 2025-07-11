<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Offer;
use App\Facades\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // Get homepage hero information from database
        $homepageHero = DB::table('homepage_hero')->first();
        
        // If no hero data exists in the database, create an empty object to avoid errors
        if (!$homepageHero) {
            $homepageHero = new \stdClass();
        }
        
        // Apply Arabic content for the hero section when in Arabic mode
        $isArabic = session('locale', app()->getLocale()) === 'ar';
        if ($isArabic) {
            // Replace English content with Arabic if available
            if (!empty($homepageHero->title_ar)) {
                $homepageHero->title = $homepageHero->title_ar;
            }
            if (!empty($homepageHero->headtag_ar)) {
                $homepageHero->headtag = $homepageHero->headtag_ar;
            }
            if (!empty($homepageHero->description_ar)) {
                $homepageHero->description = $homepageHero->description_ar;
            }
            if (!empty($homepageHero->button_text_ar)) {
                $homepageHero->button_text = $homepageHero->button_text_ar;
            }
            if (!empty($homepageHero->secondary_button_text_ar)) {
                $homepageHero->secondary_button_text = $homepageHero->secondary_button_text_ar;
            }
            if (!empty($homepageHero->scroll_indicator_text_ar)) {
                $homepageHero->scroll_indicator_text = $homepageHero->scroll_indicator_text_ar;
            }
            
            // Log the locale and hero tags for debugging
            Log::debug('Arabic mode detected in HomeController', [
                'session_locale' => session('locale'),
                'app_locale' => app()->getLocale(),
                'is_arabic' => $isArabic,
                'hero_tags' => $homepageHero->hero_tags ?? null,
                'hero_tags_ar' => $homepageHero->hero_tags_ar ?? null,
                'headtag' => $homepageHero->headtag ?? null,
                'headtag_ar' => $homepageHero->headtag_ar ?? null
            ]);
        }
        
        // Get homepage settings from database
        $homepageSettingsDB = DB::table('homepage_settings')->first();
        
        // Get and parse homepage sections
        $homepageSections = Settings::get('homepage_sections');
        if (is_string($homepageSections)) {
            $homepageSections = json_decode($homepageSections, true) ?: [];
        }
        
        // Extract settings from the database record
        $featuredProductsCount = $homepageSettingsDB ? $homepageSettingsDB->featured_products_count : 0;
        $newArrivalsCount = $homepageSettingsDB ? $homepageSettingsDB->new_arrivals_count : 0;
        $testimonialsCount = $homepageSettingsDB ? $homepageSettingsDB->testimonials_count : 0;
        $newProductDays = $homepageSettingsDB ? $homepageSettingsDB->new_product_days : 0;
        $enableAnimations = $homepageSettingsDB ? (bool)$homepageSettingsDB->animation_enabled : false;
        
        // Section visibility settings
        $showHero = $homepageSettingsDB && property_exists($homepageSettingsDB, 'show_hero') ? (bool)$homepageSettingsDB->show_hero : true;
        $showOffers = $homepageSettingsDB && property_exists($homepageSettingsDB, 'show_offers') ? (bool)$homepageSettingsDB->show_offers : true;
        $showFeaturedProducts = $homepageSettingsDB && property_exists($homepageSettingsDB, 'show_featured_products') ? (bool)$homepageSettingsDB->show_featured_products : true;
        $showNewArrivals = $homepageSettingsDB && property_exists($homepageSettingsDB, 'show_new_arrivals') ? (bool)$homepageSettingsDB->show_new_arrivals : true;
        $showCategories = $homepageSettingsDB && property_exists($homepageSettingsDB, 'show_categories') ? (bool)$homepageSettingsDB->show_categories : true;
        $showOurStory = $homepageSettingsDB ? (bool)$homepageSettingsDB->show_our_story : false;
        $showTestimonials = $homepageSettingsDB ? (bool)$homepageSettingsDB->show_testimonials : false;
        
        // Get featured products using the database-defined count and sort
        $featuredProducts = Product::where('is_featured', true)
            ->with('images')
            ->when($homepageSettingsDB && $homepageSettingsDB->featured_product_sort === 'newest', function($query) {
                return $query->latest();
            })
            ->when($homepageSettingsDB && $homepageSettingsDB->featured_product_sort === 'oldest', function($query) {
                return $query->oldest();
            })
            ->when($homepageSettingsDB && $homepageSettingsDB->featured_product_sort === 'price_asc', function($query) {
                return $query->orderBy('price', 'asc');
            })
            ->when($homepageSettingsDB && $homepageSettingsDB->featured_product_sort === 'price_desc', function($query) {
                return $query->orderBy('price', 'desc');
            })
            ->take($featuredProductsCount)
            ->get();
        
        // Identify trending categories (categories with 2 or more products in featured list)
        $categoryCounts = [];
        $trendingCategories = [];
        
        foreach ($featuredProducts as $product) {
            if ($product->category_id) {
                if (!isset($categoryCounts[$product->category_id])) {
                    $categoryCounts[$product->category_id] = 0;
                }
                $categoryCounts[$product->category_id]++;
                
                // If we have 2 or more products from this category, mark it as trending
                if ($categoryCounts[$product->category_id] >= 2) {
                    $trendingCategories[$product->category_id] = true;
                }
            }
        }
        
        // Set the trending flag on products
        foreach ($featuredProducts as $product) {
            $product->is_trending = $product->category_id && isset($trendingCategories[$product->category_id]);
        }
        
        // Get new arrivals based on database-defined days period
        $newArrivals = Product::where('created_at', '>', Carbon::now()->subDays($newProductDays))
            ->where('is_visible', true)
            ->with('images')
            ->latest()
            ->take($newArrivalsCount)
            ->get();
            
        // Get categories
        $categories = Category::all()->take($homepageSettingsDB ? $homepageSettingsDB->featured_categories_count : 0);
        
        // Get a featured product image for each category
        foreach ($categories as $category) {
            // Try to find a featured product with an image in this category
            $featuredProduct = Product::where('category_id', $category->id)
                ->where('is_visible', true)
                ->where('is_featured', true)
                ->whereNotNull('image')
                ->first();
                
            // If no featured product found, get any product with an image
            if (!$featuredProduct) {
                $featuredProduct = Product::where('category_id', $category->id)
                    ->where('is_visible', true)
                    ->whereNotNull('image')
                    ->first();
            }
            
            // Store the product image if found
            if ($featuredProduct && $featuredProduct->image) {
                $category->featured_product_image = $featuredProduct->image;
            } else {
                // Check if the product has images relation with data
                if ($featuredProduct && $featuredProduct->images && $featuredProduct->images->isNotEmpty() && !empty($featuredProduct->images->first()->image_path)) {
                    $category->featured_product_image = $featuredProduct->images->first()->image_path;
                } else {
                    $category->featured_product_image = null;
                }
            }
        }
        
        // Get active offers
        $activeOffers = Offer::active()
            ->orderBy('sort_order')
            ->get()
            ->map(function($offer) use ($isArabic) {
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
            
        // Get testimonials with a preference for the current locale
        $locale = app()->getLocale();
        
        // Get featured testimonials based on database-defined count
        $featuredTestimonials = [];
        if ($showTestimonials) {
            $featuredTestimonials = \App\Models\Testimonial::approved()
                ->featured()
                ->latest()
                ->when($locale === 'ar', function($query) {
                    // When in Arabic, only get testimonials with Arabic content
                    return $query->where(function($q) {
                        $q->whereNotNull('message_ar')
                          ->orWhereRaw("LENGTH(TRIM(message_ar)) > 0");
                    });
                })
                ->take($testimonialsCount)
                ->get();
                
            // If we don't have enough Arabic testimonials, fall back to English ones
            if ($locale === 'ar' && $featuredTestimonials->count() < $testimonialsCount) {
                $featuredTestimonials = \App\Models\Testimonial::approved()
                    ->featured()
                    ->latest()
                    ->take($testimonialsCount)
                    ->get();
            }
        }

        // Get seo defaults from database
        $seoDefaults = DB::table('seo_defaults')->first();

        // Get our_story_content
        $ourStoryContent = DB::table('our_story_content')->first();

        // Get section order from database
        $sectionOrder = $homepageSettingsDB && $homepageSettingsDB->sections_order 
            ? json_decode($homepageSettingsDB->sections_order, true) 
            : [];
            
        // Filter out sections that should be hidden based on settings
        if (!$showHero && in_array('hero', $sectionOrder)) {
            $sectionOrder = array_diff($sectionOrder, ['hero']);
        }
        
        if (!$showOffers && in_array('offers', $sectionOrder)) {
            $sectionOrder = array_diff($sectionOrder, ['offers']);
        }
        
        if (!$showFeaturedProducts && in_array('featured_products', $sectionOrder)) {
            $sectionOrder = array_diff($sectionOrder, ['featured_products']);
        }
        
        if (!$showNewArrivals && in_array('new_arrivals', $sectionOrder)) {
            $sectionOrder = array_diff($sectionOrder, ['new_arrivals']);
        }
        
        if (!$showCategories && in_array('categories', $sectionOrder)) {
            $sectionOrder = array_diff($sectionOrder, ['categories']);
        }
        
        if (!$showOurStory && in_array('our_story', $sectionOrder)) {
            $sectionOrder = array_diff($sectionOrder, ['our_story']);
        }
        
        if (!$showTestimonials && in_array('testimonials', $sectionOrder)) {
            $sectionOrder = array_diff($sectionOrder, ['testimonials']);
        }
        
        // Re-index the array to ensure numeric keys
        $sectionOrder = array_values($sectionOrder);

        // Get homepage SEO settings
        $homepageSeo = Settings::getGroup('seo')
            ->filter(function($setting) {
                return str_starts_with($setting->key, 'homepage_') || str_starts_with($setting->key, 'home_');
            })
            ->keyBy('key');

        // Get SEO metadata from settings
        $title = Settings::get('homepage_meta_title') ?? Settings::get('default_meta_title', config('app.name'));
        $description = Settings::get('homepage_meta_description') ?? Settings::get('default_meta_description');
        $keywords = Settings::get('homepage_meta_keywords') ?? Settings::get('default_meta_keywords');

        // Use the already loaded testimonials
        $testimonials = $featuredTestimonials;

        // Get text settings
        $textSettings = [];
        
        // Set default hero headtag based on locale
        $textSettings['hero'] = [
            'headtag' => $isArabic ? 'استكشف الكون' : 'Experience the Cosmos'
        ];
        
        // Base text settings with automatic language selection
        $settingsKeys = [
            'featured_products_title',
            'featured_products_description',
            'new_arrivals_title',
            'new_arrivals_tag',
            'new_arrivals_description',
            'shop_by_category_title',
            'shop_by_category_description',
            'testimonials_title',
            'testimonials_description',
            'view_all_products_text',
            'explore_new_arrivals_text',
            'offers_title',
            'offers_description',
        ];
        
        foreach ($settingsKeys as $key) {
            $arKey = "{$key}_ar";
            // First try to get the Arabic version when in Arabic mode
            if ($isArabic) {
                $arValue = Settings::get("homepage_{$arKey}");
                if (!empty($arValue)) {
                    $textSettings[$key] = $arValue;
                    continue;
                }
            }
            
            // Fallback to English version
            $textSettings[$key] = Settings::get("homepage_{$key}");
        }
        
        // Load homepage sections from database
        $homepageSectionsData = DB::table('homepage_sections')->get();
        foreach ($homepageSectionsData as $section) {
            $textSettings[$section->section_key] = [
                'title' => $isArabic && !empty($section->title_ar) ? $section->title_ar : $section->title,
                'description' => $isArabic && !empty($section->description_ar) ? $section->description_ar : $section->description,
                'button_text' => $isArabic && !empty($section->button_text_ar) ? $section->button_text_ar : $section->button_text,
                'button_url' => $section->button_url,
                'tag' => $isArabic && !empty($section->tag_ar) ? $section->tag_ar : $section->tag,
                'headtag' => $isArabic && !empty($section->tag_ar) ? $section->tag_ar : $section->tag, // Add headtag as an alias for tag
                'image' => $section->image,
            ];
        }
        
        // Get currency symbol from settings
        $currencySymbol = Settings::get('currency_symbol', '$');

        // Create a mapping for product names and categories to translate dynamically
        if ($isArabic) {
            foreach ($featuredProducts as $product) {
                // Translate product name if a translation exists
                $product->name = __($product->name);
                
                // Translate category name if available
                if ($product->category) {
                    $product->category->name = __($product->category->name);
                }
                
                // Translate short description if available
                if (!empty($product->short_description)) {
                    $product->short_description = __($product->short_description);
                }
            }
            
            foreach ($newArrivals as $product) {
                // Translate product name if a translation exists
                $product->name = __($product->name);
                
                // Translate category name if available
                if ($product->category) {
                    $product->category->name = __($product->category->name);
                }
                
                // Translate short description if available
                if (!empty($product->short_description)) {
                    $product->short_description = __($product->short_description);
                }
            }
            
            // Translate categories
            foreach ($categories as $category) {
                $category->name = __($category->name);
                if (!empty($category->description)) {
                    $category->description = __($category->description);
                }
            }
        }

        return view('home', compact(
            'featuredProducts',
            'newArrivals',
            'categories',
            'featuredTestimonials',
            'testimonials',
            'homepageSeo',
            'title',
            'description',
            'keywords',
            'showHero',
            'showOffers',
            'showFeaturedProducts',
            'showNewArrivals',
            'showCategories',
            'showTestimonials',
            'showOurStory',
            'sectionOrder',
            'enableAnimations',
            'homepageSettingsDB',
            'homepageHero',
            'homepageSections',
            'textSettings',
            'activeOffers',
            'currencySymbol',
            'seoDefaults',
            'ourStoryContent'
        ));
    }

    public function about()
    {
        // Check if we're in RTL mode
        $isRtl = is_rtl();
        
        // Get all About page settings
        $aboutSettings = Settings::getGroup('pages')
            ->filter(function($setting) {
                return str_starts_with($setting->key, 'about_');
            })
            ->keyBy('key');
            
        // Format specific JSON settings
        $ourValues = json_decode($aboutSettings['about_our_values']->value ?? '[]', true);
        $teamMembers = json_decode($aboutSettings['about_team_members']->value ?? '[]', true);
        
        // Process values and team members for RTL support
        if ($isRtl) {
            // Process our values for RTL
            foreach ($ourValues as $key => $value) {
                if (!empty($value['title_ar'])) {
                    $ourValues[$key]['title'] = $value['title_ar'];
                }
                if (!empty($value['description_ar'])) {
                    $ourValues[$key]['description'] = $value['description_ar'];
                }
            }
            
            // Process team members for RTL
            foreach ($teamMembers as $key => $member) {
                if (!empty($member['name_ar'])) {
                    $teamMembers[$key]['name'] = $member['name_ar'];
                }
                if (!empty($member['title_ar'])) {
                    $teamMembers[$key]['title'] = $member['title_ar'];
                }
                if (!empty($member['bio_ar'])) {
                    $teamMembers[$key]['bio'] = $member['bio_ar'];
                }
            }
        }
        
        // Set the page title and meta data
        $title = Settings::get($isRtl ? 'about_meta_title_ar' : 'about_meta_title') ?? Settings::get('default_meta_title', config('app.name'));
        $description = Settings::get($isRtl ? 'about_meta_description_ar' : 'about_meta_description') ?? Settings::get('default_meta_description');
        $keywords = Settings::get('about_meta_keywords') ?? Settings::get('default_meta_keywords');
        
        return view('about', compact('aboutSettings', 'ourValues', 'teamMembers', 'title', 'description', 'keywords'));
    }

    public function contact()
    {
        // Check if RTL is enabled
        $isRtl = is_rtl();
        
        // Get all Contact page settings
        $contactSettings = Settings::getGroup('pages')
            ->filter(function($setting) {
                return str_starts_with($setting->key, 'contact_');
            })
            ->keyBy('key');
            
        // Get store hours from database
        $storeHours = \App\Models\StoreHour::orderBy('id')->get();
        
        // Determine if store is currently open - Using Egypt/Cairo timezone
        $isOpen = false;
        $currentTime = now()->timezone('Africa/Cairo');
        $currentDayName = $currentTime->format('l'); // Gets current day name (Monday, Tuesday, etc.)
        $storeTimezone = 'Africa/Cairo';
        
        // Find today's store hours
        $todayHours = $storeHours->firstWhere('day', $currentDayName);
        
        if ($todayHours && strtolower($todayHours->hours) !== 'closed') {
            // Parse opening and closing times
            $parts = explode(' - ', $todayHours->hours);
            
            if (count($parts) == 2) {
                $openingTime = $this->parseTimeString($parts[0], $storeTimezone);
                $closingTime = $this->parseTimeString($parts[1], $storeTimezone);
                
                // Check if current time is between opening and closing times
                if ($openingTime && $closingTime) {
                    $currentTimeObj = \Carbon\Carbon::createFromTimeString($currentTime->format('H:i'), $storeTimezone);
                    $isOpen = $currentTimeObj->between($openingTime, $closingTime);
                }
            }
        }
        
        // Set the page title and meta data based on RTL status
        $title = Settings::get($isRtl ? 'contact_meta_title_ar' : 'contact_meta_title') ?? Settings::get('default_meta_title', config('app.name'));
        $description = Settings::get($isRtl ? 'contact_meta_description_ar' : 'contact_meta_description') ?? Settings::get('default_meta_description');
        $keywords = Settings::get('contact_meta_keywords') ?? Settings::get('default_meta_keywords');
        
        return view('contact', compact('contactSettings', 'storeHours', 'title', 'description', 'keywords', 'isOpen', 'currentDayName', 'storeTimezone'));
    }
    
    /**
     * Parse a time string like "9:00 AM" into a Carbon object
     *
     * @param string $timeString
     * @param string $timezone
     * @return \Carbon\Carbon|null
     */
    private function parseTimeString($timeString, $timezone = 'UTC')
    {
        try {
            return \Carbon\Carbon::createFromFormat('g:i A', $timeString, $timezone);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function submitTestimonial(Request $request)
    {
        // For backward compatibility, redirect to the ReviewController for general testimonials
        return redirect()->route('reviews.submit', $request->all());
    }

    public function testimonials()
    {
        $testimonials = \App\Models\Testimonial::approved()
            ->latest()
            ->paginate(12);
            
        $canSubmitTestimonial = false;
        
        if (auth()->check()) {
            // Check if user has any delivered orders
            $deliveredOrdersCount = \App\Models\Order::where('user_id', auth()->id())
                ->where('status', 'delivered')
                ->count();
                
            $canSubmitTestimonial = $deliveredOrdersCount > 0;
        }
        
        // Set the page title and meta data
        $title = Settings::get('testimonials_meta_title') ?? Settings::get('default_meta_title', config('app.name'));
        $description = Settings::get('testimonials_meta_description') ?? Settings::get('default_meta_description');
        $keywords = Settings::get('testimonials_meta_keywords') ?? Settings::get('default_meta_keywords');
            
        return view('testimonials', compact('testimonials', 'canSubmitTestimonial', 'title', 'description', 'keywords'));
    }

    /**
     * Display the terms of service page.
     */
    public function terms()
    {
        // Check if RTL is enabled
        $isRtl = is_rtl();
        
        $termsSettings = Settings::getGroup('legal')
            ->filter(function($setting) {
                return str_starts_with($setting->key, 'terms_');
            })
            ->keyBy('key');
            
        // Set the page title and meta data based on RTL status
        $title = Settings::get($isRtl ? 'terms_meta_title_ar' : 'terms_meta_title') ?? Settings::get('default_meta_title', config('app.name'));
        $description = Settings::get($isRtl ? 'terms_meta_description_ar' : 'terms_meta_description') ?? Settings::get('default_meta_description');
        $keywords = Settings::get('terms_meta_keywords') ?? Settings::get('default_meta_keywords');
            
        return view('legal.terms', compact('termsSettings', 'title', 'description', 'keywords'));
    }

    /**
     * Display the privacy policy page.
     */
    public function privacy()
    {
        // Check if RTL is enabled
        $isRtl = is_rtl();
        
        $privacySettings = Settings::getGroup('legal')
            ->filter(function($setting) {
                return str_starts_with($setting->key, 'privacy_');
            })
            ->keyBy('key');
            
        // Set the page title and meta data based on RTL status
        $title = Settings::get($isRtl ? 'privacy_meta_title_ar' : 'privacy_meta_title') ?? Settings::get('default_meta_title', config('app.name'));
        $description = Settings::get($isRtl ? 'privacy_meta_description_ar' : 'privacy_meta_description') ?? Settings::get('default_meta_description');
        $keywords = Settings::get('privacy_meta_keywords') ?? Settings::get('default_meta_keywords');
            
        return view('legal.privacy', compact('privacySettings', 'title', 'description', 'keywords'));
    }

    /**
     * Display the shipping policy page.
     */
    public function shipping()
    {
        // Check if RTL is enabled
        $isRtl = is_rtl();
        
        $shippingSettings = Settings::getGroup('legal')
            ->filter(function($setting) {
                return str_starts_with($setting->key, 'shipping_');
            })
            ->keyBy('key');
            
        // Set the page title and meta data based on RTL status
        $title = Settings::get($isRtl ? 'shipping_meta_title_ar' : 'shipping_meta_title') ?? Settings::get('default_meta_title', config('app.name'));
        $description = Settings::get($isRtl ? 'shipping_meta_description_ar' : 'shipping_meta_description') ?? Settings::get('default_meta_description');
        $keywords = Settings::get('shipping_meta_keywords') ?? Settings::get('default_meta_keywords');
            
        return view('legal.shipping', compact('shippingSettings', 'title', 'description', 'keywords'));
    }

    /**
     * Display the refund policy page.
     */
    public function refunds()
    {
        // Check if RTL is enabled
        $isRtl = is_rtl();
        
        $refundSettings = Settings::getGroup('legal')
            ->filter(function($setting) {
                return str_starts_with($setting->key, 'refund_');
            })
            ->keyBy('key');
            
        // Set the page title and meta data based on RTL status
        $title = Settings::get($isRtl ? 'refund_meta_title_ar' : 'refund_meta_title') ?? Settings::get('default_meta_title', config('app.name'));
        $description = Settings::get($isRtl ? 'refund_meta_description_ar' : 'refund_meta_description') ?? Settings::get('default_meta_description');
        $keywords = Settings::get('refund_meta_keywords') ?? Settings::get('default_meta_keywords');
            
        return view('legal.refunds', compact('refundSettings', 'title', 'description', 'keywords'));
    }
} 