<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Review;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // إحصائيات سريعة
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'low_stock_products' => Product::where('quantity', '<=', \DB::raw('low_stock_threshold'))
                ->where('quantity', '>', 0)
                ->count(),
            'out_of_stock_products' => Product::where('quantity', 0)->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_customers' => Customer::count(),
            'new_customers' => Customer::whereDate('created_at', today())->count(),
            'total_reviews' => Review::count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
        ];

        // أحدث المنتجات
        $recentProducts = Product::with(['category', 'images'])
            ->latest()
            ->limit(5)
            ->get();

        // أحدث الطلبات
        $recentOrders = Order::with(['user', 'items'])
            ->latest()
            ->limit(5)
            ->get();

        // المنتجات الأكثر مبيعاً
        $topSellingProducts = Product::with(['category'])
            ->withCount(['orderItems as total_sold' => function($q) {
                $q->select(\DB::raw('SUM(quantity)'));
            }])
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact('stats', 'recentProducts', 'recentOrders', 'topSellingProducts'));
    }
}