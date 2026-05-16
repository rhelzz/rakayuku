@extends('layouts.app')

@section('title', 'Laporan Keuangan & Profitabilitas')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Laporan Keuangan</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-headline-md text-headline-md text-on-surface mb-1">Laporan Keuangan</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Analisa margin keuntungan dan efisiensi biaya proyek.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl font-bold hover:shadow-lg hover:shadow-emerald-500/30 transition-all flex items-center gap-2 shadow-md">
                    <span class="material-symbols-outlined text-[20px]">description</span>
                    Export Excel
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="glass-panel border border-surface-variant rounded-xl p-4">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Income (Masuk)</p>
            <p class="text-2xl font-bold text-emerald-600">{{ formatRupiah($summary['income']) }}</p>
        </div>
        <div class="glass-panel border border-surface-variant rounded-xl p-4">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Outcome (Keluar)</p>
            <p class="text-2xl font-bold text-error">{{ formatRupiah($summary['outcome']) }}</p>
        </div>
        <div class="glass-panel border border-surface-variant rounded-xl p-4">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Net Cashflow</p>
            <p class="text-2xl font-bold {{ $summary['income'] >= $summary['outcome'] ? 'text-primary' : 'text-error' }}">
                {{ formatRupiah($summary['income'] - $summary['outcome']) }}
            </p>
        </div>
    </div>

    <x-table.filter placeholder="Cari laporan proyek..." />

    <div class="bg-surface-container-low border border-surface-variant rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-high/50 border-b border-surface-variant">
                    <tr>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Info Proyek</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Harga Jual</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Biaya Modal (HPP)</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Profit</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-center">Margin</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-variant/30 font-body-sm text-body-sm text-on-surface bg-white/50">
                    @forelse($financials as $data)
                    @php $order = $data['order']; @endphp
                    <tr class="hover:bg-surface-container-low transition-colors group">
                        <td class="px-6 py-4">
                            <div class="font-bold text-primary">{{ $order->order_number }}</div>
                            <div class="font-medium">{{ $order->project_name }}</div>
                            <div class="text-[11px] text-slate-400">{{ $order->customer->name }}</div>
                        </td>
                        <td class="px-6 py-4 text-right font-data-mono font-bold">
                            {{ formatRupiah($order->selling_price) }}
                        </td>
                        <td class="px-6 py-4 text-right font-data-mono text-slate-500">
                            <div title="Material">{{ formatRupiah($data['material_cost']) }}</div>
                            <div class="text-[10px] text-slate-400" title="Operational">+ {{ formatRupiah($data['production_cost']) }}</div>
                        </td>
                        <td class="px-6 py-4 text-right font-data-mono font-bold {{ $data['profit'] >= 0 ? 'text-emerald-600' : 'text-error' }}">
                            {{ formatRupiah($data['profit']) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex flex-col items-center">
                                <span class="px-2.5 py-1 rounded-lg {{ $data['margin'] >= 30 ? 'bg-emerald-50 text-emerald-700' : ($data['margin'] >= 15 ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700') }} font-bold text-[11px]">
                                    {{ round($data['margin'], 1) }}%
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $order->status === 'FINISHED' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-slate-50 text-slate-500 border-slate-200' }}">
                                {{ $order->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">Belum ada data keuangan untuk periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
