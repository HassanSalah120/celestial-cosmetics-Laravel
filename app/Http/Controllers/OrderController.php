<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the user's orders.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $orders = Order::with(['items.product', 'items.offer'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $order = Order::with(['items.product', 'items.offer.products'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    /**
     * Display order confirmation page after checkout.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function confirmation($id)
    {
        $order = Order::with(['items.product', 'items.offer.products'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('orders.confirmation', compact('order'));
    }
    
    /**
     * Display bundle details for an order item.
     *
     * @param  int  $orderId
     * @param  int  $itemId
     * @return \Illuminate\View\View
     */
    public function bundleDetails($orderId, $itemId)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($orderId);
        $orderItem = OrderItem::with('offer.products')->findOrFail($itemId);
        
        // Verify that this item belongs to the order
        if ($orderItem->order_id != $orderId) {
            abort(404);
        }
        
        // Verify this is a bundle/offer item
        if (!$orderItem->offer_id) {
            abort(404, 'This order item is not a bundle');
        }
        
        return view('orders.bundle-details', compact('order', 'orderItem'));
    }
} 