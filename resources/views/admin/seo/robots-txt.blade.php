@extends('layouts.admin')

@section('title', $pageTitle)

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-semibold text-gray-900 mt-4">Robots.txt Editor</h1>
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
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Robots.txt Editor</span>
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

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Robots.txt Rules</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Configure how search engines interact with your site. The robots.txt file is a standard used by websites to communicate with web crawlers and other web robots.
            </p>
        </div>
        
        <div class="border-t border-gray-200 p-4">
            <div class="mb-6">
                <div class="flex justify-between items-center">
                    <h4 class="text-md font-medium text-gray-700">Current Rules</h4>
                    <button id="addRuleBtn" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Rule
                    </button>
                </div>
                
                <form action="{{ route('admin.update-robots-txt') }}" method="POST" id="robotsForm">
                    @csrf
                    <div id="rulesContainer" class="mt-4 space-y-4">
                        @if(count($rules) > 0)
                            @foreach($rules as $index => $rule)
                                <div class="rule-item bg-gray-50 p-4 rounded-md">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="text-sm font-medium text-gray-900">Rule {{ $index + 1 }}</div>
                                        <button type="button" class="delete-rule text-red-600 hover:text-red-800">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <div>
                                            <label for="user_agent_{{ $index }}" class="block text-sm font-medium text-gray-700">User Agent</label>
                                            <select name="rules[{{ $index }}][user_agent]" id="user_agent_{{ $index }}" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                                                <option value="*" {{ $rule->user_agent == '*' ? 'selected' : '' }}>All robots (*)</option>
                                                <option value="googlebot" {{ $rule->user_agent == 'googlebot' ? 'selected' : '' }}>Google (googlebot)</option>
                                                <option value="bingbot" {{ $rule->user_agent == 'bingbot' ? 'selected' : '' }}>Bing (bingbot)</option>
                                                <option value="yandexbot" {{ $rule->user_agent == 'yandexbot' ? 'selected' : '' }}>Yandex (yandexbot)</option>
                                                <option value="baiduspider" {{ $rule->user_agent == 'baiduspider' ? 'selected' : '' }}>Baidu (baiduspider)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="rule_type_{{ $index }}" class="block text-sm font-medium text-gray-700">Rule Type</label>
                                            <select name="rules[{{ $index }}][rule_type]" id="rule_type_{{ $index }}" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                                                <option value="allow" {{ $rule->rule_type == 'allow' ? 'selected' : '' }}>Allow</option>
                                                <option value="disallow" {{ $rule->rule_type == 'disallow' ? 'selected' : '' }}>Disallow</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <label for="path_{{ $index }}" class="block text-sm font-medium text-gray-700">Path</label>
                                        <input type="text" name="rules[{{ $index }}][path]" id="path_{{ $index }}" value="{{ $rule->path }}" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <p class="mt-1 text-xs text-gray-500">Example: /admin/ or /private-content/</p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="rule-item bg-gray-50 p-4 rounded-md">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="text-sm font-medium text-gray-900">Rule 1</div>
                                    <button type="button" class="delete-rule text-red-600 hover:text-red-800">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="user_agent_0" class="block text-sm font-medium text-gray-700">User Agent</label>
                                        <select name="rules[0][user_agent]" id="user_agent_0" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                                            <option value="*" selected>All robots (*)</option>
                                            <option value="googlebot">Google (googlebot)</option>
                                            <option value="bingbot">Bing (bingbot)</option>
                                            <option value="yandexbot">Yandex (yandexbot)</option>
                                            <option value="baiduspider">Baidu (baiduspider)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="rule_type_0" class="block text-sm font-medium text-gray-700">Rule Type</label>
                                        <select name="rules[0][rule_type]" id="rule_type_0" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                                            <option value="allow">Allow</option>
                                            <option value="disallow" selected>Disallow</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label for="path_0" class="block text-sm font-medium text-gray-700">Path</label>
                                    <input type="text" name="rules[0][path]" id="path_0" value="/admin/" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <p class="mt-1 text-xs text-gray-500">Example: /admin/ or /private-content/</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-6">
                        <label for="sitemap_url" class="block text-sm font-medium text-gray-700">Sitemap URL</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                Sitemap:
                            </span>
                            <input type="text" name="sitemap_url" id="sitemap_url" value="{{ $sitemapUrl ?? '' }}" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md focus:ring-primary focus:border-primary sm:text-sm border-gray-300">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Example: https://yoursite.com/sitemap.xml</p>
                    </div>
                    
                    <div class="mt-6">
                        <label for="preview" class="block text-sm font-medium text-gray-700">Generated robots.txt Preview</label>
                        <textarea id="preview" rows="10" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50 font-mono" readonly></textarea>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('admin.seo.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Cancel
                        </a>
                        <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">About robots.txt</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Learn more about the robots.txt file and how it affects search engine indexing.
            </p>
        </div>
        
        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">What is robots.txt?</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        The robots.txt file is a text file webmasters create to instruct web robots (typically search engine robots) how to crawl pages on their website. The robots.txt file is part of the robots exclusion protocol (REP), a group of web standards that regulate how robots crawl the web, access and index content, and serve that content up to users.
                    </dd>
                </div>
                
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">User-Agent</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        Specifies which crawler the rules apply to. Use * to apply to all crawlers or specify a particular search engine crawler.
                    </dd>
                </div>
                
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Allow / Disallow</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <strong>Allow:</strong> Permits crawlers to access a particular URL or directory.<br>
                        <strong>Disallow:</strong> Prevents crawlers from accessing a particular URL or directory.
                    </dd>
                </div>
                
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Best Practices</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Disallow admin sections and private content</li>
                            <li>Include a link to your sitemap</li>
                            <li>Be specific with your paths</li>
                            <li>Use the robots.txt file as part of your overall SEO strategy, not as the only measure</li>
                        </ul>
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Reference to the rules container
        const rulesContainer = document.getElementById('rulesContainer');
        const addRuleBtn = document.getElementById('addRuleBtn');
        const previewTextarea = document.getElementById('preview');
        
        // Add rule button click handler
        addRuleBtn.addEventListener('click', function() {
            const ruleItems = document.querySelectorAll('.rule-item');
            const newIndex = ruleItems.length;
            
            const newRuleItem = document.createElement('div');
            newRuleItem.className = 'rule-item bg-gray-50 p-4 rounded-md';
            
            newRuleItem.innerHTML = `
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium text-gray-900">Rule ${newIndex + 1}</div>
                    <button type="button" class="delete-rule text-red-600 hover:text-red-800">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="user_agent_${newIndex}" class="block text-sm font-medium text-gray-700">User Agent</label>
                        <select name="rules[${newIndex}][user_agent]" id="user_agent_${newIndex}" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                            <option value="*" selected>All robots (*)</option>
                            <option value="googlebot">Google (googlebot)</option>
                            <option value="bingbot">Bing (bingbot)</option>
                            <option value="yandexbot">Yandex (yandexbot)</option>
                            <option value="baiduspider">Baidu (baiduspider)</option>
                        </select>
                    </div>
                    <div>
                        <label for="rule_type_${newIndex}" class="block text-sm font-medium text-gray-700">Rule Type</label>
                        <select name="rules[${newIndex}][rule_type]" id="rule_type_${newIndex}" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                            <option value="allow">Allow</option>
                            <option value="disallow" selected>Disallow</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <label for="path_${newIndex}" class="block text-sm font-medium text-gray-700">Path</label>
                    <input type="text" name="rules[${newIndex}][path]" id="path_${newIndex}" placeholder="/path/" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    <p class="mt-1 text-xs text-gray-500">Example: /admin/ or /private-content/</p>
                </div>
            `;
            
            rulesContainer.appendChild(newRuleItem);
            addDeleteRuleListeners();
            updatePreview();
        });
        
        // Delete rule button click handler
        function addDeleteRuleListeners() {
            document.querySelectorAll('.delete-rule').forEach(button => {
                button.addEventListener('click', function() {
                    // Don't delete if it's the only rule
                    if (document.querySelectorAll('.rule-item').length > 1) {
                        this.closest('.rule-item').remove();
                        reindexRules();
                        updatePreview();
                    } else {
                        alert('You must have at least one rule.');
                    }
                });
            });
        }
        
        // Reindex rules after deletion
        function reindexRules() {
            const ruleItems = document.querySelectorAll('.rule-item');
            ruleItems.forEach((item, index) => {
                // Update rule number
                item.querySelector('.text-sm.font-medium').textContent = `Rule ${index + 1}`;
                
                // Update form field names
                item.querySelectorAll('select, input').forEach(field => {
                    const name = field.getAttribute('name');
                    const newName = name.replace(/rules\[\d+\]/, `rules[${index}]`);
                    field.setAttribute('name', newName);
                    
                    const id = field.getAttribute('id');
                    const newId = id.replace(/\_\d+$/, `_${index}`);
                    field.setAttribute('id', newId);
                    
                    // Update associated labels
                    const label = item.querySelector(`label[for="${id}"]`);
                    if (label) {
                        label.setAttribute('for', newId);
                    }
                });
            });
        }
        
        // Generate preview of robots.txt content
        function updatePreview() {
            let preview = '';
            const ruleItems = document.querySelectorAll('.rule-item');
            
            let currentUserAgent = '';
            
            ruleItems.forEach((item, index) => {
                const userAgent = item.querySelector('[id^="user_agent_"]').value;
                const ruleType = item.querySelector('[id^="rule_type_"]').value;
                const path = item.querySelector('[id^="path_"]').value;
                
                if (currentUserAgent !== userAgent) {
                    if (index > 0) preview += '\n';
                    preview += `User-agent: ${userAgent}\n`;
                    currentUserAgent = userAgent;
                }
                
                preview += `${ruleType === 'allow' ? 'Allow' : 'Disallow'}: ${path}\n`;
            });
            
            const sitemapUrl = document.getElementById('sitemap_url').value;
            if (sitemapUrl) {
                preview += `\nSitemap: ${sitemapUrl}\n`;
            }
            
            previewTextarea.value = preview;
        }
        
        // Add event listeners to all form fields
        function addFormFieldListeners() {
            document.querySelectorAll('#robotsForm select, #robotsForm input').forEach(field => {
                field.addEventListener('change', updatePreview);
                field.addEventListener('keyup', updatePreview);
            });
        }
        
        // Initialize
        addDeleteRuleListeners();
        addFormFieldListeners();
        updatePreview();
        
        // Update preview when form submitted
        document.getElementById('robotsForm').addEventListener('submit', function() {
            updatePreview();
        });
    });
</script>
@endsection
@endsection 