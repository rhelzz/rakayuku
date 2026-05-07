<?php

namespace App\Exports;

use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class StockMovementExport extends BaseExport implements FromArray, WithTitle
{
    public function __construct()
    {
        $movements = StockMovement::with('material')->get();

        $this->rows = [
            ['LAPORAN PERGERAKAN STOK - RAKAYUKU ERP'],
            ['No', 'Tanggal', 'Bahan Baku', 'Tipe Pergerakan', 'Kuantitas', 'Harga Satuan (IDR)', 'Referensi Type', 'Referensi ID', 'Waktu Terdaftar'],
        ];

        foreach ($movements as $index => $movement) {
            $icon = $movement->type === 'in' ? '↑ IN (Masuk)' : '↓ OUT (Keluar)';
            
            $this->rows[] = [
                $index + 1,
                $movement->created_at->format('d/m/Y H:i'),
                $movement->material->name,
                $icon,
                $movement->qty,
                $movement->price_snapshot ?? '-',
                $movement->reference_type ?? '-',
                $movement->reference_id ?? '-',
                $movement->created_at->format('d/m/Y H:i'),
            ];
        }

        $this->rows[] = [];
        $this->rows[] = ['Total Pergerakan: ' . $movements->count() . ' | Export: ' . Carbon::now()->format('d F Y H:i')];
    }

    public function title(): string
    {
        return 'Pergerakan Stok';
    }
}
