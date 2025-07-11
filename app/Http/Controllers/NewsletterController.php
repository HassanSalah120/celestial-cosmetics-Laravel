<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsletterSubscribeRequest;
use App\Models\NewsletterSubscription;
use App\Services\NewsletterService;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /**
     * The newsletter service instance.
     *
     * @var \App\Services\NewsletterService
     */
    protected $newsletterService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\NewsletterService $newsletterService
     * @return void
     */
    public function __construct(NewsletterService $newsletterService)
    {
        $this->newsletterService = $newsletterService;
    }

    /**
     * Subscribe to the newsletter.
     * 
     * @param \App\Http\Requests\NewsletterSubscribeRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function subscribe(NewsletterSubscribeRequest $request)
    {
        $result = $this->newsletterService->processSubscription($request->validated());

        return redirect()->back()->with([
            $result['type'] => $result['message'],
            'newsletter' => true,
        ]);
    }

    /**
     * Unsubscribe from the newsletter.
     * 
     * @param Request $request
     * @param string $token
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function unsubscribe(Request $request, $token)
    {
        $result = $this->newsletterService->processUnsubscription($token);
        
        if (!$result['success']) {
            return redirect()->route('home')->with('toast_error', $result['message']);
        }

        return view('newsletter.unsubscribed');
    }
}
