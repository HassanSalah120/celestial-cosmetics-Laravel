<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsletterSubscription extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'name',
        'status',
        'token',
        'user_id',
        'subscribed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'subscribed_at' => 'datetime',
    ];

    /**
     * Get the user associated with the subscription.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get only active subscribers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get only unsubscribed subscribers.
     */
    public function scopeUnsubscribed($query)
    {
        return $query->where('status', 'unsubscribed');
    }

    /**
     * Generate a unique token for this subscription.
     */
    public static function generateToken()
    {
        return md5(uniqid() . time() . rand(10000, 99999));
    }

    /**
     * Find a subscription by token.
     */
    public static function findByToken(string $token)
    {
        return static::where('token', $token)->first();
    }

    /**
     * Get the unsubscribe URL for this subscription.
     */
    public function getUnsubscribeUrl(): string
    {
        return route('newsletter.unsubscribe', ['token' => $this->token]);
    }
}
