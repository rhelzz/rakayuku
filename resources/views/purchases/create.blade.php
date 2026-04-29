@extends('layouts.app')

@section('title', 'Record New Purchase')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumbs -->
    <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm mb-4">
        <a href="{{ route('purchases.index') }}" class="hover:text-primary transition-colors">Purchases</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface">New Purchase</span>
    </nav>

    <div class="bg-surface-container-low border border-surface-variant rounded-xl overflow-hidden shadow-xl" x-data="{ 
        items: [{ material_id: '', qty: '', price: '' }],
        add() { this.items.push({ material_id: '', qty: '', price: '' }) },
        remove(index) { this.items.splice(index, 1) }
    }">
        <div class="p-6 border-b border-surface-variant bg-surface-container-lowest/50 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-primary-container/10 blur-2xl rounded-full -mr-10 -mt-10 pointer-events-none"></div>
            <div class="flex items-center space-x-3 mb-1 relative z-10">
                <span class="material-symbols-outlined text-primary text-3xl">shopping_cart_checkout</span>
                <h3 class="font-headline-md text-headline-md text-on-surface">Record New Purchase</h3>
            </div>
            <p class="font-body-sm text-body-sm text-slate-500 relative z-10">Record raw material inward receipts and update inventory stock.</p>
        </div>

        <form action="{{ route('purchases.store') }}" method="POST" class="p-6 space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Supplier Name -->
                <div class="space-y-1.5">
                    <label for="supplier_name" class="block font-medium text-slate-700 text-sm">Supplier Name</label>
                    <input type="text" name="supplier_name" id="supplier_name" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors" placeholder="e.g. Toko Kayu Sejahtera">
                </div>

                <!-- Invoice Number -->
                <div class="space-y-1.5">
                    <label for="invoice_number" class="block font-medium text-slate-700 text-sm">Invoice / Receipt Number</label>
                    <input type="text" name="invoice_number" id="invoice_number" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors" placeholder="INV/2026/...">
                </div>

                <!-- Purchase Date -->
                <div class="space-y-1.5">
                    <label for="purchase_date" class="block font-medium text-slate-700 text-sm">Purchase Date <span class="text-error">*</span></label>
                    <input type="date" name="purchase_date" id="purchase_date" value="{{ date('Y-m-d') }}" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors">
                </div>
            </div>

            <!-- Items Table -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="font-title-sm text-title-sm text-on-surface">Purchased Items</h4>
                    <button type="button" @click="add()" class="text-primary hover:opacity-80 flex items-center gap-1 text-sm font-semibold">
                        <span class="material-symbols-outlined text-[18px]">add_circle</span>
                        Add Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-surface-variant">
                                <th class="py-2 px-1 font-label-caps text-slate-400 uppercase text-[11px]">Material</th>
                                <th class="py-2 px-1 font-label-caps text-slate-400 uppercase text-[11px] w-24">Qty</th>
                                <th class="py-2 px-1 font-label-caps text-slate-400 uppercase text-[11px] w-48">Unit Price</th>
                                <th class="py-2 px-1 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-variant/50">
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="group">
                                    <td class="py-3 px-1">
                                        <select :name="'items['+index+'][material_id]'" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-1.5 text-sm focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors">
                                            <option disabled selected value="">Select Material</option>
                                            @foreach($materials as $m)
                                                <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->unit }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-3 px-1">
                                        <input type="number" :name="'items['+index+'][qty]'" required step="0.01" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-1.5 text-sm text-right font-data-mono focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors" placeholder="0">
                                    </td>
                                    <td class="py-3 px-1">
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none text-slate-400 text-xs">Rp</div>
                                            <input type="number" :name="'items['+index+'][price]'" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg pl-7 pr-3 py-1.5 text-sm text-right font-data-mono focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors" placeholder="0">
                                        </div>
                                    </td>
                                    <td class="py-3 px-1 text-right">
                                        <button type="button" @click="remove(index)" x-show="items.length > 1" class="text-slate-500 hover:text-error transition-colors">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pt-6 border-t border-surface-variant flex justify-end gap-3">
                <a href="{{ route('purchases.index') }}" class="px-5 py-2 rounded-lg border border-surface-variant text-slate-600 hover:bg-surface-container-high transition-colors font-medium text-sm">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-container text-on-primary-container rounded-lg font-semibold hover:bg-primary transition-colors shadow-lg flex items-center gap-2 text-sm">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Record Purchase
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
