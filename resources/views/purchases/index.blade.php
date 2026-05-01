@extends('layouts.app')

@section('title', 'Riwayat Pembelian & Invoice')
@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Pembelian</span>
        </nav>

        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
            <div>
                <h1 class="font-headline-md text-headline-md text-on-background">Riwayat Pembelian</h1>
                <p class="font-body-sm text-body-sm text-slate-400 mt-1">Daftar invoice belanja bahan baku dan operasional.</p>
            </div>
            <a href="{{ route('purchases.create') }}" class="px-6 py-2.5 bg-primary text-white rounded-xl font-bold hover:bg-primary-hover shadow-lg shadow-primary/20 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
                Catat Invoice Baru
            </a>
        </div>
    <!-- Purchases Table -->
    <div class="bg-surface-container-low border border-surface-variant rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-lowest border-b border-surface-variant">
                    <tr>
                        <th class="px-6 py-4 font-label-caps text-slate-400 uppercase text-[10px] tracking-widest w-10 text-center">No</th>
                        <th class="px-6 py-4 font-label-caps text-slate-400 uppercase text-[10px] tracking-widest">Tanggal</th>
                        <th class="px-6 py-4 font-label-caps text-slate-400 uppercase text-[10px] tracking-widest">Nomor Invoice</th>
                        <th class="px-6 py-4 font-label-caps text-slate-400 uppercase text-[10px] tracking-widest">Pemasok</th>
                        <th class="px-6 py-4 font-label-caps text-slate-400 uppercase text-[10px] tracking-widest text-right">Total Belanja</th>
                        <th class="px-6 py-4 font-label-caps text-slate-400 uppercase text-[10px] tracking-widest text-center">Bukti</th>
                        <th class="px-6 py-4 font-label-caps text-slate-400 uppercase text-[10px] tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-variant/30 font-body-sm text-body-sm text-on-surface">
                    @forelse($purchases as $p)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4 text-center font-data-mono text-slate-400">{{ $purchases->firstItem() + $loop->index }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                            {{ \Carbon\Carbon::parse($p->purchase_date)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 font-bold text-primary">
                            #{{ $p->invoice_number ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 font-medium">
                            {{ $p->supplier_name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-right font-data-mono font-bold">
                            Rp {{ number_format($p->total_price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($p->invoice_proof)
                                <span class="material-symbols-outlined text-emerald-500 text-[20px]" title="Ada Lampiran">image</span>
                            @else
                                <span class="material-symbols-outlined text-slate-200 text-[20px]">no_photography</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('purchases.show', $p) }}" class="inline-flex items-center gap-1.5 text-primary hover:underline font-bold">
                                Detail <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400 italic">
                            <div class="flex flex-col items-center gap-2">
                                <span class="material-symbols-outlined text-4xl opacity-20">receipt_long</span>
                                Belum ada riwayat pembelian tercatat.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($purchases->hasPages())
            <div class="p-4 border-t border-surface-variant bg-surface-container-lowest/50">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
