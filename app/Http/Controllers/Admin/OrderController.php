<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Activity;
use App\Models\User;
use App\Mail\OrderStatusUpdateMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use App\Services\InventoryService;
use Illuminate\Support\Facades\Log;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * The inventory service instance.
     *
     * @var InventoryService
     */
    protected $inventoryService;

    /**
     * Create a new controller instance.
     *
     * @param InventoryService $inventoryService
     * @return void
     */
    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Display a listing of the orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
        // Get possible order statuses for the filter
        $orderStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
            $statusFilter = $request->input('status');
            
            // If it's an ajax request, return data for Grid.js
            if ($request->ajax() || $request->wantsJson()) {
            $query = Order::with(['user', 'items.product']);
            
                // Apply filters if they exist
                if ($request->has('filters')) {
                    $filters = $request->filters;
                    
                    // Status filters
                    if (isset($filters['status']) && !empty($filters['status'])) {
                        $statusFilters = explode(',', $filters['status']);
                        
                        $query->where(function($q) use ($statusFilters) {
                            foreach ($statusFilters as $status) {
                                $q->orWhere('status', $status);
                            }
                        });
                    }
                    
                    // Period filters
                    if (isset($filters['period']) && !empty($filters['period'])) {
                        $periodFilters = explode(',', $filters['period']);
                        
                        $query->where(function($q) use ($periodFilters) {
                            foreach ($periodFilters as $period) {
                                switch ($period) {
                                    case 'today':
                                        $q->orWhereDate('created_at', now()->toDateString());
                                        break;
                                    case 'yesterday':
                                        $q->orWhereDate('created_at', now()->subDay()->toDateString());
                                        break;
                                    case 'last7days':
                                        $q->orWhere('created_at', '>=', now()->subDays(7));
                                        break;
                                    case 'last30days':
                                        $q->orWhere('created_at', '>=', now()->subDays(30));
                                        break;
                                }
                            }
                        });
                    }
                    
                    // Special filters
                    if (isset($filters['special']) && !empty($filters['special'])) {
                        $specialFilters = explode(',', $filters['special']);
                        
                        $query->where(function($q) use ($specialFilters) {
                            foreach ($specialFilters as $specialFilter) {
                                switch ($specialFilter) {
                                    case 'attention':
                                        // Orders that need attention (pending for more than 24 hours)
                                        $q->orWhere(function($subq) {
                                            $subq->where('status', 'pending')
                                                ->where('created_at', '<=', now()->subHours(24));
                                        });
                                        break;
                                    case 'high-value':
                                        // High value orders (over $200)
                                        $q->orWhere('total_amount', '>=', 500);
                                        break;
                                    case 'new-customers':
                                        // Orders from first-time customers
                                        $q->orWhereIn('id', function($subquery) {
                                            $subquery->select(DB::raw('MIN(id)'))
                                                ->from('orders')
                                                ->whereNotNull('user_id')
                                                ->groupBy('user_id');
                                        });
                                        break;
                                }
                            }
                        });
                    }
                }
                
                // Handle legacy filter for backward compatibility
                else if ($request->has('filter')) {
                    $filter = $request->input('filter');
                    
                    switch ($filter) {
                        case 'today':
                            $query->whereDate('created_at', now()->toDateString());
                            break;
                        case 'attention':
                            $query->where('status', 'pending')
                                ->where('created_at', '<=', now()->subHours(24));
                            break;
                        case 'pending':
                            $query->where('status', 'pending');
                            break;
                        case 'processing':
                            $query->where('status', 'processing');
                            break;
                        case 'shipped':
                            $query->where('status', 'shipped');
                            break;
                        case 'high-value':
                            $query->where('total_amount', '>=', 500);
                            break;
                        case 'new-customer':
                            $query->whereIn('id', function($subquery) {
                                $subquery->select(DB::raw('MIN(id)'))
                                    ->from('orders')
                                    ->whereNotNull('user_id')
                                    ->groupBy('user_id');
                            });
                            break;
                    }
                }
                
                // Handle legacy status filter for backward compatibility
                else if ($request->has('status')) {
                    $statusFilter = $request->input('status');
                    if ($statusFilter) {
                        $query->where('status', $statusFilter);
                    }
                }
                
                // Handle search
                if ($search = $request->input('search')) {
                    $query->where(function($q) use ($search) {
                        $q->where('id', 'like', "%{$search}%")
                        ->orWhereHas('user', function($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                        });
                    });
                }

                // Handle sorting
                $sortColumn = $request->input('sort', 'created_at');
                $sortDirection = $request->input('order', 'desc');
                
                // Validate sort column to prevent SQL injection
                $allowedColumns = ['id', 'created_at', 'total_amount', 'status', 'payment_status'];
                if (!in_array($sortColumn, $allowedColumns)) {
                    $sortColumn = 'created_at';
                }
                
                $query->orderBy($sortColumn, $sortDirection);

                // Handle pagination
                $page = max(1, intval($request->input('page', 1)));
                $limit = max(1, min(100, intval($request->input('per_page', 10))));
                $total = $query->count();
                
                $orders = $query->skip(($page - 1) * $limit)
                               ->take($limit)
                               ->get();
                
                $data = $orders->map(function($order) {
                    $customerName = 'Guest';
                    $customerEmail = '-';
                    $initials = 'G';
                    $bgColor = 'bg-gray-200';
                    $textColor = 'text-gray-600';
                    
                    if ($order->user) {
                        $customerName = $order->user->name;
                        $customerEmail = $order->user->email;
                        
                        // Get initials for avatar
                        $nameParts = explode(' ', $customerName);
                        $initials = strlen($nameParts[0]) > 0 ? substr($nameParts[0], 0, 1) : '';
                        if (count($nameParts) > 1 && strlen($nameParts[1]) > 0) {
                            $initials .= substr($nameParts[1], 0, 1);
                        }
                        $initials = strtoupper($initials);
                        
                        // Determine background color based on name
                        $colors = ['bg-blue-200', 'bg-green-200', 'bg-yellow-200', 'bg-red-200', 'bg-purple-200', 'bg-pink-200'];
                        $hash = crc32($customerName);
                        $index = abs($hash % count($colors));
                        $bgColor = $colors[$index];
                        $textColors = ['text-blue-800', 'text-green-800', 'text-yellow-800', 'text-red-800', 'text-purple-800', 'text-pink-800'];
                        $textColor = $textColors[$index];
                    }
                    
                    if ($order->user && $order->user->profile_image) {
                        $avatarHtml = '<img class="h-8 w-8 rounded-full" src="' . e(asset('storage/' . $order->user->profile_image)) . '" alt="' . e($customerName) . '">';
                    } else {
                        $avatarHtml = '<div class="h-8 w-8 rounded-full ' . $bgColor . ' ' . $textColor . ' flex items-center justify-center text-sm font-medium">' . e($initials) . '</div>';
                    }
                    
                    // Get shipping country (from JSON or direct field)
                    $country = '';
                    if (is_array($order->shipping_address) && isset($order->shipping_address['country'])) {
                        $country = $order->shipping_address['country'];
                    } elseif (!empty($order->shipping_country)) {
                        $country = $order->shipping_country;
                    }
                    
                    // Calculate items count
                    $itemsCount = $order->items()->count();
                    
                    // Determine order value category
                    $orderValue = $order->total_amount ?? 0;
                    $orderValueCategory = 'low';
                    if ($orderValue >= 200) {
                        $orderValueCategory = 'high';
                    } elseif ($orderValue >= 100) {
                        $orderValueCategory = 'medium';
                    }
                    
                    // Determine if this is a new customer (first order)
                    $isNewCustomer = false;
                    if ($order->user_id) {
                        $orderCount = Order::where('user_id', $order->user_id)
                            ->where('created_at', '<', $order->created_at)
                            ->count();
                        $isNewCustomer = ($orderCount === 0);
                    }
                    
                    // Determine if the order needs attention (recent, unprocessed)
                    $needsAttention = false;
                    if ($order->status === 'pending' && $order->created_at->diffInHours(now()) < 24) {
                        $needsAttention = true;
                    }
                    
                    // Get fulfillment status based on order status
                    $fulfillmentStatus = 'Not Started';
                    if (in_array($order->status, ['processing', 'shipped', 'delivered'])) {
                        if ($order->status === 'processing') {
                            $fulfillmentStatus = 'In Progress';
                        } elseif ($order->status === 'shipped') {
                            $fulfillmentStatus = 'Shipped';
                        } elseif ($order->status === 'delivered') {
                            $fulfillmentStatus = 'Delivered';
                        }
                    }

                    return [
                        'id' => $order->id,
                        'customer_name' => $customerName,
                        'email' => $customerEmail,
                        'total' => $order->total_amount,
                        'status' => ucfirst($order->status),
                        'payment_method' => ucfirst($order->payment_method ?? $order->payment_status ?? 'Unknown'),
                        'payment_status' => ucfirst($order->payment_status ?? 'Unknown'),
                        'created_at' => $order->created_at->format('M d, Y, h:i A'),
                        'updated_at' => $order->updated_at->format('M d, Y, h:i A'),
                        'items_count' => $itemsCount,
                        'fulfillment_status' => $fulfillmentStatus,
                        'shipping_method' => ucfirst($order->shipping_method ?? 'Standard'),
                        'country' => $country,
                        'order_value_category' => $orderValueCategory,
                        'is_new_customer' => $isNewCustomer,
                        'needs_attention' => $needsAttention,
                        'has_tracking' => !empty($order->tracking_number),
                        'customer' => [
                            '_html' => '<div class="flex items-center">
                                ' . $avatarHtml . '
                                <div class="ml-3">
                                    <div class="font-medium">' . e($customerName) . '</div>
                                    <div class="text-xs text-gray-500">' . e($customerEmail) . '</div>
                                </div>
                            </div>'
                        ]
                    ];
                });
                
                return response()->json($data);
            }
            
            return view('admin.orders.index', compact('orderStatuses', 'statusFilter'));
        } catch (\Exception $e) {
            Log::error('Error in orders index: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while loading orders'], 500);
        }
    }

    /**
     * Display the specified order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'items.offer']);
        
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        $order->load(['user', 'items.product', 'items.offer']);
        $orderStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];
        
        return view('admin.orders.edit', compact('order', 'orderStatuses', 'paymentStatuses'));
    }

    /**
     * Update the specified order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|string|in:pending,paid,failed,refunded',
            'tracking_number' => 'nullable|string|max:255',
            'generate_tracking' => 'nullable|boolean',
        ]);
        
        // Store old order values for activity logging and email notification
        $oldStatus = $order->status;
        $oldPaymentStatus = $order->payment_status;
        $oldTrackingNumber = $order->tracking_number;
        
        // Generate random tracking number if requested
        if (!empty($validated['generate_tracking']) && $validated['generate_tracking']) {
            $validated['tracking_number'] = $this->generateTrackingNumber();
        }
        
        // Auto-generate tracking number if status changed to shipped and no tracking number exists
        if ($validated['status'] === 'shipped' && $oldStatus !== 'shipped' && empty($validated['tracking_number']) && empty($order->tracking_number)) {
            $validated['tracking_number'] = $this->generateTrackingNumber();
            Log::info('Auto-generated tracking number for order #' . $order->id . ': ' . $validated['tracking_number']);
        }
        
        // Check if we need to restore inventory
        $needsInventoryRestore = false;
        $inventoryRestoreReason = '';
        
        // If status changed to cancelled
        if ($validated['status'] === 'cancelled' && $oldStatus !== 'cancelled') {
            $needsInventoryRestore = true;
            $inventoryRestoreReason = 'cancellation';
        }
        
        // If payment status changed to refunded
        if ($validated['payment_status'] === 'refunded' && $oldPaymentStatus !== 'refunded') {
            $needsInventoryRestore = true;
            $inventoryRestoreReason = 'refund';
        }
        
        // Update the order
        $order->status = $validated['status'];
        $order->payment_status = $validated['payment_status'];
        $order->tracking_number = $validated['tracking_number'];
        $order->save();
        
        // Restore inventory if needed
        if ($needsInventoryRestore) {
            $this->inventoryService->restoreStockFromOrder($order, Auth::user(), $inventoryRestoreReason);
            Log::info("Restored inventory for Order #{$order->id} due to {$inventoryRestoreReason}");
        }
        
        // Log the activity
        Activity::create([
            'description' => 'Updated order #' . $order->id,
            'causer_type' => User::class,
            'causer_id' => Auth::id(),
            'subject_type' => Order::class,
            'subject_id' => $order->id,
            'properties' => [
                'changes' => [
                    'status' => [
                        'old' => $oldStatus,
                        'new' => $order->status,
                    ],
                    'payment_status' => [
                        'old' => $oldPaymentStatus,
                        'new' => $order->payment_status,
                    ],
                    'tracking_number' => [
                        'old' => $oldTrackingNumber,
                        'new' => $order->tracking_number,
                    ],
                ],
            ],
        ]);

        // Check if any changes that need email notification
        $changedFields = [];
        $oldValues = [];
        
        if ($oldStatus !== $order->status) {
            $changedFields[] = 'status';
            $oldValues['status'] = $oldStatus;
        }
        
        if ($oldPaymentStatus !== $order->payment_status) {
            $changedFields[] = 'payment_status';
            $oldValues['payment_status'] = $oldPaymentStatus;
        }
        
        if (($oldTrackingNumber !== $order->tracking_number) && !empty($order->tracking_number)) {
            $changedFields[] = 'tracking_number';
            $oldValues['tracking_number'] = $oldTrackingNumber;
        }
        
        // Send email notification if there are changes
        if (!empty($changedFields)) {
            $this->sendOrderUpdateEmail($order, $changedFields, $oldValues);
        }
        
        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified order from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        // Store old order status for email notification
        $oldStatus = $order->status;
        
        // Soft delete the order or log cancellation instead of full deletion
        $order->status = 'cancelled';
        $order->save();
        
        // Log the activity
        Activity::create([
            'description' => 'Cancelled order #' . $order->id,
            'causer_type' => User::class,
            'causer_id' => Auth::id(),
            'subject_type' => Order::class,
            'subject_id' => $order->id,
        ]);
        
        // Send email notification if status changed to cancelled
        if ($oldStatus !== 'cancelled') {
            $this->sendOrderUpdateEmail(
                $order,
                ['status'],
                ['status' => $oldStatus]
            );
        }
        
        return redirect()->route('admin.orders.index')
            ->with('success', 'Order has been cancelled.');
    }

    /**
     * Test email functionality with a specific order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function testEmail(Order $order)
    {
        try {
            Log::info('Sending order status notification for order #' . $order->id);
            
            // Make sure the order has all necessary data
            $order->load(['items.product', 'user']);
            
            if (!isset($order->shipping_address['email'])) {
                Log::error('No email address found in shipping_address for order #' . $order->id);
                return back()->with('error', 'No email address found in order shipping details.');
            }
            
            $email = $order->shipping_address['email'];
            Log::info('Sending notification email to: ' . $email);
            
            // Create notification based on current status
            $changedFields = ['status', 'payment_status'];
            
            // Use a generic previous status based on current status for the notification
            $previousStatus = $order->status;
            if ($previousStatus == 'pending') $previousStatus = 'new';
            else if ($previousStatus == 'processing') $previousStatus = 'pending';
            else if ($previousStatus == 'shipped') $previousStatus = 'processing';
            else if ($previousStatus == 'delivered') $previousStatus = 'shipped';
            else if ($previousStatus == 'cancelled') $previousStatus = 'pending';
            
            $previousPaymentStatus = $order->payment_status;
            if ($previousPaymentStatus == 'paid') $previousPaymentStatus = 'pending';
            else if ($previousPaymentStatus == 'pending') $previousPaymentStatus = 'new';
            else if ($previousPaymentStatus == 'refunded') $previousPaymentStatus = 'paid';
            else if ($previousPaymentStatus == 'failed') $previousPaymentStatus = 'pending';
            
            $oldValues = [
                'status' => $previousStatus,
                'payment_status' => $previousPaymentStatus
            ];
            
            // Add tracking number if present
            if (!empty($order->tracking_number)) {
                $changedFields[] = 'tracking_number';
                $oldValues['tracking_number'] = '';
            }
            
            // Send the email
            Mail::to($email)
                ->send(new OrderStatusUpdateMail($order, $changedFields, $oldValues));
            
            Log::info('Status notification email sent successfully to ' . $email);
            
            return back()->with('success', 'Status notification email sent successfully to ' . $email);
        } catch (\Exception $e) {
            Log::error('Error sending notification email: ' . $e->getMessage());
            Log::error('Exception stack trace: ' . $e->getTraceAsString());
            
            return back()->with('error', 'Failed to send notification email: ' . $e->getMessage());
        }
    }

    /**
     * Generate a random tracking number for orders
     * 
     * @return string
     */
    private function generateTrackingNumber()
    {
        // Format: CC-XXXXX-XXXXXXX (CC = Celestial Cosmetics, followed by 5 digits, then 7 alphanumeric)
        $prefix = 'CC';
        $middlePart = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $suffix = strtoupper(Str::random(7));
        
        return $prefix . '-' . $middlePart . '-' . $suffix;
    }
    
    /**
     * Generate a tracking number for an order and return to order page
     * 
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function generateTracking(Order $order)
    {
        $oldTrackingNumber = $order->tracking_number;
        
        // Generate and save new tracking number
        $order->tracking_number = $this->generateTrackingNumber();
        $order->save();
        
        // Log the activity
        Activity::create([
            'description' => 'Generated tracking number for order #' . $order->id,
            'causer_type' => User::class,
            'causer_id' => Auth::id(),
            'subject_type' => Order::class,
            'subject_id' => $order->id,
            'properties' => [
                'changes' => [
                    'tracking_number' => [
                        'old' => $oldTrackingNumber,
                        'new' => $order->tracking_number,
                    ],
                ],
            ],
        ]);
        
        // If order status is pending or processing, update to shipped
        if ($order->status === 'pending' || $order->status === 'processing') {
            $oldStatus = $order->status;
            $order->status = 'shipped';
            $order->save();
            
            // Log the status change
            Activity::create([
                'description' => 'Updated order #' . $order->id . ' status to shipped',
                'causer_type' => User::class,
                'causer_id' => Auth::id(),
                'subject_type' => Order::class,
                'subject_id' => $order->id,
                'properties' => [
                    'changes' => [
                        'status' => [
                            'old' => $oldStatus,
                            'new' => 'shipped',
                        ],
                    ],
                ],
            ]);
            
            // Send email notification for status change
            if (isset($order->shipping_address['email'])) {
                try {
                    Log::info('Sending tracking number and status update email to: ' . $order->shipping_address['email']);
                    
                    Mail::to($order->shipping_address['email'])
                        ->send(new OrderStatusUpdateMail(
                            $order, 
                            ['status', 'tracking_number'], 
                            ['status' => $oldStatus, 'tracking_number' => $oldTrackingNumber]
                        ));
                    
                    Log::info('Tracking and status update email sent successfully');
                } catch (\Exception $e) {
                    Log::error('Failed to send tracking update email: ' . $e->getMessage());
                }
            }
        }
        
        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Tracking number generated successfully: ' . $order->tracking_number);
    }

    /**
     * Generate a printable shipping label for an order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function shippingLabel(Order $order)
    {
        // Make sure the order has all necessary data
        $order->load(['items.product']);
        
        // Log the shipping label generation
        Log::info('Generating shipping label for order #' . $order->id);
        
        // Record activity
        Activity::create([
            'description' => 'Generated shipping label for order #' . $order->id,
            'causer_type' => User::class,
            'causer_id' => Auth::id(),
            'subject_type' => Order::class,
            'subject_id' => $order->id,
        ]);
        
        return view('admin.orders.shipping-label', compact('order'));
    }

    /**
     * Send email notification if there are changes
     *
     * @param  \App\Models\Order  $order
     * @param  array  $changedFields
     * @param  array  $oldValues
     * @return void
     */
    private function sendOrderUpdateEmail(Order $order, array $changedFields, array $oldValues)
    {
        // Check if we have an email to send to - try both new JSON and old format
        $email = null;
        
        if (is_array($order->shipping_address) && isset($order->shipping_address['email'])) {
            $email = $order->shipping_address['email'];
        } elseif (!empty($order->shipping_email)) {
            // Fallback to old column if available
            $email = $order->shipping_email;
        }
        
        if (!$email) {
            Log::warning('Cannot send order update email - no email found for order #' . $order->id);
            return;
        }
        
        try {
            Log::info('Sending order update email to: ' . $email);
            Log::info('Changed fields: ' . json_encode($changedFields));
            
            Mail::to($email)
                ->send(new OrderStatusUpdateMail($order, $changedFields, $oldValues));
            
            Log::info('Email sent successfully');
        } catch (\Exception $e) {
            // Log the error but don't prevent the order update
            Log::error('Failed to send order update email: ' . $e->getMessage());
            Log::error('Exception stack trace: ' . $e->getTraceAsString());
        }
    }

    /**
     * Get the order's correct total value
     * Handles both total and total_amount fields
     * 
     * @param \App\Models\Order $order
     * @return float
     */
    public static function getOrderTotal($order)
    {
        if (!empty($order->total) && $order->total > 0) {
            return $order->total;
        }
        
        return $order->total_amount ?? 0;
    }
    
    /**
     * Get the order's correct subtotal value
     * 
     * @param \App\Models\Order $order
     * @return float
     */
    public static function getOrderSubtotal($order)
    {
        if (!empty($order->subtotal) && $order->subtotal > 0) {
            return $order->subtotal;
        }
        
        // Calculate subtotal from total and fees if not available
        $total = self::getOrderTotal($order);
        $shipping = self::getShippingCost($order);
        $discount = self::getDiscountAmount($order);
        $paymentFee = !empty($order->payment_fee) ? $order->payment_fee : 0;
        $codFee = !empty($order->cod_fee) ? $order->cod_fee : 0;
        
        return $total - $shipping - $paymentFee - $codFee + $discount;
    }
    
    /**
     * Get the order's correct shipping cost value
     * Handles both shipping_cost and shipping_fee fields
     * 
     * @param \App\Models\Order $order
     * @return float
     */
    public static function getShippingCost($order)
    {
        if (!empty($order->shipping_cost) && $order->shipping_cost > 0) {
            return $order->shipping_cost;
        }
        
        return $order->shipping_fee ?? 0;
    }
    
    /**
     * Get the order's correct discount amount value
     * Handles both discount and discount_amount fields
     * 
     * @param \App\Models\Order $order
     * @return float
     */
    public static function getDiscountAmount($order)
    {
        if (!empty($order->discount) && $order->discount > 0) {
            return $order->discount;
        }
        
        return $order->discount_amount ?? 0;
    }
    
    /**
     * Get the order's identifier for display and URLs
     * 
     * @param \App\Models\Order $order
     * @return string
     */
    public static function getOrderIdentifier($order)
    {
        if (!empty($order->order_number)) {
            return $order->order_number;
        }
        
        return $order->id;
    }
} 