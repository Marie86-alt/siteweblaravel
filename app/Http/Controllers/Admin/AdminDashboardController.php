<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Category;
use Carbon\Carbon;
 // Assuming you have a model for the admin dashboard

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        // Statistiques générales
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'total_categories' => Category::count(),
            'total_customers' => User::where('is_admin', false)->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'total_revenue' => Order::whereIn('status', ['confirmed', 'preparing', 'shipped', 'delivered'])->sum('total_amount'),
            'this_month_revenue' => Order::whereIn('status', ['confirmed', 'preparing', 'shipped', 'delivered'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount'),
            'low_stock_products' => Product::whereColumn('stock_quantity', '<=', 'min_stock')->count(),
        ];

        // Commandes récentes
        $recentOrders = Order::with(['user', 'orderItems'])
            ->latest()
            ->limit(10)
            ->get();

        // Produits en rupture de stock
        $lowStockProducts = Product::whereColumn('stock_quantity', '<=', 'min_stock')
            ->where('is_active', true)
            ->limit(10)
            ->get();

        // Top produits vendus ce mois
        $topProducts = \DB::table('orders_items')
            ->join('orders', 'orders_items.order_id', '=', 'orders.id')
            ->join('products', 'orders_items.product_id', '=', 'products.id')
            ->whereMonth('orders.created_at', now()->month)
            ->whereYear('orders.created_at', now()->year)
            ->whereIn('orders.status', ['confirmed', 'preparing', 'shipped', 'delivered'])
            ->select(
                'products.name',
                'products.id',
                \DB::raw('SUM(orders_items.quantity) as total_sold'),
                \DB::raw('SUM(orders_items.total) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Ventes des 7 derniers jours
        $salesChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailySales = Order::whereDate('created_at', $date)
                ->whereIn('status', ['confirmed', 'preparing', 'shipped', 'delivered'])
                ->sum('total_amount');

            $salesChart[] = [
                'date' => $date->format('d/m'),
                'sales' => $dailySales
            ];
        }

        // Répartition des commandes par statut
        $ordersByStatus = [
            'pending' => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'preparing' => Order::where('status', 'preparing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'lowStockProducts',
            'topProducts',
            'salesChart',
            'ordersByStatus'
        ));
    }

    public function stats(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year

        switch ($period) {
            case 'day':
                $startDate = Carbon::today();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                break;
            default:
                $startDate = Carbon::now()->startOfMonth();
        }

        $stats = [
            'period' => $period,
            'orders_count' => Order::where('created_at', '>=', $startDate)->count(),
            'revenue' => Order::where('created_at', '>=', $startDate)
                ->whereIn('status', ['confirmed', 'preparing', 'shipped', 'delivered'])
                ->sum('total_amount'),
            'new_customers' => User::where('created_at', '>=', $startDate)
                ->where('is_admin', false)
                ->count(),
            'products_sold' => \DB::table('orders_items')
                ->join('orders', 'orders_items.order_id', '=', 'orders.id')
                ->where('orders.created_at', '>=', $startDate)
                ->whereIn('orders.status', ['confirmed', 'preparing', 'shipped', 'delivered'])
                ->sum('orders_items.quantity'),
        ];

        return response()->json($stats);
    }
}
