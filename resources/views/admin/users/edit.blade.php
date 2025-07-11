@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 font-display">Edit User</h2>
            <p class="mt-1 text-sm text-gray-600">Update user account information</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Users
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-lg shadow-sm">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="p-6 space-y-8" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">There were errors with your submission:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Profile Image Section -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Profile Image</h3>
                <div class="mt-4 flex items-start space-x-6">
                    <div class="shrink-0">
                        <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100 border">
                            @if($user->profile_image)
                                <img id="preview-image" src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                <img id="preview-image" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&color=7F9CF5&background=EBF4FF&size=96" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="relative">
                            <input type="file" name="profile_image" id="profile_image" accept="image/*" 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                onchange="document.getElementById('preview-image').src = window.URL.createObjectURL(this.files[0])">
                            <label for="profile_image" 
                                class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 cursor-pointer">
                                <svg class="h-5 w-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Choose new image
                            </label>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            JPG, PNG or GIF. Max size 2MB. Recommended square image for best results.
                        </p>
                        @error('profile_image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Basic Information Section -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Basic Information</h3>
                <div class="mt-4 grid grid-cols-1 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <p class="mt-1 text-sm text-gray-500">This email address must be unique.</p>
                    </div>
                </div>
            </div>

            <!-- Role Section -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">User Role</h3>
                <div class="mt-4 space-y-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="radio" name="role" id="role_admin" value="admin" {{ $user->role === 'admin' ? 'checked' : '' }}
                                class="h-4 w-4 text-primary focus:ring-primary border-gray-300">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="role_admin" class="font-medium text-gray-900">
                                Administrator
                            </label>
                            <p class="text-gray-500">Full access to all features and settings</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="radio" name="role" id="role_staff" value="staff" {{ $user->role === 'staff' ? 'checked' : '' }}
                                class="h-4 w-4 text-primary focus:ring-primary border-gray-300">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="role_staff" class="font-medium text-gray-900">
                                Staff
                            </label>
                            <p class="text-gray-500">Limited access based on assigned permissions (manage permissions in Roles section)</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="radio" name="role" id="role_user" value="user" {{ $user->role === 'user' ? 'checked' : '' }}
                                class="h-4 w-4 text-primary focus:ring-primary border-gray-300">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="role_user" class="font-medium text-gray-900">
                                Customer
                            </label>
                            <p class="text-gray-500">Standard customer account with no admin privileges</p>
                        </div>
                    </div>
                    
                    @if($user->role === 'staff')
                    <div class="mt-4 px-4 py-3 bg-yellow-50 rounded-md border border-yellow-200">
                        <p class="text-sm text-yellow-800">
                            <span class="font-medium">Note:</span> To manage this staff member's specific permissions, please use the 
                            <a href="{{ route('admin.roles.edit', $user) }}" class="text-blue-600 hover:text-blue-800 underline">Roles & Permissions</a> 
                            section.
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Account Status Section -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Account Status</h3>
                <div class="mt-4">
                    <div class="flex items-center text-sm">
                        <div class="bg-{{ $user->email_verified_at ? 'green' : 'yellow' }}-100 text-{{ $user->email_verified_at ? 'green' : 'yellow' }}-800 px-2 py-1 rounded-full font-medium">
                            {{ $user->email_verified_at ? 'Email Verified' : 'Email Not Verified' }}
                        </div>
                        @if($user->email_verified_at)
                            <span class="ml-2 text-gray-500">on {{ $user->email_verified_at->format('M d, Y \a\t h:i A') }}</span>
                        @endif
                    </div>
                    <p class="mt-2 text-sm text-gray-500">User created on {{ $user->created_at->format('M d, Y \a\t h:i A') }}</p>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 border-t pt-6">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-light focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 