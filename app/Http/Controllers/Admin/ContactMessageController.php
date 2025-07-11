<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminReplyMail;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of the contact messages.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = ContactMessage::query();
        
        // Filter by status if provided
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }
        
        // Order by newest first
        $query->orderBy('created_at', 'desc');
        
        $messages = $query->paginate(20);
        
        return view('admin.contact-messages.index', compact('messages'));
    }

    /**
     * Display the specified contact message.
     *
     * @param  \App\Models\ContactMessage  $message
     * @return \Illuminate\Http\Response
     */
    public function show(ContactMessage $message)
    {
        // If message is new, mark as read
        if ($message->status === 'new') {
            $message->update(['status' => 'read']);
        }
        
        return view('admin.contact-messages.show', compact('message'));
    }

    /**
     * Update the status of the specified contact message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ContactMessage  $message
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, ContactMessage $message)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,read,replied',
        ]);
        
        $message->update($validated);
        
        return redirect()->route('admin.contact-messages.show', $message)
            ->with('success', 'Message status updated successfully.');
    }

    /**
     * Send a reply to the contact message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ContactMessage  $message
     * @return \Illuminate\Http\Response
     */
    public function reply(Request $request, ContactMessage $message)
    {
        $validated = $request->validate([
            'reply' => 'required|string|min:10',
        ]);
        
        try {
            // Send email
            Mail::to($message->email)->send(new AdminReplyMail($message, $validated['reply']));

            // Update message with reply and status
            $message->update([
                'admin_reply' => $validated['reply'],
                'replied_at' => now(),
                'status' => 'replied'
            ]);

            return redirect()->route('admin.contact-messages.show', $message)
                ->with('success', 'Reply sent successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to send reply email: ' . $e->getMessage());
            
            return redirect()->route('admin.contact-messages.show', $message)
                ->with('error', 'Failed to send reply email. Please try again later.');
        }
    }

    /**
     * Remove the specified contact message from storage.
     *
     * @param  \App\Models\ContactMessage  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContactMessage $message)
    {
        $message->delete();
        
        return redirect()->route('admin.contact-messages.index')
            ->with('success', 'Message deleted successfully.');
    }
}
