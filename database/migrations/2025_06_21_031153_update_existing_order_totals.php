<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all orders with zero total_amount
        $orders = DB::table('orders')->where('total_amount', 0)->get();
        
        foreach ($orders as $order) {
            // Calculate subtotal from order items
            $subtotal = DB::table('order_items')
                ->where('order_id', $order->id)
                ->sum(DB::raw('price * quantity'));
            
            // Get discount amount
            $discount = $order->discount_amount ?? 0;
            
            // Get shipping fee
            $shipping = $order->shipping_fee ?? 0;
            
            // Get payment fee and COD fee
            $paymentFee = $order->payment_fee ?? 0;
            $codFee = $order->cod_fee ?? 0;
            
            // Calculate total
            $total = $subtotal - $discount + $shipping + $paymentFee + $codFee;
            
            // Update the order
            DB::table('orders')
                ->where('id', $order->id)
                ->update([
                    'subtotal' => $subtotal,
                    'total_amount' => $total
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be reversed as it corrects data
    }
};
