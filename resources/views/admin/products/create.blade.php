@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 font-display">Create New Product</h2>
            <p class="mt-1 text-sm text-gray-600">Add a new product to your store</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
            </svg>
            Back to Products
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
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

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category_id" id="category_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}</span>
                        </div>
                        <input type="number" name="price" id="price" value="{{ old('price') }}" required step="0.01" min="0"
                            class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>
                </div>

                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700">Stock Quantity</label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" required min="0"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="4" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('description') }}</textarea>
            </div>

            <!-- Additional Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="ingredients" class="block text-sm font-medium text-gray-700">Ingredients</label>
                    <textarea name="ingredients" id="ingredients" rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('ingredients') }}</textarea>
                </div>

                <div>
                    <label for="how_to_use" class="block text-sm font-medium text-gray-700">How to Use</label>
                    <textarea name="how_to_use" id="how_to_use" rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('how_to_use') }}</textarea>
                </div>
            </div>

            <!-- Featured Image Upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Featured Image</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                    <div class="space-y-1 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="featured_image" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary">
                                    <span>Upload a file</span>
                                    <input id="featured_image" name="featured_image" type="file" class="sr-only" accept="image/*" required>
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                        </div>
                        <div id="featured-image-preview" class="hidden mt-4">
                            <img src="#" alt="Featured Image Preview" class="mx-auto h-32 w-32 object-cover rounded-lg">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gallery Images Upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Product Gallery</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                    <div class="space-y-1 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="gallery_images" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary">
                                    <span>Upload files</span>
                                    <input id="gallery_images" name="gallery_images[]" type="file" class="sr-only" accept="image/*" multiple>
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">Upload up to 5 images (PNG, JPG, GIF up to 10MB each)</p>
                        </div>
                        <div id="gallery-images-preview" class="hidden mt-4">
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Featured Toggle -->
            <div class="flex items-center">
                <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                    class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                <label for="is_featured" class="ml-2 block text-sm text-gray-700">
                    Feature this product on the homepage
                </label>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6">
                <button type="button" onclick="window.history.back()"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Cancel
                </button>
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Create Product
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Featured Image preview functionality
    document.getElementById('featured_image').addEventListener('change', function(e) {
        const preview = document.getElementById('featured-image-preview');
        const previewImg = preview.querySelector('img');
        const file = e.target.files[0];

        if (file) {
            previewImg.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    });

    // Gallery Images preview functionality
    document.getElementById('gallery_images').addEventListener('change', function(e) {
        const preview = document.getElementById('gallery-images-preview');
        const previewGrid = preview.querySelector('.grid');
        const files = e.target.files;

        // Clear previous previews
        previewGrid.innerHTML = '';

        if (files.length > 0) {
            Array.from(files).forEach((file, index) => {
                if (index < 5) { // Limit to 5 images
                    const imgContainer = document.createElement('div');
                    imgContainer.className = 'relative';
                    
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.className = 'h-24 w-full object-cover rounded-lg';
                    img.alt = `Gallery Image ${index + 1}`;
                    
                    imgContainer.appendChild(img);
                    previewGrid.appendChild(imgContainer);
                }
            });
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    });

    // Drag and drop functionality for featured image
    const featuredDropZone = document.querySelector('form');
    const featuredFileInput = document.getElementById('featured_image');
    const galleryFileInput = document.getElementById('gallery_images');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        featuredDropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        featuredDropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        featuredDropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        const dropZone = e.target.closest('.border-dashed');
        if (dropZone) {
            dropZone.classList.add('border-primary', 'bg-primary-50');
        }
    }

    function unhighlight(e) {
        const dropZone = e.target.closest('.border-dashed');
        if (dropZone) {
            dropZone.classList.remove('border-primary', 'bg-primary-50');
        }
    }

    featuredDropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dropZone = e.target.closest('.border-dashed');
        if (!dropZone) return;

        const dt = e.dataTransfer;
        const files = dt.files;

        if (dropZone.contains(document.getElementById('featured_image'))) {
            // Handle featured image drop
            featuredFileInput.files = new FileList([files[0]]);
            const event = new Event('change');
            featuredFileInput.dispatchEvent(event);
        } else if (dropZone.contains(document.getElementById('gallery_images'))) {
            // Handle gallery images drop
            galleryFileInput.files = files;
            const event = new Event('change');
            galleryFileInput.dispatchEvent(event);
        }
    }
</script>
@endpush
@endsection 