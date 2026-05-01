@extends('layouts.app')

@section('title', 'Manajemen Pelanggan')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Pelanggan</span>
        </nav>

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-headline-md text-headline-md text-on-surface mb-1">Daftar Pelanggan</h2>
                <p class="font-body-sm text-body-sm text-slate-400">Kelola informasi klien dan riwayat proyek.</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('customers.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg font-body-sm text-body-sm font-semibold hover:bg-primary-hover transition-colors flex items-center space-x-2 shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span>Tambah Pelanggan</span>
                </a>
            </div>
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
                        <th class="px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase border-b border-surface-variant w-10">No</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase border-b border-surface-variant">Nama Klien</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase border-b border-surface-variant">Email</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase border-b border-surface-variant">Telepon</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase border-b border-surface-variant">Alamat</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase border-b border-surface-variant text-center whitespace-nowrap">Total Proyek</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase border-b border-surface-variant text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-variant font-body-sm text-body-sm text-on-surface">
                    @forelse($customers as $c)
                    <tr class="hover:bg-surface-container-high/50 transition-colors group">
                        <td class="px-4 py-3 font-data-mono text-slate-400">{{ $customers->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-on-surface">{{ $c->name }}</div>
                        </td>
                        <td class="px-4 py-3 text-slate-400">{{ $c->email ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $c->phone }}</td>
                        <td class="px-4 py-3 text-slate-400 truncate max-w-xs">{{ $c->address }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($c->orders_count > 0)
                                <span class="px-3 py-1 rounded-full bg-amber-50 text-primary border border-amber-100 font-data-mono font-bold text-xs shadow-sm">
                                    {{ $c->orders_count }}
                                </span>
                            @else
                                <span class="text-[10px] font-label-caps text-slate-400 italic">Belum Ada</span>
                            @endif
                        </td>
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
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500 italic">Tidak ada pelanggan ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
            <div class="p-4 border-t border-surface-variant bg-surface-container-lowest/50">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
