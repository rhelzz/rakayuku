<?php

namespace App\Http\Controllers;

use App\Exports\OverallBalanceExport;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\Material;
use App\Models\Payment;
use App\Models\Cashflow;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FinanceController extends Controller
{
    public function index()
    {
        // 1. Saldo Omset (Revenue from payments)
        $totalOmset = Payment::sum('amount');
        
        // 2. Hutang (Payables from purchases)
        $totalHutang = Purchase::query()->where('payment_status', '!=', 'PAID')
            ->get()
            ->sum(fn($p) => $p->total_price - $p->paid_amount);

        // 3. Piutang (Receivables from orders)
        $totalPiutang = Order::query()->where('status', '!=', 'CANCELLED')
            ->where('payment_status', '!=', 'PAID')
            ->get()
            ->sum(fn($o) => $o->remaining_payment);

        // 4. Saldo Inventaris (Stock value + Work In Progress)
        $stockValue = Material::all()->sum(fn($m) => $m->current_qty * $m->avg_price);
        $totalWIP = Order::query()->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_IN_PRODUCTION, Order::STATUS_DELIVERING], 'and', false)->sum('total_cost');
        $totalInventaris = $stockValue + $totalWIP;

        // Cash in Hand (Current Cashflow Balance)
        $cashInHand = Cashflow::currentBalance();

        // 5. Saldo Nett
        // Net = (Cash + Piutang + Inventaris) - Hutang
        $saldoNett = ($cashInHand + $totalPiutang + $totalInventaris) - $totalHutang;

        return view('finance.index', compact(
            'totalOmset',
            'totalHutang',
            'totalPiutang',
            'totalInventaris',
            'stockValue',
            'totalWIP',
            'cashInHand',
            'saldoNett'
        ));
    }

    public function inventoryDetail()
    {
        $materials = Material::query()->where('current_qty', '>', 0)
            ->orderBy('name')
            ->get();
            
        return view('finance.inventory', compact('materials'));
    }

    public function receivablesDetail()
    {
        $orders = Order::query()->where('status', '!=', 'CANCELLED')
            ->where('payment_status', '!=', 'PAID')
            ->with('customer')
            ->get();
            
        return view('finance.receivables', compact('orders'));
    }

    public function payablesDetail()
    {
        $purchases = Purchase::query()->where('payment_status', '!=', 'PAID')
            ->get();
            
        return view('finance.payables', compact('purchases'));
    }

    public function exportOverall(Request $request)
    {
        $filename = 'Ringkasan_Overall_Keuangan_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new OverallBalanceExport($request->start_date, $request->end_date),
            $filename
        );
    }
}
