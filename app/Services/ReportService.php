<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Material;
use App\Models\Purchase;
use App\Models\Payment;
use App\Models\ProductionCost;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ReportService
{
    /**
     * Get Order trends (Volume & Revenue)
     */
    public function getOrderTrends($range, $start = null, $end = null)
    {
        return Order::query()
            ->dateRange($range, $start, $end)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(selling_price) as revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    /**
     * Get Stock Health (Current vs Used in period)
     */
    public function getInventoryHealth()
    {
        return Material::all()->map(function ($m) {
            return [
                'name' => $m->name,
                'stock' => $m->current_qty,
                'value' => $m->current_qty * $m->avg_price,
                'status' => $m->current_qty < 5 ? 'Low' : 'Healthy'
            ];
        });
    }

    /**
     * Get Financial Summary per Order
     */
    public function getFinancialData($range, $start = null, $end = null)
    {
        return Order::with(['customer', 'materials', 'productionCosts'])
            ->dateRange($range, $start, $end)
            ->latest()
            ->get()
            ->map(function ($order) {
                $materialCost = $order->materials->sum('subtotal');
                $productionCost = $order->productionCosts->sum('amount');
                $totalHpp = $materialCost + $productionCost;
                $profit = $order->selling_price - $totalHpp;
                $margin = $order->selling_price > 0 ? ($profit / $order->selling_price) * 100 : 0;

                return [
                    'order' => $order,
                    'material_cost' => $materialCost,
                    'production_cost' => $productionCost,
                    'total_hpp' => $totalHpp,
                    'profit' => $profit,
                    'margin' => $margin
                ];
            });
    }

    /**
     * Get Overall Cashflow (In vs Out)
     */
    public function getCashflowData($range, $start = null, $end = null)
    {
        // Income from Payments
        $income = Payment::query()
            ->dateRange($range, $start, $end)
            ->sum('amount');

        // Outcome from Purchases
        $purchaseCost = Purchase::query()
            ->dateRange($range, $start, $end, 'purchase_date')
            ->sum('total_price');
            
        // Outcome from Production Costs (Operational)
        $operationalCost = ProductionCost::query()
            ->dateRange($range, $start, $end)
            ->sum('amount');

        return [
            'income' => $income,
            'outcome' => $purchaseCost + $operationalCost,
            'purchase_cost' => $purchaseCost,
            'operational_cost' => $operationalCost
        ];
    }

    /**
     * Get Top Used Materials (by Value)
     */
    public function getTopMaterials($range, $start = null, $end = null)
    {
        return DB::table('order_materials')
            ->join('materials', 'order_materials.material_id', '=', 'materials.id')
            ->join('orders', 'order_materials.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', $this->getDateRangeBoundaries($range, $start, $end))
            ->select('materials.name', DB::raw('SUM(order_materials.subtotal) as total_value'))
            ->groupBy('materials.id', 'materials.name')
            ->orderByDesc('total_value')
            ->limit(5)
            ->get();
    }

    /**
     * Helper to get date boundaries for raw DB queries
     */
    protected function getDateRangeBoundaries($range, $start, $end)
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        switch ($range) {
            case 'today': $startDate = Carbon::today(); break;
            case 'yesterday': $startDate = Carbon::yesterday(); $endDate = Carbon::yesterday()->endOfDay(); break;
            case '7_days': $startDate = Carbon::now()->subDays(7); break;
            case 'this_month': $startDate = Carbon::now()->startOfMonth(); break;
            case 'this_quarter': $startDate = Carbon::now()->startOfQuarter(); break;
            case '6_months': $startDate = Carbon::now()->subMonths(6); break;
            case 'this_year': $startDate = Carbon::now()->startOfYear(); break;
            case 'custom': $startDate = Carbon::parse($start); $endDate = Carbon::parse($end)->endOfDay(); break;
        }

        return [$startDate, $endDate];
    }
}
