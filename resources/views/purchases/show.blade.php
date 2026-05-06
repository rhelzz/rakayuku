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
                                <td class="py-4 px-4 text-right font-data-mono text-slate-500">{{ number_format($item->qty, 0, ',', '.') }}</td>
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
        </div>

        <!-- Sidebar: Invoice Proof -->
        <div class="space-y-6">
            <div class="bg-surface-container-low border border-surface-variant rounded-2xl shadow-sm overflow-hidden h-fit sticky top-6">
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
