<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Material;
use App\Models\Purchase;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count('*');
        $pendingOrders = Order::where('status', '=', Order::STATUS_PENDING, 'and')->count('*');
        $inProductionOrders = Order::where('status', '=', Order::STATUS_IN_PRODUCTION, 'and')->count('*');
        $finishedOrders = Order::where('status', '=', Order::STATUS_FINISHED, 'and')->count('*');
        
        // Financial Metrics
        $totalProfit = Order::where('status', '=', Order::STATUS_FINISHED, 'and')->sum('profit');
        
        // Total Piutang (Remaining Payments from non-paid orders)
        $totalReceivable = Order::all()->sum(function($order) {
            return $order->remaining_payment;
        });
        
        // Critical Inventory (Less than 2 units)
        $lowStockMaterials = Material::where('current_qty', '<', 2, 'and')->get();

        $recentOrders = Order::with('customer')->latest()->take(5)->get();

        return view('dashboard', compact(
            'totalOrders', 'pendingOrders', 'inProductionOrders', 'finishedOrders',
            'totalProfit', 'totalReceivable', 'lowStockMaterials', 'recentOrders'
        ));
    }
}
