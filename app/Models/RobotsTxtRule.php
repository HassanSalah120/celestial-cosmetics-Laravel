<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RobotsTxtRule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_agent',
        'directive',
        'value',
        'order',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    /**
     * Scope a query to only include active rules.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order rules by their order value.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Generate the robots.txt content from all active rules.
     *
     * @return string
     */
    public static function generateRobotsTxt()
    {
        $rules = self::active()->ordered()->get();
        $content = '';
        $currentUserAgent = null;
        
        foreach ($rules as $rule) {
            if ($currentUserAgent !== $rule->user_agent) {
                // Add a blank line between user agent sections
                if ($currentUserAgent !== null) {
                    $content .= "\n";
                }
                
                $content .= "User-agent: {$rule->user_agent}\n";
                $currentUserAgent = $rule->user_agent;
            }
            
            if ($rule->directive === 'crawl-delay') {
                $content .= "Crawl-delay: {$rule->value}\n";
            } elseif ($rule->directive === 'sitemap') {
                $content .= "Sitemap: {$rule->value}\n";
            } else {
                $directive = ucfirst($rule->directive);
                $content .= "{$directive}: {$rule->value}\n";
            }
        }
        
        return $content;
    }
} 