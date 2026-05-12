@extends('layouts.app')

@section('title', 'Rincian Saldo Inventaris')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('finance.index') }}" class="hover:text-primary transition-colors">Keuangan</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Detail Inventaris</span>
        </nav>

        <div class="flex justify-between items-end">
            <div>
                <h2 class="font-headline-md text-headline-md text-on-surface mb-1">Nilai Inventaris</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Aset berupa stok bahan baku yang tersedia di gudang.</p>
            </div>
            <div class="text-right">
                <p class="text-xs font-bold text-slate-500 uppercase">Total Nilai Aset</p>
                <p class="text-3xl font-black text-primary font-data-mono">{{ formatRupiah($materials->sum(fn($m) => $m->current_qty * $m->avg_price)) }}</p>
            </div>
        </div>
    </div>

    <div class="glass-panel border border-surface-variant rounded-2xl overflow-hidden bg-white shadow-sm">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 text-[10px] font-label-caps text-slate-500 uppercase">Material</th>
                    <th class="px-6 py-4 text-[10px] font-label-caps text-slate-500 uppercase text-right">Stok</th>
                    <th class="px-6 py-4 text-[10px] font-label-caps text-slate-500 uppercase text-right">HPP Avg</th>
                    <th class="px-6 py-4 text-[10px] font-label-caps text-slate-500 uppercase text-right">Subtotal Nilai</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($materials as $material)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-700">{{ $material->name }}</div>
                        <div class="text-[10px] text-slate-400 font-data-mono">{{ $material->code }} @if($material->is_dimension) | {{ $material->dimension_string }} @endif</div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="font-data-mono font-medium text-slate-600">{{ formatQty($material->current_qty) }}</span>
                        <span class="text-[10px] text-slate-400">{{ $material->unit }}</span>
                    </td>
                    <td class="px-6 py-4 text-right font-data-mono text-slate-600 text-sm">
                        {{ formatRupiah($material->avg_price) }}
                    </td>
                    <td class="px-6 py-4 text-right font-data-mono font-bold text-on-surface">
                        {{ formatRupiah($material->current_qty * $material->avg_price) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
