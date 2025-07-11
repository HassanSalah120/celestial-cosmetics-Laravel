<?php

namespace App\Services;

use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    /**
     * Deduct stock from products in an order
     *
     * @param Order $order
     * @param User|null $user
     * @return array Returns array with success status and any error messages
     */
    public function deductStockFromOrder(Order $order, ?User $user = null)
    {
        // Start transaction
        DB::beginTransaction();
        
        try {
            $errors = [];
            $insufficientStockItems = [];
            
            // First, validate all items have sufficient stock
            foreach ($order->items as $item) {
                // Skip if product doesn't exist
                if (!$item->product) {
                    continue;
                }
                
                // Lock the product row for update to prevent race conditions
                $product = Product::lockForUpdate()->find($item->product->id);
                if (!$product) {
                    // Product might have been deleted
                    $errors[] = "Product #{$item->product_id} not found";
                    continue;
                }
                
                // Check if we have enough stock
                if ($product->stock < $item->quantity) {
                    $insufficientStockItems[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'requested' => $item->quantity,
                        'available' => $product->stock
                    ];
                }
            }
            
            // If any products have insufficient stock, rollback and return error
            if (!empty($insufficientStockItems)) {
                DB::rollBack();
                
                // Log the inventory issue
                Log::warning("Insufficient stock for order #{$order->id}", [
                    'order_id' => $order->id,
                    'insufficient_items' => $insufficientStockItems
                ]);
                
                return [
                    'success' => false,
                    'message' => 'One or more products have insufficient stock',
                    'insufficient_items' => $insufficientStockItems
                ];
            }
            
            // Now that we've validated stock, proceed with deduction
            foreach ($order->items as $item) {
                // Skip if product doesn't exist
                if (!$item->product) {
                    continue;
                }
                
                // Lock the product row for update to prevent race conditions
                $product = Product::lockForUpdate()->find($item->product->id);
                if (!$product) {
                    // This shouldn't happen since we already checked above
                    continue;
                }
                
                // Update product stock
                $product->stock -= $item->quantity;
                $product->save();
                
                // Create inventory transaction record
                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'order_id' => $order->id,
                    'quantity' => -$item->quantity, // Negative for deductions
                    'type' => 'sale',
                    'notes' => "Stock deducted for Order #{$order->id}",
                    'user_id' => $user ? $user->id : null
                ]);
                
                // Check if product is now low on stock and log it
                if ($product->is_low_stock) {
                    Log::info("Product #{$product->id} ({$product->name}) is now low on stock: {$product->stock} remaining");
                }
            }
            
            // Commit transaction
            DB::commit();
            return [
                'success' => true,
                'message' => 'Stock deducted successfully'
            ];
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error("Error deducting stock for Order #{$order->id}: " . $e->getMessage(), [
                'exception' => $e,
                'order_id' => $order->id
            ]);
            return [
                'success' => false,
                'message' => 'An error occurred while processing inventory: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Restore stock for products in an order (refund or cancellation)
     *
     * @param Order $order
     * @param User|null $user
     * @param string $reason
     * @return bool
     */
    public function restoreStockFromOrder(Order $order, ?User $user = null, string $reason = 'refund')
    {
        // Start transaction
        DB::beginTransaction();
        
        try {
            foreach ($order->items as $item) {
                // Skip if product doesn't exist
                if (!$item->product) {
                    continue;
                }
                
                // Lock the product row for update to prevent race conditions
                $product = Product::lockForUpdate()->find($item->product->id);
                if (!$product) {
                    // Product might have been deleted
                    continue;
                }
                
                // Update product stock
                $product->stock += $item->quantity;
                $product->save();
                
                // Create inventory transaction record
                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'order_id' => $order->id,
                    'quantity' => $item->quantity, // Positive for additions
                    'type' => $reason,
                    'notes' => "Stock restored for Order #{$order->id} ({$reason})",
                    'user_id' => $user ? $user->id : null
                ]);
            }
            
            // Commit transaction
            DB::commit();
            return true;
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error("Error restoring stock for Order #{$order->id}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Manually adjust stock for a product
     *
     * @param Product $product
     * @param int $quantity
     * @param User|null $user
     * @param string|null $notes
     * @return bool
     */
    public function adjustStock(Product $product, int $quantity, ?User $user = null, ?string $notes = null)
    {
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Update product stock
            $oldStock = $product->stock;
            $product->stock += $quantity; // Can be positive or negative
            
            // Prevent negative stock
            if ($product->stock < 0) {
                $product->stock = 0;
                $quantity = -$oldStock; // Adjust quantity to match actual deduction
            }
            
            $product->save();
            
            // Create inventory transaction record
            InventoryTransaction::create([
                'product_id' => $product->id,
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
            Log::error("Error adjusting stock for Product #{$product->id}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if a product has sufficient stock for the requested quantity
     *
     * @param int $productId
     * @param int $requestedQuantity
     * @return bool
     */
    public function checkStock(int $productId, int $requestedQuantity)
    {
        $product = Product::find($productId);
        
        if (!$product) {
            Log::warning("Product #{$productId} not found when checking stock");
            return false;
        }
        
        return $product->stock >= $requestedQuantity;
    }
    
    /**
     * Reduce stock for a product by a specific quantity
     *
     * @param int $productId
     * @param int $quantity
     * @return array
     */
    public function reduceStock(int $productId, int $quantity)
    {
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Lock the product row for update to prevent race conditions
            $product = Product::lockForUpdate()->find($productId);
        
            if (!$product) {
                Log::warning("Product #{$productId} not found when reducing stock");
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => "Product #{$productId} not found"
                ];
            }
            
            // Check if we have enough stock
            if ($product->stock < $quantity) {
                DB::rollBack();
                Log::warning("Insufficient stock for product #{$productId} ({$product->name}). Requested: {$quantity}, Available: {$product->stock}");
                return [
                    'success' => false,
                    'message' => 'Insufficient stock',
                    'insufficient_items' => [
                        [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'requested' => $quantity,
                            'available' => $product->stock
                        ]
                    ]
                ];
            }
            
            // Update product stock
            $product->stock -= $quantity;
            $product->save();
            
            // Create inventory transaction record
            InventoryTransaction::create([
                'product_id' => $product->id,
                'order_id' => null,
                'quantity' => -$quantity, // Negative for deductions
                'type' => 'sale',
                'notes' => "Stock reduced during checkout",
                'user_id' => auth()->id()
            ]);
            
            // Check if product is now low on stock and log it
            if ($product->is_low_stock) {
                Log::info("Product #{$product->id} ({$product->name}) is now low on stock: {$product->stock} remaining");
            }
            
            // Commit transaction
            DB::commit();
            return [
                'success' => true,
                'message' => 'Stock reduced successfully'
            ];
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error("Error reducing stock for Product #{$productId}: " . $e->getMessage(), [
                'exception' => $e,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
            return [
                'success' => false,
                'message' => 'An error occurred while reducing stock: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Check if a bundle has sufficient stock for the requested quantity
     *
     * @param int $offerId
     * @param int $requestedQuantity
     * @return array
     */
    public function checkBundleStock(int $offerId, int $requestedQuantity)
    {
        try {
            $offer = \App\Models\Offer::with('products')->find($offerId);
            
            if (!$offer) {
                return [
                    'success' => false,
                    'message' => "Offer #{$offerId} not found"
                ];
            }
            
            $insufficientItems = [];
            
            // Check each product in the bundle
            foreach ($offer->products as $product) {
                $requiredQuantity = $product->pivot->quantity * $requestedQuantity;
                
                if ($product->stock < $requiredQuantity) {
                    $insufficientItems[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'requested' => $requiredQuantity,
                        'available' => $product->stock
                    ];
                }
            }
            
            if (!empty($insufficientItems)) {
                return [
                    'success' => false,
                    'message' => 'One or more products in the bundle have insufficient stock',
                    'insufficient_items' => $insufficientItems
                ];
            }
            
            return [
                'success' => true,
                'message' => 'Bundle has sufficient stock'
            ];
        } catch (\Exception $e) {
            Log::error("Error checking bundle stock for Offer #{$offerId}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while checking bundle stock: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Reduce stock for all products in a bundle
     *
     * @param int $offerId
     * @param int $quantity
     * @return array
     */
    public function reduceBundleStock(int $offerId, int $quantity)
    {
        // First check if we have enough stock
        $stockCheck = $this->checkBundleStock($offerId, $quantity);
        if (!$stockCheck['success']) {
            return $stockCheck;
        }
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            $offer = \App\Models\Offer::with('products')->find($offerId);
            
            if (!$offer) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => "Offer #{$offerId} not found"
                ];
            }
            
            // Reduce stock for each product in the bundle
            foreach ($offer->products as $product) {
                $deductAmount = $product->pivot->quantity * $quantity;
                
                // Lock the product row for update
                $productToUpdate = Product::lockForUpdate()->find($product->id);
                
                if (!$productToUpdate) {
                    // This shouldn't happen since we already checked
                    continue;
                }
                
                // Update product stock
                $productToUpdate->stock -= $deductAmount;
                $productToUpdate->save();
                
                // Create inventory transaction record
                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'order_id' => null,
                    'quantity' => -$deductAmount,
                    'type' => 'bundle_sale',
                    'notes' => "Stock reduced for bundle (Offer #{$offerId})",
                    'user_id' => auth()->id()
                ]);
                
                // Check if product is now low on stock and log it
                if ($productToUpdate->is_low_stock) {
                    Log::info("Product #{$productToUpdate->id} ({$productToUpdate->name}) is now low on stock: {$productToUpdate->stock} remaining");
                }
            }
            
            // Commit transaction
            DB::commit();
            return [
                'success' => true,
                'message' => 'Bundle stock reduced successfully'
            ];
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error("Error reducing bundle stock for Offer #{$offerId}: " . $e->getMessage(), [
                'exception' => $e,
                'offer_id' => $offerId,
                'quantity' => $quantity
            ]);
            return [
                'success' => false,
                'message' => 'An error occurred while reducing bundle stock: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Restore stock for all products in a bundle
     *
     * @param int $offerId
     * @param int $quantity
     * @param User|null $user
     * @param string $reason
     * @return bool
     */
    public function restoreBundleStock(int $offerId, int $quantity, ?User $user = null, string $reason = 'refund')
    {
        $offer = \App\Models\Offer::with('products')->find($offerId);
        
        if (!$offer) {
            Log::warning("Offer #{$offerId} not found when restoring bundle stock");
            return false;
        }
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            foreach ($offer->products as $product) {
                $productQuantity = $product->pivot->quantity * $quantity;
                
                // Lock the product row for update to prevent race conditions
                $productModel = Product::lockForUpdate()->find($product->id);
                if (!$productModel) {
                    continue;
                }
                
                // Update product stock
                $productModel->stock += $productQuantity;
                $productModel->save();
                
                // Create inventory transaction record
                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'order_id' => null,
                    'quantity' => $productQuantity, // Positive for additions
                    'type' => $reason,
                    'notes' => "Stock restored for Bundle #{$offerId} ({$reason})",
                    'user_id' => $user ? $user->id : null
                ]);
            }
            
            // Commit transaction
            DB::commit();
            return true;
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error("Error restoring stock for Bundle #{$offerId}: " . $e->getMessage());
            return false;
        }
    }
} 