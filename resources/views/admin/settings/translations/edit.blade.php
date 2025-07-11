@extends('layouts.admin')

@section('title', 'Edit Setting Translations')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-4">
        <a href="{{ route('admin.settings.translations.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-indigo-600">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Translations
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h1 class="text-xl font-semibold text-gray-900">Edit Translations for: {{ $setting->display_name }}</h1>
            <p class="mt-1 text-sm text-gray-600">
                Key: <span class="font-mono text-indigo-600">{{ $setting->key }}</span> | 
                Group: <span class="font-semibold">{{ ucfirst($setting->group) }}</span>
            </p>
        </div>
        
        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-2">Default Value</h2>
                <div class="bg-gray-50 p-4 border border-gray-200 rounded-md mb-6">
                    @if($setting->type === 'textarea')
                        <pre class="whitespace-pre-wrap">{{ $setting->value }}</pre>
                    @else
                        {{ $setting->value }}
                    @endif
                </div>
                
                <h2 class="text-lg font-semibold mb-2">Translations</h2>
                <form action="{{ route('admin.settings.translations.update', $setting) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        @foreach($availableLocales as $locale)
                            @if($locale !== config('app.locale'))
                                <div class="p-4 border border-gray-200 rounded-md">
                                    <div class="flex items-center mb-2">
                                        <label for="translations[{{ $locale }}]" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ strtoupper($locale) }}
                                        </label>
                                        @if(isset($translations[$locale]))
                                            <span class="ml-2 text-xs text-gray-500">
                                                Last updated: {{ $translations[$locale]->updated_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    @if($setting->type === 'textarea')
                                        <textarea 
                                            name="translations[{{ $locale }}]" 
                                            id="translations[{{ $locale }}]" 
                                            rows="5" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">{{ isset($translations[$locale]) ? $translations[$locale]->value : '' }}</textarea>
                                    @else
                                        <input 
                                            type="text" 
                                            name="translations[{{ $locale }}]" 
                                            id="translations[{{ $locale }}]" 
                                            value="{{ isset($translations[$locale]) ? $translations[$locale]->value : '' }}" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
                                    @endif
                                    
                                    @if(isset($translations[$locale]))
                                        <div class="mt-2 flex justify-end">
                                            <a href="{{ route('admin.settings.translations.destroy', [$setting, $locale]) }}" 
                                               onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this translation?')) { document.getElementById('delete-form-{{ $locale }}').submit(); }" 
                                               class="text-red-600 hover:text-red-900 text-xs">
                                                Delete this translation
                                            </a>
                                            
                                            <form id="delete-form-{{ $locale }}" action="{{ route('admin.settings.translations.destroy', [$setting, $locale]) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save Translations
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 