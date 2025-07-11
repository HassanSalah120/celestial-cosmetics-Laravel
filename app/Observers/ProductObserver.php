<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function created(Product $product)
    {
        // Clear product cache
        $this->clearProductCache();
        
        // Log product creation
        Log::info('Product created', [
            'product_id' => $product->id,
            'product_name' => $product->name, 
            'stock' => $product->stock
        ]);
    }

    /**
     * Handle the Product "updated" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function updated(Product $product)
    {
        // Clear product cache
        $this->clearProductCache();
        
        // If the stock has changed, log it
        if ($product->isDirty('stock')) {
            $oldStock = $product->getOriginal('stock');
            $newStock = $product->stock;
            $change = $newStock - $oldStock;
            
            Log::info('Product stock updated', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'change' => $change
            ]);
            
            // If stock went from > 0 to 0 (out of stock), or from 0 to > 0 (back in stock)
            if (($oldStock > 0 && $newStock == 0) || ($oldStock == 0 && $newStock > 0)) {
                $status = $newStock > 0 ? 'back in stock' : 'out of stock';
                Log::info("Product {$product->name} is now {$status}");
                
                // Here you could trigger notifications or other actions
            }
            
            // Check if product is now low on stock
            if ($product->is_low_stock && $oldStock > $product->low_stock_threshold && $product->is_visible) {
                Log::info("Product {$product->name} is now low on stock", [
                    'product_id' => $product->id,
                    'current_stock' => $newStock,
                    'threshold' => $product->low_stock_threshold
                ]);
                
                // Check if we've recently notified about this product
                $notifiedProductIds = Cache::get('notified_low_stock_products', []);
                
                if (!in_array($product->id, $notifiedProductIds)) {
                    // Get admin users who should receive notifications
                    $adminUsers = \App\Models\User::whereHas('permissions', function ($query) {
                        $query->where('name', 'manage_products')
                              ->orWhere('name', 'manage_inventory');
                    })->get();
                    
                    if ($adminUsers->isNotEmpty()) {
                        foreach ($adminUsers as $admin) {
                            try {
                                $admin->notify(new \App\Notifications\LowStockNotification($product));
                            } catch (\Exception $e) {
                                Log::error("Failed to send low stock notification", [
                                    'product_id' => $product->id,
                                    'admin_id' => $admin->id,
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }
                        
                        // Add to notified products cache
                        $notifiedProductIds[] = $product->id;
                        Cache::put('notified_low_stock_products', $notifiedProductIds, now()->addDays(7));
                    }
                }
            }
        }
    }

    /**
     * Handle the Product "deleted" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function deleted(Product $product)
    {
        // Clear product cache
        $this->clearProductCache();
        
        // Log product deletion
        Log::info('Product deleted', [
            'product_id' => $product->id,
            'product_name' => $product->name
        ]);
    }

    /**
     * Clear product-related cache
     * 
     * @return void
     */
    protected function clearProductCache()
    {
        Cache::forget('featured_products');
        Cache::forget('new_products');
        Cache::forget('product_categories');
        // Remove cache tags that aren't supported by the default file driver
        // Cache::tags(['products'])->flush();
    }
} 