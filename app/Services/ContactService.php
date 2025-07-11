<?php

namespace App\Services;

use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactService
{
    /**
     * Process the contact form submission, store the message, and send emails.
     *
     * @param array $validated
     * @return \App\Models\ContactMessage
     */
    public function processContactForm(array $validated)
    {
        // Add user_id if user is logged in
        if (Auth::check()) {
            $validated['user_id'] = Auth::id();
        }

        // Set default status
        $validated['status'] = 'new';

        // Store the message in the database
        $contactMessage = ContactMessage::create($validated);

        // Send notification email to admin
        $this->sendAdminNotification($contactMessage);
        
        // Send auto-reply to the user
        $this->sendAutoReply($contactMessage);

        return $contactMessage;
    }

    /**
     * Send notification email to admin.
     *
     * @param \App\Models\ContactMessage $contactMessage
     * @return void
     */
    private function sendAdminNotification(ContactMessage $contactMessage)
    {
        try {
            $adminEmail = Setting::get(
                'contact_notification_email', 
                Setting::get('admin_email', 'admin@celestialcosmetics.com')
            );
            
            Mail::to($adminEmail)
                ->send(new \App\Mail\ContactFormNotification($contactMessage));
        } catch (\Exception $e) {
            // Log the error but don't disrupt the user experience
            Log::error('Failed to send contact form notification: ' . $e->getMessage());
        }
    }

    /**
     * Send auto-reply email to the user.
     *
     * @param \App\Models\ContactMessage $contactMessage
     * @return void
     */
    private function sendAutoReply(ContactMessage $contactMessage)
    {
        try {
            Mail::to($contactMessage->email)
                ->send(new \App\Mail\ContactFormAutoReply($contactMessage));
        } catch (\Exception $e) {
            // Log the error but don't disrupt the user experience
            Log::error('Failed to send contact form auto-reply: ' . $e->getMessage());
        }
    }
} 