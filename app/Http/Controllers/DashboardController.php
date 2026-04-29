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
        $pendingOrders = Order::where('status', '=', 'PENDING', 'and')->count('*');
        $inProductionOrders = Order::where('status', '=', 'IN_PRODUCTION', 'and')->count('*');
        $finishedOrders = Order::where('status', '=', 'FINISHED', 'and')->count('*');
        
        $totalProfit = Order::where('status', '=', 'FINISHED', 'and')->sum('profit');
        
        $lowStockMaterials = Material::where('current_qty', '<', 5, 'and')->get();

        $recentOrders = Order::with('customer')->latest()->take(5)->get();

        return view('dashboard', compact(
            'totalOrders', 'pendingOrders', 'inProductionOrders', 'finishedOrders',
            'totalProfit', 'lowStockMaterials', 'recentOrders'
        ));
    }
}
