<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Portal &mdash; CargoTrace Visibility Platform</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        deepnavy: '#060c1d',
                        navyink: '#111a33',
                        oceanblue: '#0f4c81',
                        steelblue: '#3a7ca5',
                        duskblue: '#5c6b73',
                        cargogold: {
                            300: '#fde047',
                            400: '#facc15',
                            500: '#eab308',
                            600: '#d4af37',
                            700: '#b45309'
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .text-gold-gradient {
            background: linear-gradient(135deg, #fef08a 0%, #d4af37 50%, #b45309 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-deepnavy min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Ambient Ocean Lighting Background -->
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-oceanblue/40 via-navyink to-deepnavy pointer-events-none"></div>

    <div class="relative z-10 max-w-md w-full bg-navyink/90 backdrop-blur-2xl rounded-3xl p-8 sm:p-10 shadow-2xl border border-steelblue/30 text-slate-100">

        <!-- Top Back Link -->
        <div class="mb-6">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-steelblue hover:text-cargogold-400 transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Beranda (Welcome)</span>
            </a>
        </div>

        <!-- Gold CargoTrace Logo -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-navyink via-deepnavy to-navyink border border-cargogold-600/50 mx-auto p-2.5 shadow-xl shadow-cargogold-600/10 flex items-center justify-center mb-3">
                <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <ellipse cx="32" cy="32" rx="27" ry="12" stroke="#d4af37" stroke-width="1.8" stroke-dasharray="4 2" transform="rotate(-25 32 32)"/>
                    <circle cx="32" cy="32" r="21" stroke="#d4af37" stroke-width="1.2" stroke-opacity="0.35"/>
                    <line x1="11" y1="32" x2="53" y2="32" stroke="#d4af37" stroke-width="1.2" stroke-opacity="0.35"/>
                    <path d="M18 36L32 46L46 36L44 32H20L18 36Z" fill="#facc15" />
                    <rect x="27" y="24" width="10" height="7" rx="1" fill="#fef08a" />
                    <line x1="32" y1="14" x2="32" y2="19" stroke="#fef08a" stroke-width="1.5" stroke-linecap="round"/>
                    <circle cx="49" cy="24" r="2.5" fill="#fef08a"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight font-mono">
                Cargo<span class="text-gold-gradient">Trace</span>
            </h1>
            <p class="text-[11px] text-cargogold-500 font-semibold tracking-wider uppercase mt-0.5">Portal Masuk Operasional</p>
            <p class="text-[10px] text-slate-400 mt-1 italic">Cool and Bold &mdash; Strong like the Ocean, timeless like the night.</p>
        </div>

        @if($errors->any())
            <div class="mb-5 p-3 rounded-xl bg-rose-950/60 border border-rose-500/40 text-rose-300 text-xs">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase text-steelblue mb-1.5 font-mono">Alamat Email</label>
                <div class="relative">
                    <input type="email" id="emailInput" name="email" value="{{ old('email', 'admin@cargotrace.com') }}" required
                        class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm bg-deepnavy/80 border border-steelblue/30 rounded-xl text-white outline-none focus:ring-2 focus:ring-cargogold-500 focus:border-cargogold-500 transition">
                    <i class="fa-regular fa-envelope absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-steelblue mb-1.5 font-mono">Kata Sandi</label>
                <div class="relative">
                    <input type="password" id="passwordInput" name="password" value="password123" required
                        class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm bg-deepnavy/80 border border-steelblue/30 rounded-xl text-white outline-none focus:ring-2 focus:ring-cargogold-500 focus:border-cargogold-500 transition">
                    <i class="fa-solid fa-lock absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 text-slate-400 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded bg-deepnavy border-steelblue/40 text-cargogold-500 focus:ring-0">
                    <span>Ingat perangkat ini</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-cargogold-600 via-cargogold-500 to-amber-600 hover:brightness-110 text-deepnavy font-black text-xs sm:text-sm uppercase tracking-wider rounded-xl transition shadow-lg shadow-cargogold-600/25 mt-2 flex items-center justify-center gap-2">
                <span>Masuk ke Dashboard</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </form>

        <!-- Register Link -->
        <div class="mt-4 text-center">
            <span class="text-xs text-slate-400">Belum memiliki akun?</span>
            <a href="{{ route('register') }}" class="text-xs font-bold text-cargogold-400 hover:text-cargogold-300 hover:underline ml-1">
                Daftar Akun Baru &rarr;
            </a>
        </div>

        <!-- Quick Demo Accounts (UAS Evaluation Helper - Slide 18 & 22) -->
        <div class="mt-8 pt-6 border-t border-steelblue/20">
            <span class="text-[10px] uppercase font-bold text-cargogold-400 block mb-2 text-center tracking-wider font-mono">
                Akun Demo Evaluasi Dosen (1-Click Switcher)
            </span>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" onclick="setDemo('admin@cargotrace.com', 'password123')"
                    class="p-2 text-center rounded-xl bg-deepnavy/80 hover:bg-oceanblue/40 border border-steelblue/30 text-xs transition">
                    <span class="block font-bold text-white text-[11px]">Admin</span>
                    <span class="text-[9px] text-slate-400 font-mono">Sarah Johnson</span>
                </button>
                <button type="button" onclick="setDemo('operator@cargotrace.com', 'password123')"
                    class="p-2 text-center rounded-xl bg-deepnavy/80 hover:bg-oceanblue/40 border border-steelblue/30 text-xs transition">
                    <span class="block font-bold text-white text-[11px]">Operator</span>
                    <span class="text-[9px] text-slate-400 font-mono">FastShip Dispatch</span>
                </button>
                <button type="button" onclick="setDemo('customer@cargotrace.com', 'password123')"
                    class="p-2 text-center rounded-xl bg-deepnavy/80 hover:bg-oceanblue/40 border border-steelblue/30 text-xs transition">
                    <span class="block font-bold text-white text-[11px]">Customer</span>
                    <span class="text-[9px] text-slate-400 font-mono">Global Ind.</span>
                </button>
            </div>
        </div>

    </div>

    <script>
        function setDemo(email, pass) {
            document.getElementById('emailInput').value = email;
            document.getElementById('passwordInput').value = pass;
        }
    </script>
</body>
</html>
