<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CheckLowStockProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:check-low-stock {--force : Force check without respecting cooldown}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for products with low stock and send notifications to admins';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for products with low stock...');
        
        // Check if we should run this check (avoid spamming notifications)
        $lastRun = Cache::get('last_low_stock_check');
        $cooldownHours = 24; // Only check once per day
        
        if ($lastRun && !$this->option('force')) {
            $hoursSinceLastRun = now()->diffInHours($lastRun);
            
            if ($hoursSinceLastRun < $cooldownHours) {
                $this->info("Last check was {$hoursSinceLastRun} hours ago. Skipping check (cooldown: {$cooldownHours} hours).");
                $this->info("Use --force to override cooldown.");
                return;
            }
        }
        
        // Get all products that are low on stock
        $lowStockProducts = Product::where('is_visible', true)
            ->whereRaw('stock > 0') // Only in-stock products
            ->whereRaw('stock <= low_stock_threshold') // Below threshold
            ->get();
        
        if ($lowStockProducts->isEmpty()) {
            $this->info('No products with low stock found.');
            Cache::put('last_low_stock_check', now(), now()->addHours($cooldownHours));
            return;
        }
        
        $this->info('Found ' . $lowStockProducts->count() . ' products with low stock.');
        
        // Get admin users who should receive notifications
        $adminUsers = User::whereHas('permissions', function ($query) {
            $query->where('name', 'manage_products')
                  ->orWhere('name', 'manage_inventory');
        })->get();
        
        if ($adminUsers->isEmpty()) {
            $this->warn('No admin users found to notify about low stock products.');
            return;
        }
        
        $this->info('Sending notifications to ' . $adminUsers->count() . ' admin users.');
        
        // Track which products we've already notified about to avoid duplicate notifications
        $notifiedProductIds = Cache::get('notified_low_stock_products', []);
        $newNotifiedProductIds = [];
        
        // Send notifications for each low stock product
        foreach ($lowStockProducts as $product) {
            // Skip if we've already notified about this product recently
            if (in_array($product->id, $notifiedProductIds) && !$this->option('force')) {
                $this->line("Skipping notification for product #{$product->id} ({$product->name}) - already notified recently.");
                $newNotifiedProductIds[] = $product->id;
                continue;
            }
            
            $this->line("Sending notification for product #{$product->id} ({$product->name}) - Stock: {$product->stock}");
            
            // Send notification to each admin
            foreach ($adminUsers as $admin) {
                try {
                    $admin->notify(new LowStockNotification($product));
                } catch (\Exception $e) {
                    $this->error("Failed to send notification to {$admin->name}: {$e->getMessage()}");
                    Log::error("Failed to send low stock notification", [
                        'product_id' => $product->id,
                        'admin_id' => $admin->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Mark this product as notified
            $newNotifiedProductIds[] = $product->id;
            
            // Add a small delay to avoid overwhelming the mail server
            usleep(250000); // 0.25 seconds
        }
        
        // Update the cache with the products we've notified about
        Cache::put('notified_low_stock_products', $newNotifiedProductIds, now()->addDays(7));
        
        // Update the last run timestamp
        Cache::put('last_low_stock_check', now(), now()->addHours($cooldownHours));
        
        $this->info('Low stock check completed successfully.');
    }
}
