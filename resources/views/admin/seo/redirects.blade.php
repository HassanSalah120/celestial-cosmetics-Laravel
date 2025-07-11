@extends('layouts.admin')

@section('title', $pageTitle)

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-semibold text-gray-900 mt-4">Redirects Management</h1>
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
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Redirects</span>
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

    <div class="mb-6 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Add New Redirect</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Create a redirect from an old URL to a new one.</p>
            </div>
        </div>
        
        <div class="border-t border-gray-200 p-4">
            <form action="{{ route('admin.store-redirect') }}" method="POST" id="redirectForm" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="source_url" class="block text-sm font-medium text-gray-700">Source URL</label>
                        <div class="mt-1">
                            <input type="text" name="source_url" id="source_url" placeholder="/old-page" value="{{ old('source_url') }}" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">The old URL path (without domain)</p>
                        @error('source_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="target_url" class="block text-sm font-medium text-gray-700">Target URL</label>
                        <div class="mt-1">
                            <input type="text" name="target_url" id="target_url" placeholder="/new-page" value="{{ old('target_url') }}" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">The new URL (can be a full URL or path)</p>
                        @error('target_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="redirect_type" class="block text-sm font-medium text-gray-700">Redirect Type</label>
                        <div class="mt-1">
                            <select name="redirect_type" id="redirect_type" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                <option value="301" {{ old('redirect_type') == '301' ? 'selected' : '' }}>301 (Permanent)</option>
                                <option value="302" {{ old('redirect_type') == '302' ? 'selected' : '' }}>302 (Temporary)</option>
                            </select>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">301 is recommended for SEO</p>
                        @error('redirect_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="flex items-center space-x-4 mt-4">
                    <label for="is_active" class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>
                
                <div class="pt-3">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Add Redirect
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Current Redirects</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Manage your existing redirects. These are automatically applied when a visitor accesses the source URL.
            </p>
        </div>
        
        <div class="border-t border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source URL</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target URL</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($redirects as $redirect)
                            <tr id="redirect-{{ $redirect->id }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $redirect->source_url }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $redirect->target_url }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($redirect->redirect_type == 301)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            301 Permanent
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            302 Temporary
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($redirect->is_active)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $redirect->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button type="button" 
                                                class="edit-redirect text-primary hover:text-primary-dark"
                                                data-id="{{ $redirect->id }}"
                                                data-source="{{ $redirect->source_url }}"
                                                data-target="{{ $redirect->target_url }}"
                                                data-type="{{ $redirect->redirect_type }}"
                                                data-active="{{ $redirect->is_active }}">
                                            <span class="sr-only">Edit</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('admin.destroy-redirect', $redirect->id) }}" method="POST" class="inline-block delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                <span class="sr-only">Delete</span>
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No redirects found. Add your first redirect using the form above.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                {{ $redirects->links() }}
            </div>
        </div>
    </div>
    
    <!-- Edit Redirect Modal -->
    <div id="editModal" class="fixed inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div id="modalBackdrop" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="" method="POST" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Edit Redirect
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="edit_source_url" class="block text-sm font-medium text-gray-700">Source URL</label>
                                        <div class="mt-1">
                                            <input type="text" name="source_url" id="edit_source_url" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">The old URL path (without domain)</p>
                                    </div>
                                    
                                    <div>
                                        <label for="edit_target_url" class="block text-sm font-medium text-gray-700">Target URL</label>
                                        <div class="mt-1">
                                            <input type="text" name="target_url" id="edit_target_url" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">The new URL (can be a full URL or path)</p>
                                    </div>
                                    
                                    <div>
                                        <label for="edit_redirect_type" class="block text-sm font-medium text-gray-700">Redirect Type</label>
                                        <div class="mt-1">
                                            <select name="redirect_type" id="edit_redirect_type" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                                <option value="301">301 (Permanent)</option>
                                                <option value="302">302 (Temporary)</option>
                                            </select>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">301 is recommended for SEO</p>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                        <label for="edit_is_active" class="ml-2 block text-sm text-gray-700">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                            Update
                        </button>
                        <button type="button" id="cancelEdit" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Edit redirect functionality
        const editModal = document.getElementById('editModal');
        const modalBackdrop = document.getElementById('modalBackdrop');
        const cancelEdit = document.getElementById('cancelEdit');
        const editForm = document.getElementById('editForm');
        
        // Show modal when edit button is clicked
        document.querySelectorAll('.edit-redirect').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const source = this.getAttribute('data-source');
                const target = this.getAttribute('data-target');
                const type = this.getAttribute('data-type');
                const active = this.getAttribute('data-active');
                
                // Set form action
                editForm.action = "{{ url('admin/redirects') }}/" + id;
                
                // Fill form fields
                document.getElementById('edit_source_url').value = source;
                document.getElementById('edit_target_url').value = target;
                document.getElementById('edit_redirect_type').value = type;
                document.getElementById('edit_is_active').checked = active === '1';
                
                // Show modal
                editModal.classList.remove('hidden');
            });
        });
        
        // Hide modal when backdrop or cancel button is clicked
        modalBackdrop.addEventListener('click', function() {
            editModal.classList.add('hidden');
        });
        
        cancelEdit.addEventListener('click', function() {
            editModal.classList.add('hidden');
        });
        
        // Confirm delete
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to delete this redirect? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endsection
@endsection 