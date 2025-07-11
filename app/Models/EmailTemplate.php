<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'subject',
        'body_html',
        'body_text',
        'available_variables',
        'is_active',
        'css_styles',
        'include_header_footer',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'available_variables' => 'array',
        'css_styles' => 'array',
        'is_active' => 'boolean',
        'include_header_footer' => 'boolean',
    ];

    /**
     * Get the email logs for this template.
     */
    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }

    /**
     * Get an email template by its code.
     *
     * @param string $code
     * @return EmailTemplate|null
     */
    public static function findByCode(string $code)
    {
        return static::where('code', $code)->where('is_active', true)->first();
    }

    /**
     * Parse the body HTML with provided variables.
     *
     * @param array $variables
     * @return string
     */
    public function parseBodyHtml(array $variables = []): string
    {
        $html = $this->body_html;
        
        foreach ($variables as $key => $value) {
            $html = str_replace('{{' . $key . '}}', $value, $html);
        }
        
        return $html;
    }

    /**
     * Parse the subject with provided variables.
     *
     * @param array $variables
     * @return string
     */
    public function parseSubject(array $variables = []): string
    {
        $subject = $this->subject;
        
        foreach ($variables as $key => $value) {
            $subject = str_replace('{{' . $key . '}}', $value, $subject);
        }
        
        return $subject;
    }

    /**
     * Parse the body text with provided variables.
     *
     * @param array $variables
     * @return string|null
     */
    public function parseBodyText(array $variables = []): ?string
    {
        if (!$this->body_text) {
            return null;
        }
        
        $text = $this->body_text;
        
        foreach ($variables as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value, $text);
        }
        
        return $text;
    }
}
