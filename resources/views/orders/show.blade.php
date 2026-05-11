@extends('layouts.app')

@section('title', 'Detail Proyek - ' . $order->order_number)

@section('content')
<div class="space-y-6" x-data="{ activeTab: '{{ session('active_tab', 'overview') }}' }">
    @if($order->status == 'CANCELLED')
    <div class="p-4 bg-slate-100 border border-slate-300 rounded-xl flex items-start gap-3">
        <span class="material-symbols-outlined text-slate-500 text-[24px] mt-0.5">block</span>
        <div>
            <p class="text-sm font-bold text-slate-700">Pesanan Dibatalkan</p>
            @if($order->cancel_reason)
                <p class="text-xs text-slate-500 mt-1">Alasan: {{ $order->cancel_reason }}</p>
            @endif
            @if($order->cancelled_at)
                <p class="text-[10px] text-slate-400 mt-1">Dibatalkan pada: {{ $order->cancelled_at->format('d M Y, H:i') }}</p>
            @endif
        </div>
    </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('orders.index') }}" class="hover:text-primary transition-colors">Pesanan</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Detail Proyek</span>
        </nav>
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-xl bg-surface-container-high flex items-center justify-center text-primary border border-surface-variant relative shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.56-7.43H5.12"/></svg>
                    @if($order->status == 'IN_PRODUCTION')
                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-primary rounded-full animate-ping"></div>
                    @endif
                </div>
                <div>
                    <h2 class="font-headline-md text-headline-md text-on-surface">{{ $order->project_name }}</h2>
                    @if($order->project_description)
                        <p class="text-[11px] text-slate-400 italic mt-0.5">{{ $order->project_description }}</p>
                    @endif
                    <p class="font-body-sm text-body-sm text-slate-500">Klien: {{ $order->customer->name }} | Batas Waktu: {{ $order->deadline ? \Carbon\Carbon::parse($order->deadline)->format('d M Y') : '-' }}</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3 flex-wrap">
                @if($order->status == 'PENDING')
                    <a href="{{ route('orders.edit', $order) }}" class="px-5 py-2 bg-white text-on-surface border border-surface-variant rounded-lg font-semibold hover:bg-surface-container transition-colors flex items-center gap-2 text-sm">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                        Edit
                    </a>
                    <form id="startProductionForm" action="{{ route('orders.start-production', $order) }}" method="POST">
                        @csrf
                        <button type="button" onclick="confirmAction('startProductionForm', 'Mulai produksi sekarang?', 'Stok bahan baku akan mulai dialokasikan ke proyek ini.', 'warning', 'Mulai Produksi')" class="px-6 py-2 bg-primary text-white rounded-lg font-semibold hover:opacity-90 transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">play_arrow</span>
                            Mulai Produksi
                        </button>
                    </form>
                    <form id="cancelOrderForm" action="{{ route('orders.cancel', $order) }}" method="POST" x-data="{ reason: '' }">
                        @csrf
                        <input type="hidden" name="cancel_reason" x-model="reason">
                        <button type="button" onclick="cancelOrder()" class="px-5 py-2 bg-white text-error border border-error/30 rounded-lg font-semibold hover:bg-error-container/20 transition-colors flex items-center gap-2 text-sm">
                            <span class="material-symbols-outlined text-[18px]">block</span>
                            Batalkan
                        </button>
                    </form>
                @elseif($order->status == 'IN_PRODUCTION')
                    <form id="finishProductionForm" action="{{ route('orders.finish-production', $order) }}" method="POST">
                        @csrf
                        <button type="button" onclick="confirmAction('finishProductionForm', 'Selesaikan tahap produksi?', 'Anda tidak akan bisa menambah/menghapus bahan baku lagi setelah ini.', 'info', 'Ya, Selesaikan')" class="px-6 py-2 bg-amber-600 text-white rounded-lg font-semibold hover:bg-amber-700 transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                            Selesaikan Produksi
                        </button>
                    </form>
                    <form id="cancelOrderForm" action="{{ route('orders.cancel', $order) }}" method="POST" x-data="{ reason: '' }">
                        @csrf
                        <input type="hidden" name="cancel_reason" x-model="reason">
                        <button type="button" onclick="cancelOrder()" class="px-5 py-2 bg-white text-error border border-error/30 rounded-lg font-semibold hover:bg-error-container/20 transition-colors flex items-center gap-2 text-sm">
                            <span class="material-symbols-outlined text-[18px]">block</span>
                            Batalkan
                        </button>
                    </form>
                @elseif($order->status == 'DELIVERING')
                    <form id="markDeliveredForm" action="{{ route('orders.mark-delivered', $order) }}" method="POST">
                        @csrf
                        <button type="button" onclick="confirmAction('markDeliveredForm', 'Konfirmasi pengiriman selesai?', 'Status proyek akan diperbarui berdasarkan sisa tagihan.', 'question', 'Ya, Terkirim')" class="px-6 py-2 bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700 transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">task_alt</span>
                            Konfirmasi Terkirim
                        </button>
                    </form>
                @elseif($order->status == 'UNPAID_DELIVERED')
                    <a href="{{ route('orders.print', $order) }}" target="_blank" class="px-6 py-2 bg-white text-on-surface border border-surface-variant rounded-lg font-semibold hover:bg-surface-container transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">print</span>
                        Cetak Resi
                    </a>
                    <span class="px-6 py-2 bg-error-container text-error rounded-lg font-semibold border border-error/30 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">warning</span>
                        Menunggu Pelunasan (Hutang)
                    </span>
                @elseif($order->status == 'CANCELLED')
                    <span class="px-6 py-2 bg-slate-200 text-slate-600 rounded-lg font-semibold border border-slate-300 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">block</span>
                        Dibatalkan
                    </span>
                @else
                    <a href="{{ route('orders.print', $order) }}" target="_blank" class="px-6 py-2 bg-white text-on-surface border border-surface-variant rounded-lg font-semibold hover:bg-surface-container transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">print</span>
                        Cetak Invoice
                    </a>
                    <span class="px-6 py-2 bg-slate-100 text-slate-600 rounded-lg font-semibold border border-slate-200 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">done_all</span>
                        Selesai & Lunas
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats & Tabs -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Stats Card -->
        <div class="lg:col-span-3 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="glass-panel p-4 rounded-xl border border-slate-200">
                    <p class="font-label-caps text-label-caps text-slate-500 uppercase">Harga Jual</p>
                    <p class="text-xl font-bold text-on-surface mt-1">{{ formatRupiah($order->selling_price) }}</p>
                </div>
                <div class="glass-panel p-4 rounded-xl border border-slate-200">
                    <p class="font-label-caps text-label-caps text-slate-500 uppercase">Estimasi Biaya</p>
                    <p class="text-xl font-bold text-on-surface mt-1">{{ formatRupiah($order->live_total_cost) }}</p>
                </div>
                <div class="glass-panel p-4 rounded-xl border border-slate-200">
                    <p class="font-label-caps text-label-caps text-slate-500 uppercase">Estimasi Profit</p>
                    <p class="text-xl font-bold {{ $order->estimated_profit >= 0 ? 'text-emerald-600' : 'text-error' }} mt-1">
                        {{ formatRupiah($order->estimated_profit) }}
                    </p>
                </div>
                <div class="glass-panel p-4 rounded-xl border border-slate-200">
                    <p class="font-label-caps text-label-caps text-slate-500 uppercase">Margin</p>
                    <p class="text-xl font-bold {{ $order->profit_margin >= 20 ? 'text-emerald-600' : ($order->profit_margin > 0 ? 'text-amber-600' : 'text-error') }} mt-1">
                        {{ number_format($order->profit_margin, 1) }}%
                    </p>
                </div>
            </div>

            <!-- Interactive Tabs -->
            <div class="glass-panel rounded-xl border border-slate-200 overflow-hidden">
                <div class="flex overflow-x-auto border-b border-slate-200 bg-surface-container-low/50" style="scrollbar-width: none;">
                    <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'text-primary border-b-2 border-primary bg-primary/5' : 'text-slate-500 hover:text-slate-700'" class="whitespace-nowrap px-4 sm:px-6 py-4 text-sm font-semibold transition-all">Ringkasan</button>
                    <button @click="activeTab = 'materials'" :class="activeTab === 'materials' ? 'text-primary border-b-2 border-primary bg-primary/5' : 'text-slate-500 hover:text-slate-700'" class="whitespace-nowrap px-4 sm:px-6 py-4 text-sm font-semibold transition-all">Bahan Baku (Penggunaan Stok)</button>
                    <button @click="activeTab = 'costs'" :class="activeTab === 'costs' ? 'text-primary border-b-2 border-primary bg-primary/5' : 'text-slate-500 hover:text-slate-700'" class="whitespace-nowrap px-4 sm:px-6 py-4 text-sm font-semibold transition-all">Biaya Tambahan</button>
                    <button @click="activeTab = 'payments'" :class="activeTab === 'payments' ? 'text-primary border-b-2 border-primary bg-primary/5' : 'text-slate-500 hover:text-slate-700'" class="whitespace-nowrap px-4 sm:px-6 py-4 text-sm font-semibold transition-all">Pembayaran</button>
                </div>

                <div class="p-6 min-h-[400px]">
                    <!-- Overview Tab -->
                    <div x-show="activeTab === 'overview'" class="space-y-6">
                        <div class="grid grid-cols-2 gap-8">
                            <div>
                                <h4 class="text-on-surface font-semibold mb-4 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Detail Pelanggan
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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg> Keuangan
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
                                        <span class="inline-block whitespace-nowrap px-2 py-0.5 {{ $order->payment_status === 'PAID' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-amber-50 text-amber-700 border-amber-200' }} rounded text-[10px] font-bold border">
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
                                        <option value="{{ $m->id }}">{{ $m->name }}{{ $m->type ? ' (' . $m->type . ')' : '' }} — Stok: {{ formatQty($m->current_qty) }} {{ $m->unit }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-label-caps text-slate-500 uppercase">Jumlah</label>
                                <input type="number" name="qty" required step="any" min="0.01" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 text-sm" placeholder="0">
                            </div>
                            <button type="submit" class="bg-primary text-white py-2 rounded-lg font-semibold text-sm hover:opacity-90 transition-colors">Tambah ke Proyek</button>
                        </form>
                        @endif

                        <div class="bg-surface-container-low border border-surface-variant rounded-xl overflow-hidden shadow-sm">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-surface-container-high/50 border-b border-surface-variant">
                                        <tr>
                                            <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Bahan Baku</th>
                                            <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Jumlah</th>
                                            <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Modal Satuan</th>
                                            <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Subtotal</th>
                                            <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-surface-variant/30 font-body-sm text-body-sm text-on-surface bg-white/50" x-data="{ activeResidueForm: null }">
                                        @forelse($order->materials as $om)
                                        <tr class="hover:bg-surface-container-low transition-colors group">
                                            <td class="px-6 py-4 font-medium">{{ $om->material->name }}{{ $om->material->type ? ' (' . $om->material->type . ')' : '' }}</td>
                                            <td class="px-6 py-4 text-right font-data-mono text-slate-500">{{ formatQty($om->qty_used) }} {{ $om->material->unit }}</td>
                                            <td class="px-6 py-4 text-right font-data-mono text-slate-500">{{ formatRupiah($om->price_snapshot) }}</td>
                                            <td class="px-6 py-4 text-right font-data-mono font-bold text-on-surface">{{ formatRupiah($om->subtotal) }}</td>
                                            <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
                                                @if($order->status == 'IN_PRODUCTION')
                                                <button type="button" 
                                                        @click="activeResidueForm = (activeResidueForm === {{ $om->id }} ? null : {{ $om->id }})"
                                                        class="text-slate-400 hover:text-primary transition-colors" title="Catat Residu/Sisa">
                                                    <span class="material-symbols-outlined text-[18px]">recycling</span>
                                                </button>
                                                <form id="removeMaterial-{{ $om->id }}" action="{{ route('orders.remove-material', $om) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" onclick="confirmAction('removeMaterial-{{ $om->id }}', 'Hapus penggunaan bahan?', 'Stok akan dikembalikan ke inventaris.', 'warning', 'Ya, Hapus')" class="text-slate-400 hover:text-error transition-colors">
                                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                                    </button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                        <!-- Inline Residue Form -->
                                        @if($order->status == 'IN_PRODUCTION')
                                        <tr x-show="activeResidueForm === {{ $om->id }}" x-transition class="bg-primary/5">
                                            <td colspan="5" class="px-6 py-4">
                                                <form action="{{ route('orders.add-residue', $order) }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end"
                                                      x-data="{ 
                                                        qty: '', 
                                                        type: 'REUSABLE',
                                                        priceSnapshot: {{ $om->price_snapshot }},
                                                        maxQty: {{ $om->qty_used }},
                                                        displayReduction: '',
                                                        rawReduction: '',
                                                        validateQty() {
                                                            let val = parseFloat(this.qty);
                                                            if (val > this.maxQty) {
                                                                this.qty = this.maxQty;
                                                                Swal.fire({
                                                                    icon: 'warning',
                                                                    title: 'Batas Maksimal',
                                                                    text: 'Jumlah residu tidak boleh melebihi jumlah terpakai (' + this.maxQty + ')',
                                                                    toast: true,
                                                                    position: 'top-end',
                                                                    showConfirmButton: false,
                                                                    timer: 3000
                                                                });
                                                            }
                                                            this.calculateReduction();
                                                        },
                                                        calculateReduction() {
                                                            let val = parseFloat(this.qty || 0);
                                                            let total = Math.round(val * this.priceSnapshot);
                                                            this.rawReduction = total;
                                                            this.displayReduction = formatRupiahJS(total);
                                                        }
                                                      }">
                                                    @csrf
                                                    <input type="hidden" name="order_material_id" value="{{ $om->id }}">
                                                    
                                                    <div class="space-y-1">
                                                        <label class="text-[10px] font-bold text-slate-500 uppercase">Tipe Residu</label>
                                                        <select name="type" x-model="type" class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-xs">
                                                            <option value="REUSABLE">REUSABLE (Nambah Stok & Kurangi HPP)</option>
                                                            <option value="RECYCLE">RECYCLE (Kurangi HPP Saja)</option>
                                                            <option value="WASTE">WASTE (Hanya Catatan)</option>
                                                        </select>
                                                    </div>
                                                    <div class="space-y-1">
                                                        <label class="text-[10px] font-bold text-slate-500 uppercase">Qty Sisa ({{ $om->material->unit }})</label>
                                                        <input type="number" name="qty" x-model="qty" @input="validateQty()" step="any" min="0.01" :max="maxQty" required class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-xs" placeholder="0.00">
                                                    </div>
                                                    <div class="space-y-1">
                                                        <label class="text-[10px] font-bold text-slate-500 uppercase">Nilai Reduksi (Rp)</label>
                                                        <input type="text" 
                                                               x-model="displayReduction"
                                                               x-on:input="displayReduction = formatRupiahJS($event.target.value); rawReduction = displayReduction.replace(/\./g, '')"
                                                               class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-xs font-data-mono"
                                                               placeholder="0">
                                                        <input type="hidden" name="reduction_value" x-model="rawReduction">
                                                    </div>
                                                    <div class="space-y-1">
                                                        <label class="text-[10px] font-bold text-slate-500 uppercase">Catatan</label>
                                                        <input type="text" name="description" placeholder="Misal: Sisa 2m bagus" class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-xs">
                                                    </div>
                                                    <div>
                                                        <button type="submit" class="w-full bg-primary text-white py-1.5 rounded-lg text-xs font-bold hover:opacity-90 transition-colors">Simpan Residu</button>
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                        @endif
                                        @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">Belum ada bahan yang dialokasikan.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Residue List Section -->
                        @if($order->residues->count() > 0)
                        <div class="space-y-3">
                            <h4 class="text-xs font-label-caps text-slate-500 uppercase tracking-widest flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px]">recycling</span>
                                <span>Daftar Residu / Sisa Material</span>
                            </h4>
                            <div class="bg-surface-container-low border border-surface-variant rounded-xl overflow-hidden shadow-sm">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-slate-50/50 border-b border-surface-variant">
                                        <tr>
                                            <th class="px-6 py-3 font-label-caps text-slate-500 uppercase text-[9px]">Material</th>
                                            <th class="px-6 py-3 font-label-caps text-slate-500 uppercase text-[9px]">Tipe</th>
                                            <th class="px-6 py-3 font-label-caps text-slate-500 uppercase text-[9px] text-right">Qty</th>
                                            <th class="px-6 py-3 font-label-caps text-slate-500 uppercase text-[9px] text-right">Potongan HPP</th>
                                            <th class="px-6 py-3 font-label-caps text-slate-500 uppercase text-[9px]">Catatan</th>
                                            <th class="px-6 py-3 font-label-caps text-slate-500 uppercase text-[9px] text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-surface-variant/30 font-body-sm text-[12px] text-on-surface bg-white/30">
                                        @foreach($order->residues as $res)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-6 py-3 font-medium">{{ $res->material->name }}</td>
                                            <td class="px-6 py-3">
                                                @if($res->type === 'REUSABLE')
                                                    <span class="inline-block whitespace-nowrap px-2 py-0.5 rounded-full bg-green-50 text-green-700 text-[9px] font-bold border border-green-100 uppercase">REUSABLE</span>
                                                @elseif($res->type === 'RECYCLE')
                                                    <span class="inline-block whitespace-nowrap px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[9px] font-bold border border-blue-100 uppercase">RECYCLE</span>
                                                @else
                                                    <span class="inline-block whitespace-nowrap px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[9px] font-bold border border-slate-200 uppercase">WASTE</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-3 text-right font-data-mono text-slate-500">{{ formatQty($res->qty) }} {{ $res->material->unit }}</td>
                                            <td class="px-6 py-3 text-right font-data-mono text-primary font-bold">
                                                {{ $res->type !== 'WASTE' ? '- ' . formatRupiah($res->reduction_value) : 'Rp 0' }}
                                            </td>
                                            <td class="px-6 py-3 text-slate-500 italic">{{ $res->description ?? '-' }}</td>
                                            <td class="px-6 py-3 text-right">
                                                @if($order->status == 'IN_PRODUCTION')
                                                <form id="removeResidue-{{ $res->id }}" action="{{ route('orders.remove-residue', $res) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" onclick="confirmAction('removeResidue-{{ $res->id }}', 'Hapus data residu?', 'HPP dan stok akan disesuaikan kembali.', 'warning', 'Ya, Hapus')" class="text-slate-400 hover:text-error transition-colors">
                                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                                    </button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Costs Tab -->
                    <div x-show="activeTab === 'costs'" class="space-y-6">
                        @if(in_array($order->status, ['IN_PRODUCTION', 'DELIVERING']))
                        <form action="{{ route('orders.add-cost', $order) }}" method="POST" class="bg-surface-container-high/30 p-4 rounded-xl border border-slate-200 grid grid-cols-1 md:grid-cols-4 gap-4 items-end"
                              x-data="{ displayAmount: '', rawAmount: '' }">
                            @csrf
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-label-caps text-slate-500 uppercase">Tipe</label>
                                <select name="type" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 text-sm">
                                    <option value="LABOR">Tenaga Kerja / Lembur</option>
                                    <option value="RETAIL_MATERIAL">Bahan Eceran / Tambahan (Paku, Lem, dll)</option>
                                    <option value="OTHER">Lainnya</option>
                                </select>
                            </div>
                            <div class="space-y-1.5 md:col-span-2">
                                <label class="text-[11px] font-label-caps text-slate-500 uppercase">Deskripsi</label>
                                <input type="text" name="description" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 text-sm" placeholder="Misal: Paku 2 inch 10 biji">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-label-caps text-slate-500 uppercase">Jumlah (Rp)</label>
                                <input type="text" 
                                       x-model="displayAmount"
                                       x-on:input="displayAmount = formatRupiahJS($event.target.value); rawAmount = displayAmount.replace(/\./g, '')"
                                       required 
                                       class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 text-sm" 
                                       placeholder="0">
                                <input type="hidden" name="amount" x-model="rawAmount">
                            </div>
                            <button type="submit" class="bg-primary text-white py-2 rounded-lg font-semibold text-sm hover:opacity-90 transition-colors md:col-span-4">Tambah Biaya</button>
                        </form>
                        @endif

                        <div class="bg-surface-container-low border border-surface-variant rounded-xl overflow-hidden shadow-sm">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-surface-container-high/50 border-b border-surface-variant">
                                        <tr>
                                            <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Tipe</th>
                                            <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Deskripsi</th>
                                            <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Jumlah</th>
                                            <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-surface-variant/30 font-body-sm text-body-sm text-on-surface bg-white/50">
                                        @forelse($order->productionCosts as $cost)
                                        <tr class="hover:bg-surface-container-low transition-colors group">
                                            <td class="px-6 py-4">
                                                @if($cost->type == 'LABOR') <span class="inline-block whitespace-nowrap px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-bold border border-blue-100 uppercase">Tenaga Kerja</span>
                                                @elseif($cost->type == 'RETAIL_MATERIAL') <span class="inline-block whitespace-nowrap px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold border border-slate-200 uppercase">Bahan Eceran</span>
                                                @elseif($cost->type == 'DELIVERY') <span class="inline-block whitespace-nowrap px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold border border-amber-100 uppercase">Pengantaran</span>
                                                @else <span class="inline-block whitespace-nowrap px-2 py-0.5 rounded-full bg-slate-50 text-slate-500 text-[10px] font-bold border border-slate-100 uppercase">Lainnya</span> @endif
                                            </td>
                                            <td class="px-6 py-4 text-on-surface">{{ $cost->description }}</td>
                                            <td class="px-6 py-4 text-right font-data-mono font-bold text-on-surface">{{ formatRupiah($cost->amount) }}</td>
                                            <td class="px-6 py-4 text-right">
                                                @if(in_array($order->status, ['IN_PRODUCTION', 'DELIVERING']))
                                                <form id="removeCost-{{ $cost->id }}" action="{{ route('orders.remove-cost', $cost) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" onclick="confirmAction('removeCost-{{ $cost->id }}', 'Hapus biaya ini?', 'Biaya akan dihapus dan total HPP akan dihitung ulang.', 'warning', 'Ya, Hapus')" class="text-slate-400 hover:text-error transition-colors">
                                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                                    </button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">Belum ada biaya tambahan.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Payments Tab -->
                    <div x-show="activeTab === 'payments'" class="space-y-6">
                        @if($order->isPayable())
                        @php
                            $hasDP = $order->payments->where('type', 'DP')->count() > 0;
                            $totalPaid = $order->payments->sum('amount');
                            $remainingPayment = $order->selling_price - $totalPaid;
                        @endphp

                        <!-- Info Sisa Tagihan -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-4 rounded-xl border {{ $remainingPayment > 0 ? 'bg-amber-50/50 border-amber-200' : 'bg-emerald-50/50 border-emerald-200' }}">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[20px] {{ $remainingPayment > 0 ? 'text-amber-600' : 'text-emerald-600' }}">{{ $remainingPayment > 0 ? 'account_balance_wallet' : 'check_circle' }}</span>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest {{ $remainingPayment > 0 ? 'text-amber-600' : 'text-emerald-600' }}">Sisa Tagihan</p>
                                    <p class="text-lg font-bold {{ $remainingPayment > 0 ? 'text-amber-700' : 'text-emerald-700' }}">{{ formatRupiah($remainingPayment) }}</p>
                                </div>
                            </div>
                            <div class="sm:ml-auto text-right">
                                <p class="text-[10px] text-slate-500">Sudah dibayar: <span class="font-bold text-emerald-600">{{ formatRupiah($totalPaid) }}</span> dari <span class="font-bold text-on-surface">{{ formatRupiah($order->selling_price) }}</span></p>
                            </div>
                        </div>

                        <form action="{{ route('orders.pay', $order) }}" method="POST" class="bg-surface-container-high/30 p-4 pb-8 rounded-xl border border-slate-200 flex flex-col md:flex-row gap-4 md:items-end items-stretch"
                              x-data="{ displayPay: '', rawPay: '', maxAmount: {{ (int)$remainingPayment }}, overLimit: false }">
                            @csrf
                            <div class="space-y-1.5 md:w-48 shrink-0">
                                <label class="text-[11px] font-label-caps text-slate-500 uppercase">Tipe <span class="text-error">*</span></label>
                                @if($hasDP)
                                    <div class="w-full bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg px-3 py-2 text-sm font-bold flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[16px]">paid</span>
                                        Pelunasan
                                    </div>
                                    <input type="hidden" name="type" value="FINAL">
                                @else
                                    <select name="type" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 text-sm @error('type') border-error @enderror">
                                        <option value="DP" {{ old('type') == 'DP' ? 'selected' : '' }}>Uang Muka (DP)</option>
                                        <option value="FINAL" {{ old('type') == 'FINAL' ? 'selected' : '' }}>Pelunasan</option>
                                    </select>
                                @endif
                                @error('type') <p class="text-[10px] text-error mt-0.5">{{ $message }}</p> @enderror
                            </div>
                            <div class="flex-1 space-y-1.5">
                                <label class="text-[11px] font-label-caps text-slate-500 uppercase">Jumlah (Rp) <span class="text-error">*</span></label>
                                <div class="relative">
                                    <input type="text" 
                                           x-model="displayPay"
                                           x-on:input="
                                               displayPay = formatRupiahJS($event.target.value);
                                               rawPay = displayPay.replace(/\./g, '');
                                               if (parseInt(rawPay) > maxAmount) {
                                                   rawPay = maxAmount.toString();
                                                   displayPay = formatRupiahJS(rawPay);
                                                   overLimit = true;
                                                   setTimeout(() => overLimit = false, 2000);
                                               }
                                           "
                                           required 
                                           class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 text-sm @error('amount') border-error @enderror" 
                                           placeholder="Maks. {{ number_format($remainingPayment, 0, ',', '.') }}">
                                    <input type="hidden" name="amount" x-model="rawPay">
                                    <p x-show="overLimit" x-transition class="absolute left-0 top-full mt-1 text-[10px] text-error flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">warning</span>
                                        Maksimal sisa tagihan: Rp {{ number_format($remainingPayment, 0, ',', '.') }}
                                    </p>
                                </div>
                                @error('amount') <p class="text-[10px] text-error mt-0.5">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-1.5 shrink-0">
                                <label class="text-[11px] font-label-caps text-transparent uppercase select-none">&nbsp;</label>
                                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg font-semibold text-sm hover:opacity-90 transition-colors whitespace-nowrap w-full">Tambah Pembayaran</button>
                            </div>
                        </form>
                        @endif

                        <div class="bg-surface-container-low border border-surface-variant rounded-xl overflow-hidden shadow-sm">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-surface-container-high/50 border-b border-surface-variant">
                                        <tr>
                                            <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Tanggal</th>
                                            <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Tipe</th>
                                            <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-surface-variant/30 font-body-sm text-body-sm text-on-surface bg-white/50">
                                        @forelse($order->payments as $payment)
                                        <tr class="hover:bg-surface-container-low transition-colors group">
                                            <td class="px-6 py-4 text-slate-500">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                                            <td class="px-6 py-4">
                                                <span class="inline-block whitespace-nowrap px-2.5 py-1 rounded-full {{ $payment->type == 'DP' ? 'bg-amber-50 text-amber-700 border-amber-100' : ($payment->type == 'INSTALLMENT' ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-emerald-50 text-emerald-700 border-emerald-100') }} text-[10px] font-bold border uppercase">
                                                    {{ $payment->type_label }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right font-data-mono font-bold text-on-surface">{{ formatRupiah($payment->amount) }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-12 text-center text-slate-400 italic">Belum ada pembayaran.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / Lifecycle -->
        <div class="space-y-6">
            <div class="glass-panel p-6 rounded-xl border border-slate-200">
                <h4 class="text-on-surface font-semibold mb-6 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/></svg>
                    Siklus Proyek
                </h4>
                <div class="space-y-6 relative">
                    <!-- Vertical Line -->
                    <div class="absolute left-[13.5px] top-2 bottom-2 w-0.5 bg-slate-100"></div>
                    
                    <!-- Step 1: Pending -->
                    <div class="relative flex items-start gap-4">
                        <div class="w-7 h-7 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center z-10 shrink-0 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600"><path d="M20 6L9 17l-5-5"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-on-surface">Pesanan Diterima</p>
                            <p class="text-[11px] text-slate-500">{{ $order->created_at->format('d M Y') }}</p>
                        </div>
                    </div>

                    <!-- Step 2: Production -->
                    <div class="relative flex items-start gap-4">
                        <div class="w-7 h-7 rounded-full {{ in_array($order->status, ['IN_PRODUCTION', 'DELIVERING', 'UNPAID_DELIVERED', 'FINISHED']) ? 'bg-primary-fixed border border-primary/30' : 'bg-slate-50 border border-slate-200' }} flex items-center justify-center z-10 shrink-0 shadow-sm">
                            @if(in_array($order->status, ['IN_PRODUCTION', 'DELIVERING', 'UNPAID_DELIVERED', 'FINISHED']))
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M2 20V9l4-2 4 2 4-2 4 2 4-2v11a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2z"/><path d="M17 18h1"/><path d="M12 18h1"/><path d="M7 18h1"/></svg>
                            @else
                                <div class="w-1.5 h-1.5 rounded-full bg-slate-300"></div>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-semibold {{ in_array($order->status, ['IN_PRODUCTION', 'DELIVERING', 'UNPAID_DELIVERED', 'FINISHED']) ? 'text-on-surface' : 'text-slate-500' }}">Produksi</p>
                            @if($order->status == 'IN_PRODUCTION')
                                <p class="text-[11px] text-primary animate-pulse font-bold">Sedang Berjalan...</p>
                            @elseif(in_array($order->status, ['DELIVERING', 'UNPAID_DELIVERED', 'FINISHED']))
                                <p class="text-[11px] text-emerald-600 font-bold">Selesai</p>
                            @else
                                <p class="text-[11px] text-slate-500 font-medium">Menunggu...</p>
                            @endif
                        </div>
                    </div>

                    <!-- Step 3: Delivering -->
                    <div class="relative flex items-start gap-4">
                        <div class="w-7 h-7 rounded-full {{ in_array($order->status, ['DELIVERING', 'UNPAID_DELIVERED', 'FINISHED']) ? 'bg-amber-50 border border-amber-200' : 'bg-slate-50 border border-slate-200' }} flex items-center justify-center z-10 shrink-0 shadow-sm">
                            @if(in_array($order->status, ['DELIVERING', 'UNPAID_DELIVERED', 'FINISHED']))
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-600"><path d="M10 17h4V5H2v12h3m1 0a2 2 0 1 0 4 0 2 2 0 0 0-4 0m10 0a2 2 0 1 0 4 0 2 2 0 0 0-4 0m-3 0h1m2 0h3l1-4h-7z"/></svg>
                            @else
                                <div class="w-1.5 h-1.5 rounded-full bg-slate-300"></div>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-semibold {{ in_array($order->status, ['DELIVERING', 'UNPAID_DELIVERED', 'FINISHED']) ? 'text-on-surface' : 'text-slate-500' }}">Pengantaran</p>
                            @if($order->status == 'DELIVERING')
                                <p class="text-[11px] text-amber-600 animate-pulse font-bold">Dalam Perjalanan...</p>
                            @elseif(in_array($order->status, ['UNPAID_DELIVERED', 'FINISHED']))
                                <p class="text-[11px] text-emerald-600 font-bold">Sampai Tujuan</p>
                            @else
                                <p class="text-[11px] text-slate-500 font-medium">Dijadwalkan</p>
                            @endif
                        </div>
                    </div>

                    <!-- Step 4: Payment/Debt (Only shows if relevant or finished) -->
                    <div class="relative flex items-start gap-4">
                        <div class="w-7 h-7 rounded-full {{ $order->status == 'UNPAID_DELIVERED' ? 'bg-error-container border border-error/30' : ($order->status == 'FINISHED' ? 'bg-emerald-50 border border-emerald-200' : 'bg-slate-50 border border-slate-200') }} flex items-center justify-center z-10 shrink-0 shadow-sm">
                            @if($order->status == 'UNPAID_DELIVERED')
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-error"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            @elseif($order->status == 'FINISHED')
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600"><path d="M20 6L9 17l-5-5"/></svg>
                            @else
                                <div class="w-1.5 h-1.5 rounded-full bg-slate-300"></div>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-semibold {{ in_array($order->status, ['UNPAID_DELIVERED', 'FINISHED']) ? 'text-on-surface' : 'text-slate-500' }}">Status Pelunasan</p>
                            @if($order->status == 'UNPAID_DELIVERED')
                                <p class="text-[11px] text-error font-bold">Hutang: {{ formatRupiah($order->remaining_payment) }}</p>
                            @elseif($order->status == 'FINISHED')
                                <p class="text-[11px] text-emerald-600 font-bold">Lunas</p>
                            @else
                                <p class="text-[11px] text-slate-500 font-medium">Menunggu Pelunasan</p>
                            @endif
                        </div>
                    </div>

                    <!-- Step 5: Finished -->
                    <div class="relative flex items-start gap-4">
                        <div class="w-7 h-7 rounded-full {{ $order->status == 'FINISHED' ? 'bg-primary border border-primary/30' : 'bg-slate-50 border border-slate-200' }} flex items-center justify-center z-10 shrink-0 shadow-sm">
                            @if($order->status == 'FINISHED')
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-white"><path d="M20 6L9 17l-5-5"/></svg>
                            @else
                                <div class="w-1.5 h-1.5 rounded-full bg-slate-300"></div>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-semibold {{ $order->status == 'FINISHED' ? 'text-on-surface' : 'text-slate-500' }}">Proyek Ditutup</p>
                            <p class="text-[11px] text-slate-500">{{ $order->status == 'FINISHED' ? 'Semua Tahapan Selesai' : 'Tahap Akhir' }}</p>
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

<script>
    function confirmAction(formId, title, text, icon, confirmButtonText) {
        // Determine colors based on icon/action type
        let confirmButtonColor = '#3085d6'; // Default Blue
        if (icon === 'warning') confirmButtonColor = '#f59e0b'; // Amber/Orange for Start/Warning
        if (confirmButtonText.toLowerCase().includes('hapus')) confirmButtonColor = '#ef4444'; // Red for Delete
        if (icon === 'success' || icon === 'question') confirmButtonColor = '#10b981'; // Green for Success/Confirm

        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: confirmButtonColor,
            cancelButtonColor: '#64748b', // Slate for Cancel
            confirmButtonText: confirmButtonText,
            cancelButtonText: 'Batal',
            background: '#ffffff',
            color: '#0f172a',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'rounded-lg px-6 py-2 font-semibold',
                cancelButton: 'rounded-lg px-6 py-2 font-semibold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }

    function cancelOrder() {
        Swal.fire({
            title: 'Batalkan pesanan ini?',
            text: 'Tindakan ini tidak bisa dibatalkan. Stok yang sudah dipakai akan dikembalikan.',
            icon: 'warning',
            input: 'textarea',
            inputLabel: 'Alasan Pembatalan (opsional)',
            inputPlaceholder: 'Misal: Pelanggan membatalkan pesanan...',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Kembali',
            background: '#ffffff',
            color: '#0f172a',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'rounded-lg px-6 py-2 font-semibold',
                cancelButton: 'rounded-lg px-6 py-2 font-semibold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('cancelOrderForm');
                const reasonInput = form.querySelector('input[name="cancel_reason"]');
                if (reasonInput) reasonInput.value = result.value || '';
                form.submit();
            }
        });
    }
</script>
