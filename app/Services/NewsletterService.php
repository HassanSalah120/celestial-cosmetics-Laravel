<?php

namespace App\Services;

use App\Models\NewsletterSubscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NewsletterService
{
    /**
     * The email service instance.
     *
     * @var \App\Services\EmailService
     */
    protected $emailService;

    /**
     * Create a new service instance.
     *
     * @param \App\Services\EmailService $emailService
     * @return void
     */
    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Process a newsletter subscription.
     *
     * @param array $data
     * @return array
     */
    public function processSubscription(array $data)
    {
        // Check if the email already exists but was unsubscribed
        $existingSubscription = NewsletterSubscription::withTrashed()
            ->where('email', $data['email'])
            ->first();
            
        if ($existingSubscription) {
            return $this->handleExistingSubscription($existingSubscription, $data);
        } else {
            return $this->createNewSubscription($data);
        }
    }

    /**
     * Handle an existing subscription.
     *
     * @param \App\Models\NewsletterSubscription $subscription
     * @param array $data
     * @return array
     */
    private function handleExistingSubscription(NewsletterSubscription $subscription, array $data)
    {
        if ($subscription->trashed()) {
            // If it was soft deleted, restore it
            $subscription->restore();
            $subscription->update([
                'status' => 'active',
                'name' => $data['name'] ?? $subscription->name,
                'subscribed_at' => now(),
            ]);
            
            $this->sendConfirmationEmail($subscription);
            
            return [
                'type' => 'toast',
                'message' => 'Welcome back! Your subscription has been reactivated.',
            ];
        } elseif ($subscription->status === 'unsubscribed') {
            // If just unsubscribed, reactivate it
            $subscription->update([
                'status' => 'active',
                'name' => $data['name'] ?? $subscription->name,
                'subscribed_at' => now(),
            ]);
            
            $this->sendConfirmationEmail($subscription);
            
            return [
                'type' => 'toast',
                'message' => 'Welcome back! Your subscription has been reactivated.',
            ];
        } else {
            // Already subscribed
            return [
                'type' => 'toast_info',
                'message' => 'You are already subscribed to our newsletter!',
            ];
        }
    }

    /**
     * Create a new newsletter subscription.
     *
     * @param array $data
     * @return array
     */
    private function createNewSubscription(array $data)
    {
        $subscription = NewsletterSubscription::create([
            'email' => $data['email'],
            'name' => $data['name'] ?? null,
            'token' => NewsletterSubscription::generateToken(),
            'user_id' => Auth::check() ? Auth::id() : null,
            'subscribed_at' => now(),
        ]);
        
        $this->sendConfirmationEmail($subscription);
        
        return [
            'type' => 'toast',
            'message' => 'Thank you for subscribing to our newsletter!',
        ];
    }

    /**
     * Send confirmation email to the subscriber.
     *
     * @param \App\Models\NewsletterSubscription $subscription
     * @return void
     */
    private function sendConfirmationEmail(NewsletterSubscription $subscription)
    {
        try {
            $this->emailService->sendTemplatedEmail(
                $subscription->email,
                'newsletter_confirmation',
                [
                    'name' => $subscription->name ?: 'Celestial Explorer',
                    'email' => $subscription->email,
                    'unsubscribeUrl' => route('newsletter.unsubscribe', $subscription->token)
                ],
                $subscription->name,
                $subscription
            );
        } catch (\Exception $e) {
            Log::error('Failed to send newsletter confirmation email: ' . $e->getMessage());
        }
    }

    /**
     * Process a newsletter unsubscription.
     *
     * @param string $token
     * @return array
     */
    public function processUnsubscription($token)
    {
        $subscription = NewsletterSubscription::where('token', $token)->first();

        if (!$subscription) {
            return [
                'success' => false,
                'message' => 'Invalid unsubscribe link.',
            ];
        }

        $subscription->update(['status' => 'unsubscribed']);

        return [
            'success' => true,
            'message' => 'You have been successfully unsubscribed.',
        ];
    }
} 