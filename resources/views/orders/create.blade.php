@extends('layouts.app')

@section('title', 'New Project')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Breadcrumbs -->
    <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm mb-4">
        <a href="{{ route('orders.index') }}" class="hover:text-primary transition-colors">Orders</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface">New Project</span>
    </nav>

    <div class="bg-surface-container-low border border-surface-variant rounded-xl overflow-hidden shadow-xl">
        <div class="p-6 border-b border-surface-variant bg-surface-container-lowest/50 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-primary-container/10 blur-2xl rounded-full -mr-10 -mt-10 pointer-events-none"></div>
            <div class="flex items-center space-x-3 mb-1 relative z-10">
                <span class="material-symbols-outlined text-primary text-3xl">add_shopping_cart</span>
                <h3 class="font-headline-md text-headline-md text-on-surface">Register New Project</h3>
            </div>
            <p class="font-body-sm text-body-sm text-slate-500 relative z-10">Create a new furniture order and initialize manufacturing tracking.</p>
        </div>

        <form action="{{ route('orders.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Selection -->
                <div class="space-y-1.5 md:col-span-2">
                    <label for="customer_id" class="block font-medium text-slate-700 text-sm">Customer <span class="text-error">*</span></label>
                    <select name="customer_id" id="customer_id" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors appearance-none">
                        <option disabled selected value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-500">Don't see the customer? <a href="{{ route('customers.create') }}" class="text-primary hover:underline">Add new customer</a></p>
                </div>

                <!-- Project Name -->
                <div class="space-y-1.5 md:col-span-2">
                    <label for="description" class="block font-medium text-slate-700 text-sm">Project / Item Name <span class="text-error">*</span></label>
                    <input type="text" name="description" id="description" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors" placeholder="e.g. Teak Dining Table (6 Seater)">
                </div>

                <!-- Pricing -->
                <div class="space-y-1.5">
                    <label for="selling_price" class="block font-medium text-slate-700 text-sm">Selling Price (Deal) <span class="text-error">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-500 text-sm">Rp</div>
                        <input type="number" name="selling_price" id="selling_price" required class="w-full bg-white border border-slate-200 text-on-surface rounded-lg pl-10 pr-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors font-data-mono" placeholder="0">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="dp_amount" class="block font-medium text-slate-700 text-sm">Down Payment (DP)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-500 text-sm">Rp</div>
                        <input type="number" name="dp_amount" id="dp_amount" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg pl-10 pr-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors font-data-mono" placeholder="0" value="0">
                    </div>
                </div>

                <!-- Deadline -->
                <div class="space-y-1.5 md:col-span-2">
                    <label for="deadline" class="block font-medium text-slate-700 text-sm">Target Deadline</label>
                    <input type="date" name="deadline" id="deadline" class="w-full bg-white border border-slate-200 text-on-surface rounded-lg px-3 py-2 focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors">
                </div>
            </div>

            <div class="pt-6 border-t border-surface-variant flex justify-end gap-3">
                <a href="{{ route('orders.index') }}" class="px-5 py-2 rounded-lg border border-surface-variant text-slate-600 hover:bg-surface-container-high transition-colors font-medium text-sm">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-container text-on-primary-container rounded-lg font-semibold hover:bg-primary transition-colors shadow-lg flex items-center gap-2 text-sm">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Initialize Project
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
