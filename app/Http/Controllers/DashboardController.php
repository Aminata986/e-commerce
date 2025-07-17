<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function statistics()
    {
        // Nombre total de commandes
        $totalOrders = Order::count();
        
        // Chiffre d'affaires total
        $totalRevenue = Order::where('payment_status', Order::PAYMENT_PAID)->sum('total');
        
        // Produits les plus vendus
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();
        
        // Clients les plus actifs
        $topCustomers = Order::with('user')
            ->select('user_id', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(total) as total_spent'))
            ->groupBy('user_id')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();
        
        // Total des paiements effectués
        $totalPayments = Payment::where('status', 'completed')->sum('amount');
        
        // Commandes par statut
        $ordersByStatus = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();
        
        // Commandes par mois (derniers 12 mois)
        $monthlyOrders = Order::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total) as revenue')
        )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        
        return response()->json([
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'top_products' => $topProducts,
            'top_customers' => $topCustomers,
            'total_payments' => $totalPayments,
            'orders_by_status' => $ordersByStatus,
            'monthly_orders' => $monthlyOrders,
        ]);
    }
} 