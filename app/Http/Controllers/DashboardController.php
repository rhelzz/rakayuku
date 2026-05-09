<?php

namespace App\Http\Controllers;

use App\Models\Cashflow;
use App\Models\Order;
use App\Models\Material;
use App\Models\Purchase;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', Order::STATUS_PENDING)->count();
        $activeOrders = Order::whereIn('status', [Order::STATUS_IN_PRODUCTION, Order::STATUS_DELIVERING, Order::STATUS_UNPAID_DELIVERED])->count();
        $finishedOrders = Order::where('status', Order::STATUS_FINISHED)->count();
        
        $totalProfit = Order::where('status', Order::STATUS_FINISHED)->sum('profit');
        
        $totalReceivable = Order::where('payment_status', '!=', Order::PAYMENT_PAID)
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->get()
            ->sum(function($order) {
                return $order->remaining_payment;
            });
        
        $lowStockMaterials = Material::where('current_qty', '<', 2)->get();

        $recentOrders = Order::with('customer')->latest()->take(5)->get();
        
        $currentBalance = Cashflow::currentBalance();

        return view('dashboard', compact(
            'totalOrders', 'pendingOrders', 'activeOrders', 'finishedOrders',
            'totalProfit', 'totalReceivable', 'lowStockMaterials', 'recentOrders',
            'currentBalance'
        ));
    }
}
