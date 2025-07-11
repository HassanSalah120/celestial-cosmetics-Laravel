@extends('layouts.admin')

@section('title', 'View Contact Message')

@section('content')
<div class="container px-6 py-4 mx-auto">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex justify-between items-center px-6 py-4 border-b">
            <h3 class="text-lg font-medium text-gray-800">Message Details</h3>
            <div>
                <a href="{{ route('admin.contact-messages.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm rounded-md bg-gray-50 text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                </a>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="col-span-2">
                    <div class="message-details">
                        <h4 class="text-xl font-medium text-gray-800 mb-4">{{ $message->subject }}</h4>
                        
                        <div class="flex justify-between mb-4">
                            <div>
                                <span class="font-medium text-gray-700">From:</span> {{ $message->name }} ({{ $message->email }})
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Date:</span> {{ $message->created_at->format('M d, Y h:i A') }}
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <span class="font-medium text-gray-700">Status:</span>
                            <span class="px-2 ml-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $message->status === 'new' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $message->status === 'read' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                {{ $message->status === 'replied' ? 'bg-green-100 text-green-800' : '' }}">
                                {{ ucfirst($message->status) }}
                            </span>
                        </div>
                        
                        <div class="p-4 mb-6 bg-gray-50 rounded-md">
                            {!! nl2br(e($message->message)) !!}
                        </div>
                        
                        @if($message->reply)
                        <div class="mt-8">
                            <h5 class="text-lg font-medium text-gray-800 mb-2">Your Reply:</h5>
                            <div class="flex justify-between mb-2">
                                <div>
                                    <span class="font-medium text-gray-700">Sent:</span> 
                                    @if($message->replied_at && $message->replied_at instanceof \Carbon\Carbon)
                                        {{ $message->replied_at->format('M d, Y h:i A') }}
                                    @elseif($message->replied_at)
                                        {{ \Carbon\Carbon::parse($message->replied_at)->format('M d, Y h:i A') }}
                                    @else
                                        {{ $message->updated_at->format('M d, Y h:i A') }}
                                    @endif
                                </div>
                            </div>
                            <div class="p-4 bg-green-50 rounded-md border-l-4 border-green-400">
                                {!! nl2br(e($message->reply)) !!}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="col-span-1">
                    <div class="bg-gray-50 rounded-md shadow-sm">
                        <div class="px-4 py-3 border-b border-gray-200">
                            <h5 class="text-lg font-medium text-gray-800">Actions</h5>
                        </div>
                        <div class="p-4">
                            <form action="{{ route('admin.contact-messages.update-status', $message) }}" method="POST" class="mb-6">
                                @csrf
                                @method('PUT')
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Update Status</label>
                                    <select name="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                        <option value="new" {{ $message->status === 'new' ? 'selected' : '' }}>New</option>
                                        <option value="read" {{ $message->status === 'read' ? 'selected' : '' }}>Read</option>
                                        <option value="replied" {{ $message->status === 'replied' ? 'selected' : '' }}>Replied</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                    Update Status
                                </button>
                            </form>
                            
                            <hr class="my-6">
                            
                            <form action="{{ route('admin.contact-messages.reply', $message) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Send Reply</label>
                                    <textarea name="reply" rows="8" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 {{ $errors->has('reply') ? 'border-red-300' : '' }}" placeholder="Type your reply here..." required>{{ old('reply') }}</textarea>
                                    @if($errors->has('reply'))
                                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('reply') }}</p>
                                    @endif
                                </div>
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Send Reply
                                </button>
                            </form>
                            
                            <hr class="my-6">
                            
                            <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="toggleModal('deleteModal')">
                                <i class="fas fa-trash mr-1"></i> Delete Message
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 z-10 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="toggleModal('deleteModal')"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Confirm Delete
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Are you sure you want to delete this message?
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <form action="{{ isset($message) ? route('admin.contact-messages.destroy', $message) : '#' }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Delete
                    </button>
                </form>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="toggleModal('deleteModal')">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }
</script>
@endsection