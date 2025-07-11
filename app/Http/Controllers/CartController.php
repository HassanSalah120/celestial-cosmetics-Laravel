<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Offer;
use App\Services\CartService;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Helpers\TranslationHelper;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * The cart service instance.
     *
     * @var \App\Services\CartService
     */
    protected $cartService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\CartService $cartService
     * @return void
     */
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display the shopping cart page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $cartData = $this->cartService->getCartContents();
        return view('cart.index', $cartData);
    }

    /**
     * Add a product to the cart.
     *
     * @param \App\Http\Requests\AddToCartRequest $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function add(AddToCartRequest $request, Product $product)
    {
        $validated = $request->validated();
        $quantity = $validated['quantity'] ?? 1;
        $promoCode = $validated['promo_code'] ?? null;

        $result = $this->cartService->addProduct($product, $quantity, $promoCode);

        if ($request->ajax() || $request->wantsJson()) {
            $result['toast'] = true;
            return response()->json($result);
        }

        return redirect()->back()->with('toast', $result['message']);
    }

    /**
     * Update product quantity in the cart.
     *
     * @param \App\Http\Requests\UpdateCartRequest $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCartRequest $request, Product $product)
    {
        $result = $this->cartService->updateProduct($product, $request->validated()['quantity']);
        return response()->json($result);
    }

    /**
     * Remove a product from the cart.
     *
     * @param \App\Models\Product $product
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function remove(Product $product, Request $request)
    {
        $result = $this->cartService->removeProduct($product);
        if ($request->ajax() || $request->wantsJson()) {
            $result['toast'] = true;
            return response()->json($result);
        }
        return redirect()->back()->with('toast', $result['message']);
    }

    /**
     * Add an offer to the cart.
     *
     * @param \App\Http\Requests\AddToCartRequest $request
     * @param \App\Models\Offer $offer
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function addOffer(AddToCartRequest $request, Offer $offer)
    {
        $quantity = $request->validated()['quantity'] ?? 1;
        $result = $this->cartService->addOffer($offer, $quantity);

        if ($request->ajax() || $request->wantsJson()) {
            $result['toast'] = true;
            return response()->json($result, $result['status'] ?? 200);
        }
        
        $status = $result['success'] ? 'toast' : 'toast_error';
        return back()->with($status, $result['message']);
    }

    /**
     * Remove an offer from the cart.
     *
     * @param \App\Models\Offer $offer
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function removeOffer(Offer $offer, Request $request)
    {
        $result = $this->cartService->removeOffer($offer);
        if ($request->ajax() || $request->wantsJson()) {
            $result['toast'] = true;
            return response()->json($result);
        }
        return redirect()->back()->with('toast', $result['message']);
    }

    /**
     * Get mini-cart contents for AJAX requests.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function miniCart()
    {
        $miniCartData = $this->cartService->getMiniCartContents();
        return response()->json($miniCartData);
    }
} 