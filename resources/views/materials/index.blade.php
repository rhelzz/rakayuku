@extends('layouts.app')

@section('title', 'Inventaris & Pengadaan')

@section('content')
<div>
    <!-- Page Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline-md text-headline-md text-on-surface mb-1">Inventaris & Pengadaan</h2>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Kelola bahan baku, tingkat stok, dan pencatatan masuk cepat.</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('materials.create') }}" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg font-body-sm text-body-sm font-semibold hover:bg-primary transition-colors flex items-center space-x-2">
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>Bahan Baru</span>
            </a>
        </div>
    </div>

<!-- Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-grid-gutter">
    <!-- Left Side: Inventory Table (8 cols) -->
    <div class="lg:col-span-8 flex flex-col gap-grid-gutter">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="glass-panel border border-surface-variant rounded-xl p-4 flex items-center space-x-4">
                <div class="h-10 w-10 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600">
                    <span class="material-symbols-outlined">inventory</span>
                </div>
                <div>
                    <p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Total Item</p>
                    <p class="font-title-sm text-title-sm text-on-surface mt-0.5">{{ $materials->count() }}</p>
                </div>
            </div>
            <div class="glass-panel border border-surface-variant rounded-xl p-4 flex items-center space-x-4">
                <div class="h-10 w-10 rounded-lg bg-orange-50 border border-orange-200 flex items-center justify-center text-orange-600">
                    <span class="material-symbols-outlined">warning</span>
                </div>
                <div>
                    <p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Stok Rendah</p>
                    <p class="font-title-sm text-title-sm text-orange-600 mt-0.5">{{ $materials->where('current_qty', '<', 5)->count() }} Item</p>
                </div>
            </div>
            <div class="glass-panel border border-surface-variant rounded-xl p-4 flex items-center space-x-4">
                <div class="h-10 w-10 rounded-lg bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600">
                    <span class="material-symbols-outlined">payments</span>
                </div>
                <div>
                    <p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Nilai Inventaris</p>
                    <p class="font-title-sm text-title-sm text-on-surface mt-0.5">Rp {{ number_format($materials->sum(fn($m) => $m->current_qty * $m->avg_price), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="glass-panel border border-surface-variant rounded-xl flex flex-col overflow-hidden">
            <div class="p-4 border-b border-surface-variant flex justify-between items-center bg-surface-container-lowest/50">
                <h3 class="font-title-sm text-title-sm text-on-surface">Registrasi Bahan Baku</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-surface-container-high/80 sticky top-0 z-10 backdrop-blur-md">
                        <tr>
                            <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant uppercase border-b border-surface-variant whitespace-nowrap">Nama Bahan</th>
                            <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant uppercase border-b border-surface-variant text-right whitespace-nowrap">Stok Tersedia</th>
                            <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant uppercase border-b border-surface-variant text-right whitespace-nowrap">HPP Rata-rata</th>
                            <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant uppercase border-b border-surface-variant text-center whitespace-nowrap">Status</th>
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
                                        <div class="text-slate-500 font-data-mono text-[11px]">ID: {{ $material->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right font-data-mono text-data-mono @if($material->current_qty < 5) text-red-600 font-bold @endif">
                                {{ number_format($material->current_qty) }} <span class="text-slate-500 text-[11px] font-normal">{{ $material->unit }}</span>
                            </td>
                            <td class="px-4 py-3 text-right font-data-mono text-data-mono">
                                Rp {{ number_format($material->avg_price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($material->current_qty >= 5)
                                <div class="inline-flex items-center space-x-1.5 px-2 py-0.5 rounded-full bg-green-50 text-green-700 border border-green-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    <span class="text-[11px] font-medium">Optimal</span>
                                </div>
                                @else
                                <div class="inline-flex items-center space-x-1.5 px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span class="text-[11px] font-medium">Stok Rendah</span>
                                </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('materials.edit', $material) }}" class="text-slate-500 hover:text-primary">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500 italic">Tidak ada bahan yang ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Side: Quick Stock In Form (4 cols) -->
    <div class="lg:col-span-4 flex flex-col gap-grid-gutter">
        <div class="glass-panel border border-surface-variant rounded-xl overflow-hidden flex flex-col sticky top-4">
            <div class="p-5 border-b border-surface-variant bg-white/40 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary-container/10 blur-2xl rounded-full -mr-10 -mt-10 pointer-events-none"></div>
                <div class="flex items-center space-x-3 mb-1 relative z-10">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-[20px]">input</span>
                    </div>
                    <h3 class="font-title-sm text-title-sm text-on-surface">Stok Masuk Cepat</h3>
                </div>
                <p class="font-body-sm text-body-sm text-slate-500 relative z-10">Catat penerimaan masuk segera.</p>
            </div>
            <form action="{{ route('purchases.store') }}" method="POST" class="p-5 flex flex-col gap-4 font-body-sm text-body-sm">
                @csrf
                <input type="hidden" name="purchase_date" value="{{ date('Y-m-d') }}">
                
                <!-- Select Material -->
                <div class="space-y-1.5">
                    <label class="block font-medium text-slate-700">Bahan <span class="text-error">*</span></label>
                    <select name="items[0][material_id]" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary focus:ring-1 focus:ring-primary transition-colors appearance-none" required>
                        <option disabled selected value="">Pilih Bahan</option>
                        @foreach($materials as $m)
                            <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->unit }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Supplier Name (Manual Input) -->
                <div class="space-y-1.5">
                    <label class="block font-medium text-slate-700">Nama Pemasok</label>
                    <input name="supplier_name" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary focus:ring-1 focus:ring-primary transition-colors" placeholder="misal: Toko Kayu Sejahtera" type="text">
                </div>

                <!-- Qty & Unit Price Row -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block font-medium text-slate-700">Jumlah <span class="text-error">*</span></label>
                        <input name="items[0][qty]" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary focus:ring-1 focus:ring-primary transition-colors text-right font-data-mono" placeholder="0" type="number" required step="0.01">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-medium text-slate-700">Harga Satuan <span class="text-error">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">Rp</div>
                            <input name="items[0][price]" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg pl-9 pr-3 py-2 focus:border-primary focus:ring-1 focus:ring-primary transition-colors text-right font-data-mono" placeholder="0" type="number" required>
                        </div>
                    </div>
                </div>

                <!-- Invoice Number -->
                <div class="space-y-1.5">
                    <label class="block font-medium text-slate-700">Nomor Faktur</label>
                    <input name="invoice_number" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary focus:ring-1 focus:ring-primary transition-colors" placeholder="INV/2026/..." type="text">
                </div>

                <!-- Actions -->
                <div class="pt-4 border-t border-surface-variant mt-2 flex justify-end space-x-3">
                    <button class="px-5 py-2 bg-primary-container text-on-primary-container rounded-lg font-semibold hover:bg-primary transition-colors shadow-sm flex items-center space-x-2" type="submit">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        <span>Simpan Stok Masuk</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
