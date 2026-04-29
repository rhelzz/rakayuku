@extends('layouts.app')

@section('title', 'Manajemen Pelanggan')

@section('content')
<div>
    <!-- Page Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline-md text-headline-md text-on-surface mb-1">Pelanggan</h2>
            <p class="font-body-sm text-body-sm text-slate-400">Kelola informasi klien dan riwayat proyek.</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('customers.create') }}" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg font-body-sm text-body-sm font-semibold hover:bg-primary transition-colors flex items-center space-x-2">
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>Tambah Pelanggan</span>
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-surface-container-low border border-surface-variant rounded-xl flex flex-col overflow-hidden shadow-sm">
        <div class="p-4 border-b border-surface-variant flex justify-between items-center bg-surface-container-lowest/50">
            <h3 class="font-title-sm text-title-sm text-on-surface">Registrasi Klien</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase border-b border-surface-variant">Nama Klien</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase border-b border-surface-variant">Telepon</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase border-b border-surface-variant">Alamat</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase border-b border-surface-variant text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-variant font-body-sm text-body-sm text-on-surface">
                    @forelse($customers as $c)
                    <tr class="hover:bg-surface-container-high/50 transition-colors group">
                        <td class="px-4 py-3 font-medium text-on-surface">{{ $c->name }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $c->phone ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-400 truncate max-w-xs">{{ $c->address ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('customers.edit', $c) }}" class="text-slate-400 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </a>
                                <form action="{{ route('customers.destroy', $c) }}" method="POST" onsubmit="return confirm('Hapus pelanggan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-error transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-500 italic">Tidak ada pelanggan ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
