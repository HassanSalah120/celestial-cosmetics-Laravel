<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class NewsletterController extends Controller
{
    /**
     * The email service instance.
     *
     * @var \App\Services\EmailService
     */
    protected $emailService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\EmailService $emailService
     * @return void
     */
    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }
    
    /**
     * Display a listing of the subscribers.
     */
    public function index(Request $request)
    {
        $query = NewsletterSubscription::query();
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        // Stats for the dashboard cards
        $stats = [
            'total' => NewsletterSubscription::count(),
            'active' => NewsletterSubscription::active()->count(),
            'unsubscribed' => NewsletterSubscription::unsubscribed()->count(),
        ];
        
        $subscribers = $query->orderBy('created_at', 'desc')
                           ->paginate(15)
                           ->withQueryString();
        
        return view('admin.newsletters.index', compact('subscribers', 'stats'));
    }

    /**
     * Show the form for creating a newsletter.
     */
    public function create()
    {
        return view('admin.newsletters.create');
    }

    /**
     * Show the details of a subscriber.
     */
    public function show(NewsletterSubscription $subscriber)
    {
        return view('admin.newsletters.show', compact('subscriber'));
    }

    /**
     * Send newsletter to subscribers.
     */
    public function send(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'test_email' => 'nullable|email',
        ]);
        
        // Check if it's a test email
        if ($request->input('action') === 'test') {
            if (!$request->filled('test_email')) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Please provide a test email address.');
            }
            
            try {
                $testEmail = $request->test_email;
                $this->emailService->sendTemplatedEmail(
                    $testEmail,
                    'newsletter_broadcast',
                    [
                        'subject' => $request->subject,
                        'content' => $request->content,
                        'name' => 'Test User',
                        'email' => $testEmail,
                        'unsubscribeUrl' => route('newsletter.unsubscribe', 'test-token')
                    ],
                    'Test User'
                );
                
                return redirect()->back()
                    ->with('success', "Test email sent successfully to {$testEmail}.");
            } catch (\Exception $e) {
                Log::error('Failed to send test newsletter: ' . $e->getMessage());
                
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to send test email: ' . $e->getMessage());
            }
        }
        
        // Sending to all active subscribers
        $activeSubscribersCount = NewsletterSubscription::active()->count();
        
        if ($activeSubscribersCount === 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'There are no active subscribers to send the newsletter to.');
        }
        
        // Queue the emails for sending to avoid timeouts
        $successCount = 0;
        $failedCount = 0;
        
        try {
            // Use database transaction to ensure all or nothing
            DB::beginTransaction();
            
            $subscribers = NewsletterSubscription::active()->get();
            
            foreach ($subscribers as $subscriber) {
                try {
                    $this->emailService->sendTemplatedEmail(
                        $subscriber->email,
                        'newsletter_broadcast',
                        [
                            'subject' => $request->subject, 
                            'content' => $request->content,
                            'name' => $subscriber->name ?: 'Celestial Explorer',
                            'email' => $subscriber->email,
                            'unsubscribeUrl' => route('newsletter.unsubscribe', $subscriber->token)
                        ],
                        $subscriber->name,
                        $subscriber
                    );
                    
                    $successCount++;
                } catch (\Exception $e) {
                    Log::error("Failed to send newsletter to {$subscriber->email}: " . $e->getMessage());
                    $failedCount++;
                }
            }
            
            DB::commit();
            
            $message = "Newsletter sent successfully to {$successCount} subscribers.";
            if ($failedCount > 0) {
                $message .= " Failed to send to {$failedCount} subscribers. Check logs for details.";
            }
            
            return redirect()->route('admin.newsletters.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to send newsletter: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while sending the newsletter. Please try again.');
        }
    }

    /**
     * Remove the specified subscriber.
     */
    public function destroy(NewsletterSubscription $subscriber)
    {
        $subscriber->delete();
        
        return redirect()->route('admin.newsletters.index')
            ->with('success', 'Subscriber deleted successfully.');
    }

    /**
     * Toggle the subscriber's status between active and unsubscribed.
     */
    public function toggleStatus(NewsletterSubscription $subscriber)
    {
        $newStatus = $subscriber->status === 'active' ? 'unsubscribed' : 'active';
        $subscriber->update(['status' => $newStatus]);
        
        $message = $newStatus === 'active' 
            ? 'Subscriber has been reactivated.' 
            : 'Subscriber has been unsubscribed.';
        
        return redirect()->back()->with('success', $message);
    }
}
