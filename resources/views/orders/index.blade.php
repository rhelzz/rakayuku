@extends('layouts.app')

@section('title', 'Pesanan & Proyek')
@section('content')
<div class="space-y-6" x-data="{ viewMode: '{{ request('view_mode', 'kanban') }}', showExportModal: false }">
    <!-- Page Header -->
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Pesanan</span>
        </nav>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-2">
            <div>
                <h1 class="font-headline-md text-headline-md text-on-background">Pesanan & Proyek</h1>
                <p class="font-body-sm text-body-sm text-slate-400 mt-1">Kelola alur kerja manufaktur aktif.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- View Mode Switcher -->
                <div class="flex bg-surface-container-high p-1 rounded-xl border border-surface-variant">
                    <button @click="viewMode = 'kanban'; $dispatch('update-view-mode', 'kanban')" :class="viewMode === 'kanban' ? 'bg-white text-primary shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex items-center gap-2 px-4 py-1.5 rounded-lg text-sm font-bold transition-all">
                        <span class="material-symbols-outlined text-[18px]">view_kanban</span>
                        Kanban
                    </button>
                    <button @click="viewMode = 'table'; $dispatch('update-view-mode', 'table')" :class="viewMode === 'table' ? 'bg-white text-primary shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex items-center gap-2 px-4 py-1.5 rounded-lg text-sm font-bold transition-all">
                        <span class="material-symbols-outlined text-[18px]">table_rows</span>
                        Tabel
                    </button>
                </div>

                <button @click="showExportModal = true" class="px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl font-bold hover:shadow-lg hover:shadow-emerald-500/30 transition-all flex items-center gap-2 text-sm shadow-md">
                    <span class="material-symbols-outlined text-[20px]">download</span>
                    Export
                </button>

                <a href="{{ route('orders.create') }}" class="px-6 py-2.5 bg-primary text-white rounded-xl font-bold hover:bg-primary-hover shadow-lg shadow-primary/20 transition-all flex items-center gap-2 text-sm">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    Proyek Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Table Filter -->
    <x-table.filter placeholder="Cari nomor pesanan, proyek, atau pelanggan...">
        <x-slot name="customFilters">
            <input type="hidden" name="view_mode" :value="viewMode">
        </x-slot>
    </x-table.filter>

    <!-- Kanban View -->
    <div x-show="viewMode === 'kanban'" class="flex gap-6 overflow-x-auto pb-4 h-[calc(100vh-250px)]">
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

        <!-- Delivery & Debt Column -->
        <div class="flex-shrink-0 w-80 flex flex-col bg-surface-container-low rounded-xl border border-slate-200 shadow-sm">
            <div class="p-4 border-b border-slate-200 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                    <h2 class="font-title-sm text-title-sm text-on-surface">Pengantaran & Hutang</h2>
                </div>
                <span class="font-data-mono text-data-mono text-slate-500">{{ count($deliveryOrders) }}</span>
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-3">
                @forelse($deliveryOrders as $order)
                    <div class="bg-white rounded-lg border border-amber-200 p-4 shadow-sm hover:border-amber-500/50 transition-colors cursor-pointer group" onclick="window.location='{{ route('orders.show', $order) }}'">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-label-caps text-label-caps text-amber-700 font-bold uppercase">{{ $order->order_number }}</span>
                            <span class="px-2 py-0.5 rounded-sm {{ $order->status == 'UNPAID_DELIVERED' ? 'bg-error-container/20 text-error' : 'bg-amber-50 text-amber-700' }} font-label-caps text-[10px] border {{ $order->status == 'UNPAID_DELIVERED' ? 'border-error/20' : 'border-amber-200' }}">
                                {{ $order->status == 'UNPAID_DELIVERED' ? 'HUTANG' : 'PENGANTARAN' }}
                            </span>
                        </div>
                        <h3 class="font-body-md text-body-md font-semibold text-on-surface mb-1 truncate">{{ $order->project_name }}</h3>
                        <p class="font-body-sm text-body-sm text-slate-500 mb-4">{{ $order->customer->name }}</p>
                        
                        @if($order->status == 'UNPAID_DELIVERED')
                            <div class="mb-3 p-2 bg-error-container/10 rounded border border-error/10">
                                <p class="text-[10px] text-error font-bold uppercase tracking-wider mb-1">Sisa Tagihan</p>
                                <p class="text-sm font-bold text-error">{{ formatRupiah($order->remaining_payment) }}</p>
                            </div>
                        @endif

                        <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[14px] {{ $order->payment_status == 'PAID' ? 'text-emerald-600' : 'text-amber-600' }}">payments</span>
                                <span class="font-data-mono text-data-mono text-[11px] {{ $order->payment_status == 'PAID' ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ $order->payment_status == 'PAID' ? 'LUNAS' : 'SEBAGIAN' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-500">
                                <span class="material-symbols-outlined text-[14px]">local_shipping</span>
                                <span class="font-data-mono text-data-mono text-[11px]">{{ $order->updated_at->format('d M') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-slate-500 italic text-sm">Tidak ada pengantaran aktif</div>
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

    <!-- Table View -->
    <div x-show="viewMode === 'table'" class="bg-surface-container-low border border-surface-variant rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-high/50 border-b border-surface-variant">
                    <tr>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest w-10 text-center">No</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">ID Pesanan</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Proyek</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Pelanggan</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Status</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Total Harga</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-variant/30 font-body-sm text-body-sm text-on-surface bg-white/50">
                    @forelse($orders as $order)
                    <tr class="hover:bg-surface-container-low transition-colors group">
                        <td class="px-6 py-4 text-center font-data-mono text-slate-400">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-bold text-primary">{{ $order->order_number }}</td>
                        <td class="px-6 py-4 font-medium">{{ $order->project_name }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $order->customer->name }}</td>
                        <td class="px-6 py-4">
                            @if($order->status === 'PENDING')
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold border border-slate-200 uppercase">Menunggu</span>
                            @elseif($order->status === 'IN_PRODUCTION')
                                <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-[10px] font-bold border border-blue-100 uppercase">Produksi</span>
                            @elseif($order->status === 'DELIVERING')
                                <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold border border-amber-100 uppercase">Pengantaran</span>
                            @elseif($order->status === 'UNPAID_DELIVERED')
                                <span class="px-2.5 py-1 rounded-full bg-error-container/20 text-error text-[10px] font-bold border border-error/20 uppercase">Hutang</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-100 uppercase">Selesai</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-data-mono font-bold">
                            {{ formatRupiah($order->selling_price) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center gap-1.5 text-primary hover:underline font-bold">
                                Lihat <span class="material-symbols-outlined text-[16px]">visibility</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400 italic">Belum ada pesanan masuk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- Export Modal -->
    <div x-show="showExportModal" class="fixed inset-0 z-50" style="display: none;" x-cloak>
        <div x-show="showExportModal" x-transition.opacity class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showExportModal = false"></div>
        <div x-show="showExportModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-surface-container-low">
                <h3 class="font-headline-sm text-on-surface flex items-center gap-2"><span class="material-symbols-outlined text-emerald-600">file_download</span> Export Excel</h3>
                <button @click="showExportModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="{{ route('orders.export') }}" method="GET" class="p-6 space-y-5">
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