<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Offer;
use Illuminate\Support\Facades\View;

class PrintService
{
    /**
     * Generate a PDF for an order
     *
     * @param Order $order
     * @param bool $download Whether to download or stream the PDF
     * @return \Illuminate\Http\Response
     */
    public function generateOrderPDF(Order $order, $download = true)
    {
        $order->load(['items.product', 'items.offer', 'user']);
        
        $pdf = PDF::loadView('admin.pdf.order', compact('order'));
        $pdf->setPaper('a4');
        
        $filename = 'order-' . $order->order_number . '.pdf';
        
        return $download ? $pdf->download($filename) : $pdf->stream($filename);
    }
    
    /**
     * Generate a PDF for a product
     *
     * @param Product $product
     * @param bool $download Whether to download or stream the PDF
     * @return \Illuminate\Http\Response
     */
    public function generateProductPDF(Product $product, $download = true)
    {
        $product->load(['category', 'images']);
        
        $pdf = PDF::loadView('admin.pdf.product', compact('product'));
        $pdf->setPaper('a4');
        
        $filename = 'product-' . $product->id . '-' . $product->slug . '.pdf';
        
        return $download ? $pdf->download($filename) : $pdf->stream($filename);
    }
    
    /**
     * Generate a PDF for an offer
     *
     * @param Offer $offer
     * @param bool $download Whether to download or stream the PDF
     * @return \Illuminate\Http\Response
     */
    public function generateOfferPDF(Offer $offer, $download = true)
    {
        $offer->load(['products']);
        
        $pdf = PDF::loadView('admin.pdf.offer', compact('offer'));
        $pdf->setPaper('a4');
        
        $filename = 'offer-' . $offer->id . '.pdf';
        
        return $download ? $pdf->download($filename) : $pdf->stream($filename);
    }
    
    /**
     * Generate a PDF for a customer
     *
     * @param User $user
     * @param bool $download Whether to download or stream the PDF
     * @return \Illuminate\Http\Response
     */
    public function generateCustomerPDF(User $user, $download = true)
    {
        $user->load(['orders' => function($query) {
            $query->latest()->limit(10);
        }]);
        
        $pdf = PDF::loadView('admin.pdf.customer', compact('user'));
        $pdf->setPaper('a4');
        
        $filename = 'customer-' . $user->id . '.pdf';
        
        return $download ? $pdf->download($filename) : $pdf->stream($filename);
    }
    
    /**
     * Generate a PDF for inventory report
     *
     * @param array $data
     * @param bool $download Whether to download or stream the PDF
     * @return \Illuminate\Http\Response
     */
    public function generateInventoryPDF($data, $download = true)
    {
        $pdf = PDF::loadView('admin.pdf.inventory', compact('data'));
        $pdf->setPaper('a4', 'landscape');
        
        $filename = 'inventory-report-' . now()->format('Y-m-d') . '.pdf';
        
        return $download ? $pdf->download($filename) : $pdf->stream($filename);
    }
    
    /**
     * Generate a PDF for sales report
     *
     * @param array $data
     * @param bool $download Whether to download or stream the PDF
     * @return \Illuminate\Http\Response
     */
    public function generateSalesReportPDF($data, $download = true)
    {
        $pdf = PDF::loadView('admin.pdf.sales-report', compact('data'));
        $pdf->setPaper('a4', 'landscape');
        
        $filename = 'sales-report-' . now()->format('Y-m-d') . '.pdf';
        
        return $download ? $pdf->download($filename) : $pdf->stream($filename);
    }
} 