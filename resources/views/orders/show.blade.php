@extends('layouts.app')

@section('title', 'Detail Proyek - ' . $order->order_number)

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'overview' }">
    <!-- Header -->
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('orders.index') }}" class="hover:text-primary transition-colors">Pesanan</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">{{ $order->order_number }}</span>
        </nav>
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-xl bg-surface-container-high flex items-center justify-center text-primary border border-surface-variant relative">
                    <span class="material-symbols-outlined text-3xl">shopping_cart</span>
                    @if($order->status == 'IN_PRODUCTION')
                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-primary rounded-full animate-ping"></div>
                    @endif
                </div>
                <div>
                    <h2 class="font-headline-md text-headline-md text-on-surface">{{ $order->project_name ?? 'Custom Furniture' }}</h2>
                    <p class="font-body-sm text-body-sm text-slate-500">Klien: {{ $order->customer->name }} | Batas Waktu: {{ $order->deadline ? \Carbon\Carbon::parse($order->deadline)->format('d M Y') : '-' }}</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                @if($order->status == 'PENDING')
                    <form action="{{ route('orders.start-production', $order) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg font-semibold hover:opacity-90 transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">play_arrow</span>
                            Mulai Produksi
                        </button>
                    </form>
                @elseif($order->status == 'IN_PRODUCTION')
                    <form action="{{ route('orders.finish-production', $order) }}" method="POST" onsubmit="return confirm('Selesaikan produksi dan hitung total HPP/Keuntungan?')">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700 transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">task_alt</span>
                            Selesaikan Proyek
                        </button>
                    </form>
                @else
                    <span class="px-6 py-2 bg-slate-100 text-slate-600 rounded-lg font-semibold border border-slate-200 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">done_all</span>
                        Selesai
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats & Tabs -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Stats Card -->
        <div class="lg:col-span-3 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="glass-panel p-4 rounded-xl border border-slate-200">
                    <p class="font-label-caps text-label-caps text-slate-500 uppercase">Harga Jual</p>
                    <p class="text-xl font-bold text-on-surface mt-1">Rp {{ number_format($order->selling_price, 0, ',', '.') }}</p>
                </div>
                <div class="glass-panel p-4 rounded-xl border border-slate-200">
                    <p class="font-label-caps text-label-caps text-slate-500 uppercase">Biaya Produksi Saat Ini</p>
                    <p class="text-xl font-bold text-on-surface mt-1">Rp {{ number_format($order->total_cost, 0, ',', '.') }}</p>
                </div>
                <div class="glass-panel p-4 rounded-xl border border-slate-200">
                    <p class="font-label-caps text-label-caps text-slate-500 uppercase">Keuntungan</p>
                    @php $profit = ($order->status === 'FINISHED') ? $order->profit : ($order->selling_price - $order->total_cost); @endphp
                    <p class="text-xl font-bold {{ $profit >= 0 ? 'text-emerald-600' : 'text-error' }} mt-1">Rp {{ number_format($profit, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Interactive Tabs -->
            <div class="glass-panel rounded-xl border border-slate-200 overflow-hidden">
                <div class="flex border-b border-slate-200 bg-surface-container-low/50">
                    <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'text-primary border-b-2 border-primary bg-primary/5' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-4 text-sm font-semibold transition-all">Ringkasan</button>
                    <button @click="activeTab = 'materials'" :class="activeTab === 'materials' ? 'text-primary border-b-2 border-primary bg-primary/5' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-4 text-sm font-semibold transition-all">Bahan Baku (Penggunaan Stok)</button>
                    <button @click="activeTab = 'costs'" :class="activeTab === 'costs' ? 'text-primary border-b-2 border-primary bg-primary/5' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-4 text-sm font-semibold transition-all">Biaya Tambahan</button>
                    <button @click="activeTab = 'payments'" :class="activeTab === 'payments' ? 'text-primary border-b-2 border-primary bg-primary/5' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-4 text-sm font-semibold transition-all">Pembayaran</button>
                </div>

                <div class="p-6 min-h-[400px]">
                    <!-- Overview Tab -->
                    <div x-show="activeTab === 'overview'" class="space-y-6">
                        <div class="grid grid-cols-2 gap-8">
                            <div>
                                <h4 class="text-on-surface font-semibold mb-4 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-[20px]">person</span> Detail Pelanggan
                                </h4>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-[10px] font-label-caps text-slate-500 uppercase tracking-widest">Nama</p>
                                        <p class="text-sm text-on-surface">{{ $order->customer->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-label-caps text-slate-500 uppercase tracking-widest">Telepon</p>
                                        <p class="text-sm text-on-surface">{{ $order->customer->phone }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-label-caps text-slate-500 uppercase tracking-widest">Alamat</p>
                                        <p class="text-sm text-on-surface">{{ $order->customer->address ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-on-surface font-semibold mb-4 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-[20px]">payments</span> Keuangan
                                </h4>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-[10px] font-label-caps text-slate-500 uppercase tracking-widest">Total Kesepakatan</p>
                                        <p class="text-sm text-on-surface">Rp {{ number_format($order->selling_price, 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-label-caps text-slate-500 uppercase tracking-widest">Jumlah Dibayar</p>
                                        <p class="text-sm text-emerald-500">Rp {{ number_format($order->payments()->sum('amount'), 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-label-caps text-slate-500 uppercase tracking-widest">Status Pembayaran</p>
                                        <span class="px-2 py-0.5 {{ $order->payment_status === 'PAID' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-amber-50 text-amber-700 border-amber-200' }} rounded text-[10px] font-bold border">
                                            {{ $order->payment_status == 'UNPAID' ? 'BELUM LUNAS' : ($order->payment_status == 'PARTIAL' ? 'SEBAGIAN' : 'LUNAS') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Materials Tab -->
                    <div x-show="activeTab === 'materials'" class="space-y-6">
                        @if($order->status == 'IN_PRODUCTION')
                        <form action="{{ route('orders.add-material', $order) }}" method="POST" class="bg-surface-container-high/30 p-4 rounded-xl border border-slate-200 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            @csrf
                            <div class="md:col-span-2 space-y-1.5">
                                <label class="text-[11px] font-label-caps text-slate-500 uppercase">Pilih Bahan Baku</label>
                                <select name="material_id" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 text-sm">
                                    <option disabled selected value="">Pilih...</option>
                                    @foreach($materials as $m)
                                        <option value="{{ $m->id }}">{{ $m->name }} (Stok: {{ $m->current_qty }} {{ $m->unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-label-caps text-slate-500 uppercase">Jumlah</label>
                                <input type="number" name="qty" required step="0.01" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 text-sm" placeholder="0">
                            </div>
                            <button type="submit" class="bg-primary text-white py-2 rounded-lg font-semibold text-sm hover:opacity-90 transition-colors">Tambah ke Proyek</button>
                        </form>
                        @endif

                        <table class="w-full text-left">
                            <thead class="border-b border-slate-200">
                                <tr>
                                    <th class="py-3 font-label-caps text-slate-500 uppercase text-[11px]">Bahan Baku</th>
                                    <th class="py-3 font-label-caps text-slate-500 uppercase text-[11px] text-right">Jumlah Digunakan</th>
                                    <th class="py-3 font-label-caps text-slate-500 uppercase text-[11px] text-right">HPP Satuan (saat digunakan)</th>
                                    <th class="py-3 font-label-caps text-slate-500 uppercase text-[11px] text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @forelse($order->materials as $om)
                                <tr class="border-b border-slate-800/50">
                                    <td class="py-3 text-on-surface">{{ $om->material->name }}</td>
                                    <td class="py-3 text-right text-on-surface-variant">{{ number_format($om->qty_used, 2) }} {{ $om->material->unit }}</td>
                                    <td class="py-3 text-right text-on-surface-variant">Rp {{ number_format($om->price_snapshot, 0, ',', '.') }}</td>
                                    <td class="py-3 text-right font-semibold text-on-surface">Rp {{ number_format($om->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-500 italic">Belum ada bahan yang dialokasikan untuk proyek ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Costs Tab -->
                    <div x-show="activeTab === 'costs'" class="space-y-6">
                        @if($order->status == 'IN_PRODUCTION')
                        <form action="{{ route('orders.add-cost', $order) }}" method="POST" class="bg-surface-container-high/30 p-4 rounded-xl border border-slate-200 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            @csrf
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-label-caps text-slate-500 uppercase">Tipe</label>
                                <select name="type" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 text-sm">
                                    <option value="LABOR_OVERTIME">Lembur Tenaga Kerja</option>
                                    <option value="TRANSPORT">Transportasi</option>
                                    <option value="TOOLS">Peralatan</option>
                                    <option value="OTHER">Lainnya</option>
                                </select>
                            </div>
                            <div class="space-y-1.5 md:col-span-1">
                                <label class="text-[11px] font-label-caps text-slate-500 uppercase">Deskripsi</label>
                                <input type="text" name="description" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 text-sm" placeholder="misal: Biaya Pengiriman">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-label-caps text-slate-500 uppercase">Jumlah (Rp)</label>
                                <input type="number" name="amount" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 text-sm" placeholder="0">
                            </div>
                            <button type="submit" class="bg-primary text-white py-2 rounded-lg font-semibold text-sm hover:opacity-90 transition-colors">Tambah Biaya</button>
                        </form>
                        @endif

                        <table class="w-full text-left">
                            <thead class="border-b border-slate-200">
                                <tr>
                                    <th class="py-3 font-label-caps text-slate-500 uppercase text-[11px]">Tipe</th>
                                    <th class="py-3 font-label-caps text-slate-500 uppercase text-[11px]">Deskripsi</th>
                                    <th class="py-3 font-label-caps text-slate-500 uppercase text-[11px] text-right">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @forelse($order->productionCosts as $cost)
                                <tr class="border-b border-slate-800/50">
                                    <td class="py-3 text-on-surface">{{ $cost->type == 'LABOR_OVERTIME' ? 'Lembur Tenaga Kerja' : ($cost->type == 'TRANSPORT' ? 'Transportasi' : ($cost->type == 'TOOLS' ? 'Peralatan' : 'Lainnya')) }}</td>
                                    <td class="py-3 text-on-surface">{{ $cost->description }}</td>
                                    <td class="py-3 text-right font-semibold text-on-surface">Rp {{ number_format($cost->amount, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="py-8 text-center text-slate-500 italic">Belum ada biaya tambahan yang tercatat.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Payments Tab -->
                    <div x-show="activeTab === 'payments'" class="space-y-6">
                        @if($order->payment_status !== 'PAID')
                        <form action="{{ route('orders.pay', $order) }}" method="POST" class="bg-surface-container-high/30 p-4 rounded-xl border border-slate-200 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            @csrf
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-label-caps text-slate-500 uppercase">Tipe</label>
                                <select name="type" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 text-sm">
                                    <option value="DP">Uang Muka (DP)</option>
                                    <option value="FINAL" selected>Pelunasan</option>
                                </select>
                            </div>
                            <div class="md:col-span-2 space-y-1.5">
                                <label class="text-[11px] font-label-caps text-slate-500 uppercase">Jumlah (Rp)</label>
                                <input type="number" name="amount" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 text-sm" placeholder="0">
                            </div>
                            <button type="submit" class="bg-primary text-white py-2 rounded-lg font-semibold text-sm hover:opacity-90 transition-colors">Tambah Pembayaran</button>
                        </form>
                        @endif

                        <table class="w-full text-left">
                            <thead class="border-b border-slate-200">
                                <tr>
                                    <th class="py-3 font-label-caps text-slate-500 uppercase text-[11px]">Tanggal</th>
                                    <th class="py-3 font-label-caps text-slate-500 uppercase text-[11px]">Tipe</th>
                                    <th class="py-3 font-label-caps text-slate-500 uppercase text-[11px] text-right">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @forelse($order->payments as $payment)
                                <tr class="border-b border-slate-800/50">
                                    <td class="py-3 text-on-surface">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                                    <td class="py-3 text-on-surface">{{ $payment->type == 'DP' ? 'Uang Muka (DP)' : 'Pelunasan' }}</td>
                                    <td class="py-3 text-right font-semibold text-on-surface">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="py-8 text-center text-slate-500 italic">Belum ada pembayaran yang tercatat.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / Lifecycle -->
        <div class="space-y-6">
            <div class="glass-panel p-6 rounded-xl border border-slate-200">
                <h4 class="text-on-surface font-semibold mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">history</span> Siklus Proyek
                </h4>
                <div class="space-y-6 relative">
                    <!-- Vertical Line -->
                    <div class="absolute left-[11px] top-2 bottom-2 w-0.5 bg-slate-200"></div>
                    
                    <!-- Pending -->
                    <div class="relative flex items-start gap-4">
                        <div class="w-6 h-6 rounded-full bg-emerald-500/20 border-2 border-emerald-500 flex items-center justify-center z-10">
                            <span class="material-symbols-outlined text-[12px] text-emerald-500">check</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-on-surface">Pesanan Diterima</p>
                            <p class="text-[11px] text-slate-500">{{ $order->created_at->format('d M Y') }}</p>
                        </div>
                    </div>

                    <!-- In Production -->
                    <div class="relative flex items-start gap-4">
                        <div class="w-6 h-6 rounded-full {{ $order->status != 'PENDING' ? 'bg-primary/10 border-2 border-primary' : 'bg-slate-100 border-2 border-slate-200' }} flex items-center justify-center z-10">
                            @if($order->status != 'PENDING')
                                <span class="material-symbols-outlined text-[12px] text-primary fill">factory</span>
                            @else
                                <span class="material-symbols-outlined text-[12px] text-slate-400">circle</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-semibold {{ $order->status != 'PENDING' ? 'text-on-surface' : 'text-slate-500' }}">Produksi</p>
                            @if($order->status == 'IN_PRODUCTION')
                                <p class="text-[11px] text-primary animate-pulse font-bold">Sedang Berjalan...</p>
                            @elseif($order->status == 'FINISHED')
                                <p class="text-[11px] text-emerald-600 font-bold">Selesai</p>
                            @else
                                <p class="text-[11px] text-slate-500 font-medium">Menunggu...</p>
                            @endif
                        </div>
                    </div>

                    <!-- Finished -->
                    <div class="relative flex items-start gap-4">
                        <div class="w-6 h-6 rounded-full {{ $order->status == 'FINISHED' ? 'bg-emerald-50 text-emerald-600 border-2 border-emerald-600' : 'bg-slate-100 border-2 border-slate-200' }} flex items-center justify-center z-10">
                            @if($order->status == 'FINISHED')
                                <span class="material-symbols-outlined text-[12px] text-emerald-600">check</span>
                            @else
                                <span class="material-symbols-outlined text-[12px] text-slate-400">circle</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-semibold {{ $order->status == 'FINISHED' ? 'text-on-surface' : 'text-slate-500' }}">Pengiriman & Selesai</p>
                            <p class="text-[11px] text-slate-500">{{ $order->status == 'FINISHED' ? 'Proyek Ditutup' : 'Dijadwalkan' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Warning: Low Stock for Production -->
            @php $lowStockInProduction = $materials->where('current_qty', '<', 2); @endphp
            @if(count($lowStockInProduction) > 0 && $order->status == 'IN_PRODUCTION')
                <div class="p-4 bg-error-container/10 border border-error/30 rounded-xl">
                    <div class="flex items-center gap-2 text-error mb-2">
                        <span class="material-symbols-outlined text-[20px]">warning</span>
                        <p class="text-xs font-bold uppercase tracking-wider">Peringatan Inventaris Kritis</p>
                    </div>
                    <p class="text-[11px] text-slate-500 mb-3">Beberapa bahan hampir habis dan dapat menghambat produksi:</p>
                    <div class="space-y-2">
                        @foreach($lowStockInProduction as $m)
                            <div class="flex justify-between text-[11px]">
                                <span class="text-on-surface">{{ $m->name }}</span>
                                <span class="text-error font-bold">{{ $m->current_qty }} {{ $m->unit }}</span>
                            </div>
                        @endforeach
                    </div>
                    <a href="{{ route('materials.index') }}" class="mt-4 block text-center py-2 bg-error text-on-error rounded-lg text-xs font-bold">Kelola Stok Ulang</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
