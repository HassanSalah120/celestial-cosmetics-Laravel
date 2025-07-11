@extends('layouts.admin')

@section('title', 'Footer Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 font-display">Footer Management</h2>
            <p class="mt-1 text-sm text-gray-600">Customize your website's footer sections, links and settings</p>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Footer Sections Management -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Footer Sections</h3>
                <button type="button" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-dark active:bg-primary-dark focus:outline-none focus:border-primary-dark focus:ring ring-primary/30 disabled:opacity-25 transition-colors duration-200" 
                    onclick="document.getElementById('add-section-modal').classList.remove('hidden')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New Section
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sections as $section)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $section->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                    {{ ucfirst($section->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $section->sort_order }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $section->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $section->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <button type="button" class="text-blue-600 hover:text-blue-800 transition-colors" 
                                        onclick="editSection('{{ $section->id }}', '{{ $section->title }}', '{{ $section->title_ar }}', '{{ $section->type }}', {{ $section->sort_order }}, {{ $section->is_active ? 'true' : 'false' }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <form action="{{ route('admin.footer.sections.destroy', $section) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this section? This will also delete all links in this section.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No footer sections found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Links Management -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Footer Links</h3>
                <button type="button" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-dark active:bg-primary-dark focus:outline-none focus:border-primary-dark focus:ring ring-primary/30 disabled:opacity-25 transition-colors duration-200" 
                    onclick="document.getElementById('add-link-modal').classList.remove('hidden')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New Link
                </button>
            </div>

            @forelse($sections->where('type', 'links') as $section)
                <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-800 mb-3 border-b pb-2">{{ $section->title }}</h4>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($section->links as $link)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $link->title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $link->url }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $link->sort_order }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-3">
                                            <button type="button" class="text-blue-600 hover:text-blue-800 transition-colors" 
                                                onclick="editLink('{{ $link->id }}', '{{ $link->column_id }}', '{{ $link->title }}', '{{ $link->url }}', {{ $link->sort_order }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <form action="{{ route('admin.footer.links.destroy', $link) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this link?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No links found in this section.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="flex items-center justify-center p-6 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No link sections found. Create a section with type "links" first.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Footer Settings -->
        <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-2">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Footer Settings</h3>
            
            <form action="{{ route('admin.footer.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label for="copyright_text" class="block text-sm font-medium text-gray-700 mb-1">Copyright Text</label>
                            <input type="text" name="copyright_text" id="copyright_text" 
                                value="{{ $settings['copyright_text']->value ?? ('© ' . date('Y') . ' Celestial Cosmetics. All rights reserved.') }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        </div>
                        
                        <div>
                            <label for="copyright_text_ar" class="block text-sm font-medium text-gray-700 mb-1">Copyright Text (Arabic)</label>
                            <input type="text" name="copyright_text_ar" id="copyright_text_ar" 
                                value="{{ $settings['copyright_text_ar']->value ?? ('© ' . date('Y') . ' سيليستيال كوزمتكس. جميع الحقوق محفوظة.') }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        </div>
                        
                        <div>
                            <label for="tagline" class="block text-sm font-medium text-gray-700 mb-1">Footer Tagline</label>
                            <input type="text" name="tagline" id="tagline" 
                                value="{{ $settings['tagline']->value ?? 'Elevate your beauty with our cosmic collection of premium cosmetics.' }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        </div>
                        
                        <div>
                            <label for="tagline_ar" class="block text-sm font-medium text-gray-700 mb-1">Footer Tagline (Arabic)</label>
                            <input type="text" name="tagline_ar" id="tagline_ar" 
                                value="{{ $settings['tagline_ar']->value ?? 'ارتقِ بجمالك مع مجموعتنا الكونية من مستحضرات التجميل الفاخرة.' }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="facebook_url" class="block text-sm font-medium text-gray-700 mb-1">Facebook URL</label>
                            <input type="url" name="facebook_url" id="facebook_url" 
                                value="{{ $settings['facebook_url']->value ?? 'https://facebook.com' }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        </div>
                        
                        <div>
                            <label for="twitter_url" class="block text-sm font-medium text-gray-700 mb-1">Twitter URL</label>
                            <input type="url" name="twitter_url" id="twitter_url" 
                                value="{{ $settings['twitter_url']->value ?? 'https://twitter.com' }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        </div>
                        
                        <div>
                            <label for="instagram_url" class="block text-sm font-medium text-gray-700 mb-1">Instagram URL</label>
                            <input type="url" name="instagram_url" id="instagram_url" 
                                value="{{ $settings['instagram_url']->value ?? 'https://instagram.com' }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" name="show_newsletter" id="show_newsletter" 
                                value="1" {{ ($settings['show_newsletter']->value ?? '1') == '1' ? 'checked' : '' }} 
                                class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                            <label for="show_newsletter" class="ml-2 block text-sm text-gray-700">Show Newsletter</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" name="show_social_icons" id="show_social_icons" 
                                value="1" {{ ($settings['show_social_icons']->value ?? '1') == '1' ? 'checked' : '' }} 
                                class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                            <label for="show_social_icons" class="ml-2 block text-sm text-gray-700">Show Social Icons</label>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-dark active:bg-primary-dark focus:outline-none focus:border-primary-dark focus:ring ring-primary/30 disabled:opacity-25 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Section Modal -->
<div id="add-section-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Add New Footer Section</h3>
            <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('add-section-modal').classList.add('hidden')">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form action="{{ route('admin.footer.sections.store') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" id="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
                
                <div>
                    <label for="title_ar" class="block text-sm font-medium text-gray-700 mb-1">Title (Arabic)</label>
                    <input type="text" name="title_ar" id="title_ar" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
                
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" id="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="links">Links</option>
                        <option value="newsletter">Newsletter</option>
                        <option value="contact">Contact</option>
                        <option value="social">Social</option>
                    </select>
                </div>
                
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" id="sort_order" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700">Active</label>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md mr-2 transition-colors duration-200" onclick="document.getElementById('add-section-modal').classList.add('hidden')">
                    Cancel
                </button>
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-md transition-colors duration-200">
                    Add Section
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Section Modal -->
<div id="edit-section-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Edit Footer Section</h3>
            <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('edit-section-modal').classList.add('hidden')">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="edit-section-form" action="" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label for="edit_title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" id="edit_title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
                
                <div>
                    <label for="edit_title_ar" class="block text-sm font-medium text-gray-700 mb-1">Title (Arabic)</label>
                    <input type="text" name="title_ar" id="edit_title_ar" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
                
                <div>
                    <label for="edit_type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" id="edit_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="links">Links</option>
                        <option value="newsletter">Newsletter</option>
                        <option value="contact">Contact</option>
                        <option value="social">Social</option>
                    </select>
                </div>
                
                <div>
                    <label for="edit_sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" id="edit_sort_order" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                    <label for="edit_is_active" class="ml-2 block text-sm text-gray-700">Active</label>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md mr-2 transition-colors duration-200" onclick="document.getElementById('edit-section-modal').classList.add('hidden')">
                    Cancel
                </button>
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-md transition-colors duration-200">
                    Update Section
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Link Modal -->
<div id="add-link-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Add New Footer Link</h3>
            <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('add-link-modal').classList.add('hidden')">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form action="{{ route('admin.footer.links.store') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="column_id" class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                    <select name="column_id" id="column_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @foreach($sections->where('type', 'links') as $section)
                            <option value="{{ $section->id }}">{{ $section->title }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="link_title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" id="link_title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
                
                <div>
                    <label for="url" class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                    <input type="text" name="url" id="url" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
                
                <div>
                    <label for="link_sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" id="link_sort_order" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md mr-2 transition-colors duration-200" onclick="document.getElementById('add-link-modal').classList.add('hidden')">
                    Cancel
                </button>
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-md transition-colors duration-200">
                    Add Link
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Link Modal -->
<div id="edit-link-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Edit Footer Link</h3>
            <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('edit-link-modal').classList.add('hidden')">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="edit-link-form" action="" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label for="edit_column_id" class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                    <select name="column_id" id="edit_column_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @foreach($sections->where('type', 'links') as $section)
                            <option value="{{ $section->id }}">{{ $section->title }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="edit_link_title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" id="edit_link_title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
                
                <div>
                    <label for="edit_url" class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                    <input type="text" name="url" id="edit_url" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
                
                <div>
                    <label for="edit_link_sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" id="edit_link_sort_order" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md mr-2 transition-colors duration-200" onclick="document.getElementById('edit-link-modal').classList.add('hidden')">
                    Cancel
                </button>
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-md transition-colors duration-200">
                    Update Link
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function editSection(id, title, title_ar, type, sort_order, is_active) {
        document.getElementById('edit_title').value = title;
        document.getElementById('edit_title_ar').value = title_ar;
        document.getElementById('edit_type').value = type;
        document.getElementById('edit_sort_order').value = sort_order;
        document.getElementById('edit_is_active').checked = is_active;
        
        document.getElementById('edit-section-form').action = `{{ url('admin/footer/sections') }}/${id}`;
        document.getElementById('edit-section-modal').classList.remove('hidden');
    }
    
    function editLink(id, column_id, title, url, sort_order) {
        document.getElementById('edit_column_id').value = column_id;
        document.getElementById('edit_link_title').value = title;
        document.getElementById('edit_url').value = url;
        document.getElementById('edit_link_sort_order').value = sort_order;
        
        document.getElementById('edit-link-form').action = `{{ url('admin/footer/links') }}/${id}`;
        document.getElementById('edit-link-modal').classList.remove('hidden');
    }
</script>
@endpush 