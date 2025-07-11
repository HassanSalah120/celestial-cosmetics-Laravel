@extends('layouts.admin')

@section('title', $pageTitle)

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-semibold text-gray-900 mt-4">Canonical URL Management</h1>
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
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Canonical URL Management</span>
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

    <div class="bg-white shadow sm:rounded-lg mb-8">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">About Canonical URLs</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Learn how canonical URLs help prevent duplicate content issues.
            </p>
        </div>
        
        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
            <div class="text-sm text-gray-900">
                <p class="mb-3">
                    <strong>What is a canonical URL?</strong> A canonical URL is the URL of the page that Google thinks is most representative from a set of duplicate pages on your site.
                </p>
                <p class="mb-3">
                    <strong>Why are they important?</strong> If you have multiple pages with similar content, search engines may consider them as duplicate content, which can negatively impact your SEO. Using canonical URLs helps search engines understand which version of a page is the primary one.
                </p>
                <p class="mb-3">
                    <strong>When to use custom canonical URLs?</strong> You should set custom canonical URLs when:
                </p>
                <ul class="list-disc pl-6 mb-3 space-y-1">
                    <li>You have content available on multiple URLs (e.g., via parameters or different paths)</li>
                    <li>You have similar or duplicate content on different domains</li>
                    <li>You have variations of a page for different devices or languages</li>
                </ul>
                <p class="mb-3">
                    <strong>Best practices:</strong>
                </p>
                <ul class="list-disc pl-6 mb-3 space-y-1">
                    <li>Use absolute URLs for canonical tags</li>
                    <li>Be consistent with URL formats (http vs https, www vs non-www)</li>
                    <li>Only use one canonical URL per page</li>
                    <li>Regularly audit your canonical URLs to ensure they're correctly configured</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Products with Canonical URLs -->
    <div class="bg-white shadow sm:rounded-lg mb-8">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Product Canonical URLs</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Manage canonical URLs for your products.
                </p>
            </div>
        </div>
        
        <div class="border-t border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Default URL</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Custom Canonical URL</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($products as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $product->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 max-w-xs truncate">
                                    <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="text-primary hover:text-primary-dark" title="{{ route('product.show', $product->slug) }}">
                                        {{ route('product.show', $product->slug) }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 max-w-xs truncate">
                                    @if($product->canonical_url)
                                        <span class="text-green-600" title="{{ $product->canonical_url }}">
                                            {{ $product->canonical_url }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">
                                            Using default URL
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" class="text-primary hover:text-primary-dark" 
                                            onclick="openEditModal('product', {{ $product->id }}, '{{ $product->name }}', '{{ $product->canonical_url ?? '' }}')">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($products->hasPages())
                <div class="px-6 py-3 border-t border-gray-200">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
    
    <!-- Categories with Canonical URLs -->
    <div class="bg-white shadow sm:rounded-lg mb-8">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Category Canonical URLs</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Manage canonical URLs for your categories.
                </p>
            </div>
        </div>
        
        <div class="border-t border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Default URL</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Custom Canonical URL</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($categories as $category)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $category->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 max-w-xs truncate">
                                    <a href="{{ route('products.category', $category->slug) }}" target="_blank" class="text-primary hover:text-primary-dark" title="{{ route('products.category', $category->slug) }}">
                                        {{ route('products.category', $category->slug) }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 max-w-xs truncate">
                                    @if($category->canonical_url)
                                        <span class="text-green-600" title="{{ $category->canonical_url }}">
                                            {{ $category->canonical_url }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">
                                            Using default URL
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" class="text-primary hover:text-primary-dark" 
                                            onclick="openEditModal('category', {{ $category->id }}, '{{ $category->name }}', '{{ $category->canonical_url ?? '' }}')">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($categories->hasPages())
                <div class="px-6 py-3 border-t border-gray-200">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
    
    <!-- Modal for editing canonical URLs -->
    <div id="editModal" class="fixed inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Edit Canonical URL
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" id="modalItemName"></p>
                            </div>
                            
                            <form id="editCanonicalForm" action="" method="POST" class="mt-4">
                                @csrf
                                @method('PUT')
                                
                                <input type="hidden" name="item_id" id="itemId">
                                <input type="hidden" name="item_type" id="itemType">
                                
                                <div>
                                    <label for="canonical_url" class="block text-sm font-medium text-gray-700">Custom Canonical URL</label>
                                    <div class="mt-1">
                                        <input type="url" name="canonical_url" id="canonicalUrl" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md" placeholder="https://example.com/custom-url">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Leave empty to use the default URL.</p>
                                </div>
                                
                                <div class="mt-4">
                                    <label for="noindex" class="inline-flex items-center">
                                        <input type="checkbox" name="noindex" id="noindex" class="rounded border-gray-300 text-primary focus:ring-primary">
                                        <span class="ml-2 text-sm text-gray-700">Add noindex meta tag</span>
                                    </label>
                                    <p class="mt-1 text-xs text-gray-500">Prevents search engines from indexing this page.</p>
                                </div>
                                
                                <div class="mt-2">
                                    <label for="nofollow" class="inline-flex items-center">
                                        <input type="checkbox" name="nofollow" id="nofollow" class="rounded border-gray-300 text-primary focus:ring-primary">
                                        <span class="ml-2 text-sm text-gray-700">Add nofollow meta tag</span>
                                    </label>
                                    <p class="mt-1 text-xs text-gray-500">Prevents search engines from following links on this page.</p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="saveButton" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                        Save
                    </button>
                    <button type="button" id="cancelButton" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openEditModal(type, id, name, canonicalUrl) {
        // Set form values
        document.getElementById('itemType').value = type;
        document.getElementById('itemId').value = id;
        document.getElementById('modalItemName').textContent = name;
        document.getElementById('canonicalUrl').value = canonicalUrl;
        
        // Set form action
        if (type === 'product') {
            document.getElementById('editCanonicalForm').action = "{{ route('admin.seo.update-product-canonical', '') }}/" + id;
        } else {
            document.getElementById('editCanonicalForm').action = "{{ route('admin.seo.update-category-canonical', '') }}/" + id;
        }
        
        // Fetch and set noindex/nofollow status
        fetch(`/admin/seo/${type}-meta/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('noindex').checked = data.noindex;
                document.getElementById('nofollow').checked = data.nofollow;
            });
        
        // Show modal
        document.getElementById('editModal').classList.remove('hidden');
    }
    
    document.getElementById('saveButton').addEventListener('click', function() {
        document.getElementById('editCanonicalForm').submit();
    });
    
    document.getElementById('cancelButton').addEventListener('click', function() {
        document.getElementById('editModal').classList.add('hidden');
    });
</script>
@endsection 