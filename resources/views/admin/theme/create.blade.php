@extends('layouts.admin')

@section('title', 'Create New Theme')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Create New Theme</h1>
        <a href="{{ route('admin.theme.index') }}" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300 transition-colors flex items-center">
            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Themes
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
            <p class="font-bold">Please fix the following errors:</p>
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-xl font-semibold">Theme Details</h2>
        </div>
        
        <div class="p-6">
            <form action="{{ route('admin.theme.store') }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Theme Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" required>
                    <p class="mt-1 text-sm text-gray-500">Choose a descriptive name for your theme</p>
                </div>
                
                <div class="mb-6">
                    <label for="group" class="block text-sm font-medium text-gray-700 mb-1">Theme Group</label>
                    <input type="text" name="group" id="group" value="{{ old('group') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary">
                    <p class="mt-1 text-sm text-gray-500">Optional - assign this theme to a group (e.g. Dark, Light, Seasonal, etc.)</p>
                </div>
                
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Color Palette</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Primary Colors -->
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <h4 class="font-medium text-gray-900 mb-3">Primary Colors</h4>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="primary" class="block text-sm font-medium text-gray-700 mb-1">Primary</label>
                                    <div class="flex">
                                        <input type="color" name="primary-color-picker" id="primary-color-picker" value="{{ old('primary', '#1f5964') }}" class="h-10 w-10 border-0 p-0 mr-2" onchange="document.getElementById('primary').value = this.value">
                                        <input type="text" name="primary" id="primary" value="{{ old('primary', '#1f5964') }}" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" required pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$" onchange="document.getElementById('primary-color-picker').value = this.value">
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="primary-light" class="block text-sm font-medium text-gray-700 mb-1">Primary Light</label>
                                    <div class="flex">
                                        <input type="color" name="primary-light-color-picker" id="primary-light-color-picker" value="{{ old('primary-light', '#2d6e7e') }}" class="h-10 w-10 border-0 p-0 mr-2" onchange="document.getElementById('primary-light').value = this.value">
                                        <input type="text" name="primary-light" id="primary-light" value="{{ old('primary-light', '#2d6e7e') }}" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" required pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$" onchange="document.getElementById('primary-light-color-picker').value = this.value">
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="primary-dark" class="block text-sm font-medium text-gray-700 mb-1">Primary Dark</label>
                                    <div class="flex">
                                        <input type="color" name="primary-dark-color-picker" id="primary-dark-color-picker" value="{{ old('primary-dark', '#174853') }}" class="h-10 w-10 border-0 p-0 mr-2" onchange="document.getElementById('primary-dark').value = this.value">
                                        <input type="text" name="primary-dark" id="primary-dark" value="{{ old('primary-dark', '#174853') }}" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" required pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$" onchange="document.getElementById('primary-dark-color-picker').value = this.value">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Secondary Colors -->
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <h4 class="font-medium text-gray-900 mb-3">Secondary Colors</h4>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="secondary" class="block text-sm font-medium text-gray-700 mb-1">Secondary</label>
                                    <div class="flex">
                                        <input type="color" name="secondary-color-picker" id="secondary-color-picker" value="{{ old('secondary', '#312e43') }}" class="h-10 w-10 border-0 p-0 mr-2" onchange="document.getElementById('secondary').value = this.value">
                                        <input type="text" name="secondary" id="secondary" value="{{ old('secondary', '#312e43') }}" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" required pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$" onchange="document.getElementById('secondary-color-picker').value = this.value">
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="secondary-light" class="block text-sm font-medium text-gray-700 mb-1">Secondary Light</label>
                                    <div class="flex">
                                        <input type="color" name="secondary-light-color-picker" id="secondary-light-color-picker" value="{{ old('secondary-light', '#423f5a') }}" class="h-10 w-10 border-0 p-0 mr-2" onchange="document.getElementById('secondary-light').value = this.value">
                                        <input type="text" name="secondary-light" id="secondary-light" value="{{ old('secondary-light', '#423f5a') }}" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" required pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$" onchange="document.getElementById('secondary-light-color-picker').value = this.value">
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="secondary-dark" class="block text-sm font-medium text-gray-700 mb-1">Secondary Dark</label>
                                    <div class="flex">
                                        <input type="color" name="secondary-dark-color-picker" id="secondary-dark-color-picker" value="{{ old('secondary-dark', '#272536') }}" class="h-10 w-10 border-0 p-0 mr-2" onchange="document.getElementById('secondary-dark').value = this.value">
                                        <input type="text" name="secondary-dark" id="secondary-dark" value="{{ old('secondary-dark', '#272536') }}" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" required pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$" onchange="document.getElementById('secondary-dark-color-picker').value = this.value">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Accent Colors -->
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <h4 class="font-medium text-gray-900 mb-3">Accent Colors</h4>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="accent" class="block text-sm font-medium text-gray-700 mb-1">Accent</label>
                                    <div class="flex">
                                        <input type="color" name="accent-color-picker" id="accent-color-picker" value="{{ old('accent', '#d4af37') }}" class="h-10 w-10 border-0 p-0 mr-2" onchange="document.getElementById('accent').value = this.value">
                                        <input type="text" name="accent" id="accent" value="{{ old('accent', '#d4af37') }}" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" required pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$" onchange="document.getElementById('accent-color-picker').value = this.value">
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="accent-light" class="block text-sm font-medium text-gray-700 mb-1">Accent Light</label>
                                    <div class="flex">
                                        <input type="color" name="accent-light-color-picker" id="accent-light-color-picker" value="{{ old('accent-light', '#dbba5d') }}" class="h-10 w-10 border-0 p-0 mr-2" onchange="document.getElementById('accent-light').value = this.value">
                                        <input type="text" name="accent-light" id="accent-light" value="{{ old('accent-light', '#dbba5d') }}" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" required pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$" onchange="document.getElementById('accent-light-color-picker').value = this.value">
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="accent-dark" class="block text-sm font-medium text-gray-700 mb-1">Accent Dark</label>
                                    <div class="flex">
                                        <input type="color" name="accent-dark-color-picker" id="accent-dark-color-picker" value="{{ old('accent-dark', '#b3932e') }}" class="h-10 w-10 border-0 p-0 mr-2" onchange="document.getElementById('accent-dark').value = this.value">
                                        <input type="text" name="accent-dark" id="accent-dark" value="{{ old('accent-dark', '#b3932e') }}" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" required pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$" onchange="document.getElementById('accent-dark-color-picker').value = this.value">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Live Preview -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Live Preview</h3>
                    
                    <div class="border rounded-lg overflow-hidden">
                        <div id="preview-header" class="h-12 flex items-center px-4" style="background-color: #1f5964;">
                            <div class="h-6 w-6 rounded-full bg-white mr-2"></div>
                            <div class="text-white font-medium">Header</div>
                        </div>
                        
                        <div class="p-4 flex flex-col md:flex-row gap-4">
                            <div class="w-full md:w-1/4">
                                <div id="preview-sidebar" class="border rounded p-3" style="background-color: #174853;">
                                    <div class="h-6 w-full rounded bg-white/20 mb-2"></div>
                                    <div class="h-4 w-full rounded bg-white/20 mb-2"></div>
                                    <div class="h-4 w-full rounded bg-white/20 mb-2"></div>
                                    <div id="preview-accent" class="h-8 w-full rounded flex items-center justify-center text-white text-xs" style="background-color: #d4af37;">
                                        Accent Button
                                    </div>
                                </div>
                            </div>
                            
                            <div class="w-full md:w-3/4">
                                <div class="border rounded p-4 bg-white">
                                    <div id="preview-title" class="text-lg font-medium mb-3" style="color: #1f5964;">Page Title</div>
                                    <div class="h-4 w-full rounded bg-gray-200 mb-2"></div>
                                    <div class="h-4 w-5/6 rounded bg-gray-200 mb-2"></div>
                                    <div class="h-4 w-4/6 rounded bg-gray-200 mb-4"></div>
                                    
                                    <div class="flex gap-2 mt-4">
                                        <div id="preview-primary-btn" class="px-3 py-1 rounded text-white text-sm" style="background-color: #1f5964;">Primary</div>
                                        <div id="preview-secondary-btn" class="px-3 py-1 rounded text-white text-sm" style="background-color: #312e43;">Secondary</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 pt-6">
                    <button type="submit" class="bg-primary text-white font-medium py-2.5 px-5 rounded-md hover:bg-primary-dark transition-colors flex items-center">
                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Create Theme
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Live preview functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Primary color updates
        document.getElementById('primary-color-picker').addEventListener('input', function() {
            const color = this.value;
            document.getElementById('primary').value = color;
            document.getElementById('preview-header').style.backgroundColor = color;
            document.getElementById('preview-title').style.color = color;
            document.getElementById('preview-primary-btn').style.backgroundColor = color;
        });
        
        document.getElementById('primary-dark-color-picker').addEventListener('input', function() {
            const color = this.value;
            document.getElementById('primary-dark').value = color;
            document.getElementById('preview-sidebar').style.backgroundColor = color;
        });
        
        // Secondary color updates
        document.getElementById('secondary-color-picker').addEventListener('input', function() {
            const color = this.value;
            document.getElementById('secondary').value = color;
            document.getElementById('preview-secondary-btn').style.backgroundColor = color;
        });
        
        // Accent color updates
        document.getElementById('accent-color-picker').addEventListener('input', function() {
            const color = this.value;
            document.getElementById('accent').value = color;
            document.getElementById('preview-accent').style.backgroundColor = color;
        });
    });
</script>
@endsection 