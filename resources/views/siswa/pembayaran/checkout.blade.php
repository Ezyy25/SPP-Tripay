<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Metode Pembayaran SPP</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
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
    
    <style>
        /* Custom Custom Radio State Effect */
        .payment-option:has(input[type="radio"]:checked) {
            border-color: #4f46e5 !important;
            background-color: #f5f3ff !important;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.15), 0 8px 10px -6px rgba(79, 70, 229, 0.1);
        }
        .payment-option:has(input[type="radio"]:checked) .radio-dot {
            border-color: #4f46e5;
            background-color: #4f46e5;
        }
        .payment-option:has(input[type="radio"]:checked) .radio-inner {
            opacity: 1;
            transform: scale(1);
        }
    </style>
</head>
<body class="min-h-full font-sans antialiased bg-slate-50/80 text-slate-900 pb-16">

    <!-- Background Decorative Gradients -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-indigo-200/40 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 -left-40 w-96 h-96 bg-blue-100/50 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-3xl mx-auto px-4 pt-8 md:pt-12 space-y-8">
        
        <!-- Header & Nav Bar -->
        <div class="flex items-center justify-between">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100 mb-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    Sistem Pembayaran SPP
                </span>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-900 tracking-tight">Pilih Metode Pembayaran</h1>
                <p class="text-sm text-slate-500 mt-0.5">Pilih salah satu metode pembayaran TriPay yang kamu inginkan.</p>
            </div>
            <a href="javascript:history.back()" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-100 hover:text-slate-900 transition-all shadow-sm active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>

        <!-- Detail Card Tagihan -->
        <div class="relative overflow-hidden bg-gradient-to-br from-indigo-900 via-indigo-800 to-slate-900 text-white rounded-3xl p-6 md:p-8 shadow-xl shadow-indigo-900/10">
            <!-- Glass Overlay Design -->
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-1">
                    <p class="text-xs uppercase tracking-wider font-semibold text-indigo-200/80">Tagihan SPP Santri</p>
                    <h2 class="text-2xl font-bold tracking-tight text-white">{{ $tagihan->bulan }} {{ $tagihan->tahun }}</h2>
                    <p class="text-xs text-indigo-200/60 pt-1 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Otomatis terverifikasi oleh gateway TriPay
                    </p>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 md:px-6 md:py-3.5 border border-white/15 text-left md:text-right">
                    <p class="text-xs font-semibold text-indigo-200/80 uppercase tracking-wider">Total Yang Harus Dibayar</p>
                    <p class="text-2xl md:text-3xl font-extrabold tracking-tight text-white mt-0.5">
                        <span class="text-lg font-normal text-indigo-200">Rp</span> {{ number_format($tagihan->nominal, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Alert Notification Messages -->
        @if(session('error'))
            <div class="flex items-start gap-3 bg-rose-50 border border-rose-200/80 text-rose-800 p-4 rounded-2xl text-sm font-medium shadow-sm animate-shake">
                <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="flex items-start gap-3 bg-amber-50 border border-amber-200/80 text-amber-800 p-4 rounded-2xl text-sm font-medium shadow-sm">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>Silakan pilih salah satu metode pembayaran terlebih dahulu sebelum melanjutkan.</div>
            </div>
        @endif

        <!-- Form Pemilihan Metode Pembayaran -->
        <form action="{{ route('siswa.pembayaran.process', $tagihan->id) }}" method="POST" class="space-y-6">
            @csrf

            <div class="space-y-8">
                @forelse($channels as $group)
                    @php
                        $payments = $group['payment'] ?? [$group];
                        $groupName = $group['group_name'] ?? 'Metode Pembayaran';
                    @endphp

                    <div class="space-y-3">
                        <!-- Group Header -->
                        <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
                            <span class="w-1.5 h-4 bg-indigo-600 rounded-full"></span>
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ $groupName }}</h3>
                        </div>
                        
                        <!-- List Payment Channels -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                            @foreach($payments as $pay)
                                @php
                                    $code = $pay['code'] ?? null;
                                    $name = $pay['name'] ?? 'Metode Pembayaran';
                                    $icon = $pay['icon_url'] ?? null;
                                    
                                    $flatFee = $pay['fee']['flat'] ?? 0;
                                    $percentFee = $pay['fee']['percent'] ?? 0;
                                @endphp

                                @if($code)
                                    <label class="payment-option group relative flex items-center p-4 bg-white border border-slate-200/90 rounded-2xl cursor-pointer hover:border-indigo-300 hover:shadow-md transition-all duration-200">
                                        <!-- Hidden Radio Input -->
                                        <input type="radio" name="method" value="{{ $code }}" class="sr-only" required>
                                        
                                        <!-- Custom Radio Indicator -->
                                        <div class="radio-dot w-5 h-5 rounded-full border-2 border-slate-300 group-hover:border-indigo-400 flex items-center justify-center transition-colors shrink-0">
                                            <div class="radio-inner w-2 h-2 rounded-full bg-white opacity-0 transform scale-50 transition-all duration-150"></div>
                                        </div>

                                        <!-- Detail Text & Info -->
                                        <div class="ml-3.5 flex items-center justify-between w-full min-w-0">
                                            <div class="pr-2">
                                                <p class="text-sm font-bold text-slate-800 group-hover:text-indigo-900 transition-colors truncate">
                                                    {{ $name }}
                                                </p>
                                                <p class="text-xs font-medium text-slate-500 mt-0.5">
                                                    Biaya: <span class="text-slate-700 font-semibold">Rp {{ number_format($flatFee, 0, ',', '.') }}</span>
                                                    @if($percentFee > 0)
                                                        <span class="text-indigo-600 font-semibold">+{{ $percentFee }}%</span>
                                                    @endif
                                                </p>
                                            </div>

                                            <!-- Logo Payment Badge -->
                                            @if($icon)
                                                <div class="h-9 w-14 shrink-0 bg-slate-50 border border-slate-100 rounded-lg p-1 flex items-center justify-center shadow-2xs group-hover:bg-white transition-colors">
                                                    <img src="{{ $icon }}" alt="{{ $name }}" class="max-h-full max-w-full object-contain">
                                                </div>
                                            @endif
                                        </div>
                                    </label>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center bg-white border border-amber-200/70 rounded-3xl shadow-sm space-y-3">
                        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-800">Metode Pembayaran Tidak Tersedia</p>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">Gagal memuat saluran pembayaran dari TriPay API. Silakan coba beberapa saat lagi atau hubungi admin.</p>
                    </div>
                @endforelse
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit" class="w-full relative group overflow-hidden bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-2xl transition-all duration-200 shadow-lg shadow-indigo-600/25 active:scale-[0.99] flex items-center justify-center gap-2">
                    <span>Lanjutkan Ke Pembayaran</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </form>

        <!-- Footer Info Security -->
        <div class="text-center pt-2">
            <p class="text-xs text-slate-400 flex items-center justify-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Pembayaran kamu dienkripsi dan diproses secara aman oleh TriPay
            </p>
        </div>

    </div>
<x-loading />
</body>
</html>