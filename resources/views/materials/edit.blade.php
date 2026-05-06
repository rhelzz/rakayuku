@extends('layouts.app')

@section('title', 'Edit Bahan - ' . $material->name)

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Breadcrumbs -->
    <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <a href="{{ route('materials.index') }}" class="hover:text-primary transition-colors">Inventaris</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface">Edit Bahan</span>
    </nav>

    <div class="bg-surface-container-low border border-surface-variant rounded-xl overflow-hidden shadow-xl">
        <div class="p-6 border-b border-surface-variant bg-surface-container-lowest/50 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-primary-container/10 blur-2xl rounded-full -mr-10 -mt-10 pointer-events-none"></div>
            <div class="flex items-center space-x-3 mb-1 relative z-10">
                <span class="material-symbols-outlined text-primary text-3xl">edit</span>
                <h3 class="font-headline-md text-headline-md text-on-surface">Edit Bahan</h3>
            </div>
            <p class="font-body-sm text-body-sm text-slate-500 relative z-10">Perbarui spesifikasi bahan baku.</p>
        </div>

        <form action="{{ route('materials.update', $material) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg mb-6">
                <p class="text-sm font-semibold text-slate-700">Kode Barang: <code class="bg-primary text-white px-2 py-1 rounded">{{ $material->code }}</code></p>
            </div>

            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label for="name" class="block font-medium text-slate-700 text-sm">Nama Bahan <span class="text-error">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $material->name) }}" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors @error('name') border-error @enderror" placeholder="misal: Baja atau Kayu">
                    <p class="text-[11px] text-slate-400 mt-1 italic">Nama dasar material tanpa satuan.</p>
                    @error('name')
                        <p class="text-[11px] text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="type" class="block font-medium text-slate-700 text-sm">Tipe/Varian <span class="text-slate-400 text-xs">(opsional)</span></label>
                    <input type="text" name="type" id="type" value="{{ old('type', $material->type) }}" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors @error('type') border-error @enderror" placeholder="misal: Stainless, Galvanis, Premium">
                    <p class="text-[11px] text-slate-400 mt-1 italic">Untuk membedakan jenis/varian dari material yang sama.</p>
                    @error('type')
                        <p class="text-[11px] text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="unit" class="block font-medium text-slate-700 text-sm">Satuan <span class="text-error">*</span></label>
                    <input type="text" name="unit" id="unit" value="{{ old('unit', $material->unit) }}" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors @error('unit') border-error @enderror" placeholder="Misal: pcs, lembar, kg, meter, liter">
                    <p class="text-[11px] text-slate-400 mt-1 italic">Input manual satuan sesuai kebutuhan (pcs, lembar, box, meter, set, dll).</p>
                    @error('unit')
                        <p class="text-[11px] text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-6 border-t border-surface-variant flex justify-between gap-3">
                <button type="button" onclick="if(confirm('Apakah Anda yakin ingin menghapus bahan ini?')) document.getElementById('delete-form').submit();" class="px-5 py-2 rounded-lg border border-error/30 text-error hover:bg-error/10 transition-colors font-medium text-sm">
                    Hapus Bahan
                </button>
                <div class="flex gap-3">
                    <a href="{{ route('materials.index') }}" class="px-5 py-2 rounded-lg border border-surface-variant text-slate-600 hover:bg-surface-container-high transition-colors font-medium text-sm">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-primary-container text-on-primary-container rounded-lg font-semibold hover:bg-primary transition-colors shadow-lg flex items-center gap-2 text-sm">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Perbarui Bahan
                    </button>
                </div>
            </div>
        </form>
        <form id="delete-form" action="{{ route('materials.destroy', $material) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection
