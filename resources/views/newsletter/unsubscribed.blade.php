@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-b from-primary to-secondary py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 p-10 bg-white rounded-xl shadow-lg">
        <div class="text-center">
            <img class="mx-auto h-20 w-auto" src="{{ asset('storage/logo.jpg') }}" alt="Celestial Cosmetics">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Unsubscribed</h2>
            <p class="mt-2 text-sm text-gray-600">
                You have been successfully unsubscribed from our newsletter.
            </p>
        </div>
        
        <div class="mt-8 space-y-6">
            <div class="bg-blue-50 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1 md:flex md:justify-between">
                        <p class="text-sm text-blue-700">
                            We're sorry to see you go. If you'd like to re-subscribe in the future, you can do so from our homepage.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Return to Homepage
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 