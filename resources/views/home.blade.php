@extends('layouts.app')

@section('title', 'Gumay Wedding — Makeup Artist & Wedding Organizer')

@section('content')

    @php
        $skinTypes = [
            ['name' => 'Kulit Normal', 'image' => asset('images/normal.jpg')],
            ['name' => 'Kulit Kering', 'image' => asset('images/kering.jpg')],
            ['name' => 'Kulit Berminyak', 'image' => asset('images/berminyak.jpg')],
            ['name' => 'Kulit Kombinasi', 'image' => asset('images/kombinasi.jpeg')],
        ];
    @endphp

    {{-- HERO SECTION --}}
    <section class="max-w-6xl mx-auto px-6 pt-16 pb-20">
        <div class="grid md:grid-cols-2 gap-12 lg:gap-16 items-center">
            <div>
                <span class="inline-block px-3.5 py-1.5 bg-dark text-surface border border-gold/30 rounded-full text-xs font-semibold tracking-[0.18em] uppercase mb-5 shadow-xs">
                    Makeup Artist &amp; Wedding Organizer
                </span>
                <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl text-dark leading-[1.18] mb-6 font-normal">
                    Riasan yang<br><span class="italic text-gold-dark font-light">mengenal</span> kulit Anda.
                </h1>
                <p class="text-ink max-w-md mb-8 leading-relaxed text-sm sm:text-base font-normal">
                    Setiap kulit punya karakter berbeda. Jawab beberapa pertanyaan singkat,
                    dan dapatkan rekomendasi formulasi makeup yang benar-benar sesuai —
                    bukan tebakan umum.
                </p>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                    <a href="{{ route('consultation.create') }}"
                       class="inline-flex items-center justify-center gap-2 bg-dark text-surface px-7 py-3.5 rounded-full font-medium hover:bg-gold-dark hover:text-white transition duration-300 shadow-md text-sm">
                        <span>Mulai Cek Jenis Kulit</span>
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- 4 Gambar Jenis Kulit --}}
            <div class="bg-dark border border-gold/30 rounded-3xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <p class="text-xs uppercase tracking-widest text-surface font-semibold">4 Jenis Kulit yang Kami Kenali</p>
                    <span class="w-2.5 h-2.5 rounded-full bg-surface"></span>
                </div>

                {{-- Grid 2 Baris (2x2) --}}
                <div class="grid grid-cols-2 gap-4">
                    @foreach($skinTypes as $skin)
                        <div class="group relative aspect-[16/9] sm:aspect-[2/1] rounded-2xl overflow-hidden border border-gold/20 hover:border-gold transition-all duration-300 shadow-xs">
                            <img src="{{ $skin['image'] }}"
                                alt="{{ $skin['name'] }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                            <div class="absolute inset-0 bg-gradient-to-t from-dark/80 via-dark/30 to-transparent transition-opacity duration-300"></div>

                            <div class="absolute inset-0 flex items-center justify-center p-4">
                                <span class="text-xs sm:text-sm font-semibold text-white group-hover:text-bgmain transition-colors duration-200 text-center drop-shadow-xs">
                                    {{ $skin['name'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- SERTIFIKAT & PENGHARGAAN --}}
    @php
        $certificates = $galleries->get('sertifikat', collect());

        $allGalleries = collect($galleries)->flatMap(function ($items, $category) {
            return collect($items)->map(function ($item) use ($category) {
                $item->category_slug = str($category)->slug()->toString();
                $item->category_name = str_replace('_', ' ', $category);
                return $item;
            });
        });

        $categoriesList = $allGalleries->pluck('category_name', 'category_slug')->unique();
    @endphp

    @if($certificates->isNotEmpty())
    <section id="sertifikat" class="max-w-6xl mx-auto px-6 py-16 border-t border-gold/25">
        <div class="mb-10 text-center md:text-left">
            <p class="uppercase tracking-[0.18em] text-xs text-surface font-semibold mb-2">Kredibilitas Kami</p>
            <h2 class="font-display text-3xl md:text-4xl text-dark font-normal">Sertifikat &amp; Penghargaan</h2>
            <p class="text-ink text-sm mt-3 max-w-lg mx-auto md:mx-0 font-normal">
                Bukti kompetensi dan pengakuan resmi yang kami terima di bidang tata rias pengantin.
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5">
            @foreach($certificates as $cert)
                <div class="bg-dark border border-gold/25 rounded-2xl overflow-hidden hover:border-gold transition duration-300 shadow-xs">
                    <div class="w-full aspect-[4/3] bg-bgmain/40 overflow-hidden">
                        <img src="{{ asset('storage/' . $cert->file_path) }}"
                            alt="{{ $cert->title }}"
                            class="w-full h-full object-contain p-3">
                    </div>
                    <div class="p-4 border-t border-gold/20 bg-card">
                        <p class="text-sm font-medium text-dark truncate" title="{{ $cert->title }}">
                            {{ $cert->title }}
                        </p>
                        @if($cert->description)
                            <p class="text-xs text-ink mt-1 line-clamp-2 font-normal">{{ $cert->description }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- GALERI SECTION --}}
    <section id="galeri" class="max-w-6xl mx-auto px-6 py-16 border-t border-gold/25"
            x-data="{ selectedCategory: 'all', typeFilter: 'photo' }">

        <div class="mb-8 text-center md:text-left">
            <p class="uppercase tracking-[0.18em] text-xs text-gold-dark font-semibold mb-2">Portofolio Karya</p>
            <h2 class="font-display text-3xl md:text-4xl text-dark font-normal">Galeri Hasil Karya</h2>
        </div>

        <div class="flex items-center gap-6 md:gap-8 overflow-x-auto pb-4 mb-6 text-sm font-medium">
            <button type="button" @click="selectedCategory = 'all'"
                    :class="selectedCategory === 'all' ? 'text-gold-dark border-b-2 border-gold-dark' : 'text-ink hover:text-dark border-b-2 border-transparent'"
                    class="pb-1 transition whitespace-nowrap">
                Semua
            </button>
            @foreach($categoriesList as $slug => $name)
                <button type="button" @click="selectedCategory = '{{ $slug }}'"
                        :class="selectedCategory === '{{ $slug }}' ? 'text-surface border-b-2 border-gold-dark' : 'text-ink hover:text-dark border-b-2 border-transparent'"
                        class="pb-1 transition capitalize whitespace-nowrap">
                    {{ $name }}
                </button>
            @endforeach
        </div>

        <div class="bg-dark border border-gold/30 rounded-3xl p-6 sm:p-8 md:p-10 shadow-sm">

            <div class="flex items-center gap-6 mb-8 text-sm font-medium border-b border-gold/20 pb-3">
                <button type="button" @click="typeFilter = 'all'"
                        :class="typeFilter === 'all' ? 'text-surface underline underline-offset-8 decoration-2 decoration-gold-dark' : 'text-ink hover:text-card'"
                        class="transition">
                    Semua
                </button>
                <button type="button" @click="typeFilter = 'photo'"
                        :class="typeFilter === 'photo' ? 'text-surface underline underline-offset-8 decoration-2 decoration-gold-dark' : 'text-ink hover:tex-card'"
                        class="transition">
                    Foto
                </button>
                <button type="button" @click="typeFilter = 'video'"
                        :class="typeFilter === 'video' ? 'text-surface underline underline-offset-8 decoration-2 decoration-gold-dark' : 'text-ink hover:text-card'"
                        class="transition">
                    Video
                </button>
            </div>

            @if($allGalleries->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5">
                    @foreach($allGalleries as $item)
                        <div x-show="(selectedCategory === 'all' || selectedCategory === '{{ $item->category_slug }}') && (typeFilter === 'all' || typeFilter === '{{ $item->type }}')"
                            x-cloak
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="bg-card border border-gold/20 rounded-2xl overflow-hidden hover:border-gold transition group shadow-xs">

                            <div class="w-full aspect-square bg-bgmain/40 overflow-hidden">
                                @if($item->type === 'photo')
                                    <img src="{{ asset('storage/' . $item->file_path) }}"
                                        alt="{{ $item->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @else
                                    <video controls class="w-full h-full object-cover">
                                        <source src="{{ asset('storage/' . $item->file_path) }}">
                                    </video>
                                @endif
                            </div>

                            <div class="p-3 text-center bg-card">
                                <p class="text-xs font-medium text-dark truncate" title="{{ $item->title }}">
                                    {{ $item->title }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-surface text-sm font-normal">
                    Belum ada media galeri yang diunggah.
                </div>
            @endif
        </div>
    </section>

    {{-- CALL TO ACTION (CTA) SECTION --}}
    <section class="bg-dark border-y border-gold/30 relative overflow-hidden">
        <div class="max-w-3xl mx-auto px-6 py-20 text-center relative z-10">
            <p class="uppercase tracking-[0.18em] text-xs text-surface font-semibold mb-3">Konsultasi Gratis</p>
            <h2 class="font-display text-3xl sm:text-4xl md:text-5xl text-dark mb-5 leading-tight font-normal">
                Belum tahu jenis kulit Anda?
            </h2>
            <p class="text-surface mb-8 max-w-lg mx-auto leading-relaxed text-sm sm:text-base font-normal">
                Prosesnya kurang dari dua menit. Hasilnya langsung berupa rekomendasi
                formulasi riasan makeup yang dipersonalisasi sesuai jenis dan kondisi kulit Anda.
            </p>
            <a href="{{ route('consultation.create') }}"
               class="inline-flex items-center gap-2 bg-dark text-bgmain px-8 py-4 rounded-full font-medium hover:bg-gold-dark hover:text-white transition duration-300 shadow-md text-sm">
                <span>Cek Jenis Kulit Sekarang</span>
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>
    </section>

    {{-- LOKASI & KONTAK SECTION --}}
    <section id="kontak" class="max-w-6xl mx-auto px-6 py-20">
        <div class="mb-12 text-center md:text-left">
            <p class="uppercase tracking-[0.18em] text-xs text-gold-dark font-semibold mb-2">Lokasi &amp; Kontak</p>
            <h2 class="font-display text-3xl md:text-4xl text-dark font-normal">Kunjungi Sanggar Kami</h2>
        </div>

        <div class="grid lg:grid-cols-12 gap-8 items-start">
            <div class="lg:col-span-7 bg-dark p-2 border border-gold/30 rounded-3xl overflow-hidden shadow-sm">
                <div class="w-full h-80 sm:h-96 rounded-2xl overflow-hidden">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15840.852922754668!2d107.616238!3d-6.984021!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e92f1dfb814d%3A0x401e8f1fc28c680!2sMalakasari%2C%20Kec.%20Baleendah%2C%20Kabupaten%20Bandung%2C%20Jawa%20Barat!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid"
                        width="100%"
                        height="100%"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>

            <div class="lg:col-span-5 space-y-5">
                <div class="bg-dark border border-gold/25 rounded-2xl p-6 shadow-xs">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-bgmain text-gold-dark rounded-xl shrink-0 border border-gold/30">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-surface text-sm mb-1">Alamat Sanggar</h4>
                            <p class="text-surface text-xs sm:text-sm leading-relaxed font-normal">
                                Jl. Rancabungur, Kp. Rancabungur, Desa Malakasari, Kec. Baleendah, Kab. Bandung, Jawa Barat.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-dark border border-gold/25 rounded-2xl p-6 shadow-xs">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-bgmain text-gold-dark rounded-xl shrink-0 border border-gold/30">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.826-1.47-5.114-3.758-6.584-6.584l1.293-.97c.362-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-surface text-sm mb-1">WhatsApp / Telepon</h4>
                            <a href="https://wa.me/6281234567890" target="_blank" class="text-surface font-semibold text-xs sm:text-sm hover:underline block mb-1">
                                +62 812-3456-7890
                            </a>
                            <p class="text-surface text-xs font-normal">Hubungi kami untuk pemesanan &amp; konsultasi jadwal.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-dark border border-gold/25 rounded-2xl p-6 shadow-xs">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-bgmain text-gold-dark rounded-xl shrink-0 border border-gold/30">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-surface text-sm mb-1">Email Resmi</h4>
                            <a href="mailto:info@gumaywedding.com" class="text-surface font-semibold text-xs sm:text-sm hover:underline block">
                                info@gumaywedding.com
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection