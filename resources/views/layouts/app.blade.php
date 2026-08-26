<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gumay Wedding — Makeup Artist & Wedding Organizer')</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/icon/gumaywedding.jpg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        plum: '#0F0D0A',     // Dominant Black (80-85%)
                        gold: '#D4AF37',     // Accent Gold (15-20%)
                        rose: '#C9A24C',     // Muted Gold
                        ivory: '#F3EAD8',    // Neutral Light / Beige (5%)
                        surface: '#1B1712',  // Dark Surface
                        ink: '#EDE3CF',      // Soft Beige for text
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
        body { font-family: 'Work Sans', sans-serif; background: #0F0D0A; color: #EDE3CF; }
        .font-display { font-family: 'Fraunces', serif; font-variation-settings: 'opsz' 60; }
        a, button { transition: all .2s ease; }
        ::selection { background: #D4AF37; color: #0F0D0A; }
        :focus-visible { outline: 2px solid #D4AF37; outline-offset: 2px; }
    </style>
</head>
<body class="font-body bg-plum text-ink">

    <nav class="bg-plum/95 backdrop-blur sticky top-0 z-50 border-b border-gold/20">
        <div class="max-w-6xl mx-auto px-6 py-5 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 text-gold hover:text-gold/90">
                <img src="{{ asset('/images/icon/gumaywedding.jpg') }}"
                    alt="Logo Gumay Wedding"
                    class="w-10 h-10 rounded-full object-cover border border-gold/30">
                <span class="font-display text-2xl tracking-tight font-semibold">
                    Gumay Wedding
                </span>
            </a>
            <div class="flex items-center gap-6 text-sm font-medium">
                <a href="{{ route('home') }}#galeri" class="text-ink/80 hover:text-gold">Galeri</a>
                <a href="{{ route('consultation.create') }}"
                class="bg-gold text-plum px-5 py-2.5 rounded-full font-semibold hover:bg-gold/90 shadow-md shadow-gold/10">
                    Cek Jenis Kulit
                </a>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

     {{-- FOOTER --}}
    <footer class="bg-plum text-ink/80 border-t border-gold/20">
        <div class="max-w-6xl mx-auto px-6 py-12 text-sm">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-b border-gold/10 pb-8 mb-8">
                <div>
                    <p class="font-display text-2xl text-gold mb-2 font-semibold">Gumay Wedding</p>
                    <p class="max-w-md text-ink/70 text-xs sm:text-sm leading-relaxed">
                        Jasa tata rias pengantin, wedding organizer, dan dekorasi — Jl. Rancabungur, Kp. Rancabungur, Desa Malakasari, Kec. Baleendah, Kab. Bandung.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 text-xs">
                    <a href="https://wa.me/6281234567890" target="_blank" class="inline-flex items-center gap-2 border border-gold/80 text-gold px-5 py-2.5 rounded-full hover:bg-gold hover:text-plum font-medium transition">
                        <span>WhatsApp Kami</span>
                    </a>
                    <a href="mailto:info@gumaywedding.com" class="inline-flex items-center gap-2 bg-surface text-ivory px-5 py-2.5 rounded-full border border-gold/10 hover:bg-surface/80 transition">
                        <span>Email Kami</span>
                    </a>
                </div>
            </div>
            <p class="text-ink/40 text-xs">&copy; {{ date('Y') }} Gumay Wedding. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
