<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $changedFields;
    public $oldValues;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Order  $order
     * @param  array  $changedFields Array of changed field names (e.g., ['status', 'payment_status'])
     * @param  array  $oldValues Array of old values keyed by field name
     * @return void
     */
    public function __construct(Order $order, array $changedFields, array $oldValues)
    {
        $this->order = $order;
        $this->changedFields = $changedFields;
        $this->oldValues = $oldValues;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Update on Your Order #' . $this->order->id . ' - Celestial Cosmetics';
        
        return $this->subject($subject)
                    ->view('emails.order-status-update');
    }
} 