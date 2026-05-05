<?php

namespace App\Exports;

use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class FinancialReportExport implements FromArray, WithStyles, ShouldAutoSize
{
    protected $range;
    protected $start;
    protected $end;
    protected $reportService;

    public function __construct($range, $start, $end, ReportService $reportService)
    {
        $this->range = $range;
        $this->start = $start;
        $this->end = $end;
        $this->reportService = $reportService;
    }

    public function array(): array
    {
        $financials = $this->reportService->getFinancialData($this->range, $this->start, $this->end);
        $rows = [];

        // Title Rows
        $rows[] = ['LAPORAN KEUANGAN PROYEK TERPERINCI - RAKAYUKU ERP'];
        $rows[] = ['Periode:', $this->range === 'custom' ? "{$this->start} s/d {$this->end}" : strtoupper(str_replace('_', ' ', $this->range))];
        $rows[] = ['Dicetak Pada:', now()->format('d/m/Y H:i')];
        $rows[] = []; // Spacer

        foreach ($financials as $data) {
            $order = $data['order'];
            
            // Project Header Row
            $rows[] = ["PROYEK: {$order->order_number} - {$order->project_name}"];
            
            // Basic Info Rows
            $rows[] = ['Pelanggan:', $order->customer->name, '', 'Tanggal:', $order->created_at->format('d/m/Y')];
            $rows[] = ['Status:', $order->status, '', 'Harga Jual:', $order->selling_price];
            
            // Materials Header
            if ($order->materials->count() > 0) {
                $rows[] = ['Daftar Penggunaan Bahan Baku'];
                $rows[] = ['', 'Nama Bahan', 'Qty', 'Harga Satuan', 'Subtotal'];
                foreach ($order->materials as $om) {
                    $rows[] = ['', $om->material->name, $om->qty_used, $om->price_snapshot, $om->subtotal];
                }
                $rows[] = ['', '', '', 'Total Modal Bahan:', $data['material_cost']];
            }

            // Production Costs Header
            if ($order->productionCosts->count() > 0) {
                $rows[] = ['Biaya Produksi & Operasional'];
                $rows[] = ['', 'Tipe Biaya', 'Keterangan', '', 'Jumlah'];
                foreach ($order->productionCosts as $pc) {
                    $rows[] = ['', $pc->type, $pc->description, '', $pc->amount];
                }
                $rows[] = ['', '', '', 'Total Biaya Produksi:', $data['production_cost']];
            }

            // Project Profit Summary
            $rows[] = ['', '', '', 'TOTAL HPP (Modal):', $data['total_hpp']];
            $rows[] = ['', '', '', 'UNTUNG BERSIH (PROFIT):', $data['profit']];
            $rows[] = ['', '', '', 'MARGIN (%):', round($data['margin'], 2) . '%'];
            $rows[] = []; // Spacer between projects
            $rows[] = []; // Double spacer
        }

        // Final Global Summary
        $rows[] = ['RINGKASAN AKHIR PERIODE'];
        $rows[] = ['', '', '', 'TOTAL PENDAPATAN:', $financials->sum(fn($f) => $f['order']->selling_price)];
        $rows[] = ['', '', '', 'TOTAL MODAL (HPP):', $financials->sum(fn($f) => $f['total_hpp'])];
        $rows[] = ['', '', '', 'TOTAL KEUNTUNGAN:', $financials->sum(fn($f) => $f['profit'])];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        
        // Set column widths for better layout
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(20);

        // Global Number Formatting (Column E for amounts)
        $sheet->getStyle('E1:E' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');

        // Style the Title
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Apply specific styles as we iterate (re-detect rows for styling)
        for ($i = 1; $i <= $lastRow; $i++) {
            $firstCell = $sheet->getCell('A' . $i)->getValue();
            
            // Project Title Bars
            if (str_starts_with((string)$firstCell, 'PROYEK:')) {
                $sheet->mergeCells("A$i:E$i");
                $sheet->getStyle("A$i:E$i")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']]
                ]);
            }

            // Subsection Headers
            if ($firstCell === 'Daftar Penggunaan Bahan Baku' || $firstCell === 'Biaya Produksi & Operasional') {
                $sheet->mergeCells("A$i:E$i");
                $sheet->getStyle("A$i:E$i")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']]
                ]);
            }

            // Final Summary Bar
            if ($firstCell === 'RINGKASAN AKHIR PERIODE') {
                $sheet->mergeCells("A$i:E$i");
                $sheet->getStyle("A$i:E$i")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '111827']]
                ]);
            }
        }

        return [];
    }
}
