@extends('layouts.admin')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="space-y-6">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 font-display">Edit Product</h2>
            <p class="mt-1 text-sm text-gray-600">Update product information</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
            </svg>
            Back to Products
        </a>
    </div>

    <!-- Edit Product Form -->
    <div class="bg-white rounded-lg shadow-sm">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6" id="product-edit-form">
            @csrf
            @method('PUT')
            
            <!-- Current Image Hidden Field -->
            <input type="hidden" name="current_image" id="current-image-field" value="{{ $product->image }}" data-original="{{ $product->image }}">
            <!-- Immediate execution script to ensure field is populated -->
            <script>
                // This script runs immediately when parsed, not waiting for DOMContentLoaded
                (function() {
                    console.log('Immediate script - ensuring current_image is set');
                    const currentImage = "{{ $product->image }}";
                    console.log('Product image from server: ' + currentImage);
                    
                    // Force the value into a global variable as backup
                    window.productCurrentImage = currentImage;
                    
                    // Try to set the field if it already exists
                    const field = document.getElementById('current-image-field');
                    if (field) {
                        field.value = currentImage;
                        console.log('Set current_image field in immediate script');
                    }
                })();
            </script>
            <!-- End Current Image Hidden Field -->
            <!-- Add debug info for troubleshooting -->
            @if($product->image)
                <script>
                    console.log('Product has image: {{ $product->image }}');
                </script>
            @else
                <script>
                    console.log('Product has no image');
                </script>
            @endif

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Product Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category_id" id="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}</span>
                        </div>
                        <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
                    </div>
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock -->
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
                    @error('stock')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ingredients -->
            <div>
                <label for="ingredients" class="block text-sm font-medium text-gray-700">Ingredients</label>
                <textarea name="ingredients" id="ingredients" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('ingredients', $product->ingredients) }}</textarea>
                @error('ingredients')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- How to Use -->
            <div>
                <label for="how_to_use" class="block text-sm font-medium text-gray-700">How to Use</label>
                <textarea name="how_to_use" id="how_to_use" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('how_to_use', $product->how_to_use) }}</textarea>
                @error('how_to_use')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image Management Section -->
            <div class="space-y-8 bg-white rounded-lg shadow-sm p-6">
                <div class="border-b pb-4">
                    <h3 class="text-lg font-medium text-gray-900">Image Management</h3>
                    <p class="mt-1 text-sm text-gray-500">Manage your product's featured and gallery images</p>
                </div>

                <!-- Featured Image -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-base font-medium text-gray-900">Featured Image</h4>
                            <p class="mt-1 text-sm text-gray-500">This image will be displayed as the main product image</p>
                        </div>
                        @if($product->image)
                            <div id="remove-image-container">
                            <button type="button" 
                                class="inline-flex items-center px-3 py-1.5 border border-red-300 text-sm font-medium rounded-full text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200" 
                                    onclick="removeFeaturedImage({{ $product->id }})">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Remove Featured Image
                            </button>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-start space-x-6">
                        <!-- Current/Preview Image -->
                        <div class="relative group w-48 h-48 bg-gray-50 rounded-lg overflow-hidden flex items-center justify-center border-2 border-gray-200 border-dashed">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="Featured image" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-all duration-200 flex items-center justify-center">
                                    <a href="{{ asset('storage/' . $product->image) }}" 
                                       target="_blank" 
                                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-white bg-opacity-20 hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-200">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View Full Size
                                    </a>
                                </div>
                            @else
                                <div class="text-center p-4">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">No featured image set</p>
                                </div>
                            @endif
                        </div>

                        <!-- Upload Section -->
                        <div class="flex-1 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Update Featured Image</label>
                                <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-primary transition-colors duration-200" id="featured-image-dropzone">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="featured_image" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary">
                                                <span>Upload a file</span>
                                            <input id="featured_image" name="featured_image" type="file" class="sr-only" accept="image/*">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                        
                                        <!-- Preview will appear here on selection -->
                                        <div id="featured-image-edit-preview" class="hidden mt-4">
                                            <img src="#" alt="Featured Image Preview" class="mx-auto h-32 w-32 object-cover rounded-lg">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">Image Guidelines</h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <ul class="list-disc pl-5 space-y-1">
                                                <li>Recommended size: 800x800 pixels</li>
                                                <li>Maximum file size: 10MB</li>
                                                <li>Use a clear, well-lit photo</li>
                                                <li>Ensure the product is centered</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Alternative path option for when the original can't be found -->
                            @if($product->image && !Storage::disk('public')->exists($product->image))
                            <div class="mt-4 bg-red-50 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Image Not Found</h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <p>The current image ({{ $product->image }}) cannot be found in storage. Please upload a new image.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Gallery Images -->
                <div class="mt-8 border-t border-gray-200 pt-8">
                    <h3 class="text-lg font-medium text-gray-900">Gallery Images</h3>
                    <p class="mt-1 text-sm text-gray-500">Add multiple gallery images for this product. Image dimensions should be at least 800x800 pixels.</p>
                    
                    <div id="gallery-dropzone" class="mt-4 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md cursor-pointer">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4h-8m-12 0v-8m0 0v-8m0 0v-8m12 0a4 4 0 014 4v12m-4-12v-4a4 4 0 014-4h4"></path>
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="gallery_images" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none">
                                    <span>Upload gallery images</span>
                                    <input id="gallery_images" name="gallery_images[]" type="file" class="sr-only" accept="image/*" multiple>
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB each</p>
                        </div>
                    </div>

                    <!-- Gallery Preview Section -->
                    <div id="gallery-preview" class="hidden mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <!-- Preview images will be added here via JavaScript -->
                    </div>

                    <!-- Existing Gallery Images -->
                    @php
                        // Handle gallery images whether they're stored as JSON string or as a relationship
                        $galleryImages = [];
                        
                        // Try to get from gallery_images attribute (JSON string or array)
                        if (isset($product->gallery_images)) {
                            $galleryImages = is_string($product->gallery_images) 
                                ? json_decode($product->gallery_images) 
                                : $product->gallery_images;
                        }
                        
                        // If empty and images relationship exists, use that instead
                        if (empty($galleryImages) && method_exists($product, 'images') && $product->images && $product->images->count() > 0) {
                            $useRelationship = true;
                        } else {
                            $useRelationship = false;
                        }
                    @endphp

                    @if((!empty($galleryImages) && count($galleryImages) > 0) || ($useRelationship && $product->images->count() > 0))
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-900">Current Gallery Images</h4>
                            <div class="mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @if($useRelationship)
                                    @foreach($product->images as $imageModel)
                                        <div class="relative group">
                                            <img src="{{ Storage::url($imageModel->image) }}" alt="Gallery image" class="h-24 w-24 object-cover rounded-md">
                                            <button type="button" 
                                                class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 shadow opacity-0 group-hover:opacity-100 transition-opacity"
                                                onclick="removeGalleryImage('{{ $product->id }}', '{{ $imageModel->image }}', this)">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    @foreach($galleryImages as $image)
                                        <div class="relative group">
                                            <img src="{{ Storage::url($image) }}" alt="Gallery image" class="h-24 w-24 object-cover rounded-md">
                                            <button type="button" 
                                                class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 shadow opacity-0 group-hover:opacity-100 transition-opacity"
                                                onclick="removeGalleryImage('{{ $product->id }}', '{{ $image }}', this)">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Featured Product -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input type="checkbox" name="is_featured" id="is_featured" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm">
                    <label for="is_featured" class="font-medium text-gray-700">Featured Product</label>
                    <p class="text-gray-500">Featured products appear in special sections of your store</p>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" id="update-product-button" class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Product
                </button>
            </div>
        </form>
    </div>
    
    <!-- Separate form for removing featured image (outside main form) -->
    <form id="remove-featured-image-form" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded - checking current_image field');
        
        // Get the current image field
        const currentImageField = document.getElementById('current-image-field');
        
        if (currentImageField) {
            console.log('Current image field found, value: ' + (currentImageField.value || 'empty'));
            
            // Set image field from data-original attribute if it's empty
            if (!currentImageField.value && currentImageField.getAttribute('data-original')) {
                currentImageField.value = currentImageField.getAttribute('data-original');
                console.log('Set current_image field from data-original: ' + currentImageField.value);
            }
            
            // If the field exists but is still empty, try to get the image from the img element
            if (!currentImageField.value || currentImageField.value === '') {
                const imgElement = document.querySelector('.relative.group .w-full.h-full.object-cover');
                
                if (imgElement) {
                    console.log('Found image element with src: ' + imgElement.src);
                    
                    try {
                        // Extract path from src attribute
                        const fullPath = imgElement.src;
                        const storagePath = fullPath.split('/storage/')[1];
                        
                        if (storagePath) {
                            currentImageField.value = storagePath;
                            console.log('Set current_image field to: ' + storagePath);
                        }
                    } catch (e) {
                        console.error('Error extracting image path:', e);
                    }
                } else {
                    console.log('No image element found in the DOM');
                }
            }
        } else {
            console.log('Current image field not found in DOM');
        }
        
        // Initialize the featured and gallery image inputs
        setupFeaturedImage();
        setupGalleryImages();
    });
    
    function setupFeaturedImage() {
        const featuredInput = document.getElementById('featured_image');
        const dropZone = document.getElementById('featured-image-dropzone');
        const currentImageField = document.querySelector('input[name="current_image"]');
        
        // Set up drag and drop for featured image
        setupDragAndDrop(dropZone, featuredInput, updateMainImagePreview);
        
        // Handle file input change
        featuredInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                updateMainImagePreview(this.files[0]);
                // When a new file is selected, ensure we retain the current_image as a fallback
                if (currentImageField) {
                    currentImageField.setAttribute('data-has-new-file', 'true');
                }
            } else {
                // If file input was cleared, make sure current_image is preserved
                if (currentImageField) {
                    currentImageField.setAttribute('data-has-new-file', 'false');
                }
            }
        });
        
        // Handle clear button
        const clearButton = document.getElementById('clear-featured');
        if (clearButton) {
            clearButton.addEventListener('click', function() {
                featuredInput.value = '';
                updateMainImagePreview(null);
                // Ensure current_image is preserved when manually clearing
                if (currentImageField) {
                    currentImageField.setAttribute('data-has-new-file', 'false');
                }
            });
        }
        
        // Also add a check when the form is submitted
        const form = document.getElementById('product-edit-form');
        form.addEventListener('submit', function() {
            // If the file input is empty and we didn't specifically clear it,
            // ensure the current_image is preserved
            if (featuredInput.files.length === 0 && 
                currentImageField && 
                currentImageField.getAttribute('data-has-new-file') !== 'true') {
                console.log('Preserving current image:', currentImageField.value);
            }
        });
    }
    
    function setupGalleryImages() {
        const galleryInput = document.getElementById('gallery_images');
        const dropZone = document.getElementById('gallery-dropzone');
        const galleryPreview = document.getElementById('gallery-preview');
        
        // Set up drag and drop for gallery images
        setupDragAndDrop(dropZone, galleryInput, function(files) {
            updateGalleryPreviews(files);
        });
        
        // Handle file input change
        galleryInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                updateGalleryPreviews(this.files);
                galleryPreview.classList.remove('hidden');
            }
        });
    }
    
    function setupDragAndDrop(dropZone, input, callback) {
        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });
        
        // Highlight drop zone when dragging over it
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });
        
        // Handle dropped files
        dropZone.addEventListener('drop', handleDrop, false);
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        function highlight() {
            dropZone.classList.add('border-primary', 'bg-primary-50');
            dropZone.classList.remove('border-gray-300');
        }
        
        function unhighlight() {
            dropZone.classList.remove('border-primary', 'bg-primary-50');
            dropZone.classList.add('border-gray-300');
        }
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (input.multiple) {
                // For gallery images
                input.files = files;
                callback(files);
            } else {
                // For featured image
                if (files.length > 0) {
                    // Create a new FileList with just the first file
                    const fileList = new DataTransfer();
                    fileList.items.add(files[0]);
                    input.files = fileList.files;
                    callback(files[0]);
                }
            }
            
            // Show preview area if we have files
            if (files.length > 0) {
                const previewArea = dropZone.closest('div').querySelector('[id$="-preview"]');
                if (previewArea) {
                    previewArea.classList.remove('hidden');
                }
            }
        }
    }
    
    function updateMainImagePreview(file) {
        const previewDiv = document.getElementById('featured-image-edit-preview');
        const currentImage = document.querySelector('.relative.group .w-full.h-full.object-cover');
        
        if (!file) {
            if (previewDiv) previewDiv.classList.add('hidden');
            if (currentImage) {
                currentImage.closest('.relative.group').classList.remove('hidden');
            }
            return;
        }
        
        if (currentImage) {
            currentImage.closest('.relative.group').classList.add('hidden');
        }
        
        if (!previewDiv) {
            console.error('Preview div not found');
            return;
        }
        
        previewDiv.innerHTML = '';
        previewDiv.classList.remove('hidden');
        
        const img = document.createElement('img');
        img.classList.add('mx-auto', 'h-32', 'w-32', 'object-cover', 'rounded-lg');
        img.alt = 'Featured Image Preview';
        
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
        
        previewDiv.appendChild(img);
    }
    
    function updateGalleryPreviews(files) {
        const previewDiv = document.getElementById('gallery-preview');
        previewDiv.innerHTML = '';
        previewDiv.classList.remove('hidden');
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            const container = document.createElement('div');
            container.classList.add('relative', 'group');
            
            const img = document.createElement('img');
            img.classList.add('h-24', 'w-24', 'object-cover', 'rounded-md');
            img.file = file;
            
            const reader = new FileReader();
            reader.onload = (function(aImg) {
                return function(e) {
                    aImg.src = e.target.result;
                };
            })(img);
            reader.readAsDataURL(file);
            
            const removeBtn = document.createElement('button');
            removeBtn.setAttribute('type', 'button');
            removeBtn.setAttribute('data-index', i);
            removeBtn.classList.add('absolute', 'top-0', 'right-0', 'bg-red-500', 'text-white', 'rounded-full', 'p-1', 'shadow', 'transform', 'translate-x-1/2', '-translate-y-1/2', 'opacity-0', 'group-hover:opacity-100', 'transition-opacity');
            removeBtn.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            `;
            
            // We can't easily remove a file from a FileList, so this is just for visual feedback
            removeBtn.addEventListener('click', function() {
                container.remove();
                if (previewDiv.children.length === 0) {
                    previewDiv.classList.add('hidden');
                }
            });
            
            container.appendChild(img);
            container.appendChild(removeBtn);
            previewDiv.appendChild(container);
        }
    }
    
    function debugGalleryImages(productId) {
        // Send AJAX request to debug the gallery images
        fetch(`/admin/products/${productId}/edit`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Create a temporary DOM element to parse the HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            // Try to extract gallery images information
            console.log('Product ID:', productId);
            console.log('Gallery images from DOM:', tempDiv.querySelectorAll('.relative.group img').length);
            
            // Log all src attributes from gallery images
            const imgs = tempDiv.querySelectorAll('.relative.group img');
            Array.from(imgs).forEach((img, i) => {
                console.log(`Image ${i} src:`, img.src);
                console.log(`Image ${i} alt:`, img.alt);
            });
        })
        .catch(error => {
            console.error('Error debugging gallery images:', error);
        });
    }

    function removeGalleryImage(productId, imagePath, button) {
        if (!confirm('Are you sure you want to remove this image?')) {
            return;
        }
        
        console.log('Removing image:', imagePath);
        
        // Check if the imagePath needs formatting
        // Sometimes we may need just the filename, not the full path
        const simplifiedPath = imagePath.includes('/storage/') 
            ? imagePath.split('/storage/')[1] 
            : imagePath;
        
        // Debug info
        console.log('Simplified path:', simplifiedPath);
        console.log('Original button element:', button);
        
        // Remove the image visually immediately for better UX
        const imageElement = button.closest('.relative');
        if (imageElement) {
            imageElement.style.opacity = '0.5';
        }
        
        // Create a FormData object instead of JSON
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('image_path', simplifiedPath);
        
        // Use the most direct route possible
        const xhr = new XMLHttpRequest();
        xhr.open('POST', `/direct-image-remove/${productId}`);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        
        xhr.onload = function() {
            console.log('Status:', xhr.status);
            console.log('Response:', xhr.responseText);
            
            try {
                const response = JSON.parse(xhr.responseText);
                
                if (xhr.status === 200 && response.success) {
                    console.log('Gallery image removed successfully');
                    if (imageElement) {
                        imageElement.remove();
                    }
                } else {
                    console.error('Error removing image:', response.message || 'Unknown error');
                    console.log('Trying to debug gallery images structure...');
                    debugGalleryImages(productId);
                    
                    // Restore the image visibility
                    if (imageElement) {
                        imageElement.style.opacity = '1';
                    }
                    alert(response.message || 'Failed to remove the image. Please try again.');
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                // Restore the image visibility
                if (imageElement) {
                    imageElement.style.opacity = '1';
                }
                alert('An error occurred while processing the response. Please try again.');
            }
        };
        
        xhr.onerror = function() {
            console.error('Network error occurred');
            // Restore the image visibility
            if (imageElement) {
                imageElement.style.opacity = '1';
            }
            alert('A network error occurred. Please try again.');
        };
        
        // Send the request with FormData
        xhr.send(formData);
    }
    
    function removeFeaturedImage(productId) {
        if (!confirm('Are you sure you want to remove the featured image? This action cannot be undone.')) {
            return;
        }
        
        // Get the CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Set up the form action
        const form = document.getElementById('remove-featured-image-form');
        form.action = `/admin/products/${productId}/featured-image`;
        
        // Submit the form
        form.submit();
    }

    // Set up form submission event to ensure current_image is properly set
    const form = document.getElementById('product-edit-form');
    form.addEventListener('submit', function(event) {
        // Prevent default form submission temporarily
        event.preventDefault();
        
        console.log('Form submission intercepted to check current_image field');
        
        // Get current image field and file input
        const currentImageField = document.getElementById('current-image-field');
        const fileInput = document.getElementById('featured_image');
        
        // If no new file is selected, force the current image path into the field
        if (fileInput.files.length === 0) {
            // Always set the current_image to the DB value stored in data-original
            const originalValue = currentImageField.getAttribute('data-original');
            if (originalValue) {
                currentImageField.value = originalValue;
                console.log('Forcing current_image to:', originalValue);
            } else {
                // Try to get the image from the displayed image
                const imgElement = document.querySelector('.relative.group .w-full.h-full.object-cover');
                if (imgElement && imgElement.src) {
                    try {
                        const fullPath = imgElement.src;
                        const storagePath = fullPath.split('/storage/')[1];
                        if (storagePath) {
                            currentImageField.value = storagePath;
                            console.log('Extracted current_image from image src:', storagePath);
                        }
                    } catch (e) {
                        console.error('Error extracting image path:', e);
                    }
                }
            }
        }
        
        // Force debug output right before submission
        console.log('Form submitted with values:');
        const formData = new FormData(form);
        for (const [key, value] of formData.entries()) {
            if (key === 'featured_image') {
                if (value.name) {
                    console.log(`${key}: File selected - ${value.name} (${value.type}, ${value.size} bytes)`);
                } else {
                    console.log(`${key}: No file selected`);
                }
            } else {
                console.log(`${key}: ${value}`);
            }
        }
        
        console.log('Hidden current_image field:', currentImageField.value);
        console.log('Hidden field in DOM:', document.getElementById('current-image-field') ? 
                     document.getElementById('current-image-field').value : 'field not found');
        
        // Now continue with form submission
        form.submit();
    });

    // Final backup script that runs just before the page finishes loading
    document.addEventListener('DOMContentLoaded', function() {
        // Backup script to ensure current_image field is always populated
        const updateButton = document.getElementById('update-product-button');
        if (updateButton) {
            updateButton.addEventListener('click', function() {
                const currentImageField = document.getElementById('current-image-field');
                if (currentImageField && (!currentImageField.value || currentImageField.value === '')) {
                    // Try all possible sources to get the image path
                    if (window.productCurrentImage) {
                        // Use backup from global variable
                        currentImageField.value = window.productCurrentImage;
                        console.log('BACKUP: Set image from global variable:', window.productCurrentImage);
                    } else if (currentImageField.getAttribute('data-original')) {
                        // Use data-original attribute
                        currentImageField.value = currentImageField.getAttribute('data-original');
                        console.log('BACKUP: Set image from data-original attribute:', currentImageField.getAttribute('data-original'));
                    }
                }
            });
        }
        
        // Monitor the field for any changes that might empty it
        const currentImageField = document.getElementById('current-image-field');
        if (currentImageField) {
            // Get initial value
            const originalValue = currentImageField.value || currentImageField.getAttribute('data-original');
            
            // Check every few seconds that the field still has a value
            setInterval(function() {
                if ((!currentImageField.value || currentImageField.value === '') && originalValue) {
                    currentImageField.value = originalValue;
                    console.log('MONITOR: Restored emptied current_image field to:', originalValue);
                }
            }, 1000);
        }
    });
</script>
@endpush 