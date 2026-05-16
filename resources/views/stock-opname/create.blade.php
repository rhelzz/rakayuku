@extends('layouts.app')
@section('title', 'Stock Opname Baru')
@section('content')
<div class="max-w-5xl mx-auto" x-data="stockOpnameForm()">
    <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <a href="{{ route('stock-opname.index') }}" class="hover:text-primary transition-colors">Stock Opname</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface">Baru</span>
    </nav>
    <div class="bg-surface-container-low border border-surface-variant rounded-xl overflow-hidden shadow-xl">
        <div class="p-6 border-b border-surface-variant bg-surface-container-lowest/50 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-primary-container/10 blur-2xl rounded-full -mr-10 -mt-10 pointer-events-none"></div>
            <div class="flex items-center space-x-3 mb-1 relative z-10">
                <span class="material-symbols-outlined text-primary text-3xl">fact_check</span>
                <h3 class="font-headline-md text-headline-md text-on-surface">Stock Opname Baru</h3>
            </div>
            <p class="font-body-sm text-body-sm text-slate-500 relative z-10">Pilih bahan yang akan diopname dan input jumlah stok fisik.</p>
        </div>
        <form action="{{ route('stock-opname.store') }}" method="POST" class="p-6 space-y-6" @submit="submitting = true">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block font-medium text-slate-700 text-sm">No. Opname</label>
                    <input type="text" value="{{ $opnameNumber }}" disabled class="w-full bg-slate-50 border border-slate-200 text-slate-500 rounded-lg px-3 py-2 font-data-mono">
                </div>
                <div class="space-y-1.5">
                    <label for="opname_date" class="block font-medium text-slate-700 text-sm">Tanggal Opname <span class="text-error">*</span></label>
                    <input type="date" name="opname_date" id="opname_date" value="{{ old('opname_date', now()->format('Y-m-d')) }}" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary focus:ring-1 focus:ring-primary font-data-mono">
                </div>
            </div>
            <div class="space-y-1.5">
                <label for="notes" class="block font-medium text-slate-700 text-sm">Catatan <span class="text-slate-400 text-xs">(opsional)</span></label>
                <textarea name="notes" id="notes" rows="2" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary" placeholder="Catatan stock opname...">{{ old('notes') }}</textarea>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <label class="block font-medium text-slate-700 text-sm">Pilih Bahan untuk Opname <span class="text-error">*</span></label>
                    <button type="button" @click="selectAll()" class="text-xs text-primary font-bold hover:underline">Pilih Semua</button>
                </div>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                    <input type="text" x-model="searchQuery" placeholder="Cari bahan..." class="w-full bg-white border border-slate-200 rounded-lg pl-10 pr-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                <div class="border border-surface-variant rounded-xl overflow-hidden">
                    <div class="max-h-[500px] overflow-y-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-surface-container-high/50 border-b border-surface-variant sticky top-0 z-10">
                                <tr>
                                    <th class="px-4 py-3 text-[10px] tracking-widest w-10 text-center"><input type="checkbox" @change="toggleAll($event.target.checked)" :checked="allSelected" class="rounded border-slate-300 text-primary focus:ring-primary"></th>
                                    <th class="px-4 py-3 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Nama Bahan</th>
                                    <th class="px-4 py-3 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Kode</th>
                                    <th class="px-4 py-3 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest">Satuan</th>
                                    <th class="px-4 py-3 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Stok Sistem</th>
                                    <th class="px-4 py-3 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Stok Aktual</th>
                                    <th class="px-4 py-3 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest text-right">Selisih</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-variant/30 bg-white/50">
                                <template x-for="(material, index) in filteredMaterials" :key="material.id">
                                    <tr class="hover:bg-surface-container-low transition-colors" :class="{ 'bg-primary/5': material.selected }">
                                        <td class="px-4 py-3 text-center"><input type="checkbox" x-model="material.selected" class="rounded border-slate-300 text-primary focus:ring-primary"></td>
                                        <td class="px-4 py-3"><div class="font-medium text-on-surface text-sm" x-text="material.display_name"></div></td>
                                        <td class="px-4 py-3 font-data-mono text-xs text-primary font-semibold" x-text="material.code"></td>
                                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 text-xs" x-text="material.unit"></span></td>
                                        <td class="px-4 py-3 text-right font-data-mono text-sm" x-text="fmtQty(material.system_qty)"></td>
                                        <td class="px-4 py-3 text-right">
                                            <input type="number" step="0.01" min="0" x-model.number="material.actual_qty" :disabled="!material.selected"
                                                   class="w-24 bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-right text-sm font-data-mono focus:border-primary focus:ring-1 focus:ring-primary disabled:bg-slate-50 disabled:text-slate-400"
                                                   :class="{ 'border-primary bg-primary/5': material.selected }">
                                        </td>
                                        <td class="px-4 py-3 text-right font-data-mono text-sm font-bold" :class="diffClass(material)">
                                            <span x-show="material.selected" x-text="fmtDiff(material.actual_qty - material.system_qty)"></span>
                                            <span x-show="!material.selected" class="text-slate-300">-</span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="flex items-center justify-between text-xs text-slate-500">
                    <span x-text="selectedCount + ' bahan dipilih'"></span>
                    <div class="flex gap-4">
                        <span class="text-emerald-600 font-bold" x-show="totalSurplus > 0" x-text="'Surplus: +' + fmtQty(totalSurplus)"></span>
                        <span class="text-error font-bold" x-show="totalDeficit > 0" x-text="'Defisit: -' + fmtQty(totalDeficit)"></span>
                    </div>
                </div>
            </div>
            <template x-for="(material, index) in selectedMaterials" :key="material.id">
                <div>
                    <input type="hidden" :name="'items[' + index + '][material_id]'" :value="material.id">
                    <input type="hidden" :name="'items[' + index + '][actual_qty]'" :value="material.actual_qty">
                </div>
            </template>
            <div class="pt-6 border-t border-surface-variant flex justify-end gap-3">
                <a href="{{ route('stock-opname.index') }}" class="px-5 py-2 rounded-lg border border-surface-variant text-slate-600 hover:bg-surface-container-high transition-colors font-medium text-sm">Batal</a>
                <button type="submit" :disabled="submitting || selectedCount === 0" class="px-6 py-2 bg-primary-container text-on-primary-container rounded-lg font-semibold hover:bg-primary transition-colors shadow-lg flex items-center gap-2 text-sm disabled:opacity-50">
                    <span class="material-symbols-outlined text-[18px]" x-show="!submitting">save</span>
                    <span class="animate-spin h-4 w-4 border-2 border-on-primary-container border-t-transparent rounded-full" x-show="submitting" x-cloak></span>
                    <span x-text="submitting ? 'Menyimpan...' : 'Simpan Draft'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
