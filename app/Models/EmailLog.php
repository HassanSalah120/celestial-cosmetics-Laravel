<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EmailLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email_template_id',
        'template_code',
        'from_email',
        'from_name',
        'to_email',
        'to_name',
        'subject',
        'body_html',
        'body_text',
        'variables',
        'cc',
        'bcc',
        'attachments',
        'sent_at',
        'success',
        'error_message',
        'related_model_type',
        'related_model_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'variables' => 'array',
        'cc' => 'array',
        'bcc' => 'array',
        'attachments' => 'array',
        'sent_at' => 'datetime',
        'success' => 'boolean',
    ];

    /**
     * Get the email template that was used.
     */
    public function emailTemplate()
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    /**
     * Get the parent relatable model.
     */
    public function relatable(): MorphTo
    {
        return $this->morphTo('related_model');
    }

    /**
     * Scope a query to only include emails sent to a specific recipient.
     */
    public function scopeToEmail($query, $email)
    {
        return $query->where('to_email', $email);
    }

    /**
     * Scope a query to only include successful emails.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    /**
     * Scope a query to only include failed emails.
     */
    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    /**
     * Scope a query to only include emails of a specific template code.
     */
    public function scopeOfTemplate($query, $code)
    {
        return $query->where('template_code', $code);
    }
}
