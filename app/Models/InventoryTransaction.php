<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'offer_id',
        'order_id',
        'quantity',
        'type',
        'notes',
        'user_id'
    ];

    /**
     * Get the product that this transaction affects.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the offer that this transaction affects.
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * Get the order that triggered this transaction.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who initiated this transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
