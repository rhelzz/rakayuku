<!DOCTYPE html>
<html class="dark" lang="en">
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
        body { background-color: #111316; color: #e2e2e6; }
        .glass-panel { background: rgba(30, 32, 35, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(161, 141, 126, 0.2); }
        .font-body-md { font-family: 'Inter', sans-serif; font-size: 14px; line-height: 20px; }
        .font-display-lg { font-family: 'Inter', sans-serif; font-size: 32px; font-weight: 700; line-height: 40px; }
        .font-label-caps { font-family: 'Inter', sans-serif; font-size: 11px; font-weight: 700; line-height: 16px; letter-spacing: 0.05em; }
        .font-title-sm { font-family: 'Inter', sans-serif; font-size: 18px; font-weight: 600; line-height: 24px; }
        .font-data-mono { font-family: 'Inter', sans-serif; font-size: 13px; font-weight: 500; line-height: 16px; }
    </style>
</head>
<body class="flex h-screen overflow-hidden bg-background font-body-md text-on-background">

<!-- SideNavBar -->
<nav class="hidden md:flex flex-col py-6 h-screen w-64 bg-[#121417] border-r border-slate-800 shadow-lg font-inter text-[13px] font-medium z-50 shrink-0">
    <div class="px-6 mb-8">
        <h1 class="text-[#D27D2D] font-black text-xl tracking-tight">Rakayuku</h1>
        <p class="text-slate-400 text-[11px] mt-1">Custom Furniture Suite</p>
    </div>
    
    <div class="flex-1 px-4 space-y-1">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded {{ request()->routeIs('dashboard') ? 'bg-slate-800/40 text-[#D27D2D] border-r-4 border-[#D27D2D]' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800' }} transition-all duration-150 group cursor-pointer">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('dashboard') ? "font-variation-settings: 'FILL' 1;" : '' }}">dashboard</span>
            Dashboard
        </a>
        
        <a href="{{ route('materials.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded {{ request()->routeIs('materials.*') ? 'bg-slate-800/40 text-[#D27D2D] border-r-4 border-[#D27D2D]' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800' }} transition-all duration-150 group cursor-pointer">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('materials.*') ? "font-variation-settings: 'FILL' 1;" : '' }}">inventory_2</span>
            Inventory
        </a>
        
        <a href="{{ route('purchases.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded {{ request()->routeIs('purchases.*') ? 'bg-slate-800/40 text-[#D27D2D] border-r-4 border-[#D27D2D]' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800' }} transition-all duration-150 group cursor-pointer">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('purchases.*') ? "font-variation-settings: 'FILL' 1;" : '' }}">shopping_bag</span>
            Purchases
        </a>

        <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded {{ request()->routeIs('orders.*') ? 'bg-slate-800/40 text-[#D27D2D] border-r-4 border-[#D27D2D]' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800' }} transition-all duration-150 group cursor-pointer">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('orders.*') ? "font-variation-settings: 'FILL' 1;" : '' }}">shopping_cart</span>
            Orders
        </a>
        
        <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded {{ request()->routeIs('customers.*') ? 'bg-slate-800/40 text-[#D27D2D] border-r-4 border-[#D27D2D]' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800' }} transition-all duration-150 group cursor-pointer">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('customers.*') ? "font-variation-settings: 'FILL' 1;" : '' }}">group</span>
            Customers
        </a>
    </div>

    <div class="px-4 mt-auto">
        <div class="flex items-center gap-3 px-3 py-4 border-t border-slate-800 mt-4">
            <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container font-bold text-xs">
                AD
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-on-background truncate">Admin Rakayuku</p>
                <p class="text-[11px] text-slate-500 truncate">Owner</p>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content Area -->
<div class="flex flex-col flex-1 h-full overflow-hidden">
    <!-- TopAppBar (Mobile) -->
    <header class="flex justify-between items-center px-4 h-14 w-full z-40 bg-[#121417] border-b border-slate-800 shadow-sm shrink-0 md:hidden">
        <div class="flex items-center gap-4">
            <span class="material-symbols-outlined text-[#D27D2D] cursor-pointer">menu</span>
            <span class="text-lg font-bold tracking-tight text-[#D27D2D]">Rakayuku ERP</span>
        </div>
    </header>

    <!-- Main Canvas -->
    <main class="flex-1 overflow-y-auto p-6 bg-background">
        <div class="max-w-7xl mx-auto">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 p-4 bg-[#112a1f] border border-[#1e4a33] text-[#4ade80] rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined">check_circle</span>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-[#4ade80] hover:opacity-70">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 bg-error-container/20 border border-error/30 text-error rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined">error</span>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-error hover:opacity-70">
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
