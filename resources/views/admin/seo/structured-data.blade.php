@extends('layouts.admin')

@section('title', $pageTitle)

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-semibold text-gray-900 mt-4">Structured Data Management</h1>
    <nav class="flex mb-5" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-primary hover:text-primary-dark">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('admin.seo.index') }}" class="ml-1 text-sm font-medium text-primary hover:text-primary-dark md:ml-2">SEO Management</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Structured Data</span>
                </div>
            </li>
        </ol>
    </nav>
    
    @if(session('success'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="mb-6 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Add Custom Schema Markup</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Create custom structured data for specific pages on your site.</p>
            </div>
        </div>
        
        <div class="border-t border-gray-200 p-4">
            <form action="{{ route('admin.store-structured-data') }}" method="POST" id="schemaForm" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="page_url" class="block text-sm font-medium text-gray-700">Page URL</label>
                        <div class="mt-1">
                            <input type="text" name="page_url" id="page_url" placeholder="/page-path" value="{{ old('page_url') }}" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">The URL path of the page (without domain)</p>
                        @error('page_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="schema_type" class="block text-sm font-medium text-gray-700">Schema Type</label>
                        <div class="mt-1">
                            <select name="schema_type" id="schema_type" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                <option value="WebPage" {{ old('schema_type') == 'WebPage' ? 'selected' : '' }}>WebPage</option>
                                <option value="Article" {{ old('schema_type') == 'Article' ? 'selected' : '' }}>Article</option>
                                <option value="FAQPage" {{ old('schema_type') == 'FAQPage' ? 'selected' : '' }}>FAQ Page</option>
                                <option value="Organization" {{ old('schema_type') == 'Organization' ? 'selected' : '' }}>Organization</option>
                                <option value="LocalBusiness" {{ old('schema_type') == 'LocalBusiness' ? 'selected' : '' }}>Local Business</option>
                                <option value="BreadcrumbList" {{ old('schema_type') == 'BreadcrumbList' ? 'selected' : '' }}>BreadcrumbList</option>
                                <option value="Custom" {{ old('schema_type') == 'Custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Select the schema type (or custom for advanced users)</p>
                        @error('schema_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div id="schemaFields" class="space-y-4">
                    <!-- Dynamic fields will be generated based on schema type -->
                    <div class="hidden" id="webpageFields">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="webpage_name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" name="webpage_name" id="webpage_name" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label for="webpage_description" class="block text-sm font-medium text-gray-700">Description</label>
                                <input type="text" name="webpage_description" id="webpage_description" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>
                    
                    <div class="hidden" id="articleFields">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="article_headline" class="block text-sm font-medium text-gray-700">Headline</label>
                                <input type="text" name="article_headline" id="article_headline" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label for="article_author" class="block text-sm font-medium text-gray-700">Author</label>
                                <input type="text" name="article_author" id="article_author" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label for="article_published_date" class="block text-sm font-medium text-gray-700">Published Date</label>
                                <input type="date" name="article_published_date" id="article_published_date" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label for="article_image" class="block text-sm font-medium text-gray-700">Image URL</label>
                                <input type="url" name="article_image" id="article_image" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>
                    
                    <div class="hidden" id="faqFields">
                        <div id="faqContainer">
                            <div class="faqItem bg-gray-50 p-4 rounded-md mb-4">
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Question</label>
                                        <input type="text" name="faq_questions[]" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Answer</label>
                                        <textarea name="faq_answers[]" rows="3" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="addFaqBtn" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add FAQ Item
                        </button>
                    </div>
                    
                    <div class="hidden" id="organizationFields">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="org_name" class="block text-sm font-medium text-gray-700">Organization Name</label>
                                <input type="text" name="org_name" id="org_name" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label for="org_url" class="block text-sm font-medium text-gray-700">URL</label>
                                <input type="url" name="org_url" id="org_url" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label for="org_logo" class="block text-sm font-medium text-gray-700">Logo URL</label>
                                <input type="url" name="org_logo" id="org_logo" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label for="org_social_profiles" class="block text-sm font-medium text-gray-700">Social Profiles</label>
                                <input type="text" name="org_social_profiles" id="org_social_profiles" placeholder="https://facebook.com/your-page, https://twitter.com/your-handle" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <p class="mt-1 text-xs text-gray-500">Comma-separated list of social profile URLs</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="hidden" id="localBusinessFields">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="business_name" class="block text-sm font-medium text-gray-700">Business Name</label>
                                <input type="text" name="business_name" id="business_name" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label for="business_type" class="block text-sm font-medium text-gray-700">Business Type</label>
                                <input type="text" name="business_type" id="business_type" placeholder="Store, Salon, Restaurant" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label for="business_address" class="block text-sm font-medium text-gray-700">Address</label>
                                <textarea name="business_address" id="business_address" rows="3" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                            </div>
                            <div>
                                <label for="business_phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                <input type="text" name="business_phone" id="business_phone" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>
                    
                    <div class="hidden" id="breadcrumbFields">
                        <div id="breadcrumbContainer">
                            <div class="breadcrumbItem bg-gray-50 p-4 rounded-md mb-4">
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Name</label>
                                        <input type="text" name="breadcrumb_names[]" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">URL</label>
                                        <input type="text" name="breadcrumb_urls[]" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="addBreadcrumbBtn" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Breadcrumb Item
                        </button>
                    </div>
                    
                    <div class="hidden" id="customFields">
                        <div>
                            <label for="custom_schema" class="block text-sm font-medium text-gray-700">Custom JSON-LD</label>
                            <div class="flex justify-end mb-1">
                                <button type="button" id="useProductSample" class="text-xs text-primary hover:text-primary-dark flex items-center">
                                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Use Your Real Products
                                    <span class="tooltip ml-1 group relative">
                                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="tooltip-text invisible absolute z-50 w-48 p-2 bg-gray-700 text-white text-xs rounded-md -mt-1 -ml-52 group-hover:visible">
                                            This will fetch your real products from the database and use your store's currency settings
                                        </span>
                                    </span>
                                </button>
                            </div>
                            <textarea name="custom_schema" id="custom_schema" rows="10" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md font-mono"></textarea>
                            <p class="mt-1 text-xs text-gray-500">Enter JSON-LD code directly. Make sure it's valid JSON.</p>
                            
                            <div class="bg-gray-50 p-3 mt-2 rounded-md border border-gray-200">
                                <p class="text-xs font-medium text-gray-700 mb-1">Example JSON-LD for Products:</p>
                                <pre class="text-xs text-gray-600 overflow-auto max-h-40">
{
  "@context": "https://schema.org",
  "@type": "ItemList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "item": {
        "@type": "Product",
        "name": "Your Product Name",
        "description": "Description of your product",
        "image": "https://example.com/product-image.jpg",
        "offers": {
          "@type": "Offer",
          "price": "19.99",
          "priceCurrency": "{{ Settings::get('default_currency', 'EGP') }}",
          "availability": "https://schema.org/InStock",
          "priceValidUntil": "{{ date('Y-m-d', strtotime('+1 year')) }}"
        }
      }
    }
  ]
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4 mt-4">
                    <label for="is_active" class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>
                
                <div class="pt-3">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Add Schema
                    </button>
                </div>
                
                <!-- Inline emergency fix for Custom fields -->
                <script>
                    // Immediately execute
                    (function() {
                        var schemaType = document.getElementById('schema_type');
                        var customFields = document.getElementById('customFields');
                        
                        // Show custom fields if Custom is selected
                        if (schemaType && schemaType.value === 'Custom' && customFields) {
                            customFields.classList.remove('hidden');
                        }
                        
                        // Add event listener
                        if (schemaType) {
                            schemaType.addEventListener('change', function() {
                                if (this.value === 'Custom' && customFields) {
                                    customFields.classList.remove('hidden');
                                }
                            });
                        }
                        
                        // Handle sample button click
                        var sampleBtn = document.getElementById('useProductSample');
                        var customSchema = document.getElementById('custom_schema');
                        
                        if (sampleBtn && customSchema) {
                            sampleBtn.addEventListener('click', function() {
                                // Show loading state
                                sampleBtn.textContent = 'Loading...';
                                sampleBtn.disabled = true;
                                
                                // Fetch real product data from the server
                                fetch('{{ route('admin.structured-data-product-sample') }}')
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.status === 'success' || data.sample) {
                                            // Format the JSON and put it in the textarea
                                            customSchema.value = JSON.stringify(data.sample, null, 2);
                                        } else {
                                            // If there's an error, use a fallback sample
                                            console.error('Error fetching product sample:', data.message);
                                            var fallbackSample = {
                                                "@context": "https://schema.org",
                                                "@type": "ItemList",
                                                "itemListElement": [
                                                    {
                                                        "@type": "ListItem",
                                                        "position": 1,
                                                        "item": {
                                                            "@type": "Product",
                                                            "name": "Your Product Name",
                                                            "description": "Description of your product",
                                                            "image": "https://example.com/product-image.jpg",
                                                            "offers": {
                                                                "@type": "Offer",
                                                                "price": "19.99",
                                                                "priceCurrency": "{{ Settings::get('default_currency', 'EGP') }}",
                                                                "availability": "https://schema.org/InStock",
                                                                "priceValidUntil": "{{ date('Y-m-d', strtotime('+1 year')) }}"
                                                            }
                                                        }
                                                    }
                                                ]
                                            };
                                            customSchema.value = JSON.stringify(fallbackSample, null, 2);
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error fetching product sample:', error);
                                        // Use a fallback sample
                                        var fallbackSample = {
                                            "@context": "https://schema.org",
                                            "@type": "ItemList",
                                            "itemListElement": [
                                                {
                                                    "@type": "ListItem",
                                                    "position": 1,
                                                    "item": {
                                                        "@type": "Product",
                                                        "name": "Your Product Name",
                                                        "description": "Description of your product",
                                                        "image": "https://example.com/product-image.jpg",
                                                        "offers": {
                                                            "@type": "Offer",
                                                            "price": "19.99",
                                                            "priceCurrency": "{{ Settings::get('default_currency', 'EGP') }}",
                                                            "availability": "https://schema.org/InStock",
                                                            "priceValidUntil": "{{ date('Y-m-d', strtotime('+1 year')) }}"
                                                        }
                                                    }
                                                }
                                            ]
                                        };
                                        customSchema.value = JSON.stringify(fallbackSample, null, 2);
                                    })
                                    .finally(() => {
                                        // Reset button state
                                        sampleBtn.textContent = 'Use Products Sample';
                                        sampleBtn.disabled = false;
                                    });
                            });
                        }
                    })();
                </script>
            </form>
        </div>
    </div>
    
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Current Schema Markups</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Manage your existing schema markup. These are automatically added to the respective pages.
            </p>
        </div>
        
        <div class="border-t border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Page URL</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schema Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($schemas ?? [] as $schema)
                            <tr id="schema-{{ $schema->id }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $schema->page_url }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $schema->schema_type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($schema->is_active)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $schema->created_at }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.edit-structured-data', $schema->id) }}" class="text-primary hover:text-primary-dark">
                                            <span class="sr-only">Edit</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.destroy-structured-data', $schema->id) }}" method="POST" class="inline-block delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                <span class="sr-only">Delete</span>
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No schema markups found. Add your first schema using the form above.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                @if(isset($schemas))
                    {{ $schemas->links() }}
                @endif
            </div>
        </div>
    </div>
    
    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">About Structured Data</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Learn more about schema markup and how it enhances your content in search results.
            </p>
        </div>
        
        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">What is structured data?</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        Structured data is a standardized format for providing information about a page and classifying the page content. Search engines use structured data to generate rich snippets in search results, which can enhance the visibility and click-through rate of your website.
                    </dd>
                </div>
                
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Benefits</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Enhanced search results appearance</li>
                            <li>Better understanding of your content by search engines</li>
                            <li>Potential for higher click-through rates</li>
                            <li>Eligibility for special search result features</li>
                        </ul>
                    </dd>
                </div>
                
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Common Schema Types</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <ul class="list-disc pl-5 space-y-1">
                            <li><strong>WebPage:</strong> General information about a web page</li>
                            <li><strong>Article:</strong> For blog posts and news articles</li>
                            <li><strong>FAQPage:</strong> Pages with frequently asked questions</li>
                            <li><strong>Organization:</strong> Information about your business</li>
                            <li><strong>LocalBusiness:</strong> For stores with physical locations</li>
                            <li><strong>BreadcrumbList:</strong> Navigation path for the current page</li>
                        </ul>
                    </dd>
                </div>
                
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Testing Your Schema</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        After implementing schema markup, you can test it using Google's
                        <a href="https://search.google.com/test/rich-results" target="_blank" class="text-primary hover:text-primary-dark">Rich Results Test</a>
                        or the 
                        <a href="https://validator.schema.org/" target="_blank" class="text-primary hover:text-primary-dark">Schema Markup Validator</a>.
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get schema type select element
        const schemaTypeSelect = document.getElementById('schema_type');
        const schemaFields = document.getElementById('schemaFields');
        const customFields = document.getElementById('customFields');
        
        // Function to show fields for selected schema type
        function showSchemaFields(schemaType) {
            console.log('Showing fields for schema type:', schemaType);
            
            // Hide all schema type fields
            document.querySelectorAll('#schemaFields > div').forEach(div => {
                div.classList.add('hidden');
            });
            
            // Show fields for selected schema type
            switch(schemaType) {
                case 'WebPage':
                    document.getElementById('webpageFields').classList.remove('hidden');
                    break;
                case 'Article':
                    document.getElementById('articleFields').classList.remove('hidden');
                    break;
                case 'FAQPage':
                    document.getElementById('faqFields').classList.remove('hidden');
                    break;
                case 'Organization':
                    document.getElementById('organizationFields').classList.remove('hidden');
                    break;
                case 'LocalBusiness':
                    document.getElementById('localBusinessFields').classList.remove('hidden');
                    break;
                case 'BreadcrumbList':
                    document.getElementById('breadcrumbFields').classList.remove('hidden');
                    break;
                case 'Custom':
                    document.getElementById('customFields').classList.remove('hidden');
                    break;
            }
        }
        
        // Force-show custom fields if Custom is selected
        if (schemaTypeSelect.value === 'Custom') {
            customFields.classList.remove('hidden');
        }
        
        // Immediately call the function once to set initial state
        showSchemaFields(schemaTypeSelect.value);
        
        // Add event listener for schema type change
        schemaTypeSelect.addEventListener('change', function() {
            showSchemaFields(this.value);
            
            // Force show custom fields if needed
            if (this.value === 'Custom') {
                setTimeout(() => {
                    document.getElementById('customFields').classList.remove('hidden');
                }, 0);
            }
        });
        
        // Add FAQ button click handler
        const addFaqBtn = document.getElementById('addFaqBtn');
        if (addFaqBtn) {
            addFaqBtn.addEventListener('click', function() {
                const faqContainer = document.getElementById('faqContainer');
                const faqItems = faqContainer.querySelectorAll('.faqItem');
                const newItem = faqItems[0].cloneNode(true);
                
                // Clear inputs in the new item
                newItem.querySelectorAll('input, textarea').forEach(input => {
                    input.value = '';
                });
                
                faqContainer.appendChild(newItem);
            });
        }
        
        // Add Breadcrumb button click handler
        const addBreadcrumbBtn = document.getElementById('addBreadcrumbBtn');
        if (addBreadcrumbBtn) {
            addBreadcrumbBtn.addEventListener('click', function() {
                const breadcrumbContainer = document.getElementById('breadcrumbContainer');
                const breadcrumbItems = breadcrumbContainer.querySelectorAll('.breadcrumbItem');
                const newItem = breadcrumbItems[0].cloneNode(true);
                
                // Clear inputs in the new item
                newItem.querySelectorAll('input').forEach(input => {
                    input.value = '';
                });
                
                breadcrumbContainer.appendChild(newItem);
            });
        }
        
        // Delete form confirmation
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to delete this schema? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endsection
@endsection 