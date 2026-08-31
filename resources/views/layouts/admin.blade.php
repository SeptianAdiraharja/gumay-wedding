<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin — Gumay Wedding')</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/icon/gumaywedding.jpg') }}">
    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,400;9..144,500;9..144,600&family=Work+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet"
    >

    {{-- Tailwind Configuration (Disamakan dengan app.blade) --}}
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bgmain: '#E8DCB8',       // Soft Warm Gold / Light Champagne
                        surface: '#F4EFEA',      // Ivory Soft Surface
                        card: '#FFFFFF',         // Pure Warm White
                        gold: '#B58E3C',         // Deep Warm Gold
                        'gold-dark': '#8A6922',  // Espresso Gold / Rich Amber
                        dark: '#262118',         // Dark Espresso
                        ink: '#4A4235',          // Warm Muted Taupe
                    },
                    fontFamily: {
                        display: ['Fraunces', 'serif'],
                        body: ['"Work Sans"', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Work Sans', sans-serif;
            background-color: #E8DCB8;
            color: #262118;
        }

        .font-display {
            font-family: 'Fraunces', serif;
            font-variation-settings: 'opsz' 60;
        }

        a, button {
            transition: all .25s ease-in-out;
        }

        ::selection {
            background: #B58E3C;
            color: #FFFFFF;
        }

        :focus-visible {
            outline: 1px solid #B58E3C;
            outline-offset: 2px;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="font-body bg-bgmain text-dark antialiased">
@auth('web')
<div class="flex min-h-screen bg-bgmain text-dark">
    {{-- =========================
        SIDEBAR
    ========================== --}}
    <aside
        class="w-64 bg-dark text-surface p-6 flex flex-col border-r border-gold/20 shadow-xl"
    >
        {{-- Logo & Brand Header --}}
        <div class="mb-10 flex flex-col items-center text-center">
            <img
                src="{{ asset('images/icon/gumaywedding.jpg') }}"
                alt="Gumay Wedding Logo"
                class="w-16 h-16 rounded-full object-cover border border-gold/40 mb-3 shadow-md"
            >
            <p class="font-display text-2xl text-surface font-semibold">
                Gumay Wedding
            </p>

            <p class="text-xs text-gold uppercase tracking-[0.2em] mt-1 font-medium">
                Admin Panel
            </p>
        </div>

        {{-- Navigation --}}
        <nav class="space-y-1.5 flex-1">
            {{-- Dashboard --}}
            <a
                href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition duration-150
                       {{ request()->routeIs('admin.dashboard')
                           ? 'bg-gold-dark text-white font-medium shadow-sm'
                           : 'text-surface/80 hover:bg-gold-dark/30 hover:text-white' }}"
            >
                <svg class="w-5 h-5 opacity-90" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                </svg>
                <span>Dashboard</span>
            </a>

            {{-- Galeri --}}
            <a
                href="{{ route('admin.galleries.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition duration-150
                       {{ request()->routeIs('admin.galleries.*')
                           ? 'bg-gold-dark text-white font-medium shadow-sm'
                           : 'text-surface/80 hover:bg-gold-dark/30 hover:text-white' }}"
            >
                <svg class="w-5 h-5 opacity-90" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
                <span>Galeri</span>
            </a>

            {{-- Rekomendasi Makeup --}}
            <a
                href="{{ route('admin.recommendations.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition duration-150
                       {{ request()->routeIs('admin.recommendations.*')
                           ? 'bg-gold-dark text-white font-medium shadow-sm'
                           : 'text-surface/80 hover:bg-gold-dark/30 hover:text-white' }}"
            >
                <svg class="w-5 h-5 opacity-90" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                </svg>
                <span>Rekomendasi Makeup</span>
            </a>

            {{-- Riwayat Konsultasi --}}
            <a
                href="{{ route('admin.consultations.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition duration-150
                       {{ request()->routeIs('admin.consultations.*')
                           ? 'bg-gold-dark text-white font-medium shadow-sm'
                           : 'text-surface/80 hover:bg-gold-dark/30 hover:text-white' }}"
            >
                <svg class="w-5 h-5 opacity-90" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a.75.75 0 0 1-1.007-.853l.764-3.15C3.89 15.65 3 13.918 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                </svg>
                <span>Riwayat Konsultasi</span>
            </a>

            {{-- Data Latih --}}
            <a
                href="{{ route('admin.training-dataset.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition duration-150
                       {{ request()->routeIs('admin.training-dataset.*')
                           ? 'bg-gold-dark text-white font-medium shadow-sm'
                           : 'text-surface/80 hover:bg-gold-dark/30 hover:text-white' }}"
            >
                <svg class="w-5 h-5 opacity-90" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                </svg>
                <span>Data Latih (Naive Bayes)</span>
            </a>
        </nav>

        {{-- Logout --}}
        <form
            action="{{ route('admin.logout') }}"
            method="POST"
            class="pt-6 border-t border-gold/20"
        >
            @csrf
            <button
                type="submit"
                class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm
                       text-surface/70 hover:bg-rose-500/20 hover:text-white
                       transition duration-150"
            >
                <svg class="w-5 h-5 opacity-90" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
                <span>Logout</span>
            </button>
        </form>
    </aside>

    {{-- =========================
        MAIN CONTENT
    ========================== --}}
    <div class="flex-1 min-w-0 bg-bgmain">
        {{-- Top Header --}}
        <header
            class="h-20 px-10 flex items-center justify-between
                   border-b border-gold/20
                   bg-dark/95 backdrop-blur-md
                   sticky top-0 z-40"
        >
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-gold">
                    Admin Panel
                </p>

                <h1 class="font-display text-xl text-surface font-semibold">
                    @yield('page-title', 'Gumay Wedding')
                </h1>
            </div>
            <div class="text-right">
                <p class="text-xs text-surface/60">
                    Administrator
                </p>
                <p class="text-sm text-gold font-medium">
                    {{ auth('web')->user()->name ?? 'Admin' }}
                </p>
            </div>
        </header>

        {{-- Content --}}
        <main class="p-10">
            {{-- Success Alert --}}
            @if(session('success'))
                <div class="bg-card border border-gold text-dark text-sm rounded-xl p-4 mb-6 shadow-md">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gold-dark shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            {{-- Page Content --}}
            @yield('content')
        </main>
    </div>
</div>
@else
    {{-- Guest --}}
    @yield('content')
@endauth
</body>
</html>