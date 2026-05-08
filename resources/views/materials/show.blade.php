@extends('layouts.app')

@section('title', 'Riwayat Bahan - ' . $material->name . ($material->type ? ' (' . $material->type . ')' : ''))

@section('content')
<div class="space-y-6">
    <!-- Breadcrumbs & Header -->
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('materials.index') }}" class="hover:text-primary transition-colors">Inventaris</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Riwayat Bahan</span>
        </nav>
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-xl bg-surface-container-high flex items-center justify-center text-primary border border-surface-variant">
                    <span class="material-symbols-outlined text-3xl">inventory_2</span>
                </div>
                <div>
                    <h2 class="font-headline-md text-headline-md text-on-surface">{{ $material->name }}{{ $material->type ? ' (' . $material->type . ')' : '' }}</h2>
                    <p class="font-body-sm text-body-sm text-slate-500">ID: {{ $material->id }}</p>
                </div>
            </div>
            
            <div class="flex items-center gap-6 px-6 py-3 bg-surface-container-low border border-surface-variant rounded-xl">
                <div class="text-center">
                    <p class="text-[10px] font-label-caps text-slate-500 uppercase tracking-widest">Stok Saat Ini</p>
                    <p class="text-xl font-bold text-on-surface">{{ formatQty($material->current_qty) }} {{ $material->unit }}</p>
                </div>
                <div class="w-px h-8 bg-surface-variant"></div>
                <div class="text-center">
                    <p class="text-[10px] font-label-caps text-slate-500 uppercase tracking-widest">HPP Rata-rata</p>
                    <p class="text-xl font-bold text-primary">Rp {{ number_format($material->avg_price, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="bg-surface-container-low border border-surface-variant rounded-xl flex flex-col overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-high/50 border-b border-surface-variant">
                    <tr>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Waktu</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-center">Tipe</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Jumlah</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Referensi</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Detail Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-variant/30 font-body-sm text-body-sm text-on-surface bg-white/50">
                    @forelse($movements as $movement)
                    <tr class="hover:bg-surface-container-low transition-colors group">
                        <td class="px-6 py-4 text-slate-500">
                            {{ $movement->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($movement->type == 'IN')
                                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-100 uppercase">Masuk</span>
                            @elseif($movement->type == 'OUT')
                                <span class="px-2.5 py-1 rounded-full bg-red-50 text-error text-[10px] font-bold border border-red-100 uppercase">Keluar</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold border border-slate-200 uppercase">Penyesuaian</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-data-mono font-bold {{ $movement->type == 'IN' ? 'text-emerald-600' : 'text-error' }}">
                            {{ $movement->type == 'IN' ? '+' : '-' }}{{ formatQty($movement->qty) }}
                        </td>
                        <td class="px-6 py-4">
                            @if($movement->reference_type)
                                @php $refName = class_basename($movement->reference_type); @endphp
                                <span class="px-2 py-0.5 bg-surface-container-high rounded text-[10px] text-slate-600 uppercase font-bold border border-surface-variant">
                                    {{ $refName == 'Purchase' ? 'Pembelian' : ($refName == 'Order' ? 'Pesanan' : $refName) }} #{{ $movement->reference_id }}
                                </span>
                            @else
                                <span class="text-slate-400 italic">Manual</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500">
                            @if($movement->reference_type == 'App\Models\Purchase')
                                Restok dari invoice pemasok.
                            @elseif($movement->reference_type == 'App\Models\Order')
                                Digunakan untuk pengerjaan proyek.
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">Belum ada riwayat pergerakan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($movements->hasPages())
            <div class="p-4 border-t border-surface-variant bg-surface-container-lowest/30">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
