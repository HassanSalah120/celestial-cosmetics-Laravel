@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen bg-gradient-to-b from-indigo-900 to-black py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Animated stars background -->
    <div class="stars-container absolute inset-0 overflow-hidden">
        @for ($i = 0; $i < 50; $i++)
            <div class="star" style="
                top: {{ rand(0, 100) }}%; 
                left: {{ rand(0, 100) }}%; 
                width: {{ rand(1, 3) }}px; 
                height: {{ rand(1, 3) }}px; 
                animation-delay: {{ rand(0, 5000) }}ms;
                animation-duration: {{ rand(3000, 8000) }}ms;
            "></div>
        @endfor
    </div>

    <div class="z-10 text-center max-w-3xl mx-auto bg-black/30 backdrop-blur-sm p-8 rounded-2xl border border-purple-500/30 shadow-2xl">
        <h1 class="text-9xl font-extrabold text-center mb-4 text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-pink-500 to-red-500">422</h1>
        
        <h2 class="text-3xl font-bold text-white mb-6">Unprocessable Entity</h2>
        
        <p class="text-lg text-purple-200 mb-8">
            The data you submitted couldn't be processed.
            <br>Please check your form entries and try again.
        </p>
        
        @if(isset($exception) && $exception instanceof \Illuminate\Validation\ValidationException && $exception->errors())
            <div class="mb-8 p-4 bg-black/40 rounded-lg border border-purple-500/50 text-left">
                <h3 class="text-lg font-semibold text-white mb-2">Validation Errors:</h3>
                <ul class="list-disc pl-5 text-purple-200 space-y-1">
                    @foreach($exception->errors() as $field => $errors)
                        @foreach($errors as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-black bg-purple-300 hover:bg-purple-200 transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Go Back
            </a>
            <a href="{{ route('home') }}" class="inline-flex items-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-7-7v14" />
                </svg>
                Return Home
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stars-container {
        perspective: 500px;
    }
    
    .star {
        position: absolute;
        background-color: white;
        border-radius: 50%;
        opacity: 0.8;
        animation: twinkle linear infinite;
    }
    
    @keyframes twinkle {
        0% { opacity: 0; transform: translateZ(0); }
        10% { opacity: 1; transform: translateZ(-100px); }
        90% { opacity: 1; transform: translateZ(100px); }
        100% { opacity: 0; transform: translateZ(0); }
    }
</style>
@endpush 