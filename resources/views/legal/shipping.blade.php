@extends('layouts.app')

@section('meta_tags')
    @php
        use App\Helpers\TranslationHelper;
    @endphp
    <x-seo :title="$title"
           :description="$description"
           :keywords="$keywords"
           type="website" />
@endsection

@section('content')
<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-display text-primary text-center mb-6">{{ TranslationHelper::get('shipping_title', 'Shipping Policy') }}</h1>
        
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6 sm:p-8">
            <!-- Last updated info -->
            <div class="mb-6 text-sm text-gray-500 flex items-center">
                <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>{{ TranslationHelper::get('last_updated', 'Last Updated') }}: {{ $shippingSettings['shipping_last_updated']->value ?? now()->format('F d, Y') }}</span>
            </div>
            
            <!-- Main content -->
            <div class="prose max-w-none">
                {!! $shippingSettings['shipping_content']->value ?? TranslationHelper::get('shipping_default_content', 'Default shipping policy content goes here.') !!}
            </div>

            <!-- Contact section -->
            <div class="mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('contact') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark transition-colors">
                    {{ TranslationHelper::get('contact_us', 'Contact Us') }}
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 