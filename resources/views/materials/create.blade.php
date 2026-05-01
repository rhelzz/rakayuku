@extends('layouts.app')

@section('title', 'Tambah Bahan Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Breadcrumbs -->
    <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm mb-4">
        <a href="{{ route('materials.index') }}" class="hover:text-primary transition-colors">Bahan Baku</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface">Bahan Baru</span>
    </nav>

    <div class="bg-surface-container-low border border-surface-variant rounded-xl overflow-hidden shadow-xl">
        <div class="p-6 border-b border-surface-variant bg-surface-container-lowest/50 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-primary-container/10 blur-2xl rounded-full -mr-10 -mt-10 pointer-events-none"></div>
            <div class="flex items-center space-x-3 mb-1 relative z-10">
                <span class="material-symbols-outlined text-primary text-3xl">inventory_2</span>
                <h3 class="font-headline-md text-headline-md text-on-surface">Tambah Bahan Baru</h3>
            </div>
            <p class="font-body-sm text-body-sm text-slate-500 relative z-10">Daftarkan item bahan baku baru ke sistem inventaris.</p>
        </div>

        <form action="{{ route('materials.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label for="name" class="block font-medium text-slate-700 text-sm">Nama Bahan <span class="text-error">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors @error('name') border-error @enderror" placeholder="misal: Kayu Jati Grade A">
                    @error('name')
                        <p class="text-[11px] text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="space-y-1.5">
                    <label for="unit" class="block font-medium text-slate-700 text-sm">Satuan Ukuran <span class="text-error">*</span></label>
                    <select name="unit" id="unit" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors appearance-none @error('unit') border-error @enderror">
                        <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>Buah (pcs)</option>
                        <option value="bd ft" {{ old('unit') == 'bd ft' ? 'selected' : '' }}>Board Foot (bd ft)</option>
                        <option value="m3" {{ old('unit') == 'm3' ? 'selected' : '' }}>Meter Kubik (m3)</option>
                        <option value="meter" {{ old('unit') == 'meter' ? 'selected' : '' }}>Meter (m)</option>
                        <option value="gal" {{ old('unit') == 'gal' ? 'selected' : '' }}>Galon (gal)</option>
                        <option value="set" {{ old('unit') == 'set' ? 'selected' : '' }}>Set</option>
                    </select>
                    @error('unit')
                        <p class="text-[11px] text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-6 border-t border-surface-variant flex justify-end gap-3">
                <a href="{{ route('materials.index') }}" class="px-5 py-2 rounded-lg border border-surface-variant text-slate-600 hover:bg-surface-container-high transition-colors font-medium text-sm">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-container text-on-primary-container rounded-lg font-semibold hover:bg-primary transition-colors shadow-lg flex items-center gap-2 text-sm">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Bahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
