<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FixProductImagePaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:fix-image-paths';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix product image paths to follow the correct format (products/featured/*)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $products = Product::all();
        $this->info("Found {$products->count()} products");
        
        $fixedCount = 0;
        $errorCount = 0;
        
        foreach ($products as $product) {
            $this->info("Processing product #{$product->id}: {$product->name}");
            
            // Skip products that already have correct path format
            if ($product->image && strpos($product->image, 'products/featured/') === 0) {
                $this->line("  → Already has correct path format");
                continue;
            }
            
            // Skip products with no image set
            if (empty($product->image)) {
                $this->warn("  → No image path set (null)");
                continue;
            }
            
            $this->line("  Current path: {$product->image}");
            
            try {
                // Check if the file exists in storage
                if (Storage::disk('public')->exists($product->image)) {
                    // Generate a new filename
                    $extension = pathinfo($product->image, PATHINFO_EXTENSION) ?: 'jpg';
                    $newFilename = 'products/featured/' . uniqid() . '.' . $extension;
                    
                    // Make sure the directory exists
                    Storage::disk('public')->makeDirectory('products/featured');
                    
                    // Copy the file to the new location
                    if (Storage::disk('public')->copy($product->image, $newFilename)) {
                        // Update the product's image path
                        $product->image = $newFilename;
                        $product->save();
                        
                        $this->info("  → Fixed: {$newFilename}");
                        $fixedCount++;
                    } else {
                        $this->error("  → Failed to copy file");
                        $errorCount++;
                    }
                } else {
                    // Try to find the file by checking common directories
                    $possiblePaths = [
                        $product->image,
                        'images/' . $product->image,
                        'products/' . $product->image,
                        'uploads/' . $product->image
                    ];
                    
                    $found = false;
                    foreach ($possiblePaths as $path) {
                        if (Storage::disk('public')->exists($path)) {
                            $extension = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg';
                            $newFilename = 'products/featured/' . uniqid() . '.' . $extension;
                            
                            // Make sure the directory exists
                            Storage::disk('public')->makeDirectory('products/featured');
                            
                            if (Storage::disk('public')->copy($path, $newFilename)) {
                                $product->image = $newFilename;
                                $product->save();
                                
                                $this->info("  → Fixed using alternative path: {$path} → {$newFilename}");
                                $fixedCount++;
                                $found = true;
                                break;
                            }
                        }
                    }
                    
                    if (!$found) {
                        $this->warn("  → Image file not found in any expected location");
                        $errorCount++;
                    }
                }
            } catch (\Exception $e) {
                $this->error("  → Error: {$e->getMessage()}");
                Log::error('Error fixing image path', [
                    'product_id' => $product->id,
                    'image_path' => $product->image,
                    'error' => $e->getMessage()
                ]);
                $errorCount++;
            }
        }
        
        $this->newLine();
        $this->info("Completed! Fixed {$fixedCount} product image paths with {$errorCount} errors.");
        
        return Command::SUCCESS;
    }
} 