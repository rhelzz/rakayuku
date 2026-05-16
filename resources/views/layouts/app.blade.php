<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title', 'Dashboard') - Rakayuku ERP</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script>
        function formatRupiahJS(value) {
            if (!value) return '';
            let number_string = value.toString().replace(/[^,\d]/g, ''),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah;
        }
    </script>
</head>
<body class="flex h-screen overflow-hidden bg-background font-body-md text-on-background" x-data="{ sidebarOpen: false }">

<div x-show="sidebarOpen" class="fixed inset-0 bg-black/50 z-40 md:hidden" x-transition.opacity @click="sidebarOpen = false" x-cloak></div>

<nav :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" class="fixed md:static inset-y-0 left-0 z-50 w-64 h-full bg-white border-r border-slate-200 shadow-sm font-poppins text-[13px] font-medium shrink-0 flex flex-col py-6 transition-transform duration-300 transform md:translate-x-0">
    <div class="px-6 mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-primary font-black text-xl tracking-tight">Rakayuku</h1>
            <p class="text-slate-500 text-[11px] mt-1">Sistem Manajemen Furniture</p>
        </div>
        <button @click="sidebarOpen = false" class="md:hidden text-slate-500 hover:text-slate-800">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    
    <div class="flex-1 px-4 space-y-1 overflow-y-auto" x-data="{ 
        openMenu: '{{ request()->routeIs('materials.*') || request()->routeIs('inventory.*') || request()->routeIs('stock-opname.*') ? 'inventory' : '' }}' 
    }">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded {{ request()->routeIs('dashboard') ? 'bg-amber-50 text-primary border-r-4 border-primary' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition-all duration-150 group cursor-pointer">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('dashboard') ? "font-variation-settings: 'FILL' 1;" : '' }}">dashboard</span>
            Dashboard
        </a>

        <a href="{{ route('finance.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded {{ request()->routeIs('finance.*') ? 'bg-amber-50 text-primary border-r-4 border-primary' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition-all duration-150 group cursor-pointer">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('finance.*') ? "font-variation-settings: 'FILL' 1;" : '' }}">account_balance</span>
            Keuangan
        </a>

        <a href="{{ route('closing.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded {{ request()->routeIs('closing.*') ? 'bg-amber-50 text-primary border-r-4 border-primary' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition-all duration-150 group cursor-pointer">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('closing.*') ? "font-variation-settings: 'FILL' 1;" : '' }}">lock_clock</span>
            Tutup Buku
        </a>
        
        <div class="space-y-1">
            <button @click="openMenu = (openMenu === 'inventory' ? '' : 'inventory')" class="w-full flex items-center justify-between px-3 py-2.5 rounded {{ request()->routeIs('materials.*') || request()->routeIs('inventory.*') || request()->routeIs('stock-opname.*') ? 'bg-amber-50 text-primary' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition-all duration-150 group cursor-pointer">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined" style="{{ request()->routeIs('materials.*') || request()->routeIs('inventory.*') || request()->routeIs('stock-opname.*') ? "font-variation-settings: 'FILL' 1;" : '' }}">inventory_2</span>
                    Inventaris
                </div>
                <span class="material-symbols-outlined text-sm transition-transform duration-200" :class="openMenu === 'inventory' ? 'rotate-180' : ''">expand_more</span>
            </button>
            <div x-show="openMenu === 'inventory'" x-collapse x-cloak class="pl-10 space-y-1">
                <a href="{{ route('materials.index') }}" class="block py-2 px-3 rounded {{ request()->routeIs('materials.index') ? 'text-primary font-bold' : 'text-slate-500 hover:text-slate-900' }}">Daftar Bahan</a>
                <a href="{{ route('inventory.movements') }}" class="block py-2 px-3 rounded {{ request()->routeIs('inventory.movements') ? 'text-primary font-bold' : 'text-slate-500 hover:text-slate-900' }}">Log Pergerakan</a>
                <a href="{{ route('stock-opname.index') }}" class="block py-2 px-3 rounded {{ request()->routeIs('stock-opname.*') ? 'text-primary font-bold' : 'text-slate-500 hover:text-slate-900' }}">Stock Opname</a>
            </div>
        </div>
        
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

        <div class="space-y-1" x-data="{ 
            openReports: '{{ request()->routeIs('reports.*') ? 'reports' : '' }}' 
        }">
            <button @click="openReports = (openReports === 'reports' ? '' : 'reports')" class="w-full flex items-center justify-between px-3 py-2.5 rounded {{ request()->routeIs('reports.*') ? 'bg-amber-50 text-primary' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition-all duration-150 group cursor-pointer">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined" style="{{ request()->routeIs('reports.*') ? "font-variation-settings: 'FILL' 1;" : '' }}">monitoring</span>
                    Laporan
                </div>
                <span class="material-symbols-outlined text-sm transition-transform duration-200" :class="openReports === 'reports' ? 'rotate-180' : ''">expand_more</span>
            </button>
            <div x-show="openReports === 'reports'" x-collapse x-cloak class="pl-10 space-y-1">
                <a href="{{ route('reports.analytics') }}" class="block py-2 px-3 rounded {{ request()->routeIs('reports.analytics') ? 'text-primary font-bold' : 'text-slate-500 hover:text-slate-900' }}">Analytics</a>
                <a href="{{ route('reports.finance') }}" class="block py-2 px-3 rounded {{ request()->routeIs('reports.finance') ? 'text-primary font-bold' : 'text-slate-500 hover:text-slate-900' }}">Keuangan</a>
            </div>
        </div>
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

<div class="flex flex-col flex-1 h-full overflow-hidden">
    <header class="flex justify-between items-center px-4 h-14 w-full z-30 bg-white border-b border-slate-200 shadow-sm shrink-0 md:hidden">
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = true" class="text-primary hover:bg-slate-100 p-1.5 rounded-lg transition-colors flex items-center justify-center">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <span class="text-lg font-bold tracking-tight text-primary">Rakayuku ERP</span>
        </div>
    </header>

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
