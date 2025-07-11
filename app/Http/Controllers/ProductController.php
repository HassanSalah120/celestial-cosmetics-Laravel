<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * The product service instance.
     *
     * @var \App\Services\ProductService
     */
    protected $productService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\ProductService $productService
     * @return void
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a listing of the products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data = $this->productService->getProductsPageData($request);
        return view('products.index', $data);
    }
    
    /**
     * Display products by category.
     *
     * @param  string  $slug
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function category($slug, Request $request)
    {
        try {
            $data = $this->productService->getCategoryPageData($slug, $request);
            return view('products.category', $data);
        } catch (\Exception $e) {
            // Log the error
            \Illuminate\Support\Facades\Log::error('Category page error: ' . $e->getMessage(), [
                'slug' => $slug,
                'trace' => $e->getTraceAsString()
            ]);
            
            $data = $this->productService->getEmptyCategoryPageData();
            return view('products.category', $data);
        }
    }

    /**
     * Display the specified product.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        $data = $this->productService->getProductPageData($slug);
        return view('products.show', $data);
    }

    /**
     * Search for products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        $data = $this->productService->getSearchResultsData($request);
        return view('products.search', $data);
    }
    
    /**
     * Autocomplete product search.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function autocomplete(Request $request)
    {
        $results = $this->productService->getAutocompleteResults($request);
        return response()->json($results);
    }
    
    /**
     * Display debug information for a category.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function categoryDebug($slug)
    {
        $data = $this->productService->getCategoryDebugData($slug);
        return view('products.category-debug', $data);
    }
    
    /**
     * Display all product categories.
     *
     * @return \Illuminate\View\View
     */
    public function categories()
    {
        $data = $this->productService->getAllCategoriesData();
        return view('categories.index', $data);
    }
}
