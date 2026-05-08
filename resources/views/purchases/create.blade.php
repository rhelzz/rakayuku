@extends('layouts.app')

@section('title', 'Catat Pembelian Bahan (Invoice)')

@section('content')
<div class="max-w-5xl mx-auto" x-data="purchaseForm()">
    <!-- Breadcrumbs -->
    <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <a href="{{ route('purchases.index') }}" class="hover:text-primary transition-colors">Pembelian</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface">Invoice Baru</span>
    </nav>

    <form action="{{ route('purchases.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Invoice Info -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-surface-container-low border border-surface-variant rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-surface-variant bg-surface-container-lowest/50">
                        <h3 class="font-headline-sm text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">receipt_long</span>
                            Informasi Invoice
                        </h3>
                    </div>
                    
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5 md:col-span-2">
                            <label for="supplier_name" class="block font-medium text-slate-700 text-sm">Nama Pemasok / Toko</label>
                            <input type="text" name="supplier_name" id="supplier_name" value="{{ old('supplier_name') }}" class="w-full bg-white border border-slate-200 text-on-surface rounded-xl px-4 py-2.5 focus:border-primary focus:ring-1 focus:ring-primary transition-all @error('supplier_name') border-error @enderror" placeholder="Misal: UD. Rimba Jaya">
                            @error('supplier_name') <p class="text-[11px] text-error mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="invoice_number" class="block font-medium text-slate-700 text-sm">Nomor Invoice</label>
                            <input type="text" name="invoice_number" id="invoice_number" value="{{ old('invoice_number') }}" class="w-full bg-white border border-slate-200 text-on-surface rounded-xl px-4 py-2.5 focus:border-primary focus:ring-1 focus:ring-primary transition-all @error('invoice_number') border-error @enderror" placeholder="INV/{{ date('Y') }}/...">
                            @error('invoice_number') <p class="text-[11px] text-error mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="purchase_date" class="block font-medium text-slate-700 text-sm">Tanggal Belanja <span class="text-error">*</span></label>
                            <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required class="w-full bg-white border border-slate-200 text-on-surface rounded-xl px-4 py-2.5 focus:border-primary focus:ring-1 focus:ring-primary transition-all @error('purchase_date') border-error @enderror">
                            @error('purchase_date') <p class="text-[11px] text-error mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="bg-surface-container-low border border-surface-variant rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-surface-variant bg-surface-container-lowest/50 flex justify-between items-center">
                        <h3 class="font-headline-sm text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">inventory_2</span>
                            Daftar Bahan Baku
                        </h3>
                        <button type="button" @click="addItem()" class="px-4 py-1.5 bg-primary/10 text-primary hover:bg-primary/20 rounded-lg text-xs font-bold transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">add_circle</span>
                            Tambah Baris
                        </button>
                    </div>

                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50/50">
                                <tr class="border-b border-surface-variant">
                                    <th class="py-3 px-6 font-label-caps text-slate-500 uppercase text-[10px]">Bahan Baku</th>
                                    <th class="py-3 px-4 font-label-caps text-slate-500 uppercase text-[10px] w-28">Jumlah</th>
                                    <th class="py-3 px-4 font-label-caps text-slate-500 uppercase text-[10px] w-48 text-right">Harga Satuan</th>
                                    <th class="py-3 px-6 w-12"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-variant/30">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="group hover:bg-slate-50/30 transition-colors">
                                        <td class="py-4 px-6">
                                            <select :name="'items['+index+'][material_id]'" required class="w-full bg-white border-2 border-slate-200 text-on-surface rounded-xl px-4 py-2.5 text-sm focus:border-primary focus:ring-2 focus:ring-primary/10 appearance-none transition-all cursor-pointer hover:border-slate-300 font-medium" style="background-image: url('data:image/svg+xml;utf8,<svg fill=\"%234B5563\" height=\"24\" viewBox=\"0 0 24 24\" width=\"24\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7 10l5 5 5-5z\"/></svg>'); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1.5em 1.5em; padding-right: 2.5rem;">
                                                <option disabled selected value="">-- Pilih Material --</option>
                                                @foreach($materials as $m)
                                                    <option value="{{ $m->id }}">{{ $m->name }}{{ $m->type ? ' (' . $m->type . ')' : '' }} - {{ $m->unit }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="py-4 px-4">
                                            <input type="number" :name="'items['+index+'][qty]'" required step="any" min="0.01" x-model="item.qty" class="w-full bg-white border border-slate-200 text-on-surface rounded-xl px-3 py-2 text-sm text-right font-data-mono focus:border-primary focus:ring-1 focus:ring-primary transition-all" placeholder="0">
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="relative" x-data="{ 
                                                updateMask(val) {
                                                    item.displayPrice = formatRupiahJS(val);
                                                    item.price = item.displayPrice.replace(/\./g, '');
                                                }
                                            }">
                                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 text-xs font-data-mono">Rp</div>
                                                <input type="text" 
                                                       x-model="item.displayPrice"
                                                       x-on:input="updateMask($event.target.value)"
                                                       required 
                                                       class="w-full bg-white border border-slate-200 text-on-surface rounded-xl pl-8 pr-3 py-2 text-sm text-right font-data-mono focus:border-primary focus:ring-1 focus:ring-primary transition-all" 
                                                       placeholder="0">
                                                <input type="hidden" :name="'items['+index+'][price]'" x-model="item.price">
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-error/10 hover:text-error transition-all">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate-50/80 font-bold border-t border-surface-variant">
                                    <td colspan="2" class="py-4 px-6 text-sm text-slate-600">Total Estimasi Belanja</td>
                                    <td class="py-4 px-4 text-right text-primary font-data-mono">
                                        Rp <span x-text="formatNumber(calculateTotal())"></span>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Proof Upload -->
            <div class="space-y-6">
                <div class="bg-surface-container-low border border-surface-variant rounded-2xl shadow-sm overflow-hidden h-fit sticky top-6">
                    <div class="p-6 border-b border-surface-variant bg-surface-container-lowest/50">
                        <h3 class="font-headline-sm text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">add_a_photo</span>
                            Bukti Invoice
                        </h3>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div class="relative group border-2 border-dashed border-slate-200 rounded-2xl hover:border-primary transition-all bg-slate-50/50 p-4 text-center overflow-hidden min-h-[220px] flex flex-col items-center justify-center">
                            <template x-if="!imageUrl">
                                <div class="space-y-2">
                                    <span class="material-symbols-outlined text-4xl text-slate-300 group-hover:text-primary transition-colors">cloud_upload</span>
                                    <p class="text-xs text-slate-500 font-medium">Klik atau seret gambar ke sini</p>
                                    <p class="text-[10px] text-slate-400">Format: JPG, PNG (Max 3MB)</p>
                                </div>
                            </template>

                            <template x-if="imageUrl">
                                <div class="relative w-full h-full">
                                    <img :src="imageUrl" class="rounded-xl object-contain max-h-[300px] w-full shadow-sm mx-auto">
                                    <button type="button" @click="removeImage()" class="absolute -top-2 -right-2 bg-error text-white w-7 h-7 rounded-full shadow-lg flex items-center justify-center hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                </div>
                            </template>

                            <input type="file" name="invoice_proof" class="absolute inset-0 opacity-0 cursor-pointer" @change="previewImage($event)" accept="image/*">
                        </div>
                        @error('invoice_proof') <p class="text-[11px] text-error">{{ $message }}</p> @enderror

                        <div class="pt-4 space-y-3">
                            <button type="submit" class="w-full py-3 bg-primary text-white rounded-xl font-bold hover:bg-primary-hover shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[20px]">save</span>
                                Simpan Pembelian
                            </button>
                            <a href="{{ route('purchases.index') }}" class="w-full py-3 bg-white border border-slate-200 text-slate-600 rounded-xl font-semibold hover:bg-slate-50 text-center block text-sm transition-colors">
                                Batalkan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function purchaseForm() {
        return {
            items: [{ material_id: '', qty: 0, price: 0 }],
            imageUrl: null,

            addItem() {
                this.items.push({ material_id: '', qty: 0, price: 0 });
            },

            removeItem(index) {
                this.items.splice(index, 1);
            },

            calculateTotal() {
                return this.items.reduce((sum, item) => sum + (item.qty * item.price), 0);
            },

            previewImage(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imageUrl = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },

            removeImage() {
                this.imageUrl = null;
                document.querySelector('input[name="invoice_proof"]').value = '';
            },

            formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            }
        }
    }
</script>
@endsection
