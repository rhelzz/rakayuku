<?php

namespace App\Http\Controllers;

use App\Exports\CashflowExport;
use App\Models\Cashflow;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CashflowController extends Controller
{
    public function index(Request $request)
    {
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

        return view('cashflows.index', compact('cashflows', 'currentBalance'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:INITIAL,IN,OUT',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
        ]);

        if ($request->type === 'OUT' && $request->amount > Cashflow::currentBalance()) {
            return back()->withInput()->with('error', 'Saldo perusahaan tidak mencukupi. Sisa Saldo: ' . formatRupiah(Cashflow::currentBalance()));
        }

        Cashflow::create([
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Catatan arus kas berhasil ditambahkan.');
    }

    public function export(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new CashflowExport($request->type, $request->start_date, $request->end_date), 'Laporan_Arus_Kas_' . now()->format('Y-m-d_His') . '.xlsx');
    }
}
