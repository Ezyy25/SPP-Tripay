<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel SPP')</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <!-- AlpineJS & Parallax Script -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-parallax-js@5.5.1/dist/simpleParallax.min.js"></script>

    <style>
    /* Perjelas kontras teks pada Card dan Tabel */
.card {
    background-color: #1e1e2d !important; /* Gelap elegan */
    color: #ffffff !important;
}

.table {
    color: #e1e1e6 !important; /* Warna teks terang high-contrast */
}

.table th {
    color: #ffffff !important;
    font-weight: 600;
    background-color: #2b2b40 !important;
}

/* Pastikan placeholder input dan text form terbaca jelas */
.form-control, .form-select {
    background-color: #151521 !important;
    color: #ffffff !important;
    border: 1px solid #323248 !important;
}

.form-control::placeholder {
    color: #92929f !important;
}

/* Teks muted/sekunder agar tetap kontras */
.text-muted {
    color: #a1a5b7 !important;
}
    </style>
    @stack('styles')
</head>
<body x-data="{ sidebarOpen: false }" class="h-full font-sans text-slate-200 bg-slate-950 antialiased overflow-x-hidden selection:bg-indigo-500 selection:text-white">

    <!-- Background Dynamic Decorative Blobs (Parallax Layer) -->
    <div class="fixed inset-0 pointer-events-none -z-10 overflow-hidden">
        <div id="blob1" class="absolute -top-32 -right-32 w-[500px] h-[500px] bg-indigo-600/20 rounded-full blur-[120px] parallax-glow"></div>
        <div id="blob2" class="absolute top-1/2 -left-48 w-[600px] h-[600px] bg-blue-600/15 rounded-full blur-[140px] parallax-glow"></div>
        <div id="blob3" class="absolute -bottom-32 right-1/4 w-[450px] h-[450px] bg-emerald-600/10 rounded-full blur-[100px] parallax-glow"></div>
    </div>

    <div class="min-h-screen flex">

        <!-- Sidebar Overlay (Mobile) -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false" 
             class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-40 lg:hidden"></div>

        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed lg:static inset-y-0 left-0 z-50 w-72 bg-slate-900/90 border-r border-slate-800/80 transform lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col justify-between glass-nav">
            
            <div>
                <!-- Brand / Logo -->
                <div class="h-20 flex items-center px-6 border-b border-slate-800/80 justify-between">
                    <a href="#" class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-500 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <span class="text-base font-extrabold text-white tracking-wide block">SPP Admin</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-indigo-400">Panel Control</span>
                        </div>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

         <!-- Menu Utama -->
<div class="space-y-1">
    <a href="{{ route('admin.dashboard') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Dashboard
    </a>

    <a href="{{ route('admin.siswa.index') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.siswa.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        Data Siswa
    </a>

    <a href="{{ route('admin.tagihan.index') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.tagihan.index') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Data Tagihan SPP
    </a>

    <a href="{{ route('admin.tagihan.rekap') }}" 
   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.tagihan.rekap') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    Rekap Status Bayar
</a>

    <a href="{{ route('admin.notifikasi.index') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.notifikasi.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        Riwayat Notifikasi
    </a>

    <!-- MENU BARU: JADWAL TAGIHAN OTOMATIS -->
    <a href="{{ route('admin.tagihan.schedule') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.tagihan.schedule') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Jadwal Tagihan Otomatis
    </a>
</div>

            <!-- Profile Info Footer -->
            <div class="p-4 border-t border-slate-800/80">
                <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-800/40 border border-slate-700/50">
                    <div class="w-9 h-9 rounded-lg bg-indigo-500/20 border border-indigo-500/30 text-indigo-400 font-bold flex items-center justify-center shrink-0">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                        <p class="text-[10px] text-slate-400 truncate">Admin Sistem</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <!-- Top Header Navbar -->
            <header class="h-20 glass-nav border-b border-slate-800/80 flex items-center justify-between px-6 z-30 sticky top-0">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden text-slate-400 hover:text-white p-2 rounded-lg bg-slate-800/60">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h2 class="text-lg font-bold text-white tracking-tight hidden sm:block">@yield('page_title', 'Dashboard')</h2>
                </div>

                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        TriPay Gateway Active
                    </span>

                    <!-- Form Logout Fixed -->
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="p-2.5 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-xl transition-all border border-transparent hover:border-rose-500/20" title="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Dynamic Body Content -->
            <main class="flex-1 overflow-y-auto p-6 md:p-8 space-y-8">
                @yield('content')
            </main>

        </div>
    </div>

    <!-- Parallax Interactive Script -->
    <script>
        // Subtle Mouse Move Parallax Effect for background elements
        document.addEventListener('mousemove', (e) => {
            const x = e.clientX / window.innerWidth;
            const y = e.clientY / window.innerHeight;
            
            const blob1 = document.getElementById('blob1');
            const blob2 = document.getElementById('blob2');
            const blob3 = document.getElementById('blob3');
            
            if(blob1) blob1.style.transform = `translate(${x * 30}px, ${y * 30}px)`;
            if(blob2) blob2.style.transform = `translate(${-x * 40}px, ${-y * 20}px)`;
            if(blob3) blob3.style.transform = `translate(${x * 20}px, ${-y * 30}px)`;
        });
    </script>
    @stack('scripts')
    <x-loading />
</body>
</html>
