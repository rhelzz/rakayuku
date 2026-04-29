@extends('layouts.app')

@section('title', 'Riwayat Pembelian')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline-md text-headline-md text-on-surface mb-1">Pembelian & Penerimaan</h2>
            <p class="font-body-sm text-body-sm text-slate-400">Lacak penerimaan bahan baku masuk dan biayanya.</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('purchases.create') }}" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg font-body-sm text-body-sm font-semibold hover:bg-primary transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                <span>Penerimaan Baru</span>
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-surface-container-low border border-surface-variant rounded-xl flex flex-col overflow-hidden shadow-sm">
        <div class="p-4 border-b border-surface-variant flex justify-between items-center bg-surface-container-lowest/50">
            <h3 class="font-title-sm text-title-sm text-on-surface">Catatan Pembelian</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase border-b border-surface-variant">Tanggal</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase border-b border-surface-variant">Nomor Faktur</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase border-b border-surface-variant">Pemasok</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase border-b border-surface-variant text-right">Total Harga</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase border-b border-surface-variant text-center">Item</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase border-b border-surface-variant text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-variant font-body-sm text-body-sm text-on-surface">
                    @forelse($purchases as $p)
                    <tr class="hover:bg-surface-container-high/50 transition-colors group">
                        <td class="px-4 py-3 text-on-surface-variant font-data-mono">{{ \Carbon\Carbon::parse($p->purchase_date)->format('d M Y') }}</td>
                        <td class="px-4 py-3 font-medium text-on-surface">{{ $p->invoice_number ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $p->supplier_name ?? 'Input Manual' }}</td>
                        <td class="px-4 py-3 text-right font-bold text-on-primary-container">Rp {{ number_format($p->total_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 bg-surface-container-high rounded text-[10px] font-bold border border-surface-variant">
                                {{ $p->items->count() }} Item
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('purchases.show', $p) }}" class="text-slate-400 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500 italic">Tidak ada catatan pembelian ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
