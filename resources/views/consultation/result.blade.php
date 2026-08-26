@extends('layouts.app')

@section('title', 'Hasil Diagnosis Kulit & Rekomendasi Makeup — Gumay Wedding')

@section('content')

@php
    $swatchColors = [
        'normal'      => ['bg' => '#C97B84', 'label' => 'Normal'],
        'dry'         => ['bg' => '#B8935B', 'label' => 'Kering'],
        'oily'        => ['bg' => '#5C8A7A', 'label' => 'Berminyak'],
        'combination' => ['bg' => '#8A6BA8', 'label' => 'Kombinasi'],
    ];
    $activeCode = $consultation->predictedSkinType->code ?? null;
@endphp

<section class="max-w-4xl mx-auto px-6 py-12 md:py-16">

    <!-- Header & Ringkasan Klasifikasi Kulit -->
    <div class="relative bg-gradient-to-b from-plum via-plum/95 to-plum/90 border border-gold/30 rounded-3xl p-8 md:p-12 text-center shadow-2xl mb-12 overflow-hidden">
        {{-- Decorative Ambient Glow --}}
        <div class="absolute -top-24 -left-24 w-60 h-60 bg-gold/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-60 h-60 bg-rose/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Swatch Warna 4 Jenis Kulit -->
        <div class="flex flex-wrap justify-center items-center gap-3 sm:gap-4 mb-8 relative z-10">
            @foreach($swatchColors as $code => $data)
                @php $isActive = ($code === $activeCode); @endphp
                <div class="flex flex-col items-center gap-1.5 group">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full transition-all duration-300 relative {{ $isActive ? 'ring-2 ring-gold ring-offset-2 ring-offset-plum scale-125 shadow-lg shadow-gold/20' : 'opacity-40 hover:opacity-80' }}"
                         style="background: {{ $data['bg'] }}">
                        @if($isActive)
                            <span class="absolute inset-0 rounded-full animate-ping bg-gold/30"></span>
                        @endif
                    </div>
                    <span class="text-[10px] font-medium tracking-wider uppercase {{ $isActive ? 'text-gold font-bold' : 'text-ivory/40' }}">
                        {{ $data['label'] }}
                    </span>
                </div>
            @endforeach
        </div>

        <span class="relative z-10 inline-block px-4 py-1.5 bg-gold/10 border border-gold/20 text-gold rounded-full text-xs font-semibold uppercase tracking-[0.2em] mb-4">
            Hasil Diagnosis Kulit
        </span>

        <!-- Judul Hasil Akhir Gabungan (Jenis Kulit + Kondisi) -->
        <h1 class="relative z-10 font-display text-3xl sm:text-4xl md:text-5xl text-ivory mb-4 tracking-wide">
            {{ $consultation->full_diagnosis_name }}
        </h1>

        <p class="relative z-10 text-ivory/80 max-w-xl mx-auto leading-relaxed text-sm md:text-base font-light mb-6">
            {{ $consultation->predictedSkinType->description ?? 'Tidak ada deskripsi yang tersedia untuk jenis kulit ini.' }}
        </p>

        <!-- Condition Badges -->
        <div class="relative z-10 flex flex-wrap justify-center items-center gap-2.5 pt-2">
            @foreach($consultation->condition_badges as $badge)
                <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-medium border {{ $badge['bg'] }}">
                    {{ $badge['label'] }}
                </span>
            @endforeach
        </div>
    </div>

    <!-- Panduan Persiapan Kulit & Teknik Makeup Berdasarkan Kondisi -->
    @if(count($consultation->preparation_advice) > 0)
        <div class="mb-14">
            <div class="flex items-center gap-3 mb-6 pb-2 border-b border-gold/15">
                <div class="w-9 h-9 rounded-xl bg-gold/10 border border-gold/20 text-gold flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-display text-2xl text-ivory">Catatan Khusus Kondisi Kulit</h2>
                    <p class="text-xs text-ink/50">Penanganan khusus kondisi jerawat, sensitivitas &amp; persiapan pra-rias</p>
                </div>
            </div>

            <div class="grid gap-4">
                @foreach($consultation->preparation_advice as $item)
                    <div class="bg-surface border border-gold/15 rounded-2xl p-5 shadow-xs flex items-start gap-4">
                        <div class="w-2.5 h-2.5 rounded-full bg-gold mt-2 shrink-0 shadow-xs"></div>
                        <div>
                            <h3 class="text-sm font-semibold text-gold mb-1">{{ $item['title'] }}</h3>
                            <p class="text-sm text-ink/75 leading-relaxed font-light">{{ $item['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Rekomendasi Makeup -->
    <div class="mb-14">
        <div class="flex items-center gap-3 mb-6 pb-2 border-b border-gold/15">
            <div class="w-9 h-9 rounded-xl bg-gold/10 border border-gold/20 text-gold flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" />
                </svg>
            </div>
            <div>
                <h2 class="font-display text-2xl text-ivory">Rekomendasi Formulasi Makeup</h2>
                <p class="text-xs text-ink/50">Formulasi &amp; produk pilihan yang disesuaikan untuk hasil riasan tahan lama</p>
            </div>
        </div>

        @if($recommendations->isEmpty())
            <div class="bg-surface/50 border border-dashed border-gold/20 rounded-2xl p-8 text-center">
                <p class="text-ink/50 text-sm">Belum ada data rekomendasi produk untuk jenis kulit ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($recommendations as $rec)
                    <div class="group bg-surface border border-gold/15 hover:border-gold/50 rounded-2xl p-6 shadow-xs hover:shadow-md transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <span class="inline-block px-3 py-1 bg-gold/10 text-gold rounded-full text-[10px] font-semibold uppercase tracking-wider mb-3">
                                {{ str_replace('_', ' ', $rec->category) }}
                            </span>
                            <h3 class="font-display text-lg text-ivory group-hover:text-gold transition-colors duration-200 mb-2">
                                {{ $rec->title }}
                            </h3>
                            <p class="text-sm text-ink/70 leading-relaxed font-light">
                                {{ $rec->description }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Tombol Aksi Navigasi -->
    <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4 text-center">
        <a href="{{ route('consultation.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-plum text-ivory hover:bg-plum/90 border border-gold/30 px-8 py-3.5 rounded-full text-sm font-medium transition duration-200 shadow-md hover:shadow-lg w-full sm:w-auto">
            <svg class="w-4 h-4 text-gold" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            <span>Cek Ulang Jenis Kulit</span>
        </a>
    </div>

</section>
@endsection