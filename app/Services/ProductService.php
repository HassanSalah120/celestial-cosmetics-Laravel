<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Facades\Settings;
use App\Helpers\SettingsHelper;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductService
{
    /**
     * Get data for the products index page.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function getProductsPageData(Request $request): array
    {
        $locale = session('locale', app()->getLocale());
        $isArabic = $locale === 'ar';
        
        // Get sort parameters
        $sort = $request->input('sort', 'featured');
        $categorySlugs = $request->input('categories', []);
        
        // Start with a base query
        $query = Product::query()->with(['category', 'images'])->where('is_visible', true);
        
        // Apply any category filters
        if (!empty($categorySlugs)) {
            $query->whereHas('category', function($query) use ($categorySlugs) {
                $query->whereIn('slug', (array) $categorySlugs);
            });
        }
        
        // Apply sorting
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'featured':
            default:
                $query->where('is_featured', true)->orderBy('created_at', 'desc');
                break;
        }
        
        // Paginate results
        $products = $query->paginate(12)->appends($request->query());
        
        // Get product categories for the filter sidebar
        $categories = Category::all()
            ->map(function($category) use ($isArabic) {
                // Apply Arabic name if in Arabic mode and has Arabic translation
                if ($isArabic && !empty($category->name_ar)) {
                    $category->name = $category->name_ar;
                }
                return $category;
            });
        
        // Get products count by category
        $productCounts = DB::table('products')
            ->select('category_id', DB::raw('count(*) as total'))
            ->where('is_visible', true)
            ->groupBy('category_id')
            ->pluck('total', 'category_id')
            ->toArray();
        
        // Set the page title and meta data
        $title = Settings::get($isArabic ? 'products_meta_title_ar' : 'products_meta_title', __('Products'));
        $description = Settings::get($isArabic ? 'products_meta_description_ar' : 'products_meta_description');
        $keywords = Settings::get($isArabic ? 'products_meta_keywords_ar' : 'products_meta_keywords');
        
        return compact(
            'products', 
            'categories', 
            'productCounts', 
            'sort', 
            'categorySlugs',
            'title',
            'description',
            'keywords'
        );
    }

    /**
     * Get data for a product category page.
     *
     * @param string $slug
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \Exception
     */
    public function getCategoryPageData(string $slug, Request $request): array
    {
        // Validate the incoming slug
        if (empty($slug) || !is_string($slug)) {
            throw new \InvalidArgumentException('Invalid category slug provided');
        }

        $category = Category::where('slug', $slug)->firstOrFail();
        
        // Ensure category exists and has valid ID
        if (!$category || !is_numeric($category->id)) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Category with slug '{$slug}' not found or has invalid ID");
        }
        
        // Get products with explicit error handling
        $products = null; // Initialize products
        $productsTotalString = '0'; // Initialize total string
        try {
            $query = Product::where('category_id', $category->id)
                ->where('is_visible', true);
            
            // Get sort parameter
            $sort = $request->input('sort', 'newest');
            
            // Apply sorting
            switch ($sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
            
            // Get total count BEFORE pagination
            $rawTotal = $query->count();
            $productsTotalString = (string)(int)$rawTotal; // Ensure it's an integer string
            
            $products = $query->paginate(12)->appends($request->query());

        } catch (\Exception $e) {
            Log::error('Product pagination error: ' . $e->getMessage());
            // Create empty paginator as fallback
            $products = new LengthAwarePaginator(
                collect(), // Empty collection
                0, // Total items
                12, // Items per page
                1, // Current page
                ['path' => request()->url()] // Page path
            );
            $productsTotalString = '0'; // Fallback total
        }
        
        // Set meta tags for category page using category SEO fields with fallback to settings
        $metaTitle = $category->meta_title ?? 
                 $category->name . ' | ' . (SettingsHelper::get('default_meta_title') ?: config('app.name'));
        $metaDescription = $category->meta_description ?? 
                   SettingsHelper::get('default_meta_description');
        $metaKeywords = $category->meta_keywords ?? 
                SettingsHelper::get('default_meta_keywords');
        
        // Make sure to pass a currency symbol to the view
        $currencySymbol = SettingsHelper::get('currency_symbol', '$');
        
        // Add related categories to the view data
        $relatedCategories = Category::where('id', '!=', $category->id)
            ->inRandomOrder()
            ->take(4)
            ->get();
        
        // Clean the category object to ensure no arrays are passed where strings are expected
        foreach (['name', 'description', 'meta_title', 'meta_description', 'meta_keywords'] as $field) {
            if (isset($category->$field) && is_array($category->$field)) {
                $category->$field = json_encode($category->$field);
            }
        }
            
        return compact(
            'category', 
            'products', 
            'metaTitle', 
            'metaDescription', 
            'metaKeywords', 
            'currencySymbol',
            'relatedCategories',
            'productsTotalString'
        );
    }

    /**
     * Get fallback data for category page when an error occurs.
     *
     * @return array
     */
    public function getEmptyCategoryPageData(): array
    {
        // Return empty pagination object as fallback
        $products = new LengthAwarePaginator(
            collect(), // Empty collection
            0, // Total items
            12, // Items per page
            1, // Current page
            ['path' => request()->url()] // Page path
        );
        $productsTotalString = '0'; // Fallback total for error case
        
        $category = new Category();
        $category->name = 'Category Not Found';
        
        // Empty related categories collection
        $relatedCategories = collect();
        
        return [
            'category' => $category,
            'products' => $products,
            'metaTitle' => 'Category Not Found', 
            'metaDescription' => 'The requested category could not be found',
            'metaKeywords' => 'error, not found',
            'currencySymbol' => SettingsHelper::get('currency_symbol', '$'),
            'relatedCategories' => $relatedCategories,
            'productsTotalString' => $productsTotalString
        ];
    }

    /**
     * Get data for a product detail page.
     *
     * @param string $slug
     * @return array
     */
    public function getProductPageData(string $slug): array
    {
        $product = Product::where('slug', $slug)
            ->with(['category', 'images'])
            ->firstOrFail();
            
        // Get related products from the same category, excluding the current product
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_visible', true)
            ->take(4)
            ->get();
            
        // Set meta tags using product SEO fields with fallback to settings
        $title = $product->meta_title ?? 
                 $product->name . ' | ' . SettingsHelper::get('default_meta_title');
        $description = $product->meta_description ?? 
                       SettingsHelper::get('default_meta_description');
        $keywords = $product->meta_keywords ?? 
                    SettingsHelper::get('default_meta_keywords');
            
        return compact('product', 'relatedProducts', 'title', 'description', 'keywords');
    }

    /**
     * Get data for product search results.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function getSearchResultsData(Request $request): array
    {
        $query = $request->input('query');
        
        $products = Product::where('is_visible', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->with('category')
            ->paginate(12);
            
        $categories = Category::all();
        
        // Set meta tags for search results using settings
        $title = SettingsHelper::get('search_meta_title') ?? 
                 'Search Results | ' . SettingsHelper::get('default_meta_title');
        $description = SettingsHelper::get('search_meta_description') ?? 
                      SettingsHelper::get('default_meta_description');
        $keywords = SettingsHelper::get('search_meta_keywords') ?? 
                   SettingsHelper::get('default_meta_keywords');
        
        return compact('products', 'categories', 'query', 'title', 'description', 'keywords');
    }

    /**
     * Get product autocomplete search results.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function getAutocompleteResults(Request $request): array
    {
        $query = $request->input('query');
        
        if (empty($query)) {
            return [];
        }
        
        return Product::where('name', 'like', "%{$query}%")
            ->where('is_visible', true)
            ->take(5)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'url' => route('products.show', $product->slug),
                    'image' => $product->image ? asset('storage/' . $product->image) : asset('images/products/placeholder.jpg'),
                    'price' => SettingsHelper::formatPrice($product->price),
                    'category' => $product->category ? $product->category->name : null
                ];
            })->toArray();
    }

    /**
     * Get debugging data for a category page.
     *
     * @param string $slug
     * @return array
     */
    public function getCategoryDebugData(string $slug): array
    {
        try {
            // Validate the incoming slug
            if (empty($slug) || !is_string($slug)) {
                throw new \InvalidArgumentException('Invalid category slug provided');
            }

            $category = Category::where('slug', $slug)->firstOrFail();
            
            // Ensure category exists and has valid ID
            if (!$category || !is_numeric($category->id)) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Category with slug '{$slug}' not found or has invalid ID");
            }
            
            // Get products
            $products = Product::where('category_id', $category->id)
                ->where('is_visible', true)
                ->orderBy('created_at', 'desc')
                ->paginate(12);
                
            // Make sure to pass a currency symbol to the view
            $currencySymbol = SettingsHelper::get('currency_symbol', '$');
            
            return [
                'category' => $category,
                'products' => $products,
                'currencySymbol' => $currencySymbol
            ];
        } catch (\Exception $e) {
            // Log the error
            Log::error('Category debug error: ' . $e->getMessage(), [
                'slug' => $slug,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'category' => new Category(),
                'products' => collect(),
                'currencySymbol' => '$',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get data for the categories index page.
     *
     * @return array
     */
    public function getAllCategoriesData(): array
    {
        $locale = session('locale', app()->getLocale());
        $isArabic = $locale === 'ar';
        
        // Get all categories
        $categories = Category::withCount('products')
            ->orderBy('name')
            ->get()
            ->map(function($category) use ($isArabic) {
                // Apply Arabic name if in Arabic mode and has Arabic translation
                if ($isArabic && !empty($category->name_ar)) {
                    $category->name = $category->name_ar;
                }
                
                // Add image URL
                $category->image_url = $category->image 
                    ? asset('storage/' . $category->image) 
                    : asset('images/products/placeholder.jpg');
                
                return $category;
            });
        
        // Set the page title and meta data
        $title = Settings::get($isArabic ? 'categories_meta_title_ar' : 'categories_meta_title', __('Categories'));
        $description = Settings::get($isArabic ? 'categories_meta_description_ar' : 'categories_meta_description');
        $keywords = Settings::get($isArabic ? 'categories_meta_keywords_ar' : 'categories_meta_keywords');
        
        return compact(
            'categories',
            'title',
            'description',
            'keywords'
        );
    }
} 