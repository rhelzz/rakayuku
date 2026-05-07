<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class PurchaseExport extends BaseExport implements FromArray, WithTitle
{
    public function __construct()
    {
        $purchases = Purchase::with('items.material')->get();

        $this->rows = [
            ['DAFTAR PEMBELIAN/PENGADAAN - RAKAYUKU ERP'],
            ['No', 'Nomor Invoice', 'Tanggal Pembelian', 'Pemasok', 'Jumlah Item', 'Total Biaya (IDR)', 'Bukti Pembayaran', 'Tanggal Terdaftar'],
        ];

        $totalPurchase = 0;

        foreach ($purchases as $index => $purchase) {
            $this->rows[] = [
                $index + 1,
                $purchase->invoice_number ?? 'N/A',
                is_string($purchase->purchase_date) ? $purchase->purchase_date : $purchase->purchase_date->format('d/m/Y'),
                $purchase->supplier_name ?? '-',
                $purchase->items->count(),
                $purchase->total_price,
                $purchase->invoice_proof ? 'Ada' : '-',
                $purchase->created_at->format('d/m/Y H:i'),
            ];

            $totalPurchase += $purchase->total_price;

            if ($purchase->items->count() > 0) {
                foreach ($purchase->items as $item) {
                    $this->rows[] = [
                        '',
                        '  ├─ ' . $item->material->name,
                        '',
                        '',
                        $item->qty . ' ' . $item->material->unit,
                        $item->price,
                        $item->subtotal,
                        '',
                    ];
                }
            }
        }

        $this->rows[] = [];
        $this->rows[] = ['Total Pembelian: ' . $purchases->count() . ' | Total Investasi: ' . number_format($totalPurchase, 0, ',', '.') . ' | Export: ' . Carbon::now()->format('d F Y H:i')];
    }

    public function title(): string
    {
        return 'Data Pembelian';
    }
}
