@extends('layouts.app')

@section('title', 'Stock Opname')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('materials.index') }}" class="hover:text-primary transition-colors">Inventaris</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Stock Opname</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-headline-md text-headline-md text-on-surface mb-1">Stock Opname</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Penyesuaian stok sistem vs stok fisik (real).</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('stock-opname.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg font-body-sm font-semibold hover:bg-primary-hover transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span>Stock Opname Baru</span>
                </a>
            </div>
        </div>
    </div>

    <x-table.filter placeholder="Cari nomor opname..." />

    <div class="glass-panel border border-surface-variant rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-high/50 border-b border-surface-variant">
                    <tr>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest w-10 text-center">No</th>
                        <x-table.header label="No. Opname" field="opname_number" />
                        <x-table.header label="Tanggal" field="opname_date" />
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-center">Status</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-center">Total Item</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right pr-6">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-variant/30 font-body-sm text-body-sm text-on-surface bg-white/50">
                    @forelse($opnames as $opname)
                    <tr class="hover:bg-surface-container-low transition-colors group">
                        <td class="px-6 py-4 text-center font-data-mono text-slate-400">{{ $opnames->firstItem() + $loop->index }}</td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-primary font-data-mono">{{ $opname->opname_number }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-500 font-data-mono">
                            {{ $opname->opname_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($opname->status === 'COMPLETED')
                                <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-700 border border-green-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    <span class="text-[11px] font-bold uppercase">Selesai</span>
                                </div>
                            @else
                                <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span class="text-[11px] font-bold uppercase">Draft</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center font-data-mono">
                            {{ $opname->items_count }} item
                        </td>
                        <td class="px-6 py-4 text-right pr-6">
                            <a href="{{ route('stock-opname.show', $opname) }}" class="text-slate-400 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">
                            <div class="flex flex-col items-center gap-2">
                                <span class="material-symbols-outlined text-4xl opacity-20">fact_check</span>
                                Belum ada stock opname yang dibuat.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($opnames->hasPages())
            <div class="p-4 border-t border-surface-variant bg-surface-container-lowest/50">
                {{ $opnames->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
