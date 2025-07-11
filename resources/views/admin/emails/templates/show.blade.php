@extends('layouts.admin')

@section('title', 'Template Details: ' . $template->name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Template Details: {{ $template->name }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.emails.templates.preview', $template) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150" target="_blank">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Preview
            </a>
            <a href="{{ route('admin.emails.templates.edit', $template) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Template
            </a>
            <a href="{{ route('admin.emails.templates.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Templates
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Template Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Name</p>
                        <p class="text-base text-gray-900">{{ $template->name }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm font-medium text-gray-500">Code</p>
                        <p class="text-base text-gray-900">{{ $template->code }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm font-medium text-gray-500">Status</p>
                        <div class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $template->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    
                    <div>
                        <p class="text-sm font-medium text-gray-500">Include Header/Footer</p>
                        <div class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $template->include_header_footer ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $template->include_header_footer ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="md:col-span-2">
                        <p class="text-sm font-medium text-gray-500">Description</p>
                        <p class="text-base text-gray-900">{{ $template->description ?: 'No description provided' }}</p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <p class="text-sm font-medium text-gray-500">Subject</p>
                        <p class="text-base text-gray-900 font-mono">{{ $template->subject }}</p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <p class="text-sm font-medium text-gray-500">Available Variables</p>
                        <div class="flex flex-wrap gap-2 mt-1">
                            @forelse ($template->available_variables ?? [] as $variable)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $variable }}
                                </span>
                            @empty
                                <p class="text-gray-500">No variables defined</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Email Content</h2>
                
                <div class="mb-4">
                    <h3 class="text-lg font-medium mb-2">HTML Body</h3>
                    <div class="bg-gray-100 p-4 rounded-md overflow-x-auto">
                        <pre class="text-xs text-gray-800">{{ $template->body_html }}</pre>
                    </div>
                </div>
                
                @if ($template->body_text)
                    <div>
                        <h3 class="text-lg font-medium mb-2">Plain Text Body</h3>
                        <div class="bg-gray-100 p-4 rounded-md overflow-x-auto">
                            <pre class="text-xs text-gray-800">{{ $template->body_text }}</pre>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <div>
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Recent Email Logs</h2>
                
                @if ($logs->isEmpty())
                    <p class="text-gray-500">No recent emails sent using this template.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent To</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($logs as $log)
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                            {{ $log->to_email }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $log->success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $log->success ? 'Success' : 'Failed' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                            {{ $log->sent_at ? $log->sent_at->format('M d, Y H:i') : 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 text-right">
                        <a href="{{ route('admin.emails.logs.index', ['template_id' => $template->id]) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                            View All Logs <span aria-hidden="true">→</span>
                        </a>
                    </div>
                @endif
            </div>
            
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Actions</h2>
                
                <div class="flex flex-col space-y-3">
                    <form action="{{ route('admin.emails.templates.clone', $template) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:border-yellow-700 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Clone Template
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.emails.templates.destroy', $template) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this template? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete Template
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 