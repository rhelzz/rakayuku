<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Material;
use App\Models\OrderMaterial;
use App\Models\ProductionCost;
use App\Services\OrderService;
use App\Services\ProductionService;
use App\Exports\OrderExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    protected OrderService $orderService;
    protected ProductionService $productionService;

    public function __construct(OrderService $orderService, ProductionService $productionService)
    {
        $this->orderService = $orderService;
        $this->productionService = $productionService;
    }

    public function index(Request $request)
    {
        $query = Order::with('customer')
            ->search($request->search, ['order_number', 'project_name', 'customer.name'])
            ->dateRange($request->date_range, $request->start_date, $request->end_date)
            ->sort($request->sort_field ?? 'created_at', $request->sort_dir ?? 'desc');

        $orders = $query->paginate(15)->withQueryString();

        $kanbanOrders = (clone $query)->get();
        
        $pendingOrders = $kanbanOrders->where('status', Order::STATUS_PENDING);
        $inProductionOrders = $kanbanOrders->where('status', Order::STATUS_IN_PRODUCTION);
        $deliveryOrders = $kanbanOrders->whereIn('status', [Order::STATUS_DELIVERING, Order::STATUS_UNPAID_DELIVERED]);
        $finishedOrders = $kanbanOrders->where('status', Order::STATUS_FINISHED);

        return view('orders.index', compact('orders', 'pendingOrders', 'inProductionOrders', 'deliveryOrders', 'finishedOrders'));
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
            'dp_amount' => 'required|numeric|min:0|lte:selling_price',
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
        $materials = Material::where('current_qty', '>', 0)->get();
        return view('orders.show', compact('order', 'materials'));
    }

    public function edit(Order $order)
    {
        if (!$order->isEditable()) {
            return back()->with('error', 'Pesanan hanya bisa diedit saat status PENDING.');
        }

        $customers = Customer::all();
        return view('orders.edit', compact('order', 'customers'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'project_name' => 'required|string|max:255',
            'project_description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'selling_price' => 'required|numeric|min:0',
        ]);

        try {
            $this->orderService->updateOrder($order, $request->all());
            return redirect()->route('orders.show', $order)->with('success', 'Pesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function cancel(Request $request, Order $order)
    {
        $request->validate([
            'cancel_reason' => 'nullable|string|max:500',
        ]);

        try {
            $this->orderService->cancelOrder($order, $request->input('cancel_reason'));
            return redirect()->route('orders.show', $order)->with('success', 'Pesanan berhasil dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
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
            return back()->with('success', 'Produksi selesai. Proyek sekarang dalam tahap pengantaran.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function markAsDelivered(Order $order)
    {
        try {
            $this->orderService->markAsDelivered($order);
            $message = $order->status === Order::STATUS_FINISHED 
                ? 'Proyek telah selesai dan lunas.' 
                : 'Proyek telah diantar, namun masih ada sisa pembayaran (Hutang).';
            
            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function printInvoice(Order $order)
    {
        $order->load(['customer', 'payments']);
        return view('orders.print', compact('order'));
    }

    public function pay(Request $request, Order $order)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:DP,FINAL,INSTALLMENT',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            $this->orderService->payOrder(
                $order, 
                $request->input('amount'), 
                $request->input('type'),
                $request->input('notes')
            );
            return back()->with([
                'success' => 'Pembayaran berhasil ditambahkan.',
                'active_tab' => 'payments'
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function addMaterial(Request $request, Order $order)
    {
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'qty' => 'required|numeric|min:0.01',
        ]);

        try {
            $material = Material::findOrFail($request->input('material_id'));
            $this->productionService->addMaterialToOrder($order, $material, $request->input('qty'));
            return back()->with([
                'success' => 'Bahan baku berhasil ditambahkan ke proyek.',
                'active_tab' => 'materials'
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function removeMaterial(OrderMaterial $orderMaterial)
    {
        try {
            $this->productionService->removeMaterialFromOrder($orderMaterial);
            return back()->with([
                'success' => 'Bahan baku berhasil dihapus dari proyek dan stok dikembalikan.',
                'active_tab' => 'materials'
            ]);
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
            return back()->with([
                'success' => 'Biaya tambahan berhasil dicatat.',
                'active_tab' => 'costs'
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function removeCost(ProductionCost $productionCost)
    {
        try {
            $this->productionService->removeProductionCost($productionCost);
            return back()->with([
                'success' => 'Biaya produksi berhasil dihapus.',
                'active_tab' => 'costs'
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        return Excel::download(new OrderExport($request->start_date, $request->end_date), 'Daftar_Pesanan_' . now()->format('Y-m-d_His') . '.xlsx');
    }
}
