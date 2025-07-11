@extends('layouts.app')

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="bg-background min-h-screen py-16">
    <div class="container mx-auto px-4">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-display text-primary mb-4">{{ __('messages.testimonials_page_title', ['default' => 'Our Customers\' Cosmic Experiences']) }}</h1>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">{{ __('messages.testimonials_page_subtitle', ['default' => 'Read what our community has to say about their journey with Celestial Cosmetics products']) }}</p>
        </div>
        
        <!-- Success Message -->
        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-8 rounded-lg" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Testimonials Grid -->
        <div class="max-w-7xl mx-auto mb-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($testimonials as $testimonial)
                <div class="bg-white rounded-2xl shadow-md overflow-hidden flex flex-col transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                    <div class="p-8 flex-grow flex flex-col">
                        <div class="flex items-center mb-6">
                            @if(isset($testimonial->avatar) && $testimonial->avatar)
                                <div class="mr-4">
                                    <img src="{{ asset('storage/' . $testimonial->avatar) }}" alt="{{ $testimonial->customer_name }}" class="w-16 h-16 rounded-full object-cover border-2 border-accent shadow-md">
                                </div>
                            @else
                                <div class="mr-4">
                                    <div class="w-16 h-16 bg-gradient-to-br from-accent to-accent-dark text-white rounded-full flex items-center justify-center text-xl font-bold shadow-md">
                                        {{ substr($testimonial->customer_name ?? 'User', 0, 1) }}
                                    </div>
                    </div>
                            @endif
                            <div>
                                <h4 class="font-bold text-lg text-gray-900">{{ $testimonial->customer_name }}</h4>
                                <p class="text-accent text-sm font-medium">
                                    {{ $testimonial->customer_role ?: __('messages.verified_customer', ['default' => 'Verified Customer']) }}
                                </p>
                                <div class="flex text-yellow-400 mt-1">
                            @for ($i = 1; $i <= 5; $i++)
                                @if($i <= $testimonial->rating)
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @else
                                            <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endif
                            @endfor
                        </div>
                            </div>
                    </div>
                        
                        <div class="flex-grow">
                    @if($testimonial->title)
                                <h3 class="text-xl font-semibold text-primary mb-3">{{ $testimonial->title }}</h3>
                    @endif
                            <blockquote class="text-gray-700 mb-6">
                        <p class="italic">"{{ $testimonial->message }}"</p>
                    </blockquote>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center">
                            <span class="text-xs text-gray-400">{{ $testimonial->created_at->format('M d, Y') }}</span>
                            @if($testimonial->is_featured)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary bg-opacity-10 text-primary">
                                    {{ is_rtl() ? 'مميز' : 'Featured' }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-lg text-gray-600">{{ __('messages.no_testimonials_yet', ['default' => 'No testimonials have been added yet. Be the first to share your experience!']) }}</p>
                </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $testimonials->links() }}
            </div>
        </div>
        
        <!-- Submit Testimonial Form -->
        @if(auth()->check())
            @if($canSubmitTestimonial)
            <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-8 md:p-12">
                    <h2 class="text-3xl font-display text-primary mb-2">{{ __('messages.share_your_experience', ['default' => 'Share Your Experience']) }}</h2>
                    <p class="text-gray-600 mb-8">{{ __('messages.testimonial_intro', ['default' => 'We\'d love to hear about your experience with our products. Your feedback helps us improve and inspires others on their cosmic beauty journey.']) }}</p>
                    
                    <form action="{{ route('reviews.submit') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                        @csrf
                        <!-- Hidden fields for user name and email -->
                        <input type="hidden" name="customer_name" value="{{ auth()->user()->name }}">
                        <input type="hidden" name="email" value="{{ auth()->user()->email }}">

                        <!-- Display user info as read-only text -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-2">
                            <p class="text-sm text-gray-600 mb-1">{{ __('messages.submitting_as', ['default' => 'Submitting as:']) }} <span class="font-medium text-gray-900">{{ auth()->user()->name }}</span></p>
                            <p class="text-sm text-gray-600">{{ __('messages.email', ['default' => 'Email:']) }} <span class="font-medium text-gray-900">{{ auth()->user()->email }}</span></p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.title_optional', ['default' => 'Title (Optional)']) }}</label>
                            <input type="text" name="title" id="title" class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-gray-300 rounded-md" value="{{ old('title') }}">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
                            @enderror
                            </div>
                            
                            <div>
                                <label for="customer_role" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.your_role', ['default' => 'How would you describe yourself? (Optional)']) }}</label>
                                <select name="customer_role" id="customer_role" class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="">{{ __('messages.select_role', ['default' => 'Select an option']) }}</option>
                                    <option value="Skincare Enthusiast" {{ old('customer_role') == 'Skincare Enthusiast' ? 'selected' : '' }}>{{ __('messages.skincare_enthusiast', ['default' => 'Skincare Enthusiast']) }}</option>
                                    <option value="Beauty Blogger" {{ old('customer_role') == 'Beauty Blogger' ? 'selected' : '' }}>{{ __('messages.beauty_blogger', ['default' => 'Beauty Blogger']) }}</option>
                                    <option value="Makeup Artist" {{ old('customer_role') == 'Makeup Artist' ? 'selected' : '' }}>{{ __('messages.makeup_artist', ['default' => 'Makeup Artist']) }}</option>
                                    <option value="Regular Customer" {{ old('customer_role') == 'Regular Customer' ? 'selected' : '' }}>{{ __('messages.regular_customer', ['default' => 'Regular Customer']) }}</option>
                                    <option value="First-time Customer" {{ old('customer_role') == 'First-time Customer' ? 'selected' : '' }}>{{ __('messages.first_time_customer', ['default' => 'First-time Customer']) }}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.your_experience', ['default' => 'Your Experience']) }}</label>
                            <textarea name="message" id="message" rows="4" class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-gray-300 rounded-md" required>{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
                            @enderror
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('messages.your_rating', ['default' => 'Your Rating']) }}</label>
                            <div class="flex items-center space-x-3">
                                @for ($i = 5; $i >= 1; $i--)
                                <div class="flex items-center">
                                    <input type="radio" id="rating-{{ $i }}" name="rating" value="{{ $i }}" class="h-4 w-4 text-accent focus:ring-accent border-gray-300" {{ old('rating', 5) == $i ? 'checked' : '' }}>
                                    <label for="rating-{{ $i }}" class="ml-1 block text-sm text-gray-700">{{ $i }}</label>
                                </div>
                                @endfor
                            </div>
                            @error('rating')
                                <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
                            @enderror
                            </div>
                            
                            <div>
                                <label for="avatar" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.profile_photo', ['default' => 'Profile Photo (Optional)']) }}</label>
                                <input type="file" name="avatar" id="avatar" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-accent file:text-white hover:file:bg-accent-dark">
                                <p class="mt-1 text-xs text-gray-500">{{ __('messages.photo_requirements', ['default' => 'JPG, PNG or GIF up to 1MB']) }}</p>
                                @error('avatar')
                                    <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div>
                            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent">
                                {{ __('messages.submit_testimonial', ['default' => 'Submit Your Testimonial']) }}
                            </button>
                        </div>
                        
                        <p class="text-xs text-gray-500 mt-4">
                            {{ __('messages.testimonial_agreement', ['default' => 'By submitting this testimonial, you agree that it may be displayed on our website after review. We reserve the right to edit for clarity or length.']) }}
                        </p>
                    </form>
                </div>
            </div>
            @else
            <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-8 md:p-12 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="text-3xl font-display text-primary mb-2">{{ __('messages.complete_order_to_review', ['default' => 'Complete an Order to Submit a Review']) }}</h2>
                    <p class="text-gray-600 mb-6">{{ __('messages.authentic_feedback_note', ['default' => 'We value authentic feedback from our customers. You\'ll be able to submit a testimonial once you\'ve received your first order.']) }}</p>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-accent hover:bg-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent transition-colors">
                        {{ __('messages.shop_our_products', ['default' => 'Shop Our Products']) }}
                        <svg class="ml-2 -mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
            @endif
        @else
        <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-8 md:p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <h2 class="text-3xl font-display text-primary mb-2">{{ __('messages.sign_in_to_share', ['default' => 'Sign In to Share Your Experience']) }}</h2>
                <p class="text-gray-600 mb-6">{{ __('messages.sign_in_to_submit', ['default' => 'Please sign in to your account to submit a testimonial. We\'d love to hear about your experience with our products.']) }}</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-accent hover:bg-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent transition-colors">
                        {{ __('messages.sign_in', ['default' => 'Sign In']) }}
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent transition-colors">
                        {{ __('messages.create_account', ['default' => 'Create Account']) }}
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection 