<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gumay Wedding — Makeup Artist & Wedding Organizer')</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/icon/gumaywedding.jpg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,400;9..144,500;9..144,600&family=Work+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bgmain: '#E8DCB8',       // 60%: Soft Warm Gold / Light Champagne (Background Utama)
                        surface: '#F4EFEA',      // 30%: Ivory Soft Surface (Kontainer & Section)
                        card: '#FFFFFF',         // 30%: Pure Warm White (Kartu Konten)
                        gold: '#B58E3C',         // 10%: Deep Warm Gold (Aksen Judul & Border)
                        'gold-dark': '#8A6922',    // 10%: Espresso Gold / Rich Amber (Hover States)
                        dark: '#262118',         // Text Dominan: Dark Espresso (Sangat nyaman di background terang)
                        ink: '#4A4235',          // Text Sekunder: Warm Muted Taupe
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
        body { font-family: 'Work Sans', sans-serif; background: #E8DCB8; color: #262118; }
        .font-display { font-family: 'Fraunces', serif; font-variation-settings: 'opsz' 60; }
        a, button { transition: all .25s ease-in-out; }
        ::selection { background: #B58E3C; color: #FFFFFF; }
        :focus-visible { outline: 1px solid #B58E3C; outline-offset: 2px; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-body bg-bgmain text-dark antialiased">

    <nav class="bg-dark/90 backdrop-blur-md sticky top-0 z-50 border-b border-gold/20 shadow-xs">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 text-gold-dark hover:text-gold transition">
                <img src="{{ asset('/images/icon/gumaywedding.jpg') }}"
                    alt="Logo Gumay Wedding"
                    class="w-10 h-10 rounded-full object-cover border border-gold/40">
                <span class="font-display text-2xl tracking-tight font-semibold text-surface">
                    Gumay Wedding
                </span>
            </a>
            <div class="flex items-center gap-6 text-sm font-medium">
                <a href="{{ route('home') }}#galeri" class="text-ink hover:text-gold-dark text-surface transition">Galeri</a>
                <a href="{{ route('consultation.create') }}"
                class="bg-dark text-surface px-5 py-2.5 rounded-full font-medium hover:bg-gold-dark hover:text-white transition shadow-sm">
                    Cek Jenis Kulit
                </a>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-dark text-ink border-t border-gold/20">
        <div class="max-w-6xl mx-auto px-6 py-12 text-sm">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-b border-gold/20 pb-8 mb-8">
                <div>
                    <p class="font-display text-2xl text-surface mb-2 font-semibold">Gumay Wedding</p>
                    <p class="max-w-md text-surface text-xs sm:text-sm leading-relaxed">
                        Jasa tata rias pengantin, wedding organizer, dan dekorasi — Jl. Rancabungur, Kp. Rancabungur, Desa Malakasari, Kec. Baleendah, Kab. Bandung.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 text-xs">
                    <a href="https://wa.me/6281234567890" target="_blank" class="inline-flex items-center gap-2 border border-dark text-surface px-5 py-2.5 rounded-full hover:bg-dark hover:text-bgmain font-medium transition">
                        <span>WhatsApp Kami</span>
                    </a>
                    <a href="mailto:info@gumaywedding.com" class="inline-flex items-center gap-2 bg-card text-dark px-5 py-2.5 rounded-full border border-gold/30 hover:border-gold transition shadow-xs">
                        <span>Email Kami</span>
                    </a>
                </div>
            </div>
            <p class="text-surface text-xs">&copy; {{ date('Y') }} Gumay Wedding. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>