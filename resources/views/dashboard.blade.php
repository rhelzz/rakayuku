@extends('layouts.app')

@section('title', 'Ringkasan Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="font-display-lg text-display-lg text-on-background">Ringkasan Dashboard</h2>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Metrik real-time untuk pemrosesan kayu dan inventaris.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('orders.create') }}" class="px-4 py-2 rounded-lg bg-primary-container text-on-primary-container font-body-sm text-body-sm hover:opacity-90 transition-opacity flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add</span> Pesanan Baru
            </a>
        </div>
    </div>

    <!-- KPI Bento Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Active Projects -->
        <div class="glass-panel rounded-xl p-4 flex flex-col gap-2 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-symbols-outlined text-6xl text-primary-container">handyman</span>
            </div>
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Proyek Berjalan</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="font-display-lg text-display-lg text-on-background">{{ $activeOrders }}</span>
                <span class="font-body-sm text-body-sm text-tertiary flex items-center">Dalam Proses</span>
            </div>
        </div>

        <!-- Metric 2: Low Stock Alerts -->
        <div class="glass-panel rounded-xl p-4 flex flex-col gap-2 relative overflow-hidden group {{ count($lowStockMaterials) > 0 ? 'border-l-4 border-l-error' : '' }}">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-symbols-outlined text-6xl text-error">warning</span>
            </div>
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Peringatan Stok Rendah</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="font-display-lg text-display-lg text-on-background">{{ count($lowStockMaterials) }}</span>
                <span class="font-body-sm text-body-sm text-error flex items-center">Bahan Baku</span>
            </div>
            @if(count($lowStockMaterials) > 0)
                <span class="font-body-sm text-body-sm text-on-surface-variant mt-1">Perlu peninjauan segera</span>
            @else
                <span class="font-body-sm text-body-sm text-tertiary mt-1">Semua stok mencukupi</span>
            @endif
        </div>

        <!-- Metric 3: Total Profit -->
        <div class="glass-panel rounded-xl p-4 flex flex-col gap-2 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-symbols-outlined text-6xl text-primary-container">payments</span>
            </div>
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Total Keuntungan</span>
            <div class="flex items-baseline gap-1 mt-2">
                <span class="font-headline-md text-headline-md text-on-background">{{ formatRupiah($totalProfit) }}</span>
                <span class="font-body-sm text-body-sm text-tertiary ml-1">Selesai</span>
            </div>
        </div>

        <!-- Metric 4: Total Receivable -->
        <div class="glass-panel rounded-xl p-4 flex flex-col gap-2 relative overflow-hidden group border-l-4 border-l-amber-500">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-symbols-outlined text-6xl text-amber-500">account_balance_wallet</span>
            </div>
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Total Piutang</span>
            <div class="flex items-baseline gap-1 mt-2">
                <span class="font-headline-md text-headline-md text-on-background">{{ formatRupiah($totalReceivable) }}</span>
            </div>
            <span class="font-body-sm text-body-sm text-on-surface-variant mt-1">Sisa tagihan pelanggan</span>
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
</div>
@endsection
