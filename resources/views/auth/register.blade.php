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

    <div class="relative z-10 max-w-xl w-full bg-navyink/90 backdrop-blur-2xl rounded-3xl p-8 sm:p-10 shadow-2xl border border-steelblue/30 text-slate-100 my-8">
        <!-- Top Back Link -->
        <div class="mb-4">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-steelblue hover:text-cargogold-400 transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Beranda (Welcome)</span>
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
            <p class="text-xs text-steelblue font-medium mt-1">Registrasi Akun Baru Multi-Peran</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Lengkapi biodata dan tentukan hak akses peran Anda</p>
        </div>

        @if($errors->any())
            <div class="mb-5 p-3 rounded-xl bg-rose-950/60 border border-rose-500/40 text-rose-300 text-xs">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Role Selection (Admin, Operator, Customer) -->
            <div>
                <label class="block text-xs font-bold uppercase text-cargogold-400 mb-2 font-mono flex items-center justify-between">
                    <span>Pilih Peran Akun (Masuk Sebagai) *</span>
                    <span class="text-[10px] text-slate-400 font-sans font-normal">Pilih salah satu peran</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <!-- Admin -->
                    <label class="relative flex flex-col p-3 rounded-2xl border border-steelblue/30 bg-deepnavy/90 hover:border-cargogold-500/60 cursor-pointer transition has-[:checked]:border-cargogold-500 has-[:checked]:bg-oceanblue/20 has-[:checked]:shadow-md has-[:checked]:shadow-cargogold-500/10 group">
                        <input type="radio" name="role" value="admin" {{ old('role') === 'admin' ? 'checked' : '' }} required class="sr-only">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="w-8 h-8 rounded-xl bg-cargogold-500/20 text-cargogold-400 flex items-center justify-center text-sm border border-cargogold-500/30">
                                <i class="fa-solid fa-shield-halved"></i>
                            </span>
                            <span class="w-4 h-4 rounded-full border border-steelblue/50 group-has-[:checked]:border-cargogold-500 group-has-[:checked]:bg-cargogold-500 flex items-center justify-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-deepnavy opacity-0 group-has-[:checked]:opacity-100"></span>
                            </span>
                        </div>
                        <span class="font-bold text-xs text-white group-has-[:checked]:text-cargogold-300">Admin</span>
                        <span class="text-[10px] text-slate-400 mt-0.5 leading-tight">Kontrol analitik, audit log, dan seluruh sistem.</span>
                    </label>

                    <!-- Operator -->
                    <label class="relative flex flex-col p-3 rounded-2xl border border-steelblue/30 bg-deepnavy/90 hover:border-cyan-400/60 cursor-pointer transition has-[:checked]:border-cyan-400 has-[:checked]:bg-oceanblue/20 has-[:checked]:shadow-md has-[:checked]:shadow-cyan-500/10 group">
                        <input type="radio" name="role" value="operator" {{ old('role') === 'operator' ? 'checked' : '' }} class="sr-only">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-sm border border-cyan-500/30">
                                <i class="fa-solid fa-headset"></i>
                            </span>
                            <span class="w-4 h-4 rounded-full border border-steelblue/50 group-has-[:checked]:border-cyan-400 group-has-[:checked]:bg-cyan-400 flex items-center justify-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-deepnavy opacity-0 group-has-[:checked]:opacity-100"></span>
                            </span>
                        </div>
                        <span class="font-bold text-xs text-white group-has-[:checked]:text-cyan-300">Operator</span>
                        <span class="text-[10px] text-slate-400 mt-0.5 leading-tight">Kelola rute, checkpoint, dan eskalasi insiden.</span>
                    </label>

                    <!-- Customer -->
                    <label class="relative flex flex-col p-3 rounded-2xl border border-steelblue/30 bg-deepnavy/90 hover:border-emerald-400/60 cursor-pointer transition has-[:checked]:border-emerald-400 has-[:checked]:bg-oceanblue/20 has-[:checked]:shadow-md has-[:checked]:shadow-emerald-500/10 group">
                        <input type="radio" name="role" value="customer" {{ old('role', 'customer') === 'customer' ? 'checked' : '' }} class="sr-only">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm border border-emerald-500/30">
                                <i class="fa-solid fa-user"></i>
                            </span>
                            <span class="w-4 h-4 rounded-full border border-steelblue/50 group-has-[:checked]:border-emerald-400 group-has-[:checked]:bg-emerald-400 flex items-center justify-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-deepnavy opacity-0 group-has-[:checked]:opacity-100"></span>
                            </span>
                        </div>
                        <span class="font-bold text-xs text-white group-has-[:checked]:text-emerald-300">Customer</span>
                        <span class="text-[10px] text-slate-400 mt-0.5 leading-tight">Pelacakan kargo, cetak waybill, dan status live.</span>
                    </label>
                </div>
            </div>

            <!-- Biodata Grid: Nama Lengkap & Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-bold uppercase text-steelblue mb-1 font-mono">Nama Lengkap *</label>
                    <div class="relative">
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Sarah Johnson / Budi Santoso"
                            class="w-full pl-9 pr-3 py-2.5 text-xs sm:text-sm bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
                        <i class="fa-regular fa-user absolute left-3 top-3 text-slate-400 text-xs"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-steelblue mb-1 font-mono">Alamat Email *</label>
                    <div class="relative">
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="user@cargotrace.com"
                            class="w-full pl-9 pr-3 py-2.5 text-xs sm:text-sm bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
                        <i class="fa-regular fa-envelope absolute left-3 top-3 text-slate-400 text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- Biodata Grid: Perusahaan & Telepon -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-bold uppercase text-steelblue mb-1 font-mono">Perusahaan / Divisi / Instansi</label>
                    <div class="relative">
                        <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="Contoh: Global Logistics / Universitas"
                            class="w-full pl-9 pr-3 py-2.5 text-xs sm:text-sm bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
                        <i class="fa-solid fa-building absolute left-3 top-3 text-slate-400 text-xs"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-steelblue mb-1 font-mono">Nomor Telepon / WhatsApp</label>
                    <div class="relative">
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Contoh: +62 812-3456-7890"
                            class="w-full pl-9 pr-3 py-2.5 text-xs sm:text-sm bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
                        <i class="fa-solid fa-phone absolute left-3 top-3 text-slate-400 text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- Password & Confirm Password -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-bold uppercase text-steelblue mb-1 font-mono">Kata Sandi *</label>
                    <div class="relative">
                        <input type="password" name="password" required placeholder="Minimal 8 karakter"
                            class="w-full pl-9 pr-3 py-2.5 text-xs sm:text-sm bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
                        <i class="fa-solid fa-lock absolute left-3 top-3 text-slate-400 text-xs"></i>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-steelblue mb-1 font-mono">Konfirmasi Sandi *</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi"
                            class="w-full pl-9 pr-3 py-2.5 text-xs sm:text-sm bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
                        <i class="fa-solid fa-shield-check absolute left-3 top-3 text-slate-400 text-xs"></i>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-cargogold-600 via-cargogold-500 to-amber-600 hover:brightness-110 text-deepnavy font-black text-xs sm:text-sm uppercase tracking-wider rounded-xl transition shadow-lg shadow-cargogold-600/25 mt-3 flex items-center justify-center gap-2">
                <span>Daftar Akun & Buka Dashboard</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-steelblue/20 text-center">
            <p class="text-xs text-slate-400">
                Sudah memiliki akun terdaftar?
                <a href="{{ route('login') }}" class="text-cargogold-400 font-bold hover:underline ml-1">
                    Masuk ke Portal &rarr;
                </a>
            </p>
        </div>
    </div>

</body>
</html>
