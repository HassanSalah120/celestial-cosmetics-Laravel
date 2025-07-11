<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'status',
        'user_id',
        'reply',
        'replied_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'replied_at' => 'datetime',
    ];

    /**
     * Get the user that sent the contact message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
