<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Services\ContactService;
use App\Helpers\TranslationHelper;

class ContactController extends Controller
{
    protected $contactService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\ContactService $contactService
     * @return void
     */
    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    /**
     * Process the contact form submission.
     *
     * @param \App\Http\Requests\ContactFormRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submit(ContactFormRequest $request)
    {
        // Store the message and send notifications
        $this->contactService->processContactForm($request->validated());

        // Redirect back with success message
        return redirect()
            ->route('contact')
            ->with('toast', TranslationHelper::get('contact_success_message', 'Thank you for your message! We will respond as soon as possible.'));
    }
}
