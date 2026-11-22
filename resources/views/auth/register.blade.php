<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Pengirim &mdash; CargoTrace</title>
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
        <div class="mb-4">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-steelblue hover:text-cargogold-400 transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Beranda</span>
            </a>
        </div>

        <!-- Logo -->
        <div class="text-center mb-6">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-navyink via-deepnavy to-navyink border border-cargogold-600/50 mx-auto p-2 shadow-xl shadow-cargogold-600/10 flex items-center justify-center mb-2">
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
            <p class="text-xs text-steelblue font-medium mt-1">Registrasi Akun Customer / Shipper</p>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-rose-950/60 border border-rose-500/40 text-rose-300 text-xs">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST" class="space-y-3.5">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase text-steelblue mb-1">Nama Lengkap *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Elena Rostova"
                    class="w-full py-2.5 px-3 text-xs bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-steelblue mb-1">Perusahaan / Organisasi</label>
                <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="e.g. Apex Global Logistics"
                    class="w-full py-2.5 px-3 text-xs bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-steelblue mb-1">Alamat Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="shipper@company.com"
                    class="w-full py-2.5 px-3 text-xs bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase text-steelblue mb-1">Kata Sandi *</label>
                    <input type="password" name="password" required placeholder="Min 8 karakter"
                        class="w-full py-2.5 px-3 text-xs bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-steelblue mb-1">Konfirmasi Sandi *</label>
                    <input type="password" name="password_confirmation" required placeholder="Ulangi sandi"
                        class="w-full py-2.5 px-3 text-xs bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-gradient-to-r from-oceanblue to-cyan-600 hover:from-oceanblue/90 hover:to-cyan-500 text-white font-bold text-xs rounded-xl transition shadow-lg shadow-oceanblue/30 mt-3">
                Selesaikan Registrasi
            </button>
        </form>

        <p class="text-center text-xs text-slate-400 mt-6">
            Sudah memiliki akun? <a href="{{ route('login') }}" class="text-cargogold-400 font-bold hover:underline">Masuk Portal</a>
        </p>
    </div>

</body>
</html>
