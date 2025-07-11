<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\InventoryTransaction;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductBatchController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Batch update product prices
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePrices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.discount_percent' => 'nullable|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $updatedCount = 0;
            $errors = [];

            foreach ($request->products as $productData) {
                try {
                    $product = Product::findOrFail($productData['id']);
                    
                    // Update price
                    $product->price = $productData['price'];
                    
                    // Update discount if provided
                    if (isset($productData['discount_percent'])) {
                        $product->discount_percent = $productData['discount_percent'];
                    }
                    
                    $product->save();
                    $updatedCount++;
                    
                    // Log activity
                    activity()
                        ->performedOn($product)
                        ->withProperties([
                            'old_price' => $product->getOriginal('price'),
                            'new_price' => $product->price,
                            'old_discount' => $product->getOriginal('discount_percent'),
                            'new_discount' => $product->discount_percent,
                        ])
                        ->log('Updated product price via batch operation');
                } catch (\Exception $e) {
                    $errors[] = [
                        'product_id' => $productData['id'],
                        'message' => $e->getMessage()
                    ];
                    Log::error('Error updating product price: ' . $e->getMessage(), [
                        'product_id' => $productData['id'],
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} products updated successfully",
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Batch price update failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during batch update',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batch update product stock
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.stock' => 'required|integer|min:0',
            'products.*.low_stock_threshold' => 'nullable|integer|min:0',
            'update_type' => 'required|in:set,adjust',
            'notes' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $updatedCount = 0;
            $errors = [];
            $updateType = $request->update_type;
            $notes = $request->notes ?? 'Batch stock update';

            foreach ($request->products as $productData) {
                try {
                    $product = Product::findOrFail($productData['id']);
                    $oldStock = $product->stock;
                    
                    if ($updateType === 'set') {
                        // Set to specific value
                        $newStock = $productData['stock'];
                        $adjustmentAmount = $newStock - $oldStock;
                        
                        // Use inventory service to properly log the change
                        $this->inventoryService->adjustStock($product, $adjustmentAmount, auth()->user(), $notes);
                    } else {
                        // Adjust by the given amount (can be positive or negative)
                        $adjustmentAmount = $productData['stock'];
                        $this->inventoryService->adjustStock($product, $adjustmentAmount, auth()->user(), $notes);
                    }
                    
                    // Update low stock threshold if provided
                    if (isset($productData['low_stock_threshold'])) {
                        $product->low_stock_threshold = $productData['low_stock_threshold'];
                        $product->save();
                    }
                    
                    $updatedCount++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'product_id' => $productData['id'],
                        'message' => $e->getMessage()
                    ];
                    Log::error('Error updating product stock: ' . $e->getMessage(), [
                        'product_id' => $productData['id'],
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} products updated successfully",
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Batch stock update failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during batch update',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batch update product visibility
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateVisibility(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_ids' => 'required|array',
            'product_ids.*' => 'required|exists:products,id',
            'is_visible' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $updatedCount = Product::whereIn('id', $request->product_ids)
                ->update(['is_visible' => $request->is_visible]);

            // Log activity for each product
            foreach ($request->product_ids as $productId) {
                activity()
                    ->performedOn(Product::find($productId))
                    ->withProperties([
                        'is_visible' => $request->is_visible,
                    ])
                    ->log('Updated product visibility via batch operation');
            }

            DB::commit();

            $status = $request->is_visible ? 'visible' : 'hidden';
            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} products marked as {$status} successfully"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Batch visibility update failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during batch update',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batch update featured status
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateFeatured(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_ids' => 'required|array',
            'product_ids.*' => 'required|exists:products,id',
            'is_featured' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $updatedCount = Product::whereIn('id', $request->product_ids)
                ->update(['is_featured' => $request->is_featured]);

            // Log activity for each product
            foreach ($request->product_ids as $productId) {
                activity()
                    ->performedOn(Product::find($productId))
                    ->withProperties([
                        'is_featured' => $request->is_featured,
                    ])
                    ->log('Updated product featured status via batch operation');
            }

            DB::commit();

            $status = $request->is_featured ? 'featured' : 'unfeatured';
            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} products marked as {$status} successfully"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Batch featured status update failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during batch update',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
