<?php

namespace App\Services;

use App\Models\Offer;
use App\Models\Order;
use App\Models\User;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OfferInventoryService
{
    /**
     * Check if an offer has sufficient stock for the requested quantity
     *
     * @param int $offerId
     * @param int $requestedQuantity
     * @return array
     */
    public function checkStock(int $offerId, int $requestedQuantity)
    {
        try {
            $offer = Offer::find($offerId);
            
            if (!$offer) {
                return [
                    'success' => false,
                    'message' => "Offer #{$offerId} not found"
                ];
            }
            
            if ($offer->stock < $requestedQuantity) {
                return [
                    'success' => false,
                    'message' => 'Insufficient stock',
                    'insufficient_items' => [
                        [
                            'offer_id' => $offer->id,
                            'offer_name' => $offer->title,
                            'requested' => $requestedQuantity,
                            'available' => $offer->stock
                        ]
                    ]
                ];
            }
            
            return [
                'success' => true,
                'message' => 'Offer has sufficient stock'
            ];
        } catch (\Exception $e) {
            Log::error("Error checking offer stock for Offer #{$offerId}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while checking offer stock: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Reduce stock for an offer
     *
     * @param int $offerId
     * @param int $quantity
     * @return array
     */
    public function reduceStock(int $offerId, int $quantity)
    {
        // First check if we have enough stock
        $stockCheck = $this->checkStock($offerId, $quantity);
        if (!$stockCheck['success']) {
            return $stockCheck;
        }
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Lock the offer row for update to prevent race conditions
            $offer = Offer::lockForUpdate()->find($offerId);
            
            if (!$offer) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => "Offer #{$offerId} not found"
                ];
            }
            
            // Update offer stock
            $offer->stock -= $quantity;
            $offer->save();
            
            // Create inventory transaction record
            InventoryTransaction::create([
                'product_id' => null,
                'offer_id' => $offer->id,
                'order_id' => null,
                'quantity' => -$quantity, // Negative for deductions
                'type' => 'offer_sale',
                'notes' => "Stock reduced for offer #{$offerId}",
                'user_id' => auth()->id()
            ]);
            
            // Check if offer is now low on stock and log it
            if ($offer->is_low_stock) {
                Log::info("Offer #{$offer->id} ({$offer->title}) is now low on stock: {$offer->stock} remaining");
            }
            
            // Commit transaction
            DB::commit();
            return [
                'success' => true,
                'message' => 'Offer stock reduced successfully'
            ];
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error("Error reducing stock for Offer #{$offerId}: " . $e->getMessage(), [
                'exception' => $e,
                'offer_id' => $offerId,
                'quantity' => $quantity
            ]);
            return [
                'success' => false,
                'message' => 'An error occurred while reducing offer stock: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Restore stock for an offer
     *
     * @param int $offerId
     * @param int $quantity
     * @param User|null $user
     * @param string $reason
     * @return bool
     */
    public function restoreStock(int $offerId, int $quantity, ?User $user = null, string $reason = 'refund')
    {
        $offer = Offer::find($offerId);
        
        if (!$offer) {
            Log::warning("Offer #{$offerId} not found when restoring offer stock");
            return false;
        }
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Lock the offer row for update to prevent race conditions
            $offerToUpdate = Offer::lockForUpdate()->find($offerId);
            
            // Update offer stock
            $offerToUpdate->stock += $quantity;
            $offerToUpdate->save();
            
            // Create inventory transaction record
            InventoryTransaction::create([
                'product_id' => null,
                'offer_id' => $offer->id,
                'order_id' => null,
                'quantity' => $quantity, // Positive for additions
                'type' => $reason,
                'notes' => "Stock restored for Offer #{$offerId} ({$reason})",
                'user_id' => $user ? $user->id : null
            ]);
            
            // Commit transaction
            DB::commit();
            return true;
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error("Error restoring stock for Offer #{$offerId}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Adjust stock for an offer
     *
     * @param Offer $offer
     * @param int $quantity
     * @param User|null $user
     * @param string|null $notes
     * @return bool
     */
    public function adjustStock(Offer $offer, int $quantity, ?User $user = null, ?string $notes = null)
    {
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Update offer stock
            $oldStock = $offer->stock;
            $offer->stock += $quantity; // Can be positive or negative
            
            // Prevent negative stock
            if ($offer->stock < 0) {
                $offer->stock = 0;
                $quantity = -$oldStock; // Adjust quantity to match actual deduction
            }
            
            $offer->save();
            
            // Create inventory transaction record
            InventoryTransaction::create([
                'product_id' => null,
                'offer_id' => $offer->id,
                'order_id' => null,
                'quantity' => $quantity,
                'type' => 'adjustment',
                'notes' => $notes ?? "Manual stock adjustment: {$quantity}",
                'user_id' => $user ? $user->id : null
            ]);
            
            // Commit transaction
            DB::commit();
            return true;
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error("Error adjusting stock for Offer #{$offer->id}: " . $e->getMessage());
            return false;
        }
    }
} 