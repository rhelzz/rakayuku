<?php

namespace App\Exports;

use App\Models\MonthlyClosing;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class ClosingExport extends BaseExport implements FromArray, WithTitle
{
    public function __construct(MonthlyClosing $closing)
    {
        $snapshot = $closing->snapshot ?? [];

        $this->rows = [
            ['LAPORAN TUTUP BUKU - ' . strtoupper($closing->period_label) . ' - RAKAYUKU ERP'],
            ['Item', 'Nilai (IDR)', 'Keterangan'],
        ];

        $this->rows[] = ['Periode', $closing->period->format('F Y'), 'Bulan tutup buku'];
        $this->rows[] = ['Status', $closing->status, $closing->closed_at ? 'Ditutup: ' . $closing->closed_at->format('d/m/Y H:i') : '-'];
        $this->rows[] = ['Ditutup Oleh', $closing->closed_by ?? '-', ''];
        $this->rows[] = ['', '', ''];
        $this->rows[] = ['--- RINGKASAN KEUANGAN ---', '', ''];
        $this->rows[] = ['Total Omset (Pendapatan)', $snapshot['total_revenue'] ?? 0, 'Pembayaran masuk dari customer bulan ini'];
        $this->rows[] = ['Total Pembelian', $snapshot['total_purchases'] ?? 0, 'Nilai pembelian bahan baku bulan ini'];
        $this->rows[] = ['Total Pengeluaran Kas', $snapshot['total_expenses'] ?? 0, 'Uang keluar dari buku kas bulan ini'];
        $this->rows[] = ['Total Pemasukan Kas', $snapshot['total_cash_income'] ?? 0, 'Uang masuk ke buku kas bulan ini'];
        $this->rows[] = ['', '', ''];
        $this->rows[] = ['--- POSISI SALDO AKHIR ---', '', ''];
        $this->rows[] = ['Saldo Kas', $snapshot['cash_balance'] ?? 0, 'Saldo kas akhir periode'];
        $this->rows[] = ['Nilai Inventaris', $snapshot['inventory_value'] ?? 0, 'Nilai stok bahan baku'];
        $this->rows[] = ['Piutang', $snapshot['receivables'] ?? 0, 'Tagihan ke customer'];
        $this->rows[] = ['Hutang', $snapshot['payables'] ?? 0, 'Tunggakan ke supplier'];
        $this->rows[] = ['Saldo Nett', $snapshot['net_balance'] ?? 0, '(Kas + Piutang) - Hutang'];
        $this->rows[] = ['', '', ''];
        $this->rows[] = ['--- VOLUME TRANSAKSI ---', '', ''];
        $this->rows[] = ['Jumlah Pesanan', $snapshot['order_count'] ?? 0, 'Total order masuk bulan ini'];
        $this->rows[] = ['Jumlah Pembelian', $snapshot['purchase_count'] ?? 0, 'Total purchase order bulan ini'];

        $this->rows[] = [];
        $this->rows[] = [
            'Catatan: ' . ($closing->notes ?? '-'),
            '',
            'Export: ' . Carbon::now()->format('d F Y H:i'),
        ];
    }

    public function title(): string
    {
        return 'Tutup Buku';
    }
}
