@props([
    'placeholder' => 'Cari data...',
    'showDate' => true,
    'customFilters' => null
])

<div class="glass-panel border border-surface-variant rounded-xl p-4 mb-6" x-data="{ showCustomDate: '{{ request('date_range') === 'custom' }}', searchTimeout: null }" @keydown.debounce.500ms="document.getElementById('filter-form').submit()">
    <form id="filter-form" action="{{ url()->current() }}" method="GET" class="flex flex-wrap gap-4 items-end">
        <!-- Preserve Sorting -->
        <input type="hidden" name="sort_field" value="{{ request('sort_field') }}">
        <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">

        <!-- Search -->
        <div class="flex-1 min-w-[300px] space-y-1.5">
            <label class="block font-medium text-slate-700 text-xs uppercase tracking-wider">Pencarian</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="{{ $placeholder }}"
                       @keyup="clearTimeout($data.searchTimeout); $data.searchTimeout = setTimeout(() => document.getElementById('filter-form').submit(), 300)"
                       class="w-full bg-white border border-slate-200 text-on-surface rounded-lg pl-10 pr-4 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all">
            </div>
        </div>

        @if($showDate)
        <!-- Date Range -->
        <div class="w-48 space-y-1.5">
            <label class="block font-medium text-slate-700 text-xs uppercase tracking-wider">Rentang Waktu</label>
            <select name="date_range" @change="showCustomDate = ($el.value === 'custom'); setTimeout(() => document.getElementById('filter-form').submit(), 100)" 
                    class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary appearance-none outline-none">
                <option value="">Semua Waktu</option>
                <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                <option value="7_days" {{ request('date_range') == '7_days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                <option value="30_days" {{ request('date_range') == '30_days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                <option value="this_quarter" {{ request('date_range') == 'this_quarter' ? 'selected' : '' }}>Triwulan (3 Bln)</option>
                <option value="6_months" {{ request('date_range') == '6_months' ? 'selected' : '' }}>6 Bulan Terakhir</option>
                <option value="this_year" {{ request('date_range') == 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
                <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Rentang Kustom</option>
            </select>
        </div>

        <!-- Custom Date Inputs -->
        <div x-show="showCustomDate" x-transition class="flex gap-2 items-end">
            <div class="space-y-1.5">
                <label class="block font-medium text-slate-700 text-[10px] uppercase tracking-wider">Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                       @change="setTimeout(() => document.getElementById('filter-form').submit(), 100)"
                       class="bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none">
            </div>
            <div class="space-y-1.5">
                <label class="block font-medium text-slate-700 text-[10px] uppercase tracking-wider">Selesai</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                       @change="setTimeout(() => document.getElementById('filter-form').submit(), 100)"
                       class="bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none">
            </div>
        </div>
        @endif

        {{ $customFilters }}

        <!-- Actions -->
        <div class="flex items-center gap-2">
            <button type="submit" class="px-5 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-primary-hover transition-all shadow-sm flex items-center space-x-2">
                <span class="material-symbols-outlined text-[18px]">filter_list</span>
                <span>Filter</span>
            </button>
            
            @if(request()->anyFilled(['search', 'date_range', 'start_date', 'end_date', 'sort_field']))
                <a href="{{ url()->current() }}" class="px-4 py-2 text-slate-500 hover:text-error transition-colors text-sm font-medium flex items-center gap-1">
                    <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>
