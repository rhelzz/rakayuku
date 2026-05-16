@extends('layouts.app')

@section('title', 'Tutup Buku Bulanan')

@section('content')
<div class="space-y-6" x-data="{ showCloseModal: false, showReopenModal: false, selectedPeriod: '', selectedLabel: '', reopenAction: '', reopenLabel: '' }">
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Tutup Buku</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-headline-md text-headline-md text-on-surface mb-1">Tutup Buku Bulanan</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Kelola penutupan periode bulanan dan kunci transaksi.</p>
            </div>

            <div class="flex items-center gap-2 bg-surface-container-low border border-surface-variant rounded-xl px-2 py-1.5 shadow-sm">
                @if($hasPrevYear)
                    <a href="{{ route('closing.index', ['year' => $prevYear]) }}" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface-container-high transition-colors text-slate-500 hover:text-on-surface">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </a>
                @else
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 cursor-not-allowed">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </div>
                @endif

                <span class="px-3 py-1 font-bold text-on-surface text-sm min-w-[4rem] text-center font-data-mono">{{ $selectedYear }}</span>

                @if($hasNextYear)
                    <a href="{{ route('closing.index', ['year' => $nextYear]) }}" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface-container-high transition-colors text-slate-500 hover:text-on-surface">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </a>
                @else
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 cursor-not-allowed">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3">
        <span class="material-symbols-outlined text-amber-600 mt-0.5">info</span>
        <div>
            <p class="text-sm font-semibold text-amber-800">Tentang Tutup Buku</p>
            <p class="text-xs text-amber-700 mt-1">Setelah periode ditutup, <strong>tidak ada transaksi baru</strong> yang bisa dibuat pada bulan tersebut (pembelian, pesanan, arus kas). Buka kembali periode jika perlu koreksi.</p>
        </div>
    </div>

    <div class="glass-panel border border-surface-variant rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-high/50 border-b border-surface-variant">
                    <tr>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Periode</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-center">Status</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Ditutup Pada</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Oleh</th>
                        <th class="px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right pr-6">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-variant/30 font-body-sm text-body-sm text-on-surface bg-white/50">
                    @forelse($months as $month)
                    <tr class="{{ in_array($month['status'], ['UPCOMING', 'MISSED']) ? 'opacity-50' : '' }} hover:bg-surface-container-low transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @php
                                    $iconBg = match($month['status']) {
                                        'CLOSED' => 'bg-green-50 border-green-200 text-green-600',
                                        'OPEN' => 'bg-amber-50 border-amber-200 text-amber-600',
                                        'MISSED' => 'bg-red-50 border-red-200 text-red-400',
                                        default => 'bg-slate-100 border-slate-200 text-slate-300',
                                    };
                                    $icon = match($month['status']) {
                                        'CLOSED' => 'lock',
                                        'OPEN' => 'lock_open',
                                        'MISSED' => 'event_busy',
                                        default => 'schedule',
                                    };
                                @endphp
                                <div class="w-10 h-10 rounded-lg {{ $iconBg }} border flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>
                                </div>
                                <div>
                                    <div class="font-semibold {{ in_array($month['status'], ['UPCOMING', 'MISSED']) ? 'text-slate-400' : 'text-on-surface' }}">{{ $month['label'] }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $month['period']->format('01/m/Y') }} - {{ $month['period_end']->format('d/m/Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($month['status'] === 'CLOSED')
                                <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-700 border border-green-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    <span class="text-[11px] font-bold uppercase">Ditutup</span>
                                </div>
                            @elseif($month['status'] === 'UPCOMING')
                                <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-400 border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                    <span class="text-[11px] font-bold uppercase">Belum Tersedia</span>
                                </div>
                            @elseif($month['status'] === 'MISSED')
                                <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full bg-red-50 text-red-500 border border-red-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                    <span class="text-[11px] font-bold uppercase">Terlewat</span>
                                </div>
                            @else
                                <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span class="text-[11px] font-bold uppercase">Terbuka</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500 font-data-mono text-xs">
                            {{ $month['closing'] && $month['closing']->closed_at ? $month['closing']->closed_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-slate-500 text-xs">
                            {{ $month['closing'] && $month['closing']->closed_by ? $month['closing']->closed_by : '-' }}
                        </td>
                        <td class="px-6 py-4 text-right pr-6">
                            <div class="flex items-center justify-end gap-2">
                                @if($month['status'] === 'CLOSED')
                                    <a href="{{ route('closing.show', $month['closing']) }}" class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-semibold hover:bg-blue-100 transition-colors border border-blue-200 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">visibility</span>
                                        Ringkasan
                                    </a>
                                    <button @click="reopenAction = '{{ route('closing.reopen', $month['closing']) }}'; reopenLabel = '{{ $month['label'] }}'; showReopenModal = true"
                                            class="px-3 py-1.5 bg-amber-50 text-amber-700 rounded-lg text-xs font-semibold hover:bg-amber-100 transition-colors border border-amber-200 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">lock_open</span>
                                        Buka
                                    </button>
                                @elseif($month['can_close'])
                                    <button @click="selectedPeriod = '{{ $month['period']->format('Y-m-d') }}'; selectedLabel = '{{ $month['label'] }}'; showCloseModal = true"
                                            class="px-3 py-1.5 bg-primary text-white rounded-lg text-xs font-semibold hover:bg-primary-hover transition-colors shadow-sm flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">lock</span>
                                        Tutup Buku
                                    </button>
                                @elseif($month['status'] === 'MISSED')
                                    <span class="text-xs text-red-400 italic">Periode terlewat</span>
                                @else
                                    <span class="text-xs text-slate-300 italic">Belum tersedia</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">
                            <div class="flex flex-col items-center gap-2">
                                <span class="material-symbols-outlined text-4xl opacity-20">event_busy</span>
                                Belum ada periode yang tersedia untuk tahun {{ $selectedYear }}.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="showCloseModal" class="fixed z-[100]" style="display: none; top: 0; right: 0; bottom: 0; left: 0;" x-cloak>
        <div x-show="showCloseModal" x-transition.opacity class="absolute bg-slate-900/50 backdrop-blur-sm" style="top: 0; right: 0; bottom: 0; left: 0;" @click="showCloseModal = false"></div>
        <div x-show="showCloseModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="absolute top-1/2 left-1/2 bg-white rounded-2xl shadow-xl overflow-hidden"
             style="width: 100%; max-width: 28rem; transform: translate(-50%, -50%);">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-surface-container-low">
                <h3 class="font-headline-sm text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">lock</span>
                    Tutup Buku
                </h3>
                <button @click="showCloseModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="{{ route('closing.close') }}" method="POST" class="p-6 space-y-5">
                @csrf
                <input type="hidden" name="period" x-model="selectedPeriod">
                
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <p class="text-sm font-semibold text-amber-800 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">warning</span>
                        Konfirmasi Tutup Buku
                    </p>
                    <p class="text-xs text-amber-700 mt-2">Anda akan menutup buku untuk periode <strong x-text="selectedLabel"></strong>. Setelah ditutup:</p>
                    <ul class="text-xs text-amber-700 mt-1 space-y-1 ml-4 list-disc">
                        <li>Tidak bisa menambah transaksi baru pada periode ini</li>
                        <li>Snapshot keuangan akan direkam secara otomatis</li>
                        <li>Periode bisa dibuka kembali jika perlu koreksi</li>
                    </ul>
                </div>

                <div class="space-y-1.5">
                    <label class="block font-medium text-slate-700 text-sm">Catatan <span class="text-slate-400 text-xs">(opsional)</span></label>
                    <textarea name="notes" rows="3" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary" placeholder="Catatan untuk closing bulan ini..."></textarea>
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                    <button type="button" @click="showCloseModal = false" class="px-5 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-bold hover:bg-slate-200 transition-colors text-sm">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-xl font-bold hover:bg-primary-hover transition-colors shadow-md shadow-primary/20 flex items-center gap-2 text-sm">
                        <span class="material-symbols-outlined text-[18px]">lock</span> Tutup Buku Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showReopenModal" class="fixed z-[100]" style="display: none; top: 0; right: 0; bottom: 0; left: 0;" x-cloak>
        <div x-show="showReopenModal" x-transition.opacity class="absolute bg-slate-900/50 backdrop-blur-sm" style="top: 0; right: 0; bottom: 0; left: 0;" @click="showReopenModal = false"></div>
        <div x-show="showReopenModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="absolute top-1/2 left-1/2 bg-white rounded-2xl shadow-xl overflow-hidden"
             style="width: 100%; max-width: 28rem; transform: translate(-50%, -50%);">
            <div class="p-6 text-center">
                <div class="mx-auto w-16 h-16 rounded-full bg-amber-50 border-2 border-amber-200 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-amber-500 text-4xl">lock_open</span>
                </div>
                <h3 class="text-lg font-bold text-on-surface mb-2">Buka Kembali Periode?</h3>
                <p class="text-sm text-slate-500 mb-5">Anda akan membuka kembali periode <strong class="text-on-surface" x-text="reopenLabel"></strong>. Transaksi pada periode ini akan bisa diubah kembali.</p>
                <form :action="reopenAction" method="POST" x-data="{ submitting: false }" @submit="submitting = true">
                    @csrf
                    <div class="flex gap-3">
                        <button type="button" @click="showReopenModal = false" class="flex-1 px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-semibold text-sm hover:bg-slate-200 transition-colors">Batal</button>
                        <button type="submit" :disabled="submitting" class="flex-1 px-4 py-2.5 bg-amber-500 text-white rounded-xl font-semibold text-sm hover:bg-amber-600 transition-all shadow-md flex items-center justify-center gap-2 disabled:opacity-50">
                            <span x-show="!submitting" class="material-symbols-outlined text-[18px]">lock_open</span>
                            <span x-show="submitting" class="animate-spin material-symbols-outlined text-[18px]">progress_activity</span>
                            <span x-text="submitting ? 'Memproses...' : 'Ya, Buka Periode'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
