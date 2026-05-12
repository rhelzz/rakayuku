<?php

namespace App\Exports;

use App\Models\Cashflow;
use App\Models\Material;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Purchase;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class OverallBalanceExport extends BaseExport implements FromArray, WithTitle
{
    protected int $summaryTitleRow = 0;
    protected int $summaryStartRow = 0;
    protected int $summaryEndRow = 0;
    protected int $piutangTitleRow = 0;
    protected int $piutangHeaderRow = 0;
    protected int $piutangDataStartRow = 0;
    protected int $piutangDataEndRow = 0;
    protected int $hutangTitleRow = 0;
    protected int $hutangHeaderRow = 0;
    protected int $hutangDataStartRow = 0;
    protected int $hutangDataEndRow = 0;
    protected int $footerRow = 0;
    protected bool $hasPiutangData = false;
    protected bool $hasHutangData = false;

    public function __construct($startDate = null, $endDate = null)
    {
        $paymentsQuery = Payment::query();
        if ($startDate && $endDate) {
            $paymentsQuery->whereBetween('payment_date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $paymentsQuery->where('payment_date', '>=', $startDate);
        } elseif ($endDate) {
            $paymentsQuery->where('payment_date', '<=', $endDate);
        }
        $payments = $paymentsQuery->get();

        $ordersQuery = Order::with('customer')
            ->where('status', '!=', Order::STATUS_CANCELLED);
        $orders = $ordersQuery->get()
            ->filter(fn($order) => $order->remaining_payment > 0)
            ->values();

        $purchasesQuery = Purchase::query()->where('payment_status', '!=', Purchase::PAYMENT_PAID);
        $purchases = $purchasesQuery->get();

        $totalOmset = (float) $payments->sum('amount');
        $totalPiutang = (float) $orders->sum(fn($o) => $o->remaining_payment);
        $totalHutang = (float) $purchases->sum(fn($p) => $p->total_price - $p->paid_amount);

        $stockValue = (float) Material::all()->sum(fn($m) => $m->current_qty * $m->avg_price);
        $totalWip = (float) Order::query()
            ->whereIn('status', [
                Order::STATUS_PENDING,
                Order::STATUS_IN_PRODUCTION,
                Order::STATUS_DELIVERING,
            ], 'and', false)
            ->sum('total_cost');
        $totalInventaris = $stockValue + $totalWip;
        $cashInHand = (float) Cashflow::currentBalance();

        $saldoNett = ($cashInHand + $totalPiutang + $totalInventaris) - $totalHutang;

        $periodeLabel = 'SEMUA PERIODE';
        if ($startDate && $endDate) {
            $periodeLabel = Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y');
        } elseif ($startDate) {
            $periodeLabel = 'Mulai ' . Carbon::parse($startDate)->format('d/m/Y');
        } elseif ($endDate) {
            $periodeLabel = 'Sampai ' . Carbon::parse($endDate)->format('d/m/Y');
        }

        $this->rows = [];
        $row = 1;

        $this->rows[] = ['RINGKASAN OVERALL KEUANGAN - RAKAYUKU ERP'];
        $row++;
        $this->rows[] = ['Periode', $periodeLabel];
        $row++;
        $this->rows[] = ['Waktu Export', Carbon::now()->format('d F Y H:i')];
        $row++;
        $this->rows[] = [''];
        $row++;

        $this->summaryTitleRow = $row;
        $this->rows[] = ['RINGKASAN OVERALL'];
        $row++;
        $this->summaryStartRow = $row;
        $this->rows[] = ['Omset (Kas Masuk)', $totalOmset];
        $row++;
        $this->rows[] = ['Kas Saat Ini', $cashInHand];
        $row++;
        $this->rows[] = ['Total Inventaris', $totalInventaris];
        $row++;
        $this->rows[] = ['Total Piutang', $totalPiutang];
        $row++;
        $this->rows[] = ['Total Hutang', $totalHutang];
        $row++;
        $this->rows[] = ['Nett Saldo (Kas + Piutang + Inventaris - Hutang)', $saldoNett];
        $this->summaryEndRow = $row;
        $row++;

        $this->rows[] = [''];
        $row++;

        $this->piutangTitleRow = $row;
        $this->rows[] = ['DAFTAR PIUTANG'];
        $row++;
        $this->piutangHeaderRow = $row;
        $this->rows[] = ['No', 'Nomor Pesanan', 'Nama Proyek', 'Nama Pelanggan', 'Tanggal Pesanan', 'Harga Jual (IDR)', 'Sudah Dibayar (IDR)', 'Sisa Piutang (IDR)'];
        $row++;
        $this->piutangDataStartRow = $row;

        $this->hasPiutangData = !$orders->isEmpty();
        if (!$this->hasPiutangData) {
            $this->rows[] = ['Tidak ada piutang pada periode ini.'];
            $row++;
        } else {
            foreach ($orders as $index => $order) {
                $this->rows[] = [
                    $index + 1,
                    (string) ($order->order_number ?? '-'),
                    (string) ($order->project_name ?? '-'),
                    (string) ($order->customer->name ?? '-'),
                    $order->created_at->format('d/m/Y'),
                    $order->selling_price,
                    $order->total_paid,
                    $order->remaining_payment,
                ];
                $row++;
            }
        }
        $this->piutangDataEndRow = max($this->piutangDataStartRow, $row - 1);

        $this->rows[] = [''];
        $row++;

        $this->hutangTitleRow = $row;
        $this->rows[] = ['DAFTAR HUTANG'];
        $row++;
        $this->hutangHeaderRow = $row;
        $this->rows[] = ['No', 'Supplier', 'Nomor Invoice', 'Tanggal Pembelian', 'Total Tagihan (IDR)', 'Telah Dibayar (IDR)', 'Sisa Hutang (IDR)'];
        $row++;
        $this->hutangDataStartRow = $row;

        $this->hasHutangData = !$purchases->isEmpty();
        if (!$this->hasHutangData) {
            $this->rows[] = ['Tidak ada hutang pada periode ini.'];
            $row++;
        } else {
            foreach ($purchases as $index => $purchase) {
                $this->rows[] = [
                    $index + 1,
                    $purchase->supplier_name ?? 'Supplier Umum',
                    $purchase->invoice_number ?? '-',
                    is_string($purchase->purchase_date) ? $purchase->purchase_date : $purchase->purchase_date->format('d/m/Y'),
                    $purchase->total_price,
                    $purchase->paid_amount,
                    $purchase->total_price - $purchase->paid_amount,
                ];
                $row++;
            }
        }
        $this->hutangDataEndRow = max($this->hutangDataStartRow, $row - 1);

        $this->rows[] = [''];
        $row++;

        $this->footerRow = $row;
        $this->rows[] = [
            'Total Omset: ' . number_format($totalOmset, 0, ',', '.') .
            ' | Total Piutang: ' . number_format($totalPiutang, 0, ',', '.') .
            ' | Total Hutang: ' . number_format($totalHutang, 0, ',', '.') .
            ' | Nett Saldo: ' . number_format($saldoNett, 0, ',', '.') .
            ' | Export: ' . Carbon::now()->format('d F Y H:i')
        ];
    }

    public function title(): string
    {
        return 'Ringkasan Overall';
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): array
    {
        $styles = [];
        if (empty($this->rows)) {
            return $styles;
        }

        $columnCount = 0;
        foreach ($this->rows as $row) {
            if (is_array($row) && count($row) > $columnCount) {
                $columnCount = count($row);
            }
        }
        if ($columnCount === 0) {
            return $styles;
        }

        $lastColumn = $this->getColumnLetter($columnCount);

        $titleStyle = [
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F2937']],
            'font' => ['bold' => true, 'size' => 14, 'name' => 'Calibri', 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ];

        $sectionStyle = [
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '065F46']],
            'font' => ['bold' => true, 'size' => 11, 'name' => 'Calibri', 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'border' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ];

        $headerStyle = [
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '065F46']],
            'font' => ['bold' => true, 'size' => 11, 'name' => 'Calibri', 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'border' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ];

        $dataStyle = [
            'font' => ['size' => 10, 'name' => 'Calibri'],
            'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'border' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
        ];

        $alternateRowStyle = [
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDF4']],
            'font' => ['size' => 10, 'name' => 'Calibri'],
            'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'border' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
        ];

        $footerStyle = [
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
            'font' => ['bold' => true, 'italic' => true, 'size' => 10, 'name' => 'Calibri', 'color' => ['rgb' => '374151']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'border' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ];

        $sheet->mergeCells('A1:' . $lastColumn . '1');
        $styles['A1:' . $lastColumn . '1'] = $titleStyle;
        $sheet->getRowDimension(1)->setRowHeight(24);

        if ($this->summaryTitleRow > 0) {
            $sheet->mergeCells('A' . $this->summaryTitleRow . ':' . $lastColumn . $this->summaryTitleRow);
            $styles['A' . $this->summaryTitleRow . ':' . $lastColumn . $this->summaryTitleRow] = $sectionStyle;
        }

        if ($this->piutangTitleRow > 0) {
            $sheet->mergeCells('A' . $this->piutangTitleRow . ':' . $lastColumn . $this->piutangTitleRow);
            $styles['A' . $this->piutangTitleRow . ':' . $lastColumn . $this->piutangTitleRow] = $sectionStyle;
        }

        if ($this->hutangTitleRow > 0) {
            $sheet->mergeCells('A' . $this->hutangTitleRow . ':' . $lastColumn . $this->hutangTitleRow);
            $styles['A' . $this->hutangTitleRow . ':' . $lastColumn . $this->hutangTitleRow] = $sectionStyle;
        }

        if ($this->piutangHeaderRow > 0) {
            $styles['A' . $this->piutangHeaderRow . ':' . $lastColumn . $this->piutangHeaderRow] = $headerStyle;
        }

        if ($this->hutangHeaderRow > 0) {
            $styles['A' . $this->hutangHeaderRow . ':' . $lastColumn . $this->hutangHeaderRow] = $headerStyle;
        }

        if ($this->piutangDataStartRow > 0 && $this->piutangDataEndRow >= $this->piutangDataStartRow) {
            for ($row = $this->piutangDataStartRow; $row <= $this->piutangDataEndRow; $row++) {
                $styles['A' . $row . ':' . $lastColumn . $row] = (($row - $this->piutangDataStartRow) % 2 === 0)
                    ? $dataStyle
                    : $alternateRowStyle;
            }
        }

        if ($this->hutangDataStartRow > 0 && $this->hutangDataEndRow >= $this->hutangDataStartRow) {
            for ($row = $this->hutangDataStartRow; $row <= $this->hutangDataEndRow; $row++) {
                $styles['A' . $row . ':' . $lastColumn . $row] = (($row - $this->hutangDataStartRow) % 2 === 0)
                    ? $dataStyle
                    : $alternateRowStyle;
            }
        }

        if ($this->footerRow > 0) {
            $sheet->mergeCells('A' . $this->footerRow . ':' . $lastColumn . $this->footerRow);
            $styles['A' . $this->footerRow . ':' . $lastColumn . $this->footerRow] = $footerStyle;
        }

        if ($this->summaryStartRow > 0 && $this->summaryEndRow >= $this->summaryStartRow) {
            for ($row = $this->summaryStartRow; $row <= $this->summaryEndRow; $row++) {
                $styles['A' . $row . ':' . $lastColumn . $row] = (($row - $this->summaryStartRow) % 2 === 0)
                    ? $dataStyle
                    : $alternateRowStyle;
            }
        }

        if ($this->summaryStartRow > 0 && $this->summaryEndRow >= $this->summaryStartRow) {
            $sheet->getStyle('B' . $this->summaryStartRow . ':B' . $this->summaryEndRow)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');
        }

        if ($this->piutangDataStartRow > 0 && $this->piutangDataEndRow >= $this->piutangDataStartRow) {
            $sheet->getStyle('F' . $this->piutangDataStartRow . ':H' . $this->piutangDataEndRow)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');
        }

        if ($this->hutangDataStartRow > 0 && $this->hutangDataEndRow >= $this->hutangDataStartRow) {
            $sheet->getStyle('E' . $this->hutangDataStartRow . ':G' . $this->hutangDataEndRow)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');
        }

        if ($this->summaryStartRow > 0 && $this->summaryEndRow >= $this->summaryStartRow) {
            $sheet->getStyle('B' . $this->summaryStartRow . ':B' . $this->summaryEndRow)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }

        if ($this->piutangDataStartRow > 0 && $this->piutangDataEndRow >= $this->piutangDataStartRow) {
            $sheet->getStyle('F' . $this->piutangDataStartRow . ':H' . $this->piutangDataEndRow)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }

        if ($this->hutangDataStartRow > 0 && $this->hutangDataEndRow >= $this->hutangDataStartRow) {
            $sheet->getStyle('E' . $this->hutangDataStartRow . ':G' . $this->hutangDataEndRow)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }

        for ($col = 1; $col <= $columnCount; $col++) {
            $colLetter = $this->getColumnLetter($col);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        if (!$this->hasPiutangData && $this->piutangDataStartRow > 0) {
            $sheet->mergeCells('A' . $this->piutangDataStartRow . ':' . $lastColumn . $this->piutangDataStartRow);
        }

        if (!$this->hasHutangData && $this->hutangDataStartRow > 0) {
            $sheet->mergeCells('A' . $this->hutangDataStartRow . ':' . $lastColumn . $this->hutangDataStartRow);
        }

        if ($this->summaryTitleRow > 0) {
            $sheet->mergeCells('A' . $this->summaryTitleRow . ':' . $lastColumn . $this->summaryTitleRow);
            $styles['A' . $this->summaryTitleRow . ':' . $lastColumn . $this->summaryTitleRow] = $sectionStyle;
        }

        if ($this->piutangTitleRow > 0) {
            $sheet->mergeCells('A' . $this->piutangTitleRow . ':' . $lastColumn . $this->piutangTitleRow);
            $styles['A' . $this->piutangTitleRow . ':' . $lastColumn . $this->piutangTitleRow] = $sectionStyle;
        }

        if ($this->hutangTitleRow > 0) {
            $sheet->mergeCells('A' . $this->hutangTitleRow . ':' . $lastColumn . $this->hutangTitleRow);
            $styles['A' . $this->hutangTitleRow . ':' . $lastColumn . $this->hutangTitleRow] = $sectionStyle;
        }

        if ($this->piutangHeaderRow > 0) {
            $styles['A' . $this->piutangHeaderRow . ':' . $lastColumn . $this->piutangHeaderRow] = $headerStyle;
        }

        if ($this->hutangHeaderRow > 0) {
            $styles['A' . $this->hutangHeaderRow . ':' . $lastColumn . $this->hutangHeaderRow] = $headerStyle;
        }

        return $styles;
    }
}
