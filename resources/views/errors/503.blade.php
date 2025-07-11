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
        <h1 class="font-display text-7xl sm:text-9xl font-bold text-accent mb-4">503</h1>
        <h2 class="font-display text-xl sm:text-2xl font-medium text-white mb-6">We're Preparing Something Beautiful</h2>
        <div class="flex justify-center mb-6">
            <svg class="w-24 h-24 text-white/50 animate-spin-slow" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <p class="text-white/80 mb-8">Our celestial artisans are polishing the stars in our cosmic collection. The universe will be open for exploration again soon.</p>
        
        <div class="flex flex-col items-center justify-center gap-4">
            <p class="text-white/60 text-sm">Please check back in a few moments</p>
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
    
    .animate-spin-slow {
        animation: spin 3s linear infinite;
    }
    
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
</style>
@endsection 