@extends('layouts.app')

@section('title', 'Project Detail - ' . $order->order_number)

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'overview' }">
    <!-- Header -->
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('orders.index') }}" class="hover:text-primary-container transition-colors">Orders</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">{{ $order->order_number }}</span>
        </nav>
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-xl bg-surface-container-high flex items-center justify-center text-primary-container border border-surface-variant relative">
                    <span class="material-symbols-outlined text-3xl">shopping_cart</span>
                    @if($order->status == 'IN_PRODUCTION')
                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-primary-container rounded-full animate-ping"></div>
                    @endif
                </div>
                <div>
                    <h2 class="font-headline-md text-headline-md text-on-surface">{{ $order->project_name ?? 'Custom Furniture' }}</h2>
                    <p class="font-body-sm text-body-sm text-slate-400">Client: {{ $order->customer->name }} | Deadline: {{ $order->deadline ? \Carbon\Carbon::parse($order->deadline)->format('d M Y') : '-' }}</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                @if($order->status == 'PENDING')
                    <form action="{{ route('orders.start-production', $order) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-primary-container text-on-primary-container rounded-lg font-semibold hover:bg-primary transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">play_arrow</span>
                            Start Production
                        </button>
                    </form>
                @elseif($order->status == 'IN_PRODUCTION')
                    <form action="{{ route('orders.finish-production', $order) }}" method="POST" onsubmit="return confirm('Finish production and calculate final HPP/Profit?')">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700 transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">task_alt</span>
                            Finish Project
                        </button>
                    </form>
                @else
                    <span class="px-6 py-2 bg-surface-container-high text-slate-400 rounded-lg font-semibold border border-surface-variant flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">done_all</span>
                        Completed
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats & Tabs -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Stats Card -->
        <div class="lg:col-span-3 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="glass-panel p-4 rounded-xl border border-slate-800">
                    <p class="font-label-caps text-label-caps text-slate-500 uppercase">Selling Price</p>
                    <p class="text-xl font-bold text-on-surface mt-1">Rp {{ number_format($order->selling_price, 0, ',', '.') }}</p>
                </div>
                <div class="glass-panel p-4 rounded-xl border border-slate-800">
                    <p class="font-label-caps text-label-caps text-slate-500 uppercase">Current Production Cost</p>
                    <p class="text-xl font-bold text-on-surface mt-1">Rp {{ number_format($order->total_cost, 0, ',', '.') }}</p>
                </div>
                <div class="glass-panel p-4 rounded-xl border border-slate-800">
                    <p class="font-label-caps text-label-caps text-slate-500 uppercase">Profit</p>
                    @php $profit = ($order->status === 'FINISHED') ? $order->profit : ($order->selling_price - $order->total_cost); @endphp
                    <p class="text-xl font-bold {{ $profit >= 0 ? 'text-emerald-500' : 'text-error' }} mt-1">Rp {{ number_format($profit, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Interactive Tabs -->
            <div class="glass-panel rounded-xl border border-slate-800 overflow-hidden">
                <div class="flex border-b border-slate-800 bg-surface-container-low/50">
                    <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'text-primary-container border-b-2 border-primary-container bg-surface-container-high/30' : 'text-slate-500 hover:text-slate-300'" class="px-6 py-4 text-sm font-semibold transition-all">Overview</button>
                    <button @click="activeTab = 'materials'" :class="activeTab === 'materials' ? 'text-primary-container border-b-2 border-primary-container bg-surface-container-high/30' : 'text-slate-500 hover:text-slate-300'" class="px-6 py-4 text-sm font-semibold transition-all">Materials (Stock Usage)</button>
                    <button @click="activeTab = 'costs'" :class="activeTab === 'costs' ? 'text-primary-container border-b-2 border-primary-container bg-surface-container-high/30' : 'text-slate-500 hover:text-slate-300'" class="px-6 py-4 text-sm font-semibold transition-all">Additional Costs</button>
                    <button @click="activeTab = 'payments'" :class="activeTab === 'payments' ? 'text-primary-container border-b-2 border-primary-container bg-surface-container-high/30' : 'text-slate-500 hover:text-slate-300'" class="px-6 py-4 text-sm font-semibold transition-all">Payments</button>
                </div>

                <div class="p-6 min-h-[400px]">
                    <!-- Overview Tab -->
                    <div x-show="activeTab === 'overview'" class="space-y-6">
                        <div class="grid grid-cols-2 gap-8">
                            <div>
                                <h4 class="text-on-surface font-semibold mb-4 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary-container text-[20px]">person</span> Customer Details
                                </h4>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-[10px] font-label-caps text-slate-500 uppercase tracking-widest">Name</p>
                                        <p class="text-sm text-on-surface">{{ $order->customer->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-label-caps text-slate-500 uppercase tracking-widest">Phone</p>
                                        <p class="text-sm text-on-surface">{{ $order->customer->phone }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-label-caps text-slate-500 uppercase tracking-widest">Address</p>
                                        <p class="text-sm text-on-surface">{{ $order->customer->address ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-on-surface font-semibold mb-4 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary-container text-[20px]">payments</span> Financials
                                </h4>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-[10px] font-label-caps text-slate-500 uppercase tracking-widest">Total Deal</p>
                                        <p class="text-sm text-on-surface">Rp {{ number_format($order->selling_price, 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-label-caps text-slate-500 uppercase tracking-widest">Amount Paid</p>
                                        <p class="text-sm text-emerald-500">Rp {{ number_format($order->payments()->sum('amount'), 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-label-caps text-slate-500 uppercase tracking-widest">Payment Status</p>
                                        <span class="px-2 py-0.5 {{ $order->payment_status === 'PAID' ? 'bg-emerald-500/20 text-emerald-500 border-emerald-500/30' : 'bg-warning-container/20 text-warning border-warning/30' }} rounded text-[10px] font-bold border">
                                            {{ $order->payment_status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Materials Tab -->
                    <div x-show="activeTab === 'materials'" class="space-y-6">
                        @if($order->status == 'IN_PRODUCTION')
                        <form action="{{ route('orders.add-material', $order) }}" method="POST" class="bg-surface-container-high/30 p-4 rounded-xl border border-slate-700 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            @csrf
                            <div class="md:col-span-2 space-y-1.5">
                                <label class="text-[11px] font-label-caps text-slate-400 uppercase">Select Material</label>
                                <select name="material_id" required class="w-full bg-surface-container-highest border border-slate-700 text-on-surface rounded-lg px-3 py-2 text-sm">
                                    <option disabled selected value="">Choose...</option>
                                    @foreach($materials as $m)
                                        <option value="{{ $m->id }}">{{ $m->name }} (Stock: {{ $m->current_qty }} {{ $m->unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-label-caps text-slate-400 uppercase">Quantity</label>
                                <input type="number" name="qty" required step="0.01" class="w-full bg-surface-container-highest border border-slate-700 text-on-surface rounded-lg px-3 py-2 text-sm" placeholder="0">
                            </div>
                            <button type="submit" class="bg-primary-container text-on-primary-container py-2 rounded-lg font-semibold text-sm hover:bg-primary transition-colors">Add to Project</button>
                        </form>
                        @endif

                        <table class="w-full text-left">
                            <thead class="border-b border-slate-800">
                                <tr>
                                    <th class="py-3 font-label-caps text-slate-400 uppercase text-[11px]">Material</th>
                                    <th class="py-3 font-label-caps text-slate-400 uppercase text-[11px] text-right">Qty Used</th>
                                    <th class="py-3 font-label-caps text-slate-400 uppercase text-[11px] text-right">Unit HPP (at usage)</th>
                                    <th class="py-3 font-label-caps text-slate-400 uppercase text-[11px] text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @forelse($order->materials as $om)
                                <tr class="border-b border-slate-800/50">
                                    <td class="py-3 text-on-surface">{{ $om->material->name }}</td>
                                    <td class="py-3 text-right text-on-surface-variant">{{ number_format($om->qty_used, 2) }} {{ $om->material->unit }}</td>
                                    <td class="py-3 text-right text-on-surface-variant">Rp {{ number_format($om->price_snapshot, 0, ',', '.') }}</td>
                                    <td class="py-3 text-right font-semibold text-on-surface">Rp {{ number_format($om->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-500 italic">No materials assigned to this project yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Costs Tab -->
                    <div x-show="activeTab === 'costs'" class="space-y-6">
                        @if($order->status == 'IN_PRODUCTION')
                        <form action="{{ route('orders.add-cost', $order) }}" method="POST" class="bg-surface-container-high/30 p-4 rounded-xl border border-slate-700 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            @csrf
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-label-caps text-slate-400 uppercase">Type</label>
                                <select name="type" required class="w-full bg-surface-container-highest border border-slate-700 text-on-surface rounded-lg px-3 py-2 text-sm">
                                    <option value="LABOR_OVERTIME">Labor Overtime</option>
                                    <option value="TRANSPORT">Transport</option>
                                    <option value="TOOLS">Tools</option>
                                    <option value="OTHER">Other</option>
                                </select>
                            </div>
                            <div class="space-y-1.5 md:col-span-1">
                                <label class="text-[11px] font-label-caps text-slate-400 uppercase">Description</label>
                                <input type="text" name="description" required class="w-full bg-surface-container-highest border border-slate-700 text-on-surface rounded-lg px-3 py-2 text-sm" placeholder="e.g. Shipping fee">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-label-caps text-slate-400 uppercase">Amount (Rp)</label>
                                <input type="number" name="amount" required class="w-full bg-surface-container-highest border border-slate-700 text-on-surface rounded-lg px-3 py-2 text-sm" placeholder="0">
                            </div>
                            <button type="submit" class="bg-primary-container text-on-primary-container py-2 rounded-lg font-semibold text-sm hover:bg-primary transition-colors">Add Cost</button>
                        </form>
                        @endif

                        <table class="w-full text-left">
                            <thead class="border-b border-slate-800">
                                <tr>
                                    <th class="py-3 font-label-caps text-slate-400 uppercase text-[11px]">Type</th>
                                    <th class="py-3 font-label-caps text-slate-400 uppercase text-[11px]">Description</th>
                                    <th class="py-3 font-label-caps text-slate-400 uppercase text-[11px] text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @forelse($order->productionCosts as $cost)
                                <tr class="border-b border-slate-800/50">
                                    <td class="py-3 text-on-surface">{{ $cost->type }}</td>
                                    <td class="py-3 text-on-surface">{{ $cost->description }}</td>
                                    <td class="py-3 text-right font-semibold text-on-surface">Rp {{ number_format($cost->amount, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="py-8 text-center text-slate-500 italic">No additional costs recorded.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Payments Tab -->
                    <div x-show="activeTab === 'payments'" class="space-y-6">
                        @if($order->payment_status !== 'PAID')
                        <form action="{{ route('orders.pay', $order) }}" method="POST" class="bg-surface-container-high/30 p-4 rounded-xl border border-slate-700 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            @csrf
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-label-caps text-slate-400 uppercase">Type</label>
                                <select name="type" required class="w-full bg-surface-container-highest border border-slate-700 text-on-surface rounded-lg px-3 py-2 text-sm">
                                    <option value="DP">Down Payment (DP)</option>
                                    <option value="FINAL" selected>Final Payment</option>
                                </select>
                            </div>
                            <div class="md:col-span-2 space-y-1.5">
                                <label class="text-[11px] font-label-caps text-slate-400 uppercase">Amount (Rp)</label>
                                <input type="number" name="amount" required class="w-full bg-surface-container-highest border border-slate-700 text-on-surface rounded-lg px-3 py-2 text-sm" placeholder="0">
                            </div>
                            <button type="submit" class="bg-primary-container text-on-primary-container py-2 rounded-lg font-semibold text-sm hover:bg-primary transition-colors">Add Payment</button>
                        </form>
                        @endif

                        <table class="w-full text-left">
                            <thead class="border-b border-slate-800">
                                <tr>
                                    <th class="py-3 font-label-caps text-slate-400 uppercase text-[11px]">Date</th>
                                    <th class="py-3 font-label-caps text-slate-400 uppercase text-[11px]">Type</th>
                                    <th class="py-3 font-label-caps text-slate-400 uppercase text-[11px] text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @forelse($order->payments as $payment)
                                <tr class="border-b border-slate-800/50">
                                    <td class="py-3 text-on-surface">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                                    <td class="py-3 text-on-surface">{{ $payment->type }}</td>
                                    <td class="py-3 text-right font-semibold text-on-surface">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="py-8 text-center text-slate-500 italic">No payments recorded.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / Lifecycle -->
        <div class="space-y-6">
            <div class="glass-panel p-6 rounded-xl border border-slate-800">
                <h4 class="text-on-surface font-semibold mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary-container text-[20px]">history</span> Project Lifecycle
                </h4>
                <div class="space-y-6 relative">
                    <!-- Vertical Line -->
                    <div class="absolute left-[11px] top-2 bottom-2 w-0.5 bg-slate-800"></div>
                    
                    <!-- Pending -->
                    <div class="relative flex items-start gap-4">
                        <div class="w-6 h-6 rounded-full bg-emerald-500/20 border-2 border-emerald-500 flex items-center justify-center z-10">
                            <span class="material-symbols-outlined text-[12px] text-emerald-500">check</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-on-surface">Order Received</p>
                            <p class="text-[11px] text-slate-500">{{ $order->created_at->format('d M Y') }}</p>
                        </div>
                    </div>

                    <!-- In Production -->
                    <div class="relative flex items-start gap-4">
                        <div class="w-6 h-6 rounded-full {{ $order->status != 'PENDING' ? 'bg-primary-container/20 border-2 border-primary-container' : 'bg-slate-800 border-2 border-slate-700' }} flex items-center justify-center z-10">
                            @if($order->status != 'PENDING')
                                <span class="material-symbols-outlined text-[12px] text-primary-container fill">factory</span>
                            @else
                                <span class="material-symbols-outlined text-[12px] text-slate-500">circle</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-semibold {{ $order->status != 'PENDING' ? 'text-on-surface' : 'text-slate-500' }}">Production</p>
                            @if($order->status == 'IN_PRODUCTION')
                                <p class="text-[11px] text-primary-container animate-pulse">In Progress...</p>
                            @elseif($order->status == 'FINISHED')
                                <p class="text-[11px] text-emerald-500">Completed</p>
                            @else
                                <p class="text-[11px] text-slate-500">Waiting...</p>
                            @endif
                        </div>
                    </div>

                    <!-- Finished -->
                    <div class="relative flex items-start gap-4">
                        <div class="w-6 h-6 rounded-full {{ $order->status == 'FINISHED' ? 'bg-emerald-500/20 border-2 border-emerald-500' : 'bg-slate-800 border-2 border-slate-700' }} flex items-center justify-center z-10">
                            @if($order->status == 'FINISHED')
                                <span class="material-symbols-outlined text-[12px] text-emerald-500">check</span>
                            @else
                                <span class="material-symbols-outlined text-[12px] text-slate-500">circle</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-semibold {{ $order->status == 'FINISHED' ? 'text-on-surface' : 'text-slate-500' }}">Delivery & Close</p>
                            <p class="text-[11px] text-slate-500">{{ $order->status == 'FINISHED' ? 'Project Closed' : 'Scheduled' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Warning: Low Stock for Production -->
            @php $lowStockInProduction = $materials->where('current_qty', '<', 2); @endphp
            @if(count($lowStockInProduction) > 0 && $order->status == 'IN_PRODUCTION')
                <div class="p-4 bg-error-container/10 border border-error/30 rounded-xl">
                    <div class="flex items-center gap-2 text-error mb-2">
                        <span class="material-symbols-outlined text-[20px]">warning</span>
                        <p class="text-xs font-bold uppercase tracking-wider">Critical Inventory Alert</p>
                    </div>
                    <p class="text-[11px] text-slate-400 mb-3">Some materials are running very low and might stall production:</p>
                    <div class="space-y-2">
                        @foreach($lowStockInProduction as $m)
                            <div class="flex justify-between text-[11px]">
                                <span class="text-on-surface">{{ $m->name }}</span>
                                <span class="text-error font-bold">{{ $m->current_qty }} {{ $m->unit }}</span>
                            </div>
                        @endforeach
                    </div>
                    <a href="{{ route('materials.index') }}" class="mt-4 block text-center py-2 bg-error text-on-error rounded-lg text-xs font-bold">Manage Restock</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
