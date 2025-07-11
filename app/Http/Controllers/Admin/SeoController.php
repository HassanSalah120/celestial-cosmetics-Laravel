<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Product;
use App\Models\Category;
use App\Models\Activity;
use App\Models\Redirect;
use App\Models\RobotsTxtRule;
use App\Models\StructuredData;
use App\Models\SeoDefaults;
use App\Models\CurrencyConfig;
use App\Models\GeneralSetting;
use App\Helpers\SettingsHelper;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SeoController extends Controller
{
    /**
     * Display the SEO settings dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // Check if settings table exists
            if (DB::getSchemaBuilder()->hasTable('settings')) {
        $seoSettings = Setting::where('group', 'seo')->get()->keyBy('key');
            } else {
                throw new \Exception('Settings table does not exist');
            }
        } catch (\Exception $e) {
            // Try to get data from normalized tables
            $seoDefaults = $this->ensureSeoDefaultsExist();
            
            // Convert SeoDefaults model to collection format similar to settings
            $seoSettings = collect([
                'default_meta_title' => (object) ['key' => 'default_meta_title', 'value' => $seoDefaults->default_meta_title],
                'default_meta_description' => (object) ['key' => 'default_meta_description', 'value' => $seoDefaults->default_meta_description],
                'default_meta_keywords' => (object) ['key' => 'default_meta_keywords', 'value' => $seoDefaults->default_meta_keywords],
                'og_default_image' => (object) ['key' => 'og_default_image', 'value' => $seoDefaults->og_default_image],
                'og_site_name' => (object) ['key' => 'og_site_name', 'value' => $seoDefaults->og_site_name],
                'twitter_site' => (object) ['key' => 'twitter_site', 'value' => $seoDefaults->twitter_site],
                'twitter_creator' => (object) ['key' => 'twitter_creator', 'value' => $seoDefaults->twitter_creator],
                'default_robots_content' => (object) ['key' => 'default_robots_content', 'value' => $seoDefaults->default_robots_content],
                'enable_structured_data' => (object) ['key' => 'enable_structured_data', 'value' => $seoDefaults->enable_structured_data ? '1' : '0'],
                'enable_robots_txt' => (object) ['key' => 'enable_robots_txt', 'value' => $seoDefaults->enable_robots_txt ? '1' : '0'],
                'enable_sitemap' => (object) ['key' => 'enable_sitemap', 'value' => $seoDefaults->enable_sitemap ? '1' : '0'],
                'sitemap_change_frequency' => (object) ['key' => 'sitemap_change_frequency', 'value' => $seoDefaults->sitemap_change_frequency],
            ]);
        }
        
        return view('admin.seo.index', compact('seoSettings'));
    }
    
    /**
     * Display the SEO settings for products.
     *
     * @return \Illuminate\Http\Response
     */
    public function products()
    {
        $products = Product::orderBy('name')->get();
        
        return view('admin.seo.products', compact('products'));
    }
    
    /**
     * Display the SEO settings for categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function categories()
    {
        $categories = Category::orderBy('name')->get();
        
        return view('admin.seo.categories', compact('categories'));
    }
    
    /**
     * Show the form for editing SEO settings for a specific product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editProduct($id)
    {
        $product = Product::findOrFail($id);
        
        return view('admin.seo.edit-product', compact('product'));
    }
    
    /**
     * Update SEO settings for a specific product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $validated = $request->validate([
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'twitter_card_type' => 'nullable|string|in:summary,summary_large_image',
        ]);
        
        // Handle og_image upload
        if ($request->hasFile('og_image')) {
            // Delete old image if exists
            if ($product->og_image) {
                Storage::disk('public')->delete($product->og_image);
            }
            $validated['og_image'] = $request->file('og_image')->store('seo', 'public');
        }
        
        $product->update($validated);
        
        // Log activity
        Activity::create([
            'description' => 'Updated SEO settings for product: ' . $product->name,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'subject_type' => get_class($product),
            'subject_id' => $product->id,
            'status' => 'completed',
            'properties' => json_encode(['action' => 'updated'])
        ]);
        
        return redirect()->route('admin.seo.products')
            ->with('success', 'Product SEO settings updated successfully');
    }
    
    /**
     * Show the form for editing SEO settings for a specific category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editCategory($id)
    {
        $category = Category::findOrFail($id);
        
        return view('admin.seo.edit-category', compact('category'));
    }
    
    /**
     * Update SEO settings for a specific category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $validated = $request->validate([
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'twitter_card_type' => 'nullable|string|in:summary,summary_large_image',
        ]);
        
        // Handle og_image upload
        if ($request->hasFile('og_image')) {
            // Delete old image if exists
            if ($category->og_image) {
                Storage::disk('public')->delete($category->og_image);
            }
            $validated['og_image'] = $request->file('og_image')->store('seo', 'public');
        }
        
        $category->update($validated);
        
        // Log activity
        Activity::create([
            'description' => 'Updated SEO settings for category: ' . $category->name,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'subject_type' => get_class($category),
            'subject_id' => $category->id,
            'status' => 'completed',
            'properties' => json_encode(['action' => 'updated'])
        ]);
        
        return redirect()->route('admin.seo.categories')
            ->with('success', 'Category SEO settings updated successfully');
    }
    
    /**
     * Update global SEO settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateSettings(Request $request)
    {
        $settings = $request->except(['_token', '_method']);
        
        // Get all boolean settings (checkboxes) to handle unchecked ones
        $booleanSettings = [
            'enable_robots_txt',
            'enable_sitemap', 
            'enable_structured_data',
            'enable_breadcrumb_schema',
            'enable_product_schema',
            'enable_search_schema',
            'enable_organization_schema',
            'seo_friendly_urls',
            'sitemap_include_images'
        ];
        
        // Set missing boolean settings to 0 (unchecked)
        foreach ($booleanSettings as $key) {
            if (!isset($settings[$key])) {
                $settings[$key] = '0';
            }
        }
        
        try {
            // Check if settings table exists
            if (DB::getSchemaBuilder()->hasTable('settings')) {
                // Update all settings in the old settings table
        foreach ($settings as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }
        
        // Handle og_default_image upload
        if ($request->hasFile('og_default_image')) {
            $ogDefaultImage = $request->file('og_default_image')->store('seo', 'public');
            Setting::where('key', 'og_default_image')->update(['value' => $ogDefaultImage]);
                }
            } else {
                throw new \Exception('Settings table does not exist');
            }
        } catch (\Exception $e) {
            // Try to update normalized tables
            $seoDefaults = SeoDefaults::first();
            
            if (!$seoDefaults) {
                $seoDefaults = new SeoDefaults();
            }
            
            // Map settings to SeoDefaults model fields
            $seoDefaults->default_meta_title = $settings['default_meta_title'] ?? '';
            $seoDefaults->default_meta_description = $settings['default_meta_description'] ?? '';
            $seoDefaults->default_meta_keywords = $settings['default_meta_keywords'] ?? '';
            $seoDefaults->og_site_name = $settings['og_site_name'] ?? '';
            $seoDefaults->twitter_site = $settings['twitter_site'] ?? '';
            $seoDefaults->twitter_creator = $settings['twitter_creator'] ?? '';
            $seoDefaults->default_robots_content = $settings['default_robots_content'] ?? 'index,follow';
            $seoDefaults->enable_structured_data = isset($settings['enable_structured_data']) && $settings['enable_structured_data'] === '1';
            $seoDefaults->enable_robots_txt = isset($settings['enable_robots_txt']) && $settings['enable_robots_txt'] === '1';
            $seoDefaults->enable_sitemap = isset($settings['enable_sitemap']) && $settings['enable_sitemap'] === '1';
            $seoDefaults->sitemap_change_frequency = $settings['sitemap_change_frequency'] ?? 'weekly';
            
            // Handle og_default_image upload
            if ($request->hasFile('og_default_image')) {
                $ogDefaultImage = $request->file('og_default_image')->store('seo', 'public');
                $seoDefaults->og_default_image = $ogDefaultImage;
            }
            
            $seoDefaults->save();
        }
        
        // Clear cache
        Cache::forget('settings');
        
        // Log activity
        Activity::create([
            'description' => 'Updated global SEO settings',
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'subject_type' => Setting::class,
            'subject_id' => null,
            'status' => 'completed',
            'properties' => json_encode(['action' => 'updated'])
        ]);
        
        return redirect()->route('admin.seo.index')
            ->with('success', 'SEO settings updated successfully');
    }
    
    /**
     * Generate a sitemap.
     *
     * @return \Illuminate\Http\Response
     */
    public function generateSitemap()
    {
        // Create a new sitemap object
        $sitemap = app(\Spatie\Sitemap\Sitemap::class);
        
        // Add home page
        $sitemap->add(route('home'), now(), '1.0', 'daily');
        
        // Add product pages
        $products = Product::where('is_visible', true)->get();
        foreach ($products as $product) {
            $sitemap->add(route('products.show', $product->slug), $product->updated_at, '0.8', 'weekly');
        }
        
        // Add category pages
        $categories = Category::all();
        foreach ($categories as $category) {
            $sitemap->add(route('products.category', $category->slug), $category->updated_at, '0.8', 'weekly');
        }
        
        // Add other static pages
        $sitemap->add(route('products.index'), now(), '0.9', 'daily');
        $sitemap->add(route('about'), now(), '0.7', 'monthly');
        $sitemap->add(route('contact'), now(), '0.7', 'monthly');
        
        // Generate and store the sitemap
        $sitemap->writeToFile(public_path('sitemap.xml'));
        
        // Log activity
        Activity::create([
            'description' => 'Generated sitemap',
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'subject_type' => null,
            'subject_id' => null,
            'status' => 'completed',
            'properties' => json_encode(['action' => 'generated'])
        ]);
        
        return redirect()->route('admin.seo.index')
            ->with('success', 'Sitemap generated successfully.');
    }

    /**
     * Display the SEO health checker page.
     *
     * @return \Illuminate\Http\Response
     */
    public function healthChecker()
    {
        $pageTitle = 'SEO Health Checker';
        
        // Get products and categories to check
        $products = Product::with(['category', 'images'])->take(10)->get();
        $categories = Category::with('products')->take(10)->get();
        
        try {
            // Check if settings table exists
            if (DB::getSchemaBuilder()->hasTable('settings')) {
        // Basic site settings
        $siteSettings = Setting::where('group', 'seo')->orWhere('key', 'site_name')->get()->keyBy('key');
            } else {
                throw new \Exception('Settings table does not exist');
            }
        } catch (\Exception $e) {
            // Try to get data from normalized tables
            $seoDefaults = SeoDefaults::first();
            $generalSettings = GeneralSetting::first();
            
            if ($seoDefaults && $generalSettings) {
                // Convert SeoDefaults model to collection format similar to settings
                $siteSettings = collect([
                    'default_meta_title' => (object) ['key' => 'default_meta_title', 'value' => $seoDefaults->default_meta_title],
                    'default_meta_description' => (object) ['key' => 'default_meta_description', 'value' => $seoDefaults->default_meta_description],
                    'default_meta_keywords' => (object) ['key' => 'default_meta_keywords', 'value' => $seoDefaults->default_meta_keywords],
                    'og_default_image' => (object) ['key' => 'og_default_image', 'value' => $seoDefaults->og_default_image],
                    'og_site_name' => (object) ['key' => 'og_site_name', 'value' => $seoDefaults->og_site_name],
                    'twitter_site' => (object) ['key' => 'twitter_site', 'value' => $seoDefaults->twitter_site],
                    'twitter_creator' => (object) ['key' => 'twitter_creator', 'value' => $seoDefaults->twitter_creator],
                    'default_robots_content' => (object) ['key' => 'default_robots_content', 'value' => $seoDefaults->default_robots_content],
                    'enable_structured_data' => (object) ['key' => 'enable_structured_data', 'value' => $seoDefaults->enable_structured_data ? '1' : '0'],
                    'enable_robots_txt' => (object) ['key' => 'enable_robots_txt', 'value' => $seoDefaults->enable_robots_txt ? '1' : '0'],
                    'enable_sitemap' => (object) ['key' => 'enable_sitemap', 'value' => $seoDefaults->enable_sitemap ? '1' : '0'],
                    'sitemap_change_frequency' => (object) ['key' => 'sitemap_change_frequency', 'value' => $seoDefaults->sitemap_change_frequency],
                    'site_name' => (object) ['key' => 'site_name', 'value' => $generalSettings->site_name],
                ]);
            } else {
                // If no SeoDefaults record exists, create a default collection
                $siteSettings = collect([
                    'default_meta_title' => (object) ['key' => 'default_meta_title', 'value' => 'Celestial Cosmetics - Beauty Products'],
                    'default_meta_description' => (object) ['key' => 'default_meta_description', 'value' => 'Discover high-quality beauty products from Celestial Cosmetics.'],
                    'default_meta_keywords' => (object) ['key' => 'default_meta_keywords', 'value' => 'cosmetics, beauty, skincare'],
                    'og_default_image' => (object) ['key' => 'og_default_image', 'value' => ''],
                    'og_site_name' => (object) ['key' => 'og_site_name', 'value' => 'Celestial Cosmetics'],
                    'twitter_site' => (object) ['key' => 'twitter_site', 'value' => '@celestialcosm'],
                    'twitter_creator' => (object) ['key' => 'twitter_creator', 'value' => '@celestialcosm'],
                    'default_robots_content' => (object) ['key' => 'default_robots_content', 'value' => 'index,follow'],
                    'enable_structured_data' => (object) ['key' => 'enable_structured_data', 'value' => '1'],
                    'enable_robots_txt' => (object) ['key' => 'enable_robots_txt', 'value' => '1'],
                    'enable_sitemap' => (object) ['key' => 'enable_sitemap', 'value' => '1'],
                    'sitemap_change_frequency' => (object) ['key' => 'sitemap_change_frequency', 'value' => 'weekly'],
                    'site_name' => (object) ['key' => 'site_name', 'value' => 'Celestial Cosmetics'],
                ]);
            }
        }
        
        // Run all SEO Health checks
        $healthChecks = [
            'meta_title' => $this->checkMetaTitles($products, $categories),
            'meta_description' => $this->checkMetaDescriptions($products, $categories),
            'content_quality' => $this->checkContentQuality($products, $categories),
            'image_optimization' => $this->checkImageOptimization($products, $categories),
            'url_structure' => $this->checkUrlStructure($products, $categories),
            'structured_data' => $this->checkStructuredData(),
            'robots_txt' => $this->checkRobotsTxt(),
            'sitemap' => $this->checkSitemap(),
            'canonical_urls' => $this->checkCanonicalUrls($products, $categories),
        ];
        
        // Calculate overall SEO score
        $totalChecks = count($healthChecks);
        $passedChecks = 0;
        
        foreach ($healthChecks as $check) {
            if ($check['pass']) {
                $passedChecks++;
            }
        }
        
        $seoScore = $totalChecks > 0 ? round(($passedChecks / $totalChecks) * 100) : 0;
        
        // Get priority issues (most important to fix)
        $priorityIssues = [];
        $priorityChecks = ['meta_title', 'meta_description', 'content_quality', 'structured_data'];
        
        foreach ($priorityChecks as $checkName) {
            if (isset($healthChecks[$checkName]) && !$healthChecks[$checkName]['pass']) {
                // Get up to 3 issues from each priority check
                $issues = array_slice($healthChecks[$checkName]['issues'], 0, 3);
                // Get corresponding suggestions (with a check for existence)
                $suggestions = isset($healthChecks[$checkName]['suggestions']) 
                    ? array_slice($healthChecks[$checkName]['suggestions'], 0, 3) 
                    : [];
                
                // For each issue, add to priority issues with suggestion if available
                foreach ($issues as $index => $issue) {
                    $priorityIssues[] = [
                        'check' => $checkName,
                        'issue' => $issue,
                        'suggestion' => $suggestions[$index] ?? null
                    ];
                }
            }
        }
        
        // Sort priority issues by importance (hardcoded order)
        usort($priorityIssues, function($a, $b) use ($priorityChecks) {
            $aIndex = array_search($a['check'], $priorityChecks);
            $bIndex = array_search($b['check'], $priorityChecks);
            return $aIndex - $bIndex;
        });
        
        // Limit to 5 priority issues
        $priorityIssues = array_slice($priorityIssues, 0, 5);
        
        return view('admin.seo.health-checker', compact(
            'pageTitle', 
            'healthChecks', 
            'siteSettings', 
            'seoScore', 
            'priorityIssues'
        ));
    }

    /**
     * Check for meta title issues and provide suggestions
     */
    private function checkMetaTitles($products, $categories)
    {
        $issues = [];
        $suggestions = [];
        
        // Check products
        foreach ($products as $product) {
            $title = $product->meta_title ?: $product->name;
            
            if (empty($title)) {
                $issues[] = "Product #{$product->id} ({$product->name}) missing meta title";
                // Generate suggestion based on product data
                $suggestedTitle = $product->name;
                if ($product->brand) {
                    $suggestedTitle .= " | {$product->brand}";
                }
                if ($product->category) {
                    $suggestedTitle .= " | {$product->category->name}";
                }
                $suggestions[] = "Suggested title for {$product->name}: \"{$suggestedTitle}\" (" . strlen($suggestedTitle) . " chars)";
            } elseif (strlen($title) > 60) {
                $issues[] = "Product #{$product->id} ({$product->name}) meta title too long (" . strlen($title) . " chars)";
                // Suggest a shortened version
                $shortTitle = substr($title, 0, 57) . '...';
                $suggestions[] = "Consider shortening to: \"{$shortTitle}\" (60 chars)";
            } elseif (strlen($title) < 30) {
                $issues[] = "Product #{$product->id} ({$product->name}) meta title too short (" . strlen($title) . " chars)";
                // Suggest an enhanced version
                $enhancedTitle = $title;
                if ($product->brand && !str_contains(strtolower($title), strtolower($product->brand))) {
                    $enhancedTitle .= " | {$product->brand}";
                }
                if ($product->category && !str_contains(strtolower($title), strtolower($product->category->name))) {
                    $enhancedTitle .= " | {$product->category->name}";
                }
                $suggestions[] = "Consider enhancing to: \"{$enhancedTitle}\" (" . strlen($enhancedTitle) . " chars)";
            }
            
            // Check if title contains keywords from the product name
            if (!empty($title) && !str_contains(strtolower($title), strtolower($product->name))) {
                $issues[] = "Product #{$product->id} meta title doesn't contain the product name";
                $suggestions[] = "Include the product name '{$product->name}' in the meta title for better SEO";
            }
        }
        
        // Check categories
        foreach ($categories as $category) {
            $title = $category->meta_title ?: $category->name;
            
            if (empty($title)) {
                $issues[] = "Category #{$category->id} ({$category->name}) missing meta title";
                // Generate suggestion based on category data
                $suggestedTitle = $category->name . " Products | Celestial Cosmetics";
                $suggestions[] = "Suggested title for {$category->name}: \"{$suggestedTitle}\" (" . strlen($suggestedTitle) . " chars)";
            } elseif (strlen($title) > 60) {
                $issues[] = "Category #{$category->id} ({$category->name}) meta title too long (" . strlen($title) . " chars)";
                // Suggest a shortened version
                $shortTitle = substr($title, 0, 57) . '...';
                $suggestions[] = "Consider shortening to: \"{$shortTitle}\" (60 chars)";
            } elseif (strlen($title) < 30) {
                $issues[] = "Category #{$category->id} ({$category->name}) meta title too short (" . strlen($title) . " chars)";
                // Suggest an enhanced version
                $enhancedTitle = $title . " | Beauty Products | Celestial Cosmetics";
                $suggestions[] = "Consider enhancing to: \"{$enhancedTitle}\" (" . strlen($enhancedTitle) . " chars)";
            }
            
            // Check if title contains keywords from the category name
            if (!empty($title) && !str_contains(strtolower($title), strtolower($category->name))) {
                $issues[] = "Category #{$category->id} meta title doesn't contain the category name";
                $suggestions[] = "Include the category name '{$category->name}' in the meta title for better SEO";
            }
        }
        
        return [
            'pass' => count($issues) === 0,
            'issues' => $issues,
            'suggestions' => $suggestions
        ];
    }

    /**
     * Check for meta description issues and provide suggestions
     */
    private function checkMetaDescriptions($products, $categories)
    {
        $issues = [];
        $suggestions = [];
        
        // Check products
        foreach ($products as $product) {
            $description = $product->meta_description ?: $product->description;
            
            if (empty($description)) {
                $issues[] = "Product #{$product->id} ({$product->name}) missing meta description";
                // Generate suggestion based on product data
                $suggestedDesc = "Discover our {$product->name}";
                if ($product->brand) {
                    $suggestedDesc .= " from {$product->brand}";
                }
                if ($product->description) {
                    $shortDesc = substr(strip_tags($product->description), 0, 100);
                    $suggestedDesc .= ". {$shortDesc}...";
                } else {
                    $suggestedDesc .= ". High-quality beauty product from Celestial Cosmetics.";
                }
                $suggestions[] = "Suggested description for {$product->name}: \"{$suggestedDesc}\" (" . strlen($suggestedDesc) . " chars)";
            } elseif (strlen($description) > 160) {
                $issues[] = "Product #{$product->id} ({$product->name}) meta description too long (" . strlen($description) . " chars)";
                // Suggest a shortened version
                $shortDesc = substr($description, 0, 157) . '...';
                $suggestions[] = "Consider shortening to: \"{$shortDesc}\" (160 chars)";
            } elseif (strlen($description) < 50) {
                $issues[] = "Product #{$product->id} ({$product->name}) meta description too short (" . strlen($description) . " chars)";
                // Suggest an enhanced version
                $enhancedDesc = $description;
                if (strlen($enhancedDesc) < 50 && $product->description) {
                    $additionalText = " " . substr(strip_tags($product->description), 0, 50 - strlen($enhancedDesc));
                    $enhancedDesc .= $additionalText;
                }
                $enhancedDesc .= " Shop now at Celestial Cosmetics.";
                $suggestions[] = "Consider enhancing to: \"{$enhancedDesc}\" (" . strlen($enhancedDesc) . " chars)";
            }
            
            // Check for call to action
            if (!empty($description) && !preg_match('/(shop|buy|discover|explore|get|order|find|browse)/i', $description)) {
                $issues[] = "Product #{$product->id} meta description lacks a call to action";
                $suggestions[] = "Add a call to action like 'Shop now', 'Discover', or 'Explore' to encourage clicks";
            }
            
            // Check for keyword inclusion
            if (!empty($description) && !str_contains(strtolower($description), strtolower($product->name))) {
                $issues[] = "Product #{$product->id} meta description doesn't contain the product name";
                $suggestions[] = "Include the product name '{$product->name}' in the meta description for better SEO";
            }
        }
        
        // Check categories
        foreach ($categories as $category) {
            $description = $category->meta_description ?: $category->description;
            
            if (empty($description)) {
                $issues[] = "Category #{$category->id} ({$category->name}) missing meta description";
                // Generate suggestion based on category data
                $productCount = \App\Models\Product::where('category_id', $category->id)->count();
                $suggestedDesc = "Explore our {$category->name} collection with {$productCount} products. High-quality beauty products from Celestial Cosmetics. Shop now for the best in beauty.";
                $suggestions[] = "Suggested description for {$category->name}: \"{$suggestedDesc}\" (" . strlen($suggestedDesc) . " chars)";
            } elseif (strlen($description) > 160) {
                $issues[] = "Category #{$category->id} ({$category->name}) meta description too long (" . strlen($description) . " chars)";
                // Suggest a shortened version
                $shortDesc = substr($description, 0, 157) . '...';
                $suggestions[] = "Consider shortening to: \"{$shortDesc}\" (160 chars)";
            } elseif (strlen($description) < 50) {
                $issues[] = "Category #{$category->id} ({$category->name}) meta description too short (" . strlen($description) . " chars)";
                // Suggest an enhanced version
                $productCount = \App\Models\Product::where('category_id', $category->id)->count();
                $enhancedDesc = $description . " Browse our collection of {$productCount} {$category->name} products. Shop now at Celestial Cosmetics.";
                $enhancedDesc = substr($enhancedDesc, 0, 160);
                $suggestions[] = "Consider enhancing to: \"{$enhancedDesc}\" (" . strlen($enhancedDesc) . " chars)";
            }
            
            // Check for call to action
            if (!empty($description) && !preg_match('/(shop|buy|discover|explore|get|order|find|browse)/i', $description)) {
                $issues[] = "Category #{$category->id} meta description lacks a call to action";
                $suggestions[] = "Add a call to action like 'Shop now', 'Discover', or 'Explore' to encourage clicks";
            }
        }
        
        return [
            'pass' => count($issues) === 0,
            'issues' => $issues,
            'suggestions' => $suggestions
        ];
    }

    /**
     * Check structured data and provide recommendations
     */
    private function checkStructuredData()
    {
        $issues = [];
        $suggestions = [];
        
        // Check if structured data is enabled
        $enabled = $this->getSetting('enable_structured_data', '0') === '1';
        
        if (!$enabled) {
            $issues[] = "Structured data is not enabled in settings";
            $suggestions[] = "Enable structured data in SEO settings to improve search engine understanding of your content";
        }
        
        // Check product schema
        $productSchema = $this->getSetting('default_schema_product');
        if (empty($productSchema)) {
            $issues[] = "Default product schema template is missing";
            $suggestions[] = "Add a Product schema template following Schema.org standards";
        } else {
            try {
                $schema = json_decode($productSchema, true);
                if (!isset($schema['@context']) || !isset($schema['@type'])) {
                    $issues[] = "Default product schema is missing required properties";
                    $suggestions[] = "Ensure the product schema has @context and @type properties";
                }
                
                // Check for missing recommended properties
                $recommendedProperties = ['name', 'description', 'image', 'offers', 'brand', 'sku'];
                $missingProperties = [];
                
                foreach ($recommendedProperties as $property) {
                    if (!isset($schema[$property])) {
                        $missingProperties[] = $property;
                    }
                }
                
                if (!empty($missingProperties)) {
                    $issues[] = "Product schema is missing recommended properties: " . implode(', ', $missingProperties);
                    $suggestions[] = "Add the following properties to your product schema: " . implode(', ', $missingProperties);
                }
                
                // Check offers properties
                if (isset($schema['offers']) && is_array($schema['offers'])) {
                    $offerRecommendedProps = ['price', 'priceCurrency', 'availability', 'url'];
                    $missingOfferProps = [];
                    
                    foreach ($offerRecommendedProps as $property) {
                        if (!isset($schema['offers'][$property])) {
                            $missingOfferProps[] = $property;
                        }
                    }
                    
                }
            } catch (\Exception $e) {
                $issues[] = "Default product schema is invalid JSON";
            }
        }
        
        return [
            'pass' => count($issues) === 0,
            'issues' => $issues
        ];
    }

    /**
     * Check robots.txt and provide suggestions
     */
    private function checkRobotsTxt()
    {
        $issues = [];
        $suggestions = [];
        
        // Check if robots.txt exists
        $robotsTxtContent = $this->getSetting('robots_txt_content');
        
        if (empty($robotsTxtContent)) {
            $issues[] = "Robots.txt content is empty";
            
            // Create a sample robots.txt
            $sampleRobotsTxt = "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /checkout/\nDisallow: /cart/\nDisallow: /account/\nSitemap: " . url('sitemap.xml');
            $suggestions[] = "Create a robots.txt file with the following content:\n```\n{$sampleRobotsTxt}\n```";
        } else {
            // Check for some common elements
            if (!str_contains($robotsTxtContent, 'User-agent:')) {
                $issues[] = "Robots.txt is missing User-agent directive";
                $suggestions[] = "Add a User-agent directive, e.g., 'User-agent: *'";
            }
            
            if (!str_contains($robotsTxtContent, 'Sitemap:')) {
                $issues[] = "Robots.txt is missing Sitemap directive";
                $suggestions[] = "Add a Sitemap directive, e.g., 'Sitemap: " . url('sitemap.xml') . "'";
            }
            
            // Check for potentially missing disallow directives
            $importantDisallowPaths = ['/admin', '/checkout', '/cart', '/account', '/login', '/register'];
            $missingDisallowPaths = [];
            
            foreach ($importantDisallowPaths as $path) {
                if (!str_contains($robotsTxtContent, "Disallow: {$path}")) {
                    $missingDisallowPaths[] = $path;
                }
            }
            
            if (!empty($missingDisallowPaths)) {
                $issues[] = "Robots.txt is missing important Disallow directives";
                $suggestions[] = "Consider adding Disallow directives for private sections: " . implode(', ', $missingDisallowPaths);
            }
            
            // Check for potentially missing allow directives
            $importantAllowPaths = ['/products', '/categories', '/blog'];
            $missingAllowPaths = [];
            
            foreach ($importantAllowPaths as $path) {
                if (!str_contains($robotsTxtContent, "Allow: {$path}") && file_exists(public_path($path))) {
                    $missingAllowPaths[] = $path;
                }
            }
            
            if (!empty($missingAllowPaths)) {
                $issues[] = "Robots.txt could benefit from explicit Allow directives";
                $suggestions[] = "Consider adding explicit Allow directives for important content: " . implode(', ', $missingAllowPaths);
            }
            
            // Check for syntax issues
            $lines = explode("\n", $robotsTxtContent);
            foreach ($lines as $index => $line) {
                $lineNumber = $index + 1;
                $line = trim($line);
                
                if (!empty($line) && !preg_match('/^(User-agent|Allow|Disallow|Sitemap|Crawl-delay|Host):/i', $line)) {
                    $issues[] = "Robots.txt line {$lineNumber} has invalid syntax: {$line}";
                    $suggestions[] = "Fix syntax on line {$lineNumber}. Each line should start with a valid directive like User-agent, Allow, Disallow, etc.";
                }
            }
            
            // Check if there's a crawl delay
            if (!str_contains($robotsTxtContent, 'Crawl-delay:')) {
                $suggestions[] = "Consider adding a Crawl-delay directive if your server has performance issues with crawlers: 'Crawl-delay: 1'";
            }
        }
        
        // Check if physical robots.txt file exists
        if (!file_exists(public_path('robots.txt'))) {
            $issues[] = "Physical robots.txt file is missing in public directory";
            $suggestions[] = "Generate the robots.txt file using the admin interface";
        } else {
            $fileContent = file_get_contents(public_path('robots.txt'));
            $settingContent = $this->getSetting('robots_txt_content');
            
            if ($fileContent !== $settingContent) {
                $issues[] = "Physical robots.txt file doesn't match the settings value";
                $suggestions[] = "Regenerate the robots.txt file to match the current settings";
            }
        }
        
        return [
            'pass' => count($issues) === 0,
            'issues' => $issues,
            'suggestions' => $suggestions
        ];
    }

    /**
     * Check sitemap and provide suggestions
     */
    private function checkSitemap()
    {
        $issues = [];
        $suggestions = [];
        
        // Check if sitemap.xml exists
        if (!file_exists(public_path('sitemap.xml'))) {
            $issues[] = "Sitemap.xml file not found";
            $suggestions[] = "Generate a sitemap using the 'Generate Sitemap' button in the SEO dashboard";
        } else {
            // Check sitemap.xml age
            $sitemapAge = Carbon::parse(filemtime(public_path('sitemap.xml')))->diffInDays();
            
            if ($sitemapAge > 7) {
                $issues[] = "Sitemap.xml is {$sitemapAge} days old (older than 7 days)";
                $suggestions[] = "Regenerate your sitemap to ensure it contains the latest content. Consider setting up a weekly automatic regeneration";
            }
            
            // Check sitemap.xml content
            $sitemapContent = file_get_contents(public_path('sitemap.xml'));
            
            if (!str_contains($sitemapContent, '<urlset')) {
                $issues[] = "Sitemap.xml does not appear to be a valid sitemap";
                $suggestions[] = "Regenerate your sitemap as it appears to be invalid";
            } else {
                // Analyze sitemap content
                try {
                    $xml = simplexml_load_file(public_path('sitemap.xml'));
                    $urlCount = count($xml->url);
                    
                    if ($urlCount < 5) {
                        $issues[] = "Sitemap contains only {$urlCount} URLs, which seems low";
                        $suggestions[] = "Make sure all your important pages are included in the sitemap";
                    }
                    
                    // Check if homepage is in sitemap
                    $homepageFound = false;
                    foreach ($xml->url as $url) {
                        $loc = (string)$url->loc;
                        if ($loc === url('/') || $loc === url('')) {
                            $homepageFound = true;
                            break;
                        }
                    }
                    
                    if (!$homepageFound) {
                        $issues[] = "Homepage is not included in the sitemap";
                        $suggestions[] = "Ensure your homepage is included in the sitemap";
                    }
                    
                    // Count missing priority and changefreq attributes
                    $missingPriority = 0;
                    $missingChangefreq = 0;
                    
                    foreach ($xml->url as $url) {
                        if (!isset($url->priority)) {
                            $missingPriority++;
                        }
                        if (!isset($url->changefreq)) {
                            $missingChangefreq++;
                        }
                    }
                    
                    if ($missingPriority > 0) {
                        $issues[] = "{$missingPriority} URLs in the sitemap are missing priority";
                        $suggestions[] = "Add priority attributes to URLs in your sitemap to guide search engines";
                    }
                    
                    if ($missingChangefreq > 0) {
                        $issues[] = "{$missingChangefreq} URLs in the sitemap are missing changefreq";
                        $suggestions[] = "Add changefreq attributes to URLs in your sitemap";
                    }
                    
                    // Check product URLs
                    $productCount = \App\Models\Product::count();
                    $productUrlsInSitemap = 0;
                    
                    foreach ($xml->url as $url) {
                        $loc = (string)$url->loc;
                        if (str_contains($loc, '/products/')) {
                            $productUrlsInSitemap++;
                        }
                    }
                    
                    if ($productCount > 0 && $productUrlsInSitemap < $productCount) {
                        $issues[] = "Not all products ({$productUrlsInSitemap} of {$productCount}) are included in the sitemap";
                        $suggestions[] = "Regenerate your sitemap to include all active products";
                    }
                    
                    // Check category URLs
                    $categoryCount = \App\Models\Category::count();
                    $categoryUrlsInSitemap = 0;
                    
                    foreach ($xml->url as $url) {
                        $loc = (string)$url->loc;
                        if (str_contains($loc, '/categories/')) {
                            $categoryUrlsInSitemap++;
                        }
                    }
                    
                    if ($categoryCount > 0 && $categoryUrlsInSitemap < $categoryCount) {
                        $issues[] = "Not all categories ({$categoryUrlsInSitemap} of {$categoryCount}) are included in the sitemap";
                        $suggestions[] = "Regenerate your sitemap to include all categories";
                    }
                } catch (\Exception $e) {
                    $issues[] = "Error parsing sitemap.xml: " . $e->getMessage();
                    $suggestions[] = "Regenerate your sitemap as it appears to be invalid";
                }
            }
            
            // Check if sitemap is registered with Google
            $robotsTxtContent = file_exists(public_path('robots.txt')) ? file_get_contents(public_path('robots.txt')) : '';
            if (!str_contains($robotsTxtContent, 'Sitemap:')) {
                $issues[] = "Sitemap is not registered in robots.txt";
                $suggestions[] = "Add 'Sitemap: " . url('sitemap.xml') . "' to your robots.txt file";
            }
            
            // Check if sitemap is gzipped
            if (!file_exists(public_path('sitemap.xml.gz'))) {
                $suggestions[] = "Consider creating a compressed version of your sitemap (sitemap.xml.gz) to reduce bandwidth usage";
            }
        }
        
        return [
            'pass' => count($issues) === 0,
            'issues' => $issues,
            'suggestions' => $suggestions
        ];
    }

    /**
     * Check canonical URLs
     */
    private function checkCanonicalUrls($products, $categories)
    {
        $issues = [];
        $suggestions = []; // Add suggestions array
        
        // Check products
        foreach ($products as $product) {
            if ($product->canonical_url && !filter_var($product->canonical_url, FILTER_VALIDATE_URL)) {
                $issues[] = "Product #{$product->id} ({$product->name}) has an invalid canonical URL: {$product->canonical_url}";
                $suggestions[] = "Update canonical URL for {$product->name} to be a valid URL or remove it to use the default product URL";
            }
        }
        
        // Check categories
        foreach ($categories as $category) {
            if ($category->canonical_url && !filter_var($category->canonical_url, FILTER_VALIDATE_URL)) {
                $issues[] = "Category #{$category->id} ({$category->name}) has an invalid canonical URL: {$category->canonical_url}";
                $suggestions[] = "Update canonical URL for {$category->name} to be a valid URL or remove it to use the default category URL";
            }
        }
        
        return [
            'pass' => count($issues) === 0,
            'issues' => $issues,
            'suggestions' => $suggestions // Add suggestions to return value
        ];
    }

    /**
     * Display the robots.txt editor page.
     *
     * @return \Illuminate\Http\Response
     */
    public function robotsTxt()
    {
        $pageTitle = 'Robots.txt Editor';
        
        // Get the current robots.txt content from settings
        $robotsTxtContent = $this->getSetting('robots_txt_content', '');
        
        // Get all the robot rules
        $rules = RobotsTxtRule::orderBy('order')->get();
        
        return view('admin.seo.robots-txt', compact('pageTitle', 'robotsTxtContent', 'rules'));
    }

    /**
     * Update the robots.txt content.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateRobotsTxt(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'robots_txt_content' => 'required|string',
        ]);
        
        // Update the setting
        Setting::where('key', 'robots_txt_content')->update([
            'value' => $validated['robots_txt_content']
        ]);
        
        // Write to the robots.txt file
        file_put_contents(public_path('robots.txt'), $validated['robots_txt_content']);
        
        // Log the activity
        Activity::create([
            'description' => 'Updated robots.txt content',
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'subject_type' => Setting::class,
            'subject_id' => null,
            'status' => 'completed',
            'properties' => json_encode(['action' => 'updated'])
        ]);
        
        return redirect()->route('admin.seo.robots-txt')
            ->with('success', 'Robots.txt file updated successfully');
    }

    /**
     * Manage robot rules.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function manageRobotRules(Request $request)
    {
        // Delete existing rules
        RobotsTxtRule::truncate();
        
        // Add new rules
        $rules = $request->input('rules', []);
        
        foreach ($rules as $index => $rule) {
            if (!empty($rule['directive']) && !empty($rule['value'])) {
                RobotsTxtRule::create([
                    'user_agent' => $rule['user_agent'] ?? '*',
                    'directive' => $rule['directive'],
                    'value' => $rule['value'],
                    'order' => $index,
                    'is_active' => isset($rule['is_active']) && $rule['is_active'] == '1'
                ]);
            }
        }
        
        // Generate robots.txt content and update settings
        $content = RobotsTxtRule::generateRobotsTxt();
        
        Setting::where('key', 'robots_txt_content')->update([
            'value' => $content
        ]);
        
        // Write to the robots.txt file
        file_put_contents(public_path('robots.txt'), $content);
        
        // Log the activity
        Activity::create([
            'description' => 'Updated robots.txt rules',
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'subject_type' => Setting::class,
            'subject_id' => null,
            'status' => 'completed',
            'properties' => json_encode(['action' => 'updated'])
        ]);
        
        return redirect()->route('admin.seo.robots-txt')
            ->with('success', 'Robots.txt rules updated successfully');
    }

    /**
     * Display the redirects management page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirects()
    {
        $pageTitle = 'Redirects Management';
        
        // Get all redirects
        $redirects = Redirect::orderBy('source_url')->paginate(15);
        
        return view('admin.seo.redirects', compact('pageTitle', 'redirects'));
    }

    /**
     * Store a new redirect.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeRedirect(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'source_url' => 'required|string|unique:redirects',
            'target_url' => 'required|string',
            'type' => 'required|in:301,302',
            'is_active' => 'boolean',
            'notes' => 'nullable|string'
        ]);
        
        // Create the redirect
        Redirect::create($validated);
        
        // Log the activity
        Activity::create([
            'description' => 'Created a new redirect from ' . $validated['source_url'] . ' to ' . $validated['target_url'],
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'subject_type' => Redirect::class,
            'subject_id' => null,
            'status' => 'completed',
            'properties' => json_encode(['action' => 'created'])
        ]);
        
        return redirect()->route('admin.seo.redirects')
            ->with('success', 'Redirect created successfully');
    }

    /**
     * Update an existing redirect.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateRedirect(Request $request, $id)
    {
        // Find the redirect
        $redirect = Redirect::findOrFail($id);
        
        // Validate the request
        $validated = $request->validate([
            'source_url' => 'required|string|unique:redirects,source_url,' . $id,
            'target_url' => 'required|string',
            'type' => 'required|in:301,302',
            'is_active' => 'boolean',
            'notes' => 'nullable|string'
        ]);
        
        // Update the redirect
        $redirect->update($validated);
        
        // Log the activity
        Activity::create([
            'description' => 'Updated redirect from ' . $validated['source_url'] . ' to ' . $validated['target_url'],
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'subject_type' => Redirect::class,
            'subject_id' => $redirect->id,
            'status' => 'completed',
            'properties' => json_encode(['action' => 'updated'])
        ]);
        
        return redirect()->route('admin.seo.redirects')
            ->with('success', 'Redirect updated successfully');
    }

    /**
     * Delete a redirect.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyRedirect($id)
    {
        // Find the redirect
        $redirect = Redirect::findOrFail($id);
        
        // Log the activity before deleting
        Activity::create([
            'description' => 'Deleted redirect from ' . $redirect->source_url . ' to ' . $redirect->target_url,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'subject_type' => Redirect::class,
            'subject_id' => $redirect->id,
            'status' => 'completed',
            'properties' => json_encode(['action' => 'deleted'])
        ]);
        
        // Delete the redirect
        $redirect->delete();
        
        return redirect()->route('admin.seo.redirects')
            ->with('success', 'Redirect deleted successfully');
    }

    /**
     * Display the structured data management page.
     *
     * @return \Illuminate\Http\Response
     */
    public function structuredData()
    {
        $pageTitle = 'Structured Data Management';
        
        try {
            // Check if settings table exists
            if (DB::getSchemaBuilder()->hasTable('settings')) {
        // Get structured data settings
        $structuredDataSettings = [
            'enable_structured_data' => Setting::where('key', 'enable_structured_data')->value('value') == '1',
            'default_schema_product' => Setting::where('key', 'default_schema_product')->value('value'),
            'default_schema_breadcrumbs' => Setting::where('key', 'default_schema_breadcrumbs')->value('value')
        ];
            } else {
                throw new \Exception('Settings table does not exist');
            }
        } catch (\Exception $e) {
            // Try to get data from normalized tables
            $seoDefaults = SeoDefaults::first();
            
            if ($seoDefaults) {
                // Convert SeoDefaults model to structured data settings format
                $structuredDataSettings = [
                    'enable_structured_data' => $seoDefaults->enable_structured_data,
                    'default_schema_product' => $seoDefaults->default_schema_product ?? '{"@context":"https://schema.org","@type":"Product","name":"Product Name","description":"Product Description"}',
                    'default_schema_breadcrumbs' => $seoDefaults->default_schema_breadcrumbs ?? '{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[]}'
                ];
            } else {
                // Default values if SeoDefaults doesn't exist
                $structuredDataSettings = [
                    'enable_structured_data' => true,
                    'default_schema_product' => '{"@context":"https://schema.org","@type":"Product","name":"Product Name","description":"Product Description"}',
                    'default_schema_breadcrumbs' => '{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[]}'
                ];
            }
        }
        
        // Get custom structured data
        $customData = StructuredData::with('entity')
            ->orderBy('entity_type')
            ->orderBy('entity_id')
            ->paginate(10);
        
        // Get page-specific schema markups
        $schemas = DB::table('schema_markups')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('admin.seo.structured-data', compact('pageTitle', 'structuredDataSettings', 'customData', 'schemas'));
    }

    /**
     * Update structured data settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStructuredDataSettings(Request $request)
    {
        // Validate schema templates
        try {
            $productSchema = json_decode($request->input('default_schema_product', '{}'), true);
            $breadcrumbSchema = json_decode($request->input('default_schema_breadcrumbs', '{}'), true);
            
            // Basic validation
            if (!isset($productSchema['@context']) || $productSchema['@context'] !== 'https://schema.org') {
                return redirect()->back()->with('error', 'Product schema must have @context set to https://schema.org');
            }
            
            if (!isset($breadcrumbSchema['@context']) || $breadcrumbSchema['@context'] !== 'https://schema.org') {
                return redirect()->back()->with('error', 'Breadcrumb schema must have @context set to https://schema.org');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Invalid JSON in schema templates');
        }
        
        try {
            // Check if settings table exists
            if (DB::getSchemaBuilder()->hasTable('settings')) {
        // Update enable/disable setting
        Setting::where('key', 'enable_structured_data')->update([
            'value' => $request->input('enable_structured_data') ? '1' : '0'
        ]);
        
        // Update schema templates
        Setting::where('key', 'default_schema_product')->update([
            'value' => json_encode($productSchema)
        ]);
        
        Setting::where('key', 'default_schema_breadcrumbs')->update([
            'value' => json_encode($breadcrumbSchema)
        ]);
            } else {
                throw new \Exception('Settings table does not exist');
            }
        } catch (\Exception $e) {
            // Try to update normalized tables
            $seoDefaults = SeoDefaults::first();
            
            if (!$seoDefaults) {
                $seoDefaults = new SeoDefaults();
            }
            
            // Map settings to SeoDefaults model fields
            $seoDefaults->enable_structured_data = $request->input('enable_structured_data') ? true : false;
            $seoDefaults->default_schema_product = json_encode($productSchema);
            $seoDefaults->default_schema_breadcrumbs = json_encode($breadcrumbSchema);
            
            $seoDefaults->save();
        }
        
        // Log the activity
        Activity::create([
            'description' => 'Updated structured data settings',
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'subject_type' => SeoDefaults::class,
            'subject_id' => null,
            'status' => 'completed',
            'properties' => json_encode(['action' => 'updated'])
        ]);
        
        return redirect()->route('admin.seo.structured-data')
            ->with('success', 'Structured data settings updated successfully');
    }

    /**
     * Show the form for editing a schema markup.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editStructuredData($id)
    {
        // Find the schema markup
        $schema = DB::table('schema_markups')->where('id', $id)->first();
        
        if (!$schema) {
            return redirect()->route('admin.seo.structured-data')
                ->with('error', 'Schema markup not found');
        }
        
        $pageTitle = 'Edit Schema Markup';
        
        return view('admin.seo.edit-structured-data', compact('pageTitle', 'schema'));
    }

    /**
     * Store a new custom structured data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeStructuredData(Request $request)
    {
        try {
            // Basic validation for all schema types
            $request->validate([
                'page_url' => 'required|string',
            'schema_type' => 'required|string',
                'is_active' => 'nullable|boolean'
            ]);
            
            // Get currency settings
            try {
                // Check if settings table exists
                if (DB::getSchemaBuilder()->hasTable('settings')) {
            $currencySettings = Setting::whereIn('key', [
                'default_currency',
                'currency_symbol'
            ])->get()->keyBy('key');
            
            $defaultCurrency = $currencySettings->get('default_currency')->value ?? 'EGP';
                } else {
                    throw new \Exception('Settings table does not exist');
                }
            } catch (\Exception $e) {
                // Try to get data from normalized tables
                $currencyConfig = CurrencyConfig::first();
                
                if ($currencyConfig) {
                    $defaultCurrency = $currencyConfig->currency_code ?? 'EGP';
                } else {
                    // Default value if neither exists
                    $defaultCurrency = 'EGP';
                }
            }
            
            // Process the structured data based on schema type
            $schemaData = [];
            
            // Get active status (default to true if not provided)
            $isActive = $request->has('is_active') ? true : false;
            
            switch($request->schema_type) {
                case 'WebPage':
                    $schemaData = [
                        '@context' => 'https://schema.org',
                        '@type' => 'WebPage',
                        'name' => $request->webpage_name,
                        'description' => $request->webpage_description
                    ];
                    break;
                    
                case 'Article':
                    $schemaData = [
                        '@context' => 'https://schema.org',
                        '@type' => 'Article',
                        'headline' => $request->article_headline,
                        'author' => [
                            '@type' => 'Person',
                            'name' => $request->article_author
                        ],
                        'datePublished' => $request->article_published_date,
                        'image' => $request->article_image
                    ];
                    break;
                    
                case 'FAQPage':
                    $questions = $request->faq_questions ?? [];
                    $answers = $request->faq_answers ?? [];
                    $faqItems = [];
                    
                    for ($i = 0; $i < count($questions); $i++) {
                        if (!empty($questions[$i]) && !empty($answers[$i])) {
                            $faqItems[] = [
                                '@type' => 'Question',
                                'name' => $questions[$i],
                                'acceptedAnswer' => [
                                    '@type' => 'Answer',
                                    'text' => $answers[$i]
                                ]
                            ];
                        }
                    }
                    
                    $schemaData = [
                        '@context' => 'https://schema.org',
                        '@type' => 'FAQPage',
                        'mainEntity' => $faqItems
                    ];
                    break;
                    
                case 'Custom':
                    // Additional validation for Custom schema type
                    $request->validate([
                        'custom_schema' => 'required'
                    ], [
                        'custom_schema.required' => 'The custom schema field is required when using Custom schema type.'
                    ]);
                    
                    try {
                        // First check if it's valid JSON
                        if (empty($request->custom_schema)) {
                            return redirect()->back()
                                ->withInput()
                                ->with('error', 'Custom schema cannot be empty');
                        }
                        
                        $schemaData = json_decode($request->custom_schema, true);
                        
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            return redirect()->back()
                                ->withInput()
                                ->with('error', 'Invalid JSON format for custom schema: ' . json_last_error_msg());
                        }
                        
                        // Validate basic schema.org structure
                        if (!isset($schemaData['@context']) || $schemaData['@context'] !== 'https://schema.org') {
                            return redirect()->back()
                                ->withInput()
                                ->with('error', 'Schema must have @context set to https://schema.org');
                        }
                        
                        if (!isset($schemaData['@type'])) {
                            return redirect()->back()
                                ->withInput()
                                ->with('error', 'Schema must have an @type property');
                        }
                        
                        // If it's a product schema, ensure it uses the correct currency
                        if (isset($schemaData['@type']) && $schemaData['@type'] === 'Product' && isset($schemaData['offers'])) {
                            // Set the currency to the default if not specified
                            if (!isset($schemaData['offers']['priceCurrency'])) {
                                $schemaData['offers']['priceCurrency'] = $defaultCurrency;
                            }
                        }
                        
                        // If it's an ItemList with Product items, ensure they use the correct currency
                        if (isset($schemaData['@type']) && $schemaData['@type'] === 'ItemList' && 
                            isset($schemaData['itemListElement']) && is_array($schemaData['itemListElement'])) {
                            
                            foreach ($schemaData['itemListElement'] as &$item) {
                                if (isset($item['item']) && isset($item['item']['@type']) && 
                                    $item['item']['@type'] === 'Product' && isset($item['item']['offers'])) {
                                    
                                    // Set the currency to the default if not specified
                                    if (!isset($item['item']['offers']['priceCurrency'])) {
                                        $item['item']['offers']['priceCurrency'] = $defaultCurrency;
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        return redirect()->back()
                            ->withInput()
                            ->with('error', 'Error parsing custom schema: ' . $e->getMessage());
                    }
                    break;
                    
                default:
                    // Unknown schema type
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Unknown schema type selected');
            }
            
            // Insert into schema_markups table
            DB::table('schema_markups')->insert([
                'page_url' => $request->page_url,
                'schema_type' => $request->schema_type,
                'schema_data' => json_encode($schemaData),
                'is_active' => $isActive,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        
        // Log the activity
            Activity::create([
                'description' => 'Created a new schema markup for page: ' . $request->page_url,
                'causer_type' => get_class(auth()->user()),
                'causer_id' => auth()->id(),
                'subject_type' => 'schema_markup',
                'subject_id' => null,
                'status' => 'completed',
                'properties' => json_encode(['action' => 'created'])
            ]);
            
            return redirect()->route('admin.seo.structured-data')
                ->with('success', 'Schema markup created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Please check the form for errors');
        } catch (\Exception $e) {
            // Handle other exceptions
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error saving schema markup: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing structured data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStructuredData(Request $request, $id)
    {
        try {
            // Find the schema markup
            $schema = DB::table('schema_markups')->where('id', $id)->first();
            
            if (!$schema) {
                return redirect()->route('admin.seo.structured-data')
                    ->with('error', 'Schema markup not found');
            }
            
            // Get currency settings
            try {
                // Check if settings table exists
                if (DB::getSchemaBuilder()->hasTable('settings')) {
            $currencySettings = Setting::whereIn('key', [
                'default_currency',
                'currency_symbol'
            ])->get()->keyBy('key');
            
            $defaultCurrency = $currencySettings->get('default_currency')->value ?? 'EGP';
                } else {
                    throw new \Exception('Settings table does not exist');
                }
            } catch (\Exception $e) {
                // Try to get data from normalized tables
                $currencyConfig = CurrencyConfig::first();
                
                if ($currencyConfig) {
                    $defaultCurrency = $currencyConfig->currency_code ?? 'EGP';
                } else {
                    // Default value if neither exists
                    $defaultCurrency = 'EGP';
                }
            }
        
        // Validate the request
        $validated = $request->validate([
                'page_url' => 'required|string',
            'schema_type' => 'required|string',
                'custom_schema' => 'required|json',
                'is_active' => 'nullable|boolean'
            ]);
            
            // Get active status (default to false if not provided)
            $isActive = $request->has('is_active') ? true : false;
            
            // Parse the JSON schema data
            $schemaData = json_decode($validated['custom_schema'], true);
            
            // Apply currency to product schema if needed
            if (isset($schemaData['@type'])) {
                if ($schemaData['@type'] === 'Product' && isset($schemaData['offers'])) {
                    // Set the currency to the default if not specified
                    if (!isset($schemaData['offers']['priceCurrency'])) {
                        $schemaData['offers']['priceCurrency'] = $defaultCurrency;
                    }
                } elseif ($schemaData['@type'] === 'ItemList' && 
                    isset($schemaData['itemListElement']) && is_array($schemaData['itemListElement'])) {
                    
                    foreach ($schemaData['itemListElement'] as &$item) {
                        if (isset($item['item']) && isset($item['item']['@type']) && 
                            $item['item']['@type'] === 'Product' && isset($item['item']['offers'])) {
                            
                            // Set the currency to the default if not specified
                            if (!isset($item['item']['offers']['priceCurrency'])) {
                                $item['item']['offers']['priceCurrency'] = $defaultCurrency;
                            }
                        }
                    }
                }
            }
            
            // Update the schema markup
            DB::table('schema_markups')
                ->where('id', $id)
                ->update([
                    'page_url' => $validated['page_url'],
                    'schema_type' => $validated['schema_type'],
                    'schema_data' => json_encode($schemaData),
                    'is_active' => $isActive,
                    'updated_at' => now()
                ]);
        
        // Log the activity
            Activity::create([
                'description' => 'Updated schema markup for page: ' . $validated['page_url'],
                'causer_type' => get_class(auth()->user()),
                'causer_id' => auth()->id(),
                'subject_type' => 'schema_markup',
                'subject_id' => $id,
                'status' => 'completed',
                'properties' => json_encode(['action' => 'updated'])
            ]);
            
            return redirect()->route('admin.seo.structured-data')
                ->with('success', 'Schema markup updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating schema markup: ' . $e->getMessage());
        }
    }

    /**
     * Delete a structured data.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyStructuredData($id)
    {
        try {
            // Find the schema markup record
            $schema = DB::table('schema_markups')->where('id', $id)->first();
            
            if (!$schema) {
                return redirect()->route('admin.seo.structured-data')
                    ->with('error', 'Schema markup not found');
            }
        
        // Log the activity before deleting
            Activity::create([
                'description' => 'Deleted schema markup for page: ' . $schema->page_url,
                'causer_type' => get_class(auth()->user()),
                'causer_id' => auth()->id(),
                'subject_type' => 'schema_markup',
                'subject_id' => $id,
                'status' => 'completed',
                'properties' => json_encode(['action' => 'deleted'])
            ]);
            
            // Delete the schema markup
            DB::table('schema_markups')->where('id', $id)->delete();
            
            return redirect()->route('admin.seo.structured-data')
                ->with('success', 'Schema markup deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('admin.seo.structured-data')
                ->with('error', 'Error deleting schema markup: ' . $e->getMessage());
        }
    }

    /**
     * Get product sample schema data with real products
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductSample()
    {
        try {
            // Get currency settings from database
            try {
                // Check if settings table exists
                if (DB::getSchemaBuilder()->hasTable('settings')) {
            $currencySettings = Setting::whereIn('key', [
                'default_currency',
                'currency_symbol',
                'currency_position',
                'thousand_separator',
                'decimal_separator',
                'decimal_digits'
            ])->get()->keyBy('key');
            
                    $currencyCode = $currencySettings->get('default_currency')->value ?? 'EGP';
            $currencySymbol = $currencySettings->get('currency_symbol')->value ?? 'ج.م';
                } else {
                    throw new \Exception('Settings table does not exist');
                }
            } catch (\Exception $e) {
                // Try to get data from normalized tables
                $currencyConfig = CurrencyConfig::first();
                
                if ($currencyConfig) {
                    $currencyCode = $currencyConfig->currency_code ?? 'EGP';
                    $currencySymbol = $currencyConfig->currency_symbol ?? 'ج.م';
                } else {
                    // Default values if neither exists
                    $currencyCode = 'EGP';
                    $currencySymbol = 'ج.م';
                }
            }
            
            // Get a sample product
            $product = Product::with(['category', 'images', 'reviews'])
                ->where('status', 'active')
                ->first();
                
            if (!$product) {
                return response()->json([
                    'error' => 'No active products found'
                ], 404);
            }
            
            // Base product URL
            $productUrl = route('products.show', $product->slug);
            
            // Generate review data if available
            $reviewData = [];
            $ratingValue = 0;
            $reviewCount = 0;
            
            if ($product->reviews->count() > 0) {
                $reviewCount = $product->reviews->count();
                $ratingValue = $product->reviews->avg('rating');
                
                foreach ($product->reviews as $review) {
                    $reviewData[] = [
                        '@type' => 'Review',
                        'reviewRating' => [
                            '@type' => 'Rating',
                            'ratingValue' => $review->rating,
                            'bestRating' => 5
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'name' => $review->name
                        ],
                        'reviewBody' => $review->comment
                    ];
                }
            }
            
            // Generate image data
            $imageData = [];
            
            if ($product->images->count() > 0) {
                foreach ($product->images as $image) {
                    $imageData[] = asset('storage/' . $image->path);
                }
            } elseif ($product->image) {
                $imageData[] = asset('storage/' . $product->image);
            }
            
            // Format price
            $price = $product->price;
            if ($product->sale_price > 0 && $product->sale_price < $product->price) {
                $price = $product->sale_price;
            }
            
            // Build the schema
            $schema = [
                '@context' => 'https://schema.org',
                        '@type' => 'Product',
                        'name' => $product->name,
                'description' => $product->description,
                'sku' => $product->sku,
                'mpn' => $product->sku,
                'category' => $product->category ? $product->category->name : '',
                'image' => $imageData,
                        'brand' => [
                            '@type' => 'Brand',
                    'name' => $product->brand ?? 'Celestial Cosmetics'
                        ],
                        'offers' => [
                            '@type' => 'Offer',
                    'priceCurrency' => $currencyCode,
                    'price' => $price,
                            'availability' => $product->stock_quantity > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                    'url' => $productUrl
                ]
            ];
            
            // Add reviews if available
            if ($reviewCount > 0) {
                $schema['aggregateRating'] = [
                    '@type' => 'AggregateRating',
                    'ratingValue' => round($ratingValue, 1),
                    'reviewCount' => $reviewCount
                ];
                
                if (!empty($reviewData)) {
                    $schema['review'] = $reviewData;
                }
            }
            
            return response()->json($schema);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate and display the sitemap.
     *
     * @return \Illuminate\Http\Response
     */
    public function sitemapViewer()
    {
        $pageTitle = 'Sitemap Viewer';
        
        // Check if sitemap.xml exists
        $sitemapExists = file_exists(public_path('sitemap.xml'));
        $sitemapUrl = url('sitemap.xml');
        
        // Read the sitemap.xml content if it exists
        $sitemapContent = null;
        $parsedSitemap = null;
        
        if ($sitemapExists) {
            $sitemapContent = file_get_contents(public_path('sitemap.xml'));
            
            // Parse the XML to extract URLs and other information
            try {
                $xml = simplexml_load_file(public_path('sitemap.xml'));
                $urls = [];
                
                foreach ($xml->url as $url) {
                    $urlData = [
                        'loc' => (string) $url->loc,
                        'lastmod' => isset($url->lastmod) ? (string) $url->lastmod : null,
                        'priority' => isset($url->priority) ? (string) $url->priority : null,
                        'changefreq' => isset($url->changefreq) ? (string) $url->changefreq : null
                    ];
                    
                    $urls[] = $urlData;
                }
                
                $parsedSitemap = [
                    'count' => count($urls),
                    'urls' => $urls
                ];
            } catch (\Exception $e) {
                // If there's an error parsing the sitemap, just continue
            }
        }
        
        return view('admin.seo.sitemap-viewer', compact('pageTitle', 'sitemapExists', 'sitemapUrl', 'sitemapContent', 'parsedSitemap'));
    }

    /**
     * Show the form for editing the homepage SEO settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function editHomepage()
    {
        try {
            // Check if settings table exists
            if (DB::getSchemaBuilder()->hasTable('settings')) {
        $homepageSeo = Setting::where('group', 'seo')
            ->where(function($query) {
                $query->where('key', 'like', 'homepage_%')
                    ->orWhere('key', 'like', 'home_%');
            })
            ->get()
            ->keyBy('key');
            
        // If settings don't exist yet, use defaults
        if ($homepageSeo->isEmpty()) {
            $defaults = [
                'homepage_meta_title' => Setting::where('key', 'default_meta_title')->first()->value ?? config('app.name'),
                'homepage_meta_description' => Setting::where('key', 'default_meta_description')->first()->value ?? '',
                'homepage_meta_keywords' => Setting::where('key', 'default_meta_keywords')->first()->value ?? '',
                'homepage_og_image' => Setting::where('key', 'og_default_image')->first()->value ?? '',
                'homepage_twitter_card_type' => 'summary_large_image'
            ];
            
            foreach ($defaults as $key => $value) {
                Setting::firstOrCreate(
                    ['key' => $key],
                    [
                        'key' => $key,
                        'display_name' => ucwords(str_replace(['_', 'homepage', 'meta'], [' ', 'Homepage', 'Meta'], $key)),
                        'value' => $value,
                        'group' => 'seo'
                    ]
                );
            }
            
            // Refresh the collection
            $homepageSeo = Setting::where('group', 'seo')
                ->where(function($query) {
                    $query->where('key', 'like', 'homepage_%')
                        ->orWhere('key', 'like', 'home_%');
                })
                ->get()
                ->keyBy('key');
                }
            } else {
                throw new \Exception('Settings table does not exist');
            }
        } catch (\Exception $e) {
            // Try to get data from normalized tables
            $seoDefaults = $this->ensureSeoDefaultsExist();
            
            // Convert SeoDefaults model to collection format similar to settings
            $homepageSeo = collect([
                'homepage_meta_title' => (object) ['key' => 'homepage_meta_title', 'value' => $seoDefaults->default_meta_title],
                'homepage_meta_description' => (object) ['key' => 'homepage_meta_description', 'value' => $seoDefaults->default_meta_description],
                'homepage_meta_keywords' => (object) ['key' => 'homepage_meta_keywords', 'value' => $seoDefaults->default_meta_keywords],
                'homepage_og_image' => (object) ['key' => 'homepage_og_image', 'value' => $seoDefaults->og_default_image],
                'homepage_twitter_card_type' => (object) ['key' => 'homepage_twitter_card_type', 'value' => 'summary_large_image']
            ]);
        }
        
        return view('admin.seo.edit-homepage', ['homepageSeo' => $homepageSeo]);
    }
    
    /**
     * Update the homepage SEO settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateHomepage(Request $request)
    {
        $validated = $request->validate([
            'homepage_meta_title' => 'nullable|string|max:70',
            'homepage_meta_description' => 'nullable|string|max:160',
            'homepage_meta_keywords' => 'nullable|string|max:255',
            'homepage_og_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'homepage_twitter_card_type' => 'nullable|string|in:summary,summary_large_image',
        ]);
        
        $ogImagePath = null;
        // Handle og_image upload
        if ($request->hasFile('homepage_og_image')) {
            $ogImagePath = $request->file('homepage_og_image')->store('seo', 'public');
        }
        
        try {
            // Check if settings table exists
            if (DB::getSchemaBuilder()->hasTable('settings')) {
                // Delete old image if exists and a new one is uploaded
                if ($request->hasFile('homepage_og_image')) {
            $oldImage = Setting::where('key', 'homepage_og_image')->first();
            if ($oldImage && $oldImage->value) {
                Storage::disk('public')->delete($oldImage->value);
            }
                    $validated['homepage_og_image'] = $ogImagePath;
        }
        
        // Update the settings
        foreach ($validated as $key => $value) {
            if ($value !== null || $request->hasFile($key)) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => $value,
                        'group' => 'seo',
                        'display_name' => ucwords(str_replace(['_', 'homepage', 'meta'], [' ', 'Homepage', 'Meta'], $key))
                    ]
                );
            }
                }
            } else {
                throw new \Exception('Settings table does not exist');
            }
        } catch (\Exception $e) {
            // Try to update normalized tables
            $seoDefaults = SeoDefaults::first();
            
            if (!$seoDefaults) {
                $seoDefaults = new SeoDefaults();
            }
            
            // Map homepage settings to SeoDefaults model fields
            $seoDefaults->default_meta_title = $validated['homepage_meta_title'] ?? $seoDefaults->default_meta_title;
            $seoDefaults->default_meta_description = $validated['homepage_meta_description'] ?? $seoDefaults->default_meta_description;
            $seoDefaults->default_meta_keywords = $validated['homepage_meta_keywords'] ?? $seoDefaults->default_meta_keywords;
            
            // Handle og_image upload
            if ($request->hasFile('homepage_og_image')) {
                // Delete old image if exists
                if ($seoDefaults->og_default_image) {
                    Storage::disk('public')->delete($seoDefaults->og_default_image);
                }
                $seoDefaults->og_default_image = $ogImagePath;
            }
            
            $seoDefaults->save();
        }
        
        // Clear cache
        Cache::forget('settings');
        
        // Log activity
        Activity::create([
            'description' => 'Updated homepage SEO settings',
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'subject_type' => Setting::class,
            'subject_id' => null,
            'status' => 'completed',
            'properties' => json_encode(['action' => 'updated'])
        ]);
        
        return redirect()->route('admin.seo.edit-homepage')
            ->with('success', 'Homepage SEO settings updated successfully');
    }

    /**
     * Helper method to get a setting value from either the old settings table or the new normalized tables
     *
     * @param string $key The setting key
     * @param mixed $default The default value if not found
     * @param string $group The settings group (only used for old settings table)
     * @return mixed
     */
    private function getSetting($key, $default = null, $group = 'seo')
    {
        try {
            // Check if settings table exists
            if (DB::getSchemaBuilder()->hasTable('settings')) {
                $setting = Setting::where('key', $key)->first();
                return $setting ? $setting->value : $default;
            } else {
                throw new \Exception('Settings table does not exist');
            }
        } catch (\Exception $e) {
            // Try to get from normalized tables
            switch ($key) {
                // SeoDefaults table fields
                case 'default_meta_title':
                case 'default_meta_description':
                case 'default_meta_keywords':
                case 'og_default_image':
                case 'og_site_name':
                case 'twitter_site':
                case 'twitter_creator':
                case 'default_robots_content':
                case 'enable_structured_data':
                case 'enable_robots_txt':
                case 'enable_sitemap':
                case 'sitemap_change_frequency':
                case 'default_schema_product':
                case 'default_schema_breadcrumbs':
                    $seoDefaults = SeoDefaults::first();
                    
                    if ($seoDefaults) {
                        if (in_array($key, ['enable_structured_data', 'enable_robots_txt', 'enable_sitemap'])) {
                            return $seoDefaults->{$key} ? '1' : '0';
                        }
                        return $seoDefaults->{$key} ?? $default;
                    }
                    break;
                    
                // CurrencyConfig table fields
                case 'currency_symbol':
                case 'default_currency':
                case 'currency_position':
                case 'thousand_separator':
                case 'decimal_separator':
                case 'decimal_digits':
                    $currencyConfig = CurrencyConfig::first();
                    
                    if ($currencyConfig) {
                        if ($key === 'default_currency') {
                            return $currencyConfig->currency_code ?? $default;
                        }
                        
                        if ($key === 'decimal_digits') {
                            return $currencyConfig->decimal_places ?? $default;
                        }
                        
                        return $currencyConfig->{$key} ?? $default;
                    }
                    break;
                    
                // GeneralSetting table fields
                case 'site_name':
                case 'site_logo':
                case 'site_favicon':
                    $generalSetting = GeneralSetting::first();
                    
                    if ($generalSetting) {
                        return $generalSetting->{$key} ?? $default;
                    }
                    break;
            }
            
            // If no matching field was found or the model doesn't exist, return the default
            return $default;
        }
    }

    /**
     * Ensures that a SeoDefaults record exists with sensible defaults
     *
     * @return \App\Models\SeoDefaults
     */
    private function ensureSeoDefaultsExist()
    {
        $seoDefaults = SeoDefaults::first();
        
        if (!$seoDefaults) {
            $seoDefaults = new SeoDefaults();
            $seoDefaults->default_meta_title = 'Celestial Cosmetics - Beauty Products';
            $seoDefaults->default_meta_description = 'Discover high-quality beauty products from Celestial Cosmetics.';
            $seoDefaults->default_meta_keywords = 'cosmetics, beauty, skincare';
            $seoDefaults->og_site_name = 'Celestial Cosmetics';
            $seoDefaults->twitter_site = '@celestialcosm';
            $seoDefaults->twitter_creator = '@celestialcosm';
            $seoDefaults->default_robots_content = 'index,follow';
            $seoDefaults->enable_structured_data = true;
            $seoDefaults->enable_robots_txt = true;
            $seoDefaults->enable_sitemap = true;
            $seoDefaults->sitemap_change_frequency = 'weekly';
            $seoDefaults->save();
            
            // Log creation
            Activity::create([
                'description' => 'Created default SEO settings',
                'causer_type' => auth()->check() ? get_class(auth()->user()) : 'System',
                'causer_id' => auth()->check() ? auth()->id() : null,
                'subject_type' => SeoDefaults::class,
                'subject_id' => $seoDefaults->id,
                'status' => 'completed',
                'properties' => json_encode(['action' => 'created'])
            ]);
        }
        
        return $seoDefaults;
    }

    /**
     * Check content quality and provide suggestions
     */
    private function checkContentQuality($products, $categories)
    {
        $issues = [];
        $suggestions = [];
        
        // Content length thresholds
        $minProductLength = 200; // Minimum 200 characters for product descriptions
        $minCategoryLength = 150; // Minimum 150 characters for category descriptions
        
        // Check products content
        foreach ($products as $product) {
            $content = $product->description ? strip_tags($product->description) : '';
            $contentLength = strlen($content);
            
            if (empty($content)) {
                $issues[] = "Product #{$product->id} ({$product->name}) has no description content";
                $suggestions[] = "Add a detailed description for {$product->name} highlighting its features, benefits, and ingredients";
            } elseif ($contentLength < $minProductLength) {
                $issues[] = "Product #{$product->id} ({$product->name}) description is too short ({$contentLength} chars)";
                $suggestions[] = "Expand the description to at least {$minProductLength} characters. Include more details about features, usage, and benefits";
            }
            
            // Check for keyword stuffing (simplistic approach)
            if (!empty($content)) {
                $words = str_word_count(strtolower($content), 1);
                $productNameWords = str_word_count(strtolower($product->name), 1);
                
                $nameOccurrences = 0;
                foreach ($productNameWords as $nameWord) {
                    if (strlen($nameWord) > 3) { // Only count meaningful words
                        $nameOccurrences += array_count_values($words)[$nameWord] ?? 0;
                    }
                }
                
                $wordCount = count($words);
                if ($wordCount > 0) {
                    $density = ($nameOccurrences / $wordCount) * 100;
                    
                    if ($density > 5) {
                        $issues[] = "Product #{$product->id} ({$product->name}) has keyword stuffing (density: " . round($density, 1) . "%)";
                        $suggestions[] = "Reduce the number of times the product name appears. Aim for a keyword density below 5%";
                    }
                }
            }
            
            // Check for formatting
            if (!empty($product->description) && !preg_match('/<(h1|h2|h3|h4|h5|h6|ul|ol|li|p|br)[^>]*>/i', $product->description)) {
                $issues[] = "Product #{$product->id} ({$product->name}) description lacks proper HTML formatting";
                $suggestions[] = "Add headings, paragraphs, or bullet points to improve readability";
            }
        }
        
        // Check categories content
        foreach ($categories as $category) {
            $content = $category->description ? strip_tags($category->description) : '';
            $contentLength = strlen($content);
            
            if (empty($content)) {
                $issues[] = "Category #{$category->id} ({$category->name}) has no description content";
                $suggestions[] = "Add a detailed description for the {$category->name} category explaining what customers can find in this section";
            } elseif ($contentLength < $minCategoryLength) {
                $issues[] = "Category #{$category->id} ({$category->name}) description is too short ({$contentLength} chars)";
                $suggestions[] = "Expand the description to at least {$minCategoryLength} characters. Describe the types of products in this category and their benefits";
            }
            
            // Check for formatting
            if (!empty($category->description) && !preg_match('/<(h1|h2|h3|h4|h5|h6|ul|ol|li|p|br)[^>]*>/i', $category->description)) {
                $issues[] = "Category #{$category->id} ({$category->name}) description lacks proper HTML formatting";
                $suggestions[] = "Add headings, paragraphs, or bullet points to improve readability";
            }
            
            // Check for product count
            $productCount = \App\Models\Product::where('category_id', $category->id)->count();
            if ($productCount < 3) {
                $issues[] = "Category #{$category->id} ({$category->name}) has only {$productCount} products";
                $suggestions[] = "Add more products to the {$category->name} category or consider merging it with another category";
            }
        }
        
        return [
            'pass' => count($issues) === 0,
            'issues' => $issues,
            'suggestions' => $suggestions
        ];
    }

    /**
     * Check image optimization and provide suggestions
     */
    private function checkImageOptimization($products, $categories)
    {
        $issues = [];
        $suggestions = [];
        
        // Check product images
        foreach ($products as $product) {
            // Check if product has images
            if (!$product->image && (!$product->images || $product->images->isEmpty())) {
                $issues[] = "Product #{$product->id} ({$product->name}) has no images";
                $suggestions[] = "Add at least one high-quality image for {$product->name}";
                continue;
            }
            
            // Check primary image alt text
            $mainImage = $product->images && $product->images->isNotEmpty() 
                ? $product->images->firstWhere('is_primary', true) 
                : null;
                
            if ($mainImage && empty($mainImage->alt_text)) {
                $issues[] = "Product #{$product->id} ({$product->name}) main image missing alt text";
                $suggestions[] = "Add descriptive alt text for {$product->name} main image, e.g., \"{$product->name} - {$product->brand} cosmetic product\"";
            }
            
            // Check for multiple images
            if ($product->images && $product->images->count() < 2) {
                $issues[] = "Product #{$product->id} ({$product->name}) has only " . $product->images->count() . " image(s)";
                $suggestions[] = "Add multiple images for {$product->name} showing different angles and usage examples";
            }
            
            // Check image file sizes (if available)
            if ($product->images) {
                foreach ($product->images as $image) {
                    if (file_exists(public_path('storage/' . $image->path))) {
                        $fileSize = filesize(public_path('storage/' . $image->path)) / 1024; // KB
                        
                        if ($fileSize > 200) {
                            $issues[] = "Product #{$product->id} image is too large ({$fileSize} KB)";
                            $suggestions[] = "Optimize the image to reduce file size below 200KB while maintaining quality";
                        }
                    }
                }
            }
        }
        
        // Check category images
        foreach ($categories as $category) {
            if (empty($category->image)) {
                $issues[] = "Category #{$category->id} ({$category->name}) has no image";
                $suggestions[] = "Add a representative image for the {$category->name} category";
                continue;
            }
            
            // Check alt text (if the field exists)
            if (property_exists($category, 'image_alt') && empty($category->image_alt)) {
                $issues[] = "Category #{$category->id} ({$category->name}) image missing alt text";
                $suggestions[] = "Add descriptive alt text for {$category->name} category image, e.g., \"{$category->name} products collection\"";
            }
            
            // Check image file size
            if (file_exists(public_path('storage/' . $category->image))) {
                $fileSize = filesize(public_path('storage/' . $category->image)) / 1024; // KB
                
                if ($fileSize > 250) {
                    $issues[] = "Category #{$category->id} image is too large ({$fileSize} KB)";
                    $suggestions[] = "Optimize the category image to reduce file size below 250KB while maintaining quality";
                }
            }
        }
        
        return [
            'pass' => count($issues) === 0,
            'issues' => $issues,
            'suggestions' => $suggestions
        ];
    }

    /**
     * Check URL structure and provide suggestions
     */
    private function checkUrlStructure($products, $categories)
    {
        $issues = [];
        $suggestions = [];
        
        // Check product URLs
        foreach ($products as $product) {
            if (empty($product->slug)) {
                $issues[] = "Product #{$product->id} ({$product->name}) has no slug/URL";
                $suggestions[] = "Generate a SEO-friendly slug for {$product->name}";
                continue;
            }
            
            // Check slug length
            if (strlen($product->slug) > 50) {
                $issues[] = "Product #{$product->id} slug is too long (" . strlen($product->slug) . " chars)";
                $suggestions[] = "Shorten the slug to be more concise while maintaining keywords";
            }
            
            // Check if slug contains product name keywords
            $nameWords = str_word_count(strtolower($product->name), 1);
            $slugWords = explode('-', $product->slug);
            $foundNameKeywords = false;
            
            foreach ($nameWords as $word) {
                if (strlen($word) > 3 && in_array($word, $slugWords)) {
                    $foundNameKeywords = true;
                    break;
                }
            }
            
            if (!$foundNameKeywords) {
                $issues[] = "Product #{$product->id} slug doesn't contain product name keywords";
                $suggestions[] = "Update the slug to include main keywords from the product name";
            }
        }
        
        // Check category URLs
        foreach ($categories as $category) {
            if (empty($category->slug)) {
                $issues[] = "Category #{$category->id} ({$category->name}) has no slug/URL";
                $suggestions[] = "Generate a SEO-friendly slug for {$category->name} category";
                continue;
            }
            
            // Check if slug matches category name
            $expectedSlug = strtolower(str_replace(' ', '-', $category->name));
            if ($category->slug !== $expectedSlug) {
                $issues[] = "Category #{$category->id} slug doesn't match category name";
                $suggestions[] = "Consider updating the slug to '{$expectedSlug}' to match the category name";
            }
        }
        
        return [
            'pass' => count($issues) === 0,
            'issues' => $issues,
            'suggestions' => $suggestions
        ];
    }

    /**
     * Generate optimized SEO metadata for a product
     *
     * @param  \App\Models\Product  $product
     * @return array
     */
    public function generateProductSeoMetadata(Product $product)
    {
        // Prepare basic product information
        $name = $product->name;
        $description = $product->description ? strip_tags($product->description) : '';
        $category = $product->category ? $product->category->name : '';
        $brand = $product->brand ?? '';
        $price = $product->price;
        $siteName = $this->getSetting('site_name', 'Celestial Cosmetics');
        
        // Generate meta title (max 60 characters)
        $metaTitle = $name;
        
        if (!empty($brand) && strlen($metaTitle . ' - ' . $brand) <= 55) {
            $metaTitle .= ' - ' . $brand;
        }
        
        if (!empty($category) && strlen($metaTitle . ' | ' . $category) <= 55) {
            $metaTitle .= ' | ' . $category;
        }
        
        if (strlen($metaTitle . ' | ' . $siteName) <= 60) {
            $metaTitle .= ' | ' . $siteName;
        }
        
        // Generate meta description (max 160 characters)
        $metaDescription = '';
        
        if (!empty($description)) {
            // Get first 120 characters of description
            $descriptionExcerpt = substr($description, 0, 120);
            $lastSpace = strrpos($descriptionExcerpt, ' ');
            if ($lastSpace !== false) {
                $descriptionExcerpt = substr($descriptionExcerpt, 0, $lastSpace);
            }
            
            $metaDescription = $descriptionExcerpt;
            
            // Add price if there's room
            if (strlen($metaDescription . '. Buy now for ' . $this->formatPrice($price)) <= 155) {
                $metaDescription .= '. Buy now for ' . $this->formatPrice($price);
            }
        } else {
            // Create a generic description
            $metaDescription = "Discover our {$name}";
            
            if (!empty($brand)) {
                $metaDescription .= " from {$brand}";
            }
            
            if (!empty($category)) {
                $metaDescription .= " in our {$category} collection";
            }
            
            $metaDescription .= ". High-quality beauty products from {$siteName}.";
        }
        
        // Generate meta keywords
        $keywordParts = array_filter([$name, $brand, $category]);
        $additionalKeywords = ['beauty', 'cosmetics', 'skincare'];
        
        // Add some product-specific keywords based on description
        if (!empty($description)) {
            $commonWords = ['and', 'the', 'for', 'with', 'without', 'that', 'this', 'these', 'those', 'from', 'have', 'has'];
            $words = str_word_count(strtolower($description), 1);
            $filteredWords = array_filter($words, function($word) use ($commonWords) {
                return strlen($word) > 4 && !in_array($word, $commonWords);
            });
            
            $wordFrequency = array_count_values($filteredWords);
            arsort($wordFrequency);
            
            // Take top 5 most frequent meaningful words
            $topKeywords = array_slice(array_keys($wordFrequency), 0, 5);
            
            $additionalKeywords = array_merge($additionalKeywords, $topKeywords);
        }
        
        $metaKeywords = implode(', ', array_merge($keywordParts, $additionalKeywords));
        
        // Format JSON-LD structured data
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $name,
            'description' => $description,
            'sku' => $product->sku ?? '',
            'brand' => [
                '@type' => 'Brand',
                'name' => $brand ?: $siteName
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => $price,
                'priceCurrency' => $this->getSetting('default_currency', 'USD'),
                'availability' => $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'url' => route('products.show', $product->slug)
            ]
        ];
        
        // Add image if available
        if ($product->image) {
            $structuredData['image'] = asset('storage/' . $product->image);
        } elseif ($product->images && $product->images->isNotEmpty()) {
            $structuredData['image'] = asset('storage/' . $product->images->first()->path);
        }
        
        return [
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'meta_keywords' => $metaKeywords,
            'structured_data' => json_encode($structuredData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        ];
    }

    /**
     * Generate optimized SEO metadata for a category
     *
     * @param  \App\Models\Category  $category
     * @return array
     */
    public function generateCategorySeoMetadata(Category $category)
    {
        // Prepare basic category information
        $name = $category->name;
        $description = $category->description ? strip_tags($category->description) : '';
        $siteName = $this->getSetting('site_name', 'Celestial Cosmetics');
        $productCount = Product::where('category_id', $category->id)->count();
        
        // Generate meta title (max 60 characters)
        $metaTitle = "{$name} Products";
        
        if (strlen($metaTitle . ' | ' . $siteName) <= 60) {
            $metaTitle .= ' | ' . $siteName;
        }
        
        // Generate meta description (max 160 characters)
        if (!empty($description)) {
            // Get first 120 characters of description
            $descriptionExcerpt = substr($description, 0, 120);
            $lastSpace = strrpos($descriptionExcerpt, ' ');
            if ($lastSpace !== false) {
                $descriptionExcerpt = substr($descriptionExcerpt, 0, $lastSpace);
            }
            
            $metaDescription = $descriptionExcerpt;
            
            // Add product count if there's room
            if ($productCount > 0 && strlen($metaDescription . ". Shop our {$productCount} products now.") <= 160) {
                $metaDescription .= ". Shop our {$productCount} products now.";
            }
        } else {
            // Create a generic description
            $metaDescription = "Explore our {$name} collection";
            
            if ($productCount > 0) {
                $metaDescription .= " featuring {$productCount} premium products";
            }
            
            $metaDescription .= ". High-quality beauty products from {$siteName}. Shop now for the best in beauty.";
        }
        
        // Generate meta keywords
        $keywordParts = [$name, $name . ' products', $name . ' collection'];
        $additionalKeywords = ['beauty', 'cosmetics', 'skincare', 'collection'];
        
        // Add some common products in this category
        $topProducts = Product::where('category_id', $category->id)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->pluck('name')
            ->toArray();
            
        if (!empty($topProducts)) {
            $additionalKeywords = array_merge($additionalKeywords, $topProducts);
        }
        
        $metaKeywords = implode(', ', array_merge($keywordParts, $additionalKeywords));
        
        // Format JSON-LD structured data
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => "{$name} Products Collection",
            'description' => $description ?: "Explore our {$name} collection from {$siteName}."
        ];
        
        // Add image if available
        if ($category->image) {
            $structuredData['image'] = asset('storage/' . $category->image);
        }
        
        return [
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'meta_keywords' => $metaKeywords,
            'structured_data' => json_encode($structuredData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        ];
    }

    /**
     * Format price with currency
     *
     * @param float $price
     * @return string
     */
    private function formatPrice($price)
    {
        $currencySymbol = $this->getSetting('currency_symbol', '$');
        $currencyPosition = $this->getSetting('currency_position', 'left');
        $decimalDigits = (int)$this->getSetting('decimal_digits', 2);
        
        $formattedPrice = number_format($price, $decimalDigits);
        
        if ($currencyPosition === 'left') {
            return $currencySymbol . $formattedPrice;
        } else {
            return $formattedPrice . $currencySymbol;
        }
    }

    /**
     * Display SEO suggestion form for products
     *
     * @return \Illuminate\Http\Response
     */
    public function seoSuggestionTool()
    {
        $products = Product::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        
        return view('admin.seo.suggestion-tool', compact('products', 'categories'));
    }

    /**
     * Generate SEO suggestions for a specific product
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateSeoSuggestions(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:product,category',
            'id' => 'required|integer'
        ]);
        
        if ($validated['type'] === 'product') {
            $product = Product::findOrFail($validated['id']);
            $metadata = $this->generateProductSeoMetadata($product);
            
            return response()->json([
                'status' => 'success',
                'item' => $product,
                'metadata' => $metadata
            ]);
        } else {
            $category = Category::findOrFail($validated['id']);
            $metadata = $this->generateCategorySeoMetadata($category);
            
            return response()->json([
                'status' => 'success',
                'item' => $category,
                'metadata' => $metadata
            ]);
        }
    }

    /**
     * Generate SEO suggestions for a product or category.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateSuggestions(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:product,category',
            'id' => 'required|integer',
        ]);

        try {
            if ($validated['type'] === 'product') {
                $item = Product::findOrFail($validated['id']);
                
                // Generate suggestions based on product data
                $metadata = [
                    'meta_title' => $this->generateProductTitle($item),
                    'meta_description' => $this->generateProductDescription($item),
                    'meta_keywords' => $this->generateProductKeywords($item),
                    'structured_data' => $this->generateProductStructuredData($item),
                ];
            } else {
                $item = Category::findOrFail($validated['id']);
                
                // Generate suggestions based on category data
                $metadata = [
                    'meta_title' => $this->generateCategoryTitle($item),
                    'meta_description' => $this->generateCategoryDescription($item),
                    'meta_keywords' => $this->generateCategoryKeywords($item),
                    'structured_data' => $this->generateCategoryStructuredData($item),
                ];
            }

            return response()->json([
                'status' => 'success',
                'item' => $item,
                'metadata' => $metadata,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Apply SEO suggestions to a single item.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function applySuggestions(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:product,category',
                'id' => 'required|integer',
                'meta_title' => 'required|string|max:255',
                'meta_description' => 'required|string',
                'meta_keywords' => 'nullable|string',
                'structured_data' => 'nullable|string',
            ]);

            if ($validated['type'] === 'product') {
                $item = Product::findOrFail($validated['id']);
            } else {
                $item = Category::findOrFail($validated['id']);
            }

            // Update the item's SEO metadata
            $item->meta_title = $validated['meta_title'];
            $item->meta_description = $validated['meta_description'];
            $item->meta_keywords = $validated['meta_keywords'];
            
            // Handle structured data if your model supports it
            if (isset($validated['structured_data']) && property_exists($item, 'structured_data')) {
                $item->structured_data = $validated['structured_data'];
            }
            
            $item->save();

            return response()->json([
                'status' => 'success',
                'message' => 'SEO suggestions applied successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Apply SEO suggestions to multiple items.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyBulkSuggestions(Request $request)
    {
        try {
            $validated = $request->validate([
                'suggestions' => 'required|array',
                'suggestions.*.id' => 'required|integer',
                'suggestions.*.type' => 'required|in:product,category',
                'suggestions.*.metadata.meta_title' => 'required|string|max:255',
                'suggestions.*.metadata.meta_description' => 'required|string',
                'suggestions.*.metadata.meta_keywords' => 'nullable|string',
                'suggestions.*.metadata.structured_data' => 'nullable|string',
            ]);

            $appliedCount = 0;

            foreach ($validated['suggestions'] as $suggestion) {
                if ($suggestion['type'] === 'product') {
                    $item = Product::find($suggestion['id']);
                } else {
                    $item = Category::find($suggestion['id']);
                }

                if ($item) {
                    // Update the item's SEO metadata
                    $item->meta_title = $suggestion['metadata']['meta_title'];
                    $item->meta_description = $suggestion['metadata']['meta_description'];
                    $item->meta_keywords = $suggestion['metadata']['meta_keywords'] ?? null;
                    
                    // Handle structured data if your model supports it
                    if (isset($suggestion['metadata']['structured_data']) && property_exists($item, 'structured_data')) {
                        $item->structured_data = $suggestion['metadata']['structured_data'];
                    }
                    
                    $item->save();
                    $appliedCount++;
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "Applied SEO suggestions to {$appliedCount} items",
                'applied_count' => $appliedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate meta title for a product.
     *
     * @param \App\Models\Product $product
     * @return string
     */
    private function generateProductTitle($product)
    {
        // Example title generation logic
        $title = $product->name;
        
        if (!empty($product->brand)) {
            $title .= ' | ' . $product->brand;
        }
        
        if (isset($product->category) && !empty($product->category->name)) {
            $title .= ' | ' . $product->category->name;
        }
        
        $title .= ' | ' . config('app.name');
        
        return substr($title, 0, 60);
    }

    /**
     * Generate meta description for a product.
     *
     * @param \App\Models\Product $product
     * @return string
     */
    private function generateProductDescription($product)
    {
        $description = "Discover " . $product->name;
        
        if (!empty($product->brand)) {
            $description .= " by " . $product->brand;
        }
        
        if (!empty($product->short_description)) {
            // Use the short description if available
            $description .= ". " . $product->short_description;
        } else if (!empty($product->description)) {
            // Use a truncated version of the full description
            $plainDescription = strip_tags($product->description);
            $truncated = substr($plainDescription, 0, 100);
            $description .= ". " . $truncated . (strlen($plainDescription) > 100 ? '...' : '');
        }
        
        // Add a call to action
        $description .= " Shop now at " . config('app.name') . ".";
        
        return substr($description, 0, 160);
    }

    /**
     * Generate meta keywords for a product.
     *
     * @param \App\Models\Product $product
     * @return string
     */
    private function generateProductKeywords($product)
    {
        $keywords = [$product->name];
        
        if (!empty($product->brand)) {
            $keywords[] = $product->brand;
        }
        
        if (isset($product->category) && !empty($product->category->name)) {
            $keywords[] = $product->category->name;
        }
        
        // Add product type and other attributes that might be available
        if (!empty($product->product_type)) {
            $keywords[] = $product->product_type;
        }
        
        // Add more keywords based on the product's attributes
        if (isset($product->tags) && is_array($product->tags)) {
            $keywords = array_merge($keywords, $product->tags);
        }
        
        return implode(', ', array_unique($keywords));
    }

    /**
     * Generate structured data for a product.
     *
     * @param \App\Models\Product $product
     * @return string
     */
    private function generateProductStructuredData($product)
    {
        $data = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => !empty($product->short_description) ? $product->short_description : strip_tags(substr($product->description ?? '', 0, 200)),
        ];
        
        // Add product image
        if (!empty($product->image)) {
            $data['image'] = url($product->image);
        }
        
        // Add product brand
        if (!empty($product->brand)) {
            $data['brand'] = [
                '@type' => 'Brand',
                'name' => $product->brand
            ];
        }
        
        // Add product SKU
        if (!empty($product->sku)) {
            $data['sku'] = $product->sku;
        }
        
        // Add product price
        if (isset($product->price)) {
            $data['offers'] = [
                '@type' => 'Offer',
                'url' => route('products.show', $product->slug ?? $product->id),
                'price' => $product->price,
                'priceCurrency' => 'USD', // Adjust based on your store's currency
                'availability' => 'https://schema.org/InStock',
            ];
        }
        
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * Generate meta title for a category.
     *
     * @param \App\Models\Category $category
     * @return string
     */
    private function generateCategoryTitle($category)
    {
        $title = $category->name;
        
        // Add product count if available
        if (isset($category->products_count) && $category->products_count > 0) {
            $title .= " - " . $category->products_count . " Products";
        }
        
        $title .= " | " . config('app.name');
        
        return substr($title, 0, 60);
    }

    /**
     * Generate meta description for a category.
     *
     * @param \App\Models\Category $category
     * @return string
     */
    private function generateCategoryDescription($category)
    {
        $description = "Explore our collection of " . $category->name;
        
        if (!empty($category->description)) {
            // Use a truncated version of the description
            $plainDescription = strip_tags($category->description);
            $truncated = substr($plainDescription, 0, 100);
            $description .= ". " . $truncated . (strlen($plainDescription) > 100 ? '...' : '');
        }
        
        // Add product count if available
        if (isset($category->products_count) && $category->products_count > 0) {
            $description .= " Browse " . $category->products_count . " products in our " . $category->name . " collection.";
        }
        
        // Add a call to action
        $description .= " Shop online at " . config('app.name') . ".";
        
        return substr($description, 0, 160);
    }

    /**
     * Generate meta keywords for a category.
     *
     * @param \App\Models\Category $category
     * @return string
     */
    private function generateCategoryKeywords($category)
    {
        $keywords = [$category->name];
        
        // Add parent category if available
        if (isset($category->parent) && !empty($category->parent->name)) {
            $keywords[] = $category->parent->name;
        }
        
        // Add common terms related to the category
        $keywords[] = $category->name . ' products';
        $keywords[] = $category->name . ' collection';
        $keywords[] = 'buy ' . $category->name;
        $keywords[] = $category->name . ' ' . config('app.name');
        
        return implode(', ', array_unique($keywords));
    }

    /**
     * Generate structured data for a category.
     *
     * @param \App\Models\Category $category
     * @return string
     */
    private function generateCategoryStructuredData($category)
    {
        $data = [
            '@context' => 'https://schema.org/',
            '@type' => 'CollectionPage',
            'name' => $category->name,
            'description' => !empty($category->description) ? strip_tags($category->description) : $category->name . ' collection at ' . config('app.name'),
        ];
        
        // Add category image
        if (!empty($category->image)) {
            $data['image'] = url($category->image);
        }
        
        // Add breadcrumb
        $breadcrumbItems = [];
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => url('/')
        ];
        
        if (isset($category->parent) && !empty($category->parent->name)) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $category->parent->name,
                'item' => route('admin.categories.show', $category->parent->slug ?? $category->parent->id)
            ];
            
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $category->name,
                'item' => route('admin.categories.show', $category->slug ?? $category->id)
            ];
        } else {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $category->name,
                'item' => route('admin.categories.show', $category->slug ?? $category->id)
            ];
        }
        
        $data['breadcrumb'] = [
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbItems
        ];
        
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * Display the SEO suggestion tool.
     *
     * @return \Illuminate\Http\Response
     */
    public function suggestionTool()
    {
        $products = Product::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        
        return view('admin.seo.suggestion-tool', compact('products', 'categories'));
    }
} 