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

    <!-- Table Filter -->
    <x-table.filter placeholder="Cari nama, email, atau telepon pelanggan..." />

    <!-- Table Card -->
    <div class="bg-surface-container-low border border-surface-variant rounded-xl flex flex-col overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-high/50 border-b border-surface-variant">
                    <tr>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest w-10 text-center">No</th>
                        <x-table.header label="Nama Klien" field="name" />
                        <x-table.header label="Email" field="email" />
                        <x-table.header label="Telepon" field="phone" />
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Alamat</th>
                        <x-table.header label="Total Proyek" field="orders_count" align="center" />
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-variant/30 font-body-sm text-body-sm text-on-surface bg-white/50">
                    @forelse($customers as $c)
                    <tr class="hover:bg-surface-container-low transition-colors group">
                        <td class="px-6 py-4 text-center font-data-mono text-slate-400">{{ $customers->firstItem() + $loop->index }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-on-surface">{{ $c->name }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $c->email ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $c->phone }}</td>
                        <td class="px-6 py-4 text-slate-500 truncate max-w-xs" title="{{ $c->address }}">{{ $c->address }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($c->orders_count > 0)
                                <span class="px-3 py-1 rounded-full bg-amber-50 text-primary border border-amber-100 font-data-mono font-bold text-xs shadow-sm">
                                    {{ $c->orders_count }}
                                </span>
                            @else
                                <span class="text-[10px] font-label-caps text-slate-400 italic">Belum Ada</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('customers.edit', $c) }}" class="text-slate-400 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </a>
                                <form action="{{ route('customers.destroy', $c) }}" method="POST" onsubmit="return confirm('Hapus pelanggan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-error transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400 italic">
                            <div class="flex flex-col items-center gap-2">
                                <span class="material-symbols-outlined text-4xl opacity-20">person_off</span>
                                Tidak ada pelanggan ditemukan.
                            </div>
                        </td>
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
