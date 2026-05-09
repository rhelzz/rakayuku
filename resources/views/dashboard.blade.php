@extends('layouts.app')

@section('title', 'Ringkasan Dashboard')

@section('content')
<div class="space-y-6" x-data="{ showExportModal: false, exportUrl: '', exportTitle: '' }">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="font-display-lg text-display-lg text-on-background">Ringkasan Dashboard</h2>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Metrik real-time untuk pemrosesan kayu dan inventaris.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-semibold text-slate-600 mr-1">Quick Export:</span>
                <button @click="exportUrl = '{{ route('cashflows.export') }}'; exportTitle = 'Arus Kas'; showExportModal = true" class="px-3 py-1.5 text-xs bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors border border-indigo-200 whitespace-nowrap">Arus Kas</button>
                <button @click="exportUrl = '{{ route('orders.export_receivables') }}'; exportTitle = 'Piutang'; showExportModal = true" class="px-3 py-1.5 text-xs bg-rose-50 text-rose-700 rounded-lg hover:bg-rose-100 transition-colors border border-rose-200 whitespace-nowrap">Piutang</button>
                <button @click="exportUrl = '{{ route('orders.export') }}'; exportTitle = 'Pesanan'; showExportModal = true" class="px-3 py-1.5 text-xs bg-emerald-50 text-emerald-700 rounded-lg hover:bg-emerald-100 transition-colors border border-emerald-200 whitespace-nowrap">Pesanan</button>
                <button @click="exportUrl = '{{ route('customers.export') }}'; exportTitle = 'Pelanggan'; showExportModal = true" class="px-3 py-1.5 text-xs bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors border border-blue-200 whitespace-nowrap">Pelanggan</button>
                <button @click="exportUrl = '{{ route('materials.export') }}'; exportTitle = 'Bahan'; showExportModal = true" class="px-3 py-1.5 text-xs bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors border border-purple-200 whitespace-nowrap">Bahan</button>
                <button @click="exportUrl = '{{ route('purchases.export') }}'; exportTitle = 'Pembelian'; showExportModal = true" class="px-3 py-1.5 text-xs bg-amber-50 text-amber-700 rounded-lg hover:bg-amber-100 transition-colors border border-amber-200 whitespace-nowrap">Pembelian</button>
            </div>
            <a href="{{ route('orders.create') }}" class="px-4 py-2 rounded-lg bg-primary-container text-on-primary-container font-body-sm hover:opacity-90 transition-opacity flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add</span> Pesanan Baru
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="glass-panel rounded-xl p-4 flex flex-col gap-2 relative overflow-hidden group border-l-4 border-l-primary">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-symbols-outlined text-6xl text-primary">account_balance_wallet</span>
            </div>
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Saldo Perusahaan</span>
            <div class="flex flex-col mt-2 overflow-hidden">
                <span class="text-xl lg:text-2xl font-bold tracking-tight text-on-background whitespace-nowrap" title="{{ formatRupiah($currentBalance) }}">{{ formatRupiah($currentBalance) }}</span>
                <span class="font-body-sm text-body-sm text-primary mt-1">
                    <a href="{{ route('cashflows.index') }}" class="hover:underline flex items-center gap-1">Detail Arus Kas <span class="material-symbols-outlined text-[14px]">arrow_forward</span></a>
                </span>
            </div>
        </div>

        <!-- Metric 1: Active Projects -->
        <div class="glass-panel rounded-xl p-4 flex flex-col gap-2 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-symbols-outlined text-6xl text-primary-container">handyman</span>
            </div>
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Proyek Berjalan</span>
            <div class="flex flex-col mt-2">
                <span class="text-xl lg:text-2xl font-bold tracking-tight text-on-background leading-none">{{ $activeOrders }}</span>
                <span class="font-body-sm text-body-sm text-tertiary mt-1">Dalam Proses</span>
            </div>
        </div>

        <!-- Metric 2: Low Stock Alerts -->
        <div class="glass-panel rounded-xl p-4 flex flex-col gap-2 relative overflow-hidden group {{ count($lowStockMaterials) > 0 ? 'border-l-4 border-l-error' : '' }}">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-symbols-outlined text-6xl text-error">warning</span>
            </div>
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Peringatan Stok Rendah</span>
            <div class="flex flex-col mt-2">
                <span class="text-xl lg:text-2xl font-bold tracking-tight text-on-background leading-none">{{ count($lowStockMaterials) }}</span>
                <div class="flex flex-col mt-1 gap-0.5">
                    <span class="font-body-sm text-body-sm text-error">Bahan Baku</span>
                    @if(count($lowStockMaterials) > 0)
                        <span class="font-body-sm text-body-sm text-on-surface-variant">Perlu peninjauan segera</span>
                    @else
                        <span class="font-body-sm text-body-sm text-tertiary">Semua stok mencukupi</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Metric 3: Total Profit -->
        <div class="glass-panel rounded-xl p-4 flex flex-col gap-2 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-symbols-outlined text-6xl text-primary-container">payments</span>
            </div>
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Total Keuntungan</span>
            <div class="flex flex-col mt-2 overflow-hidden">
                <span class="text-xl lg:text-2xl font-bold tracking-tight text-on-background whitespace-nowrap" title="{{ formatRupiah($totalProfit) }}">{{ formatRupiah($totalProfit) }}</span>
                <span class="font-body-sm text-body-sm text-tertiary mt-1">Selesai</span>
            </div>
        </div>

        <!-- Metric 4: Total Receivable -->
        <div class="glass-panel rounded-xl p-4 flex flex-col gap-2 relative overflow-hidden group border-l-4 border-l-amber-500">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-symbols-outlined text-6xl text-amber-500">account_balance_wallet</span>
            </div>
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Total Piutang</span>
            <div class="flex flex-col mt-2 overflow-hidden">
                <span class="text-xl lg:text-2xl font-bold tracking-tight text-on-background whitespace-nowrap" title="{{ formatRupiah($totalReceivable) }}">{{ formatRupiah($totalReceivable) }}</span>
                <span class="font-body-sm text-body-sm text-on-surface-variant mt-1">Sisa tagihan pelanggan</span>
            </div>
        </div>
    </div>

    <!-- Secondary Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Recent Orders Queue -->
        <div class="lg:col-span-2 glass-panel rounded-xl flex flex-col overflow-hidden">
            <div class="p-4 border-b border-surface-container-high flex justify-between items-center">
                <h3 class="font-title-sm text-title-sm text-on-background">Antrean Pesanan Terbaru</h3>
                <a href="{{ route('orders.index') }}" class="text-primary hover:opacity-80 font-body-sm text-body-sm flex items-center gap-1">
                    Lihat Semua <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-surface-container-high/50 border-b border-surface-variant">
                        <tr>
                            <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest w-10 text-center">ID</th>
                            <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Pelanggan</th>
                            <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Batas Waktu</th>
                            <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-center">Status</th>
                            <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-variant/30 font-body-sm text-body-sm text-on-surface bg-white/50">
                        @forelse($recentOrders as $order)
                        <tr class="hover:bg-surface-container-low transition-colors group">
                            <td class="px-6 py-4 text-center font-data-mono text-primary font-bold">{{ $order->order_number }}</td>
                            <td class="px-6 py-4 font-medium text-on-surface">{{ $order->customer->name }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $order->deadline ? \Carbon\Carbon::parse($order->deadline)->format('d M Y') : '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($order->status == 'IN_PRODUCTION')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-bold border border-blue-100 uppercase">Produksi</span>
                                @elseif($order->status == 'DELIVERING')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold border border-amber-100 uppercase">Antar</span>
                                @elseif($order->status == 'UNPAID_DELIVERED')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-error-container/20 text-error text-[10px] font-bold border border-error/20 uppercase">Hutang</span>
                                @elseif($order->status == 'FINISHED')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-100 uppercase">Selesai</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-slate-50 text-slate-500 text-[10px] font-bold border border-slate-100 uppercase">Tunggu</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('orders.show', $order) }}" class="text-primary hover:underline font-bold">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">Tidak ada pesanan terbaru.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Stock Summary / Status Sidebar -->
        <div class="glass-panel rounded-xl p-4 flex flex-col">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="font-title-sm text-title-sm text-on-background">Peringatan Inventaris</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Bahan yang perlu restock.</p>
                </div>
                <span class="material-symbols-outlined text-error">inventory</span>
            </div>
            
            <div class="space-y-3 flex-1 overflow-y-auto">
                @forelse($lowStockMaterials as $material)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-surface-container-low border border-outline-variant/30">
                        <div>
                            <p class="text-on-background font-medium">{{ $material->name }}{{ $material->type ? ' (' . $material->type . ')' : '' }}</p>
                            <p class="text-xs text-on-surface-variant">{{ $material->unit }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-error font-bold">{{ $material->current_qty }}</p>
                            <p class="text-[10px] text-error uppercase font-bold">Stok Rendah</p>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-full text-center p-6">
                        <span class="material-symbols-outlined text-4xl text-tertiary/20 mb-2">check_circle</span>
                        <p class="text-on-surface-variant text-sm">Semua bahan berada di atas tingkat stok aman.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-4 pt-4 border-t border-surface-container-high">
                <a href="{{ route('materials.index') }}" class="w-full py-2 flex justify-center items-center gap-2 rounded-lg border border-outline-variant text-on-surface font-body-sm hover:bg-surface-container-high transition-colors">
                    Kelola Inventaris
                </a>
            </div>
        </div>
    </div>
    <!-- Export Modal -->
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
                <h3 class="font-headline-sm text-on-surface flex items-center gap-2"><span class="material-symbols-outlined text-emerald-600">file_download</span> Export <span x-text="exportTitle"></span></h3>
                <button @click="showExportModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form :action="exportUrl" method="GET" class="p-6 space-y-5">
                <p class="text-sm text-slate-500 font-body-sm">Pilih rentang waktu untuk data yang ingin di-export. Biarkan kosong untuk export semua data.</p>
                <div class="grid gap-4" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
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
