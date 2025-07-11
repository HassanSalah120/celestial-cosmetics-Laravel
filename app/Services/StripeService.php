<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentConfig;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;

class StripeService
{
    protected $stripe;
    protected $config;

    public function __construct()
    {
        $this->config = PaymentConfig::first();
        
        Log::info('StripeService constructor', [
            'has_config' => (bool)$this->config,
            'enable_stripe' => $this->config ? $this->config->enable_stripe : false,
            'has_key' => $this->config && !empty($this->config->stripe_secret_key)
        ]);
        
        if ($this->config && $this->config->enable_stripe && $this->config->stripe_secret_key) {
            $this->stripe = new StripeClient($this->config->stripe_secret_key);
        }
    }

    /**
     * Create a payment intent for an order
     *
     * @param Order $order
     * @return array|null
     */
    public function createPaymentIntent(Order $order)
    {
        if (!$this->stripe) {
            Log::error('Stripe is not properly configured');
            return null;
        }

        try {
            $amountInCents = (int)($order->total_amount * 100); // Convert to cents
            
            Log::info('Creating Stripe payment intent', [
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'amount_cents' => $amountInCents,
                'currency' => $order->currency ?? $this->config->currency
            ]);
            
            $paymentIntentData = [
                'amount' => $amountInCents,
                'currency' => strtolower($order->currency ?? $this->config->currency),
                'description' => "Order #{$order->order_number}",
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_email' => $order->shipping_address['email'] ?? '',
                ],
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'receipt_email' => $order->shipping_address['email'] ?? '',
            ];
            
            // Set capture method
            if (!$this->config->stripe_capture_method) {
                $paymentIntentData['capture_method'] = 'manual';
            }
            
            // Set statement descriptor if available
            if ($this->config->stripe_statement_descriptor) {
                $paymentIntentData['statement_descriptor'] = $this->config->stripe_statement_descriptor;
            }
            
            Log::info('Payment intent data', $paymentIntentData);
            
            $paymentIntent = $this->stripe->paymentIntents->create($paymentIntentData);
            
            $result = [
                'clientSecret' => $paymentIntent->client_secret,
                'paymentIntentId' => $paymentIntent->id,
                'amount' => $paymentIntent->amount / 100, // Convert back to decimal
                'currency' => $paymentIntent->currency,
            ];
            
            Log::info('Stripe payment intent created successfully', [
                'intent_id' => $paymentIntent->id,
                'client_secret' => substr($paymentIntent->client_secret, 0, 10) . '...'
            ]);
            
            return $result;
        } catch (ApiErrorException $e) {
            Log::error('Stripe API Error: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'error_type' => get_class($e),
                'error_code' => $e->getStripeCode(),
                'error_details' => method_exists($e, 'getJsonBody') ? $e->getJsonBody() : null
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Unexpected error creating payment intent: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'error_type' => get_class($e)
            ]);
        }
    }

    /**
     * Retrieve a payment intent
     *
     * @param string $paymentIntentId
     * @return \Stripe\PaymentIntent|null
     */
    public function retrievePaymentIntent($paymentIntentId)
    {
        if (!$this->stripe) {
            return null;
        }

        try {
            return $this->stripe->paymentIntents->retrieve($paymentIntentId);
        } catch (ApiErrorException $e) {
            Log::error('Stripe API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Capture a payment intent that was created with manual capture
     *
     * @param string $paymentIntentId
     * @return \Stripe\PaymentIntent|null
     */
    public function capturePaymentIntent($paymentIntentId)
    {
        if (!$this->stripe) {
            return null;
        }

        try {
            return $this->stripe->paymentIntents->capture($paymentIntentId);
        } catch (ApiErrorException $e) {
            Log::error('Stripe API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cancel a payment intent
     *
     * @param string $paymentIntentId
     * @return \Stripe\PaymentIntent|null
     */
    public function cancelPaymentIntent($paymentIntentId)
    {
        if (!$this->stripe) {
            return null;
        }

        try {
            return $this->stripe->paymentIntents->cancel($paymentIntentId);
        } catch (ApiErrorException $e) {
            Log::error('Stripe API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Handle a webhook event
     *
     * @param string $payload
     * @param string $sigHeader
     * @return array
     */
    public function handleWebhook($payload, $sigHeader)
    {
        if (!$this->stripe || !$this->config->stripe_webhook_secret) {
            Log::error('Stripe webhook is not configured', [
                'has_stripe' => (bool)$this->stripe,
                'has_webhook_secret' => !empty($this->config->stripe_webhook_secret)
            ]);
            return ['success' => false, 'message' => 'Stripe webhook is not configured'];
        }

        try {
            // Log the signature and secret first few characters to help with debugging
            Log::debug('Attempting signature verification', [
                'sig_header_length' => strlen($sigHeader),
                'webhook_secret_prefix' => substr($this->config->stripe_webhook_secret, 0, 8) . '...',
                'payload_length' => strlen($payload)
            ]);
            
            // Verify signature and construct event
            $event = \Stripe\Webhook::constructEvent(
                $payload, 
                $sigHeader, 
                $this->config->stripe_webhook_secret
            );
            
            Log::info('Stripe webhook signature verified', [
                'event_type' => $event->type,
                'event_id' => $event->id,
                'api_version' => $event->api_version,
            ]);

            // Handle the event based on its type
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    
                    // Extract order ID from metadata or object
                    $orderId = null;
                    if (isset($paymentIntent->metadata->order_id)) {
                        $orderId = $paymentIntent->metadata->order_id;
                    } elseif (isset($paymentIntent->metadata) && is_array($paymentIntent->metadata)) {
                        $orderId = $paymentIntent->metadata['order_id'] ?? null;
                    }
                    
                    Log::info('Payment intent succeeded', [
                        'payment_intent_id' => $paymentIntent->id,
                        'order_id' => $orderId,
                        'amount' => $paymentIntent->amount / 100,
                        'currency' => $paymentIntent->currency,
                        'metadata' => json_encode($paymentIntent->metadata)
                    ]);
                    
                    return [
                        'success' => true,
                        'event' => $event->type,
                        'paymentIntentId' => $paymentIntent->id,
                        'orderId' => $orderId,
                    ];

                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    
                    // Extract order ID from metadata
                    $orderId = null;
                    if (isset($paymentIntent->metadata->order_id)) {
                        $orderId = $paymentIntent->metadata->order_id;
                    } elseif (isset($paymentIntent->metadata) && is_array($paymentIntent->metadata)) {
                        $orderId = $paymentIntent->metadata['order_id'] ?? null;
                    }
                    
                    Log::info('Payment intent failed', [
                        'payment_intent_id' => $paymentIntent->id,
                        'order_id' => $orderId,
                        'error_message' => $paymentIntent->last_payment_error->message ?? null,
                        'error_code' => $paymentIntent->last_payment_error->code ?? null,
                        'metadata' => json_encode($paymentIntent->metadata)
                    ]);
                    
                    return [
                        'success' => true,
                        'event' => $event->type,
                        'paymentIntentId' => $paymentIntent->id,
                        'orderId' => $orderId,
                    ];
                    
                case 'charge.succeeded':
                    $charge = $event->data->object;
                    
                    // Extract order ID from metadata
                    $orderId = null;
                    if (isset($charge->metadata->order_id)) {
                        $orderId = $charge->metadata->order_id;
                    } elseif (isset($charge->metadata) && is_array($charge->metadata)) {
                        $orderId = $charge->metadata['order_id'] ?? null;
                    }
                    
                    Log::info('Charge succeeded', [
                        'charge_id' => $charge->id,
                        'payment_intent_id' => $charge->payment_intent,
                        'order_id' => $orderId,
                        'amount' => $charge->amount / 100,
                        'currency' => $charge->currency,
                        'description' => $charge->description,
                        'metadata' => json_encode($charge->metadata)
                    ]);
                    
                    return [
                        'success' => true,
                        'event' => $event->type,
                        'paymentIntentId' => $charge->payment_intent,
                        'orderId' => $orderId,
                    ];

                default:
                    // Log but don't take action on other event types
                    Log::info('Received unhandled Stripe event', [
                        'event_type' => $event->type,
                        'event_id' => $event->id
                    ]);
                    
                    return [
                        'success' => true,
                        'event' => $event->type,
                        'message' => 'Unhandled event type',
                    ];
            }
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            Log::error('Webhook error - invalid payload: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return ['success' => false, 'message' => 'Invalid payload: ' . $e->getMessage()];
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            Log::error('Webhook signature error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return ['success' => false, 'message' => 'Invalid signature: ' . $e->getMessage()];
        } catch (\Exception $e) {
            // Other exceptions
            Log::error('General webhook error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['success' => false, 'message' => 'Error processing webhook: ' . $e->getMessage()];
        }
    }

    /**
     * Check if Stripe is properly configured
     *
     * @return bool
     */
    public function isConfigured()
    {
        $result = (
            $this->config &&
            $this->config->enable_stripe &&
            !empty($this->config->stripe_publishable_key) &&
            !empty($this->config->stripe_secret_key)
        );
        
        Log::info('Stripe isConfigured', [
            'result' => $result,
            'has_config' => (bool)$this->config,
            'enable_stripe' => $this->config ? $this->config->enable_stripe : false,
            'has_publishable_key' => $this->config && !empty($this->config->stripe_publishable_key),
            'has_secret_key' => $this->config && !empty($this->config->stripe_secret_key)
        ]);
        
        return $result;
    }

    /**
     * Get the publishable key
     *
     * @return string|null
     */
    public function getPublishableKey()
    {
        return $this->config->stripe_publishable_key ?? null;
    }
} 