@extends('layouts.admin')

@section('content')
    <div class="pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold text-gray-900 mb-6">General Settings</h1>
            
            @include('admin.partials.alerts')
            
            <div class="bg-white shadow-sm rounded-lg">
                <form action="{{ route('admin.settings.general.update') }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Site Name -->
                        <div>
                            <label for="site_name" class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                            <input type="text" name="site_name" id="site_name" 
                                value="{{ $generalSettings->site_name ?? old('site_name', 'Celestial Cosmetics') }}" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        </div>
                        
                        <!-- Site Name (Arabic) -->
                        <div>
                            <label for="site_name_arabic" class="block text-sm font-medium text-gray-700 mb-1">Site Name (Arabic)</label>
                            <input type="text" name="site_name_arabic" id="site_name_arabic" 
                                value="{{ $generalSettings->site_name_arabic ?? old('site_name_arabic', 'سيليستيال كوزمتكس') }}" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        </div>
                        
                        <!-- Site Logo -->
                        <div class="md:col-span-2">
                            <label for="site_logo" class="block text-sm font-medium text-gray-700 mb-1">Site Logo</label>
                            <div class="flex items-start space-x-4">
                                @if(isset($generalSettings->site_logo) && $generalSettings->site_logo)
                                    <div class="flex-shrink-0">
                                        <img src="{{ asset('storage/' . $generalSettings->site_logo) }}" alt="Current Logo" class="h-16 w-auto object-contain">
                                    </div>
                                @endif
                                <div class="flex-grow">
                                    <input type="file" name="site_logo" id="site_logo" 
                                        accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                                    <p class="mt-1 text-sm text-gray-500">Recommended size: 200px × 50px</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Site Favicon -->
                        <div class="md:col-span-2">
                            <label for="site_favicon" class="block text-sm font-medium text-gray-700 mb-1">Site Favicon</label>
                            <div class="flex items-start space-x-4">
                                @if(isset($generalSettings->site_favicon) && $generalSettings->site_favicon)
                                    <div class="flex-shrink-0">
                                        <img src="{{ asset('storage/' . $generalSettings->site_favicon) }}" alt="Current Favicon" class="h-10 w-auto object-contain">
                                    </div>
                                @endif
                                <div class="flex-grow">
                                    <input type="file" name="site_favicon" id="site_favicon" 
                                        accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                                    <p class="mt-1 text-sm text-gray-500">Recommended size: 32px × 32px, .ico or .png format</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Social Login Toggle -->
                        <div>
                            <div class="flex items-center">
                                <input type="checkbox" name="enable_social_login" id="enable_social_login" 
                                    {{ isset($generalSettings->enable_social_login) && $generalSettings->enable_social_login ? 'checked' : '' }}
                                    class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary">
                                <label for="enable_social_login" class="ml-2 block text-sm text-gray-700">Enable Social Login</label>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Allow users to sign in with social media accounts</p>
                        </div>
                        
                        <!-- Registration Toggle -->
                        <div>
                            <div class="flex items-center">
                                <input type="checkbox" name="enable_registration" id="enable_registration" 
                                    {{ isset($generalSettings->enable_registration) && $generalSettings->enable_registration ? 'checked' : '' }}
                                    class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary">
                                <label for="enable_registration" class="ml-2 block text-sm text-gray-700">Enable Registration</label>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Allow new users to register on the site</p>
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
@endsection
