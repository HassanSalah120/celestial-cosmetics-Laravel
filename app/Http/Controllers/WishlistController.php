<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use App\Helpers\TranslationHelper;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display a listing of the wishlist items.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $wishlistItems = [];
        
        // Only get wishlist items if the user is logged in
        if (Auth::check()) {
            $wishlistItems = auth()->user()->wishlists()
                ->with('product')
                ->latest()
                ->get();
        }
        
        return view('wishlist.index', compact('wishlistItems'));
    }
    
    /**
     * Add a product to the wishlist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function add(Request $request, Product $product)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => TranslationHelper::get('login_required', 'Please log in to add items to your wishlist.')
                ], 401);
            }
            
            return redirect()->route('login')
                ->with('error', TranslationHelper::get('login_required', 'Please log in to add items to your wishlist.'));
        }
        
        // Check if product already in wishlist
        $existingItem = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();
            
        if ($existingItem) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => TranslationHelper::get('already_in_wishlist', 'This product is already in your wishlist.'),
                    'in_wishlist' => true,
                    'wishlist_count' => auth()->user()->wishlists()->count()
                ]);
            }
            
            return redirect()->back()
                ->with('info', TranslationHelper::get('already_in_wishlist', 'This product is already in your wishlist.'));
        }
        
        // Add to wishlist
        Wishlist::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id
        ]);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => TranslationHelper::get('added_to_wishlist', 'Product added to your wishlist.'),
                'in_wishlist' => true,
                'wishlist_count' => auth()->user()->wishlists()->count()
            ]);
        }
        
        return redirect()->back()
            ->with('success', TranslationHelper::get('added_to_wishlist', 'Product added to your wishlist.'));
    }
    
    /**
     * Remove a product from the wishlist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function remove(Request $request, Product $product)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => TranslationHelper::get('login_required', 'Please log in to manage your wishlist.')
                ], 401);
            }
            
            return redirect()->route('login')
                ->with('error', TranslationHelper::get('login_required', 'Please log in to manage your wishlist.'));
        }
        
        // Remove from wishlist
        $deleted = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->delete();
            
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => TranslationHelper::get('removed_from_wishlist', 'Product removed from your wishlist.'),
                'in_wishlist' => false,
                'wishlist_count' => auth()->user()->wishlists()->count()
            ]);
        }
        
        return redirect()->back()
            ->with('success', TranslationHelper::get('removed_from_wishlist', 'Product removed from your wishlist.'));
    }
    
    /**
     * Check if a product is in the user's wishlist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(Request $request, Product $product)
    {
        if (!Auth::check()) {
            return response()->json([
                'in_wishlist' => false
            ]);
        }
        
        $exists = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->exists();
            
        return response()->json([
            'in_wishlist' => $exists
        ]);
    }
    
    /**
     * Clear all items from the wishlist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear(Request $request)
    {
        if (!Auth::check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => TranslationHelper::get('login_required', 'Please log in to manage your wishlist.')
                ], 401);
            }
            
            return redirect()->route('login')
                ->with('error', TranslationHelper::get('login_required', 'Please log in to manage your wishlist.'));
        }
        
        // Delete all wishlist items for this user
        Wishlist::where('user_id', auth()->id())->delete();
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => TranslationHelper::get('wishlist_cleared', 'Your wishlist has been cleared.'),
                'wishlist_count' => 0
            ]);
        }
        
        return redirect()->route('wishlist.index')
            ->with('success', TranslationHelper::get('wishlist_cleared', 'Your wishlist has been cleared.'));
    }
}
