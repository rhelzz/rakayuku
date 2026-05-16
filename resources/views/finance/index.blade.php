@extends('layouts.app')

@section('title', 'Master Saldo & Keuangan')

@section('content')
<div class="space-y-6" x-data="{ showExportModal: false }">
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Keuangan</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-headline-md text-headline-md text-on-surface mb-1">Master Saldo</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Ikhtisar posisi keuangan, aset inventaris, dan arus kas perusahaan.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button @click="showExportModal = true" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg font-body-sm font-semibold hover:shadow-lg hover:shadow-emerald-500/30 transition-all flex items-center gap-2 shadow-md">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    <span>Export Excel</span>
                </button>
                <a href="{{ route('finance.cashflow') }}" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg font-body-sm font-semibold hover:bg-surface-container-highest transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                    <span>Buku Kas</span>
                </a>
            </div>
        </div>
    </div>

    <div class="relative overflow-hidden bg-gradient-to-br from-primary to-primary-hover rounded-3xl p-8 shadow-xl shadow-primary/20 text-white">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 blur-3xl rounded-full -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-black/10 blur-2xl rounded-full -ml-10 -mb-10"></div>
        
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <p class="text-primary-container text-sm font-bold uppercase tracking-widest mb-1 opacity-80">Saldo Nett Perusahaan</p>
                    <h1 class="text-4xl md:text-5xl font-black font-data-mono">{{ formatRupiah($saldoNett) }}</h1>
                    <div class="flex items-center gap-2 mt-4 text-xs bg-white/20 w-fit px-3 py-1.5 rounded-full backdrop-blur-md">
                        <span class="material-symbols-outlined text-xs">info</span>
                        <span>(Kas + Piutang) - Hutang</span>
                    </div>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20">
                    <p class="text-xs font-bold opacity-70 uppercase mb-2">Kas Saat Ini</p>
                    <p class="text-2xl font-bold font-data-mono">{{ formatRupiah($cashInHand) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="glass-panel border border-surface-variant rounded-2xl p-6 hover:shadow-lg transition-all group relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <span class="material-symbols-outlined text-6xl text-primary">trending_up</span>
            </div>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">trending_up</span>
                </div>
                <h4 class="font-bold text-slate-700">Saldo Omset</h4>
            </div>
            <p class="text-xs text-slate-500 mb-1 uppercase font-bold tracking-tight">Total Penjualan Masuk</p>
            <p class="text-xl font-bold text-on-surface font-data-mono">{{ formatRupiah($totalOmset) }}</p>
            <div class="mt-4 pt-4 border-t border-slate-100">
                <a href="{{ route('reports.finance') }}" class="text-xs font-bold text-primary flex items-center gap-1 hover:underline">
                    Lihat Laporan <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        </div>

        <div class="glass-panel border border-surface-variant rounded-2xl p-6 hover:shadow-lg transition-all group relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <span class="material-symbols-outlined text-6xl text-amber-600">inventory_2</span>
            </div>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">inventory_2</span>
                </div>
                <h4 class="font-bold text-slate-700">Saldo Inventaris</h4>
            </div>
            <p class="text-xs text-slate-500 mb-1 uppercase font-bold tracking-tight">Nilai Aset Mengendap</p>
            <p class="text-xl font-bold text-on-surface font-data-mono">{{ formatRupiah($totalInventaris) }}</p>
            <div class="mt-2 space-y-1">
                <div class="flex justify-between text-[10px]">
                    <span class="text-slate-500">Stok Bahan:</span>
                    <span class="font-bold text-slate-700">{{ formatRupiah($stockValue) }}</span>
                </div>
                <div class="flex justify-between text-[10px]">
                    <span class="text-slate-500">WIP (Proyek):</span>
                    <span class="font-bold text-slate-700">{{ formatRupiah($totalWIP) }}</span>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100">
                <a href="{{ route('finance.inventory') }}" class="text-xs font-bold text-primary flex items-center gap-1 hover:underline">
                    Rincian Stok <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        </div>

        <div class="glass-panel border border-surface-variant rounded-2xl p-6 hover:shadow-lg transition-all group relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <span class="material-symbols-outlined text-6xl text-emerald-600">call_received</span>
            </div>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">call_received</span>
                </div>
                <h4 class="font-bold text-slate-700">Piutang</h4>
            </div>
            <p class="text-xs text-slate-500 mb-1 uppercase font-bold tracking-tight">Tagihan ke Customer</p>
            <p class="text-xl font-bold text-emerald-600 font-data-mono">{{ formatRupiah($totalPiutang) }}</p>
            <div class="mt-4 pt-4 border-t border-slate-100">
                <a href="{{ route('finance.receivables') }}" class="text-xs font-bold text-primary flex items-center gap-1 hover:underline">
                    Daftar Penagihan <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        </div>

        <div class="glass-panel border border-surface-variant rounded-2xl p-6 hover:shadow-lg transition-all group relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <span class="material-symbols-outlined text-6xl text-error">call_made</span>
            </div>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-error flex items-center justify-center">
                    <span class="material-symbols-outlined">call_made</span>
                </div>
                <h4 class="font-bold text-slate-700">Hutang</h4>
            </div>
            <p class="text-xs text-slate-500 mb-1 uppercase font-bold tracking-tight">Tunggakan ke Supplier</p>
            <p class="text-xl font-bold text-error font-data-mono">{{ formatRupiah($totalHutang) }}</p>
            <div class="mt-4 pt-4 border-t border-slate-100">
                <a href="{{ route('finance.payables') }}" class="text-xs font-bold text-primary flex items-center gap-1 hover:underline">
                    Jadwal Bayar <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>

    <div class="glass-panel border border-surface-variant rounded-2xl overflow-hidden shadow-sm bg-white">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-headline-sm text-on-surface">Arus Kas Terakhir</h3>
            <a href="{{ route('finance.cashflow') }}" class="text-sm text-primary font-bold hover:underline">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-[10px] font-label-caps text-slate-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-[10px] font-label-caps text-slate-500 uppercase">Deskripsi</th>
                        <th class="px-6 py-3 text-[10px] font-label-caps text-slate-500 uppercase text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse(\App\Models\Cashflow::latest()->take(5)->get() as $flow)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 text-sm text-slate-600 font-data-mono">{{ $flow->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full {{ $flow->type == 'IN' ? 'bg-emerald-500' : ($flow->type == 'OUT' ? 'bg-error' : 'bg-blue-500') }}"></span>
                                <span class="text-sm font-medium text-slate-700">{{ $flow->description }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right font-data-mono font-bold {{ $flow->type == 'IN' ? 'text-emerald-600' : ($flow->type == 'OUT' ? 'text-error' : 'text-slate-700') }}">
                            {{ $flow->type == 'OUT' ? '-' : ($flow->type == 'IN' ? '+' : '') }} {{ formatRupiah($flow->amount) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-slate-400 italic">Belum ada mutasi kas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="showExportModal" class="fixed z-[100]" style="display: none; top: 0; right: 0; bottom: 0; left: 0;" x-cloak>
        <div x-show="showExportModal" x-transition.opacity class="absolute bg-slate-900/50 backdrop-blur-sm" style="top: 0; right: 0; bottom: 0; left: 0;" @click="showExportModal = false"></div>
        <div x-show="showExportModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="absolute top-1/2 left-1/2 bg-white rounded-2xl shadow-xl overflow-hidden"
             style="width: 100%; max-width: 28rem; transform: translate(-50%, -50%);">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-surface-container-low">
                <h3 class="font-headline-sm text-on-surface flex items-center gap-2"><span class="material-symbols-outlined text-emerald-600">file_download</span> Export Overall</h3>
                <button @click="showExportModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="{{ route('finance.export.overall') }}" method="GET" class="p-6 space-y-5">
                <p class="text-sm text-slate-500 font-body-sm">Pilih rentang waktu untuk data yang ingin di-export. Biarkan kosong untuk export semua data.</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-label-caps text-slate-500 mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm font-data-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-label-caps text-slate-500 mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm font-data-mono">
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 mt-6">
                    <button type="button" @click="showExportModal = false" class="px-5 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-bold hover:bg-slate-200 transition-colors text-sm">Batal</button>
                    <button type="submit" @click="setTimeout(() => showExportModal = false, 500)" class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 transition-colors shadow-md shadow-emerald-500/20 flex items-center gap-2 text-sm">
                        <span class="material-symbols-outlined text-[18px]">download</span> Export Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
