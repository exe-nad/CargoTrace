<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - CargoTrace Global Shipment Visibility</title>

    <!-- Tailwind CSS with Dark Mode & Custom Ocean Palette -->
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
                        },
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0284c7',
                            600: '#0369a1',
                            700: '#0f172a',
                            800: '#0b1120',
                            900: '#080d19',
                            accent: '#06b6d4',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Leaflet.js CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap');
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background-color 0.25s ease, color 0.25s ease;
        }
        .sidebar-active {
            background: linear-gradient(90deg, rgba(212, 175, 55, 0.18) 0%, rgba(212, 175, 55, 0.04) 100%);
            color: #facc15;
            border-left: 4px solid #d4af37;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #060c1d;
        }
        ::-webkit-scrollbar-thumb {
            background: #1c2541;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #3a7ca5;
        }
        /* Hide Google Translate Banner Frame */
        .goog-te-banner-frame.skiptranslate { display: none !important; }
        body { top: 0px !important; }
        #goog-gt-tt { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="text-slate-800 dark:text-slate-100 antialiased flex h-screen overflow-hidden bg-slate-50 dark:bg-deepnavy transition-colors duration-200">

    <!-- Left Sidebar (Deep Navy, Gold Globe & Vessel Logo, Ocean Accents) -->
    <aside class="w-64 bg-slate-900 dark:bg-[#070e22] text-slate-300 flex flex-col flex-shrink-0 z-30 select-none border-r border-slate-800 dark:border-navyink">
        <!-- Brand Header (Gold Theme: Globe + Track + Vessel) -->
        <a href="{{ url('/') }}" class="h-20 flex items-center px-6 space-x-3 border-b border-slate-800/80 dark:border-navyink/80 group">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-navyink via-deepnavy to-navyink border border-cargogold-600/50 p-1.5 shadow-lg shadow-cargogold-600/10 flex items-center justify-center transition-transform duration-200 group-hover:scale-105">
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
            <div>
                <span class="text-xl font-bold tracking-tight text-white flex items-center gap-1.5 font-mono">
                    Cargo<span class="text-cargogold-400">Trace</span>
                </span>
                <span class="text-[10px] uppercase tracking-wider text-cargogold-500 font-semibold block -mt-0.5">Visibility Platform</span>
            </div>
        </a>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 py-6 space-y-1.5 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('dashboard') ? 'sidebar-active font-semibold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 dark:hover:bg-navyink/60' }}">
                <i class="fa-solid fa-table-columns w-6 text-base text-cargogold-400"></i>
                <span class="ml-2">Dashboard</span>
            </a>

            <a href="{{ route('shipments.index') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('shipments.*') ? 'sidebar-active font-semibold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 dark:hover:bg-navyink/60' }}">
                <i class="fa-solid fa-box-archive w-6 text-base text-slate-400"></i>
                <span class="ml-2">Shipments</span>
            </a>

            <a href="{{ route('tracking.index') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('tracking.*') ? 'sidebar-active font-semibold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 dark:hover:bg-navyink/60' }}">
                <i class="fa-solid fa-location-dot w-6 text-base text-cyan-400"></i>
                <span class="ml-2">Tracking</span>
            </a>

            <a href="{{ route('exceptions.index') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('exceptions.*') ? 'sidebar-active font-semibold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 dark:hover:bg-navyink/60' }}">
                <i class="fa-solid fa-triangle-exclamation w-6 text-base text-amber-400"></i>
                <span class="ml-2">Exceptions</span>
                @php
                    $pendingEx = \App\Models\ShipmentException::whereIn('status', ['OPEN', 'INVESTIGATING'])->count();
                @endphp
                @if($pendingEx > 0)
                    <span class="ml-auto px-2 py-0.5 text-[11px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30 rounded-full">{{ $pendingEx }}</span>
                @endif
            </a>

            <a href="{{ route('carriers.index') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('carriers.*') ? 'sidebar-active font-semibold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 dark:hover:bg-navyink/60' }}">
                <i class="fa-solid fa-truck-fast w-6 text-base text-slate-400"></i>
                <span class="ml-2">Carriers</span>
            </a>

            <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('reports.*') ? 'sidebar-active font-semibold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 dark:hover:bg-navyink/60' }}">
                <i class="fa-solid fa-chart-line w-6 text-base text-slate-400"></i>
                <span class="ml-2">Reports</span>
            </a>

            <!-- User Profile Menu -->
            <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('profile.*') ? 'sidebar-active font-semibold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 dark:hover:bg-navyink/60' }}">
                <i class="fa-solid fa-id-badge w-6 text-base text-cargogold-400"></i>
                <span class="ml-2">Profile</span>
            </a>

            @if(auth()->user() && auth()->user()->isAdmin())
            <div class="pt-4 mt-4 border-t border-slate-800/80 dark:border-navyink/80">
                <span class="px-4 text-[11px] font-semibold tracking-wider uppercase text-slate-500 block mb-2">Administration</span>
                <a href="{{ route('audit.index') }}" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('audit.*') ? 'sidebar-active font-semibold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 dark:hover:bg-navyink/60' }}">
                    <i class="fa-solid fa-clipboard-list w-6 text-base text-purple-400"></i>
                    <span class="ml-2">Audit Logs</span>
                </a>
            </div>
            @endif

            <!-- Direct Link to Welcome Landing Page -->
            <div class="pt-3 mt-3 border-t border-slate-800/80 dark:border-navyink/80">
                <a href="{{ url('/') }}" target="_blank"
                    class="flex items-center px-4 py-2 rounded-xl text-xs font-semibold text-cargogold-400 hover:bg-cargogold-500/10 transition">
                    <i class="fa-solid fa-globe w-6 text-sm"></i>
                    <span>Welcome Page &rarr;</span>
                </a>
            </div>
        </nav>

        <!-- Bottom User / Logout Section -->
        <div class="p-4 border-t border-slate-800 dark:border-navyink bg-slate-950/40 dark:bg-deepnavy/90">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center px-4 py-2.5 text-sm font-medium text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-xl transition-all duration-150">
                    <i class="fa-solid fa-arrow-right-from-bracket w-6 text-base"></i>
                    <span class="ml-2">Logs Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50 dark:bg-deepnavy">

        <!-- Top Header Bar -->
        <header class="h-20 bg-white dark:bg-navyink/90 border-b border-slate-200/80 dark:border-navyink px-8 flex items-center justify-between flex-shrink-0 z-20 transition-colors duration-200">
            <!-- Left: Page Title -->
            <div class="flex items-center space-x-4">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">@yield('header', 'Dashboard')</h1>
                <span class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live Visibility Connected
                </span>
            </div>

            <!-- Right: Search, Language Selector, Dark Mode Toggle, Notification Bell, User Dropdown -->
            <div class="flex items-center space-x-4 md:space-x-5">

                <!-- Search Bar -->
                <form action="{{ route('shipments.index') }}" method="GET" class="relative hidden sm:block">
                    <input type="text" name="search" placeholder="Search shipments..." value="{{ request('search') }}"
                        class="w-56 pl-10 pr-4 py-2 text-xs bg-slate-100/80 dark:bg-deepnavy/90 border border-slate-200 dark:border-steelblue/30 rounded-xl text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cargogold-500 focus:bg-white dark:focus:bg-deepnavy transition-all duration-150">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-2.5 text-slate-400 text-xs"></i>
                </form>

                <!-- Language Selector Dropdown -->
                <div class="relative" id="appLangDropdownWrapper">
                    <button type="button" id="appLangDropdownBtn"
                        class="px-2.5 py-1.5 rounded-xl bg-slate-100 dark:bg-deepnavy/90 border border-slate-200 dark:border-steelblue/30 hover:border-cargogold-500 text-slate-700 dark:text-slate-200 text-xs font-medium flex items-center gap-1.5 transition">
                        <span id="appLangFlag">🇮🇩</span>
                        <span id="appLangLabel" class="hidden md:inline">ID</span>
                        <i class="fa-solid fa-chevron-down text-[9px] text-slate-400"></i>
                    </button>

                    <div id="appLangMenu" class="hidden absolute right-0 mt-2 w-48 rounded-2xl bg-white dark:bg-navyink border border-slate-200 dark:border-steelblue/40 shadow-2xl py-1.5 z-50 text-xs text-slate-700 dark:text-slate-200 backdrop-blur-xl">
                        <div class="px-3 py-1 text-[10px] uppercase font-bold text-slate-400 dark:text-steelblue border-b border-slate-100 dark:border-deepnavy">
                            Language
                        </div>
                        <div class="max-h-60 overflow-y-auto py-1">
                            <button onclick="setAppLanguage('id', '🇮🇩', 'ID')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-oceanblue/40 flex items-center justify-between">
                                <span>🇮🇩 Indonesia</span>
                                <span class="text-[10px] text-cargogold-400 font-mono">ID</span>
                            </button>
                            <button onclick="setAppLanguage('en', '🇬🇧', 'EN')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-oceanblue/40 flex items-center justify-between">
                                <span>🇬🇧 English</span>
                                <span class="text-[10px] text-slate-400 font-mono">EN</span>
                            </button>
                            <button onclick="setAppLanguage('ja', '🇯🇵', 'JA')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-oceanblue/40 flex items-center justify-between">
                                <span>🇯🇵 日本語</span>
                                <span class="text-[10px] text-slate-400 font-mono">JA</span>
                            </button>
                            <button onclick="setAppLanguage('zh-CN', '🇨🇳', 'ZH')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-oceanblue/40 flex items-center justify-between">
                                <span>🇨🇳 中文</span>
                                <span class="text-[10px] text-slate-400 font-mono">ZH</span>
                            </button>
                            <button onclick="setAppLanguage('ar', '🇸🇦', 'AR')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-oceanblue/40 flex items-center justify-between">
                                <span>🇸🇦 العربية</span>
                                <span class="text-[10px] text-slate-400 font-mono">AR</span>
                            </button>
                            <button onclick="setAppLanguage('es', '🇪🇸', 'ES')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-oceanblue/40 flex items-center justify-between">
                                <span>🇪🇸 Español</span>
                                <span class="text-[10px] text-slate-400 font-mono">ES</span>
                            </button>
                            <button onclick="setAppLanguage('fr', '🇫🇷', 'FR')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-oceanblue/40 flex items-center justify-between">
                                <span>🇫🇷 Français</span>
                                <span class="text-[10px] text-slate-400 font-mono">FR</span>
                            </button>
                            <button onclick="setAppLanguage('de', '🇩🇪', 'DE')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-oceanblue/40 flex items-center justify-between">
                                <span>🇩🇪 Deutsch</span>
                                <span class="text-[10px] text-slate-400 font-mono">DE</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Dark / Light Theme Toggle Button -->
                <button type="button" id="appThemeToggleBtn" title="Toggle Light / Dark Mode"
                    class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-deepnavy/90 border border-slate-200 dark:border-steelblue/30 text-cargogold-400 hover:border-cargogold-500/50 flex items-center justify-center transition shadow-xs">
                    <i id="appThemeIcon" class="fa-solid fa-moon text-xs"></i>
                </button>

                <!-- Notifications Dropdown -->
                <div class="relative" id="notificationBellContainer">
                    <button id="notificationBtn" class="relative p-2 text-slate-500 dark:text-slate-300 hover:text-slate-800 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-deepnavy transition">
                        <i class="fa-regular fa-bell text-lg"></i>
                        @php
                            $unreadNotifs = auth()->user() ? auth()->user()->notifications()->where('is_read', false)->count() : 0;
                        @endphp
                        @if($unreadNotifs > 0)
                            <span class="absolute top-1 right-1 w-4 h-4 bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white dark:border-navyink">
                                {{ $unreadNotifs }}
                            </span>
                        @endif
                    </button>

                    <!-- Notification Panel Popup -->
                    <div id="notificationDropdown" class="hidden absolute right-0 mt-3 w-80 bg-white dark:bg-navyink rounded-2xl shadow-2xl border border-slate-100 dark:border-steelblue/30 py-3 z-50">
                        <div class="px-4 pb-2 border-b border-slate-100 dark:border-deepnavy flex items-center justify-between">
                            <h4 class="font-bold text-sm text-slate-900 dark:text-white">Notifications</h4>
                            <form action="{{ route('notifications.read') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs text-cargogold-400 hover:underline">Mark all read</button>
                            </form>
                        </div>
                        <div class="max-h-72 overflow-y-auto divide-y divide-slate-50 dark:divide-deepnavy">
                            @php
                                $recentNotifs = auth()->user() ? auth()->user()->notifications()->latest()->limit(5)->get() : collect();
                            @endphp
                            @forelse($recentNotifs as $notif)
                                <a href="{{ $notif->link ?? '#' }}" class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-deepnavy/50 transition {{ !$notif->is_read ? 'bg-cyan-50/40 dark:bg-oceanblue/20' : '' }}">
                                    <div class="flex items-start gap-2.5">
                                        <div class="mt-0.5 w-2 h-2 rounded-full {{ !$notif->is_read ? 'bg-cargogold-500' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-slate-800 dark:text-slate-100 truncate">{{ $notif->title }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5">{{ $notif->message }}</p>
                                            <span class="text-[10px] text-slate-400 dark:text-steelblue mt-1 block">{{ $notif->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="p-6 text-center text-xs text-slate-400">
                                    <i class="fa-regular fa-bell-slash text-2xl mb-2 text-slate-300 dark:text-slate-600 block"></i>
                                    No notifications at this moment.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- User Profile Link (Direct Click to Profile Page) -->
                @auth
                <a href="{{ route('profile.show') }}" title="Buka Profil Pengguna" class="flex items-center space-x-3 pl-3 pr-2 py-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-navyink/90 border border-transparent hover:border-steelblue/30 transition group cursor-pointer">
                    <div class="w-9 h-9 rounded-full bg-slate-800 dark:bg-deepnavy text-cargogold-400 font-bold text-xs flex items-center justify-center border-2 border-cargogold-500/50 shadow-sm group-hover:scale-105 group-hover:border-cargogold-400 transition">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="hidden md:block text-left">
                        <span class="text-xs font-bold text-slate-900 dark:text-white block leading-tight group-hover:text-cargogold-400 transition">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] text-slate-500 dark:text-steelblue capitalize flex items-center gap-1">
                            @if(auth()->user()->isAdmin())
                                <i class="fa-solid fa-shield-halved text-cargogold-400 text-[9px]"></i> Administrator
                            @elseif(auth()->user()->isOperator())
                                <i class="fa-solid fa-headset text-cyan-400 text-[9px]"></i> Logistics Operator
                            @else
                                <i class="fa-solid fa-user-check text-emerald-400 text-[9px]"></i> Customer (Shipper)
                            @endif
                        </span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-[10px] text-slate-400 dark:text-steelblue group-hover:translate-x-0.5 group-hover:text-cargogold-400 transition hidden sm:inline-block"></i>
                </a>
                @endauth
            </div>
        </header>

        <!-- Hidden Google Translate Element -->
        <div id="google_translate_element_app" style="display:none;"></div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mx-8 mt-4 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-lg"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="mx-8 mt-4 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 dark:text-rose-400 text-lg"></i>
                    <div class="text-sm font-medium">
                        {{ session('error') ?? 'Please correct the errors in the form.' }}
                        @if($errors->any())
                            <ul class="list-disc list-inside mt-1 text-xs">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        <!-- Scrollable Page Body -->
        <main class="flex-1 overflow-y-auto px-8 py-6">
            @yield('content')
        </main>

    </div>

    <!-- Leaflet.js -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- UI Scripts for Notification, Theme, and Multilingual -->
    <script>
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');

        if (notificationBtn && notificationDropdown) {
            notificationBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                notificationDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', (e) => {
                if (!notificationDropdown.contains(e.target) && !notificationBtn.contains(e.target)) {
                    notificationDropdown.classList.add('hidden');
                }
            });
        }

        // Dark / Light Mode in Layout
        const appThemeToggleBtn = document.getElementById('appThemeToggleBtn');
        const appThemeIcon = document.getElementById('appThemeIcon');
        const appHtmlElement = document.documentElement;

        const currentTheme = localStorage.getItem('cargotrace_theme') || 'dark';
        if (currentTheme === 'dark') {
            appHtmlElement.classList.add('dark');
            appHtmlElement.classList.remove('light');
            if (appThemeIcon) appThemeIcon.className = 'fa-solid fa-moon text-xs';
        } else {
            appHtmlElement.classList.remove('dark');
            appHtmlElement.classList.add('light');
            if (appThemeIcon) appThemeIcon.className = 'fa-solid fa-sun text-xs text-amber-500';
        }

        if (appThemeToggleBtn) {
            appThemeToggleBtn.addEventListener('click', () => {
                if (appHtmlElement.classList.contains('dark')) {
                    appHtmlElement.classList.remove('dark');
                    appHtmlElement.classList.add('light');
                    appThemeIcon.className = 'fa-solid fa-sun text-xs text-amber-500';
                    localStorage.setItem('cargotrace_theme', 'light');
                } else {
                    appHtmlElement.classList.remove('light');
                    appHtmlElement.classList.add('dark');
                    appThemeIcon.className = 'fa-solid fa-moon text-xs text-cargogold-400';
                    localStorage.setItem('cargotrace_theme', 'dark');
                }
            });
        }

        // Language Dropdown in Layout
        const appLangDropdownBtn = document.getElementById('appLangDropdownBtn');
        const appLangMenu = document.getElementById('appLangMenu');
        const appLangFlag = document.getElementById('appLangFlag');
        const appLangLabel = document.getElementById('appLangLabel');

        if (appLangDropdownBtn && appLangMenu) {
            appLangDropdownBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                appLangMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', () => {
                appLangMenu.classList.add('hidden');
            });
        }

        function setAppLanguage(code, flag, label) {
            if (appLangFlag) appLangFlag.textContent = flag;
            if (appLangLabel) appLangLabel.textContent = label;
            localStorage.setItem('cargotrace_lang', code);

            // Google translate trigger
            const selectEl = document.querySelector('.goog-te-combo');
            if (selectEl) {
                selectEl.value = code;
                selectEl.dispatchEvent(new Event('change'));
            }
        }

        function googleTranslateElementInitApp() {
            new google.translate.TranslateElement({
                pageLanguage: 'id',
                autoDisplay: false
            }, 'google_translate_element_app');
        }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInitApp"></script>

    @stack('scripts')
</body>
</html>
