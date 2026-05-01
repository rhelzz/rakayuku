@extends('layouts.app')

@section('title', 'Tambah Pelanggan Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Breadcrumbs -->
    <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <a href="{{ route('customers.index') }}" class="hover:text-primary transition-colors">Pelanggan</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface">Pelanggan Baru</span>
    </nav>

    <div class="bg-surface-container-low border border-surface-variant rounded-xl overflow-hidden shadow-xl">
        <div class="p-6 border-b border-surface-variant bg-surface-container-lowest/50 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-primary-container/10 blur-2xl rounded-full -mr-10 -mt-10 pointer-events-none"></div>
            <div class="flex items-center space-x-3 mb-1 relative z-10">
                <span class="material-symbols-outlined text-primary text-3xl">person_add</span>
                <h3 class="font-headline-md text-headline-md text-on-surface">Tambah Pelanggan Baru</h3>
            </div>
            <p class="font-body-sm text-body-sm text-slate-500 relative z-10">Daftarkan klien baru untuk manajemen proyek.</p>
        </div>

        <form action="{{ route('customers.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label for="name" class="block font-medium text-slate-700 text-sm">Nama Pelanggan <span class="text-error">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors @error('name') border-error @enderror" placeholder="misal: John Doe">
                    @error('name')
                        <p class="text-[11px] text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="email" class="block font-medium text-slate-700 text-sm">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors @error('email') border-error @enderror" placeholder="misal: john@example.com">
                    @error('email')
                        <p class="text-[11px] text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="phone" class="block font-medium text-slate-700 text-sm">Nomor Telepon <span class="text-error">*</span></label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors @error('phone') border-error @enderror" placeholder="misal: 08123456789">
                    @error('phone')
                        <p class="text-[11px] text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="address" class="block font-medium text-slate-700 text-sm">Alamat <span class="text-error">*</span></label>
                    <textarea name="address" id="address" rows="3" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors @error('address') border-error @enderror" placeholder="Alamat lengkap...">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-[11px] text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-6 border-t border-surface-variant flex justify-end gap-3">
                <a href="{{ route('customers.index') }}" class="px-5 py-2 rounded-lg border border-surface-variant text-slate-600 hover:bg-surface-container-high transition-colors font-medium text-sm">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg font-semibold hover:opacity-90 transition-colors shadow-lg flex items-center gap-2 text-sm">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Pelanggan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
