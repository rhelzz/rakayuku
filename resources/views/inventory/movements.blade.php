@extends('layouts.app')

@section('title', 'Log Transaksi Stok (Stock Ledger)')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline-md text-headline-md text-on-surface mb-1">Log Transaksi Stok</h2>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Pantau seluruh aliran masuk dan keluar bahan baku.</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('materials.index') }}" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg font-body-sm text-body-sm font-semibold hover:bg-surface-container-highest transition-colors flex items-center space-x-2">
                <span class="material-symbols-outlined text-[18px]">inventory</span>
                <span>Daftar Bahan</span>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="glass-panel border border-surface-variant rounded-xl p-4 mb-6">
        <form action="{{ route('inventory.movements') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="space-y-1.5 flex-1 min-w-[200px]">
                <label class="block font-medium text-slate-700 text-xs uppercase tracking-wider">Filter Bahan</label>
                <select name="material_id" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary appearance-none">
                    <option value="">Semua Bahan</option>
                    @foreach($materials as $m)
                        <option value="{{ $m->id }}" {{ request('material_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="space-y-1.5 w-40">
                <label class="block font-medium text-slate-700 text-xs uppercase tracking-wider">Tipe</label>
                <select name="type" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary appearance-none">
                    <option value="">Semua Tipe</option>
                    <option value="IN" {{ request('type') == 'IN' ? 'selected' : '' }}>MASUK (Beli)</option>
                    <option value="OUT" {{ request('type') == 'OUT' ? 'selected' : '' }}>KELUAR (Produksi)</option>
                    <option value="ADJUSTMENT" {{ request('type') == 'ADJUSTMENT' ? 'selected' : '' }}>KOREKSI</option>
                </select>
            </div>

            <button type="submit" class="px-5 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-primary-hover transition-colors shadow-sm flex items-center space-x-2">
                <span class="material-symbols-outlined text-[18px]">filter_list</span>
                <span>Terapkan</span>
            </button>
            
            @if(request()->anyFilled(['material_id', 'type']))
                <a href="{{ route('inventory.movements') }}" class="px-5 py-2 text-slate-500 hover:text-error transition-colors text-sm font-medium">Reset</a>
            @endif
        </form>
    </div>

    <!-- Ledger Table -->
    <div class="glass-panel border border-surface-variant rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-high/50 border-b border-surface-variant">
                    <tr>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Waktu Transaksi</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Bahan Baku</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-center">Tipe</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Jumlah</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">HPP Saat Itu</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Referensi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-variant/30 font-body-sm text-body-sm text-on-surface bg-white/50">
                    @forelse($movements as $m)
                    <tr class="hover:bg-surface-container-low transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                            {{ $m->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-on-surface">{{ $m->material->name }}</div>
                            <div class="text-[10px] text-slate-400 font-data-mono uppercase">{{ $m->material->unit }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($m->type === 'IN')
                                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-100">MASUK</span>
                            @elseif($m->type === 'OUT')
                                <span class="px-2.5 py-1 rounded-full bg-orange-50 text-orange-700 text-[10px] font-bold border border-orange-100">KELUAR</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold border border-slate-200">KOREKSI</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-data-mono font-bold {{ $m->type === 'IN' ? 'text-emerald-600' : ($m->type === 'OUT' ? 'text-orange-600' : 'text-slate-600') }}">
                            {{ $m->type === 'OUT' ? '-' : ($m->type === 'IN' ? '+' : '') }}{{ number_format(abs($m->qty), 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right font-data-mono text-slate-500">
                            Rp {{ number_format($m->price_snapshot, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($m->reference_type)
                                @php 
                                    $refName = basename(str_replace('\\', '/', $m->reference_type));
                                @endphp
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">{{ $refName }}</span>
                                    @if($refName === 'Purchase')
                                        <a href="{{ route('purchases.show', $m->reference_id) }}" class="text-primary hover:underline font-medium italic">#{{ $m->reference_id }}</a>
                                    @elseif($refName === 'Order')
                                        <a href="{{ route('orders.show', $m->reference_id) }}" class="text-primary hover:underline font-medium italic">#{{ $m->reference_id }}</a>
                                    @else
                                        <span class="text-on-surface-variant font-medium">#{{ $m->reference_id }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-slate-300 italic">Tanpa Referensi</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">Belum ada riwayat transaksi stok.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($movements->hasPages())
            <div class="p-4 border-t border-surface-variant bg-surface-container-lowest/50">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
