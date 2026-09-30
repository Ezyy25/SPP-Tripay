<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Siswa')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
<body class="min-h-screen bg-slate-950 text-slate-200 flex flex-col">

    <!-- Top Navbar khusus Siswa -->
    <header class="h-20 bg-slate-900/80 border-b border-slate-800 backdrop-blur sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-6 h-full flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-500 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                </div>
                <div>
                    <span class="text-base font-extrabold text-white block">Portal SPP Siswa</span>
                    <span class="text-[10px] text-indigo-400 font-bold uppercase tracking-wider">Siswa Panel</span>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <span class="text-sm font-semibold text-slate-300 hidden sm:inline">
                    {{ auth()->user()->name ?? 'Siswa' }}
                </span>

                <!-- Lonceng Notifikasi Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative p-2 text-slate-400 hover:text-white transition rounded-xl bg-slate-800/50 border border-slate-700/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-rose-500 rounded-full animate-ping"></span>
                            <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-rose-500 rounded-full"></span>
                        @endif
                    </button>

                    <!-- Panel Dropdown Notifikasi -->
                    <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl py-2 z-50">
                        <div class="px-4 py-2 border-b border-slate-800 flex justify-between items-center">
                            <span class="font-bold text-xs text-slate-400 uppercase tracking-wider">Notifikasi</span>
                            <span class="text-[10px] bg-indigo-500/20 text-indigo-400 px-2 py-0.5 rounded-full font-bold">
                                {{ auth()->user()->unreadNotifications->count() }} Baru
                            </span>
                        </div>
                        <div class="max-h-64 overflow-y-auto divide-y divide-slate-800/50">
                            @forelse(auth()->user()->notifications as $notification)
                                <a href="{{ $notification->data['link'] ?? route('siswa.dashboard') }}" class="block px-4 py-3 hover:bg-slate-800/50 transition">
                                    <p class="text-xs font-bold text-indigo-400">{{ $notification->data['title'] ?? 'Notifikasi' }}</p>
                                    <p class="text-xs text-slate-300 mt-0.5 line-clamp-2">{{ $notification->data['message'] ?? '' }}</p>
                                    <span class="text-[10px] text-slate-500 mt-1 block">{{ $notification->created_at->diffForHumans() }}</span>
                                </a>
                            @empty
                                <div class="px-4 py-6 text-center text-xs text-slate-500">
                                    Belum ada notifikasi baru.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 border border-rose-500/20 rounded-xl text-xs font-bold transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-6 md:p-8">
        @yield('content')
    </main>
<x-loading />
    @stack('scripts')
</body>
</html>