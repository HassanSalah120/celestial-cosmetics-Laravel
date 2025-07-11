@extends('layouts.admin')

@section('title', 'Preview: ' . $template->name)

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Preview: {{ $template->name }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.emails.templates.edit', $template) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Template
            </a>
            <a href="{{ route('admin.emails.templates.show', $template) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Template Details
            </a>
            <a href="{{ route('admin.emails.templates.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Templates
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="border-b border-gray-200 px-4 py-3 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <button id="btn-prev" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <button id="btn-next" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                            <button id="btn-refresh" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </button>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Email Preview</span>
                        </div>
                    </div>
                </div>
                
                <div class="border-b border-gray-200 px-4 py-3">
                    <div class="flex flex-col space-y-1">
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-500 w-20">From:</span>
                            <span class="text-sm text-gray-900">{{ config('mail.from.name') }} &lt;{{ config('mail.from.address') }}&gt;</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-500 w-20">To:</span>
                            <span class="text-sm text-gray-900">Sample Recipient &lt;recipient@example.com&gt;</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-500 w-20">Subject:</span>
                            <span class="text-sm text-gray-900 font-medium">{{ $subject }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="p-1 bg-gray-100">
                    <div class="bg-white border border-gray-200 min-h-screen">
                        <iframe id="email-preview" class="w-full h-screen" srcdoc="{{ $html }}"></iframe>
                    </div>
                </div>
            </div>
        </div>
        
        <div>
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Template Information</h2>
                
                <div class="flex flex-col space-y-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Name</p>
                        <p class="text-base text-gray-900">{{ $template->name }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm font-medium text-gray-500">Code</p>
                        <p class="text-base text-gray-900">{{ $template->code }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm font-medium text-gray-500">Subject</p>
                        <p class="text-base text-gray-900 font-mono">{{ $subject }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Sample Variable Values</h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variable</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($variables as $key => $value)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <div class="text-sm font-mono text-gray-900">{{$key}}</div>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ Str::limit($value, 50) }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No variables used in this template
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const iframe = document.getElementById('email-preview');
        const btnPrev = document.getElementById('btn-prev');
        const btnNext = document.getElementById('btn-next');
        const btnRefresh = document.getElementById('btn-refresh');
        
        btnRefresh.addEventListener('click', function() {
            iframe.contentWindow.location.reload();
        });
        
        btnPrev.addEventListener('click', function() {
            // In a real app, this would load previous email
            alert('This is just a preview. No previous email to display.');
        });
        
        btnNext.addEventListener('click', function() {
            // In a real app, this would load next email
            alert('This is just a preview. No next email to display.');
        });
    });
</script>
@endpush 