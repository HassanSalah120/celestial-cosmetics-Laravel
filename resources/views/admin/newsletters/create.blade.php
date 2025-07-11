@extends('layouts.admin')

@section('title', 'Send Newsletter')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <!-- Page header -->
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Send Newsletter</h1>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-lg rounded-sm border border-slate-200 p-6">
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-800 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-800 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.newsletters.send') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <!-- Subject -->
                <div>
                    <label for="subject" class="block text-sm font-medium mb-1">Email Subject <span class="text-rose-500">*</span></label>
                    <input id="subject" name="subject" type="text" class="form-input w-full" value="{{ old('subject') }}" required>
                    @error('subject')
                        <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Content Editor -->
                <div>
                    <label for="content" class="block text-sm font-medium mb-1">Email Content <span class="text-rose-500">*</span></label>
                    <textarea id="content" name="content" class="form-textarea w-full h-72" required>{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="bg-amber-50 text-amber-800 p-4 rounded mb-6">
                    <h3 class="font-semibold mb-2">Available Variables</h3>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        <li><code>{name}</code> - The subscriber's name (if available)</li>
                        <li><code>{email}</code> - The subscriber's email address</li>
                        <li>Unsubscribe link is automatically added to the footer</li>
                    </ul>
                </div>
                
                <!-- Test Email -->
                <div class="border-t border-slate-200 pt-6">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Send Test Email</h3>
                    <div class="flex items-end gap-4">
                        <div class="grow">
                            <label for="test_email" class="block text-sm font-medium mb-1">Test Email Address</label>
                            <input id="test_email" name="test_email" type="email" class="form-input w-full" value="{{ old('test_email') }}">
                            @error('test_email')
                                <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <button type="submit" name="action" value="test" class="btn border-slate-200 hover:border-slate-300 text-slate-600">Send Test</button>
                        </div>
                    </div>
                </div>
                
                <!-- Form buttons -->
                <div class="border-t border-slate-200 pt-6 flex items-center justify-between">
                    <a href="{{ route('admin.newsletters.index') }}" class="btn border-slate-200 hover:border-slate-300 text-slate-600">Cancel</a>
                    <div class="flex items-center">
                        <div class="hidden mr-3 sm:block">
                            <span class="text-sm text-slate-500 italic">This will send to {{ App\Models\NewsletterSubscription::active()->count() }} active subscribers</span>
                        </div>
                        <button type="submit" class="btn bg-primary hover:bg-primary-dark text-white">Send Newsletter</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        tinymce.init({
            selector: '#content',
            height: 400,
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 16px; }'
        });
    });
</script>
@endsection

@endsection 