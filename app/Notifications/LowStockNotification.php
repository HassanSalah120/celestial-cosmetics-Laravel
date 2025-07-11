<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Product;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $product;
    protected $currentStock;

    /**
     * Create a new notification instance.
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
        $this->currentStock = $product->stock;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('admin.products.edit', $this->product);
        
        return (new MailMessage)
            ->subject('Low Stock Alert: ' . $this->product->name)
            ->greeting('Low Stock Alert!')
            ->line('The product "' . $this->product->name . '" is running low on stock.')
            ->line('Current stock level: ' . $this->currentStock)
            ->line('Low stock threshold: ' . $this->product->low_stock_threshold)
            ->action('Manage Product', $url)
            ->line('Please restock this product soon to avoid stockouts.')
            ->line('Thank you for using Celestial Cosmetics!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'current_stock' => $this->currentStock,
            'threshold' => $this->product->low_stock_threshold,
            'url' => route('admin.products.edit', $this->product),
        ];
    }
}
