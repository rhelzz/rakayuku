@extends('layouts.app')
@section('title', 'Detail Stock Opname - ' . $stockOpname->opname_number)
@section('content')
<div class="space-y-6" x-data="{ showFinalizeModal: false }">
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('stock-opname.index') }}" class="hover:text-primary transition-colors">Stock Opname</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">{{ $stockOpname->opname_number }}</span>
        </nav>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-xl {{ $stockOpname->isCompleted() ? 'bg-green-50 text-green-600 border-green-200' : 'bg-amber-50 text-amber-600 border-amber-200' }} border flex items-center justify-center">
                    <span class="material-symbols-outlined text-3xl">{{ $stockOpname->isCompleted() ? 'check_circle' : 'pending' }}</span>
                </div>
                <div>
                    <h2 class="font-headline-md text-headline-md text-on-surface">{{ $stockOpname->opname_number }}</h2>
                    <div class="flex items-center gap-2 flex-wrap mt-1">
                        @if($stockOpname->isCompleted())
                            <span class="px-2.5 py-1 rounded-full bg-green-50 text-green-700 text-[11px] font-bold border border-green-200 uppercase">Selesai</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[11px] font-bold border border-amber-200 uppercase">Draft</span>
                        @endif
                        <span class="text-slate-300">&bull;</span>
                        <span class="text-xs text-slate-500">{{ $stockOpname->opname_date->format('d/m/Y') }}</span>
                        @if($stockOpname->completed_at)
                            <span class="text-slate-300">&bull;</span>
                            <span class="text-xs text-slate-500">Difinalisasi {{ $stockOpname->completed_at->format('d/m/Y H:i') }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                @if($stockOpname->isCompleted())
                    <a href="{{ route('stock-opname.export', $stockOpname) }}" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg font-body-sm font-semibold hover:shadow-lg transition-all flex items-center gap-2 shadow-md">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        <span>Export Excel</span>
                    </a>
                @else
                    <button @click="showFinalizeModal = true" type="button" class="px-4 py-2 bg-primary text-white rounded-lg font-body-sm font-semibold hover:bg-primary-hover transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        <span>Finalisasi Opname</span>
                    </button>
                @endif
                <a href="{{ route('stock-opname.index') }}" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg font-body-sm font-semibold hover:bg-surface-container-highest transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span><span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    @if($stockOpname->notes)
    <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl flex items-start gap-2">
        <span class="material-symbols-outlined text-blue-500 text-[18px] mt-0.5">notes</span>
        <p class="text-sm text-blue-700">{{ $stockOpname->notes }}</p>
    </div>
    @endif

    @if($stockOpname->isDraft())
    <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3">
        <span class="material-symbols-outlined text-amber-600 mt-0.5">warning</span>
        <div>
            <p class="text-sm font-semibold text-amber-800">Draft - Belum Difinalisasi</p>
            <p class="text-xs text-amber-700 mt-1">Stock opname ini masih dalam status draft. Klik <strong>"Finalisasi Opname"</strong> untuk menerapkan penyesuaian stok ke sistem.</p>
        </div>
    </div>
    @endif

    @php
        $totalItems = $stockOpname->items->count();
        $matchItems = $stockOpname->items->where('difference', 0)->count();
        $surplusItems = $stockOpname->items->where('difference', '>', 0)->count();
        $deficitItems = $stockOpname->items->where('difference', '<', 0)->count();
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="glass-panel border border-surface-variant rounded-xl p-4 flex items-center space-x-3">
            <div class="h-10 w-10 rounded-lg bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600">
                <span class="material-symbols-outlined">inventory</span>
            </div>
            <div>
                <p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider text-[10px]">Total Item</p>
                <p class="font-title-sm text-title-sm text-on-surface mt-0.5">{{ $totalItems }}</p>
            </div>
        </div>
        <div class="glass-panel border border-surface-variant rounded-xl p-4 flex items-center space-x-3">
            <div class="h-10 w-10 rounded-lg bg-green-50 border border-green-200 flex items-center justify-center text-green-600">
                <span class="material-symbols-outlined">check</span>
            </div>
            <div>
                <p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider text-[10px]">Cocok</p>
                <p class="font-title-sm text-title-sm text-green-600 mt-0.5">{{ $matchItems }}</p>
            </div>
        </div>
        <div class="glass-panel border border-surface-variant rounded-xl p-4 flex items-center space-x-3">
            <div class="h-10 w-10 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined">arrow_upward</span>
            </div>
            <div>
                <p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider text-[10px]">Surplus</p>
                <p class="font-title-sm text-title-sm text-emerald-600 mt-0.5">{{ $surplusItems }}</p>
            </div>
        </div>
        <div class="glass-panel border border-surface-variant rounded-xl p-4 flex items-center space-x-3">
            <div class="h-10 w-10 rounded-lg bg-red-50 border border-red-200 flex items-center justify-center text-error">
                <span class="material-symbols-outlined">arrow_downward</span>
            </div>
            <div>
                <p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider text-[10px]">Defisit</p>
                <p class="font-title-sm text-title-sm text-error mt-0.5">{{ $deficitItems }}</p>
            </div>
        </div>
    </div>

    <div class="glass-panel border border-surface-variant rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-high/50 border-b border-surface-variant">
                    <tr>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest w-10 text-center">No</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Nama Bahan</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Kode</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Satuan</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Stok Sistem</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Stok Aktual</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Selisih</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-variant/30 font-body-sm text-body-sm text-on-surface bg-white/50">
                    @foreach($stockOpname->items as $item)
                    <tr class="hover:bg-surface-container-low transition-colors {{ $item->difference != 0 ? 'bg-amber-50/30' : '' }}">
                        <td class="px-6 py-4 text-center font-data-mono text-slate-400">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-medium">{{ $item->material->display_name }}</td>
                        <td class="px-6 py-4 font-data-mono text-xs text-primary font-semibold">{{ $item->material->code }}</td>
                        <td class="px-6 py-4"><span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 text-xs">{{ $item->material->unit }}</span></td>
                        <td class="px-6 py-4 text-right font-data-mono">{{ formatQty($item->system_qty) }}</td>
                        <td class="px-6 py-4 text-right font-data-mono font-bold">{{ formatQty($item->actual_qty) }}</td>
                        <td class="px-6 py-4 text-right font-data-mono font-bold {{ $item->difference > 0 ? 'text-emerald-600' : ($item->difference < 0 ? 'text-error' : 'text-slate-400') }}">
                            {{ $item->difference > 0 ? '+' : '' }}{{ formatQty($item->difference) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->difference == 0)
                                <span class="px-2 py-0.5 rounded-full bg-green-50 text-green-700 text-[10px] font-bold border border-green-200">COCOK</span>
                            @elseif($item->difference > 0)
                                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">SURPLUS</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-red-50 text-error text-[10px] font-bold border border-red-200">DEFISIT</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($stockOpname->isDraft())
    <div x-show="showFinalizeModal" class="fixed z-[100]" style="display: none; top: 0; right: 0; bottom: 0; left: 0;" x-cloak>
        <div x-show="showFinalizeModal" x-transition.opacity class="absolute bg-slate-900/50 backdrop-blur-sm" style="top: 0; right: 0; bottom: 0; left: 0;" @click="showFinalizeModal = false"></div>
        <div x-show="showFinalizeModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="absolute top-1/2 left-1/2 bg-white rounded-2xl shadow-xl overflow-hidden"
             style="width: 100%; max-width: 28rem; transform: translate(-50%, -50%);">

            <div class="p-6 text-center">
                <div class="mx-auto w-16 h-16 rounded-full bg-emerald-50 border-2 border-emerald-200 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-emerald-500 text-4xl">inventory</span>
                </div>
                <h3 class="text-lg font-bold text-on-surface mb-2">Finalisasi Stock Opname?</h3>
                <p class="text-sm text-slate-500 mb-4">Stok sistem akan disesuaikan dengan hasil opname. Tindakan ini <strong class="text-red-600">tidak bisa dibatalkan</strong>.</p>

                <div class="bg-slate-50 rounded-xl p-4 mb-5 text-left space-y-2">
                    <p class="text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Ringkasan Penyesuaian</p>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Total Item</span>
                        <span class="font-semibold text-on-surface">{{ $stockOpname->items->count() }} bahan</span>
                    </div>
                    @php
                        $surplusCount = $stockOpname->items->where('difference', '>', 0)->count();
                        $deficitCount = $stockOpname->items->where('difference', '<', 0)->count();
                        $matchCount = $stockOpname->items->where('difference', 0)->count();
                    @endphp
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Cocok</span>
                        <span class="font-semibold text-green-600">{{ $matchCount }} item</span>
                    </div>
                    @if($surplusCount > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Surplus (stok naik)</span>
                        <span class="font-semibold text-emerald-600">+{{ $surplusCount }} item</span>
                    </div>
                    @endif
                    @if($deficitCount > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Defisit (stok turun)</span>
                        <span class="font-semibold text-red-600">{{ $deficitCount }} item</span>
                    </div>
                    @endif
                </div>

                <form action="{{ route('stock-opname.complete', $stockOpname) }}" method="POST" x-data="{ submitting: false }" @submit="submitting = true">
                    @csrf
                    <div class="flex gap-3">
                        <button type="button" @click="showFinalizeModal = false" class="flex-1 px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-semibold text-sm hover:bg-slate-200 transition-colors">
                            Batal
                        </button>
                        <button type="submit" :disabled="submitting" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl font-semibold text-sm hover:shadow-lg hover:shadow-emerald-500/30 transition-all shadow-md flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!submitting" class="material-symbols-outlined text-[18px]">check_circle</span>
                            <span x-show="submitting" class="animate-spin material-symbols-outlined text-[18px]">progress_activity</span>
                            <span x-text="submitting ? 'Memproses...' : 'Ya, Finalisasi'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
