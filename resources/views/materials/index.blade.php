@extends('layouts.app')

@section('title', 'Inventaris & Bahan Baku')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Inventaris</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-headline-md text-headline-md text-on-surface mb-1">Inventaris & Bahan Baku</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Kelola katalog bahan baku dan pantau ketersediaan stok fisik.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('inventory.movements') }}" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg font-body-sm font-semibold hover:bg-surface-container-highest transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">history</span>
                    <span>Log Transaksi</span>
                </a>
                <a href="{{ route('materials.export') }}" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg font-body-sm font-semibold hover:shadow-lg hover:shadow-emerald-500/30 transition-all flex items-center gap-2 shadow-md">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    <span>Export Excel</span>
                </a>
                <a href="{{ route('materials.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg font-body-sm font-semibold hover:bg-primary-hover transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span>Registrasi Bahan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Table Filter -->
    <x-table.filter placeholder="Cari nama bahan..." />

    <!-- Content Area -->
    <div class="flex flex-col gap-6">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="glass-panel border border-surface-variant rounded-xl p-4 flex items-center space-x-4">
                <div class="h-10 w-10 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600">
                    <span class="material-symbols-outlined">inventory</span>
                </div>
                <div>
                    <p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Total Item</p>
                    <p class="font-title-sm text-title-sm text-on-surface mt-0.5">{{ $materials->total() }}</p>
                </div>
            </div>
            <div class="glass-panel border border-surface-variant rounded-xl p-4 flex items-center space-x-4">
                <div class="h-10 w-10 rounded-lg bg-orange-50 border border-orange-200 flex items-center justify-center text-orange-600">
                    <span class="material-symbols-outlined">warning</span>
                </div>
                <div>
                    <p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Stok Rendah</p>
                    <p class="font-title-sm text-title-sm text-orange-600 mt-0.5">{{ \App\Models\Material::where('current_qty', '<', 5)->count() }} Item</p>
                </div>
            </div>
            <div class="glass-panel border border-surface-variant rounded-xl p-4 flex items-center space-x-4">
                <div class="h-10 w-10 rounded-lg bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600">
                    <span class="material-symbols-outlined">payments</span>
                </div>
                <div>
                    <p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Nilai Inventaris</p>
                    <p class="font-title-sm text-title-sm text-on-surface mt-0.5">{{ formatRupiah($materials->sum(fn($m) => $m->current_qty * $m->avg_price)) }}</p>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="glass-panel border border-surface-variant rounded-xl flex flex-col overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-surface-container-high/50 border-b border-surface-variant">
                        <tr>
                            <x-table.header label="Nama Bahan" field="name" />
                            <x-table.header label="Kode Barang" field="code" />
                            <x-table.header label="Satuan" field="unit" />
                            <x-table.header label="Stok Tersedia" field="current_qty" align="right" />
                            <x-table.header label="HPP Rata-rata" field="avg_price" align="right" />
                            <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-center">Status</th>
                            <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right pr-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-variant/30 font-body-sm text-body-sm text-on-surface bg-white/50">
                        @forelse($materials as $material)
                        <tr class="hover:bg-surface-container-low transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded bg-surface-container-high border border-surface-variant flex items-center justify-center text-slate-400 flex-shrink-0">
                                        <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-on-surface">{{ $material->name }}</div>
                                        @if($material->type)<div class="text-slate-500 text-[11px]">Tipe: {{ $material->type }}</div>@endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-data-mono text-data-mono">
                                <div class="flex flex-col gap-1">
                                    <div class="font-semibold text-primary">{{ $material->code }}</div>
                                    <div class="text-[10px] text-slate-400">ID: {{ $material->id }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-medium text-xs">{{ $material->unit }}</span>
                            </td>
                            <td class="px-6 py-4 text-right font-data-mono text-data-mono @if($material->current_qty < 5) text-error font-bold @endif">
                                {{ number_format($material->current_qty, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-data-mono text-data-mono">
                                {{ formatRupiah($material->avg_price) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($material->current_qty >= 5)
                                <div class="inline-flex items-center space-x-1.5 px-2 py-0.5 rounded-full bg-green-50 text-green-700 border border-green-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    <span class="text-[11px] font-medium">Optimal</span>
                                </div>
                                @else
                                <div class="inline-flex items-center space-x-1.5 px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span class="text-[11px] font-medium">Stok Rendah</span>
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right pr-6">
                                <a href="{{ route('materials.edit', $material) }}" class="text-slate-400 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="material-symbols-outlined text-4xl opacity-20">inventory_2</span>
                                    Belum ada bahan baku yang terdaftar.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($materials->hasPages())
                <div class="p-4 border-t border-surface-variant bg-surface-container-lowest/50">
                    {{ $materials->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