<script>
function stockOpnameForm() {
    return {
        submitting: false, searchQuery: '',
        materials: [@foreach($materials as $m){id:{{ $m->id }},name:@json($m->name),display_name:@json($m->display_name),code:@json($m->code),unit:@json($m->unit),system_qty:{{ $m->current_qty }},actual_qty:{{ $m->current_qty }},selected:false},@endforeach],
        get filteredMaterials() { const q=this.searchQuery.toLowerCase(); return q ? this.materials.filter(m=>m.name.toLowerCase().includes(q)||m.code.toLowerCase().includes(q)) : this.materials; },
        get selectedMaterials() { return this.materials.filter(m=>m.selected); },
        get selectedCount() { return this.selectedMaterials.length; },
        get allSelected() { return this.filteredMaterials.length>0 && this.filteredMaterials.every(m=>m.selected); },
        get totalSurplus() { return this.selectedMaterials.reduce((s,m)=>{const d=m.actual_qty-m.system_qty; return d>0?s+d:s;},0); },
        get totalDeficit() { return this.selectedMaterials.reduce((s,m)=>{const d=m.actual_qty-m.system_qty; return d<0?s+Math.abs(d):s;},0); },
        selectAll() { this.materials.forEach(m=>m.selected=true); },
        toggleAll(c) { this.filteredMaterials.forEach(m=>m.selected=c); },
        fmtQty(v) { return v%1===0?v.toString():v.toFixed(2); },
        fmtDiff(v) { const f=this.fmtQty(Math.abs(v)); return v>0?'+'+f:v<0?'-'+f:'0'; },
        diffClass(m) { if(!m.selected) return 'text-slate-400'; const d=m.actual_qty-m.system_qty; return d>0?'text-emerald-600':d<0?'text-error':'text-slate-400'; },
    };
}
</script>
@endsection
