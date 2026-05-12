@extends('layouts.app')

@section('title', 'Detail Invoice Pembelian')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Breadcrumbs -->
    <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <a href="{{ route('purchases.index') }}" class="hover:text-primary transition-colors">Pembelian</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface">Detail Invoice</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-surface-container-low border border-surface-variant rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-surface-variant bg-surface-container-lowest/50 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-2xl">receipt_long</span>
                        </div>
                        <div>
                            <h3 class="font-headline-sm text-on-surface">Invoice #{{ $purchase->invoice_number ?? 'Tanpa Nomor' }}</h3>
                            <p class="text-[11px] text-slate-500 uppercase font-bold tracking-wider">{{ $purchase->supplier_name ?? 'Pemasok Umum' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-label-caps text-slate-400 uppercase">Tanggal Belanja</p>
                        <p class="text-sm font-bold text-on-surface">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</p>
                    </div>
                </div>

                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-surface-container-high/50 border-b border-surface-variant">
                            <tr>
                                <th class="py-4 px-6 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Bahan Baku</th>
                                <th class="py-4 px-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Jumlah</th>
                                <th class="py-4 px-6 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Harga Satuan</th>
                                <th class="py-4 px-6 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-variant/30 font-body-sm text-body-sm text-on-surface bg-white/50">
                            @foreach($purchase->items as $item)
                            <tr class="hover:bg-surface-container-low transition-colors group">
                                <td class="py-4 px-6 font-medium text-on-surface">{{ $item->material->name }}{{ $item->material->type ? ' (' . $item->material->type . ')' : '' }}</td>
                                <td class="py-4 px-4 text-right font-data-mono text-slate-500">{{ formatQty($item->qty) }} {{ $item->material->unit }}</td>
                                <td class="py-4 px-6 text-right font-data-mono text-slate-500">{{ formatRupiah($item->price) }}</td>
                                <td class="py-4 px-6 text-right font-data-mono font-bold text-on-surface">{{ formatRupiah($item->subtotal) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-surface-container-high/30 font-bold border-t border-surface-variant">
                            <tr>
                                <td colspan="3" class="py-4 px-6 text-sm text-slate-600 text-right">Total Pembayaran</td>
                                <td class="py-4 px-6 text-right text-primary font-data-mono text-lg">
                                    {{ formatRupiah($purchase->total_price) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Payment Status & Form (for Credit Purchases) -->
            @if($purchase->payment_status !== 'PAID')
            <div class="bg-white border border-amber-200 rounded-2xl shadow-sm overflow-hidden p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div>
                        <h4 class="font-bold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-amber-600">account_balance_wallet</span>
                            Status Pembayaran Hutang
                        </h4>
                        <p class="text-[11px] text-slate-500">Invoice ini dibeli secara kredit/hutang.</p>
                    </div>
                    <div class="bg-amber-50 px-4 py-2 rounded-xl border border-amber-100 text-right">
                        <p class="text-[10px] font-bold text-amber-600 uppercase">Sisa Hutang</p>
                        <p class="text-xl font-bold text-amber-700 font-data-mono">{{ formatRupiah($purchase->total_price - $purchase->paid_amount) }}</p>
                    </div>
                </div>

                <form action="{{ route('purchases.pay', $purchase) }}" method="POST" class="bg-slate-50 p-4 rounded-xl border border-slate-200 grid grid-cols-1 md:grid-cols-3 gap-4 items-end"
                      x-data="{ displayAmount: '', rawAmount: '' }">
                    @csrf
                    <div class="space-y-1.5 md:col-span-2">
                        <label class="text-[11px] font-label-caps text-slate-500 uppercase">Jumlah Bayar (Rp)</label>
                        <input type="text" 
                               x-model="displayAmount"
                               x-on:input="displayAmount = formatRupiahJS($event.target.value); rawAmount = displayAmount.replace(/\./g, '')"
                               required 
                               class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 text-sm" 
                               placeholder="0">
                        <input type="hidden" name="amount" x-model="rawAmount">
                    </div>
                    <button type="submit" class="bg-primary text-white py-2 rounded-lg font-semibold text-sm hover:opacity-90 transition-colors">Bayar Hutang</button>
                </form>
            </div>
            @endif
        </div>

        <!-- Sidebar: Invoice Proof -->
        <div class="space-y-6">
            <div class="bg-surface-container-low border border-surface-variant rounded-2xl shadow-sm overflow-hidden h-fit sticky top-6">
                <div class="p-6 border-b border-surface-variant bg-surface-container-lowest/50">
                    <h3 class="font-headline-sm text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">info</span>
                        Status
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-slate-500">Status Bayar:</span>
                        <span class="inline-block whitespace-nowrap px-2 py-1 rounded-full {{ $purchase->payment_status === 'PAID' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-100' }} text-[10px] font-bold border uppercase">
                            {{ $purchase->payment_status === 'PAID' ? 'LUNAS' : ($purchase->payment_status === 'PARTIAL' ? 'DICICIL' : 'HUTANG') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-slate-500">Total:</span>
                        <span class="text-sm font-bold">{{ formatRupiah($purchase->total_price) }}</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-slate-100 pt-3">
                        <span class="text-xs text-slate-500">Terbayar:</span>
                        <span class="text-sm font-bold text-emerald-600">{{ formatRupiah($purchase->paid_amount) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-surface-container-low border border-surface-variant rounded-2xl shadow-sm overflow-hidden h-fit">
                <div class="p-6 border-b border-surface-variant bg-surface-container-lowest/50">
                    <h3 class="font-headline-sm text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">image</span>
                        Lampiran Bukti
                    </h3>
                </div>
                
                <div class="p-6">
                    @if($purchase->invoice_proof)
                        <div class="space-y-4">
                            <div class="rounded-xl overflow-hidden border border-slate-200 shadow-sm bg-slate-50 group relative">
                                <img src="{{ asset('storage/' . $purchase->invoice_proof) }}" class="w-full h-auto object-contain max-h-[400px]">
                                <a href="{{ asset('storage/' . $purchase->invoice_proof) }}" target="_blank" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white font-semibold gap-2">
                                    <span class="material-symbols-outlined">zoom_in</span> Lihat Penuh
                                </a>
                            </div>
                            <a href="{{ asset('storage/' . $purchase->invoice_proof) }}" download class="w-full py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl font-semibold hover:bg-slate-50 text-center flex items-center justify-center gap-2 text-sm transition-all">
                                <span class="material-symbols-outlined text-[18px]">download</span>
                                Unduh Gambar
                            </a>
                        </div>
                    @else
                        <div class="py-12 px-4 text-center border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50/50">
                            <span class="material-symbols-outlined text-4xl text-slate-300">no_photography</span>
                            <p class="text-xs text-slate-500 mt-2">Tidak ada bukti invoice yang diupload.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
