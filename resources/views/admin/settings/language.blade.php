@extends('layouts.admin')

@section('content')
    <div class="pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold text-gray-900 mb-6">Language Settings</h1>
            
            @include('admin.partials.alerts')
            
            <div class="bg-white shadow-sm rounded-lg">
                <form action="{{ route('admin.settings.language.update') }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <!-- Language Switcher Toggle -->
                        <div>
                            <div class="flex items-center">
                                <input type="checkbox" name="enable_language_switcher" id="enable_language_switcher" 
                                    {{ isset($generalSettings->enable_language_switcher) && $generalSettings->enable_language_switcher ? 'checked' : '' }}
                                    class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary">
                                <label for="enable_language_switcher" class="ml-2 block text-sm text-gray-700">Enable Language Switcher</label>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Show the language switcher in the header of your website</p>
                        </div>
                        
                        <!-- Available Languages -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Available Languages</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($allLanguages as $code => $language)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="available_languages[]" id="lang_{{ $code }}" 
                                            value="{{ $code }}" 
                                            {{ in_array($code, $selectedLanguages ?? ['en']) ? 'checked' : '' }}
                                            class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary">
                                        <label for="lang_{{ $code }}" class="ml-2 block text-sm text-gray-700">
                                            {{ $language['name'] }} ({{ $language['native'] }})
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Select the languages you want to make available on your website</p>
                        </div>
                        
                        <!-- Default Language -->
                        <div>
                            <label for="default_language" class="block text-sm font-medium text-gray-700 mb-1">Default Language</label>
                            <select name="default_language" id="default_language" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                @foreach($allLanguages as $code => $language)
                                    <option value="{{ $code }}" 
                                        {{ isset($generalSettings->default_language) && $generalSettings->default_language == $code ? 'selected' : '' }}>
                                        {{ $language['name'] }} ({{ $language['native'] }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500">The language that will be used by default</p>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <div class="rounded-md bg-blue-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1 md:flex md:justify-between">
                                    <p class="text-sm text-blue-700">
                                        After changing language settings, you may need to clear the application cache.
                                    </p>
                                    <p class="mt-3 text-sm md:mt-0 md:ml-6">
                                        <button type="button" onclick="clearCache()" class="whitespace-nowrap font-medium text-blue-700 hover:text-blue-600">
                                            Clear Cache <span aria-hidden="true">&rarr;</span>
                                        </button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        function clearCache() {
            if (confirm('Are you sure you want to clear the application cache?')) {
                fetch('/admin/clear-cache', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Cache cleared successfully!');
                    } else {
                        alert('Failed to clear cache.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while clearing the cache.');
                });
            }
        }
    </script>
    @endpush
@endsection
