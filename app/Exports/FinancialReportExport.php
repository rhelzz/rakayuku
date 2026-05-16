<?php

namespace App\Exports;

use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;

class FinancialReportExport implements FromArray, WithStyles, ShouldAutoSize, WithTitle
{
    protected string $range;
    protected ?string $start;
    protected ?string $end;
    protected ReportService $reportService;

    public function __construct(string $range, ?string $start, ?string $end, ReportService $reportService)
    {
        $this->range = $range;
        $this->start = $start;
        $this->end = $end;
        $this->reportService = $reportService;
    }

    public function title(): string
    {
        return 'Laporan Keuangan Terperinci';
    }

    public function array(): array
    {
        $financials = $this->reportService->getFinancialData($this->range, $this->start, $this->end);
        $rows = [];

        $rows[] = ['RAKAYUKU ERP - SISTEM MANAJEMEN PRODUKSI'];
        $rows[] = ['LAPORAN ANALISA KEUANGAN PROYEK'];
        $rows[] = [];
        
        $periodeStr = $this->range === 'custom' 
            ? Carbon::parse($this->start)->format('d/m/Y') . " - " . Carbon::parse($this->end)->format('d/m/Y')
            : strtoupper(str_replace('_', ' ', $this->range));
            
        $rows[] = ['KONFIGURASI LAPORAN'];
        $rows[] = ['Periode Analisa', ': ' . $periodeStr];
        $rows[] = ['Waktu Generasi', ': ' . now()->format('d F Y, H:i')];
        $rows[] = ['Total Proyek', ': ' . $financials->count() . ' Proyek'];
        $rows[] = [];
        $rows[] = []; // Spacer

        foreach ($financials as $data) {
            $order = $data['order'];
            
            $rows[] = ["SEKSI PROYEK: {$order->order_number}"];
            $rows[] = ['Nama Proyek', $order->project_name, '', 'Tanggal Pesanan', $order->created_at->format('d/m/Y')];
            $rows[] = ['Nama Pelanggan', $order->customer->name, '', 'Status Terakhir', strtoupper($order->status)];
            $rows[] = ['Nilai Kontrak', $order->selling_price];
            $rows[] = []; // Spacer

            if ($order->materials->count() > 0) {
                $rows[] = ['RINCIAN PEMAKAIAN BAHAN BAKU'];
                $rows[] = ['NO', 'ITEM BAHAN BAKU', 'KUANTITAS', 'HARGA SATUAN', 'SUBTOTAL (IDR)'];
                foreach ($order->materials as $idx => $om) {
                    $rows[] = [$idx + 1, $om->material->name, $om->qty_used, $om->price_snapshot, $om->subtotal];
                }
                $rows[] = ['', '', '', 'TOTAL MODAL MATERIAL', $data['material_cost']];
                $rows[] = []; // Spacer
            }

            if ($order->productionCosts->count() > 0) {
                $rows[] = ['BIAYA OPERASIONAL & PRODUKSI'];
                $rows[] = ['NO', 'TIPE BIAYA', 'KETERANGAN / NOTES', '', 'JUMLAH (IDR)'];
                foreach ($order->productionCosts as $idx => $pc) {
                    $rows[] = [$idx + 1, strtoupper($pc->type), $pc->description, '', $pc->amount];
                }
                $rows[] = ['', '', '', 'TOTAL BIAYA OPERASIONAL', $data['production_cost']];
                $rows[] = []; // Spacer
            }

            $rows[] = ['ANALISA PROFITABILITAS PROYEK'];
            $rows[] = ['', '', '', 'TOTAL HPP (MODAL KESELURUHAN)', $data['total_hpp']];
            $rows[] = ['', '', '', 'LABA KOTOR (GROSS PROFIT)', $data['profit']];
            $rows[] = ['', '', '', 'MARGIN KEUNTUNGAN', ($data['margin'] / 100)]; // For percentage format
            $rows[] = []; // Double spacer
            $rows[] = [];
        }

        $rows[] = ['RINGKASAN EKSEKUTIF AKHIR PERIODE'];
        $rows[] = ['METRIK UTAMA', 'NILAI TOTAL (IDR)', '', 'PERSENTASE / KETERANGAN'];
        
        $totalRevenue = $financials->sum(fn($f) => $f['order']->selling_price);
        $totalHPP = $financials->sum(fn($f) => $f['total_hpp']);
        $totalProfit = $financials->sum(fn($f) => $f['profit']);
        $avgMargin = $financials->count() > 0 ? $financials->avg('margin') : 0;

        $rows[] = ['Total Pendapatan (Omzet)', $totalRevenue, '', 'Berdasarkan ' . $financials->count() . ' proyek'];
        $rows[] = ['Total Beban Pokok (HPP)', $totalHPP, '', round(($totalHPP / max($totalRevenue, 1)) * 100, 1) . '% dari omzet'];
        $rows[] = ['Total Keuntungan Bersih', $totalProfit, '', round($avgMargin, 1) . '% rata-rata margin'];
        
        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = 'E';
        
        $sheet->getColumnDimension('A')->setWidth(18);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);

