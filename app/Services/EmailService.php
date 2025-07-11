<?php

namespace App\Services;

use App\Mail\TemplatedMail;
use App\Models\EmailTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Send an email using a template.
     *
     * @param string $to
     * @param string $templateCode
     * @param array $variables
     * @param string|null $toName
     * @param \Illuminate\Database\Eloquent\Model|null $relatedModel
     * @param array $cc
     * @param array $bcc
     * @param array $attachments
     * @return bool
     */
    public function sendTemplatedEmail(
        string $to,
        string $templateCode,
        array $variables = [],
        ?string $toName = null,
        ?Model $relatedModel = null,
        array $cc = [],
        array $bcc = [],
        array $attachments = []
    ): bool {
        // Check if template exists
        $template = EmailTemplate::findByCode($templateCode);
        
        if (!$template) {
            logger()->error("Email template not found: {$templateCode}");
            return false;
        }
        
        try {
            // Set recipient name in variables if not already set
            if ($toName && !isset($variables['recipient_name'])) {
                $variables['recipient_name'] = $toName;
            }
            
            // Create mailable
            $mail = new TemplatedMail($templateCode, $variables, $relatedModel);
            
            // Add CC and BCC recipients
            if (!empty($cc)) {
                $mail->cc($cc);
            }
            
            if (!empty($bcc)) {
                $mail->bcc($bcc);
            }
            
            // Add attachments
            foreach ($attachments as $attachment) {
                $mail->attach($attachment);
            }
            
            // Send email
            Mail::to($to, $toName)->send($mail);
            
            return true;
        } catch (\Exception $e) {
            logger()->error("Failed to send email template {$templateCode} to {$to}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a welcome email to a new user.
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function sendWelcomeEmail($user): bool
    {
        return $this->sendTemplatedEmail(
            $user->email,
            'welcome',
            [
                'name' => $user->name,
                'email' => $user->email,
            ],
            $user->name,
            $user
        );
    }

    /**
     * Send an order confirmation email.
     *
     * @param \App\Models\Order $order
     * @return bool
     */
    public function sendOrderConfirmation($order): bool
    {
        $orderItems = '';
        foreach ($order->items as $item) {
            $orderItems .= "<p><strong>{$item->quantity}x {$item->name}</strong> - " . 
                           format_currency($item->price) . "</p>";
        }
        
        $shippingAddress = "{$order->shipping_address['name']}<br>" .
                          "{$order->shipping_address['address1']}<br>" .
                          ($order->shipping_address['address2'] ? "{$order->shipping_address['address2']}<br>" : '') .
                          "{$order->shipping_address['city']}, {$order->shipping_address['state']} {$order->shipping_address['zip']}<br>" .
                          "{$order->shipping_address['country']}";
        
        return $this->sendTemplatedEmail(
            $order->shipping_address['email'],
            'order_confirmation',
            [
                'customer_name' => $order->shipping_address['name'],
                'order_number' => $order->id,
                'order_date' => $order->created_at->format('F j, Y'),
                'order_total' => format_currency($order->total),
                'payment_method' => $order->payment_method,
                'order_items' => $orderItems,
                'shipping_address' => $shippingAddress,
            ],
            $order->shipping_address['name'],
            $order
        );
    }

    /**
     * Send an order shipped email.
     *
     * @param \App\Models\Order $order
     * @param string $trackingNumber
     * @param string $shippingCarrier
     * @param string $trackingUrl
     * @return bool
     */
    public function sendOrderShipped($order, string $trackingNumber, string $shippingCarrier, string $trackingUrl): bool
    {
        $orderItems = '';
        foreach ($order->items as $item) {
            $orderItems .= "<p><strong>{$item->quantity}x {$item->name}</strong></p>";
        }
        
        return $this->sendTemplatedEmail(
            $order->shipping_address['email'],
            'order_shipped',
            [
                'customer_name' => $order->shipping_address['name'],
                'order_number' => $order->id,
                'tracking_number' => $trackingNumber,
                'shipping_carrier' => $shippingCarrier,
                'tracking_url' => $trackingUrl,
                'order_items' => $orderItems,
            ],
            $order->shipping_address['name'],
            $order
        );
    }

    /**
     * Send an abandoned cart email.
     *
     * @param string $email
     * @param string $name
     * @param array $cartItems
     * @param string $cartRecoveryLink
     * @param \Illuminate\Database\Eloquent\Model|null $user
     * @return bool
     */
    public function sendAbandonedCartEmail(
        string $email,
        string $name,
        array $cartItems,
        string $cartRecoveryLink,
        ?Model $user = null
    ): bool {
        $itemsHtml = '';
        foreach ($cartItems as $item) {
            $itemsHtml .= "<p><strong>{$item['quantity']}x {$item['name']}</strong> - " . 
                          format_currency($item['price']) . "</p>";
        }
        
        return $this->sendTemplatedEmail(
            $email,
            'abandoned_cart',
            [
                'customer_name' => $name,
                'cart_items' => $itemsHtml,
                'cart_recovery_link' => $cartRecoveryLink,
            ],
            $name,
            $user
        );
    }
} 