<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminContactReply extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The contact message instance.
     *
     * @var \App\Models\ContactMessage
     */
    public $contactMessage;

    /**
     * The reply message.
     *
     * @var string
     */
    public $reply;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ContactMessage $contactMessage, string $reply)
    {
        $this->contactMessage = $contactMessage;
        $this->reply = $reply;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Re: ' . $this->contactMessage->subject . ' - Celestial Cosmetics',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.contact.admin-reply',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
