@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 font-display">Edit Category: {{ $category->name }}</h2>
            <p class="mt-1 text-sm text-gray-600">Update the details of this category</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Categories
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-lg shadow-sm">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-8">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">There were errors with your submission:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Basic Information Section -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Basic Information</h3>
                <div class="mt-4 grid grid-cols-1 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Category Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <p class="mt-1 text-sm text-gray-500">Current slug: <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $category->slug }}</span></p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('description', $category->description) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Provide a detailed description of this category.</p>
                    </div>
                </div>
            </div>

            <!-- Image Section -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Category Image</h3>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">
                        Category Image
                    </label>
                    <div class="mt-2 flex items-center space-x-6">
                        <div class="flex-shrink-0 h-32 w-32 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                            <div id="preview-container" class="h-full w-full {{ $category->image ? '' : 'hidden' }}">
                                <img id="preview" class="h-full w-full object-cover rounded-lg" src="{{ $category->image ? asset('storage/' . $category->image) : '' }}">
                            </div>
                            <div id="placeholder" class="text-center p-4 {{ $category->image ? 'hidden' : '' }}">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                            </div>
                        </div>
                        <div>
                            <label for="image" class="cursor-pointer bg-primary hover:bg-primary-dark text-white py-2 px-4 rounded-md font-medium text-sm inline-flex items-center transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12"/>
                                </svg>
                                {{ $category->image ? 'Change Image' : 'Upload Image' }}
                                <input type="file" id="image" name="image" accept="image/*" class="hidden">
                            </label>
                            <button type="button" id="clear-image" class="mt-2 text-sm text-red-600 hover:text-red-800 {{ $category->image ? '' : 'hidden' }}">
                                Remove image
                            </button>
                            @if($category->image)
                                <p class="mt-2 text-xs text-gray-500">Current image will be replaced if you upload a new one.</p>
                            @endif
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Upload a category image to enhance visibility.</p>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 border-t pt-6">
                <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-light focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent">
                    Update Category
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('image');
        const previewContainer = document.getElementById('preview-container');
        const preview = document.getElementById('preview');
        const placeholder = document.getElementById('placeholder');
        const clearButton = document.getElementById('clear-image');
        const form = document.querySelector('form');
        
        // Handle file upload preview
        imageInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    clearButton.classList.remove('hidden');
                }
                
                reader.readAsDataURL(file);
            }
        });

        // Handle clear button click
        clearButton.addEventListener('click', function() {
            imageInput.value = '';
            previewContainer.classList.add('hidden');
            placeholder.classList.remove('hidden');
            
            // Add a hidden input to indicate image should be removed
            const removeImageInput = document.createElement('input');
            removeImageInput.type = 'hidden';
            removeImageInput.name = 'remove_image';
            removeImageInput.value = '1';
            form.appendChild(removeImageInput);
            
            clearButton.textContent = 'Image will be removed on save';
            clearButton.classList.add('cursor-not-allowed', 'opacity-50');
        });
    });
</script>
@endpush
@endsection 