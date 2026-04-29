@extends('layouts.app')

@section('title', 'Purchase Detail - ' . ($purchase->invoice_number ?? $purchase->id))

@section('content')
<div class="space-y-6">
    <!-- Breadcrumbs & Header -->
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('purchases.index') }}" class="hover:text-primary transition-colors">Purchases</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Receipt Detail</span>
        </nav>
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-xl bg-surface-container-high flex items-center justify-center text-primary border border-surface-variant">
                    <span class="material-symbols-outlined text-3xl">receipt_long</span>
                </div>
                <div>
                    <h2 class="font-headline-md text-headline-md text-on-surface">{{ $purchase->invoice_number ?? 'Manual Receipt' }}</h2>
                    <p class="font-body-sm text-body-sm text-slate-500">Supplier: {{ $purchase->supplier_name ?? 'Manual Entry' }} | Date: {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</p>
                </div>
            </div>
            
            <div class="flex items-center gap-6 px-6 py-3 bg-surface-container-low border border-surface-variant rounded-xl">
                <div class="text-center">
                    <p class="text-[10px] font-label-caps text-slate-500 uppercase tracking-widest">Total Transaction</p>
                    <p class="text-xl font-bold text-primary">Rp {{ number_format($purchase->total_price, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-surface-container-low border border-surface-variant rounded-xl flex flex-col overflow-hidden">
        <div class="p-4 border-b border-surface-variant bg-surface-container-lowest/50">
            <h3 class="font-title-sm text-title-sm text-on-surface">Purchased Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-surface-container-high bg-surface-container-low/50">
                        <th class="p-3 px-4 font-label-caps text-label-caps text-slate-400 uppercase">Material</th>
                        <th class="p-3 px-4 font-label-caps text-label-caps text-slate-400 uppercase text-right">Quantity</th>
                        <th class="p-3 px-4 font-label-caps text-label-caps text-slate-400 uppercase text-right">Unit Price</th>
                        <th class="p-3 px-4 font-label-caps text-label-caps text-slate-400 uppercase text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="font-body-sm text-body-sm">
                    @foreach($purchase->items as $item)
                        <tr class="border-b border-surface-container-high hover:bg-surface-container/50 transition-colors">
                            <td class="p-3 px-4">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-slate-500">inventory_2</span>
                                    <span class="text-on-surface font-medium">{{ $item->material->name }}</span>
                                </div>
                            </td>
                            <td class="p-3 px-4 text-right text-on-surface-variant font-data-mono">
                                {{ number_format($item->qty) }} {{ $item->material->unit }}
                            </td>
                            <td class="p-3 px-4 text-right text-on-surface-variant font-data-mono">
                                Rp {{ number_format($item->price, 0, ',', '.') }}
                            </td>
                            <td class="p-3 px-4 text-right font-bold text-on-surface font-data-mono">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-surface-container-lowest/30">
                    <tr>
                        <td colspan="3" class="p-4 text-right font-label-caps text-slate-500 uppercase">Grand Total</td>
                        <td class="p-4 text-right text-lg font-black text-primary font-data-mono">
                            Rp {{ number_format($purchase->total_price, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
