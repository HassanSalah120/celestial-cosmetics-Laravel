@extends('layouts.admin')

@section('title', 'Add New Testimonial')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 font-display">Add New Testimonial</h1>
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
        <form action="{{ route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Customer Information -->
                <div class="space-y-6">
                    <h2 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">Customer Information</h2>
                    
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Customer Name <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="customer_name_ar" class="block text-sm font-medium text-gray-700 mb-1">Customer Name (Arabic)</label>
                        <input type="text" name="customer_name_ar" id="customer_name_ar" value="{{ old('customer_name_ar') }}" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="customer_role" class="block text-sm font-medium text-gray-700 mb-1">Customer Role</label>
                        <input type="text" name="customer_role" id="customer_role" value="{{ old('customer_role') }}" placeholder="e.g. Beauty Blogger, Loyal Customer" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="customer_role_ar" class="block text-sm font-medium text-gray-700 mb-1">Customer Role (Arabic)</label>
                        <input type="text" name="customer_role_ar" id="customer_role_ar" value="{{ old('customer_role_ar') }}" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="avatar" class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                        <input type="file" name="avatar" id="avatar" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-accent file:text-white hover:file:bg-accent-dark">
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG or GIF up to 1MB</p>
                    </div>
                </div>
                
                <!-- Testimonial Content -->
                <div class="space-y-6">
                    <h2 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">Testimonial Content</h2>
                    
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="title_ar" class="block text-sm font-medium text-gray-700 mb-1">Title (Arabic)</label>
                        <input type="text" name="title_ar" id="title_ar" value="{{ old('title_ar') }}" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message <span class="text-red-500">*</span></label>
                        <textarea name="message" id="message" rows="5" required class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">{{ old('message') }}</textarea>
                    </div>
                    
                    <div>
                        <label for="message_ar" class="block text-sm font-medium text-gray-700 mb-1">Message (Arabic)</label>
                        <textarea name="message_ar" id="message_ar" rows="5" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">{{ old('message_ar') }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating <span class="text-red-500">*</span></label>
                        <div class="flex items-center space-x-4">
                            @for($i = 5; $i >= 1; $i--)
                            <div class="flex items-center">
                                <input type="radio" id="rating-{{ $i }}" name="rating" value="{{ $i }}" class="h-4 w-4 text-accent focus:ring-accent border-gray-300" {{ old('rating', 5) == $i ? 'checked' : '' }}>
                                <label for="rating-{{ $i }}" class="ml-1.5 block text-sm text-gray-700">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-6 pt-4">
                        <div class="flex items-center h-5">
                            <input type="hidden" name="is_approved" value="0">
                            <input id="is_approved" name="is_approved" type="checkbox" value="1" class="focus:ring-accent h-4 w-4 text-accent border-gray-300 rounded" {{ old('is_approved') ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_approved" class="font-medium text-gray-700">Approved</label>
                            <p class="text-gray-500">Testimonial will be visible on the website</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-6">
                        <div class="flex items-center h-5">
                            <input type="hidden" name="is_featured" value="0">
                            <input id="is_featured" name="is_featured" type="checkbox" value="1" class="focus:ring-accent h-4 w-4 text-accent border-gray-300 rounded" {{ old('is_featured') ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_featured" class="font-medium text-gray-700">Featured</label>
                            <p class="text-gray-500">Testimonial will appear on the homepage</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="pt-6 border-t border-gray-200 flex justify-end">
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white py-2.5 px-8 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Create Testimonial
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 