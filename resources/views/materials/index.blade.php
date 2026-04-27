@extends('layouts.app')

@section('title', 'Inventory & Procurement')

@section('content')
<div>
    <!-- Page Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline-md text-headline-md text-on-surface mb-1">Inventory & Procurement</h2>
            <p class="font-body-sm text-body-sm text-slate-400">Manage raw materials, stock levels, and quick inward processing.</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('materials.create') }}" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg font-body-sm text-body-sm font-semibold hover:bg-primary transition-colors flex items-center space-x-2">
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>New Material</span>
            </a>
        </div>
    </div>

<!-- Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-grid-gutter">
    <!-- Left Side: Inventory Table (8 cols) -->
    <div class="lg:col-span-8 flex flex-col gap-grid-gutter">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-surface-container-low border border-surface-variant rounded-xl p-4 flex items-center space-x-4">
                <div class="h-10 w-10 rounded-lg bg-surface-container-high flex items-center justify-center text-primary-container">
                    <span class="material-symbols-outlined">inventory</span>
                </div>
                <div>
                    <p class="font-label-caps text-label-caps text-slate-400 uppercase tracking-wider">Total Items</p>
                    <p class="font-title-sm text-title-sm text-on-surface mt-0.5">{{ $materials->count() }}</p>
                </div>
            </div>
            <div class="bg-surface-container-low border border-surface-variant rounded-xl p-4 flex items-center space-x-4">
                <div class="h-10 w-10 rounded-lg bg-[#332514] flex items-center justify-center text-[#ffb77d]">
                    <span class="material-symbols-outlined">warning</span>
                </div>
                <div>
                    <p class="font-label-caps text-label-caps text-slate-400 uppercase tracking-wider">Low Stock</p>
                    <p class="font-title-sm text-title-sm text-[#ffb77d] mt-0.5">{{ $materials->where('current_qty', '<', 5)->count() }} Items</p>
                </div>
            </div>
            <div class="bg-surface-container-low border border-surface-variant rounded-xl p-4 flex items-center space-x-4">
                <div class="h-10 w-10 rounded-lg bg-surface-container-high flex items-center justify-center text-slate-300">
                    <span class="material-symbols-outlined">payments</span>
                </div>
                <div>
                    <p class="font-label-caps text-label-caps text-slate-400 uppercase tracking-wider">Inventory Value</p>
                    <p class="font-title-sm text-title-sm text-on-surface mt-0.5">Rp {{ number_format($materials->sum(fn($m) => $m->current_qty * $m->avg_price), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="bg-surface-container-low border border-surface-variant rounded-xl flex flex-col overflow-hidden">
            <div class="p-4 border-b border-surface-variant flex justify-between items-center bg-surface-container-lowest/50">
                <h3 class="font-title-sm text-title-sm text-on-surface">Material Registry</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-surface-container sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase border-b border-surface-variant whitespace-nowrap">Material Name</th>
                            <th class="px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase border-b border-surface-variant text-right whitespace-nowrap">Qty on Hand</th>
                            <th class="px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase border-b border-surface-variant text-right whitespace-nowrap">Moving Avg HPP</th>
                            <th class="px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase border-b border-surface-variant text-center whitespace-nowrap">Status</th>
                            <th class="px-4 py-3 border-b border-surface-variant w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-variant font-body-sm text-body-sm text-on-surface">
                        @forelse($materials as $material)
                        <tr class="hover:bg-surface-container-high/50 transition-colors group">
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded bg-surface-container-high border border-surface-variant flex items-center justify-center text-slate-400 flex-shrink-0">
                                        <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-on-surface">{{ $material->name }}</div>
                                        <div class="text-slate-400 font-data-mono text-[11px]">ID: {{ $material->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right font-data-mono text-data-mono @if($material->current_qty < 5) text-error font-bold @endif">
                                {{ number_format($material->current_qty) }} <span class="text-slate-500 text-[11px] font-normal">{{ $material->unit }}</span>
                            </td>
                            <td class="px-4 py-3 text-right font-data-mono text-data-mono">
                                Rp {{ number_format($material->avg_price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($material->current_qty >= 5)
                                <div class="inline-flex items-center space-x-1.5 px-2 py-0.5 rounded-full bg-[#112a1f] text-[#4ade80] border border-[#1e4a33]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4ade80]"></span>
                                    <span class="text-[11px] font-medium">Optimal</span>
                                </div>
                                @else
                                <div class="inline-flex items-center space-x-1.5 px-2 py-0.5 rounded-full bg-[#332514] text-[#ffb77d] border border-[#4a361c]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#ffb77d] animate-pulse"></span>
                                    <span class="text-[11px] font-medium">Low Stock</span>
                                </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('materials.edit', $material) }}" class="text-slate-500 hover:text-primary-container">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500 italic">No materials found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Side: Quick Stock In Form (4 cols) -->
    <div class="lg:col-span-4 flex flex-col gap-grid-gutter">
        <div class="bg-surface-container-low border border-surface-variant rounded-xl overflow-hidden flex flex-col sticky top-4">
            <div class="p-5 border-b border-surface-variant bg-surface-container-lowest/50 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary-container/10 blur-2xl rounded-full -mr-10 -mt-10 pointer-events-none"></div>
                <div class="flex items-center space-x-3 mb-1 relative z-10">
                    <span class="material-symbols-outlined text-primary-container">input</span>
                    <h3 class="font-title-sm text-title-sm text-on-surface">Quick Stock In</h3>
                </div>
                <p class="font-body-sm text-body-sm text-slate-400 relative z-10">Record immediate inward receipts.</p>
            </div>
            <form action="{{ route('purchases.store') }}" method="POST" class="p-5 flex flex-col gap-4 font-body-sm text-body-sm">
                @csrf
                <input type="hidden" name="purchase_date" value="{{ date('Y-m-d') }}">
                
                <!-- Select Material -->
                <div class="space-y-1.5">
                    <label class="block font-medium text-slate-300">Material <span class="text-error">*</span></label>
                    <select name="items[0][material_id]" class="w-full bg-surface-container-highest border border-slate-700 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors appearance-none" required>
                        <option disabled selected value="">Select Material</option>
                        @foreach($materials as $m)
                            <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->unit }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Supplier Name (Manual Input) -->
                <div class="space-y-1.5">
                    <label class="block font-medium text-slate-300">Supplier Name</label>
                    <input name="supplier_name" class="w-full bg-surface-container-highest border border-slate-700 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors" placeholder="e.g. Toko Kayu Sejahtera" type="text">
                </div>

                <!-- Qty & Unit Price Row -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block font-medium text-slate-300">Quantity <span class="text-error">*</span></label>
                        <input name="items[0][qty]" class="w-full bg-surface-container-highest border border-slate-700 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors text-right font-data-mono" placeholder="0" type="number" required step="0.01">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-medium text-slate-300">Unit Price <span class="text-error">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-500">Rp</div>
                            <input name="items[0][price]" class="w-full bg-surface-container-highest border border-slate-700 text-on-surface rounded-lg pl-9 pr-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors text-right font-data-mono" placeholder="0" type="number" required>
                        </div>
                    </div>
                </div>

                <!-- Invoice Number -->
                <div class="space-y-1.5">
                    <label class="block font-medium text-slate-300">Invoice Number</label>
                    <input name="invoice_number" class="w-full bg-surface-container-highest border border-slate-700 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors" placeholder="INV/2026/..." type="text">
                </div>

                <!-- Actions -->
                <div class="pt-4 border-t border-surface-variant mt-2 flex justify-end space-x-3">
                    <button class="px-5 py-2 bg-primary-container text-on-primary-container rounded-lg font-semibold hover:bg-primary transition-colors shadow-sm flex items-center space-x-2" type="submit">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        <span>Record Stock In</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
