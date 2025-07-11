<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Offer;
use App\Services\PrintService;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    protected $printService;
    
    public function __construct(PrintService $printService)
    {
        $this->printService = $printService;
    }
    
    /**
     * Generate a PDF for an order
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function orderPDF($id, Request $request)
    {
        $order = Order::findOrFail($id);
        $download = $request->has('download');
        
        return $this->printService->generateOrderPDF($order, $download);
    }
    
    /**
     * Generate a PDF for a product
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function productPDF($id, Request $request)
    {
        $product = Product::findOrFail($id);
        $download = $request->has('download');
        
        return $this->printService->generateProductPDF($product, $download);
    }
    
    /**
     * Generate a PDF for an offer
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function offerPDF($id, Request $request)
    {
        $offer = Offer::findOrFail($id);
        $download = $request->has('download');
        
        return $this->printService->generateOfferPDF($offer, $download);
    }
    
    /**
     * Generate a PDF for a customer
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function customerPDF($id, Request $request)
    {
        $user = User::findOrFail($id);
        $download = $request->has('download');
        
        return $this->printService->generateCustomerPDF($user, $download);
    }
    
    /**
     * Generate a PDF for inventory report
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function inventoryPDF(Request $request)
    {
        // Get inventory data
        $products = Product::with('category')->get();
        $lowStockProducts = Product::where('stock', '<=', function($query) {
            $query->selectRaw('COALESCE(low_stock_threshold, 5)');
        })->where('stock', '>', 0)->with('category')->get();
        
        $outOfStockProducts = Product::where('stock', 0)->with('category')->get();
        
        $data = [
            'products' => $products,
            'lowStockProducts' => $lowStockProducts,
            'outOfStockProducts' => $outOfStockProducts,
            'generatedAt' => now(),
        ];
        
        $download = $request->has('download');
        
        return $this->printService->generateInventoryPDF($data, $download);
    }
    
    /**
     * Generate a PDF for sales report
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function salesReportPDF(Request $request)
    {
        // Get date range
        $endDate = $request->input('end_date') 
            ? \Carbon\Carbon::parse($request->input('end_date')) 
            : \Carbon\Carbon::now();
            
        $startDate = $request->input('start_date') 
            ? \Carbon\Carbon::parse($request->input('start_date')) 
            : $endDate->copy()->subDays(30);
        
        // Get orders in date range with all needed relationships
        $orders = Order::whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->with([
                'items.product.category',
                'items.offer',
                'user'
            ])
            ->get();
        
        // Calculate totals
        $totalRevenue = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        
        $data = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'orders' => $orders,
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'averageOrderValue' => $averageOrderValue,
            'generatedAt' => now(),
        ];
        
        $download = $request->has('download');
        
        return $this->printService->generateSalesReportPDF($data, $download);
    }
} 