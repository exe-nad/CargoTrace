<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - CargoTrace Global Shipment Visibility</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
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
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
        }
        .sidebar-active {
            background: linear-gradient(90deg, rgba(6, 182, 212, 0.22) 0%, rgba(6, 182, 212, 0.05) 100%);
            color: #38bdf8;
            border-left: 4px solid #06b6d4;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @stack('styles')
</head>
<body class="text-slate-800 antialiased flex h-screen overflow-hidden">

    <!-- Left Sidebar (Matching Mockup exactly: Dark Navy, Ship Logo, Cyan Highlights) -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col flex-shrink-0 z-30 select-none border-r border-slate-800">
        <!-- Brand Header -->
        <div class="h-20 flex items-center px-6 space-x-3 border-b border-slate-800/80">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-600 to-sky-400 flex items-center justify-center text-white shadow-lg shadow-cyan-500/30">
                <i class="fa-solid fa-ship text-lg"></i>
            </div>
            <div>
                <span class="text-xl font-bold tracking-tight text-white flex items-center gap-1.5">
                    CargoTrace
                </span>
                <span class="text-[10px] uppercase tracking-wider text-cyan-400 font-semibold block -mt-0.5">Visibility Platform</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 py-6 space-y-1.5 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('dashboard') || request()->is('/') ? 'sidebar-active text-cyan-400 font-semibold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-table-columns w-6 text-base text-cyan-400"></i>
                <span class="ml-2">Dashboard</span>
            </a>

            <a href="{{ route('shipments.index') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('shipments.*') ? 'sidebar-active text-cyan-400 font-semibold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-box-archive w-6 text-base text-slate-400"></i>
                <span class="ml-2">Shipments</span>
            </a>

            <a href="{{ route('tracking.index') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('tracking.*') ? 'sidebar-active text-cyan-400 font-semibold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-location-dot w-6 text-base text-slate-400"></i>
                <span class="ml-2">Tracking</span>
            </a>

            <a href="{{ route('exceptions.index') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('exceptions.*') ? 'sidebar-active text-cyan-400 font-semibold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-triangle-exclamation w-6 text-base text-amber-400"></i>
                <span class="ml-2">Exceptions</span>
                @php
                    $pendingEx = \App\Models\ShipmentException::whereIn('status', ['OPEN', 'INVESTIGATING'])->count();
                @endphp
                @if($pendingEx > 0)
                    <span class="ml-auto px-2 py-0.5 text-[11px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30 rounded-full">{{ $pendingEx }}</span>
                @endif
            </a>

            <a href="{{ route('carriers.index') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('carriers.*') ? 'sidebar-active text-cyan-400 font-semibold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-truck-fast w-6 text-base text-slate-400"></i>
                <span class="ml-2">Carriers</span>
            </a>

            <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('reports.*') ? 'sidebar-active text-cyan-400 font-semibold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-chart-line w-6 text-base text-slate-400"></i>
                <span class="ml-2">Reports</span>
            </a>

            @if(auth()->user() && auth()->user()->isAdmin())
            <div class="pt-4 mt-4 border-t border-slate-800/80">
                <span class="px-4 text-[11px] font-semibold tracking-wider uppercase text-slate-500 block mb-2">Administration</span>
                <a href="{{ route('audit.index') }}" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('audit.*') ? 'sidebar-active text-cyan-400 font-semibold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <i class="fa-solid fa-clipboard-list w-6 text-base text-purple-400"></i>
                    <span class="ml-2">Audit Logs</span>
                </a>
            </div>
            @endif
        </nav>

        <!-- Bottom User / Logout Section -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/40">
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
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">

        <!-- Top Header Bar -->
        <header class="h-20 bg-white border-b border-slate-200/80 px-8 flex items-center justify-between flex-shrink-0 z-20">
            <!-- Left: Page Title -->
            <div class="flex items-center space-x-4">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">@yield('header', 'Dashboard')</h1>
                <span class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live Visibility Connected
                </span>
            </div>

            <!-- Right: Search, Notification Bell, User Dropdown -->
            <div class="flex items-center space-x-6">
                <!-- Search Bar -->
                <form action="{{ route('shipments.index') }}" method="GET" class="relative hidden sm:block">
                    <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                        class="w-64 pl-10 pr-4 py-2 text-sm bg-slate-100/80 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white transition-all duration-150">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                </form>

                <!-- Notifications Dropdown -->
                <div class="relative" x-data="{ open: false }" id="notificationBellContainer">
                    <button id="notificationBtn" class="relative p-2 text-slate-500 hover:text-slate-800 rounded-xl hover:bg-slate-100 transition">
                        <i class="fa-regular fa-bell text-xl"></i>
                        @php
                            $unreadNotifs = auth()->user() ? auth()->user()->notifications()->where('is_read', false)->count() : 0;
                        @endphp
                        @if($unreadNotifs > 0)
                            <span class="absolute top-1.5 right-1.5 w-5 h-5 bg-rose-500 text-white text-[11px] font-bold rounded-full flex items-center justify-center border-2 border-white">
                                {{ $unreadNotifs }}
                            </span>
                        @endif
                    </button>

                    <!-- Notification Panel Popup -->
                    <div id="notificationDropdown" class="hidden absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 py-3 z-50">
                        <div class="px-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                            <h4 class="font-bold text-sm text-slate-900">Notifications</h4>
                            <form action="{{ route('notifications.read') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs text-cyan-600 hover:underline">Mark all read</button>
                            </form>
                        </div>
                        <div class="max-h-72 overflow-y-auto divide-y divide-slate-50">
                            @php
                                $recentNotifs = auth()->user() ? auth()->user()->notifications()->latest()->limit(5)->get() : collect();
                            @endphp
                            @forelse($recentNotifs as $notif)
                                <a href="{{ $notif->link ?? '#' }}" class="block px-4 py-3 hover:bg-slate-50 transition {{ !$notif->is_read ? 'bg-cyan-50/40' : '' }}">
                                    <div class="flex items-start gap-2.5">
                                        <div class="mt-0.5 w-2 h-2 rounded-full {{ !$notif->is_read ? 'bg-cyan-500' : 'bg-slate-300' }}"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-slate-800 truncate">{{ $notif->title }}</p>
                                            <p class="text-xs text-slate-500 line-clamp-2 mt-0.5">{{ $notif->message }}</p>
                                            <span class="text-[10px] text-slate-400 mt-1 block">{{ $notif->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="p-6 text-center text-xs text-slate-400">
                                    <i class="fa-regular fa-bell-slash text-2xl mb-2 text-slate-300 block"></i>
                                    No notifications at this moment.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown (Sarah Johnson in Mockup) -->
                @auth
                <div class="flex items-center space-x-3 pl-2 border-l border-slate-200">
                    <div class="w-10 h-10 rounded-full bg-slate-800 text-white font-bold text-sm flex items-center justify-center border-2 border-white shadow-sm ring-2 ring-slate-100">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="hidden md:block text-left">
                        <span class="text-sm font-bold text-slate-900 block leading-tight">{{ auth()->user()->name }}</span>
                        <span class="text-xs text-slate-500 capitalize">
                            @if(auth()->user()->isAdmin())
                                Administrator
                            @elseif(auth()->user()->isOperator())
                                Logistics Operator
                            @else
                                Shipper (Customer)
                            @endif
                        </span>
                    </div>
                </div>
                @endauth
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mx-8 mt-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm animate-fade-in">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="mx-8 mt-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-sm animate-fade-in">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 text-lg"></i>
                    <div class="text-sm font-medium">
                        {{ session('error') ?? 'Please correct the errors in the form.' }}
                        @if($errors->any())
                            <ul class="list-disc list-inside mt-1 text-xs">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
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

    <script>
        // Toggle notification dropdown
        const notifBtn = document.getElementById('notificationBtn');
        const notifDropdown = document.getElementById('notificationDropdown');
        if (notifBtn && notifDropdown) {
            notifBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                notifDropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', (e) => {
                if (!notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
                    notifDropdown.classList.add('hidden');
                }
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
