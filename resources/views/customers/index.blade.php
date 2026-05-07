@extends('layouts.app')

@section('title', 'Manajemen Pelanggan')

@section('content')
<div class="space-y-6" x-data="{ showExportModal: false }">
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
            <div class="flex flex-wrap gap-2">
                <button @click="showExportModal = true" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg font-body-sm font-semibold hover:shadow-lg hover:shadow-emerald-500/30 transition-all flex items-center gap-2 shadow-md">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    <span>Export Excel</span>
                </button>
                <a href="{{ route('customers.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg font-body-sm font-semibold hover:bg-primary-hover transition-colors flex items-center gap-2 shadow-lg shadow-primary/20">
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
    <!-- Export Modal -->
    <div x-show="showExportModal" class="fixed inset-0 z-50" style="display: none;" x-cloak>
        <div x-show="showExportModal" x-transition.opacity class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showExportModal = false"></div>
        <div x-show="showExportModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-surface-container-low">
                <h3 class="font-headline-sm text-on-surface flex items-center gap-2"><span class="material-symbols-outlined text-emerald-600">file_download</span> Export Excel</h3>
                <button @click="showExportModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="{{ route('customers.export') }}" method="GET" class="p-6 space-y-5">
                <p class="text-sm text-slate-500 font-body-sm">Pilih rentang waktu untuk data yang ingin di-export. Biarkan kosong untuk export semua data.</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-label-caps text-slate-500 mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm font-data-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-label-caps text-slate-500 mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm font-data-mono">
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 mt-6">
                    <button type="button" @click="showExportModal = false" class="px-5 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-bold hover:bg-slate-200 transition-colors text-sm">Batal</button>
                    <button type="submit" @click="setTimeout(() => showExportModal = false, 500)" class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 transition-colors shadow-md shadow-emerald-500/20 flex items-center gap-2 text-sm">
                        <span class="material-symbols-outlined text-[18px]">download</span> Export Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
