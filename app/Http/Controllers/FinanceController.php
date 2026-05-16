<?php

namespace App\Http\Controllers;

use App\Exports\OverallBalanceExport;
use App\Exports\CashflowExport;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\Material;
use App\Models\Payment;
use App\Models\Cashflow;
use App\Traits\CheckClosingPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class FinanceController extends Controller
{
    use CheckClosingPeriod;

    public function index()
    {
        $totalOmset = Payment::sum('amount');
        
        $totalHutang = (float) Purchase::where('payment_status', '!=', 'PAID')
            ->selectRaw('COALESCE(SUM(total_price - paid_amount), 0) as total')
            ->value('total');

        $totalPiutang = (float) Order::where('status', '!=', 'CANCELLED')
            ->where('payment_status', '!=', 'PAID')
            ->selectRaw('COALESCE(SUM(selling_price - COALESCE((SELECT SUM(amount) FROM payments WHERE payments.order_id = orders.id), 0)), 0) as total')
            ->value('total');

        $stockValue = (float) Material::query()
            ->selectRaw('COALESCE(SUM(current_qty * avg_price), 0) as total')
            ->value('total');
        $totalWIP = (float) Order::whereIn('status', [Order::STATUS_PENDING, Order::STATUS_IN_PRODUCTION, Order::STATUS_DELIVERING])
            ->sum('total_cost');
        $totalInventaris = $stockValue + $totalWIP;

        $cashInHand = Cashflow::currentBalance();

        $saldoNett = ($cashInHand + $totalPiutang) - $totalHutang;

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

    public function cashflowDetail(Request $request)
    {
        $request->validate([
            'type' => 'nullable|in:IN,OUT,INITIAL',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $query = Cashflow::query()->with('reference');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }

        $cashflows = $query->latest()->paginate(15)->withQueryString();
        $currentBalance = Cashflow::currentBalance();

        return view('finance.cashflow', compact('cashflows', 'currentBalance'));
    }

    public function storeCashflow(Request $request)
    {
        $request->validate([
            'type' => 'required|in:INITIAL,IN,OUT',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
        ]);

        $closingCheck = $this->checkClosingPeriod();
        if ($closingCheck) return $closingCheck;

        return DB::transaction(function () use ($request) {
            if ($request->type === 'OUT') {
                $currentBalance = Cashflow::currentBalance();
                if ($request->amount > $currentBalance) {
                    return back()->withInput()->with('error', 'Saldo perusahaan tidak mencukupi. Sisa Saldo: ' . formatRupiah($currentBalance));
                }
            }

            Cashflow::create([
                'type' => $request->type,
                'amount' => $request->amount,
                'description' => $request->description,
            ]);

            return back()->with('success', 'Catatan arus kas berhasil ditambahkan.');
        });
    }

    public function exportCashflow(Request $request)
    {
        return Excel::download(new CashflowExport($request->type, $request->start_date, $request->end_date), 'Laporan_Arus_Kas_' . now()->format('Y-m-d_His') . '.xlsx');
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
