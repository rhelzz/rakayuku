<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title', 'Dashboard') - Rakayuku ERP</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- Scripts & Styles -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { background-color: #f8fafc; color: #0f172a; }
        .glass-panel { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(203, 213, 225, 0.5); }
        .font-body-md { font-family: 'Inter', sans-serif; font-size: 14px; line-height: 20px; }
        .font-display-lg { font-family: 'Inter', sans-serif; font-size: 32px; font-weight: 700; line-height: 40px; }
        .font-label-caps { font-family: 'Inter', sans-serif; font-size: 11px; font-weight: 700; line-height: 16px; letter-spacing: 0.05em; }
        .font-title-sm { font-family: 'Inter', sans-serif; font-size: 18px; font-weight: 600; line-height: 24px; }
        .font-data-mono { font-family: 'Inter', sans-serif; font-size: 13px; font-weight: 500; line-height: 16px; }
    </style>
</head>
<body class="flex h-screen overflow-hidden bg-background font-body-md text-on-background">

<!-- SideNavBar -->
<nav class="hidden md:flex flex-col py-6 h-screen w-64 bg-white border-r border-slate-200 shadow-sm font-inter text-[13px] font-medium z-50 shrink-0">
    <div class="px-6 mb-8">
        <h1 class="text-primary font-black text-xl tracking-tight">Rakayuku</h1>
        <p class="text-slate-500 text-[11px] mt-1">Sistem Manajemen Furnitur</p>
    </div>
    
    <div class="flex-1 px-4 space-y-1">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded {{ request()->routeIs('dashboard') ? 'bg-amber-50 text-primary border-r-4 border-primary' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition-all duration-150 group cursor-pointer">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('dashboard') ? "font-variation-settings: 'FILL' 1;" : '' }}">dashboard</span>
            Dashboard
        </a>
        
        <a href="{{ route('materials.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded {{ request()->routeIs('materials.*') ? 'bg-amber-50 text-primary border-r-4 border-primary' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition-all duration-150 group cursor-pointer">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('materials.*') ? "font-variation-settings: 'FILL' 1;" : '' }}">inventory_2</span>
            Inventaris
        </a>
        
        <a href="{{ route('purchases.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded {{ request()->routeIs('purchases.*') ? 'bg-amber-50 text-primary border-r-4 border-primary' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition-all duration-150 group cursor-pointer">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('purchases.*') ? "font-variation-settings: 'FILL' 1;" : '' }}">shopping_bag</span>
            Pembelian
        </a>

        <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded {{ request()->routeIs('orders.*') ? 'bg-amber-50 text-primary border-r-4 border-primary' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition-all duration-150 group cursor-pointer">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('orders.*') ? "font-variation-settings: 'FILL' 1;" : '' }}">shopping_cart</span>
            Pesanan
        </a>
        
        <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded {{ request()->routeIs('customers.*') ? 'bg-amber-50 text-primary border-r-4 border-primary' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition-all duration-150 group cursor-pointer">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('customers.*') ? "font-variation-settings: 'FILL' 1;" : '' }}">group</span>
            Pelanggan
        </a>
    </div>

    <div class="px-4 mt-auto">
        <div class="flex items-center gap-3 px-3 py-4 border-t border-slate-100 mt-4">
            <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white font-bold text-xs">
                AD
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-on-background truncate">Admin Rakayuku</p>
                <p class="text-[11px] text-slate-500 truncate">Pemilik</p>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content Area -->
<div class="flex flex-col flex-1 h-full overflow-hidden">
    <!-- TopAppBar (Mobile) -->
    <header class="flex justify-between items-center px-4 h-14 w-full z-40 bg-white border-b border-slate-200 shadow-sm shrink-0 md:hidden">
        <div class="flex items-center gap-4">
            <span class="material-symbols-outlined text-primary cursor-pointer">menu</span>
            <span class="text-lg font-bold tracking-tight text-primary">Rakayuku ERP</span>
        </div>
    </header>

    <!-- Main Canvas -->
    <main class="flex-1 overflow-y-auto p-6 bg-background">
        <div class="max-w-7xl mx-auto">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined">check_circle</span>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-700 hover:opacity-70">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined">error</span>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-red-700 hover:opacity-70">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

</body>
</html>
