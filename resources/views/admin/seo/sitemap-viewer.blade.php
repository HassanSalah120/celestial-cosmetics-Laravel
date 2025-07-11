@extends('layouts.admin')

@section('title', $pageTitle)

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-semibold text-gray-900 mt-4">Sitemap Viewer</h1>
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
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Sitemap Viewer</span>
                </div>
            </li>
        </ol>
    </nav>
    
    @if(session('success'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Sitemap Information</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Overview of your site's XML sitemap.</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ url('sitemap.xml') }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    View Raw XML
                </a>
                <a href="{{ route('admin.seo.generate-sitemap') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Regenerate Sitemap
                </a>
            </div>
        </div>
        
        <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-3">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Sitemap URL</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <a href="{{ url('sitemap.xml') }}" target="_blank" class="text-primary hover:text-primary-dark">
                            {{ url('sitemap.xml') }}
                        </a>
                    </dd>
                </div>
                
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Last Generated</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if(file_exists(public_path('sitemap.xml')))
                            {{ date('F j, Y, g:i a', filemtime(public_path('sitemap.xml'))) }}
                        @else
                            Not generated yet
                        @endif
                    </dd>
                </div>
                
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Total URLs</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $totalUrls ?? 'Unknown' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>
    
    @if(isset($urls) && count($urls) > 0)
        <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">URLs in Sitemap</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    These are all the URLs included in your sitemap.
                </p>
            </div>
            
            <div class="border-t border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Modified</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Change Frequency</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($urls as $url)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 max-w-xs truncate">
                                        <span title="{{ $url['loc'] }}" class="hover:text-primary hover:underline cursor-help">
                                            {{ $url['loc'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ isset($url['lastmod']) ? date('Y-m-d', strtotime($url['lastmod'])) : 'Not specified' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if(isset($url['changefreq']))
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @switch($url['changefreq'])
                                                    @case('always') bg-red-100 text-red-800 @break
                                                    @case('hourly') bg-orange-100 text-orange-800 @break
                                                    @case('daily') bg-yellow-100 text-yellow-800 @break
                                                    @case('weekly') bg-green-100 text-green-800 @break
                                                    @case('monthly') bg-blue-100 text-blue-800 @break
                                                    @case('yearly') bg-indigo-100 text-indigo-800 @break
                                                    @default bg-gray-100 text-gray-800
                                                @endswitch
                                            ">
                                                {{ ucfirst($url['changefreq']) }}
                                            </span>
                                        @else
                                            Not specified
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if(isset($url['priority']))
                                            {{ $url['priority'] }}
                                        @else
                                            Not specified
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ $url['loc'] }}" target="_blank" class="text-primary hover:text-primary-dark">
                                            <span class="sr-only">Visit</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="mt-8 bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">No Sitemap Found</h3>
                <div class="mt-2 max-w-xl text-sm text-gray-500">
                    <p>No sitemap has been generated yet or it's empty.</p>
                </div>
                <div class="mt-5">
                    <a href="{{ route('admin.seo.generate-sitemap') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Generate Sitemap
                    </a>
                </div>
            </div>
        </div>
    @endif
    
    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">About Sitemaps</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Learn more about XML sitemaps and how they help search engines discover your content.
            </p>
        </div>
        
        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">What is a sitemap?</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        A sitemap is an XML file that lists URLs for a site along with additional metadata about each URL (when it was last updated, how often it changes, and how important it is relative to other URLs in the site) so that search engines can more intelligently crawl the site.
                    </dd>
                </div>
                
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Benefits</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Helps search engines discover your URLs</li>
                            <li>Notifies search engines about content changes</li>
                            <li>Enables more efficient crawling</li>
                            <li>Provides metadata about your content</li>
                        </ul>
                    </dd>
                </div>
                
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Best Practices</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Keep your sitemap up to date</li>
                            <li>Include canonical URLs only</li>
                            <li>Add lastmod dates for dynamic content</li>
                            <li>Link to your sitemap in robots.txt</li>
                            <li>Submit your sitemap to Google Search Console</li>
                        </ul>
                    </dd>
                </div>
                
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Next Steps</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        After generating your sitemap, submit it to search engines through their webmaster tools:
                        <ul class="list-disc pl-5 mt-2 space-y-1">
                            <li><a href="https://search.google.com/search-console" target="_blank" class="text-primary hover:text-primary-dark">Google Search Console</a></li>
                            <li><a href="https://www.bing.com/webmasters" target="_blank" class="text-primary hover:text-primary-dark">Bing Webmaster Tools</a></li>
                            <li><a href="https://yandex.com/webmaster/" target="_blank" class="text-primary hover:text-primary-dark">Yandex Webmaster</a></li>
                        </ul>
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection 