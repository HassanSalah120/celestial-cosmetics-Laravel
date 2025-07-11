@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-primary/5 to-secondary/5 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary to-secondary rounded-lg shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">Activity Details</h1>
                    <p class="text-white/80 mt-2">View detailed information about this activity</p>
                </div>
                <a href="{{ route('admin.activities.index') }}" class="px-4 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20 transition-colors duration-200">
                    Back to Activities
                </a>
            </div>
        </div>

        <!-- Activity Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Info -->
            <div class="lg:col-span-2">
                <div class="bg-white/80 backdrop-blur-md rounded-lg shadow-lg p-6">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">Description</h2>
                        <p class="text-gray-700">{{ $activity->description }}</p>
                    </div>

                    @if($activity->properties)
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">Properties</h2>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                    @endif

                    @if($activity->subject)
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">Related Item</h2>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-700">
                                <span class="font-medium">Type:</span> {{ class_basename($activity->subject_type) }}<br>
                                <span class="font-medium">ID:</span> {{ $activity->subject_id }}
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="space-y-8">
                <!-- Status Card -->
                <div class="bg-white/80 backdrop-blur-md rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Status</h2>
                    <div class="flex items-center">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            {{ $activity->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $activity->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $activity->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($activity->status) }}
                        </span>
                    </div>
                </div>

                <!-- User Info -->
                <div class="bg-white/80 backdrop-blur-md rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">User Information</h2>
                    @if($activity->causer)
                        <div class="space-y-2">
                            <div class="flex items-center space-x-3">
                                <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $activity->causer->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $activity->causer->email }}</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-sm text-gray-500">System Activity</div>
                    @endif
                </div>

                <!-- Timestamp Info -->
                <div class="bg-white/80 backdrop-blur-md rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Timestamp</h2>
                    <div class="space-y-2">
                        <div>
                            <div class="text-sm text-gray-500">Created</div>
                            <div class="text-sm font-medium text-gray-900">
                                {{ $activity->created_at->format('M d, Y H:i:s') }}
                                <span class="text-gray-500">({{ $activity->created_at->diffForHumans() }})</span>
                            </div>
                        </div>
                        @if($activity->updated_at->ne($activity->created_at))
                        <div>
                            <div class="text-sm text-gray-500">Updated</div>
                            <div class="text-sm font-medium text-gray-900">
                                {{ $activity->updated_at->format('M d, Y H:i:s') }}
                                <span class="text-gray-500">({{ $activity->updated_at->diffForHumans() }})</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 