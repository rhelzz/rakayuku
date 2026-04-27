@extends('layouts.app')

@section('title', 'Material History - ' . $material->name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumbs & Header -->
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('materials.index') }}" class="hover:text-primary-container transition-colors">Inventory</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Material History</span>
        </nav>
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-xl bg-surface-container-high flex items-center justify-center text-primary-container border border-surface-variant">
                    <span class="material-symbols-outlined text-3xl">inventory_2</span>
                </div>
                <div>
                    <h2 class="font-headline-md text-headline-md text-on-surface">{{ $material->name }}</h2>
                    <p class="font-body-sm text-body-sm text-slate-400">SKU: {{ $material->id }} | Unit: {{ $material->unit }}</p>
                </div>
            </div>
            
            <div class="flex items-center gap-6 px-6 py-3 bg-surface-container-low border border-surface-variant rounded-xl">
                <div class="text-center">
                    <p class="text-[10px] font-label-caps text-slate-500 uppercase tracking-widest">Current Stock</p>
                    <p class="text-xl font-bold text-on-surface">{{ number_format($material->qty) }} <span class="text-xs font-normal text-slate-500">{{ $material->unit }}</span></p>
                </div>
                <div class="w-px h-8 bg-surface-variant"></div>
                <div class="text-center">
                    <p class="text-[10px] font-label-caps text-slate-500 uppercase tracking-widest">Average HPP</p>
                    <p class="text-xl font-bold text-primary-container">Rp {{ number_format($material->avg_price, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="bg-surface-container-low border border-surface-variant rounded-xl flex flex-col overflow-hidden">
        <div class="p-4 border-b border-surface-variant bg-surface-container-lowest/50">
            <h3 class="font-title-sm text-title-sm text-on-surface">Stock Movement History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-surface-container-high bg-surface-container-low/50">
                        <th class="p-3 px-4 font-label-caps text-label-caps text-slate-400 uppercase">Date</th>
                        <th class="p-3 px-4 font-label-caps text-label-caps text-slate-400 uppercase">Type</th>
                        <th class="p-3 px-4 font-label-caps text-label-caps text-slate-400 uppercase text-right">Quantity</th>
                        <th class="p-3 px-4 font-label-caps text-label-caps text-slate-400 uppercase">Reference</th>
                        <th class="p-3 px-4 font-label-caps text-label-caps text-slate-400 uppercase">Details</th>
                    </tr>
                </thead>
                <tbody class="font-body-sm text-body-sm">
                    @forelse($movements as $movement)
                        <tr class="border-b border-surface-container-high hover:bg-surface-container/50 transition-colors">
                            <td class="p-3 px-4 text-on-surface-variant">
                                {{ $movement->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="p-3 px-4">
                                @if($movement->type == 'IN')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-[#112a1f] text-[#4ade80] border border-[#1e4a33] text-xs font-medium">
                                        <span class="material-symbols-outlined text-[14px]">arrow_downward</span> STOCK IN
                                    </span>
                                @elseif($movement->type == 'OUT')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-error-container/20 text-error border border-error/30 text-xs font-medium">
                                        <span class="material-symbols-outlined text-[14px]">arrow_upward</span> STOCK OUT
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-surface-container-high text-slate-400 border border-surface-variant text-xs font-medium">
                                        <span class="material-symbols-outlined text-[14px]">tune</span> ADJUSTMENT
                                    </span>
                                @endif
                            </td>
                            <td class="p-3 px-4 text-right font-data-mono font-bold {{ $movement->type == 'IN' ? 'text-[#4ade80]' : 'text-error' }}">
                                {{ $movement->type == 'IN' ? '+' : '-' }}{{ number_format($movement->qty) }}
                            </td>
                            <td class="p-3 px-4 text-on-surface-variant">
                                @if($movement->reference_type)
                                    <span class="px-2 py-0.5 bg-surface-container-high rounded text-[10px] text-slate-300 uppercase font-bold border border-surface-variant">
                                        {{ class_basename($movement->reference_type) }} #{{ $movement->reference_id }}
                                    </span>
                                @else
                                    <span class="text-slate-500 italic">Manual</span>
                                @endif
                            </td>
                            <td class="p-3 px-4 text-on-surface-variant">
                                @if($movement->reference_type == 'App\Models\Purchase')
                                    Purchase from supplier
                                @elseif($movement->reference_type == 'App\Models\Order')
                                    Used in production for Order #{{ $movement->reference_id }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-10 text-center text-slate-500 italic">No stock movements recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($movements->hasPages())
            <div class="p-4 border-t border-surface-variant bg-surface-container-lowest/30">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
