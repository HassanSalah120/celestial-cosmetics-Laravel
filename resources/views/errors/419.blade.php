@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12 bg-gradient-to-b from-primary to-primary-light relative overflow-hidden">
    <!-- Animated stars background -->
    <div class="absolute inset-0 overflow-hidden">
        @for ($i = 0; $i < 50; $i++)
            <div class="star" style="
                top: {{ rand(0, 100) }}%; 
                left: {{ rand(0, 100) }}%; 
                width: {{ rand(1, 4) }}px; 
                height: {{ rand(1, 4) }}px; 
                animation-delay: {{ rand(0, 5000) }}ms;
                animation-duration: {{ rand(3000, 8000) }}ms;
            "></div>
        @endfor
    </div>

    <div class="relative z-10 text-center max-w-lg bg-white/10 backdrop-blur-md p-8 sm:p-12 rounded-xl shadow-2xl border border-white/20">
        <h1 class="font-display text-7xl sm:text-9xl font-bold text-accent mb-4">419</h1>
        <h2 class="font-display text-xl sm:text-2xl font-medium text-white mb-6">Page Expired</h2>
        <p class="text-white/80 mb-8">Your celestial session has drifted into a distant galaxy. Please refresh the page to continue your cosmic journey.</p>
        
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center px-5 py-3 border border-white/30 rounded-md text-sm font-medium text-white hover:bg-white/10 transition-all duration-200 w-full sm:w-auto">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Go Back
            </a>
            <a href="{{ url()->current() }}" class="{{ Settings::get('header_btn_bg_color', 'bg-accent') }} inline-flex items-center justify-center px-5 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:{{ Settings::get('header_btn_hover_color', 'opacity-90') }} transition-all duration-200 w-full sm:w-auto">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh Page
            </a>
        </div>
    </div>
</div>

<style>
    .star {
        position: absolute;
        background-color: white;
        border-radius: 50%;
        opacity: 0.6;
        animation: twinkle linear infinite;
    }
    
    @keyframes twinkle {
        0% { opacity: 0.2; }
        50% { opacity: 0.8; }
        100% { opacity: 0.2; }
    }
</style>
@endsection 