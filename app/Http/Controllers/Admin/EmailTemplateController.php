<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of email templates.
     */
    public function index()
    {
        $templates = EmailTemplate::orderBy('name')->get();
        
        return view('admin.emails.templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new email template.
     */
    public function create()
    {
        return view('admin.emails.templates.create');
    }

    /**
     * Store a newly created email template.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:email_templates',
            'description' => 'nullable|string',
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'body_text' => 'nullable|string',
            'available_variables' => 'nullable|string',
            'is_active' => 'boolean',
            'include_header_footer' => 'boolean',
        ]);

        // Convert available_variables to array
        if (!empty($validated['available_variables'])) {
            $variables = array_map('trim', explode(',', $validated['available_variables']));
            $validated['available_variables'] = $variables;
        }

        // Create template
        EmailTemplate::create($validated);

        return redirect()
            ->route('admin.emails.templates.index')
            ->with('success', 'Email template created successfully.');
    }

    /**
     * Display the specified email template.
     */
    public function show(EmailTemplate $template)
    {
        // Get recent logs for this template
        $logs = EmailLog::where('email_template_id', $template->id)
            ->orderBy('created_at', 'desc')
            ->take(30)
            ->get();

        return view('admin.emails.templates.show', compact('template', 'logs'));
    }

    /**
     * Show the form for editing the specified email template.
     */
    public function edit(EmailTemplate $template)
    {
        return view('admin.emails.templates.edit', compact('template'));
    }

    /**
     * Update the specified email template.
     */
    public function update(Request $request, EmailTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => "required|string|max:255|unique:email_templates,code,{$template->id}",
            'description' => 'nullable|string',
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'body_text' => 'nullable|string',
            'available_variables' => 'nullable|string',
            'is_active' => 'boolean',
            'include_header_footer' => 'boolean',
        ]);

        // Convert available_variables to array
        if (!empty($validated['available_variables'])) {
            $variables = array_map('trim', explode(',', $validated['available_variables']));
            $validated['available_variables'] = $variables;
        }

        // Update template
        $template->update($validated);

        return redirect()
            ->route('admin.emails.templates.index')
            ->with('success', 'Email template updated successfully.');
    }

    /**
     * Remove the specified email template.
     */
    public function destroy(EmailTemplate $template)
    {
        $template->delete();

        return redirect()
            ->route('admin.emails.templates.index')
            ->with('success', 'Email template deleted successfully.');
    }

    /**
     * Preview the email template with sample data.
     */
    public function preview(EmailTemplate $template)
    {
        // Generate some sample data for the preview
        $variables = [];
        if ($template->available_variables) {
            // Decode the JSON string into an array
            $availableVars = is_array($template->available_variables) 
                ? $template->available_variables 
                : json_decode($template->available_variables, true);
                
            if (is_array($availableVars)) {
                foreach ($availableVars as $variable) {
                    $variables[$variable] = "Sample {$variable}";
                }
            }
        }

        // Special case for common variables
        $availableVars = is_array($template->available_variables) 
            ? $template->available_variables 
            : json_decode($template->available_variables, true) ?? [];
            
        if (in_array('name', $availableVars)) {
            $variables['name'] = 'John Doe';
        }
        
        if (in_array('email', $availableVars)) {
            $variables['email'] = 'john.doe@example.com';
        }
        
        if (in_array('customer_name', $availableVars)) {
            $variables['customer_name'] = 'John Doe';
        }
        
        if (in_array('order_number', $availableVars)) {
            $variables['order_number'] = '12345';
        }
        
        if (in_array('order_date', $availableVars)) {
            $variables['order_date'] = date('F j, Y');
        }
        
        if (in_array('order_total', $availableVars)) {
            $variables['order_total'] = '$99.99';
        }
        
        if (in_array('verification_link', $availableVars)) {
            $variables['verification_link'] = 'https://celestial-cosmetics.com/verify/sample-token';
        }
        
        if (in_array('message', $availableVars)) {
            $variables['message'] = 'This is a sample message to show how the template will look with user content.';
        }
        
        if (in_array('unsubscribe_link', $availableVars)) {
            $variables['unsubscribe_link'] = 'https://celestial-cosmetics.com/unsubscribe/sample-token';
        }
        
        if (in_array('refund_amount', $availableVars)) {
            $variables['refund_amount'] = '$49.99';
        }
        
        if (in_array('refund_date', $availableVars)) {
            $variables['refund_date'] = date('F j, Y');
        }

        // Replace variables in the template
        $html = $template->parseBodyHtml($variables);
        $subject = $template->parseSubject($variables);

        return view('admin.emails.templates.preview', compact('template', 'html', 'subject', 'variables'));
    }

    /**
     * Clone an existing template.
     */
    public function clone(EmailTemplate $template)
    {
        $newTemplate = $template->replicate();
        $newTemplate->name = "Copy of {$template->name}";
        $newTemplate->code = "{$template->code}_" . Str::random(5);
        $newTemplate->is_active = false;
        $newTemplate->save();

        return redirect()
            ->route('admin.emails.templates.edit', $newTemplate)
            ->with('success', 'Email template cloned successfully.');
    }

    /**
     * Show the email logs.
     */
    public function logs(Request $request)
    {
        $query = EmailLog::with('emailTemplate')->orderBy('created_at', 'desc');

        // Filter by template
        if ($request->has('template_id') && $request->template_id) {
            $query->where('email_template_id', $request->template_id);
        }

        // Filter by success/failure
        if ($request->has('status')) {
            if ($request->status === 'success') {
                $query->where('success', true);
            } elseif ($request->status === 'failed') {
                $query->where('success', false);
            }
        }

        // Filter by recipient
        if ($request->has('to_email') && $request->to_email) {
            $query->where('to_email', 'like', "%{$request->to_email}%");
        }

        $logs = $query->paginate(20)->withQueryString();
        $templates = EmailTemplate::orderBy('name')->pluck('name', 'id');

        return view('admin.emails.logs.index', compact('logs', 'templates'));
    }

    /**
     * Show a specific email log.
     */
    public function showLog(EmailLog $log)
    {
        return view('admin.emails.logs.show', compact('log'));
    }

    /**
     * Show test email form.
     */
    public function showTestEmailForm()
    {
        return view('admin.emails.test');
    }

    /**
     * Send a test email.
     */
    public function sendTestEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $details = [
                'title' => 'Test Email from Celestial Cosmetics',
                'body' => 'This is a test email to verify that your email configuration is working correctly.'
            ];

            Mail::to($validated['email'])->send(new \App\Mail\TestMail($details));

            return redirect()
                ->route('admin.emails.test')
                ->with('success', 'Test email sent successfully to ' . $validated['email']);
        } catch (\Exception $e) {
            Log::error('Failed to send test email: ' . $e->getMessage());
            
            return redirect()
                ->route('admin.emails.test')
                ->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }
}
