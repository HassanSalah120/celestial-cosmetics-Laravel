<?php

namespace App\Mail;

use App\Models\EmailLog;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;

class TemplatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The email template.
     *
     * @var \App\Models\EmailTemplate
     */
    protected $template;

    /**
     * The template code if template object not available.
     *
     * @var string|null
     */
    protected $templateCode;

    /**
     * The variables to replace in the template.
     *
     * @var array
     */
    protected $variables = [];

    /**
     * The related model (optional).
     *
     * @var \Illuminate\Database\Eloquent\Model|null
     */
    protected $relatedModel = null;

    /**
     * Create a new message instance.
     *
     * @param string $templateCode
     * @param array $variables
     * @param \Illuminate\Database\Eloquent\Model|null $relatedModel
     */
    public function __construct(string $templateCode, array $variables = [], ?Model $relatedModel = null)
    {
        $this->templateCode = $templateCode;
        $this->variables = $variables;
        $this->relatedModel = $relatedModel;
        
        // Try to load the template
        $this->template = EmailTemplate::findByCode($templateCode);

        if (!$this->template) {
            // Log that template was not found
            logger()->warning("Email template not found: {$templateCode}");
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->template 
            ? $this->template->parseSubject($this->variables)
            : 'No Template Found: ' . $this->templateCode;

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        if ($this->template) {
            $html = $this->template->parseBodyHtml($this->variables);
            $text = $this->template->parseBodyText($this->variables);

            return new Content(
                htmlString: $html,
                text: $text,
            );
        }

        // Fallback content if template is not found
        return new Content(
            htmlString: '<p>Template not found: ' . $this->templateCode . '</p>',
            text: 'Template not found: ' . $this->templateCode,
        );
    }

    /**
     * Log this email in the database.
     */
    public function build()
    {
        // Create log entry
        $log = new EmailLog([
            'email_template_id' => $this->template ? $this->template->id : null,
            'template_code' => $this->templateCode,
            'from_email' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
            'to_email' => $this->to[0]['address'] ?? 'unknown@example.com',
            'to_name' => $this->to[0]['name'] ?? null,
            'subject' => $this->subject,
            'body_html' => $this->html,
            'body_text' => $this->text,
            'variables' => $this->variables,
            'cc' => $this->cc,
            'bcc' => $this->bcc,
            'attachments' => $this->attachments,
            'success' => true, // Will be updated if fails
            'sent_at' => now(),
        ]);

        // Add related model if provided
        if ($this->relatedModel) {
            $log->related_model_type = get_class($this->relatedModel);
            $log->related_model_id = $this->relatedModel->getKey();
        }

        $log->save();

        return $this;
    }

    /**
     * Handle the mail sending failure.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function failed($exception)
    {
        // Update the log to mark as failed
        EmailLog::where([
            'template_code' => $this->templateCode,
            'to_email' => $this->to[0]['address'] ?? 'unknown@example.com',
        ])
        ->whereNull('error_message')
        ->orderBy('created_at', 'desc')
        ->first()
        ?->update([
            'success' => false,
            'error_message' => $exception->getMessage(),
        ]);
    }
}
