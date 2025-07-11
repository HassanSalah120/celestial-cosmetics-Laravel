<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;

class CheckOrderTotals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:check-totals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all order totals to verify they are correct';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::all();
        
        $this->info('Checking order totals...');
        $this->newLine();
        
        $headers = ['ID', 'Order Number', 'Subtotal', 'Discount', 'Shipping', 'Total'];
        $rows = [];
        
        foreach ($orders as $order) {
            $rows[] = [
                $order->id,
                $order->order_number,
                $order->subtotal,
                $order->discount_amount,
                $order->shipping_fee,
                $order->total_amount
            ];
        }
        
        $this->table($headers, $rows);
        
        return Command::SUCCESS;
    }
} 