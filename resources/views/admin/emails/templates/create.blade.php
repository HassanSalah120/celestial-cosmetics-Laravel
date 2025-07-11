@extends('layouts.admin')

@section('title', 'Create Email Template')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Create Email Template</h1>
        <a href="{{ route('admin.emails.templates.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Templates
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
            <div class="font-medium">Whoops! Something went wrong.</div>
            <ul class="mt-3 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.emails.templates.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Template Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                </div>

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Template Code</label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                    <p class="mt-1 text-xs text-gray-500">Unique identifier for the template (e.g., welcome_email, password_reset)</p>
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="2" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('description') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="subject" class="block text-sm font-medium text-gray-700">Email Subject</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                    <p class="mt-1 text-xs text-gray-500">You can use variables like {{name}} in the subject</p>
                </div>

                <div class="md:col-span-2">
                    <label for="body_html" class="block text-sm font-medium text-gray-700">HTML Body</label>
                    <textarea name="body_html" id="body_html" rows="12" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('body_html') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">You can use variables like {{name}} in the body</p>
                </div>

                <div class="md:col-span-2">
                    <label for="body_text" class="block text-sm font-medium text-gray-700">Plain Text Body (Optional)</label>
                    <textarea name="body_text" id="body_text" rows="6" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('body_text') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Plain text version of the email for clients that don't support HTML</p>
                </div>

                <div>
                    <label for="available_variables" class="block text-sm font-medium text-gray-700">Available Variables</label>
                    <input type="text" name="available_variables" id="available_variables" value="{{ old('available_variables') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    <p class="mt-1 text-xs text-gray-500">Comma-separated list of variables that can be used in this template (e.g., name,email,order_id)</p>
                </div>

                <div class="flex flex-col space-y-4">
                    <div>
                        <label for="is_active" class="inline-flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Active</span>
                        </label>
                    </div>

                    <div>
                        <label for="include_header_footer" class="inline-flex items-center">
                            <input type="checkbox" name="include_header_footer" id="include_header_footer" value="1" {{ old('include_header_footer', '1') == '1' ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Include Header/Footer</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Create Template
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-generate code from name
        const nameInput = document.getElementById('name');
        const codeInput = document.getElementById('code');
        
        nameInput.addEventListener('blur', function() {
            if (codeInput.value === '') {
                const code = nameInput.value
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '_')
                    .replace(/^_+|_+$/g, '');
                codeInput.value = code;
            }
        });
    });
</script>
@endpush 