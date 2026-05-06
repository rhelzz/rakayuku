@extends('layouts.app')

@section('title', 'Proyek Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Breadcrumbs -->
    <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <a href="{{ route('orders.index') }}" class="hover:text-primary transition-colors">Pesanan</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface">Proyek Baru</span>
    </nav>

    <div class="bg-surface-container-low border border-surface-variant rounded-xl overflow-hidden shadow-xl">
        <div class="p-6 border-b border-surface-variant bg-surface-container-lowest/50 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-primary-container/10 blur-2xl rounded-full -mr-10 -mt-10 pointer-events-none"></div>
            <div class="flex items-center space-x-3 mb-1 relative z-10">
                <span class="material-symbols-outlined text-primary text-3xl">add_shopping_cart</span>
                <h3 class="font-headline-md text-headline-md text-on-surface">Daftarkan Proyek Baru</h3>
            </div>
            <p class="font-body-sm text-body-sm text-slate-500 relative z-10">Buat pesanan furniture baru dan inisialisasi pelacakan manufaktur.</p>
        </div>

        <form action="{{ route('orders.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Selection -->
                <div class="space-y-1.5 md:col-span-2">
                    <label for="customer_id" class="block font-medium text-slate-700 text-sm">Pelanggan <span class="text-error">*</span></label>
                    <select name="customer_id" id="customer_id" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors appearance-none @error('customer_id') border-error @enderror">
                        <option disabled selected value="">Pilih Pelanggan</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }} ({{ $customer->phone }})</option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="text-[11px] text-error mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-[11px] text-slate-500">Pelanggan tidak ditemukan? <a href="{{ route('customers.create') }}" class="text-primary hover:underline">Tambah pelanggan baru</a></p>
                </div>

                <!-- Project Name -->
                <div class="space-y-1.5 md:col-span-2">
                    <label for="project_name" class="block font-medium text-slate-700 text-sm">Nama Proyek / Item <span class="text-error">*</span></label>
                    <input type="text" name="project_name" id="project_name" value="{{ old('project_name') }}" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors @error('project_name') border-error @enderror" placeholder="misal: Meja Makan Jati (6 Kursi)">
                    @error('project_name')
                        <p class="text-[11px] text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Project Description -->
                <div class="space-y-1.5 md:col-span-2">
                    <label for="project_description" class="block font-medium text-slate-700 text-sm">Deskripsi Detail (Opsional)</label>
                    <textarea name="project_description" id="project_description" rows="2" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors @error('project_description') border-error @enderror" placeholder="Catatan tambahan spesifikasi...">{{ old('project_description') }}</textarea>
                    @error('project_description')
                        <p class="text-[11px] text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pricing -->
                <div class="space-y-1.5" x-data="{ 
                    displayPrice: '{{ old('selling_price') ? number_format(old('selling_price'), 0, ',', '.') : '' }}',
                    rawPrice: '{{ old('selling_price', 0) }}'
                }">
                    <label for="display_selling_price" class="block font-medium text-slate-700 text-sm">Harga Jual (Kesepakatan) <span class="text-error">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-500 text-sm">Rp</div>
                        <input type="text" 
                               id="display_selling_price" 
                               x-model="displayPrice"
                               x-on:input="displayPrice = formatRupiahJS($event.target.value); rawPrice = displayPrice.replace(/\./g, '')"
                               required 
                               class="w-full bg-white border border-slate-200 text-on-surface rounded-lg pl-10 pr-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors font-data-mono @error('selling_price') border-error @enderror" 
                               placeholder="0">
                        <input type="hidden" name="selling_price" x-model="rawPrice">
                    </div>
                    @error('selling_price')
                        <p class="text-[11px] text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5" x-data="{ 
                    displayDp: '{{ old('dp_amount') ? number_format(old('dp_amount'), 0, ',', '.') : '' }}',
                    rawDp: '{{ old('dp_amount', 0) }}'
                }">
                    <label for="display_dp_amount" class="block font-medium text-slate-700 text-sm">Uang Muka (DP) <span class="text-error">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-500 text-sm">Rp</div>
                        <input type="text" 
                               id="display_dp_amount" 
                               x-model="displayDp"
                               x-on:input="displayDp = formatRupiahJS($event.target.value); rawDp = displayDp.replace(/\./g, '')"
                               required 
                               class="w-full bg-white border border-slate-200 text-on-surface rounded-lg pl-10 pr-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors font-data-mono @error('dp_amount') border-error @enderror" 
                               placeholder="0">
                        <input type="hidden" name="dp_amount" x-model="rawDp">
                    </div>
                    @error('dp_amount')
                        <p class="text-[11px] text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deadline -->
                <div class="space-y-1.5 md:col-span-2">
                    <label for="deadline" class="block font-medium text-slate-700 text-sm">Target Batas Waktu</label>
                    <input type="date" name="deadline" id="deadline" value="{{ old('deadline') }}" min="{{ date('Y-m-d') }}" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors @error('deadline') border-error @enderror">
                    @error('deadline')
                        <p class="text-[11px] text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-6 border-t border-surface-variant flex justify-end gap-3">
                <a href="{{ route('orders.index') }}" class="px-5 py-2 rounded-lg border border-surface-variant text-slate-600 hover:bg-surface-container-high transition-colors font-medium text-sm">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-container text-on-primary-container rounded-lg font-semibold hover:bg-primary transition-colors shadow-lg flex items-center gap-2 text-sm">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Inisialisasi Proyek
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
