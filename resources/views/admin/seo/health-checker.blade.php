@extends('layouts.admin')

@section('title', $pageTitle)

@section('content')
<div class="container-xl px-4 mt-4">
    <h1 class="text-xl font-bold mb-4">SEO Health Checker</h1>

    <!-- Overall Score Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold">Overall SEO Score</h2>
                <p class="text-gray-500 text-sm">Based on {{ count($healthChecks) }} checks</p>
            </div>
            <div class="text-center">
                <div class="relative inline-block w-32 h-32">
                    <svg class="w-full h-full" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f0f0f0" stroke-width="3.8"></circle>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="{{ $seoScore >= 80 ? '#10b981' : ($seoScore >= 50 ? '#f59e0b' : '#ef4444') }}" 
                                stroke-width="3.8" stroke-dasharray="{{ $seoScore * 0.01 * 100 }} 100" stroke-dashoffset="25" stroke-linecap="round"></circle>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-3xl font-bold">{{ $seoScore }}%</span>
                    </div>
                </div>
                <p class="mt-2 font-medium text-{{ $seoScore >= 80 ? 'emerald' : ($seoScore >= 50 ? 'amber' : 'red') }}-600">
                    {{ $seoScore >= 80 ? 'Good' : ($seoScore >= 50 ? 'Needs Improvement' : 'Poor') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Priority Issues Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Priority Issues to Fix</h2>
        
        @if(count($priorityIssues) > 0)
            <div class="space-y-4">
                @foreach($priorityIssues as $priorityIssue)
                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-amber-800">
                                {{ ucfirst(str_replace('_', ' ', $priorityIssue['check'])) }}: {{ $priorityIssue['issue'] }}
                            </h3>
                            @if(isset($priorityIssue['suggestion']))
                            <div class="mt-2 text-sm text-amber-700">
                                <p><strong>Suggestion:</strong> {{ $priorityIssue['suggestion'] }}</p>
                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
                            @else
            <p class="text-gray-500">No priority issues found. Great job!</p>
                            @endif
                        </div>

    <!-- Detailed SEO Checks Accordion -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold mb-4">Detailed SEO Checks</h2>
        
        <div class="space-y-4" x-data="{ openTab: null }">
            @foreach($healthChecks as $checkName => $check)
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button 
                        @click="openTab = (openTab === '{{ $checkName }}') ? null : '{{ $checkName }}'" 
                        class="flex justify-between items-center w-full p-4 text-left bg-gray-50 hover:bg-gray-100 focus:outline-none"
                    >
                        <div class="flex items-center">
                            <div class="w-6 h-6 mr-2">
                                @if($check['pass'])
                                    <svg class="text-emerald-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                    <svg class="text-red-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                            </div>
                            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $checkName)) }}</span>
                </div>
                        <div class="flex items-center">
                            <span class="text-sm {{ $check['pass'] ? 'text-emerald-500' : 'text-red-500' }}">
                                {{ $check['pass'] ? 'Pass' : count($check['issues']) . ' issue(s)' }}
                            </span>
                            <svg :class="{'rotate-180': openTab === '{{ $checkName }}'}" class="ml-2 h-5 w-5 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                        </div>
                    </button>
                    
                    <div 
                        x-show="openTab === '{{ $checkName }}'" 
                        x-transition:enter="transition ease-out duration-200" 
                        x-transition:enter-start="opacity-0 transform -translate-y-2" 
                        x-transition:enter-end="opacity-100 transform translate-y-0" 
                        x-transition:leave="transition ease-in duration-200" 
                        x-transition:leave-start="opacity-100 transform translate-y-0" 
                        x-transition:leave-end="opacity-0 transform -translate-y-2" 
                        class="p-4 border-t border-gray-200"
                    >
                        @if(!$check['pass'])
                            <div class="mb-4">
                                <h4 class="font-medium text-gray-700 mb-2">Issues:</h4>
                                <ul class="list-disc pl-5 space-y-1 text-gray-600">
                                    @foreach($check['issues'] as $issue)
                                    <li>{{ $issue }}</li>
                                @endforeach
                            </ul>
                </div>
                
                            @if(isset($check['suggestions']) && count($check['suggestions']) > 0)
                                <div>
                                    <h4 class="font-medium text-gray-700 mb-2">Suggestions:</h4>
                                    <ul class="list-disc pl-5 space-y-1 text-gray-600">
                                        @foreach($check['suggestions'] as $suggestion)
                                            <li>{{ $suggestion }}</li>
                                @endforeach
                            </ul>
                </div>
                            @endif
                        @else
                            <p class="text-gray-600">No issues found! This check is passing successfully.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush 