@extends('layouts.admin')

@section('title', 'Edit Testimonial')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 font-display">Edit Testimonial</h1>
        <a href="{{ route('admin.testimonials.index') }}" class="bg-gray-100 text-gray-700 py-2.5 px-5 rounded-md hover:bg-gray-200 transition-colors duration-200 flex items-center border border-gray-300">
            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Testimonials
        </a>
    </div>

    @if($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
        <div class="flex items-start">
            <svg class="h-5 w-5 mr-2 mt-0.5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="font-bold">Please fix the following errors:</p>
                <ul class="list-disc ml-8 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
        <form action="{{ route('admin.testimonials.update', $testimonial) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Customer Information -->
                <div class="space-y-6">
                    <h2 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">Customer Information</h2>
                    
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Customer Name <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', $testimonial->customer_name) }}" required class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="customer_name_ar" class="block text-sm font-medium text-gray-700 mb-1">Customer Name (Arabic)</label>
                        <input type="text" name="customer_name_ar" id="customer_name_ar" value="{{ old('customer_name_ar', $testimonial->customer_name_ar) }}" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $testimonial->email) }}" required class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="customer_role" class="block text-sm font-medium text-gray-700 mb-1">Customer Role</label>
                        <input type="text" name="customer_role" id="customer_role" value="{{ old('customer_role', $testimonial->customer_role) }}" placeholder="e.g. Beauty Blogger, Loyal Customer" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="customer_role_ar" class="block text-sm font-medium text-gray-700 mb-1">Customer Role (Arabic)</label>
                        <input type="text" name="customer_role_ar" id="customer_role_ar" value="{{ old('customer_role_ar', $testimonial->customer_role_ar) }}" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="avatar" class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                        
                        @if($testimonial->avatar)
                            <div class="mb-3 flex items-center">
                                <img src="{{ asset('storage/' . $testimonial->avatar) }}" alt="{{ $testimonial->customer_name }}" class="h-16 w-16 rounded-full object-cover mr-3 border border-gray-200 shadow-sm">
                                <span class="text-sm text-gray-500">Current profile photo</span>
                            </div>
                        @endif
                        
                        <input type="file" name="avatar" id="avatar" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-accent file:text-white hover:file:bg-accent-dark">
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG or GIF up to 1MB. Leave empty to keep current image.</p>
                    </div>
                </div>
                
                <!-- Testimonial Content -->
                <div class="space-y-6">
                    <h2 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">Testimonial Content</h2>
                    
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $testimonial->title) }}" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="title_ar" class="block text-sm font-medium text-gray-700 mb-1">Title (Arabic)</label>
                        <input type="text" name="title_ar" id="title_ar" value="{{ old('title_ar', $testimonial->title_ar) }}" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message <span class="text-red-500">*</span></label>
                        <textarea name="message" id="message" rows="5" required class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">{{ old('message', $testimonial->message) }}</textarea>
                    </div>
                    
                    <div>
                        <label for="message_ar" class="block text-sm font-medium text-gray-700 mb-1">Message (Arabic)</label>
                        <textarea name="message_ar" id="message_ar" rows="5" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">{{ old('message_ar', $testimonial->message_ar) }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating <span class="text-red-500">*</span></label>
                        <div class="flex items-center space-x-4">
                            @for($i = 5; $i >= 1; $i--)
                            <div class="flex items-center">
                                <input type="radio" id="rating-{{ $i }}" name="rating" value="{{ $i }}" class="h-4 w-4 text-accent focus:ring-accent border-gray-300" {{ old('rating', $testimonial->rating) == $i ? 'checked' : '' }}>
                                <label for="rating-{{ $i }}" class="ml-1.5 block text-sm text-gray-700">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-6 pt-4">
                        <div class="flex items-center h-5">
                            <input type="hidden" name="is_approved" value="0">
                            <input id="is_approved" name="is_approved" type="checkbox" value="1" class="focus:ring-accent h-4 w-4 text-accent border-gray-300 rounded" {{ old('is_approved', $testimonial->is_approved) ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_approved" class="font-medium text-gray-700">Approved</label>
                            <p class="text-gray-500">Testimonial will be visible on the website</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-6">
                        <div class="flex items-center h-5">
                            <input type="hidden" name="is_featured" value="0">
                            <input id="is_featured" name="is_featured" type="checkbox" value="1" class="focus:ring-accent h-4 w-4 text-accent border-gray-300 rounded" {{ old('is_featured', $testimonial->is_featured) ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_featured" class="font-medium text-gray-700">Featured</label>
                            <p class="text-gray-500">Testimonial will appear on the homepage</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="pt-6 border-t border-gray-200 flex justify-between">
                <button type="button" onclick="document.getElementById('delete-form').submit();" class="bg-red-600 hover:bg-red-700 text-white py-2.5 px-6 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Testimonial
                </button>
                
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white py-2.5 px-8 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Testimonial
                </button>
            </div>
        </form>

        <!-- Delete Form (outside the main form) -->
        <form id="delete-form" action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST" class="hidden" onsubmit="return confirm('Are you sure you want to delete this testimonial?');">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection 