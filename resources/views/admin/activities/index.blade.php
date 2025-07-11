@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-primary/5 to-secondary/5 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary to-secondary rounded-lg shadow-lg p-6 mb-8">
            <h1 class="text-3xl font-bold text-white">Activity Log</h1>
            <p class="text-white/80 mt-2">Monitor system activities and user actions</p>
        </div>

        <!-- Filters -->
        <div class="bg-white/80 backdrop-blur-md rounded-lg shadow-lg p-6 mb-8">
            <form action="{{ route('admin.activities.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-accent focus:ring-accent">
                        <option value="">All Status</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700">From Date</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-accent focus:ring-accent">
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700">To Date</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-accent focus:ring-accent">
                </div>

                <div class="md:col-span-3 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-accent text-white rounded-lg hover:bg-accent-light transition-colors duration-200">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Activity List -->
        <div class="bg-white/80 backdrop-blur-md rounded-lg shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($activities as $activity)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $activity->created_at->diffForHumans() }}
                                    <div class="text-xs text-gray-400">
                                        {{ $activity->created_at->format('M d, Y H:i:s') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($activity->causer)
                                        <div class="text-sm font-medium text-gray-900">{{ $activity->causer->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $activity->causer->email }}</div>
                                    @else
                                        <div class="text-sm text-gray-500">System</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $activity->description }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $activity->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $activity->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $activity->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($activity->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <a href="{{ route('admin.activities.show', $activity) }}" 
                                        class="text-primary hover:text-accent transition-colors duration-200">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No activities found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $activities->links() }}
        </div>
    </div>
</div>
@endsection 