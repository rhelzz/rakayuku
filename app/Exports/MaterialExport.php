<?php

namespace App\Exports;

use App\Models\Material;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class MaterialExport extends BaseExport implements FromArray, WithTitle
{
    public function __construct()
    {
        $materials = Material::all();

        $this->rows = [
            ['DAFTAR BAHAN BAKU - RAKAYUKU ERP'],
            ['No', 'Kode Bahan', 'Nama Bahan', 'Tipe', 'Satuan', 'Harga Rata-Rata (IDR)', 'Stok Tersedia', 'Status', 'Tanggal Terdaftar'],
        ];

        foreach ($materials as $index => $material) {
            $status = $material->current_qty >= 50 ? 'Tersedia' : ($material->current_qty >= 10 ? 'Rendah' : ($material->current_qty > 0 ? 'Kritis' : 'Kosong'));
            
            $this->rows[] = [
                $index + 1,
                $material->code,
                $material->name,
                $material->type ?? '-',
                $material->unit,
                $material->avg_price,
                $material->current_qty,
                $status,
                $material->created_at->format('d/m/Y'),
            ];
        }

        $this->rows[] = [];
        $this->rows[] = ['Total Bahan: ' . $materials->count() . ' | Tanggal Export: ' . Carbon::now()->format('d F Y H:i')];
    }

    public function title(): string
    {
        return 'Data Bahan Baku';
    }
}
