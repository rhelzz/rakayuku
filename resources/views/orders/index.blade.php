@extends('layouts.app')

@section('title', 'Pesanan & Proyek')
@section('content')
<div class="space-y-6" x-data="{ openNewProjectModal: false }">
    <!-- Page Header -->
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Pesanan</span>
        </nav>

        <div class="flex justify-between items-center mb-6 border-b border-slate-200 pb-6">
            <div>
                <h1 class="font-headline-md text-headline-md text-on-background">Pesanan & Proyek</h1>
                <p class="font-body-sm text-body-sm text-slate-400 mt-1">Kelola alur kerja manufaktur aktif.</p>
            </div>
            <a href="{{ route('orders.create') }}" class="px-6 py-2.5 bg-primary text-white rounded-xl font-bold hover:bg-primary-hover shadow-lg shadow-primary/20 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Proyek Baru
            </a>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="flex gap-6 overflow-x-auto pb-4 h-[calc(100vh-200px)]">
        <!-- Pending Column -->
        <div class="flex-shrink-0 w-80 flex flex-col bg-surface-container-low rounded-xl border border-slate-200 shadow-sm">
            <div class="p-4 border-b border-slate-200 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-slate-400"></div>
                    <h2 class="font-title-sm text-title-sm text-on-surface">Menunggu</h2>
                </div>
                <span class="font-data-mono text-data-mono text-slate-500">{{ count($pendingOrders) }}</span>
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-3">
                @forelse($pendingOrders as $order)
                    <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm hover:border-primary/30 transition-colors cursor-pointer group" onclick="window.location='{{ route('orders.show', $order) }}'">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-label-caps text-label-caps text-slate-500 uppercase">{{ $order->order_number }}</span>
                            <span class="px-2 py-0.5 rounded-sm bg-slate-50 text-slate-600 font-label-caps text-[10px] border border-slate-200">Menunggu</span>
                        </div>
                        <h3 class="font-body-md text-body-md font-semibold text-on-surface mb-1 truncate">{{ $order->project_name }}</h3>
                        <p class="font-body-sm text-body-sm text-slate-500 mb-4">{{ $order->customer->name }}</p>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-1.5">
                                @if($order->payment_status === \App\Models\Order::PAYMENT_UNPAID)
                                    <span class="material-symbols-outlined text-[14px] text-error">warning</span>
                                    <span class="font-data-mono text-data-mono text-[11px] text-error">BELUM BAYAR</span>
                                @elseif($order->payment_status === \App\Models\Order::PAYMENT_PARTIAL)
                                    <span class="material-symbols-outlined text-[14px] text-amber-600">payments</span>
                                    <span class="font-data-mono text-data-mono text-[11px] text-amber-600">DIBAYAR SEBAGIAN</span>
                                @else
                                    <span class="material-symbols-outlined text-[14px] text-emerald-600">check_circle</span>
                                    <span class="font-data-mono text-data-mono text-[11px] text-emerald-600">LUNAS</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-500">
                                <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                <span class="font-data-mono text-data-mono text-[11px]">{{ $order->created_at->format('d M') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-slate-500 italic text-sm">Tidak ada proyek menunggu</div>
                @endforelse
            </div>
        </div>

        <!-- In Production Column -->
        <div class="flex-shrink-0 w-80 flex flex-col bg-surface-container-low rounded-xl border border-slate-200 shadow-sm">
            <div class="p-4 border-b border-slate-200 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-primary"></div>
                    <h2 class="font-title-sm text-title-sm text-on-surface">Dalam Produksi</h2>
                </div>
                <span class="font-data-mono text-data-mono text-slate-500">{{ count($inProductionOrders) }}</span>
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-3">
                @forelse($inProductionOrders as $order)
                    <div class="bg-white rounded-lg border border-primary/20 p-4 shadow-sm hover:border-primary/50 transition-colors cursor-pointer group relative overflow-hidden" onclick="window.location='{{ route('orders.show', $order) }}'">
                        <div class="absolute top-0 left-0 w-1 h-full bg-primary"></div>
                        <div class="flex justify-between items-start mb-2 pl-2">
                            <span class="font-label-caps text-label-caps text-primary font-bold uppercase">{{ $order->order_number }}</span>
                            <span class="px-2 py-0.5 rounded-sm bg-primary-container/20 text-on-primary-container font-label-caps text-[10px] border border-primary-container/30">Diproses</span>
                        </div>
                        <h3 class="font-body-md text-body-md font-semibold text-on-surface mb-1 pl-2 truncate">{{ $order->project_name ?? 'Custom Furniture' }}</h3>
                        <p class="font-body-sm text-body-sm text-slate-500 mb-4 pl-2">{{ $order->customer->name }}</p>
                        
                        <div class="pl-2 mb-3">
                            <div class="w-full bg-slate-100 rounded-full h-1.5 mb-1">
                                <div class="bg-primary h-1.5 rounded-full" style="width: 45%"></div>
                            </div>
                            <span class="font-data-mono text-[10px] text-slate-500">Tahap Produksi</span>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-slate-100 pl-2">
                            <div class="flex items-center gap-1.5">
                                @if($order->payment_status === \App\Models\Order::PAYMENT_UNPAID)
                                    <span class="material-symbols-outlined text-[14px] text-error">warning</span>
                                    <span class="font-data-mono text-data-mono text-[11px] text-error">BELUM BAYAR</span>
                                @elseif($order->payment_status === \App\Models\Order::PAYMENT_PARTIAL)
                                    <span class="material-symbols-outlined text-[14px] text-amber-600">payments</span>
                                    <span class="font-data-mono text-data-mono text-[11px] text-amber-600">DIBAYAR SEBAGIAN</span>
                                @else
                                    <span class="material-symbols-outlined text-[14px] text-emerald-600">check_circle</span>
                                    <span class="font-data-mono text-data-mono text-[11px] text-emerald-600">LUNAS</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-500">
                                <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                <span class="font-data-mono text-data-mono text-[11px]">{{ $order->created_at->format('d M') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-slate-500 italic text-sm">Tidak ada produksi aktif</div>
                @endforelse
            </div>
        </div>

        <!-- Finished Column -->
        <div class="flex-shrink-0 w-80 flex flex-col bg-surface-container-low rounded-xl border border-slate-200 shadow-sm">
            <div class="p-4 border-b border-slate-200 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-emerald-600"></div>
                    <h2 class="font-title-sm text-title-sm text-on-surface">Selesai</h2>
                </div>
                <span class="font-data-mono text-data-mono text-slate-500">{{ count($finishedOrders) }}</span>
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-3">
                @forelse($finishedOrders as $order)
                    <div class="bg-white rounded-lg border border-emerald-100 p-4 shadow-sm group cursor-pointer hover:border-emerald-600/50 transition-colors" onclick="window.location='{{ route('orders.show', $order) }}'">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-label-caps text-label-caps text-slate-500 uppercase">{{ $order->order_number }}</span>
                            <span class="px-2 py-0.5 rounded-sm bg-emerald-50 text-emerald-700 font-label-caps text-[10px] border border-emerald-100">Selesai</span>
                        </div>
                        <h3 class="font-body-md text-body-md font-semibold text-on-surface mb-1 truncate">{{ $order->project_name ?? 'Custom Furniture' }}</h3>
                        <p class="font-body-sm text-body-sm text-slate-500 mb-4">{{ $order->customer->name }}</p>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-1.5">
                                @if($order->payment_status === \App\Models\Order::PAYMENT_UNPAID)
                                    <span class="material-symbols-outlined text-[14px] text-error">warning</span>
                                    <span class="font-data-mono text-data-mono text-[11px] text-error">BELUM BAYAR</span>
                                @elseif($order->payment_status === \App\Models\Order::PAYMENT_PARTIAL)
                                    <span class="material-symbols-outlined text-[14px] text-amber-600">payments</span>
                                    <span class="font-data-mono text-data-mono text-[11px] text-amber-600">DIBAYAR SEBAGIAN</span>
                                @else
                                    <span class="material-symbols-outlined text-[14px] text-emerald-600">check_circle</span>
                                    <span class="font-data-mono text-data-mono text-[11px] text-emerald-600">LUNAS</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-500">
                                <span class="material-symbols-outlined text-[14px]">done_all</span>
                                <span class="font-data-mono text-data-mono text-[11px]">{{ $order->updated_at->format('d M') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-slate-500 italic text-sm">Tidak ada proyek selesai</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection