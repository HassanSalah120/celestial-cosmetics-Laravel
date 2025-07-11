<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        Log::info('New order created', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'user_id' => $order->user_id,
            'total' => $order->total,
            'status' => $order->status
        ]);
    }

    /**
     * Handle the Order "updated" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        // Log when order status changes
        if ($order->isDirty('status')) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;
            
            Log::info('Order status changed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);
            
            // You could trigger specific actions based on status changes
            if ($newStatus == 'canceled' && $oldStatus != 'canceled') {
                // Order has been canceled - could trigger additional logic here
                Log::info('Order has been canceled', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number
                ]);
            }
            
            if ($newStatus == 'completed' && $oldStatus != 'completed') {
                // Order has been completed - could trigger additional logic here
                Log::info('Order has been completed', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number
                ]);
            }
        }
        
        // Log when payment status changes
        if ($order->isDirty('payment_status')) {
            $oldPaymentStatus = $order->getOriginal('payment_status');
            $newPaymentStatus = $order->payment_status;
            
            Log::info('Order payment status changed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_payment_status' => $oldPaymentStatus,
                'new_payment_status' => $newPaymentStatus
            ]);
            
            // You could trigger specific actions based on payment status changes
            if ($newPaymentStatus == 'paid' && $oldPaymentStatus != 'paid') {
                // Payment has been received - could trigger additional logic here
                Log::info('Payment has been received', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number
                ]);
            }
        }
    }

    /**
     * Handle the Order "deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
        Log::warning('Order deleted', [
            'order_id' => $order->id,
            'order_number' => $order->order_number
        ]);
    }
} 