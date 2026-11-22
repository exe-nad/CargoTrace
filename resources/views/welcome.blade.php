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
                            200: '#fef08a',
                            300: '#fde047',
                            400: '#facc15',
                            500: '#eab308',
                            600: '#d4af37',
                            700: '#b45309',
                            800: '#854d0e'
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace']
                    },
                    animation: {
                        'ken-burns': 'kenburns 20s ease-in-out infinite alternate',
                        'radar-sweep': 'radar 5s linear infinite',
                        'float-slow': 'float 6s ease-in-out infinite',
                        'float-reverse': 'floatRev 7s ease-in-out infinite',
                        'pulse-glow': 'pulseGlow 3s ease-in-out infinite',
                        'spin-slow': 'spin 25s linear infinite',
                        'drift': 'driftAcross 30s linear infinite'
                    },
                    keyframes: {
                        kenburns: {
                            '0%': { transform: 'scale(1) translate(0, 0)' },
                            '100%': { transform: 'scale(1.12) translate(-1%, -1.5%)' }
                        },
                        radar: {
                            '0%': { transform: 'rotate(0deg)' },
                            '100%': { transform: 'rotate(360deg)' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px) rotate(0deg)' },
                            '50%': { transform: 'translateY(-14px) rotate(2deg)' }
                        },
                        floatRev: {
                            '0%, 100%': { transform: 'translateY(0px) rotate(0deg)' },
                            '50%': { transform: 'translateY(12px) rotate(-2deg)' }
                        },
                        pulseGlow: {
                            '0%, 100%': { opacity: '0.4', transform: 'scale(1)' },
                            '50%': { opacity: '0.8', transform: 'scale(1.04)' }
                        },
                        driftAcross: {
                            '0%': { transform: 'translateX(-120px)' },
                            '100%': { transform: 'translateX(calc(100vw + 120px))' }
                        }
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* 3D Liquid Gold Text Gradient */
        .text-gold-3d {
            background: linear-gradient(135deg, #fffbeb 0%, #fef08a 25%, #d4af37 55%, #ca8a04 80%, #78350f 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 4px 18px rgba(212, 175, 55, 0.45));
        }

        /* Luminous Ocean Cyan Gradient */
        .text-cyan-glow {
            background: linear-gradient(135deg, #e0f2fe 0%, #38bdf8 50%, #0284c7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 2px 10px rgba(56, 189, 248, 0.35));
        }

        /* Glassmorphism Cards */
        .glass-panel {
            background: rgba(17, 26, 51, 0.72);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(58, 124, 165, 0.28);
        }

        .glass-panel-gold {
            background: rgba(17, 26, 51, 0.82);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.45);
            box-shadow: 0 10px 40px -10px rgba(6, 12, 29, 0.8), 0 0 25px rgba(212, 175, 55, 0.15);
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
    <header class="fixed top-0 left-0 right-0 z-50 backdrop-blur-xl bg-deepnavy/85 border-b border-navyink/80 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">

            <!-- Brand Logo (Gold Theme: Globe + Track + Vessel) -->
            <a href="{{ url('/') }}" class="flex items-center gap-3.5 group">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-navyink via-deepnavy to-navyink border border-cargogold-500/50 p-2 shadow-xl shadow-cargogold-600/15 flex items-center justify-center transition-transform duration-300 group-hover:scale-105">
                    <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full filter drop-shadow">
                        <ellipse cx="32" cy="32" rx="27" ry="12" stroke="#d4af37" stroke-width="1.8" stroke-dasharray="4 2" transform="rotate(-25 32 32)"/>
                        <circle cx="32" cy="32" r="21" stroke="#d4af37" stroke-width="1.2" stroke-opacity="0.35"/>
                        <ellipse cx="32" cy="32" rx="10" ry="21" stroke="#d4af37" stroke-width="1.2" stroke-opacity="0.35"/>
                        <line x1="11" y1="32" x2="53" y2="32" stroke="#d4af37" stroke-width="1.2" stroke-opacity="0.35"/>
                        <path d="M18 36L32 46L46 36L44 32H20L18 36Z" fill="url(#navGoldGrad)" />
                        <rect x="27" y="24" width="10" height="7" rx="1" fill="#fef08a" />
                        <rect x="29" y="19" width="6" height="5" rx="0.5" fill="#facc15" />
                        <line x1="32" y1="14" x2="32" y2="19" stroke="#fef08a" stroke-width="1.5" stroke-linecap="round"/>
                        <circle cx="49" cy="24" r="2.5" fill="#fef08a" class="animate-ping"/>
                        <circle cx="49" cy="24" r="2.5" fill="#fef08a"/>
                        <defs>
                            <linearGradient id="navGoldGrad" x1="0" y1="0" x2="64" y2="64" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#fef08a"/>
                                <stop offset="50%" stop-color="#d4af37"/>
                                <stop offset="100%" stop-color="#b45309"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-2xl font-black tracking-tight text-white font-mono">Cargo<span class="text-gold-3d">Trace</span></span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-cargogold-600/20 text-cargogold-300 border border-cargogold-500/30">v2.4</span>
                    </div>
                    <span class="text-[10px] text-steelblue tracking-widest uppercase font-semibold block -mt-1 font-mono">Global Telematics</span>
                </div>
            </a>

            <!-- Right Controls: Navigation, Language Selector, Theme Toggle, Auth Buttons -->
            <div class="flex items-center gap-2.5 sm:gap-4">

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
                        class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-cargogold-600 via-cargogold-500 to-amber-600 text-deepnavy text-xs font-black uppercase tracking-wider hover:brightness-110 transition shadow-lg shadow-cargogold-600/25 flex items-center gap-2">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Buka Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-4 py-2.5 rounded-xl bg-navyink/90 hover:bg-oceanblue border border-steelblue/30 hover:border-cargogold-500/40 text-xs font-semibold text-slate-100 transition flex items-center gap-2">
                        <i class="fa-solid fa-right-to-bracket text-cargogold-400"></i>
                        <span data-i18n="nav_login">Masuk</span>
                    </a>
                    <a href="{{ route('register') }}"
                        class="hidden sm:inline-flex px-5 py-2.5 rounded-xl bg-gradient-to-r from-cargogold-600 via-cargogold-500 to-amber-600 text-deepnavy text-xs font-black uppercase tracking-wider hover:brightness-110 transition shadow-lg shadow-cargogold-600/25 items-center gap-1.5">
                        <span data-i18n="nav_register">Daftar Akun</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Hero & Moving Visuals Carousel -->
    <main class="flex-1 pt-20 relative overflow-hidden">

        <!-- ========================================================================= -->
        <!-- BACKGROUND CINEMATIC VISUALS SLIDER WITH REAL LOGISTICS & MARITIME IMAGES -->
        <!-- ========================================================================= -->
        <div class="absolute inset-0 z-0 overflow-hidden">

            <!-- Deep Ocean Multi-layer Tint Gradients (Ensures 100% crisp text readability) -->
            <div class="absolute inset-0 bg-gradient-to-b from-deepnavy/80 via-deepnavy/75 to-deepnavy z-10 pointer-events-none"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-oceanblue/25 via-navyink/70 to-deepnavy z-10 pointer-events-none"></div>

            <!-- Slide 0: Mega Cargo Ship Traversing Deep Ocean at Night (Real Photo + Motion) -->
            <div class="carousel-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-100" id="slide-0">
                <img src="https://images.unsplash.com/photo-1559136555-9303baea8ebd?auto=format&fit=crop&w=2000&q=80"
                    alt="Mega Container Vessel on Deep Ocean"
                    class="w-full h-full object-cover object-center animate-ken-burns filter brightness-75 contrast-125 saturate-110">
            </div>

            <!-- Slide 1: Planet Earth with Global Maritime Route Tracks from Orbit (Real Visual + Motion) -->
            <div class="carousel-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0" id="slide-1">
                <img src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=2000&q=80"
                    alt="Global Telemetry & Orbit Routing"
                    class="w-full h-full object-cover object-center animate-ken-burns filter brightness-70 contrast-130">
            </div>

            <!-- Slide 2: Automated Smart Harbor & Twilight Gantry Cranes (Real Photo + Motion) -->
            <div class="carousel-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0" id="slide-2">
                <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=2000&q=80"
                    alt="Automated Container Port at Dusk"
                    class="w-full h-full object-cover object-center animate-ken-burns filter brightness-75 contrast-120">
            </div>

            <!-- Slide 3: Intermodal Freight Radar & Global Maritime Corridor (Real Photo + Motion) -->
            <div class="carousel-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0" id="slide-3">
                <img src="https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?auto=format&fit=crop&w=2000&q=80"
                    alt="Intermodal Cargo Ship Terminal"
                    class="w-full h-full object-cover object-center animate-ken-burns filter brightness-75 contrast-125">
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- DYNAMIC FLOATING ANIMATED LOGISTICS ICONS (CARGO, GLOBE, RADAR, VESSEL)  -->
        <!-- ========================================================================= -->
        <div class="absolute inset-0 z-10 pointer-events-none overflow-hidden">
            <!-- Floating Cargo Vessel Icon (Upper Left) -->
            <div class="absolute top-28 left-[6%] hidden md:flex items-center gap-3 p-3 rounded-2xl glass-panel animate-float-slow text-cargogold-400 shadow-xl shadow-deepnavy/60">
                <div class="w-10 h-10 rounded-xl bg-cargogold-500/20 border border-cargogold-500/40 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-ship"></i>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-300 block">Active Carrier</span>
                    <span class="text-xs font-mono font-bold text-white">MV Global Pioneer</span>
                </div>
            </div>

            <!-- Floating Rotating Globe (Upper Right) -->
            <div class="absolute top-28 right-[6%] hidden md:flex items-center gap-3 p-3 rounded-2xl glass-panel animate-float-reverse text-cyan-400 shadow-xl shadow-deepnavy/60">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/20 border border-cyan-500/40 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-earth-americas animate-spin-slow"></i>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-300 block">Pacific Telemetry</span>
                    <span class="text-xs font-mono font-bold text-emerald-400 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span> Live AIS Stream
                    </span>
                </div>
            </div>

            <!-- Floating Radar Pulse Node (Middle Left) -->
            <div class="absolute top-1/2 left-[4%] hidden lg:flex items-center gap-2.5 px-3 py-2 rounded-xl glass-panel animate-float-slow text-[11px] font-mono text-cyan-300">
                <i class="fa-solid fa-satellite-dish text-cargogold-400 text-sm"></i>
                <span>Open-Meteo Radar &bull; Dynamic SLA</span>
            </div>

            <!-- Floating Container Dispatch (Middle Right) -->
            <div class="absolute top-1/2 right-[4%] hidden lg:flex items-center gap-2.5 px-3 py-2 rounded-xl glass-panel animate-float-reverse text-[11px] font-mono text-amber-300">
                <i class="fa-solid fa-box-open text-amber-400 text-sm"></i>
                <span>ISO Intermodal &bull; 40ft High-Cube</span>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- HERO CONTENT SECTION (CLEAN VERTICAL FLOW - NO OVERLAPPING ELEMENTS!)     -->
        <!-- ========================================================================= -->
        <div class="relative z-20 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-24 flex flex-col items-center text-center">

            <!-- Responsive Unified Telemetry HUD Ticker (Replaces old overlapping pills!) -->
            <div class="mb-6 flex flex-wrap items-center justify-center gap-2 sm:gap-3 text-xs">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-navyink/90 border border-steelblue/40 shadow-lg text-[11px] font-mono text-cyan-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>AIS TELEMETRY ONLINE</span>
                </div>
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-navyink/90 border border-cargogold-500/40 shadow-lg text-[11px] font-mono text-cargogold-300">
                    <i class="fa-solid fa-anchor text-cargogold-400"></i>
                    <span>46 PORTS LINKED</span>
                </div>
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-navyink/90 border border-purple-500/40 shadow-lg text-[11px] font-mono text-purple-300">
                    <i class="fa-solid fa-shield-halved text-purple-400"></i>
                    <span>MARITIME PROTOCOL V2.4</span>
                </div>
            </div>

            <!-- Cool and Bold Tagline Badge -->
            <div class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-full glass-panel-gold mb-8 max-w-2xl">
                <span class="w-6 h-6 rounded-full bg-cargogold-500/20 text-cargogold-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-compass"></i>
                </span>
                <span class="text-xs sm:text-sm font-extrabold uppercase tracking-widest text-cargogold-300 font-mono">
                    Cool and Bold &mdash; Strong like the Ocean, timeless like the night.
                </span>
            </div>

            <!-- High-Impact Main Heading (Upgraded Typography with 3D Gold Glow) -->
            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black tracking-tight leading-[1.08] mb-6">
                <span class="block text-white drop-shadow-md">
                    <span data-i18n="hero_title_1">Selamat Datang di</span>
                    <span class="text-gold-3d inline-block px-1">CargoTrace</span>
                </span>
                <span class="block text-xl sm:text-3xl lg:text-4xl font-extrabold text-cyan-glow tracking-normal mt-3 font-mono" data-i18n="hero_subtitle">
                    Global Shipment Visibility & Tracking Platform
                </span>
            </h1>

            <!-- Impactful Subtitle / Description -->
            <p class="max-w-2xl text-sm sm:text-base lg:text-lg text-slate-200 font-normal leading-relaxed mb-10 drop-shadow" data-i18n="hero_desc">
                Pantau pergerakan kargo multimodal antarbenua secara real-time dengan akurasi telemetri satelit maritim. Didukung pelacakan kontainer presisi, kalkulasi cuaca rute otomatis, dan playbook resolusi insiden pengiriman berstandar internasional.
            </p>

            <!-- Quick Shipment Tracking Search Bar (Interactive Hero Widget) -->
            <div class="w-full max-w-2xl glass-panel-gold rounded-3xl p-3 shadow-2xl mb-8">
                <form action="{{ route('tracking.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2">
                    <div class="relative flex-1 w-full">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-cargogold-400 text-sm"></i>
                        <input type="text" name="number" id="quickTrackInput"
                            placeholder="Masukkan nomor pengiriman (contoh: SHP-2024-001284)..."
                            value="SHP-2024-001284"
                            class="w-full pl-11 pr-4 py-3.5 bg-deepnavy/90 border border-steelblue/40 rounded-2xl text-xs sm:text-sm text-white placeholder-slate-400 font-mono focus:outline-none focus:border-cargogold-400 focus:ring-2 focus:ring-cargogold-500/30 transition">
                    </div>
                    <button type="submit"
                        class="w-full sm:w-auto px-7 py-3.5 bg-gradient-to-r from-cargogold-600 via-cargogold-500 to-amber-600 hover:brightness-110 text-deepnavy text-xs sm:text-sm font-black uppercase tracking-wider rounded-2xl transition shadow-xl shadow-cargogold-600/30 flex items-center justify-center gap-2 shrink-0">
                        <i class="fa-solid fa-location-crosshairs text-base"></i>
                        <span data-i18n="track_btn">Lacak Kargo</span>
                    </button>
                </form>

                <!-- Demo Resi Pills -->
                <div class="flex flex-wrap items-center justify-center gap-2 mt-3 pt-2.5 border-t border-steelblue/20 text-[11px] text-slate-300">
                    <span class="text-steelblue font-bold" data-i18n="demo_numbers">Nomor Demo:</span>
                    <button type="button" onclick="document.getElementById('quickTrackInput').value='SHP-2024-001284'" class="hover:text-cargogold-400 font-mono underline">SHP-2024-001284</button> &bull;
                    <button type="button" onclick="document.getElementById('quickTrackInput').value='SHP-2024-00123'" class="hover:text-cargogold-400 font-mono underline">SHP-2024-00123</button> &bull;
                    <button type="button" onclick="document.getElementById('quickTrackInput').value='SHP-2024-00122'" class="hover:text-cargogold-400 font-mono underline">SHP-2024-00122</button>
                </div>
            </div>

            <!-- Call to Action Buttons -->
            <div class="flex flex-wrap items-center justify-center gap-4 mb-10">
                <a href="{{ route('login') }}"
                    class="px-8 py-4 rounded-2xl bg-gradient-to-r from-oceanblue via-steelblue to-oceanblue hover:scale-105 border border-steelblue/40 text-white font-bold text-sm tracking-wide transition shadow-xl shadow-oceanblue/40 flex items-center gap-3">
                    <i class="fa-solid fa-arrow-right-to-bracket text-cargogold-400 text-base"></i>
                    <span data-i18n="cta_login">Masuk ke Portal Operasional</span>
                </a>

                <a href="#features"
                    class="px-8 py-4 rounded-2xl bg-navyink/90 hover:bg-navyink border border-cargogold-500/40 hover:border-cargogold-400 text-slate-200 font-bold text-sm tracking-wide transition flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-nodes text-cargogold-400"></i>
                    <span data-i18n="cta_explore">Eksplorasi Fitur & Modul</span>
                </a>
            </div>

            <!-- Interactive Slider Controls & Dot Indicators -->
            <div class="flex items-center gap-4 glass-panel px-5 py-2.5 rounded-full shadow-lg">
                <button type="button" onclick="prevSlide()" class="w-7 h-7 rounded-full bg-deepnavy/80 hover:bg-oceanblue text-slate-300 hover:text-white flex items-center justify-center text-xs transition" title="Slide Sebelumnya">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>

                <div class="flex items-center gap-2.5">
                    <span class="text-[10px] text-steelblue uppercase font-bold mr-1">Visual Vista:</span>
                    <button onclick="goToSlide(0)" class="slider-dot w-3.5 h-3.5 rounded-full bg-cargogold-500 transition-all duration-300 shadow-sm" id="dot-0" title="Slide 1: Ocean Vessel"></button>
                    <button onclick="goToSlide(1)" class="slider-dot w-3.5 h-3.5 rounded-full bg-slate-600 hover:bg-slate-400 transition-all duration-300" id="dot-1" title="Slide 2: Global Earth Track"></button>
                    <button onclick="goToSlide(2)" class="slider-dot w-3.5 h-3.5 rounded-full bg-slate-600 hover:bg-slate-400 transition-all duration-300" id="dot-2" title="Slide 3: Container Harbor"></button>
                    <button onclick="goToSlide(3)" class="slider-dot w-3.5 h-3.5 rounded-full bg-slate-600 hover:bg-slate-400 transition-all duration-300" id="dot-3" title="Slide 4: Freight Radar"></button>
                </div>

                <button type="button" onclick="nextSlide()" class="w-7 h-7 rounded-full bg-deepnavy/80 hover:bg-oceanblue text-slate-300 hover:text-white flex items-center justify-center text-xs transition" title="Slide Berikutnya">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>

        </div>

        <!-- Metric Counter Grid (Cool and Bold Theme) -->
        <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="glass-panel rounded-3xl p-6 text-center hover:border-cargogold-500/60 transition duration-300 shadow-xl">
                    <div class="text-3xl lg:text-4xl font-black text-white font-mono mb-1">128<span class="text-cargogold-400">+</span></div>
                    <div class="text-xs font-bold uppercase tracking-wider text-steelblue font-mono" data-i18n="stat_1">Total Kiriman Global</div>
                </div>
                <div class="glass-panel rounded-3xl p-6 text-center hover:border-cargogold-500/60 transition duration-300 shadow-xl">
                    <div class="text-3xl lg:text-4xl font-black text-emerald-400 font-mono mb-1">99.4<span class="text-xs font-bold">%</span></div>
                    <div class="text-xs font-bold uppercase tracking-wider text-steelblue font-mono" data-i18n="stat_2">Ketepatan SLA Tiba</div>
                </div>
                <div class="glass-panel rounded-3xl p-6 text-center hover:border-cargogold-500/60 transition duration-300 shadow-xl">
                    <div class="text-3xl lg:text-4xl font-black text-cyan-400 font-mono mb-1">46<span class="text-cargogold-400">+</span></div>
                    <div class="text-xs font-bold uppercase tracking-wider text-steelblue font-mono" data-i18n="stat_3">Pelabuhan & Hub Rute</div>
                </div>
                <div class="glass-panel rounded-3xl p-6 text-center hover:border-cargogold-500/60 transition duration-300 shadow-xl">
                    <div class="text-3xl lg:text-4xl font-black text-cargogold-400 font-mono mb-1">24<span class="text-xs">/7</span></div>
                    <div class="text-xs font-bold uppercase tracking-wider text-steelblue font-mono" data-i18n="stat_4">Telemetri Satelit Aktif</div>
                </div>
            </div>
        </div>

        <!-- Features Showcase Section -->
        <section id="features" class="relative z-20 py-20 bg-deepnavy/95 border-t border-navyink">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="text-xs font-mono font-bold uppercase tracking-widest text-cargogold-400 block mb-2" data-i18n="feat_tag">Arsitektur & Kapabilitas</span>
                    <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight" data-i18n="feat_title">Teknologi Pelacakan Multimodal Berstandar Industri</h2>
                    <p class="text-sm text-slate-300 mt-3" data-i18n="feat_desc">Solusi komprehensif untuk visibilitas rantai pasok global dari pelabuhan asal hingga penerima akhir.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <!-- Feature 1: Real-time Telemetry -->
                    <div class="bg-navyink/80 border border-steelblue/30 hover:border-cargogold-500/60 rounded-3xl p-8 transition duration-300 hover:shadow-2xl hover:shadow-oceanblue/20">
                        <div class="w-12 h-12 rounded-2xl bg-oceanblue/50 text-cyan-400 border border-cyan-500/30 flex items-center justify-center text-xl mb-6">
                            <i class="fa-solid fa-satellite-dish"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2" data-i18n="feat_1_title">Telemetri GPS & Leaflet.js</h3>
                        <p class="text-xs text-slate-300 leading-relaxed" data-i18n="feat_1_desc">Pemetaan interaktif OpenStreetMap dengan polylines rute multimodal, checkpoint singgah berkala, serta radar beacon pulsasi pada posisi armada aktif.</p>
                    </div>

                    <!-- Feature 2: Ambient Weather API -->
                    <div class="bg-navyink/80 border border-steelblue/30 hover:border-cargogold-500/60 rounded-3xl p-8 transition duration-300 hover:shadow-2xl hover:shadow-oceanblue/20">
                        <div class="w-12 h-12 rounded-2xl bg-navyink text-cargogold-400 border border-cargogold-500/30 flex items-center justify-center text-xl mb-6">
                            <i class="fa-solid fa-cloud-sun-rain"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2" data-i18n="feat_2_title">Integrasi Cuaca Open-Meteo</h3>
                        <p class="text-xs text-slate-300 leading-relaxed" data-i18n="feat_2_desc">Telemetri meteorologi live langsung dari koordinat kapal laut: memantau temperatur, kecepatan angin, dan peringatan badai secara otomatis.</p>
                    </div>

                    <!-- Feature 3: Operational Exception Playbook -->
                    <div class="bg-navyink/80 border border-steelblue/30 hover:border-cargogold-500/60 rounded-3xl p-8 transition duration-300 hover:shadow-2xl hover:shadow-oceanblue/20">
                        <div class="w-12 h-12 rounded-2xl bg-rose-950/60 text-rose-400 border border-rose-500/30 flex items-center justify-center text-xl mb-6">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2" data-i18n="feat_3_title">Playbook Resolusi Insiden (SOP)</h3>
                        <p class="text-xs text-slate-300 leading-relaxed" data-i18n="feat_3_desc">Panduan operasional otomatis untuk 6 insiden kargo (Customs Hold, Port Congestion, Weather Delay, Kargo Rusak, dsb.) dengan pencatatan audit log lengkap.</p>
                    </div>

                    <!-- Feature 4: Certified Print Waybill -->
                    <div class="bg-navyink/80 border border-steelblue/30 hover:border-cargogold-500/60 rounded-3xl p-8 transition duration-300 hover:shadow-2xl hover:shadow-oceanblue/20">
                        <div class="w-12 h-12 rounded-2xl bg-cargogold-600/20 text-cargogold-400 border border-cargogold-500/30 flex items-center justify-center text-xl mb-6">
                            <i class="fa-solid fa-file-invoice"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2" data-i18n="feat_4_title">Official Bill of Lading / Waybill</h3>
                        <p class="text-xs text-slate-300 leading-relaxed" data-i18n="feat_4_desc">Pencetakan surat jalan / konfirmasi pengiriman resmi standar industri dengan layout thermal/A4 siap cetak, QR code verifikasi, dan barcode pengiriman.</p>
                    </div>

                    <!-- Feature 5: RESTful API & Sanctum -->
                    <div class="bg-navyink/80 border border-steelblue/30 hover:border-cargogold-500/60 rounded-3xl p-8 transition duration-300 hover:shadow-2xl hover:shadow-oceanblue/20">
                        <div class="w-12 h-12 rounded-2xl bg-steelblue/30 text-steelblue border border-steelblue/40 flex items-center justify-center text-xl mb-6">
                            <i class="fa-solid fa-code"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2" data-i18n="feat_5_title">RESTful API & Sanctum Auth</h3>
                        <p class="text-xs text-slate-300 leading-relaxed" data-i18n="feat_5_desc">Integrasi sistem pihak ketiga melalui API endpoint `/api/v1/shipments` terlindungi token bearer Sanctum, rate limiting, dan dokumentasi Postman lengkap.</p>
                    </div>

                    <!-- Feature 6: Role-Based Access Control -->
                    <div class="bg-navyink/80 border border-steelblue/30 hover:border-cargogold-500/60 rounded-3xl p-8 transition duration-300 hover:shadow-2xl hover:shadow-oceanblue/20">
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
                    <span class="text-sm font-bold text-white font-mono">Cargo<span class="text-gold-3d">Trace</span></span>
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
        // 1. CAROUSEL / SLIDER LOGIC WITH AUTO-ROTATION AND MANUAL CONTROLS
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

        function nextSlide() {
            const next = (currentSlide + 1) % totalSlides;
            goToSlide(next);
        }

        function prevSlide() {
            const prev = (currentSlide - 1 + totalSlides) % totalSlides;
            goToSlide(prev);
        }

        // Auto-rotate every 6 seconds
        setInterval(nextSlide, 6000);

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
            }
        };

        function changeLanguage(langCode, flag, label) {
            currentLangFlag.textContent = flag;
            currentLangLabel.textContent = label;
            localStorage.setItem('cargotrace_lang', langCode);

            const dict = translations[langCode] || translations['en'];
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (dict[key]) {
                    el.textContent = dict[key];
                }
            });

            triggerGoogleTranslate(langCode);
        }

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
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

</body>
</html>