        $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->getFont()->setName('Arial')->setSize(10);
        $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('B:E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('E1:E' . $lastRow)->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $sheet->getStyle('D1:D' . $lastRow)->getNumberFormat()->setFormatCode('"Rp "#,##0');

        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(11)->getColor()->setRGB('64748b');
        
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(18)->getColor()->setRGB('0f172a');
        
        $sheet->getStyle('A4')->getFont()->setBold(true)->getColor()->setRGB('1e293b');
        $sheet->getStyle('A5:A7')->getFont()->setBold(true);

        for ($i = 1; $i <= $lastRow; $i++) {
            $firstCell = (string)$sheet->getCell('A' . $i)->getValue();
            
            if (str_starts_with($firstCell, 'SEKSI PROYEK:')) {
                $sheet->mergeCells("A$i:E$i");
                $sheet->getRowDimension($i)->setRowHeight(25);
                $sheet->getStyle("A$i:E$i")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e40af']], // Navy Blue
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);
            }

            $subHeaders = ['RINCIAN PEMAKAIAN BAHAN BAKU', 'BIAYA OPERASIONAL & PRODUKSI', 'ANALISA PROFITABILITAS PROYEK', 'RINGKASAN EKSEKUTIF AKHIR PERIODE'];
            if (in_array($firstCell, $subHeaders)) {
                $sheet->mergeCells("A$i:E$i");
                $sheet->getStyle("A$i:E$i")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '1e293b']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f1f5f9']],
                    'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'cbd5e1']]]
                ]);
            }

            if ($firstCell === 'NO' || $firstCell === 'METRIK UTAMA') {
                $sheet->getStyle("A$i:E$i")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f8fafc']],
                    'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '94a3b8']]]
                ]);
                $sheet->getStyle("A$i:E$i")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            }

            if (in_array($firstCell, ['Nama Proyek', 'Nama Pelanggan', 'Nilai Kontrak', 'Total Pendapatan (Omzet)', 'Total Beban Pokok (HPP)', 'Total Keuntungan Bersih'])) {
                $sheet->getStyle('A' . $i)->getFont()->setBold(true);
                if ($firstCell === 'Nilai Kontrak') {
                    $sheet->getStyle('B' . $i)->getNumberFormat()->setFormatCode('"Rp "#,##0');
                    $sheet->getStyle('B' . $i)->getFont()->setBold(true)->setSize(11);
                }
            }

            $valD = (string)$sheet->getCell('D' . $i)->getValue();
            if (str_starts_with($valD, 'TOTAL') || str_starts_with($valD, 'LABA KOTOR') || str_starts_with($valD, 'MARGIN')) {
                $sheet->getStyle("D$i:E$i")->getFont()->setBold(true);
                $sheet->getStyle("E$i")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
                
                if ($valD === 'MARGIN KEUNTUNGAN') {
                    $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
                    $sheet->getStyle('E' . $i)->getFont()->getColor()->setRGB('059669');
                }
            }
        }

        $summaryStart = 1;
        for ($i = $lastRow; $i > 1; $i--) {
            if ($sheet->getCell('A' . $i)->getValue() === 'RINGKASAN EKSEKUTIF AKHIR PERIODE') {
                $summaryStart = $i;
                break;
            }
        }
        $sheet->getStyle("A{$summaryStart}:E" . ($summaryStart + 4))->applyFromArray([
            'borders' => [
                'outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => '0f172a']]
            ]
        ]);

        return [];
    }
}
