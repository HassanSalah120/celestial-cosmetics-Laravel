<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Product;
use App\Facades\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class OfferController extends Controller
{
    /**
     * Display a listing of the offers.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // Get query parameters
            $status = request('status');
            $product = request('product');
            $search = request('search');
            $format = request('format');
            
            // Build the query
            $query = Offer::with('products');
            
            // Apply filters
            if ($status) {
                $now = now();
                switch ($status) {
                    case 'active':
                        $query->where('is_active', true)
                            ->where(function($q) use ($now) {
                                $q->whereNull('expires_at')
                                    ->orWhere('expires_at', '>', $now);
                            })
                            ->where(function($q) use ($now) {
                                $q->whereNull('starts_at')
                                    ->orWhere('starts_at', '<=', $now);
                            });
                        break;
                    case 'inactive':
                        $query->where('is_active', false);
                        break;
                    case 'expired':
                        $query->where('expires_at', '<', $now);
                        break;
                    case 'scheduled':
                        $query->where('starts_at', '>', $now);
                        break;
                }
            }
            
            if ($product) {
                if ($product === 'with-product') {
                    $query->has('products');
                } elseif ($product === 'without-product') {
                    $query->doesntHave('products');
                }
            }
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('subtitle', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('products', function($sq) use ($search) {
                          $sq->where('name', 'like', "%{$search}%");
                      });
                });
            }
            
            // Sort by order if not searching
            if (!$search) {
                $query->orderBy('sort_order', 'asc');
            }
            
            $offers = $query->get();
            
            // Add calculated status field to each offer
            $offers->transform(function($offer) {
                $now = now();
                $status = 'inactive';
                
                if ($offer->is_active) {
                    if ($offer->starts_at && $offer->starts_at > $now) {
                        $status = 'scheduled';
                    } else if ($offer->expires_at && $offer->expires_at < $now) {
                        $status = 'expired';
                    } else {
                        $status = 'active';
                    }
                }
                
                $offer->status = $status;
                return $offer;
            });
            
            $currencySymbol = Settings::get('currency_symbol', '$');
            
            // Check if JSON format is requested
            if ($format === 'json' || request()->ajax()) {
                return response()->json($offers);
            }
            
        return view('admin.offers.index', compact('offers', 'currencySymbol'));
        } catch (\Exception $e) {
            Log::error('Error in offers index: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while loading offers data'], 500);
        }
    }

    /**
     * Show the form for creating a new offer.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $products = Product::where('is_visible', true)->orderBy('name')->get();
        $currencySymbol = Settings::get('currency_symbol', '$');
        return view('admin.offers.create', compact('products', 'currencySymbol'));
    }

    /**
     * Store a newly created offer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'subtitle_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'tag' => 'nullable|string|max:100',
            'tag_ar' => 'nullable|string|max:100',
            'original_price' => 'nullable|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0',
            'discount_text' => 'nullable|string|max:100',
            'discount_text_ar' => 'nullable|string|max:100',
            'stock' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'button_text' => 'nullable|string|max:100',
            'button_text_ar' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:255',
            'product_id' => 'nullable|exists:products,id',
            'is_active' => 'sometimes|boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'sort_order' => 'nullable|integer|min:0',
            'bundle_products' => 'nullable|array',
            'bundle_products.*' => 'nullable|exists:products,id',
            'bundle_quantities' => 'nullable|array',
            'bundle_quantities.*' => 'nullable|integer|min:1',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images/offers', 'public');
            $validated['image'] = 'storage/' . $path;
        }

        // Set default values
        $validated['is_active'] = $request->has('is_active');

        // Create the offer
        $offer = Offer::create($validated);

        // Sync bundle products if any
        $this->syncBundleProducts($offer, $request);

        return redirect()->route('admin.offers.index')
            ->with('success', 'Offer created successfully!');
    }

    /**
     * Show the form for editing the specified offer.
     *
     * @param  \App\Models\Offer  $offer
     * @return \Illuminate\View\View
     */
    public function edit(Offer $offer)
    {
        // Load the offer with its products
        $offer->load('products');
        
        $products = Product::where('is_visible', true)->orderBy('name')->get();
        $currencySymbol = Settings::get('currency_symbol', '$');
        return view('admin.offers.edit', compact('offer', 'products', 'currencySymbol'));
    }

    /**
     * Update the specified offer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Offer  $offer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Offer $offer)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'subtitle_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'tag' => 'nullable|string|max:100',
            'tag_ar' => 'nullable|string|max:100',
            'original_price' => 'nullable|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0',
            'discount_text' => 'nullable|string|max:100',
            'discount_text_ar' => 'nullable|string|max:100',
            'stock' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'button_text' => 'nullable|string|max:100',
            'button_text_ar' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:255',
            'product_id' => 'nullable|exists:products,id',
            'is_active' => 'sometimes|boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'sort_order' => 'nullable|integer|min:0',
            'bundle_products' => 'nullable|array',
            'bundle_products.*' => 'nullable|exists:products,id',
            'bundle_quantities' => 'nullable|array',
            'bundle_quantities.*' => 'nullable|integer|min:1',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($offer->image) {
                // Remove the storage prefix if it exists to get the actual path
                $imagePath = str_replace('storage/', '', $offer->image);
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
            
            // Store new image
            $path = $request->file('image')->store('images/offers', 'public');
            $validated['image'] = 'storage/' . $path;
        }

        // Update the offer
        $offer->update($validated);

        // Handle bundle products if provided
        if ($request->has('bundle_products')) {
        $this->syncBundleProducts($offer, $request);
        }

        return redirect()->route('admin.offers.index')
            ->with('success', 'Offer updated successfully!');
    }

    /**
     * Remove the specified offer from storage.
     *
     * @param  \App\Models\Offer  $offer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Offer $offer)
    {
        try {
        // Delete the image if it exists
        if ($offer->image) {
            // Remove the storage prefix if it exists to get the actual path
            $imagePath = str_replace('storage/', '', $offer->image);
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        // Delete the offer
        $offer->delete();

            // Return JSON response for AJAX requests
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Offer deleted successfully'
                ]);
            }

        return redirect()->route('admin.offers.index')
            ->with('success', 'Offer deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting offer: ' . $e->getMessage());
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the offer'
                ], 500);
            }
            
            return redirect()->route('admin.offers.index')
                ->with('error', 'An error occurred while deleting the offer.');
        }
    }

    /**
     * Toggle the status of an offer.
     *
     * @param  \App\Models\Offer  $offer
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(Offer $offer)
    {
        try {
            $offer->is_active = !$offer->is_active;
            $offer->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Offer status updated successfully',
                'is_active' => $offer->is_active
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling offer status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the offer status'
            ], 500);
        }
    }

    /**
     * Duplicate an offer.
     *
     * @param  \App\Models\Offer  $offer
     * @return \Illuminate\Http\JsonResponse
     */
    public function duplicate(Offer $offer)
    {
        try {
            // Create a new offer with the same attributes
            $newOffer = $offer->replicate();
            $newOffer->title = $newOffer->title . ' (Copy)';
            
            // Set as inactive by default
            $newOffer->is_active = false;
            
            // If there's a sort order, place it after the original
            if ($offer->sort_order !== null) {
                $newOffer->sort_order = $offer->sort_order + 1;
                
                // Shift other offers down
                Offer::where('sort_order', '>', $offer->sort_order)
                    ->increment('sort_order');
            }
            
            // If there's an image, duplicate it
            if ($offer->image && Storage::disk('public')->exists($offer->image)) {
                $extension = pathinfo($offer->image, PATHINFO_EXTENSION);
                $newPath = 'images/offers/' . uniqid() . '.' . $extension;
                
                if (Storage::disk('public')->copy($offer->image, $newPath)) {
                    $newOffer->image = $newPath;
                }
            }
            
            $newOffer->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Offer duplicated successfully',
                'offer' => $newOffer
            ]);
        } catch (\Exception $e) {
            Log::error('Error duplicating offer: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while duplicating the offer'
            ], 500);
        }
    }

    /**
     * Update the order of offers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrder(Request $request)
    {
        $ids = $request->input('ids', []);
        
        foreach ($ids as $index => $id) {
            Offer::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Toggle the active status of an offer.
     *
     * @param  \App\Models\Offer  $offer
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleActive(Offer $offer)
    {
        $offer->is_active = !$offer->is_active;
        $offer->save();

        return response()->json([
            'success' => true,
            'is_active' => $offer->is_active
        ]);
    }

    /**
     * Sync bundle products with the offer
     * 
     * @param \App\Models\Offer $offer
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    private function syncBundleProducts(Offer $offer, Request $request)
    {
        // Clear existing bundle products
        $bundleProducts = $request->input('bundle_products', []);
        $bundleQuantities = $request->input('bundle_quantities', []);
        
        // Prepare data for sync
        $syncData = [];
        
        foreach ($bundleProducts as $index => $productId) {
            if (!$productId) continue; // Skip empty selections
            
            $quantity = isset($bundleQuantities[$index]) ? $bundleQuantities[$index] : 1;
            
            $syncData[$productId] = [
                'quantity' => $quantity,
                // Default values for other pivot fields
                'discount_percentage' => null,
                'fixed_price' => null
            ];
        }
        
        // Sync the products with the offer
        $offer->products()->sync($syncData);
    }
}
