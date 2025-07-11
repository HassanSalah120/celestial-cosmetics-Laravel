<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Product;
use App\Models\Category;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class HomepageController extends Controller
{
    /**
     * Display the homepage content settings form
     *
     * @return \Illuminate\View\View
     */
    public function editContent()
    {
        try {
            // Get homepage settings from the normalized table
            $homepageSettings = DB::table('homepage_settings')->first();
            $homepageHero = DB::table('homepage_hero')->first();
            $homepageSections = DB::table('homepage_sections')->get()->keyBy('section_key');
            
            // Get our story content
            $ourStoryContent = DB::table('our_story_content')->first();
            
            // Create settings objects that emulate the old settings model
            $settings = [];
            
            // Settings from homepage_settings table
            if ($homepageSettings) {
                $settings['homepage_featured_products_count'] = (object)['value' => $homepageSettings->featured_products_count ?? 8];
                $settings['homepage_new_arrivals_count'] = (object)['value' => $homepageSettings->new_arrivals_count ?? 4];
                $settings['homepage_featured_categories_count'] = (object)['value' => $homepageSettings->featured_categories_count ?? 3];
                $settings['homepage_testimonials_count'] = (object)['value' => $homepageSettings->testimonials_count ?? 3];
                $settings['homepage_new_product_days'] = (object)['value' => $homepageSettings->new_product_days ?? 30];
                $settings['homepage_animation_enabled'] = (object)['value' => $homepageSettings->animation_enabled ? '1' : '0'];
                $settings['homepage_show_hero'] = (object)['value' => property_exists($homepageSettings, 'show_hero') ? ($homepageSettings->show_hero ? '1' : '0') : '1'];
                $settings['homepage_show_offers'] = (object)['value' => property_exists($homepageSettings, 'show_offers') ? ($homepageSettings->show_offers ? '1' : '0') : '1'];
                $settings['homepage_show_featured_products'] = (object)['value' => property_exists($homepageSettings, 'show_featured_products') ? ($homepageSettings->show_featured_products ? '1' : '0') : '1'];
                $settings['homepage_show_new_arrivals'] = (object)['value' => property_exists($homepageSettings, 'show_new_arrivals') ? ($homepageSettings->show_new_arrivals ? '1' : '0') : '1'];
                $settings['homepage_show_categories'] = (object)['value' => property_exists($homepageSettings, 'show_categories') ? ($homepageSettings->show_categories ? '1' : '0') : '1'];
                $settings['homepage_show_our_story'] = (object)['value' => $homepageSettings->show_our_story ? '1' : '0'];
                $settings['homepage_show_testimonials'] = (object)['value' => $homepageSettings->show_testimonials ? '1' : '0'];
                $settings['homepage_sections_order'] = (object)['value' => $homepageSettings->sections_order];
            }
            
            // Settings from homepage_hero table
            if ($homepageHero) {
                $settings['homepage_hero_title'] = (object)['value' => $homepageHero->title ?? 'Discover Celestial Beauty'];
                $settings['homepage_hero_title_ar'] = (object)['value' => $homepageHero->title_ar ?? 'اكتشف جمال سيليستيال'];
                $settings['homepage_hero_headtag'] = (object)['value' => $homepageHero->headtag ?? 'Experience the Cosmos'];
                $settings['homepage_hero_headtag_ar'] = (object)['value' => $homepageHero->headtag_ar ?? 'استكشف الكون'];
                $settings['homepage_hero_description'] = (object)['value' => $homepageHero->description ?? 'Explore our range of premium cosmetics inspired by the cosmos.'];
                $settings['homepage_hero_description_ar'] = (object)['value' => $homepageHero->description_ar ?? 'استكشف مجموعتنا من مستحضرات التجميل الفاخرة المستوحاة من الكون.'];
                $settings['homepage_hero_button_text'] = (object)['value' => $homepageHero->button_text ?? 'Shop Now'];
                $settings['homepage_hero_button_text_ar'] = (object)['value' => $homepageHero->button_text_ar ?? 'تسوق الآن'];
                $settings['homepage_hero_secondary_button_text'] = (object)['value' => $homepageHero->secondary_button_text ?? 'Learn More'];
                $settings['homepage_hero_secondary_button_text_ar'] = (object)['value' => $homepageHero->secondary_button_text_ar ?? 'اعرف المزيد'];
                $settings['homepage_hero_button_url'] = (object)['value' => $homepageHero->button_url ?? '/products'];
                $settings['homepage_hero_secondary_button_url'] = (object)['value' => $homepageHero->secondary_button_url ?? '/about'];
                $settings['homepage_hero_scroll_indicator_text'] = (object)['value' => $homepageHero->scroll_indicator_text ?? 'Scroll to explore'];
                $settings['homepage_hero_scroll_indicator_text_ar'] = (object)['value' => $homepageHero->scroll_indicator_text_ar ?? 'مرر للاستكشاف'];
                $settings['homepage_hero_image'] = (object)['value' => $homepageHero->image ?? '/images/hero-product.png'];
            }
        
        // Get products for selection
        $products = Product::where('is_visible', true)
            ->orderBy('name')
            ->get(['id', 'name', 'image', 'price']);
            
        // Get categories for selection
        $categories = Category::orderBy('name')->get(['id', 'name', 'image']);
        
        // Get testimonials for selection
        $testimonials = Testimonial::orderBy('created_at', 'desc')->get();
        
        // Available homepage sections
        $availableSections = [
            'hero' => 'Hero Section',
            'offers' => 'Special Offers',
            'featured_products' => 'Featured Products',
            'new_arrivals' => 'New Arrivals',
            'our_story' => 'Our Story',
            'categories' => 'Shop by Category',
            'testimonials' => 'Testimonials'
        ];
        
        // Get the current section order or use default if not set
            $sectionOrder = json_decode($settings['homepage_sections_order']->value ?? json_encode(array_keys($availableSections)), true) ?? array_keys($availableSections);
            
            // Fetch section-specific data
            if ($homepageSections) {
                foreach ($homepageSections as $key => $section) {
                    $settings['homepage_'.$key.'_title'] = (object)['value' => $section->title ?? ucfirst(str_replace('_', ' ', $key))];
                    $settings['homepage_'.$key.'_title_ar'] = (object)['value' => $section->title_ar ?? ''];
                    $settings['homepage_'.$key.'_description'] = (object)['value' => $section->description ?? ''];
                    $settings['homepage_'.$key.'_description_ar'] = (object)['value' => $section->description_ar ?? ''];
                    $settings['homepage_'.$key.'_tag'] = (object)['value' => $section->tag ?? ''];
                    $settings['homepage_'.$key.'_tag_ar'] = (object)['value' => $section->tag_ar ?? ''];
                }
            }
        
        return view('admin.homepage.content', compact(
            'settings', 
            'homepageSettings',
                'homepageHero',
            'products', 
            'categories', 
            'testimonials', 
            'availableSections',
                'sectionOrder',
            'homepageSections',
            'ourStoryContent'
            ));
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error in homepage edit content: ' . $e->getMessage());
            
            // Return a view with error
            return back()->with('error', 'An error occurred while loading settings: ' . $e->getMessage());
        }
    }
    
    /**
     * Update the homepage content settings
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateContent(Request $request)
    {
        // Debug information to see what's being submitted
        Log::info('Homepage content update request', [
            'has_settings' => $request->has('settings'),
            'settings_count' => $request->has('settings') ? count($request->settings) : 0,
            'has_images' => $request->hasFile('images'),
            'all_data' => $request->all()
        ]);
        
        // Basic validation for required fields
        $validator = Validator::make($request->all(), [
            'settings.homepage_sections_order' => 'nullable|json',
            'hero_title' => 'required|string|max:255',
            'hero_description' => 'required|string',
            'hero_button_text' => 'required|string|max:50',
            'hero_secondary_button_text' => 'nullable|string|max:50',
            'hero_button_url' => 'required|string|max:255',
            'hero_secondary_button_url' => 'nullable|string|max:255',
            'new_product_days' => 'nullable|integer|min:1|max:90',
            'testimonials_count' => 'nullable|integer|min:1',
            'featured_products_count' => 'nullable|integer|min:1',
            'new_arrivals_count' => 'nullable|integer|min:1',
            'featured_categories_count' => 'nullable|integer|min:1',
        ]);
        
        if ($validator->fails()) {
            Log::error('Validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);
            
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Get the new_product_days value
        $newProductDays = $request->input('new_product_days', 30);
        
        // Update the homepage settings table
        DB::table('homepage_settings')->updateOrInsert(
            ['id' => 1], // Assuming there's only one row in this table
            [
                'featured_products_count' => (int) $request->input('featured_products_count', 8),
                'new_arrivals_count' => (int) $request->input('new_arrivals_count', 4),
                'testimonials_count' => (int) $request->input('testimonials_count', 3),
                'featured_categories_count' => (int) $request->input('featured_categories_count', 3),
                'new_product_days' => (int) $newProductDays,
                'animation_enabled' => $request->has('animation_enabled'),
                'show_hero' => $request->has('show_hero'),
                'show_offers' => $request->has('show_offers'),
                'show_featured_products' => $request->has('show_featured_products'),
                'show_new_arrivals' => $request->has('show_new_arrivals'),
                'show_categories' => $request->has('show_categories'),
                'show_our_story' => $request->has('show_our_story'),
                'show_testimonials' => $request->has('show_testimonials'),
                'sections_order' => $request->input('settings.homepage_sections_order', json_encode([
                    'hero', 'offers', 'featured_products', 'new_arrivals', 'our_story', 'categories', 'testimonials'
                ])),
                'updated_at' => now()
            ]
        );
        
        // Update homepage hero table
        DB::table('homepage_hero')->updateOrInsert(
            ['id' => 1], // Assuming there's only one row
            [
                'title' => $request->input('hero_title'),
                'title_ar' => $request->input('hero_title_ar'),
                'headtag' => $request->input('hero_headtag', 'Experience the Cosmos'),
                'headtag_ar' => $request->input('hero_headtag_ar', 'استكشف الكون'),
                'description' => $request->input('hero_description'),
                'description_ar' => $request->input('hero_description_ar'),
                'button_text' => $request->input('hero_button_text'),
                'button_text_ar' => $request->input('hero_button_text_ar'),
                'button_url' => $request->input('hero_button_url', '/products'),
                'secondary_button_text' => $request->input('hero_secondary_button_text'),
                'secondary_button_text_ar' => $request->input('hero_secondary_button_text_ar'),
                'secondary_button_url' => $request->input('hero_secondary_button_url', '/about'),
                'scroll_indicator_text' => $request->input('hero_scroll_indicator_text', 'Scroll to explore'),
                'scroll_indicator_text_ar' => $request->input('hero_scroll_indicator_text_ar'),
                'hero_tags' => $request->input('hero_tags') 
                    ? json_encode(array_map('trim', explode(',', $request->input('hero_tags')))) 
                    : json_encode(['Cruelty-Free', '100% Natural']),
                'hero_tags_ar' => $request->input('hero_tags_ar') 
                    ? json_encode(array_map('trim', explode(',', $request->input('hero_tags_ar')))) 
                    : json_encode(['خالي من القسوة', '100٪ طبيعي']),
                'updated_at' => now()
            ]
        );
        
        // Handle image uploads
        if ($request->hasFile('hero_image')) {
            try {
                $image = $request->file('hero_image');
                
                if ($image->isValid()) {
                    // Store the new image
                    $path = $image->store('images/homepage', 'public');
                    
                    // Update hero image in the database
                    DB::table('homepage_hero')->where('id', 1)->update([
                        'image' => '/storage/' . $path
                    ]);
                    
                    Log::info("Saved new hero image at: {$path}");
                }
            } catch (\Exception $e) {
                Log::error("Error processing hero image: " . $e->getMessage(), [
                    'exception' => $e
                ]);
            }
        }
        
        // Handle Our Story image upload
        if ($request->hasFile('our_story_image')) {
            try {
                $image = $request->file('our_story_image');
                
                if ($image->isValid()) {
                    // Store the new image
                    $path = $image->store('images/our-story', 'public');
                    
                    // Update our_story_content in the database
                    DB::table('our_story_content')->updateOrInsert(
                        ['id' => 1],
                        [
                            'image' => '/storage/' . $path,
                            'updated_at' => now()
                        ]
                    );
                    
                    Log::info("Saved new our story image at: {$path}");
                }
            } catch (\Exception $e) {
                Log::error("Error processing our story image: " . $e->getMessage(), [
                    'exception' => $e
                ]);
            }
        }
        
        // Process Our Story content
        DB::table('our_story_content')->updateOrInsert(
            ['id' => 1],
            [
                'title' => $request->input('our_story_title', 'Beauty Inspired by the Cosmos'),
                'title_ar' => $request->input('our_story_title_ar', ''),
                'subtitle' => $request->input('our_story_subtitle', 'About Us'),
                'subtitle_ar' => $request->input('our_story_subtitle_ar', ''),
                'description' => $request->input('our_story_description', ''),
                'description_ar' => $request->input('our_story_description_ar', ''),
                'button_text' => $request->input('our_story_button_text', 'Learn more about our journey'),
                'button_text_ar' => $request->input('our_story_button_text_ar', ''),
                'button_url' => $request->input('our_story_button_url', '/about'),
                'feature1_title' => $request->input('our_story_feature1_title', 'Cruelty-Free'),
                'feature1_title_ar' => $request->input('our_story_feature1_title_ar', ''),
                'feature1_text' => $request->input('our_story_feature1_text', 'All our products are ethically made and never tested on animals'),
                'feature1_text_ar' => $request->input('our_story_feature1_text_ar', ''),
                'feature1_icon' => $request->input('our_story_feature1_icon', 'check-circle'),
                'feature2_title' => $request->input('our_story_feature2_title', 'Innovative Formulas'),
                'feature2_title_ar' => $request->input('our_story_feature2_title_ar', ''),
                'feature2_text' => $request->input('our_story_feature2_text', 'Advanced ingredients inspired by celestial elements'),
                'feature2_text_ar' => $request->input('our_story_feature2_text_ar', ''),
                'feature2_icon' => $request->input('our_story_feature2_icon', 'star'),
                'secondary_button_text' => $request->input('our_story_secondary_button_text', 'Explore Products'),
                'secondary_button_text_ar' => $request->input('our_story_secondary_button_text_ar', ''),
                'secondary_button_url' => $request->input('our_story_secondary_button_url', '/products'),
                'year_founded' => $request->input('our_story_year', '2023'),
                'updated_at' => now()
            ]
        );
        
        // Update homepage sections data
        if ($request->has('section')) {
            foreach ($request->section as $sectionKey => $sectionData) {
                DB::table('homepage_sections')
                    ->where('section_key', $sectionKey)
                    ->update([
                        'title' => $sectionData['title'] ?? '',
                        'title_ar' => $sectionData['title_ar'] ?? '',
                        'description' => $sectionData['description'] ?? '',
                        'description_ar' => $sectionData['description_ar'] ?? '',
                        'button_text' => $sectionData['button_text'] ?? '',
                        'button_text_ar' => $sectionData['button_text_ar'] ?? '',
                        'button_url' => $sectionData['button_url'] ?? '',
                        'tag' => $sectionData['tag'] ?? '',
                        'tag_ar' => $sectionData['tag_ar'] ?? '',
                        'updated_at' => now()
                    ]);
            }
        }
        
        // Clear the cache
        Cache::forget('homepage_settings');
        Cache::forget('homepage_hero');
        Cache::forget('homepage_sections');
        Cache::forget('our_story_content');
        
        return redirect()
            ->route('admin.homepage-content')
            ->with('success', 'Homepage content has been updated successfully!');
    }
} 