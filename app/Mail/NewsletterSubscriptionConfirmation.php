<?php

namespace App\Mail;

use App\Models\NewsletterSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class NewsletterSubscriptionConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The subscription instance.
     */
    public $subscription;

    /**
     * Create a new message instance.
     */
    public function __construct(NewsletterSubscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Celestial Cosmetics Newsletter',
            from: new Address('newsletter@celestialcosmetics.com', 'Celestial Cosmetics'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter.subscription-confirmation',
            with: [
                'name' => $this->subscription->name,
                'unsubscribeUrl' => $this->subscription->getUnsubscribeUrl(),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
