<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Material;
use App\Models\OrderMaterial;
use App\Services\OrderService;
use App\Services\ProductionService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;
    protected ProductionService $productionService;

    public function __construct(OrderService $orderService, ProductionService $productionService)
    {
        $this->orderService = $orderService;
        $this->productionService = $productionService;
    }

    public function index()
    {
        $orders = Order::with('customer')->latest('created_at')->get(['*']);
        
        $pendingOrders = $orders->where('status', Order::STATUS_PENDING);
        $inProductionOrders = $orders->where('status', Order::STATUS_IN_PRODUCTION);
        $finishedOrders = $orders->where('status', Order::STATUS_FINISHED);

        return view('orders.index', compact('pendingOrders', 'inProductionOrders', 'finishedOrders'));
    }

    public function create()
    {
        $customers = Customer::all();
        return view('orders.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_number' => 'nullable|string|unique:orders,order_number',
            'project_name' => 'required|string|max:255',
            'project_description' => 'nullable|string',
            'deadline' => 'nullable|date|after_or_equal:today',
            'selling_price' => 'required|numeric|min:0',
            'dp_amount' => 'required|numeric|min:0',
        ]);

        try {
            $order = $this->orderService->createOrder($request->all());
            return redirect()->route('orders.show', $order)->with('success', 'Pesanan berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'materials.material', 'productionCosts', 'payments']);
        $materials = Material::where('current_qty', '>', 0, 'and')->get(['*']); 
        return view('orders.show', compact('order', 'materials'));
    }

    public function startProduction(Order $order)
    {
        try {
            $this->orderService->startProduction($order);
            return back()->with('success', 'Produksi telah dimulai.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function finishProduction(Order $order)
    {
        try {
            $this->orderService->finishOrder($order);
            return back()->with('success', 'Produksi telah selesai. HPP dan Profit telah dikalkulasi secara final.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function pay(Request $request, Order $order)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:DP,FINAL'
        ]);

        try {
            $this->orderService->payOrder($order, $request->input('amount'), $request->input('type'));
            return back()->with('success', 'Pembayaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function addMaterial(Request $request, Order $order)
    {
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'qty' => 'required|integer|min:1',
        ]);

        try {
            $material = Material::findOrFail($request->input('material_id'));
            $this->productionService->addMaterialToOrder($order, $material, $request->input('qty'));
            return back()->with('success', 'Bahan baku berhasil ditambahkan ke proyek.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function removeMaterial(OrderMaterial $orderMaterial)
    {
        try {
            $this->productionService->removeMaterialFromOrder($orderMaterial);
            return back()->with('success', 'Bahan baku berhasil dihapus dari proyek dan stok dikembalikan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function addCost(Request $request, Order $order)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
        ]);

        try {
            $this->productionService->addProductionCost($order, $request->input('type'), $request->input('amount'), $request->input('description'));
            return back()->with('success', 'Biaya tambahan berhasil dicatat.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
