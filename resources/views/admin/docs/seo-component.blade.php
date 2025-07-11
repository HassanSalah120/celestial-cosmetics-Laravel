@extends('layouts.admin')

@section('content')
<div class="py-6" x-data="{ tab: 'overview' }">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <div class="mb-5 flex items-center text-sm text-gray-500">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
            <svg class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <a href="{{ route('admin.seo.index') }}" class="hover:text-primary">SEO Management</a>
            <svg class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span>SEO Component Documentation</span>
        </div>

        <!-- Page Header -->
        <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6 mb-6 rounded-t-lg shadow-sm">
            <h1 class="text-2xl font-semibold text-gray-900">SEO Component Documentation</h1>
            <p class="mt-1 text-sm text-gray-600">
                Learn how to use the SEO component to optimize your pages for search engines
            </p>
        </div>
        
        <div class="mt-6 bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Overview</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        The SEO component provides a reusable way to add SEO meta tags to any page in your application.
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="prose max-w-none">
                        <p>The SEO component centralizes all meta tag management in one place, making it easier to maintain and update SEO settings across your site.</p>
                        
                        <h3>Benefits</h3>
                        <ul>
                            <li>Consistent SEO structure across all pages</li>
                            <li>DRY (Don't Repeat Yourself) approach</li>
                            <li>Easier updates to SEO strategy</li>
                            <li>Default fallbacks for missing data</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6 bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Basic Usage</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        How to use the SEO component in your blade templates.
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="prose max-w-none">
                        <p>To use the SEO component, add it to your view's <code>meta_tags</code> section:</p>
                        
                        <pre class="bg-gray-50 p-4 rounded-md text-sm overflow-x-auto code-snippet"><code>@extends('layouts.app')

@section('meta_tags')
    &lt;x-seo 
        :title="$page->title" 
        :description="$page->description"
        :keywords="$page->keywords"
        :ogImage="$page->image"
    /&gt;
@endsection</code></pre>

                        <p>You can also pass the component parameters directly from your controller:</p>
                        
                        <pre class="bg-gray-50 p-4 rounded-md text-sm overflow-x-auto code-snippet"><code>public function show(Product $product)
{
    return view('products.show', [
        'product' => $product,
        'title' => $product->meta_title ?? $product->name,
        'description' => $product->meta_description,
        // ...
    ]);
}</code></pre>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6 bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Available Parameters</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        All the parameters you can pass to the SEO component.
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="overflow-x-auto shadow rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parameter</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Default</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">title</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">The page title</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">Settings::get('default_meta_title')</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">description</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">The page description</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">Settings::get('default_meta_description')</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">keywords</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">The page keywords</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">Settings::get('default_meta_keywords')</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">ogImage</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">The Open Graph image path</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">Settings::get('og_default_image')</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">type</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">The Open Graph type</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">'website'</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">canonical</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">The canonical URL</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">url()->current()</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">robots</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">The robots meta content</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">Settings::get('default_robots_content')</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6 bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Examples</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Common usage examples for different page types.
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="prose max-w-none">
                        <h4 class="text-primary">Product Page</h4>
                        <pre class="bg-gray-50 p-4 rounded-md text-sm overflow-x-auto code-snippet"><code>&lt;x-seo 
    :title="$product->meta_title ?? $product->name . ' | ' . config('app.name')"
    :description="$product->meta_description ?? Str::limit(strip_tags($product->description), 160)"
    :keywords="$product->meta_keywords ?? $product->category->name . ', ' . $product->name"
    :ogImage="$product->featured_image"
    type="product" 
/&gt;</code></pre>

                        <h4 class="text-primary">Category Page</h4>
                        <pre class="bg-gray-50 p-4 rounded-md text-sm overflow-x-auto code-snippet"><code>&lt;x-seo 
    :title="$category->meta_title ?? $category->name . ' | ' . config('app.name')"
    :description="$category->meta_description ?? Str::limit($category->description, 160)"
    :keywords="$category->meta_keywords ?? $category->name"
    :ogImage="$category->image"
/&gt;</code></pre>

                        <h4 class="text-primary">Blog Post</h4>
                        <pre class="bg-gray-50 p-4 rounded-md text-sm overflow-x-auto code-snippet"><code>&lt;x-seo 
    :title="$post->title . ' | Blog | ' . config('app.name')"
    :description="$post->excerpt ?? Str::limit(strip_tags($post->content), 160)"
    :keywords="$post->keywords ?? implode(', ', $post->tags->pluck('name')->toArray())"
    :ogImage="$post->featured_image"
    type="article"
/&gt;</code></pre>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Output Preview section -->
        <div class="mt-6 bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Output Preview</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Example of the HTML generated by the SEO component
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Generated HTML</h4>
                        <div class="code-snippet p-4 rounded border border-gray-300 text-xs font-mono overflow-x-auto">
                            <pre>&lt;title&gt;Product Name | Celestial Cosmetics&lt;/title&gt;
&lt;meta name="description" content="This luxurious celestial-inspired product will transform your beauty routine..."&gt;
&lt;meta name="keywords" content="skincare, moisturizer, celestial cosmetics"&gt;

&lt;!-- Open Graph Tags --&gt;
&lt;meta property="og:title" content="Product Name | Celestial Cosmetics"&gt;
&lt;meta property="og:description" content="This luxurious celestial-inspired product will transform your beauty routine..."&gt;
&lt;meta property="og:image" content="https://example.com/images/product.jpg"&gt;
&lt;meta property="og:url" content="https://example.com/products/product-name"&gt;
&lt;meta property="og:type" content="product"&gt;

&lt;!-- Twitter Card Tags --&gt;
&lt;meta name="twitter:card" content="summary_large_image"&gt;
&lt;meta name="twitter:title" content="Product Name | Celestial Cosmetics"&gt;
&lt;meta name="twitter:description" content="This luxurious celestial-inspired product will transform your beauty routine..."&gt;
&lt;meta name="twitter:image" content="https://example.com/images/product.jpg"&gt;

&lt;!-- Canonical URL --&gt;
&lt;link rel="canonical" href="https://example.com/products/product-name"&gt;

&lt;!-- Robots Meta Tag --&gt;
&lt;meta name="robots" content="index, follow"&gt;</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .prose code {
        background-color: rgba(0, 0, 0, 0.05);
        padding: 0.2em 0.4em;
        border-radius: 0.3em;
        font-size: 0.9em;
    }
    .prose pre code {
        background-color: transparent;
        padding: 0;
        border-radius: 0;
        font-size: inherit;
    }
    .prose h4 {
        margin-top: 1.5em;
        margin-bottom: 0.5em;
    }
    .prose ul {
        list-style-type: disc;
        padding-left: 1.5em;
    }
    .prose ul li {
        margin-top: 0.25em;
        margin-bottom: 0.25em;
    }
    .code-snippet {
        background-color: #1e1e1e !important;
        color: #e6e6e6 !important;
        padding: 1rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        font-family: 'Courier New', monospace;
        margin: 1rem 0;
        line-height: 1.5;
    }
    
    .code-snippet .tag {
        color: #569cd6;
    }
    
    .code-snippet .attribute {
        color: #9cdcfe;
    }
    
    .code-snippet .value {
        color: #ce9178;
    }
    
    .code-snippet .comment {
        color: #6a9955;
    }
    
    .code-snippet .language-php {
        color: #dcdcaa;
    }
    
    /* Override only for code blocks, not all bg-gray-50 elements */
    pre.bg-gray-50 {
        background-color: #1e1e1e !important;
        color: #e6e6e6 !important;
    }
</style>
@endpush
@endsection 