<?php

namespace App\Observers;

use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\Log;

class InventoryTransactionObserver
{
    /**
     * Handle the InventoryTransaction "created" event.
     *
     * @param  \App\Models\InventoryTransaction  $transaction
     * @return void
     */
    public function created(InventoryTransaction $transaction)
    {
        $productName = $transaction->product ? $transaction->product->name : 'Unknown';
        $actionType = $transaction->quantity > 0 ? 'added to' : 'removed from';
        $quantityChange = abs($transaction->quantity);
        
        Log::info("Inventory transaction: {$quantityChange} units {$actionType} {$productName}", [
            'transaction_id' => $transaction->id,
            'product_id' => $transaction->product_id,
            'order_id' => $transaction->order_id,
            'quantity' => $transaction->quantity,
            'type' => $transaction->type,
            'notes' => $transaction->notes
        ]);
        
        // Check for critically low stock if this was a stock reduction
        if ($transaction->quantity < 0 && $transaction->product) {
            $stockWarningThreshold = 5; // This could be configurable
            if ($transaction->product->stock <= $stockWarningThreshold) {
                Log::warning("Low stock alert: {$productName} has only {$transaction->product->stock} units remaining", [
                    'product_id' => $transaction->product_id,
                    'current_stock' => $transaction->product->stock,
                    'warning_threshold' => $stockWarningThreshold
                ]);
                
                // Here you could trigger low stock notifications to admin
            }
        }
    }
} 