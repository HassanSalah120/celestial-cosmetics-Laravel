<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    protected $stripeService;
    
    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
        // No middleware needed as the webhook is excluded in VerifyCsrfToken middleware
    }
    
    /**
     * Handle Stripe webhook
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handleWebhook(Request $request)
    {
        try {
            $payload = $request->getContent();
            $sigHeader = $request->header('Stripe-Signature');
            
            Log::info('Stripe webhook received', [
                'has_signature' => !empty($sigHeader),
                'content_length' => strlen($payload),
                'request_path' => $request->path(),
                'signature_length' => strlen($sigHeader ?? ''),
                'request_method' => $request->method(),
                'stripe_signature_header' => $sigHeader ? substr($sigHeader, 0, 50) . '...' : null,
                'request_ip' => $request->ip(),
                'webhook_endpoint' => url('/stripe/webhook'),
                'all_headers' => collect($request->headers->all())
                    ->map(function($item) { 
                        return is_array($item) && isset($item[0]) ? $item[0] : $item; 
                    })
                    ->toArray(),
            ]);
            
            if (!$sigHeader) {
                Log::error('Stripe webhook missing signature header');
                return response()->json(['error' => 'Stripe signature missing'], 400);
            }
            
            // Create a more detailed log of the payload (only in debug mode)
            if (config('app.debug')) {
                $jsonData = json_decode($payload, true);
                if ($jsonData) {
                    $eventType = $jsonData['type'] ?? 'unknown';
                    $eventId = $jsonData['id'] ?? 'unknown';
                    Log::debug("Stripe webhook payload", [
                        'event_type' => $eventType,
                        'event_id' => $eventId,
                        'payload_summary' => json_encode(array_intersect_key($jsonData, array_flip(['id', 'type', 'created'])))
                    ]);
                }
            }
            
            $result = $this->stripeService->handleWebhook($payload, $sigHeader);
            
            if (!$result['success']) {
                Log::error('Stripe webhook error: ' . ($result['message'] ?? 'Unknown error'), [
                    'result' => $result
                ]);
                return response()->json(['error' => $result['message'] ?? 'Webhook error'], 400);
            }
            
            // If this is a payment_intent.succeeded event, update the order
            if (isset($result['event']) && $result['event'] === 'payment_intent.succeeded' && isset($result['orderId'])) {
                Log::info('Updating order status for successful payment', [
                    'order_id' => $result['orderId'],
                    'payment_intent_id' => $result['paymentIntentId']
                ]);
                $this->updateOrderStatus($result['orderId'], $result['paymentIntentId']);
            }
            
            // If this is a payment_intent.payment_failed event, mark the order as failed
            if (isset($result['event']) && $result['event'] === 'payment_intent.payment_failed' && isset($result['orderId'])) {
                Log::info('Marking order as failed for failed payment', [
                    'order_id' => $result['orderId'],
                    'payment_intent_id' => $result['paymentIntentId']
                ]);
                $this->markOrderAsFailed($result['orderId'], $result['paymentIntentId']);
            }
            
            Log::info('Stripe webhook processed successfully', [
                'event' => $result['event'] ?? 'unknown',
                'order_id' => $result['orderId'] ?? null,
            ]);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Exception in Stripe webhook handler: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Internal server error processing webhook',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update order status after successful payment
     * 
     * @param int $orderId
     * @param string $paymentIntentId
     * @return void
     */
    private function updateOrderStatus($orderId, $paymentIntentId)
    {
        try {
            $order = Order::find($orderId);
            
            if (!$order) {
                Log::error("Order not found for ID: {$orderId}");
                return;
            }
            
            // Update order with payment information
            $order->payment_status = 'paid';
            $order->payment_method = 'stripe';
            $order->payment_id = $paymentIntentId;
            $order->status = 'completed'; // Change from 'processing' to 'completed'
            $order->save();
            
            // Additional actions like sending email, etc.
            // ...
            
            Log::info("Order {$order->order_number} marked as paid with Stripe payment {$paymentIntentId}");
        } catch (\Exception $e) {
            Log::error("Error updating order status: " . $e->getMessage());
        }
    }
    
    /**
     * Mark order as failed after payment failure
     * 
     * @param int $orderId
     * @param string $paymentIntentId
     * @return void
     */
    private function markOrderAsFailed($orderId, $paymentIntentId)
    {
        try {
            $order = Order::find($orderId);
            
            if (!$order) {
                Log::error("Order not found for ID: {$orderId}");
                return;
            }
            
            // Update order with payment failure information
            $order->payment_status = 'failed';
            $order->payment_method = 'stripe';
            $order->payment_id = $paymentIntentId;
            $order->status = 'payment_failed'; // Update to your desired status
            $order->save();
            
            Log::info("Order {$order->order_number} marked as payment failed with Stripe payment {$paymentIntentId}");
        } catch (\Exception $e) {
            Log::error("Error updating order status: " . $e->getMessage());
        }
    }
} 