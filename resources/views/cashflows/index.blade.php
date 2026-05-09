@extends('layouts.app')

@section('title', 'Arus Kas & Saldo Perusahaan')
@section('content')
<div class="space-y-6" x-data="{ showAddModal: false }">
    <!-- Page Header -->
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Arus Kas</span>
        </nav>

        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
            <div>
                <h1 class="font-headline-md text-headline-md text-on-background">Arus Kas & Saldo</h1>
                <p class="font-body-sm text-body-sm text-slate-400 mt-1">Riwayat mutasi saldo masuk dan keluar perusahaan.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('cashflows.export', request()->all()) }}" class="px-6 py-2.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl font-bold hover:bg-emerald-100 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">file_download</span>
                    Export Excel
                </a>
                <button @click="showAddModal = true" class="px-6 py-2.5 bg-primary text-white rounded-xl font-bold hover:bg-primary-hover shadow-lg shadow-primary/20 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">add_card</span>
                    Tambah Saldo
                </button>
            </div>
        </div>
    </div>

    <!-- Saldo Card -->
    <div class="grid grid-cols-1 mb-6">
        <div class="bg-gradient-to-r from-primary from-60% to-primary-container rounded-2xl p-5 md:p-6 flex flex-col relative overflow-hidden group shadow-lg shadow-primary/20">
            <div class="absolute -top-10 -right-10 p-4 opacity-30 group-hover:opacity-50 transition-opacity">
                <span class="material-symbols-outlined text-[150px] text-primary">account_balance_wallet</span>
            </div>
            <span class="font-label-caps text-label-caps text-white/90 uppercase tracking-wider mb-1 relative z-10">Saldo Saat Ini</span>
            <div class="flex flex-col relative z-10">
                <span class="text-3xl md:text-4xl font-bold tracking-tight text-white">{{ formatRupiah($currentBalance) }}</span>
            </div>
            <span class="font-body-sm text-body-sm text-white/90 mt-1 relative z-10">Total ketersediaan dana perusahaan dari seluruh transaksi</span>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-surface-container-low rounded-xl p-4 border border-surface-variant mb-6">
        <form action="{{ route('cashflows.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-label-caps text-slate-500 mb-1">Tipe Transaksi</label>
                <select name="type" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                    <option value="">Semua Tipe</option>
                    <option value="IN" {{ request('type') == 'IN' ? 'selected' : '' }}>Uang Masuk</option>
                    <option value="OUT" {{ request('type') == 'OUT' ? 'selected' : '' }}>Uang Keluar</option>
                    <option value="INITIAL" {{ request('type') == 'INITIAL' ? 'selected' : '' }}>Saldo Awal</option>
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs font-label-caps text-slate-500 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm font-data-mono">
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs font-label-caps text-slate-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm font-data-mono">
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-bold hover:bg-slate-200 transition-colors text-sm h-[38px] flex items-center">Filter</button>
            </div>
            @if(request()->hasAny(['type', 'start_date', 'end_date']))
            <div>
                <a href="{{ route('cashflows.index') }}" class="px-4 py-2 text-error hover:bg-error-container/20 rounded-xl font-bold transition-colors text-sm h-[38px] flex items-center">Reset</a>
            </div>
            @endif
        </form>
    </div>

    <!-- Cashflows Table -->
    <div class="bg-surface-container-low border border-surface-variant rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-high/50 border-b border-surface-variant">
                    <tr>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest w-10 text-center">No</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Tanggal</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-center">Tipe</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Keterangan</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-variant/30 font-body-sm text-body-sm text-on-surface bg-white/50">
                    @forelse($cashflows as $cf)
                    <tr class="hover:bg-surface-container-low transition-colors group">
                        <td class="px-6 py-4 text-center font-data-mono text-slate-400">{{ $cashflows->firstItem() + $loop->index }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-slate-500 font-data-mono">
                            {{ \Carbon\Carbon::parse($cf->created_at)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($cf->type === 'IN')
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-100 uppercase">Masuk</span>
                            @elseif($cf->type === 'OUT')
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-error-container/20 text-error text-[10px] font-bold border border-error/20 uppercase">Keluar</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-bold border border-blue-100 uppercase">Saldo Awal</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-medium">
                            {{ $cf->description }}
                        </td>
                        <td class="px-6 py-4 text-right font-data-mono font-bold {{ $cf->type === 'OUT' ? 'text-error' : 'text-emerald-600' }}">
                            {{ $cf->type === 'OUT' ? '-' : '+' }} {{ formatRupiah($cf->amount) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">
                            <div class="flex flex-col items-center gap-2">
                                <span class="material-symbols-outlined text-4xl opacity-20">receipt_long</span>
                                Belum ada riwayat arus kas.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($cashflows->hasPages())
            <div class="p-4 border-t border-surface-variant bg-surface-container-lowest/50">
                {{ $cashflows->links() }}
            </div>
        @endif
    </div>

    <!-- Add Modal -->
    <div x-show="showAddModal" class="fixed z-[100]" style="display: none; top: 0; right: 0; bottom: 0; left: 0;" x-cloak>
        <div x-show="showAddModal" x-transition.opacity class="absolute bg-slate-900/50 backdrop-blur-sm" style="top: 0; right: 0; bottom: 0; left: 0;" @click="showAddModal = false"></div>
        <div x-show="showAddModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="absolute top-1/2 left-1/2 bg-white rounded-2xl shadow-xl overflow-hidden"
             style="width: 100%; max-width: 28rem; transform: translate(-50%, -50%);">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-surface-container-low">
                <h3 class="font-headline-sm text-on-surface flex items-center gap-2"><span class="material-symbols-outlined text-primary">add_card</span> Tambah Saldo Perusahaan</h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="{{ route('cashflows.store') }}" method="POST" class="p-6 space-y-5">
                @csrf
                <input type="hidden" name="type" value="INITIAL">
                <div x-data="{ 
                    displayAmount: '', 
                    actualAmount: '',
                    updateMask(val) {
                        this.displayAmount = formatRupiahJS(val);
                        this.actualAmount = this.displayAmount.replace(/\./g, '');
                    }
                }">
                    <label class="block text-xs font-label-caps text-slate-500 mb-1">Jumlah (Rp) <span class="text-error">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-slate-500 text-sm font-data-mono">Rp</span>
                        </div>
                        <input type="text" 
                               x-model="displayAmount" 
                               x-on:input="updateMask($event.target.value)"
                               required 
                               class="w-full pl-10 pr-3 rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm font-data-mono text-left"
                               placeholder="0">
                        <input type="hidden" name="amount" x-model="actualAmount">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-label-caps text-slate-500 mb-1">Keterangan <span class="text-error">*</span></label>
                    <input type="text" name="description" required placeholder="Contoh: Saldo awal / Tambahan dana" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                </div>
                
                <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 mt-6">
                    <button type="button" @click="showAddModal = false" class="px-5 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-bold hover:bg-slate-200 transition-colors text-sm">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-xl font-bold hover:bg-primary-hover transition-colors shadow-md shadow-primary/20 flex items-center gap-2 text-sm">
                        <span class="material-symbols-outlined text-[18px]">save</span> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
