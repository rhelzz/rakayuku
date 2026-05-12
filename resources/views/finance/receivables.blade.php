@extends('layouts.app')

@section('title', 'Rincian Piutang Customer')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('finance.index') }}" class="hover:text-primary transition-colors">Keuangan</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Piutang Customer</span>
        </nav>

        <div class="flex justify-between items-end">
            <div>
                <h2 class="font-headline-md text-headline-md text-on-surface mb-1">Piutang (Receivables)</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Daftar tagihan pembayaran pesanan yang belum dilunasi oleh customer.</p>
            </div>
            <div class="text-right">
                <p class="text-xs font-bold text-slate-500 uppercase">Total Piutang Aktif</p>
                <p class="text-3xl font-black text-emerald-600 font-data-mono">{{ formatRupiah($orders->sum(fn($o) => $o->remaining_payment)) }}</p>
            </div>
        </div>
    </div>

    <div class="glass-panel border border-surface-variant rounded-2xl overflow-hidden bg-white shadow-sm">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 text-[10px] font-label-caps text-slate-500 uppercase">Customer / Pesanan</th>
                    <th class="px-6 py-4 text-[10px] font-label-caps text-slate-500 uppercase text-right">Total Tagihan</th>
                    <th class="px-6 py-4 text-[10px] font-label-caps text-slate-500 uppercase text-right">Telah Dibayar</th>
                    <th class="px-6 py-4 text-[10px] font-label-caps text-slate-500 uppercase text-right">Sisa Piutang</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($orders as $order)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-700">{{ $order->customer->name }}</div>
                        <div class="text-[11px] text-slate-500">{{ $order->project_name }}</div>
                        <div class="text-[10px] text-slate-400 font-data-mono">{{ $order->order_number }}</div>
                    </td>
                    <td class="px-6 py-4 text-right font-data-mono text-slate-600 text-sm">
                        {{ formatRupiah($order->selling_price) }}
                    </td>
                    <td class="px-6 py-4 text-right font-data-mono text-emerald-600 text-sm">
                        {{ formatRupiah($order->total_paid) }}
                    </td>
                    <td class="px-6 py-4 text-right font-data-mono font-bold text-error">
                        {{ formatRupiah($order->remaining_payment) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-slate-400 italic">Tidak ada piutang aktif.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
