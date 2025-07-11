<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CheckProductCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:product-category {id=2}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if product has a valid category relationship';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $productId = $this->argument('id');
        
        // Check direct database record
        $this->info('Checking direct database record:');
        $dbProduct = DB::table('products')->where('id', $productId)->first();
        
        if (!$dbProduct) {
            $this->error("Product ID $productId not found in database.");
            return 1;
        }
        
        $this->info("Product ID: {$dbProduct->id}");
        $this->info("Product Name: {$dbProduct->name}");
        $this->info("Category ID: " . ($dbProduct->category_id ?? 'null'));
        
        // Check if category exists
        if ($dbProduct->category_id) {
            $category = DB::table('categories')->where('id', $dbProduct->category_id)->first();
            if ($category) {
                $this->info("Category exists in DB: {$category->name} (ID: {$category->id})");
            } else {
                $this->error("Category ID {$dbProduct->category_id} does not exist in database!");
            }
        }
        
        // Check using Eloquent model
        $this->newLine();
        $this->info('Checking using Eloquent relationship:');
        $product = Product::find($productId);
        
        if (!$product) {
            $this->error("Product model with ID $productId not found.");
            return 1;
        }
        
        $this->info("Product ID: {$product->id}");
        $this->info("Product Name: {$product->name}");
        $this->info("Category ID stored on model: " . ($product->category_id ?? 'null'));
        
        if ($product->category) {
            $this->info("Category loaded via relationship: {$product->category->name} (ID: {$product->category->id})");
        } else {
            $this->error("Category relationship returns null!");
            
            // Force eager loading to see if it works
            $this->newLine();
            $this->info('Trying with eager loading:');
            $productWithCategory = Product::with('category')->find($productId);
            
            if ($productWithCategory->category) {
                $this->info("Category loaded via eager loading: {$productWithCategory->category->name} (ID: {$productWithCategory->category->id})");
            } else {
                $this->error("Category still returns null with eager loading!");
            }
        }
        
        return 0;
    }
}
