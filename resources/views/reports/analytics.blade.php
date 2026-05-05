@extends('layouts.app')

@section('title', 'Analytics & Tren Bisnis')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4">
        <nav class="flex text-sm text-slate-500 gap-2 items-center font-body-sm">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Analytics</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-headline-md text-headline-md text-on-surface mb-1">Analytics Dashboard</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Visualisasi performa bisnis dan tren operasional.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('reports.finance') }}" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg font-semibold hover:bg-surface-container-highest transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                    Laporan Keuangan
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <x-table.filter placeholder="Cari data trend..." />

    <!-- Chart Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Order Volume Trend -->
        <div class="glass-panel border border-surface-variant rounded-xl p-6 shadow-sm">
            <h3 class="font-title-sm text-title-sm mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">trending_up</span>
                Tren Volume Pesanan
            </h3>
            <div class="h-64">
                <canvas id="orderVolumeChart"></canvas>
            </div>
        </div>

        <!-- Revenue Trend -->
        <div class="glass-panel border border-surface-variant rounded-xl p-6 shadow-sm">
            <h3 class="font-title-sm text-title-sm mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600">payments</span>
                Tren Pendapatan (Revenue)
            </h3>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Inventory Status -->
        <div class="glass-panel border border-surface-variant rounded-xl p-6 shadow-sm">
            <h3 class="font-title-sm text-title-sm mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-600">inventory_2</span>
                Status Stok Bahan Baku
            </h3>
            <div class="h-64">
                <canvas id="inventoryChart"></canvas>
            </div>
        </div>

        <!-- Cashflow Overview -->
        <div class="glass-panel border border-surface-variant rounded-xl p-6 shadow-sm">
            <h3 class="font-title-sm text-title-sm mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600">account_balance</span>
                Ringkasan Cashflow (In vs Out)
            </h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="cashflowChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const orderData = @json($orderTrends);
    const inventoryData = @json($inventoryHealth);
    const cashflowData = @json($cashflow);

    const labels = orderData.map(d => d.month);

    // Order Volume Chart
    new Chart(document.getElementById('orderVolumeChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Pesanan',
                data: orderData.map(d => d.count),
                borderColor: '#4F46E5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Revenue Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue (Rp)',
                data: orderData.map(d => d.revenue),
                backgroundColor: '#10B981',
                borderRadius: 8
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Inventory Chart
    new Chart(document.getElementById('inventoryChart'), {
        type: 'bar',
        data: {
            labels: inventoryData.map(d => d.name),
            datasets: [{
                label: 'Stok Tersedia',
                data: inventoryData.map(d => d.stock),
                backgroundColor: inventoryData.map(d => d.stock < 5 ? '#EF4444' : '#F59E0B'),
                borderRadius: 4
            }]
        },
        options: { 
            indexAxis: 'y',
            responsive: true, 
            maintainAspectRatio: false 
        }
    });

    // Cashflow Chart
    new Chart(document.getElementById('cashflowChart'), {
        type: 'doughnut',
        data: {
            labels: ['Income (Masuk)', 'Outcome (Keluar)'],
            datasets: [{
                data: [cashflowData.income, cashflowData.outcome],
                backgroundColor: ['#10B981', '#EF4444'],
                borderWidth: 0
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Top Materials Chart
    new Chart(document.getElementById('topMaterialsChart'), {
        type: 'pie',
        data: {
            labels: materialData.map(d => d.name),
            datasets: [{
                data: materialData.map(d => d.total_value),
                backgroundColor: ['#F43F5E', '#8B5CF6', '#F59E0B', '#10B981', '#3B82F6'],
                borderWidth: 0
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endsection
