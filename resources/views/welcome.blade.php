<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CargoTrace &mdash; Global Shipment Visibility & Tracking Platform</title>

    <!-- Tailwind CSS with CDN -->
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
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace']
                    },
                    animation: {
                        'pulse-slow': 'pulse 3.5s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'spin-slow': 'spin 22s linear infinite',
                        'radar-sweep': 'radar 4s linear infinite',
                        'float': 'float 5s ease-in-out infinite'
                    },
                    keyframes: {
                        radar: {
                            '0%': { transform: 'rotate(0deg)' },
                            '100%': { transform: 'rotate(360deg)' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-8px)' }
                        }
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Gold Gradient Text */
        .text-gold-gradient {
            background: linear-gradient(135deg, #fef08a 0%, #d4af37 50%, #b45309 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Gold Gradient Border */
        .border-gold-glow {
            border: 1px solid rgba(212, 175, 55, 0.4);
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.15);
        }

        /* Oceanic Backdrop Glow */
        .ocean-glow {
            background: radial-gradient(circle at 50% 20%, rgba(15, 76, 129, 0.45) 0%, rgba(17, 26, 51, 0.95) 75%, #060c1d 100%);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
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

        /* Hide Google Translate Banner Artifacts */
        .goog-te-banner-frame.skiptranslate { display: none !important; }
        body { top: 0px !important; }
        #goog-gt-tt { display: none !important; }
        .goog-tooltip { display: none !important; }
        .goog-tooltip:hover { display: none !important; }
        .goog-text-highlight { background-color: transparent !important; border: none !important; box-shadow: none !important; }
    </style>
</head>
<body class="bg-deepnavy text-slate-100 antialiased selection:bg-cargogold-500 selection:text-deepnavy min-h-screen flex flex-col dark:bg-[#060c1d] dark:text-slate-100 light:bg-slate-50 light:text-slate-900 transition-colors duration-300">

    <!-- Top Navigation Bar -->
    <header class="fixed top-0 left-0 right-0 z-50 backdrop-blur-md bg-deepnavy/80 border-b border-navyink/80 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">

            <!-- Brand Logo (Gold Theme: Globe + Track + Vessel) -->
            <a href="{{ url('/') }}" class="flex items-center gap-3.5 group">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-navyink via-deepnavy to-navyink border border-cargogold-600/40 p-2 shadow-lg shadow-cargogold-600/10 flex items-center justify-center transition-transform duration-300 group-hover:scale-105">
                    <!-- SVG Emblem: Gold Globe, Orbit Track & Cargo Vessel -->
                    <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full filter drop-shadow">
                        <!-- Outer Orbit Track -->
                        <ellipse cx="32" cy="32" rx="27" ry="12" stroke="url(#goldGrad)" stroke-width="1.8" stroke-dasharray="4 2" transform="rotate(-25 32 32)"/>
                        <!-- Globe Latitude/Longitude -->
                        <circle cx="32" cy="32" r="21" stroke="#d4af37" stroke-width="1.2" stroke-opacity="0.35"/>
                        <ellipse cx="32" cy="32" rx="10" ry="21" stroke="#d4af37" stroke-width="1.2" stroke-opacity="0.35"/>
                        <line x1="11" y1="32" x2="53" y2="32" stroke="#d4af37" stroke-width="1.2" stroke-opacity="0.35"/>
                        <!-- Modern Cargo Vessel Prow -->
                        <path d="M18 36L32 46L46 36L44 32H20L18 36Z" fill="url(#goldGrad)" />
                        <!-- Vessel Bridge / Containers -->
                        <rect x="27" y="24" width="10" height="7" rx="1" fill="#fef08a" />
                        <rect x="29" y="19" width="6" height="5" rx="0.5" fill="#facc15" />
                        <line x1="32" y1="14" x2="32" y2="19" stroke="#fef08a" stroke-width="1.5" stroke-linecap="round"/>
                        <!-- Radar Pulse Point -->
                        <circle cx="49" cy="24" r="2.5" fill="#fef08a" class="animate-ping"/>
                        <circle cx="49" cy="24" r="2.5" fill="#fef08a"/>
                        <!-- Definitions -->
                        <defs>
                            <linearGradient id="goldGrad" x1="0" y1="0" x2="64" y2="64" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#fef08a"/>
                                <stop offset="50%" stop-color="#d4af37"/>
                                <stop offset="100%" stop-color="#b45309"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-2xl font-black tracking-tight text-white font-mono">Cargo<span class="text-gold-gradient">Trace</span></span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-cargogold-600/20 text-cargogold-400 border border-cargogold-600/30">v2.4</span>
                    </div>
                    <span class="text-[10px] text-steelblue tracking-widest uppercase font-semibold block -mt-1 font-mono">Global Telematics</span>
                </div>
            </a>

            <!-- Right Controls: Navigation, Theme Toggle, Language Selector, Auth Buttons -->
            <div class="flex items-center gap-3 md:gap-5">

                <!-- Language Selector Dropdown -->
                <div class="relative" id="langDropdownWrapper">
                    <button type="button" id="langDropdownBtn"
                        class="px-3 py-2 rounded-xl bg-navyink/90 border border-steelblue/30 hover:border-cargogold-500/50 text-slate-200 text-xs font-medium flex items-center gap-2 transition shadow-sm">
                        <span id="currentLangFlag">🇮🇩</span>
                        <span id="currentLangLabel" class="hidden sm:inline">Indonesia</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                    </button>

                    <!-- Language Menu -->
                    <div id="langMenu" class="hidden absolute right-0 mt-2 w-56 rounded-2xl bg-navyink border border-steelblue/40 shadow-2xl py-2 z-50 text-xs text-slate-200 backdrop-blur-xl">
                        <div class="px-3 py-1.5 text-[10px] uppercase font-bold tracking-wider text-steelblue border-b border-navyink/80 flex items-center justify-between">
                            <span>Select Language</span>
                            <i class="fa-solid fa-globe text-cargogold-500"></i>
                        </div>
                        <div class="max-h-64 overflow-y-auto py-1 space-y-0.5" id="langOptions">
                            <button onclick="changeLanguage('id', '🇮🇩', 'Bahasa Indonesia')" class="w-full text-left px-3 py-2 hover:bg-oceanblue/40 flex items-center justify-between transition">
                                <span>🇮🇩 Bahasa Indonesia</span>
                                <span class="text-[10px] text-cargogold-400 font-mono">ID</span>
                            </button>
                            <button onclick="changeLanguage('en', '🇬🇧', 'English (US/UK)')" class="w-full text-left px-3 py-2 hover:bg-oceanblue/40 flex items-center justify-between transition">
                                <span>🇬🇧 English</span>
                                <span class="text-[10px] text-slate-400 font-mono">EN</span>
                            </button>
                            <button onclick="changeLanguage('ja', '🇯🇵', '日本語 (Japanese)')" class="w-full text-left px-3 py-2 hover:bg-oceanblue/40 flex items-center justify-between transition">
                                <span>🇯🇵 日本語</span>
                                <span class="text-[10px] text-slate-400 font-mono">JA</span>
                            </button>
                            <button onclick="changeLanguage('zh-CN', '🇨🇳', '中文 (Mandarin)')" class="w-full text-left px-3 py-2 hover:bg-oceanblue/40 flex items-center justify-between transition">
                                <span>🇨🇳 中文 (简体)</span>
                                <span class="text-[10px] text-slate-400 font-mono">ZH</span>
                            </button>
                            <button onclick="changeLanguage('ar', '🇸🇦', 'العربية (Arabic)')" class="w-full text-left px-3 py-2 hover:bg-oceanblue/40 flex items-center justify-between transition">
                                <span>🇸🇦 العربية</span>
                                <span class="text-[10px] text-slate-400 font-mono">AR</span>
                            </button>
                            <button onclick="changeLanguage('es', '🇪🇸', 'Español')" class="w-full text-left px-3 py-2 hover:bg-oceanblue/40 flex items-center justify-between transition">
                                <span>🇪🇸 Español</span>
                                <span class="text-[10px] text-slate-400 font-mono">ES</span>
                            </button>
                            <button onclick="changeLanguage('fr', '🇫🇷', 'Français')" class="w-full text-left px-3 py-2 hover:bg-oceanblue/40 flex items-center justify-between transition">
                                <span>🇫🇷 Français</span>
                                <span class="text-[10px] text-slate-400 font-mono">FR</span>
                            </button>
                            <button onclick="changeLanguage('de', '🇩🇪', 'Deutsch')" class="w-full text-left px-3 py-2 hover:bg-oceanblue/40 flex items-center justify-between transition">
                                <span>🇩🇪 Deutsch</span>
                                <span class="text-[10px] text-slate-400 font-mono">DE</span>
                            </button>
                            <button onclick="changeLanguage('ru', '🇷🇺', 'Русский')" class="w-full text-left px-3 py-2 hover:bg-oceanblue/40 flex items-center justify-between transition">
                                <span>🇷🇺 Русский</span>
                                <span class="text-[10px] text-slate-400 font-mono">RU</span>
                            </button>
                            <button onclick="changeLanguage('pt', '🇵🇹', 'Português')" class="w-full text-left px-3 py-2 hover:bg-oceanblue/40 flex items-center justify-between transition">
                                <span>🇵🇹 Português</span>
                                <span class="text-[10px] text-slate-400 font-mono">PT</span>
                            </button>
                            <button onclick="changeLanguage('ko', '🇰🇷', '한국어')" class="w-full text-left px-3 py-2 hover:bg-oceanblue/40 flex items-center justify-between transition">
                                <span>🇰🇷 한국어</span>
                                <span class="text-[10px] text-slate-400 font-mono">KO</span>
                            </button>
                            <button onclick="changeLanguage('hi', '🇮🇳', 'हिन्दी')" class="w-full text-left px-3 py-2 hover:bg-oceanblue/40 flex items-center justify-between transition">
                                <span>🇮🇳 हिन्दी</span>
                                <span class="text-[10px] text-slate-400 font-mono">HI</span>
                            </button>
                            <button onclick="changeLanguage('nl', '🇳🇱', 'Nederlands')" class="w-full text-left px-3 py-2 hover:bg-oceanblue/40 flex items-center justify-between transition">
                                <span>🇳🇱 Nederlands</span>
                                <span class="text-[10px] text-slate-400 font-mono">NL</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Theme Toggle Button (Light/Dark Mode) -->
                <button type="button" id="themeToggleBtn" title="Toggle Light / Dark Mode"
                    class="w-10 h-10 rounded-xl bg-navyink/90 border border-steelblue/30 hover:border-cargogold-500/50 text-cargogold-400 flex items-center justify-center transition shadow-sm hover:scale-105">
                    <i id="themeIcon" class="fa-solid fa-moon text-sm"></i>
                </button>

                <!-- Auth Navigation Buttons -->
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-cargogold-600 via-cargogold-500 to-amber-600 text-deepnavy text-xs font-extrabold uppercase tracking-wider hover:opacity-95 transition shadow-lg shadow-cargogold-600/20 flex items-center gap-2">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Buka Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-4 py-2.5 rounded-xl bg-navyink/90 hover:bg-oceanblue border border-steelblue/30 hover:border-cargogold-500/40 text-xs font-semibold text-slate-100 transition flex items-center gap-2">
                        <i class="fa-solid fa-right-to-bracket text-cargogold-400"></i>
                        <span data-i18n="nav_login">Masuk</span>
                    </a>
                    <a href="{{ route('register') }}"
                        class="hidden sm:inline-flex px-5 py-2.5 rounded-xl bg-gradient-to-r from-cargogold-600 via-cargogold-500 to-amber-600 text-deepnavy text-xs font-extrabold uppercase tracking-wider hover:brightness-110 transition shadow-lg shadow-cargogold-600/20 items-center gap-1.5">
                        <span data-i18n="nav_register">Daftar Akun</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Hero & Moving Visuals Carousel -->
    <main class="flex-1 pt-20 relative overflow-hidden">

        <!-- Background Moving Visuals Carousel (Slider) -->
        <div class="absolute inset-0 z-0">
            <!-- Slide 1: Global Rotating Globe & Maritime Tracks -->
            <div class="carousel-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-100" id="slide-0">
                <div class="absolute inset-0 bg-gradient-to-t from-deepnavy via-deepnavy/80 to-deepnavy/60 z-10"></div>
                <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-oceanblue/30 via-navyink/80 to-deepnavy z-10"></div>
                <!-- Dynamic SVG Art: Globe & Orbital Route Trajectory -->
                <svg class="w-full h-full object-cover opacity-35 filter blur-[0.5px] scale-105 animate-pulse-slow" viewBox="0 0 1600 900" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Concentric Coordinate Radar Grids -->
                    <circle cx="800" cy="450" r="380" stroke="#3a7ca5" stroke-width="1.2" stroke-dasharray="6 4" stroke-opacity="0.5"/>
                    <circle cx="800" cy="450" r="280" stroke="#0f4c81" stroke-width="1.5" stroke-opacity="0.6"/>
                    <circle cx="800" cy="450" r="180" stroke="#d4af37" stroke-width="1.2" stroke-dasharray="4 6" stroke-opacity="0.4"/>
                    <!-- Latitude lines -->
                    <path d="M420 450C420 300 1180 300 1180 450" stroke="#4682b4" stroke-width="1.2" stroke-opacity="0.4"/>
                    <path d="M420 450C420 600 1180 600 1180 450" stroke="#4682b4" stroke-width="1.2" stroke-opacity="0.4"/>
                    <!-- Global Maritime Route Polyline (Pacific - Indian - Atlantic Corridor) -->
                    <path d="M250 520 Q 500 380 750 480 T 1100 420 T 1400 500" stroke="#facc15" stroke-width="3" stroke-dasharray="10 8" stroke-linecap="round" stroke-opacity="0.8"/>
                    <path d="M200 300 Q 550 550 850 350 T 1350 400" stroke="#38bdf8" stroke-width="2.5" stroke-dasharray="8 6" stroke-linecap="round" stroke-opacity="0.7"/>
                    <!-- Checkpoint Hub Nodes -->
                    <circle cx="750" cy="480" r="8" fill="#d4af37" class="animate-ping"/>
                    <circle cx="750" cy="480" r="6" fill="#facc15"/>
                    <circle cx="1100" cy="420" r="6" fill="#38bdf8"/>
                    <circle cx="500" cy="400" r="6" fill="#10b981"/>
                </svg>
            </div>

            <!-- Slide 2: Mega Container Ship on Deep Ocean at Night -->
            <div class="carousel-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0" id="slide-1">
                <div class="absolute inset-0 bg-gradient-to-t from-deepnavy via-navyink/85 to-deepnavy/70 z-10"></div>
                <!-- Night Ocean Horizon with Glowing Vessel SVG -->
                <svg class="w-full h-full object-cover opacity-30" viewBox="0 0 1600 900" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Ocean Horizon Line -->
                    <line x1="0" y1="580" x2="1600" y2="580" stroke="#3a7ca5" stroke-width="2" stroke-opacity="0.4"/>
                    <!-- Ocean Waves -->
                    <path d="M0 620 C 300 600 500 640 800 620 C 1100 600 1300 640 1600 620" stroke="#0f4c81" stroke-width="2"/>
                    <path d="M0 680 C 400 660 700 700 1100 670 C 1400 650 1500 690 1600 680" stroke="#1c2541" stroke-width="3"/>
                    <!-- Mega Container Vessel Silhouette with Gold Container Stacks -->
                    <path d="M450 560 L 520 460 L 1180 460 L 1250 540 L 1280 570 L 410 570 Z" fill="#111a33" stroke="#3a7ca5" stroke-width="2"/>
                    <!-- Stacks of Shipping Containers -->
                    <rect x="540" y="380" width="120" height="70" fill="#0f4c81" stroke="#3a7ca5" stroke-width="1.5"/>
                    <rect x="670" y="360" width="130" height="90" fill="#d4af37" stroke="#fef08a" stroke-width="1.5" fill-opacity="0.8"/>
                    <rect x="810" y="380" width="140" height="70" fill="#1e3d59" stroke="#4682b4" stroke-width="1.5"/>
                    <rect x="960" y="390" width="110" height="60" fill="#d4af37" stroke="#facc15" stroke-width="1.5" fill-opacity="0.9"/>
                    <!-- Vessel Bridge Tower & Gold Mast Navigation Beacon -->
                    <rect x="1100" y="350" width="60" height="100" fill="#1c2541" stroke="#3a7ca5" stroke-width="1.5"/>
                    <line x1="1130" y1="300" x2="1130" y2="350" stroke="#d4af37" stroke-width="3"/>
                    <circle cx="1130" cy="300" r="5" fill="#facc15" class="animate-ping"/>
                </svg>
            </div>

            <!-- Slide 3: Automated Twilight Harbor Terminal with Cranes -->
            <div class="carousel-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0" id="slide-2">
                <div class="absolute inset-0 bg-gradient-to-t from-deepnavy via-navyink/90 to-deepnavy/60 z-10"></div>
                <!-- Twilight Gantry Cranes & Terminal Yard Silhouette -->
                <svg class="w-full h-full object-cover opacity-35" viewBox="0 0 1600 900" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Gantry Cranes A, B, C -->
                    <path d="M300 620 L 350 250 L 550 250 L 600 620" stroke="#4682b4" stroke-width="4" stroke-linecap="round"/>
                    <line x1="250" y1="280" x2="680" y2="280" stroke="#facc15" stroke-width="5"/>
                    <line x1="450" y1="280" x2="450" y2="400" stroke="#d4af37" stroke-width="2.5" stroke-dasharray="4 4"/>
                    <!-- Crane 2 -->
                    <path d="M900 620 L 940 290 L 1120 290 L 1160 620" stroke="#3a7ca5" stroke-width="4"/>
                    <line x1="860" y1="310" x2="1240" y2="310" stroke="#facc15" stroke-width="5"/>
                    <!-- Ground Terminal Container Blocks -->
                    <rect x="100" y="550" width="160" height="60" fill="#0f4c81" opacity="0.6"/>
                    <rect x="660" y="530" width="200" height="80" fill="#d4af37" opacity="0.5"/>
                    <rect x="1220" y="540" width="180" height="70" fill="#1e3d59" opacity="0.7"/>
                </svg>
            </div>

            <!-- Slide 4: Real-Time Radar HUD & Telemetry Intermodal Grid -->
            <div class="carousel-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0" id="slide-3">
                <div class="absolute inset-0 bg-gradient-to-t from-deepnavy via-deepnavy/80 to-navyink/90 z-10"></div>
                <!-- High-tech HUD Radar & Waypoint Vectors -->
                <svg class="w-full h-full object-cover opacity-35" viewBox="0 0 1600 900" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="800" cy="450" r="350" stroke="#1e3d59" stroke-width="2"/>
                    <circle cx="800" cy="450" r="220" stroke="#3a7ca5" stroke-width="1.5" stroke-dasharray="10 5"/>
                    <circle cx="800" cy="450" r="90" stroke="#d4af37" stroke-width="1.5"/>
                    <line x1="450" y1="450" x2="1150" y2="450" stroke="#1c2541" stroke-width="1.5"/>
                    <line x1="800" y1="100" x2="800" y2="800" stroke="#1c2541" stroke-width="1.5"/>
                    <!-- Rotating Radar Sector Line -->
                    <line x1="800" y1="450" x2="1080" y2="230" stroke="#facc15" stroke-width="3" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <!-- Floating Telemetry Indicator Elements (Cool & Bold) -->
        <div class="absolute top-28 left-8 hidden lg:flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-navyink/60 border border-steelblue/30 text-[11px] font-mono text-cyan-400 backdrop-blur-md z-20">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
            <span>AIS SATELLITE TELEMETRY ONLINE &bull; 46 PORTS LINKED</span>
        </div>

        <div class="absolute top-28 right-8 hidden lg:flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-navyink/60 border border-cargogold-500/30 text-[11px] font-mono text-cargogold-400 backdrop-blur-md z-20">
            <i class="fa-solid fa-shield-halved"></i>
            <span>MARITIME CARGO TRACE PROTOCOL V2.4</span>
        </div>

        <!-- Hero Container -->
        <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-24 flex flex-col items-center text-center">

            <!-- Tagline Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-navyink/80 border border-cargogold-600/40 shadow-xl shadow-cargogold-600/10 mb-8 backdrop-blur-md">
                <span class="text-xs uppercase tracking-widest font-black text-gold-gradient font-mono flex items-center gap-2">
                    <i class="fa-solid fa-compass text-cargogold-400"></i>
                    Cool and Bold &mdash; Strong like the Ocean, timeless like the night.
                </span>
            </div>

            <!-- Main Heading (Welcome to CargoTrace) -->
            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black tracking-tight text-white max-w-5xl leading-[1.1] mb-6">
                <span data-i18n="hero_title_1">Selamat Datang di</span>
                <span class="text-gold-gradient drop-shadow">CargoTrace</span>
                <span class="block text-2xl sm:text-4xl lg:text-5xl font-extrabold text-slate-200 mt-2 font-mono" data-i18n="hero_subtitle">
                    Global Shipment Visibility & Tracking Platform
                </span>
            </h1>

            <!-- Small Description -->
            <p class="max-w-3xl text-sm sm:text-base lg:text-lg text-slate-300 font-normal leading-relaxed mb-10" data-i18n="hero_desc">
                Pantau pergerakan kargo multimodal antarbenua secara real-time dengan akurasi telemetri satelit maritim. Didukung pelacakan kontainer presisi, kalkulasi cuaca rute otomatis, dan playbook resolusi insiden pengiriman berstandar internasional.
            </p>

            <!-- Quick Shipment Tracking Search Bar (Direct Action Widget) -->
            <div class="w-full max-w-2xl bg-navyink/90 border border-cargogold-600/40 rounded-3xl p-3 shadow-2xl shadow-deepnavy/80 backdrop-blur-xl mb-12">
                <form action="{{ route('tracking.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2">
                    <div class="relative flex-1 w-full">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-cargogold-500 text-sm"></i>
                        <input type="text" name="number" id="quickTrackInput"
                            placeholder="Masukkan nomor pengiriman (contoh: SHP-2024-001284)..."
                            value="SHP-2024-001284"
                            class="w-full pl-11 pr-4 py-3.5 bg-deepnavy/80 border border-steelblue/30 rounded-2xl text-xs sm:text-sm text-white placeholder-slate-400 font-mono focus:outline-none focus:border-cargogold-500 focus:ring-2 focus:ring-cargogold-500/20 transition">
                    </div>
                    <button type="submit"
                        class="w-full sm:w-auto px-7 py-3.5 bg-gradient-to-r from-cargogold-600 via-cargogold-500 to-amber-600 hover:brightness-110 text-deepnavy text-xs sm:text-sm font-black uppercase tracking-wider rounded-2xl transition shadow-lg shadow-cargogold-600/30 flex items-center justify-center gap-2 shrink-0">
                        <i class="fa-solid fa-location-crosshairs"></i>
                        <span data-i18n="track_btn">Lacak Kargo</span>
                    </button>
                </form>

                <!-- Demo Tracking Pills -->
                <div class="flex items-center justify-center gap-2 mt-3 pt-2.5 border-t border-steelblue/20 text-[11px] text-slate-400">
                    <span class="text-steelblue font-semibold" data-i18n="demo_numbers">Nomor Demo:</span>
                    <button type="button" onclick="document.getElementById('quickTrackInput').value='SHP-2024-001284'" class="hover:text-cargogold-400 font-mono underline">SHP-2024-001284</button> &bull;
                    <button type="button" onclick="document.getElementById('quickTrackInput').value='SHP-2024-00123'" class="hover:text-cargogold-400 font-mono underline">SHP-2024-00123</button> &bull;
                    <button type="button" onclick="document.getElementById('quickTrackInput').value='SHP-2024-00122'" class="hover:text-cargogold-400 font-mono underline">SHP-2024-00122</button>
                </div>
            </div>

            <!-- Call to Action Buttons -->
            <div class="flex flex-wrap items-center justify-center gap-4 mb-16">
                <a href="{{ route('login') }}"
                    class="px-8 py-4 rounded-2xl bg-gradient-to-r from-oceanblue via-steelblue to-oceanblue hover:scale-105 border border-steelblue/40 text-white font-bold text-sm tracking-wide transition shadow-xl shadow-oceanblue/40 flex items-center gap-3">
                    <i class="fa-solid fa-arrow-right-to-bracket text-cargogold-400 text-base"></i>
                    <span data-i18n="cta_login">Masuk ke Portal Operasional</span>
                </a>

                <a href="#features"
                    class="px-8 py-4 rounded-2xl bg-navyink/80 hover:bg-navyink border border-cargogold-600/30 hover:border-cargogold-500 text-slate-200 font-bold text-sm tracking-wide transition flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-nodes text-cargogold-400"></i>
                    <span data-i18n="cta_explore">Eksplorasi Fitur & Modul</span>
                </a>
            </div>

            <!-- Slider Navigation Indicators / Dots -->
            <div class="flex items-center gap-3 bg-navyink/70 px-4 py-2 rounded-full border border-steelblue/30 backdrop-blur-md">
                <span class="text-[11px] text-steelblue uppercase font-bold mr-1">Telemetry Vista:</span>
                <button onclick="goToSlide(0)" class="slider-dot w-3 h-3 rounded-full bg-cargogold-500 transition-all duration-300" id="dot-0"></button>
                <button onclick="goToSlide(1)" class="slider-dot w-3 h-3 rounded-full bg-slate-600 hover:bg-slate-400 transition-all duration-300" id="dot-1"></button>
                <button onclick="goToSlide(2)" class="slider-dot w-3 h-3 rounded-full bg-slate-600 hover:bg-slate-400 transition-all duration-300" id="dot-2"></button>
                <button onclick="goToSlide(3)" class="slider-dot w-3 h-3 rounded-full bg-slate-600 hover:bg-slate-400 transition-all duration-300" id="dot-3"></button>
            </div>

        </div>

        <!-- Metric Counter Grid (Cool and Bold Theme) -->
        <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-navyink/70 border border-steelblue/30 rounded-3xl p-6 text-center backdrop-blur-md hover:border-cargogold-500/50 transition duration-300">
                    <div class="text-3xl lg:text-4xl font-black text-white font-mono mb-1">128<span class="text-cargogold-400">+</span></div>
                    <div class="text-xs font-bold uppercase tracking-wider text-steelblue font-mono" data-i18n="stat_1">Total Kiriman Global</div>
                </div>
                <div class="bg-navyink/70 border border-steelblue/30 rounded-3xl p-6 text-center backdrop-blur-md hover:border-cargogold-500/50 transition duration-300">
                    <div class="text-3xl lg:text-4xl font-black text-emerald-400 font-mono mb-1">99.4<span class="text-xs font-bold">%</span></div>
                    <div class="text-xs font-bold uppercase tracking-wider text-steelblue font-mono" data-i18n="stat_2">Ketepatan SLA Tiba</div>
                </div>
                <div class="bg-navyink/70 border border-steelblue/30 rounded-3xl p-6 text-center backdrop-blur-md hover:border-cargogold-500/50 transition duration-300">
                    <div class="text-3xl lg:text-4xl font-black text-cyan-400 font-mono mb-1">46<span class="text-cargogold-400">+</span></div>
                    <div class="text-xs font-bold uppercase tracking-wider text-steelblue font-mono" data-i18n="stat_3">Pelabuhan & Hub Rute</div>
                </div>
                <div class="bg-navyink/70 border border-steelblue/30 rounded-3xl p-6 text-center backdrop-blur-md hover:border-cargogold-500/50 transition duration-300">
                    <div class="text-3xl lg:text-4xl font-black text-cargogold-400 font-mono mb-1">24<span class="text-xs">/7</span></div>
                    <div class="text-xs font-bold uppercase tracking-wider text-steelblue font-mono" data-i18n="stat_4">Telemetri Satelit Aktif</div>
                </div>
            </div>
        </div>

        <!-- Features Showcase Section -->
        <section id="features" class="relative z-20 py-20 bg-deepnavy/90 border-t border-navyink">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="text-xs font-mono font-bold uppercase tracking-widest text-cargogold-500 block mb-2" data-i18n="feat_tag">Arsitektur & Kapabilitas</span>
                    <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight" data-i18n="feat_title">Teknologi Pelacakan Multimodal Berstandar Industri</h2>
                    <p class="text-sm text-slate-300 mt-3" data-i18n="feat_desc">Solusi komprehensif untuk visibilitas rantai pasok global dari pelabuhan asal hingga penerima akhir.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <!-- Feature 1: Real-time Telemetry -->
                    <div class="bg-navyink/60 border border-steelblue/30 hover:border-cargogold-500/60 rounded-3xl p-8 transition duration-300 hover:shadow-2xl hover:shadow-oceanblue/20">
                        <div class="w-12 h-12 rounded-2xl bg-oceanblue/50 text-cyan-400 border border-cyan-500/30 flex items-center justify-center text-xl mb-6">
                            <i class="fa-solid fa-satellite-dish"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2" data-i18n="feat_1_title">Telemetri GPS & Leaflet.js</h3>
                        <p class="text-xs text-slate-300 leading-relaxed" data-i18n="feat_1_desc">Pemetaan interaktif OpenStreetMap dengan polylines rute multimodal, checkpoint singgah berkala, serta radar beacon pulsasi pada posisi armada aktif.</p>
                    </div>

                    <!-- Feature 2: Ambient Weather API -->
                    <div class="bg-navyink/60 border border-steelblue/30 hover:border-cargogold-500/60 rounded-3xl p-8 transition duration-300 hover:shadow-2xl hover:shadow-oceanblue/20">
                        <div class="w-12 h-12 rounded-2xl bg-navyink text-cargogold-400 border border-cargogold-500/30 flex items-center justify-center text-xl mb-6">
                            <i class="fa-solid fa-cloud-sun-rain"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2" data-i18n="feat_2_title">Integrasi Cuaca Open-Meteo</h3>
                        <p class="text-xs text-slate-300 leading-relaxed" data-i18n="feat_2_desc">Telemetri meteorologi live langsung dari koordinat kapal laut: memantau temperatur, kecepatan angin, dan peringatan badai secara otomatis.</p>
                    </div>

                    <!-- Feature 3: Operational Exception Playbook -->
                    <div class="bg-navyink/60 border border-steelblue/30 hover:border-cargogold-500/60 rounded-3xl p-8 transition duration-300 hover:shadow-2xl hover:shadow-oceanblue/20">
                        <div class="w-12 h-12 rounded-2xl bg-rose-950/60 text-rose-400 border border-rose-500/30 flex items-center justify-center text-xl mb-6">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2" data-i18n="feat_3_title">Playbook Resolusi Insiden (SOP)</h3>
                        <p class="text-xs text-slate-300 leading-relaxed" data-i18n="feat_3_desc">Panduan operasional otomatis untuk 6 insiden kargo (Customs Hold, Port Congestion, Weather Delay, Kargo Rusak, dsb.) dengan pencatatan audit log lengkap.</p>
                    </div>

                    <!-- Feature 4: Certified Print Waybill -->
                    <div class="bg-navyink/60 border border-steelblue/30 hover:border-cargogold-500/60 rounded-3xl p-8 transition duration-300 hover:shadow-2xl hover:shadow-oceanblue/20">
                        <div class="w-12 h-12 rounded-2xl bg-cargogold-600/20 text-cargogold-400 border border-cargogold-500/30 flex items-center justify-center text-xl mb-6">
                            <i class="fa-solid fa-file-invoice"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2" data-i18n="feat_4_title">Official Bill of Lading / Waybill</h3>
                        <p class="text-xs text-slate-300 leading-relaxed" data-i18n="feat_4_desc">Pencetakan surat jalan / konfirmasi pengiriman resmi standar industri dengan layout thermal/A4 siap cetak, QR code verifikasi, dan barcode pengiriman.</p>
                    </div>

                    <!-- Feature 5: RESTful API & Sanctum -->
                    <div class="bg-navyink/60 border border-steelblue/30 hover:border-cargogold-500/60 rounded-3xl p-8 transition duration-300 hover:shadow-2xl hover:shadow-oceanblue/20">
                        <div class="w-12 h-12 rounded-2xl bg-steelblue/30 text-steelblue border border-steelblue/40 flex items-center justify-center text-xl mb-6">
                            <i class="fa-solid fa-code"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2" data-i18n="feat_5_title">RESTful API & Sanctum Auth</h3>
                        <p class="text-xs text-slate-300 leading-relaxed" data-i18n="feat_5_desc">Integrasi sistem pihak ketiga melalui API endpoint `/api/v1/shipments` terlindungi token bearer Sanctum, rate limiting, dan dokumentasi Postman lengkap.</p>
                    </div>

                    <!-- Feature 6: Role-Based Access Control -->
                    <div class="bg-navyink/60 border border-steelblue/30 hover:border-cargogold-500/60 rounded-3xl p-8 transition duration-300 hover:shadow-2xl hover:shadow-oceanblue/20">
                        <div class="w-12 h-12 rounded-2xl bg-purple-950/60 text-purple-400 border border-purple-500/30 flex items-center justify-center text-xl mb-6">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2" data-i18n="feat_6_title">4 Hak Akses (RBAC) & Audit Log</h3>
                        <p class="text-xs text-slate-300 leading-relaxed" data-i18n="feat_6_desc">Pemisahan wewenang ketat antara Administrator, Logistics Officer, Carrier/Driver, dan Customer dengan histori aktivitas tersimpan di tabel audit.</p>
                    </div>

                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-navyink border-t border-steelblue/20 py-12 text-slate-400 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-deepnavy border border-cargogold-500/40 p-1 flex items-center justify-center">
                    <i class="fa-solid fa-ship text-cargogold-400 text-xs"></i>
                </div>
                <div>
                    <span class="text-sm font-bold text-white font-mono">Cargo<span class="text-gold-gradient">Trace</span></span>
                    <p class="text-[11px] text-slate-400">Cool and Bold &mdash; Strong like the Ocean, timeless like the night.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-6 text-xs text-slate-300">
                <a href="{{ route('tracking.index') }}" class="hover:text-cargogold-400 transition" data-i18n="foot_tracking">Pelacakan Kargo</a>
                <a href="{{ route('login') }}" class="hover:text-cargogold-400 transition" data-i18n="foot_login">Portal Masuk</a>
                <a href="{{ route('register') }}" class="hover:text-cargogold-400 transition" data-i18n="foot_register">Registrasi Akun</a>
                <span class="text-slate-600">|</span>
                <span class="text-slate-400">Tugas Akhir Pemrograman Web 2 (UAS)</span>
            </div>

            <div class="text-center md:text-right">
                <p>&copy; 2026 CargoTrace Platform. All rights reserved.</p>
                <p class="text-[10px] text-steelblue mt-1">Palette: Deep Navy, Navy Ink, Ocean Blue, Steel Blue, Dusk Blue & Gold</p>
            </div>
        </div>
    </footer>

    <!-- Hidden Google Translate Element Container -->
    <div id="google_translate_element" style="display:none;"></div>

    <!-- JavaScript for Slider, Dark/Light Mode, and Multi-Language Translation -->
    <script>
        // 1. CAROUSEL / SLIDER LOGIC
        let currentSlide = 0;
        const totalSlides = 4;
        const slides = [
            document.getElementById('slide-0'),
            document.getElementById('slide-1'),
            document.getElementById('slide-2'),
            document.getElementById('slide-3')
        ];
        const dots = [
            document.getElementById('dot-0'),
            document.getElementById('dot-1'),
            document.getElementById('dot-2'),
            document.getElementById('dot-3')
        ];

        function goToSlide(index) {
            slides[currentSlide].classList.remove('opacity-100');
            slides[currentSlide].classList.add('opacity-0');
            dots[currentSlide].classList.remove('bg-cargogold-500');
            dots[currentSlide].classList.add('bg-slate-600');

            currentSlide = index;

            slides[currentSlide].classList.remove('opacity-0');
            slides[currentSlide].classList.add('opacity-100');
            dots[currentSlide].classList.remove('bg-slate-600');
            dots[currentSlide].classList.add('bg-cargogold-500');
        }

        // Auto-rotate every 5 seconds
        setInterval(() => {
            const next = (currentSlide + 1) % totalSlides;
            goToSlide(next);
        }, 5000);

        // 2. LIGHT & DARK MODE TOGGLE LOGIC
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        const themeIcon = document.getElementById('themeIcon');
        const htmlElement = document.documentElement;

        // Check stored theme preference
        const storedTheme = localStorage.getItem('cargotrace_theme') || 'dark';
        if (storedTheme === 'dark') {
            htmlElement.classList.add('dark');
            htmlElement.classList.remove('light');
            themeIcon.className = 'fa-solid fa-moon text-sm';
        } else {
            htmlElement.classList.remove('dark');
            htmlElement.classList.add('light');
            themeIcon.className = 'fa-solid fa-sun text-sm text-amber-500';
        }

        themeToggleBtn.addEventListener('click', () => {
            if (htmlElement.classList.contains('dark')) {
                htmlElement.classList.remove('dark');
                htmlElement.classList.add('light');
                themeIcon.className = 'fa-solid fa-sun text-sm text-amber-500';
                localStorage.setItem('cargotrace_theme', 'light');
            } else {
                htmlElement.classList.remove('light');
                htmlElement.classList.add('dark');
                themeIcon.className = 'fa-solid fa-moon text-sm text-cargogold-400';
                localStorage.setItem('cargotrace_theme', 'dark');
            }
        });

        // 3. MULTI-LANGUAGE SELECTOR & DICTIONARY
        const langDropdownBtn = document.getElementById('langDropdownBtn');
        const langMenu = document.getElementById('langMenu');
        const currentLangFlag = document.getElementById('currentLangFlag');
        const currentLangLabel = document.getElementById('currentLangLabel');

        langDropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            langMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', () => {
            langMenu.classList.add('hidden');
        });

        const translations = {
            id: {
                nav_login: "Masuk",
                nav_register: "Daftar Akun",
                hero_title_1: "Selamat Datang di",
                hero_subtitle: "Global Shipment Visibility & Tracking Platform",
                hero_desc: "Pantau pergerakan kargo multimodal antarbenua secara real-time dengan akurasi telemetri satelit maritim. Didukung pelacakan kontainer presisi, kalkulasi cuaca rute otomatis, dan playbook resolusi insiden pengiriman berstandar internasional.",
                track_btn: "Lacak Kargo",
                demo_numbers: "Nomor Demo:",
                cta_login: "Masuk ke Portal Operasional",
                cta_explore: "Eksplorasi Fitur & Modul",
                stat_1: "Total Kiriman Global",
                stat_2: "Ketepatan SLA Tiba",
                stat_3: "Pelabuhan & Hub Rute",
                stat_4: "Telemetri Satelit Aktif",
                feat_tag: "Arsitektur & Kapabilitas",
                feat_title: "Teknologi Pelacakan Multimodal Berstandar Industri",
                feat_desc: "Solusi komprehensif untuk visibilitas rantai pasok global dari pelabuhan asal hingga penerima akhir.",
                feat_1_title: "Telemetri GPS & Leaflet.js",
                feat_1_desc: "Pemetaan interaktif OpenStreetMap dengan polylines rute multimodal, checkpoint singgah berkala, serta radar beacon pulsasi pada posisi armada aktif.",
                feat_2_title: "Integrasi Cuaca Open-Meteo",
                feat_2_desc: "Telemetri meteorologi live langsung dari koordinat kapal laut: memantau temperatur, kecepatan angin, dan peringatan badai secara otomatis.",
                feat_3_title: "Playbook Resolusi Insiden (SOP)",
                feat_3_desc: "Panduan operasional otomatis untuk 6 insiden kargo (Customs Hold, Port Congestion, Weather Delay, Kargo Rusak, dsb.) dengan pencatatan audit log lengkap.",
                feat_4_title: "Official Bill of Lading / Waybill",
                feat_4_desc: "Pencetakan surat jalan / konfirmasi pengiriman resmi standar industri dengan layout thermal/A4 siap cetak, QR code verifikasi, dan barcode pengiriman.",
                feat_5_title: "RESTful API & Sanctum Auth",
                feat_5_desc: "Integrasi sistem pihak ketiga melalui API endpoint `/api/v1/shipments` terlindungi token bearer Sanctum, rate limiting, dan dokumentasi Postman lengkap.",
                feat_6_title: "4 Hak Akses (RBAC) & Audit Log",
                feat_6_desc: "Pemisahan wewenang ketat antara Administrator, Logistics Officer, Carrier/Driver, dan Customer dengan histori aktivitas tersimpan di tabel audit.",
                foot_tracking: "Pelacakan Kargo",
                foot_login: "Portal Masuk",
                foot_register: "Registrasi Akun"
            },
            en: {
                nav_login: "Sign In",
                nav_register: "Register",
                hero_title_1: "Welcome to",
                hero_subtitle: "Global Shipment Visibility & Tracking Platform",
                hero_desc: "Track intercontinental multimodal cargo movements in real time with precision maritime satellite telemetry. Powered by container tracking, automated route meteorology, and an international incident resolution playbook.",
                track_btn: "Track Cargo",
                demo_numbers: "Demo Numbers:",
                cta_login: "Sign In to Operational Portal",
                cta_explore: "Explore Capabilities & Modules",
                stat_1: "Global Active Shipments",
                stat_2: "On-Time Arrival SLA",
                stat_3: "Connected Ports & Hubs",
                stat_4: "Live Satellite Telemetry",
                feat_tag: "Architecture & Capabilities",
                feat_title: "Enterprise Multimodal Tracking Technology",
                feat_desc: "End-to-end global supply chain visibility from port of origin to final consignee delivery.",
                feat_1_title: "GPS Telemetry & Leaflet.js",
                feat_1_desc: "Interactive OpenStreetMap geospatial routing with dashed route polylines, checkpoint waypoints, and animated radar pulsating beacons.",
                feat_2_title: "Open-Meteo Weather Telemetry",
                feat_2_desc: "Live meteorological telemetry from vessel coordinates: monitoring temperature, wind velocity, and storm advisories dynamically.",
                feat_3_title: "Operational Incident Playbook",
                feat_3_desc: "Automated resolution SOPs for 6 critical exception types (Customs Hold, Port Congestion, Weather Delays, Damaged Cargo) with full audit trails.",
                feat_4_title: "Official Bill of Lading / Waybill",
                feat_4_desc: "Industry-standard certified waybills with thermal/A4 print styling, verification QR codes, and tracking barcodes.",
                feat_5_title: "RESTful API & Sanctum Auth",
                feat_5_desc: "Seamless third-party integration via token-authenticated `/api/v1/shipments` with rate limiting and complete Postman specifications.",
                feat_6_title: "Role-Based Access & Audit Logs",
                feat_6_desc: "Strict 4-tier RBAC authorization separating Administrators, Logistics Officers, Carriers, and Customers with immutable audit logging.",
                foot_tracking: "Cargo Tracking",
                foot_login: "Sign In",
                foot_register: "Register Account"
            },
            ja: {
                nav_login: "ログイン",
                nav_register: "新規登録",
                hero_title_1: "へようこそ",
                hero_subtitle: "国際貨物可視化・追跡プラットフォーム",
                hero_desc: "高精度な海洋衛星テレメトリにより、国際複合一貫輸送の貨物動態をリアルタイムで追跡します。",
                track_btn: "貨物を追跡",
                demo_numbers: "デモ追跡番号:",
                cta_login: "運用ポータルにログイン",
                cta_explore: "機能を探索する",
                stat_1: "世界運行貨物数",
                stat_2: "定時到着率SLA",
                stat_3: "接続港湾・ハブ数",
                stat_4: "24時間衛星監視",
                feat_tag: "技術構造と機能",
                feat_title: "業界標準の複合一貫輸送追跡技術",
                feat_desc: "出発港から最終荷受人までのサプライチェーン全体を完全に可視化します。",
                feat_1_title: "GPSテレメトリ & Leaflet.js",
                feat_1_desc: "地図上に航路と現在地をレーダーパルスでリアルタイム表示します。",
                feat_2_title: "気象データ連携",
                feat_2_desc: "船舶の緯度経度からリアルタイムの海洋気象情報を自動取得します。",
                feat_3_title: "インシデント解決手順 (SOP)",
                feat_3_desc: "通関保留や港湾混雑などの異常事態に迅速に対応する運用手順。",
                feat_4_title: "公式船荷証券 (B/L)",
                feat_4_desc: "QRコードとバーコード付きの標準的な船荷証券および送り状の印刷。",
                feat_5_title: "REST API & Sanctum認証",
                feat_5_desc: "外部システムとの安全なデータ連携API。",
                feat_6_title: "役割別アクセス制御",
                feat_6_desc: "管理者、運送会社、荷主ごとに権限を厳格に分離。",
                foot_tracking: "貨物追跡",
                foot_login: "ログイン",
                foot_register: "アカウント登録"
            }
        };

        function changeLanguage(langCode, flag, label) {
            currentLangFlag.textContent = flag;
            currentLangLabel.textContent = label;
            localStorage.setItem('cargotrace_lang', langCode);

            // Apply dictionary translation if exists
            const dict = translations[langCode] || translations['en'];
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (dict[key]) {
                    el.textContent = dict[key];
                }
            });

            // Trigger Google Translate engine for full-page multilingual coverage
            triggerGoogleTranslate(langCode);
        }

        // Google Translate Engine integration
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'id',
                autoDisplay: false
            }, 'google_translate_element');
        }

        function triggerGoogleTranslate(lang) {
            const selectEl = document.querySelector('.goog-te-combo');
            if (selectEl) {
                selectEl.value = lang;
                selectEl.dispatchEvent(new Event('change'));
            }
        }
    </script>

    <!-- Load Google Translate official script asynchronously -->
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

</body>
</html>
