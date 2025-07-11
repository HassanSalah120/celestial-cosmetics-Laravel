@extends('layouts.admin')

@section('content')
<div class="container mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Create New Offer</h1>
            <p class="mt-1 text-sm text-gray-600">Add a promotional offer to display on the homepage</p>
        </div>
        <a href="{{ route('admin.offers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Offers
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm">
        <form action="{{ route('admin.offers.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded relative" role="alert">
                <strong class="font-medium">Oops! There were some problems with your input.</strong>
                <ul class="mt-3 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Language Tabs -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex">
                    <button type="button" id="tab-en" class="tab-btn py-3 px-6 border-b-2 border-primary text-primary font-medium">
                        English
                    </button>
                    <button type="button" id="tab-ar" class="tab-btn py-3 px-6 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium">
                        Arabic (العربية)
                    </button>
                </nav>
            </div>

            <!-- English Content Tab -->
            <div id="content-en" class="tab-content space-y-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Offer Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>

                    <div>
                        <label for="subtitle" class="block text-sm font-medium text-gray-700">Subtitle</label>
                        <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('description') }}</textarea>
                </div>

                <!-- Additional Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tag" class="block text-sm font-medium text-gray-700">Tag Label</label>
                        <input type="text" name="tag" id="tag" value="{{ old('tag', 'SPECIAL OFFER') }}" placeholder="e.g. SPECIAL OFFER"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>

                    <div>
                        <label for="discount_text" class="block text-sm font-medium text-gray-700">Discount Text</label>
                        <input type="text" name="discount_text" id="discount_text" value="{{ old('discount_text') }}" placeholder="e.g. 25% OFF"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <p class="mt-1 text-xs text-gray-500">Optional custom text. If empty, system will calculate from prices</p>
                    </div>
                </div>

                <!-- Button Information -->
                <div>
                    <label for="button_text" class="block text-sm font-medium text-gray-700">Button Text</label>
                    <input type="text" name="button_text" id="button_text" value="{{ old('button_text', 'Buy Now') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
            </div>

            <!-- Arabic Content Tab -->
            <div id="content-ar" class="tab-content space-y-6 hidden">
                <!-- Arabic Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title_ar" class="block text-sm font-medium text-gray-700">Offer Title (Arabic)</label>
                        <input type="text" name="title_ar" id="title_ar" value="{{ old('title_ar') }}" dir="rtl"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>

                    <div>
                        <label for="subtitle_ar" class="block text-sm font-medium text-gray-700">Subtitle (Arabic)</label>
                        <input type="text" name="subtitle_ar" id="subtitle_ar" value="{{ old('subtitle_ar') }}" dir="rtl"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>
                </div>

                <!-- Arabic Description -->
                <div>
                    <label for="description_ar" class="block text-sm font-medium text-gray-700">Description (Arabic)</label>
                    <textarea name="description_ar" id="description_ar" rows="3" dir="rtl"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('description_ar') }}</textarea>
                </div>

                <!-- Arabic Additional Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tag_ar" class="block text-sm font-medium text-gray-700">Tag Label (Arabic)</label>
                        <input type="text" name="tag_ar" id="tag_ar" value="{{ old('tag_ar', 'عرض خاص') }}" dir="rtl"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>

                    <div>
                        <label for="discount_text_ar" class="block text-sm font-medium text-gray-700">Discount Text (Arabic)</label>
                        <input type="text" name="discount_text_ar" id="discount_text_ar" value="{{ old('discount_text_ar') }}" dir="rtl"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>
                </div>

                <!-- Arabic Button Information -->
                <div>
                    <label for="button_text_ar" class="block text-sm font-medium text-gray-700">Button Text (Arabic)</label>
                    <input type="text" name="button_text_ar" id="button_text_ar" value="{{ old('button_text_ar', 'اشتري الآن') }}" dir="rtl"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
            </div>

            <!-- Common Fields (non-language specific) -->
            <div class="pt-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">General Information</h3>
                
                <!-- Pricing Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="original_price" class="block text-sm font-medium text-gray-700">Original Price</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">{{ $currencySymbol }}</span>
                            </div>
                            <input type="number" name="original_price" id="original_price" value="{{ old('original_price') }}" step="0.01" min="0"
                                class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        </div>
                    </div>

                    <div>
                        <label for="discounted_price" class="block text-sm font-medium text-gray-700">Discounted Price</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">{{ $currencySymbol }}</span>
                            </div>
                            <input type="number" name="discounted_price" id="discounted_price" value="{{ old('discounted_price') }}" step="0.01" min="0"
                                class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        </div>
                    </div>
                </div>

                <!-- Stock Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700">Stock Quantity</label>
                        <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <p class="mt-1 text-xs text-gray-500">Number of units available for sale</p>
                    </div>

                    <div>
                        <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700">Low Stock Threshold</label>
                        <input type="number" name="low_stock_threshold" id="low_stock_threshold" value="{{ old('low_stock_threshold', 5) }}" min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <p class="mt-1 text-xs text-gray-500">Quantity at which to show low stock warnings</p>
                    </div>
                </div>

                <!-- Product Selection -->
                <div class="mb-6">
                    <label for="product_id" class="block text-sm font-medium text-gray-700">Related Product</label>
                    <select name="product_id" id="product_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="">-- Select a product --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} ({{ $currencySymbol }}{{ number_format($product->price, 2) }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Optional. If selected, customers can add this product to cart directly from the offer.</p>
                </div>

                <!-- Bundle Products Section -->
                <div class="mb-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bundle Products</h3>
                    <p class="text-sm text-gray-600 mb-4">Select products to include in this bundle. Each product can have a specific quantity.</p>
                    
                    <div id="bundle-products-container">
                        <div class="bundle-product-row grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Product</label>
                                <select name="bundle_products[]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bundle-product-select">
                                    <option value="">-- Select a product --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->name }} ({{ $currencySymbol }}{{ number_format($product->price, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Quantity</label>
                                <input type="number" name="bundle_quantities[]" min="1" value="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>
                            <div>
                                <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 remove-bundle-product">
                                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" id="add-bundle-product" class="mt-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Another Product
                    </button>
                </div>

                <!-- URL Information -->
                <div class="mb-6">
                    <label for="button_url" class="block text-sm font-medium text-gray-700">Button URL</label>
                    <input type="text" name="button_url" id="button_url" value="{{ old('button_url', '/products') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>

                <!-- Date Range Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="starts_at" class="block text-sm font-medium text-gray-700">Start Date/Time</label>
                        <input type="datetime-local" name="starts_at" id="starts_at" value="{{ old('starts_at') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>

                    <div>
                        <label for="expires_at" class="block text-sm font-medium text-gray-700">Expiry Date/Time</label>
                        <input type="datetime-local" name="expires_at" id="expires_at" value="{{ old('expires_at') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>
                </div>

                <!-- Image Upload -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Offer Image</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary">
                                        <span>Upload a file</span>
                                        <input id="image" name="image" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                            <div id="image-preview" class="hidden mt-4">
                                <img src="#" alt="Offer Image Preview" class="mx-auto h-32 w-auto object-cover rounded-lg">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status and Order -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700">Sort Order</label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
                    </div>

                    <div class="flex items-center h-full pt-6">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}
                            class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-700">
                            Make this offer active
                        </label>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.offers.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Create Offer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Tab functionality
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => {
                    btn.classList.remove('border-primary', 'text-primary');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });
                
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Add active class to clicked button
                button.classList.remove('border-transparent', 'text-gray-500');
                button.classList.add('border-primary', 'text-primary');
                
                // Show corresponding content
                const contentId = 'content-' + button.id.split('-')[1];
                document.getElementById(contentId).classList.remove('hidden');
            });
        });
    });

    // Image preview functionality
    document.getElementById('image').addEventListener('change', function(e) {
        const preview = document.getElementById('image-preview');
        const previewImg = preview.querySelector('img');
        const file = e.target.files[0];

        if (file) {
            previewImg.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    });

    // Price calculation
    const originalPrice = document.getElementById('original_price');
    const discountedPrice = document.getElementById('discounted_price');
    const discountText = document.getElementById('discount_text');

    function updateDiscountText() {
        const original = parseFloat(originalPrice.value);
        const discounted = parseFloat(discountedPrice.value);
        
        if (!isNaN(original) && !isNaN(discounted) && original > 0 && discounted > 0 && original > discounted) {
            const discount = Math.round((original - discounted) / original * 100);
            discountText.placeholder = `${discount}% OFF`;
        } else {
            discountText.placeholder = 'e.g. 25% OFF';
        }
    }

    originalPrice.addEventListener('input', updateDiscountText);
    discountedPrice.addEventListener('input', updateDiscountText);

    // Bundle products functionality
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('bundle-products-container');
        const addButton = document.getElementById('add-bundle-product');
        
        // Function to add event listeners to remove buttons
        function addRemoveListeners() {
            document.querySelectorAll('.remove-bundle-product').forEach(button => {
                button.addEventListener('click', function() {
                    if (container.children.length > 1) {
                        this.closest('.bundle-product-row').remove();
                    } else {
                        alert('You need at least one product in the bundle.');
                    }
                });
            });
        }
        
        // Initialize remove listeners
        addRemoveListeners();
        
        // Add new product row
        addButton.addEventListener('click', function() {
            const newRow = container.children[0].cloneNode(true);
            
            // Clear selected values
            newRow.querySelector('select').value = '';
            newRow.querySelector('input[type="number"]').value = 1;
            
            container.appendChild(newRow);
            addRemoveListeners();
        });
    });
</script>
@endpush 