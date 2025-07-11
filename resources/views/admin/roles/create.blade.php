@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 font-display">Assign User Role</h2>
            <p class="mt-1 text-sm text-gray-600">Assign roles and permissions to a user</p>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 border border-gray-300 bg-white rounded-lg text-gray-700 hover:bg-gray-50 inline-flex items-center transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Role Management
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            
            @if($errors->any())
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-400 text-red-700">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <!-- User Selection -->
            <div class="mb-6">
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Select User</label>
                <select id="user_id" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
                    <option value="">-- Select a user --</option>
                    @foreach($eligibleUsers as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                <p class="mt-1 text-sm text-gray-500">Only users with the standard user role are shown</p>
            </div>
            
            <!-- Role Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Role</label>
                <div class="space-y-4 mt-2">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="role_admin" name="role" type="radio" value="admin" class="focus:ring-primary h-4 w-4 text-primary border-gray-300" onchange="togglePermissionsVisibility()">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="role_admin" class="font-medium text-gray-700">Administrator</label>
                            <p class="text-gray-500">Full access to all features and settings</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="role_staff" name="role" type="radio" value="staff" class="focus:ring-primary h-4 w-4 text-primary border-gray-300" onchange="togglePermissionsVisibility()">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="role_staff" class="font-medium text-gray-700">Staff</label>
                            <p class="text-gray-500">Limited access based on assigned permissions</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Permissions Selection (only visible when Staff is selected) -->
            <div id="permissions_section" class="mb-6 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-3">Select Permissions</label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-2">
                    @foreach($availablePermissions as $permission => $description)
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="permission_{{ $permission }}" name="permissions[]" type="checkbox" value="{{ $permission }}" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="permission_{{ $permission }}" class="font-medium text-gray-700">{{ $permission }}</label>
                            <p class="text-gray-500">{{ $description }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 border-t pt-6">
                <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-light focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent">
                    Assign Role
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function togglePermissionsVisibility() {
        const permissionsSection = document.getElementById('permissions_section');
        const staffRoleSelected = document.getElementById('role_staff').checked;
        
        if (staffRoleSelected) {
            permissionsSection.classList.remove('hidden');
        } else {
            permissionsSection.classList.add('hidden');
            // Uncheck all permission checkboxes when not using staff role
            document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    }
</script>
@endpush
@endsection 