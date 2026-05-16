<?php

namespace App\Http\Controllers;

use App\Models\MonthlyClosing;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\Material;
use App\Models\Payment;
use App\Models\Cashflow;
use App\Exports\ClosingExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ClosingController extends Controller
{
    private const MIN_YEAR = 2026;

    public function index(Request $request)
    {
        $currentYear = (int) now()->year;
        $selectedYear = (int) $request->input('year', $currentYear);

        $allowedYears = range(self::MIN_YEAR, $currentYear + 1);
        if (!in_array($selectedYear, $allowedYears)) {
            $selectedYear = $currentYear;
        }

        $currentMonth = now()->startOfMonth();

        $periodDates = collect();
        for ($m = 1; $m <= 12; $m++) {
            $periodDates->push(Carbon::createFromDate($selectedYear, $m, 1)->startOfMonth()->format('Y-m-d'));
        }

        $closings = MonthlyClosing::whereIn('period', $periodDates->toArray())
            ->get()
            ->keyBy(fn($c) => $c->period->format('Y-m-d'));

        $months = $periodDates->map(function ($dateStr) use ($closings, $currentMonth) {
            $date = Carbon::parse($dateStr);
            $closing = $closings->get($dateStr);
            $isClosed = $closing?->status === MonthlyClosing::STATUS_CLOSED;
            $isFuture = $date->gt($currentMonth);
            $isCurrent = $date->equalTo($currentMonth);
            $isPast = $date->lt($currentMonth);

            if ($isClosed) {
                $status = 'CLOSED';
            } elseif ($isFuture) {
                $status = 'UPCOMING';
            } elseif ($isCurrent) {
                $status = 'OPEN';
            } else {
                $status = 'MISSED';
            }

            return [
                'period' => $date,
                'period_end' => $date->copy()->endOfMonth(),
                'label' => $date->translatedFormat('F Y'),
                'closing' => $closing,
                'status' => $status,
                'can_close' => $isCurrent && !$isClosed,
            ];
        })->sortByDesc('period')->values();

        $hasPrevYear = ($selectedYear - 1) >= self::MIN_YEAR;
        $hasNextYear = ($selectedYear + 1) <= ($currentYear + 1);
        $prevYear = $selectedYear - 1;
        $nextYear = $selectedYear + 1;

        return view('closing.index', compact(
            'months',
            'selectedYear',
            'currentYear',
            'hasPrevYear',
            'hasNextYear',
            'prevYear',
            'nextYear'
        ));
    }

    public function show(MonthlyClosing $closing)
    {
        return view('closing.show', compact('closing'));
    }

    public function close(Request $request)
    {
        $request->validate([
            'period' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $periodDate = Carbon::parse($request->period)->startOfMonth();
        $periodLabel = $periodDate->translatedFormat('F Y');
        $currentMonth = now()->startOfMonth();

        if (!$periodDate->equalTo($currentMonth)) {
            if ($periodDate->gt($currentMonth)) {
                return back()->with('error', "Tidak bisa menutup periode {$periodLabel} karena bulan tersebut belum tiba.");
            }
            return back()->with('error', "Tidak bisa menutup periode {$periodLabel} karena bulan tersebut sudah terlewat.");
        }

        return DB::transaction(function () use ($request, $periodDate, $periodLabel) {
            $existing = MonthlyClosing::where('period', $periodDate->format('Y-m-d'))
                ->lockForUpdate()
                ->first();

            if ($existing && $existing->status === MonthlyClosing::STATUS_CLOSED) {
                return back()->with('error', "Periode {$periodLabel} sudah ditutup buku.");
            }

            $snapshot = $this->generateSnapshot($periodDate);

            $closingData = [
                'status' => MonthlyClosing::STATUS_CLOSED,
                'closed_at' => now(),
                'closed_by' => auth()->user()?->name ?? 'Admin Rakayuku',
                'notes' => $request->notes,
                'snapshot' => $snapshot,
            ];

            if ($existing) {
                $existing->update($closingData);
            } else {
                MonthlyClosing::create(array_merge(
                    ['period' => $periodDate->format('Y-m-d')],
                    $closingData,
                ));
            }

            return back()->with('success', "Periode {$periodLabel} berhasil ditutup buku.");
        });
    }

    public function reopen(MonthlyClosing $closing)
    {
        if ($closing->status !== MonthlyClosing::STATUS_CLOSED) {
            return back()->with('error', 'Periode ini belum ditutup buku.');
        }

        $closing->update([
            'status' => MonthlyClosing::STATUS_OPEN,
            'closed_at' => null,
            'closed_by' => null,
        ]);

        return back()->with('success', 'Periode ' . $closing->period_label . ' berhasil dibuka kembali.');
    }

    public function exportClosing(MonthlyClosing $closing)
    {
        $filename = 'Tutup_Buku_' . $closing->period->format('Y-m') . '_' . now()->format('His') . '.xlsx';
        return Excel::download(new ClosingExport($closing), $filename);
    }

    /**
     * Generate financial snapshot for a given period.
     * All queries are efficient (no full table scans, no collection-level sums).
     */
    private function generateSnapshot(Carbon $periodDate): array
    {
        $startOfMonth = $periodDate->copy();
        $endOfMonth = $periodDate->copy()->endOfMonth();

        $totalRevenue = Payment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $totalPurchases = Purchase::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('total_price');

        $totalExpenses = Cashflow::where('type', 'OUT')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $totalCashIncome = Cashflow::whereIn('type', ['IN', 'INITIAL'])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $cashBalance = Cashflow::currentBalance();

        $inventoryValue = (float) Material::query()
            ->selectRaw('COALESCE(SUM(current_qty * avg_price), 0) as total')
            ->value('total');

        $receivables = (float) Order::where('status', '!=', 'CANCELLED')
            ->where('payment_status', '!=', 'PAID')
            ->selectRaw('COALESCE(SUM(selling_price - COALESCE((SELECT SUM(amount) FROM payments WHERE payments.order_id = orders.id), 0)), 0) as total')
            ->value('total');

        $payables = (float) Purchase::where('payment_status', '!=', 'PAID')
            ->selectRaw('COALESCE(SUM(total_price - paid_amount), 0) as total')
            ->value('total');

        $netBalance = ($cashBalance + $receivables) - $payables;

        $orderCount = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $purchaseCount = Purchase::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

        return [
            'total_revenue' => $totalRevenue,
            'total_purchases' => $totalPurchases,
            'total_expenses' => $totalExpenses,
            'total_cash_income' => $totalCashIncome,
            'cash_balance' => $cashBalance,
            'inventory_value' => $inventoryValue,
            'receivables' => $receivables,
            'payables' => $payables,
            'net_balance' => $netBalance,
            'order_count' => $orderCount,
            'purchase_count' => $purchaseCount,
        ];
    }
}
