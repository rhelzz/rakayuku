@extends('layouts.app')

@section('title', 'Ringkasan Tutup Buku - ' . $closing->period_label)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('closing.index') }}" class="hover:text-primary transition-colors">Tutup Buku</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">{{ $closing->period_label }}</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-xl bg-green-50 flex items-center justify-center text-green-600 border border-green-200">
                    <span class="material-symbols-outlined text-3xl">lock</span>
                </div>
                <div>
                    <h2 class="font-headline-md text-headline-md text-on-surface">{{ $closing->period_label }}</h2>
                    <div class="flex items-center gap-2 flex-wrap mt-1">
                        <span class="px-2.5 py-1 rounded-full bg-green-50 text-green-700 text-[11px] font-bold border border-green-200 uppercase">{{ $closing->status }}</span>
                        @if($closing->closed_at)
                            <span class="text-slate-300">•</span>
                            <span class="text-xs text-slate-500">Ditutup {{ $closing->closed_at->format('d/m/Y H:i') }}</span>
                        @endif
                        @if($closing->closed_by)
                            <span class="text-slate-300">•</span>
                            <span class="text-xs text-slate-500">oleh {{ $closing->closed_by }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('closing.export', $closing) }}" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg font-body-sm font-semibold hover:shadow-lg hover:shadow-emerald-500/30 transition-all flex items-center gap-2 shadow-md">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    <span>Export Excel</span>
                </a>
                <a href="{{ route('closing.index') }}" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg font-body-sm font-semibold hover:bg-surface-container-highest transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    @php $snapshot = $closing->snapshot ?? []; @endphp

    <div class="relative overflow-hidden bg-gradient-to-br from-primary to-primary-hover rounded-3xl p-8 shadow-xl shadow-primary/20 text-white">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 blur-3xl rounded-full -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-black/10 blur-2xl rounded-full -ml-10 -mb-10"></div>
        
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <p class="text-primary-container text-sm font-bold uppercase tracking-widest mb-1 opacity-80">Saldo Nett Akhir Periode</p>
                    <h1 class="text-4xl md:text-5xl font-black font-data-mono">{{ formatRupiah($snapshot['net_balance'] ?? 0) }}</h1>
                    <div class="flex items-center gap-2 mt-4 text-xs bg-white/20 w-fit px-3 py-1.5 rounded-full backdrop-blur-md">
                        <span class="material-symbols-outlined text-xs">info</span>
                        <span>(Kas + Piutang) - Hutang</span>
                    </div>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20">
                    <p class="text-xs font-bold opacity-70 uppercase mb-2">Saldo Kas</p>
                    <p class="text-2xl font-bold font-data-mono">{{ formatRupiah($snapshot['cash_balance'] ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="glass-panel border border-surface-variant rounded-2xl p-6 relative overflow-hidden">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">trending_up</span>
                </div>
                <h4 class="font-bold text-slate-700">Omset Bulan Ini</h4>
            </div>
            <p class="text-2xl font-bold text-on-surface font-data-mono">{{ formatRupiah($snapshot['total_revenue'] ?? 0) }}</p>
            <p class="text-xs text-slate-500 mt-1">Pembayaran masuk dari customer</p>
        </div>

        <div class="glass-panel border border-surface-variant rounded-2xl p-6 relative overflow-hidden">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">shopping_bag</span>
                </div>
                <h4 class="font-bold text-slate-700">Total Pembelian</h4>
            </div>
            <p class="text-2xl font-bold text-on-surface font-data-mono">{{ formatRupiah($snapshot['total_purchases'] ?? 0) }}</p>
            <p class="text-xs text-slate-500 mt-1">Pembelian bahan baku bulan ini</p>
        </div>

        <div class="glass-panel border border-surface-variant rounded-2xl p-6 relative overflow-hidden">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">inventory_2</span>
                </div>
                <h4 class="font-bold text-slate-700">Nilai Inventaris</h4>
            </div>
            <p class="text-2xl font-bold text-on-surface font-data-mono">{{ formatRupiah($snapshot['inventory_value'] ?? 0) }}</p>
            <p class="text-xs text-slate-500 mt-1">Nilai stok bahan baku akhir periode</p>
        </div>

        <div class="glass-panel border border-surface-variant rounded-2xl p-6 relative overflow-hidden">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">call_received</span>
                </div>
                <h4 class="font-bold text-slate-700">Piutang</h4>
            </div>
            <p class="text-2xl font-bold text-emerald-600 font-data-mono">{{ formatRupiah($snapshot['receivables'] ?? 0) }}</p>
            <p class="text-xs text-slate-500 mt-1">Tagihan ke customer</p>
        </div>

        <div class="glass-panel border border-surface-variant rounded-2xl p-6 relative overflow-hidden">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-error flex items-center justify-center">
                    <span class="material-symbols-outlined">call_made</span>
                </div>
                <h4 class="font-bold text-slate-700">Hutang</h4>
            </div>
            <p class="text-2xl font-bold text-error font-data-mono">{{ formatRupiah($snapshot['payables'] ?? 0) }}</p>
            <p class="text-xs text-slate-500 mt-1">Tunggakan ke supplier</p>
        </div>

        <div class="glass-panel border border-surface-variant rounded-2xl p-6 relative overflow-hidden">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">bar_chart</span>
                </div>
                <h4 class="font-bold text-slate-700">Volume Transaksi</h4>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500">Pesanan:</span>
                    <span class="font-bold text-on-surface font-data-mono">{{ $snapshot['order_count'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500">Pembelian:</span>
                    <span class="font-bold text-on-surface font-data-mono">{{ $snapshot['purchase_count'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="glass-panel border border-surface-variant rounded-2xl p-6">
        <h3 class="font-headline-sm text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">account_balance_wallet</span>
            Arus Kas Periode Ini
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                <p class="text-xs font-bold text-emerald-600 uppercase mb-1">Total Pemasukan Kas</p>
                <p class="text-xl font-bold text-emerald-700 font-data-mono">{{ formatRupiah($snapshot['total_cash_income'] ?? 0) }}</p>
            </div>
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
                <p class="text-xs font-bold text-error uppercase mb-1">Total Pengeluaran Kas</p>
                <p class="text-xl font-bold text-error font-data-mono">{{ formatRupiah($snapshot['total_expenses'] ?? 0) }}</p>
            </div>
        </div>
    </div>

    @if($closing->notes)
    <div class="glass-panel border border-surface-variant rounded-2xl p-6">
        <h3 class="font-headline-sm text-on-surface mb-2 flex items-center gap-2">
            <span class="material-symbols-outlined text-slate-400">notes</span>
            Catatan
        </h3>
        <p class="text-sm text-slate-600">{{ $closing->notes }}</p>
    </div>
    @endif
</div>
@endsection
