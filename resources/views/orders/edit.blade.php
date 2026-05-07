@extends('layouts.app')

@section('title', 'Edit Proyek - ' . $order->order_number)

@section('content')
<div class="max-w-2xl mx-auto">
    <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <a href="{{ route('orders.index') }}" class="hover:text-primary transition-colors">Pesanan</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <a href="{{ route('orders.show', $order) }}" class="hover:text-primary transition-colors">{{ $order->order_number }}</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface">Edit</span>
    </nav>

    <div class="bg-surface-container-low border border-surface-variant rounded-xl overflow-hidden shadow-xl">
        <div class="p-6 border-b border-surface-variant bg-surface-container-lowest/50 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-amber-500/10 blur-2xl rounded-full -mr-10 -mt-10 pointer-events-none"></div>
            <div class="flex items-center space-x-3 mb-1 relative z-10">
                <span class="material-symbols-outlined text-amber-600 text-3xl">edit_note</span>
                <h3 class="font-headline-md text-headline-md text-on-surface">Edit Pesanan</h3>
            </div>
            <p class="font-body-sm text-body-sm text-slate-500 relative z-10">Perbarui data pesanan <strong>{{ $order->order_number }}</strong>. Hanya bisa diedit saat status PENDING.</p>
        </div>

        <form action="{{ route('orders.update', $order) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5 md:col-span-2">
                    <label class="block font-medium text-slate-700 text-sm">Nomor Pesanan</label>
                    <div class="w-full bg-slate-50 border border-slate-200 text-slate-500 rounded-lg px-3 py-2 font-data-mono text-sm">
                        {{ $order->order_number }}
                    </div>
                </div>

                <div class="space-y-1.5 md:col-span-2">
                    <label class="block font-medium text-slate-700 text-sm">Pelanggan</label>
                    <div class="w-full bg-slate-50 border border-slate-200 text-slate-500 rounded-lg px-3 py-2 text-sm">
                        {{ $order->customer->name }} ({{ $order->customer->phone }})
                    </div>
                    <p class="text-[11px] text-slate-400 italic">Pelanggan tidak bisa diubah setelah pesanan dibuat.</p>
                </div>

                <div class="space-y-1.5 md:col-span-2">
                    <label for="project_name" class="block font-medium text-slate-700 text-sm">Nama Proyek / Item <span class="text-error">*</span></label>
                    <input type="text" name="project_name" id="project_name" value="{{ old('project_name', $order->project_name) }}" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors @error('project_name') border-error @enderror" placeholder="Misal: Meja Makan Jati (6 Kursi)">
                    @error('project_name')
                        <p class="text-[11px] text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5 md:col-span-2">
                    <label for="project_description" class="block font-medium text-slate-700 text-sm">Deskripsi Detail (Opsional)</label>
                    <textarea name="project_description" id="project_description" rows="2" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors @error('project_description') border-error @enderror" placeholder="Catatan tambahan spesifikasi...">{{ old('project_description', $order->project_description) }}</textarea>
                    @error('project_description')
                        <p class="text-[11px] text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @php
                    $currentPrice = old('selling_price', $order->selling_price);
                @endphp
                <div class="space-y-1.5" x-data="{ 
                    displayPrice: '{{ number_format($currentPrice, 0, ',', '.') }}',
                    rawPrice: '{{ $currentPrice }}'
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
                    @if($order->total_paid > 0)
                        <p class="text-[11px] text-amber-600 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">info</span>
                            Sudah ada pembayaran Rp {{ number_format($order->total_paid, 0, ',', '.') }}. Harga jual tidak boleh kurang dari jumlah yang sudah dibayar.
                        </p>
                    @endif
                </div>

                <div class="space-y-1.5">
                    <label class="block font-medium text-slate-700 text-sm">Uang Muka (DP)</label>
                    <div class="w-full bg-slate-50 border border-slate-200 text-slate-500 rounded-lg px-3 py-2 font-data-mono text-sm">
                        Rp {{ number_format($order->dp_amount, 0, ',', '.') }}
                    </div>
                    <p class="text-[11px] text-slate-400 italic">DP diatur saat pembuatan pesanan dan tidak bisa diubah.</p>
                </div>

                <div class="space-y-1.5 md:col-span-2">
                    <label for="deadline" class="block font-medium text-slate-700 text-sm">Target Batas Waktu</label>
                    <input type="date" name="deadline" id="deadline" value="{{ old('deadline', $order->deadline ? $order->deadline->format('Y-m-d') : '') }}" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors @error('deadline') border-error @enderror">
                    @error('deadline')
                        <p class="text-[11px] text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-6 border-t border-surface-variant flex justify-end gap-3">
                <a href="{{ route('orders.show', $order) }}" class="px-5 py-2 rounded-lg border border-surface-variant text-slate-600 hover:bg-surface-container-high transition-colors font-medium text-sm">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-container text-on-primary-container rounded-lg font-semibold hover:bg-primary transition-colors shadow-lg flex items-center gap-2 text-sm">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
