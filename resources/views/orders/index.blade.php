@extends('layouts.app')

@section('title', 'Orders & Projects')

@section('content')
<div x-data="{ openNewProjectModal: false }">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="font-headline-md text-headline-md text-on-background">Orders & Projects</h1>
            <p class="font-body-sm text-body-sm text-slate-400 mt-1">Manage active manufacturing workflow.</p>
        </div>
        <a href="{{ route('orders.create') }}" class="bg-primary-container text-on-primary-container hover:bg-primary transition-colors px-4 py-2 rounded-lg font-body-sm text-body-sm font-semibold flex items-center gap-2 shadow-sm">
            <span class="material-symbols-outlined text-[18px]">add</span>
            New Project
        </a>
    </div>

    <!-- Kanban Board -->
    <div class="flex gap-6 overflow-x-auto pb-4 h-[calc(100vh-200px)]">
        <!-- Pending Column -->
        <div class="flex-shrink-0 w-80 flex flex-col bg-surface-container-low rounded-xl border border-slate-800 shadow-sm">
            <div class="p-4 border-b border-slate-800 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-slate-400"></div>
                    <h2 class="font-title-sm text-title-sm text-on-surface">Pending</h2>
                </div>
                <span class="font-data-mono text-data-mono text-slate-500">{{ count($pendingOrders) }}</span>
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-3">
                @forelse($pendingOrders as $order)
                    <div class="bg-surface-variant rounded-lg border border-slate-800 p-4 shadow-sm hover:border-slate-600 transition-colors cursor-pointer group" onclick="window.location='{{ route('orders.show', $order) }}'">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-label-caps text-label-caps text-slate-400 uppercase">{{ $order->order_number }}</span>
                            <span class="px-2 py-0.5 rounded-sm bg-surface-container-high text-slate-300 font-label-caps text-[10px] border border-slate-700">Pending</span>
                        </div>
                        <h3 class="font-body-md text-body-md font-semibold text-on-surface mb-1 truncate">{{ $order->project_name ?? 'Custom Furniture' }}</h3>
                        <p class="font-body-sm text-body-sm text-slate-400 mb-4">{{ $order->customer->name }}</p>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-700/50">
                            <div class="flex items-center gap-1.5 {{ $order->payment_status === 'UNPAID' ? 'text-error' : 'text-emerald-500' }}">
                                <span class="material-symbols-outlined text-[14px]">{{ $order->payment_status === 'UNPAID' ? 'warning' : 'check_circle' }}</span>
                                <span class="font-data-mono text-data-mono text-[11px]">{{ $order->payment_status }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-400">
                                <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                <span class="font-data-mono text-data-mono text-[11px]">{{ $order->created_at->format('M d') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-slate-500 italic text-sm">No pending projects</div>
                @endforelse
            </div>
        </div>

        <!-- In Production Column -->
        <div class="flex-shrink-0 w-80 flex flex-col bg-surface-container-low rounded-xl border border-slate-800 shadow-sm">
            <div class="p-4 border-b border-slate-800 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-primary-container"></div>
                    <h2 class="font-title-sm text-title-sm text-on-surface">In Production</h2>
                </div>
                <span class="font-data-mono text-data-mono text-slate-500">{{ count($inProductionOrders) }}</span>
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-3">
                @forelse($inProductionOrders as $order)
                    <div class="bg-surface-variant rounded-lg border border-primary-container/30 p-4 shadow-sm hover:border-primary-container/60 transition-colors cursor-pointer group relative overflow-hidden" onclick="window.location='{{ route('orders.show', $order) }}'">
                        <div class="absolute top-0 left-0 w-1 h-full bg-primary-container"></div>
                        <div class="flex justify-between items-start mb-2 pl-2">
                            <span class="font-label-caps text-label-caps text-primary-container uppercase">{{ $order->order_number }}</span>
                            <span class="px-2 py-0.5 rounded-sm bg-surface-container-high text-slate-300 font-label-caps text-[10px] border border-slate-700">Processing</span>
                        </div>
                        <h3 class="font-body-md text-body-md font-semibold text-on-surface mb-1 pl-2 truncate">{{ $order->project_name ?? 'Custom Furniture' }}</h3>
                        <p class="font-body-sm text-body-sm text-slate-400 mb-4 pl-2">{{ $order->customer->name }}</p>
                        
                        <div class="pl-2 mb-3">
                            <div class="w-full bg-surface-container-highest rounded-full h-1.5 mb-1">
                                <div class="bg-primary-container h-1.5 rounded-full" style="width: 45%"></div>
                            </div>
                            <span class="font-data-mono text-[10px] text-slate-400">Production Phase</span>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-slate-700/50 pl-2">
                            <div class="flex items-center gap-1.5 text-emerald-500">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                <span class="font-data-mono text-data-mono text-[11px]">{{ $order->payment_status }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-400">
                                <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                <span class="font-data-mono text-data-mono text-[11px]">{{ $order->created_at->format('M d') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-slate-500 italic text-sm">No active production</div>
                @endforelse
            </div>
        </div>

        <!-- Finished Column -->
        <div class="flex-shrink-0 w-80 flex flex-col bg-surface-container-low rounded-xl border border-slate-800 shadow-sm">
            <div class="p-4 border-b border-slate-800 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-emerald-600"></div>
                    <h2 class="font-title-sm text-title-sm text-on-surface">Finished</h2>
                </div>
                <span class="font-data-mono text-data-mono text-slate-500">{{ count($finishedOrders) }}</span>
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-3">
                @forelse($finishedOrders as $order)
                    <div class="bg-surface-variant/70 rounded-lg border border-slate-800 p-4 shadow-sm group cursor-pointer hover:border-emerald-600/50 transition-colors" onclick="window.location='{{ route('orders.show', $order) }}'">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-label-caps text-label-caps text-slate-500 uppercase">{{ $order->order_number }}</span>
                            <span class="px-2 py-0.5 rounded-sm bg-surface-container-high text-slate-400 font-label-caps text-[10px] border border-slate-700">Completed</span>
                        </div>
                        <h3 class="font-body-md text-body-md font-semibold text-slate-300 mb-1 truncate">{{ $order->project_name ?? 'Custom Furniture' }}</h3>
                        <p class="font-body-sm text-body-sm text-slate-500 mb-4">{{ $order->customer->name }}</p>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-700/50">
                            <div class="flex items-center gap-1.5 text-emerald-700">
                                <span class="material-symbols-outlined text-[14px]">task_alt</span>
                                <span class="font-data-mono text-data-mono text-[11px]">{{ $order->payment_status }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-500">
                                <span class="material-symbols-outlined text-[14px]">done_all</span>
                                <span class="font-data-mono text-data-mono text-[11px]">{{ $order->updated_at->format('M d') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-slate-500 italic text-sm">No finished projects</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
