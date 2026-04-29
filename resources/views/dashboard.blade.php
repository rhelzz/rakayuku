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
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Proyek Aktif</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="font-display-lg text-display-lg text-on-background">{{ $inProductionOrders }}</span>
                <span class="font-body-sm text-body-sm text-tertiary flex items-center">Dalam Produksi</span>
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
                <span class="font-body-md text-body-md text-on-surface-variant">Rp</span>
                <span class="font-headline-md text-headline-md text-on-background">{{ number_format($totalProfit, 0, ',', '.') }}</span>
                <span class="font-body-sm text-body-sm text-tertiary ml-1">Akumulasi</span>
            </div>
        </div>

        <!-- Metric 4: Pending Orders -->
        <div class="glass-panel rounded-xl p-4 flex flex-col gap-2 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-symbols-outlined text-6xl text-primary-container">schedule</span>
            </div>
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Pesanan Menunggu</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="font-display-lg text-display-lg text-on-background">{{ $pendingOrders }}</span>
                <span class="font-body-sm text-body-sm text-on-surface-variant">Menunggu produksi</span>
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
                    <thead>
                        <tr class="border-b border-surface-container-high bg-surface-container-low/50">
                            <th class="p-3 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase">ID Pesanan</th>
                            <th class="p-3 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Pelanggan</th>
                            <th class="p-3 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Batas Waktu</th>
                            <th class="p-3 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Status</th>
                            <th class="p-3 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="font-body-sm text-body-sm">
                        @forelse($recentOrders as $order)
                            <tr class="border-b border-surface-container-high hover:bg-surface-container/50 transition-colors">
                                <td class="p-3 px-4 font-data-mono text-data-mono text-primary font-bold">{{ $order->order_number }}</td>
                                <td class="p-3 px-4 text-on-background font-medium">{{ $order->customer->name }}</td>
                                <td class="p-3 px-4 text-on-surface-variant">{{ $order->deadline ? \Carbon\Carbon::parse($order->deadline)->format('d M Y') : '-' }}</td>
                                <td class="p-3 px-4">
                                    @if($order->status == 'IN_PRODUCTION')
                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded bg-secondary-container text-on-secondary-container text-xs font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span> Dalam Produksi
                                        </span>
                                    @elseif($order->status == 'FINISHED')
                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded bg-green-50 text-green-700 text-xs font-medium border border-green-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded bg-surface-container-high text-on-surface-variant text-xs font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-on-surface-variant"></span> Menunggu
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 px-4 text-right">
                                    <a href="{{ route('orders.show', $order) }}" class="text-primary hover:underline">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-on-surface-variant italic">Tidak ada pesanan terbaru.</td>
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
                            <p class="text-on-background font-medium">{{ $material->name }}</p>
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
