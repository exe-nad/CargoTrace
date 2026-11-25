@extends('layouts.app')

@section('title', 'Profil Akun - ' . $user->name)
@section('header', 'Profil Pengguna')

@section('content')
<div class="space-y-6">

    <!-- Hero Header Profile Card -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-navyink via-slate-900 to-deepnavy border border-steelblue/40 p-6 sm:p-8 text-white shadow-2xl shadow-deepnavy/60">
        <div class="absolute -right-16 -top-16 w-72 h-72 rounded-full bg-oceanblue/20 blur-3xl pointer-events-none"></div>
        <div class="absolute right-8 bottom-3 opacity-10 pointer-events-none text-steelblue text-8xl font-black">
            <i class="fa-solid fa-id-card"></i>
        </div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <!-- Avatar Circle -->
                <div class="relative flex-shrink-0">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-gradient-to-br from-deepnavy via-navyink to-slate-900 border-2 border-cargogold-500/60 flex items-center justify-center text-cargogold-400 font-mono font-black text-2xl sm:text-3xl shadow-xl shadow-cargogold-600/20">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-emerald-500 border-2 border-deepnavy flex items-center justify-center text-white text-[10px]" title="Akun Aktif & Terverifikasi">
                        <i class="fa-solid fa-check"></i>
                    </span>
                </div>

                <!-- User Meta -->
                <div>
                    <div class="flex flex-wrap items-center gap-2.5 mb-1.5">
                        <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">{{ $user->name }}</h2>

                        @if($user->isAdmin())
                            <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-cargogold-500/20 text-cargogold-400 border border-cargogold-500/40 flex items-center gap-1.5 shadow-sm">
                                <i class="fa-solid fa-shield-halved text-xs"></i> Administrator Sistem
                            </span>
                        @elseif($user->isOperator())
                            <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 flex items-center gap-1.5 shadow-sm">
                                <i class="fa-solid fa-headset text-xs"></i> Logistik Operator
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 flex items-center gap-1.5 shadow-sm">
                                <i class="fa-solid fa-user-check text-xs"></i> Customer / Shipper
                            </span>
                        @endif
                    </div>

                    <p class="text-xs text-slate-300 flex flex-wrap items-center gap-y-1 gap-x-4">
                        <span class="flex items-center gap-1.5 text-steelblue">
                            <i class="fa-regular fa-envelope text-slate-400"></i>
                            <span class="text-slate-200">{{ $user->email }}</span>
                        </span>
                        @if($user->company_name)
                            <span class="flex items-center gap-1.5 text-steelblue">
                                <i class="fa-solid fa-building text-slate-400"></i>
                                <span class="text-slate-200">{{ $user->company_name }}</span>
                            </span>
                        @endif
                        @if($user->phone)
                            <span class="flex items-center gap-1.5 text-steelblue">
                                <i class="fa-solid fa-phone text-slate-400"></i>
                                <span class="text-slate-200">{{ $user->phone }}</span>
                            </span>
                        @endif
                    </p>

                    <div class="mt-2 text-[11px] text-slate-400 flex items-center gap-2 font-mono">
                        <i class="fa-regular fa-calendar-check text-cargogold-400"></i>
                        <span>Bergabung sejak {{ $user->created_at->format('d F Y') }} ({{ $user->created_at->diffForHumans() }})</span>
                    </div>
                </div>
            </div>

            <!-- Quick Action Badges -->
            <div class="flex flex-wrap md:flex-col items-start md:items-end gap-2 text-xs">
                <span class="px-3 py-1.5 rounded-xl bg-deepnavy/90 border border-steelblue/30 text-slate-300 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>Sesi: <strong>Aktif (Online)</strong></span>
                </span>
                <span class="px-3 py-1.5 rounded-xl bg-deepnavy/90 border border-steelblue/30 text-steelblue font-mono text-[11px]">
                    User ID: #CT-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Role Telemetry Metrics Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-navyink/90 rounded-2xl p-5 border border-steelblue/30 shadow-lg shadow-deepnavy/40 backdrop-blur-md flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-oceanblue/20 text-cyan-400 border border-oceanblue/40 flex items-center justify-center flex-shrink-0 text-xl">
                <i class="{{ $stats['primary_icon'] ?? 'fa-solid fa-boxes-stacked' }}"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-steelblue">{{ $stats['primary_label'] ?? 'Aktivitas' }}</p>
                <h3 class="text-2xl font-black text-white mt-0.5">{{ $stats['primary_value'] ?? '0' }}</h3>
            </div>
        </div>

        <div class="bg-navyink/90 rounded-2xl p-5 border border-steelblue/30 shadow-lg shadow-deepnavy/40 backdrop-blur-md flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-cargogold-500/20 text-cargogold-400 border border-cargogold-500/40 flex items-center justify-center flex-shrink-0 text-xl">
                <i class="{{ $stats['sec_icon'] ?? 'fa-solid fa-truck' }}"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-steelblue">{{ $stats['sec_label'] ?? 'Metrik 2' }}</p>
                <h3 class="text-2xl font-black text-cargogold-400 mt-0.5">{{ $stats['sec_value'] ?? '0' }}</h3>
            </div>
        </div>

        <div class="bg-navyink/90 rounded-2xl p-5 border border-steelblue/30 shadow-lg shadow-deepnavy/40 backdrop-blur-md flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-rose-500/20 text-rose-400 border border-rose-500/40 flex items-center justify-center flex-shrink-0 text-xl">
                <i class="{{ $stats['tert_icon'] ?? 'fa-solid fa-triangle-exclamation' }}"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-steelblue">{{ $stats['tert_label'] ?? 'Metrik 3' }}</p>
                <h3 class="text-2xl font-black text-rose-400 mt-0.5">{{ $stats['tert_value'] ?? '0' }}</h3>
            </div>
        </div>

        <div class="bg-navyink/90 rounded-2xl p-5 border border-steelblue/30 shadow-lg shadow-deepnavy/40 backdrop-blur-md flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 flex items-center justify-center flex-shrink-0 text-xl">
                <i class="{{ $stats['audit_icon'] ?? 'fa-solid fa-clipboard-check' }}"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-steelblue">{{ $stats['audit_label'] ?? 'Metrik 4' }}</p>
                <h3 class="text-2xl font-black text-emerald-400 mt-0.5">{{ $stats['audit_value'] ?? '0' }}</h3>
            </div>
        </div>
    </div>

    <!-- Main Content 2 Columns: Edit Forms & Account Privileges -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Edit Biodata & Password (col-span-7) -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Card 1: Form Ubah Biodata Profil -->
            <div class="bg-navyink/90 rounded-2xl p-6 sm:p-7 border border-steelblue/30 shadow-lg shadow-deepnavy/40 backdrop-blur-md">
                <div class="flex items-center justify-between pb-4 border-b border-steelblue/20 mb-5">
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-user-pen text-cargogold-400"></i>
                            <span>Biodata & Informasi Pribadi</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Perbarui informasi identitas akun operasional Anda</p>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-lg bg-deepnavy text-steelblue border border-steelblue/30 font-mono">
                        Editable
                    </span>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-steelblue mb-1 font-mono">Nama Lengkap *</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full py-2.5 px-3.5 text-xs sm:text-sm bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-steelblue mb-1 font-mono">Alamat Email *</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full py-2.5 px-3.5 text-xs sm:text-sm bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-steelblue mb-1 font-mono">Perusahaan / Divisi / Instansi</label>
                            <input type="text" name="company_name" value="{{ old('company_name', $user->company_name) }}" placeholder="e.g. PT Global Forwarding"
                                class="w-full py-2.5 px-3.5 text-xs sm:text-sm bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-steelblue mb-1 font-mono">Nomor Telepon / WhatsApp</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="e.g. +62 812 3456 7890"
                                class="w-full py-2.5 px-3.5 text-xs sm:text-sm bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-steelblue mb-1 font-mono">Peran Sistem (Role)</label>
                        <div class="py-2.5 px-3.5 text-xs sm:text-sm bg-deepnavy/60 border border-steelblue/20 rounded-xl text-slate-400 flex items-center justify-between">
                            <span class="font-bold text-slate-200 capitalize">
                                {{ $user->role }} &mdash;
                                @if($user->isAdmin())
                                    Administrator Sistem (Hak Akses Penuh)
                                @elseif($user->isOperator())
                                    Logistics Operator (Pengendali Pengiriman & Telemetri)
                                @else
                                    Customer / Shipper (Pelacak Kargo & Waybill)
                                @endif
                            </span>
                            <span class="text-[11px] text-cargogold-400 font-mono"><i class="fa-solid fa-lock text-[10px]"></i> Tetap</span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-steelblue/20 flex items-center justify-end">
                        <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-oceanblue to-cyan-600 hover:from-oceanblue/90 hover:to-cyan-500 rounded-xl transition shadow-md shadow-oceanblue/30 flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk text-cargogold-400"></i>
                            <span>Simpan Perubahan Biodata</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Card 2: Keamanan & Ganti Kata Sandi -->
            <div class="bg-navyink/90 rounded-2xl p-6 sm:p-7 border border-steelblue/30 shadow-lg shadow-deepnavy/40 backdrop-blur-md">
                <div class="flex items-center justify-between pb-4 border-b border-steelblue/20 mb-5">
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-key text-cargogold-400"></i>
                            <span>Keamanan & Ganti Kata Sandi</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Pastikan akun Anda terlindungi dengan sandi yang kuat</p>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-lg bg-deepnavy text-cargogold-400 border border-steelblue/30 font-mono">
                        Security
                    </span>
                </div>

                <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold uppercase text-steelblue mb-1 font-mono">Kata Sandi Saat Ini *</label>
                        <input type="password" name="current_password" required placeholder="Masukkan kata sandi lama Anda"
                            class="w-full py-2.5 px-3.5 text-xs sm:text-sm bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-steelblue mb-1 font-mono">Kata Sandi Baru *</label>
                            <input type="password" name="password" required placeholder="Minimal 8 karakter"
                                class="w-full py-2.5 px-3.5 text-xs sm:text-sm bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-steelblue mb-1 font-mono">Konfirmasi Sandi Baru *</label>
                            <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi baru"
                                class="w-full py-2.5 px-3.5 text-xs sm:text-sm bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-steelblue/20 flex items-center justify-end">
                        <button type="submit" class="px-5 py-2.5 text-xs font-bold text-deepnavy bg-gradient-to-r from-cargogold-400 to-amber-400 hover:from-cargogold-300 hover:to-amber-300 rounded-xl transition shadow-md shadow-cargogold-500/20 flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-xs"></i>
                            <span>Perbarui Kata Sandi</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- Right Column: Privileges, Recent Activity & Security Info (col-span-5) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Card 3: Hak Akses & Tanggung Jawab Peran -->
            <div class="bg-navyink/90 rounded-2xl p-6 border border-steelblue/30 shadow-lg shadow-deepnavy/40 backdrop-blur-md space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-steelblue/20">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-user-shield text-cargogold-400"></i>
                        <span>Otoritas & Hak Akses Peran</span>
                    </h3>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-deepnavy text-steelblue uppercase">
                        {{ $user->role }}
                    </span>
                </div>

                <div class="space-y-3 text-xs">
                    @if($user->isAdmin())
                        <div class="p-3 rounded-xl bg-deepnavy/90 border border-steelblue/30 flex items-start gap-3">
                            <i class="fa-solid fa-check-double text-cargogold-400 text-sm mt-0.5"></i>
                            <div>
                                <strong class="text-white block font-semibold">Administrasi Penuh Platform</strong>
                                <span class="text-slate-400">Dapat mengelola armada carrier, membuat shipment, mengaudit sistem, serta memantau semua kargo.</span>
                            </div>
                        </div>
                        <div class="p-3 rounded-xl bg-deepnavy/90 border border-steelblue/30 flex items-start gap-3">
                            <i class="fa-solid fa-clipboard-list text-purple-400 text-sm mt-0.5"></i>
                            <div>
                                <strong class="text-white block font-semibold">Audit Logs & Keamanan</strong>
                                <span class="text-slate-400">Akses eksklusif melihat seluruh jejak rekam mutasi data, IP address, dan perubahan checkpoint.</span>
                            </div>
                        </div>
                    @elseif($user->isOperator())
                        <div class="p-3 rounded-xl bg-deepnavy/90 border border-steelblue/30 flex items-start gap-3">
                            <i class="fa-solid fa-route text-cyan-400 text-sm mt-0.5"></i>
                            <div>
                                <strong class="text-white block font-semibold">Dispatch & Registrasi Pengiriman</strong>
                                <span class="text-slate-400">Dapat meregistrasi kargo baru, menginput muatan manifest, dan menautkan rute carrier.</span>
                            </div>
                        </div>
                        <div class="p-3 rounded-xl bg-deepnavy/90 border border-steelblue/30 flex items-start gap-3">
                            <i class="fa-solid fa-location-crosshairs text-cyan-400 text-sm mt-0.5"></i>
                            <div>
                                <strong class="text-white block font-semibold">Telemetri Peta & Log Checkpoint</strong>
                                <span class="text-slate-400">Memperbarui posisi GPS container, memicu eskalasi delay, dan mencatat penyelesaian insiden.</span>
                            </div>
                        </div>
                    @else
                        <div class="p-3 rounded-xl bg-deepnavy/90 border border-steelblue/30 flex items-start gap-3">
                            <i class="fa-solid fa-map-location-dot text-emerald-400 text-sm mt-0.5"></i>
                            <div>
                                <strong class="text-white block font-semibold">Visibilitas Real-Time Kargo</strong>
                                <span class="text-slate-400">Memantau trayektori pengiriman barang secara live melalui satelit OpenStreetMap.</span>
                            </div>
                        </div>
                        <div class="p-3 rounded-xl bg-deepnavy/90 border border-steelblue/30 flex items-start gap-3">
                            <i class="fa-solid fa-print text-emerald-400 text-sm mt-0.5"></i>
                            <div>
                                <strong class="text-white block font-semibold">Cetak Waybill Digital</strong>
                                <span class="text-slate-400">Mencetak Surat Muatan Udara / Laut berstandar internasional dengan barcode kargo.</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card 4: Riwayat Aktivitas Akun Terakhir -->
            <div class="bg-navyink/90 rounded-2xl p-6 border border-steelblue/30 shadow-lg shadow-deepnavy/40 backdrop-blur-md space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-steelblue/20">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-cargogold-400"></i>
                        <span>Aktivitas & Log Keamanan Terakhir</span>
                    </h3>
                    <span class="text-[10px] text-slate-400 font-mono">Immutable</span>
                </div>

                <div class="space-y-2.5">
                    @forelse($recentActivities as $act)
                        <div class="p-3 rounded-xl bg-deepnavy/90 border border-steelblue/20 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold
                                    {{ str_contains($act->action, 'LOGIN') || str_contains($act->action, 'REGISTER') ? 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30' : (str_contains($act->action, 'UPDATE') ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-oceanblue/20 text-slate-300') }}">
                                    {{ $act->action }}
                                </span>
                                <span class="text-slate-300 font-medium">
                                    {{ $act->entity_type ? class_basename($act->entity_type) . ' #' . $act->entity_id : 'Sistem Portal' }}
                                </span>
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono">
                                {{ $act->created_at->diffForHumans() }}
                            </span>
                        </div>
                    @empty
                        <div class="py-4 text-center text-xs text-slate-500">
                            Belum ada riwayat aktivitas yang tercatat.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Card 5: Status Keamanan & Sesi -->
            <div class="bg-navyink/90 rounded-2xl p-5 border border-steelblue/30 shadow-lg shadow-deepnavy/40 backdrop-blur-md text-xs space-y-2">
                <div class="flex items-center justify-between text-slate-300">
                    <span class="text-slate-400">Algoritma Sandi:</span>
                    <span class="font-mono text-emerald-400 font-bold">Bcrypt (Cost 12)</span>
                </div>
                <div class="flex items-center justify-between text-slate-300">
                    <span class="text-slate-400">Proteksi CSRF:</span>
                    <span class="font-mono text-emerald-400 font-bold">Enabled & Verified</span>
                </div>
                <div class="flex items-center justify-between text-slate-300">
                    <span class="text-slate-400">Zona Waktu Server:</span>
                    <span class="font-mono text-steelblue">{{ config('app.timezone', 'UTC') }}</span>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
