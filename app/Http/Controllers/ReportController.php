<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Exports\FinancialReportExport;
use App\Exports\PaymentExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function analytics(Request $request)
    {
        $range = $request->input('date_range', '30_days');
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        $orderTrends = $this->reportService->getOrderTrends($range, $start, $end);
        $inventoryHealth = $this->reportService->getInventoryHealth();
        $cashflow = $this->reportService->getCashflowData($range, $start, $end);
        $topMaterials = $this->reportService->getTopMaterials($range, $start, $end);

        return view('reports.analytics', compact('orderTrends', 'inventoryHealth', 'cashflow', 'topMaterials', 'range'));
    }

    public function finance(Request $request)
    {
        $range = $request->input('date_range', '30_days');
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        if ($request->input('export') === 'excel') {
            return $this->exportExcel($request);
        }

        $financials = $this->reportService->getFinancialData($range, $start, $end);
        $summary = $this->reportService->getCashflowData($range, $start, $end);

        return view('reports.finance', compact('financials', 'summary', 'range'));
    }

    public function exportExcel(Request $request)
    {
        $range = $request->input('date_range', '30_days');
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        
        $filename = 'Laporan_Keuangan_Rakayuku_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new FinancialReportExport($range, $start, $end, $this->reportService), $filename);
    }

    public function exportPayments()
    {
        return Excel::download(new PaymentExport(), 'Laporan_Pembayaran_' . now()->format('Y-m-d_His') . '.xlsx');
    }
}
