@extends('layouts.app')

@section('title', 'Rincian Hutang Supplier')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('finance.index') }}" class="hover:text-primary transition-colors">Keuangan</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Hutang ke Supplier</span>
        </nav>

        <div class="flex justify-between items-end">
            <div>
                <h2 class="font-headline-md text-headline-md text-on-surface mb-1">Hutang (Payables)</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Daftar tagihan pembelian bahan baku yang belum dilunasi ke supplier.</p>
            </div>
            <div class="text-right">
                <p class="text-xs font-bold text-slate-500 uppercase">Total Hutang Aktif</p>
                <p class="text-3xl font-black text-error font-data-mono">{{ formatRupiah($purchases->sum(fn($p) => $p->total_price - $p->paid_amount)) }}</p>
            </div>
        </div>
    </div>

    <div class="glass-panel border border-surface-variant rounded-2xl overflow-hidden bg-white shadow-sm">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 text-[10px] font-label-caps text-slate-500 uppercase">Supplier / Invoice</th>
                    <th class="px-6 py-4 text-[10px] font-label-caps text-slate-500 uppercase">Tanggal</th>
                    <th class="px-6 py-4 text-[10px] font-label-caps text-slate-500 uppercase text-right">Total Tagihan</th>
                    <th class="px-6 py-4 text-[10px] font-label-caps text-slate-500 uppercase text-right">Telah Dibayar</th>
                    <th class="px-6 py-4 text-[10px] font-label-caps text-slate-500 uppercase text-right">Sisa Hutang</th>
                    <th class="px-6 py-4 text-[10px] font-label-caps text-slate-500 uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($purchases as $purchase)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-700">{{ $purchase->supplier_name ?? 'Supplier Umum' }}</div>
                        <div class="text-[10px] text-slate-400 font-data-mono">INV: {{ $purchase->invoice_number ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 text-right font-data-mono text-slate-600 text-sm">
                        {{ formatRupiah($purchase->total_price) }}
                    </td>
                    <td class="px-6 py-4 text-right font-data-mono text-emerald-600 text-sm">
                        {{ formatRupiah($purchase->paid_amount) }}
                    </td>
                    <td class="px-6 py-4 text-right font-data-mono font-bold text-error">
                        {{ formatRupiah($purchase->total_price - $purchase->paid_amount) }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('purchases.show', $purchase) }}"
                           class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-primary text-white hover:opacity-90 transition-colors">
                            Bayar
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-slate-400 italic">Tidak ada hutang aktif.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
