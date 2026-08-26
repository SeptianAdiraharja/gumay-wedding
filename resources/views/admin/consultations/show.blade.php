@extends('layouts.admin')

@section('title', 'Detail Konsultasi')

@section('content')
<!-- Tombol Kembali -->
<a href="{{ route('admin.consultations.index') }}"
   class="inline-flex items-center gap-2 text-gold text-sm font-medium hover:text-gold/80 transition duration-150 mb-6">
    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
    </svg>
    <span>Kembali ke Riwayat</span>
</a>

    <div class="flex items-center gap-2 mb-4">
        <a href="{{ route('admin.consultations.export-pdf', $consultation->id) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600/20 border border-red-500/30 text-red-400 hover:bg-red-600/30 rounded-xl text-xs font-semibold transition duration-150 shadow-sm">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            <span>Export PDF</span>
        </a>
    </div>

<div class="mb-8">
    <p class="uppercase tracking-[0.2em] text-xs text-gold/70 font-semibold mb-2">Data Pengunjung</p>
    <h1 class="font-display text-3xl text-ivory">Detail Konsultasi</h1>
</div>

<!-- Main Card Container -->
<div class="bg-plum border border-gold/10 rounded-2xl p-8 w-full space-y-8 shadow-lg shadow-black/20">

    <!-- Grid Informasi Pengunjung & Hasil Klasifikasi -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Informasi Pengunjung -->
        <div class="space-y-4">
            <h2 class="text-xs font-semibold uppercase tracking-wide text-ink/70 border-b border-gold/10 pb-2">Informasi Pengunjung</h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between py-1 border-b border-gold/5">
                    <span class="text-ink/60">Nama Pengunjung</span>
                    <span class="font-medium text-ivory">{{ $consultation->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-gold/5">
                    <span class="text-ink/60">No. Handphone</span>
                    <span class="font-medium text-ivory">{{ $consultation->phone ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-gold/5">
                    <span class="text-ink/60">Jenis Kelamin</span>
                    <span class="font-medium text-ivory capitalize">{{ $consultation->gender ?? 'Wanita' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-gold/5">
                    <span class="text-ink/60">Tanggal Konsultasi</span>
                    <span class="font-medium text-ivory">{{ $consultation->created_at->format('d M Y, H:i') }}</span>
                </div>
            </div>

            @if($consultation->photo_path)
                <div class="pt-3">
                    <p class="text-xs text-ink/60 mb-2">Foto Wajah Pengunjung:</p>
                    <img src="{{ asset('storage/' . $consultation->photo_path) }}" alt="Foto Konsultasi" class="w-32 h-32 object-cover rounded-xl border border-gold/20 shadow-md">
                </div>
            @endif
        </div>

        <!-- Hasil Klasifikasi Utama -->
        <div class="space-y-4">
            <h2 class="text-xs font-semibold uppercase tracking-wide text-ink/70 border-b border-gold/10 pb-2">Hasil Diagnosis</h2>
            <div class="bg-surface border border-gold/15 rounded-2xl p-5 flex flex-col justify-center items-start">
                <span class="text-xs uppercase tracking-wider text-gold font-semibold mb-1">Hasil Diagnosis Kulit</span>
                <p class="font-display text-2xl sm:text-3xl text-ivory font-bold mb-3">
                    {{ $consultation->full_diagnosis_name }}
                </p>
                <div class="flex flex-wrap gap-2">
                    @foreach($consultation->condition_badges as $badge)
                        <span class="inline-block px-2.5 py-1 text-xs rounded-full border {{ $badge['bg'] }}">
                            {{ $badge['label'] }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Parameter Jawaban Pengunjung -->
    <div>
        <h2 class="text-xs font-semibold uppercase tracking-wide text-ink/70 border-b border-gold/10 pb-4 mb-4">Parameter Jawaban Kulit (6 Fitur)</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
            <div class="bg-surface border border-gold/10 rounded-xl p-3.5 text-center">
                <p class="text-[11px] uppercase tracking-wide text-ink/60 font-semibold mb-1">Minyak</p>
                <p class="text-sm font-medium text-ivory capitalize">{{ $consultation->tingkat_minyak ?? '-' }}</p>
            </div>
            <div class="bg-surface border border-gold/10 rounded-xl p-3.5 text-center">
                <p class="text-[11px] uppercase tracking-wide text-ink/60 font-semibold mb-1">Kering</p>
                <p class="text-sm font-medium text-ivory capitalize">{{ $consultation->tingkat_kering ?? '-' }}</p>
            </div>
            <div class="bg-surface border border-gold/10 rounded-xl p-3.5 text-center">
                <p class="text-[11px] uppercase tracking-wide text-ink/60 font-semibold mb-1">Pori-Pori</p>
                <p class="text-sm font-medium text-ivory capitalize">{{ $consultation->pori_pori ?? '-' }}</p>
            </div>
            <div class="bg-surface border border-gold/10 rounded-xl p-3.5 text-center">
                <p class="text-[11px] uppercase tracking-wide text-ink/60 font-semibold mb-1">Skincare</p>
                <p class="text-sm font-medium text-ivory capitalize">{{ $consultation->penggunaan_skincare === 'ya' ? 'Rutin' : 'Tidak' }}</p>
            </div>
            <div class="bg-surface border border-gold/10 rounded-xl p-3.5 text-center">
                <p class="text-[11px] uppercase tracking-wide text-ink/60 font-semibold mb-1">Jerawat</p>
                <p class="text-sm font-medium text-ivory capitalize">{{ $consultation->jerawat ?? '-' }}</p>
            </div>
            <div class="bg-surface border border-gold/10 rounded-xl p-3.5 text-center">
                <p class="text-[11px] uppercase tracking-wide text-ink/60 font-semibold mb-1">Sensitivitas</p>
                <p class="text-sm font-medium text-ivory capitalize">{{ $consultation->sensitivitas ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Detail Perhitungan Matematis Algoritma Naive Bayes -->
    <div class="space-y-6 pt-4 border-t border-gold/10">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <p class="text-[10px] uppercase tracking-[0.2em] text-gold/80 font-bold">Transparansi Algoritma</p>
                <h2 class="font-display text-2xl text-ivory">Perhitungan Naive Bayes & Laplace Smoothing</h2>
            </div>
            <div class="text-xs text-ink/60 bg-surface border border-gold/15 px-3.5 py-1.5 rounded-full">
                Total Data Latih Terpakai: <span class="font-semibold text-gold">{{ $calculation['total_training_samples'] ?? 0 }} sampel</span>
            </div>
        </div>

        <!-- 1. Ranking & Distribusi Probabilitas Akhir -->
        <div class="space-y-3">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-ink/70">1. Hasil Distribusi Probabilitas Kelas (P(C|X))</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                @if(isset($calculation['classes']))
                    @foreach($calculation['classes'] as $cls)
                        @php
                            $isWinner = ($consultation->predicted_skin_type_id == $cls['skin_type_id']) || ($cls['code'] == ($consultation->predictedSkinType?->code));
                        @endphp
                        <div class="p-4 rounded-2xl border transition duration-150 {{ $isWinner ? 'bg-gold/10 border-gold/40 shadow-md shadow-gold/5' : 'bg-surface/60 border-gold/10' }}">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-sm text-ivory">{{ $cls['name'] }}</span>
                                    @if($isWinner)
                                        <span class="px-2 py-0.5 text-[10px] uppercase tracking-wider font-bold rounded-full bg-gold text-plum">
                                            Terpilih (Pemenang)
                                        </span>
                                    @endif
                                </div>
                                <span class="font-mono text-sm font-bold {{ $isWinner ? 'text-gold' : 'text-ink/70' }}">
                                    {{ $cls['percentage'] }}%
                                </span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full bg-plum rounded-full h-2 overflow-hidden border border-gold/10">
                                <div class="h-full rounded-full transition-all duration-500 {{ $isWinner ? 'bg-gold' : 'bg-gold/40' }}"
                                     style="width: {{ max($cls['percentage'], 2) }}%"></div>
                            </div>

                            <!-- Detail Singkat Skor -->
                            <div class="flex justify-between items-center text-[11px] text-ink/50 mt-2 font-mono">
                                <span>Prior: {{ $cls['prior_fraction'] }} ({{ round($cls['prior_val'], 4) }})</span>
                                <span>Posterior: {{ sprintf('%.6e', $cls['posterior_val']) }}</span>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- 2. Tabel Matriks Rincian Perhitungan Tiap Parameter -->
        <div class="space-y-3 pt-2">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-ink/70">2. Matriks Likelihood & Probabilitas per Atribut</h3>
            <div class="overflow-x-auto border border-gold/15 rounded-2xl bg-surface/40 shadow-inner">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-surface border-b border-gold/15 text-gold text-[11px] uppercase tracking-wider font-semibold">
                            <th class="py-3.5 px-4">Kelas ($C$)</th>
                            <th class="py-3.5 px-3">Prior $P(C)$</th>
                            <th class="py-3.5 px-3">P(Minyak|C)</th>
                            <th class="py-3.5 px-3">P(Kering|C)</th>
                            <th class="py-3.5 px-3">P(Pori|C)</th>
                            <th class="py-3.5 px-3">P(Skincare|C)</th>
                            <th class="py-3.5 px-3">P(Jerawat|C)</th>
                            <th class="py-3.5 px-3">P(Sensitif|C)</th>
                            <th class="py-3.5 px-3 font-mono">Likelihood $\prod$</th>
                            <th class="py-3.5 px-3 font-mono">Posterior</th>
                            <th class="py-3.5 px-4 text-right font-bold">Hasil Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gold/10 text-ivory">
                        @if(isset($calculation['classes']))
                            @foreach($calculation['classes'] as $cls)
                                @php
                                    $isWinner = ($consultation->predicted_skin_type_id == $cls['skin_type_id']) || ($cls['code'] == ($consultation->predictedSkinType?->code));
                                @endphp
                                <tr class="hover:bg-surface/60 transition {{ $isWinner ? 'bg-gold/5 font-medium' : '' }}">
                                    <td class="py-3.5 px-4 font-semibold text-ivory whitespace-nowrap">
                                        <div class="flex items-center gap-1.5">
                                            @if($isWinner)
                                                <span class="w-2 h-2 rounded-full bg-gold inline-block"></span>
                                            @endif
                                            <span>{{ $cls['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3 font-mono text-[11px] text-ink/75" title="{{ $cls['prior_fraction'] }}">
                                        {{ round($cls['prior_val'], 3) }}
                                    </td>

                                    <!-- 6 Fitur -->
                                    @foreach(['tingkat_minyak', 'tingkat_kering', 'pori_pori', 'penggunaan_skincare', 'jerawat', 'sensitivitas'] as $featKey)
                                        @php
                                            $attr = $cls['attribute_likelihoods'][$featKey] ?? null;
                                        @endphp
                                        <td class="py-3.5 px-3 font-mono text-[11px]" title="{{ $attr['fraction_str'] ?? '' }} = {{ round($attr['prob_val'] ?? 0, 4) }}">
                                            <span class="text-ink/85">{{ round($attr['prob_val'] ?? 0, 3) }}</span>
                                            <span class="block text-[9px] text-ink/40 font-sans leading-tight">({{ $attr['matching_count'] ?? 0 }}+1)/({{ $attr['class_count'] ?? 0 }}+{{ $attr['possible_count'] ?? 0 }})</span>
                                        </td>
                                    @endforeach

                                    <!-- Total Likelihood -->
                                    <td class="py-3.5 px-3 font-mono text-[11px] text-ink/70">
                                        {{ sprintf('%.3e', $cls['total_likelihood']) }}
                                    </td>

                                    <!-- Raw Posterior -->
                                    <td class="py-3.5 px-3 font-mono text-[11px] text-gold font-semibold">
                                        {{ sprintf('%.3e', $cls['posterior_val']) }}
                                    </td>

                                    <!-- Normalized Percentage -->
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-sm whitespace-nowrap {{ $isWinner ? 'text-gold' : 'text-ink/70' }}">
                                        {{ $cls['percentage'] }}%
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. Panduan Rumus Matematika (Untuk Laporan & Sidang) -->
        <div class="bg-surface/50 border border-gold/10 rounded-2xl p-5 text-xs text-ink/75 space-y-2.5">
            <p class="font-semibold text-gold flex items-center gap-2">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
                <span>Keterangan Formula Naive Bayes + Laplace Smoothing:</span>
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1 font-mono text-[11px] text-ink/80">
                <div class="bg-plum/60 border border-gold/10 p-3 rounded-xl">
                    <p class="font-bold text-gold mb-1 font-sans text-xs">Laplace Smoothing per Atribut:</p>
                    <p>P(x<sub>i</sub> | C) = (N<sub>ic</sub> + 1) / (N<sub>c</sub> + |V<sub>i</sub>|)</p>
                    <p class="text-[10px] text-ink/50 mt-1 font-sans">N<sub>ic</sub> = Cocok di data latih, N<sub>c</sub> = Total sampel kelas, |V<sub>i</sub>| = Jumlah variasi opsi nilai.</p>
                </div>
                <div class="bg-plum/60 border border-gold/10 p-3 rounded-xl">
                    <p class="font-bold text-gold mb-1 font-sans text-xs">Normalisasi Posterior Probabilitas:</p>
                    <p>P(C | X) = (P(C) &times; &prod; P(x<sub>i</sub> | C)) / &sum; Posterior</p>
                    <p class="text-[10px] text-ink/50 mt-1 font-sans">Kelas dengan nilai P(C|X) tertinggi dipilih sebagai jenis kulit utama pengunjung.</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection