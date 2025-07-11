@extends('layouts.admin')

@php
use Illuminate\Support\Str;
@endphp

@section('title', 'Category SEO Management')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-semibold text-gray-900 mt-4">Category SEO Management</h1>
    <nav class="flex mb-5" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-primary hover:text-primary-dark">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('admin.seo.index') }}" class="ml-1 text-sm font-medium text-primary hover:text-primary-dark md:ml-2">SEO Management</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Category SEO</span>
                </div>
            </li>
        </ol>
    </nav>
    
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex items-center">
            <svg class="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
            </svg>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Category SEO Settings</h3>
        </div>
        <div class="px-4 py-5 sm:p-6">
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            <p class="text-gray-600 mb-4">Manage SEO settings for product categories in your store. Optimizing category pages can significantly improve your site's SEO performance and help customers find your products.</p>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="categoriesTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meta Title</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meta Description</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meta Keywords</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($categories as $category)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($category->image)
                                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="h-10 w-10 rounded-md object-cover flex-shrink-0">
                                        @else
                                            <div class="bg-gray-100 h-10 w-10 rounded-md flex items-center justify-center flex-shrink-0">
                                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $category->slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($category->meta_title)
                                        <span class="text-green-600">{{ Str::limit($category->meta_title, 40) }}</span>
                                    @else
                                        <span class="text-gray-500 italic">Using default: {{ Str::limit($category->seo_title, 40) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($category->meta_description)
                                        <span class="text-green-600">{{ Str::limit($category->meta_description, 60) }}</span>
                                    @else
                                        <span class="text-gray-500 italic">Using default: {{ Str::limit($category->seo_description, 60) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($category->meta_keywords)
                                        <span class="text-green-600">{{ Str::limit($category->meta_keywords, 40) }}</span>
                                    @else
                                        <span class="text-gray-500 italic">Not set</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.seo.edit-category', $category->id) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit SEO
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex items-center">
            <svg class="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Category SEO Best Practices</h3>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-base font-medium text-gray-800 mb-2">Category Meta Titles</h4>
                    <ul class="space-y-1 text-sm text-gray-600 list-disc pl-5">
                        <li>Include the category name and main products/keywords.</li>
                        <li>Describe what customers can find in this category.</li>
                        <li>Include your brand name if space allows.</li>
                        <li>Keep titles within 50-60 characters for optimal display.</li>
                        <li>Example: "Celestial Jewelry Collection | Moon & Star Designs | Celestial Cosmetics"</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-base font-medium text-gray-800 mb-2">Category Meta Descriptions</h4>
                    <ul class="space-y-1 text-sm text-gray-600 list-disc pl-5">
                        <li>Summarize the types of products found in the category.</li>
                        <li>Include key benefits, materials, or features of products.</li>
                        <li>Add a clear call-to-action for visitors.</li>
                        <li>Keep descriptions within 150-160 characters.</li>
                        <li>Example: "Browse our handcrafted celestial jewelry featuring mystical moon and star designs. Made with premium materials including sterling silver and gold. Free shipping available."</li>
                    </ul>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <h4 class="text-base font-medium text-gray-800 mb-2">Category Page Optimization</h4>
                    <ul class="space-y-1 text-sm text-gray-600 list-disc pl-5">
                        <li>Use descriptive H1 headings that match search intent.</li>
                        <li>Include relevant category descriptions with target keywords.</li>
                        <li>Ensure proper use of H2 and H3 headings for subcategories.</li>
                        <li>Implement breadcrumb navigation for better user experience and SEO.</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-base font-medium text-gray-800 mb-2">Internal Linking</h4>
                    <ul class="space-y-1 text-sm text-gray-600 list-disc pl-5">
                        <li>Link related categories together to help search engines and users navigate.</li>
                        <li>Feature popular or flagship products prominently on category pages.</li>
                        <li>Include links to informational content related to the category.</li>
                        <li>Use descriptive anchor text for category links from other pages.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    $(document).ready(function() {
        $('#categoriesTable').DataTable({
            order: [[0, 'asc']],
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            columnDefs: [
                { orderable: false, targets: [4] }
            ]
        });
    });
</script>
@endsection
@endsection 