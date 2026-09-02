@extends('layouts.app')

@section('title', 'Cek Jenis Kulit — Gumay Wedding')

@section('content')
<section class="max-w-2xl mx-auto px-6 py-16">

    <div class="text-center mb-10">
        <p class="uppercase tracking-[0.2em] text-xs text-gold font-semibold mb-3">Konsultasi Singkat</p>
        <h1 class="font-display text-4xl text-ivory mb-3">Cek Jenis Kulit Anda</h1>
        <p class="text-ink/60 max-w-md mx-auto">
            Jawab sesuai kondisi kulit wajah Anda saat ini — tidak ada jawaban benar atau salah.
        </p>
    </div>

    @if ($errors->any())
        <div class="bg-gold/10 border border-gold/40 text-ivory text-sm rounded-xl p-4 mb-6">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('consultation.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-surface border border-gold/10 rounded-3xl p-8 md:p-10 space-y-8 shadow-sm">
        @csrf

        <!-- Input Nama & No HP -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gold/70 mb-2">Nama (opsional)</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Anisa"
                       class="w-full bg-plum/40 border border-gold/30 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm px-4 py-2.5 outline-none transition duration-150 placeholder-ink/40">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gold/70 mb-2">No. HP (opsional)</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08123456789"
                       class="w-full bg-plum/40 border border-gold/30 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm px-4 py-2.5 outline-none transition duration-150 placeholder-ink/40">
            </div>
        </div>

        <!-- Input Jenis Kelamin -->
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wide text-gold/70 mb-3">Jenis Kelamin</label>
            <div class="flex flex-wrap gap-3">
                @foreach(['pria' => 'Pria', 'wanita' => 'Wanita'] as $value => $labelText)
                    <label class="flex items-center gap-2 border border-gold/30 bg-plum/40 rounded-full px-5 py-2.5 cursor-pointer hover:border-gold hover:bg-gold/5 transition-all duration-150 has-[:checked]:bg-gold has-[:checked]:border-gold has-[:checked]:text-plum shadow-xs">
                        <input type="radio"
                               name="gender"
                               value="{{ $value }}"
                               class="hidden"
                               {{ old('gender') === $value ? 'checked' : '' }}
                               required>
                        <span class="text-sm font-medium">{{ $labelText }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Input Foto Wajah -->
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wide text-gold/70 mb-3">Foto Wajah</label>

            <label for="photo-input"
                   class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gold/30 bg-plum/40 rounded-2xl px-6 py-8 cursor-pointer hover:border-gold hover:bg-gold/5 transition-all duration-150 text-center">
                <img id="photo-preview" src="" alt="" class="hidden w-24 h-24 object-cover rounded-xl mb-1">
                <span id="photo-placeholder-icon" class="text-2xl">📷</span>
                <span id="photo-label-text" class="text-sm font-medium text-gold/70">
                    Klik untuk unggah foto wajah (JPG/PNG, maks. 2MB)
                </span>
                <input type="file"
                       id="photo-input"
                       name="photo"
                       accept="image/png, image/jpeg, image/jpg"
                       class="hidden"
                       onchange="previewPhoto(event)">
            </label>
        </div>

        <hr class="border-gold/10">

        @php
            $optionLabels = [
                'tingkat_minyak' => [
                    'rendah' => 'Rendah (Kesat / minim minyak)',
                    'sedang' => 'Sedang (Berminyak di T-Zone saja)',
                    'tinggi' => 'Tinggi (Berminyak di seluruh wajah)',
                ],
                'tingkat_kering' => [
                    'rendah' => 'Rendah (Lembap / kenyal)',
                    'sedang' => 'Sedang (Kering di area pipi)',
                    'tinggi' => 'Tinggi (Sering tertarik & mengelupas)',
                ],
                'pori_pori' => [
                    'kecil' => 'Kecil (Halus / hampir tidak terlihat)',
                    'sedang' => 'Sedang (Terlihat di area hidung/T-Zone)',
                    'besar' => 'Besar (Terbuka di sebagian besar wajah)',
                ],
                'penggunaan_skincare' => [
                    'ya' => 'Ya, rutin skincare basic',
                    'tidak' => 'Tidak sama sekali',
                    'dokter' => 'Menggunakan skincare dari dokter (cream siang, malam)',
                ],
                'jerawat' => [
                    'ya' => 'Ya (Ada jerawat aktif / beruntusan)',
                    'tidak' => 'Tidak (Wajah bebas jerawat)',
                ],
                'sensitivitas' => [
                    'rendah' => 'Rendah (Kulit stabil & toleran)',
                    'sedang' => 'Sedang (Sesekali perih/kemerahan)',
                    'tinggi' => 'Tinggi (Sangat mudah memerah & gatal)',
                ],
            ];
        @endphp

        <!-- Pertanyaan -->
        @foreach($questions as $i => $q)
            <div class="p-5 rounded-2xl bg-plum/30 border border-gold/15">
                <div class="flex items-baseline gap-3 mb-4">
                    <span class="font-display text-gold text-lg font-bold">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <label class="text-sm font-semibold text-ivory leading-relaxed">{{ $q->question_text }}</label>
                </div>

                <!-- Pilihan Opsi Radio -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 pl-0 md:pl-8">
                    @foreach($q->options as $option)
                        @php
                            $label = $optionLabels[$q->attribute_key][$option] ?? ucfirst($option);
                        @endphp
                        <label class="flex items-center gap-2.5 border border-gold/25 bg-surface/80 rounded-xl px-4 py-3 cursor-pointer hover:border-gold hover:bg-gold/10 transition-all duration-150 has-[:checked]:bg-gold has-[:checked]:border-gold has-[:checked]:text-plum shadow-xs">
                            <input type="radio"
                                   name="{{ $q->attribute_key }}"
                                   value="{{ $option }}"
                                   class="hidden"
                                   {{ old($q->attribute_key) === $option ? 'checked' : '' }}
                                   required>
                            <span class="text-xs sm:text-sm font-medium leading-tight">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        <button type="submit"
                class="w-full bg-gold text-plum py-3.5 rounded-full font-medium hover:bg-gold/90 transition duration-200 shadow-md hover:shadow-lg">
            Lihat Hasil &amp; Rekomendasi
        </button>
    </form>

</section>

<script>
    function previewPhoto(event) {
        const file = event.target.files[0];
        if (!file) return;

        const preview = document.getElementById('photo-preview');
        const icon = document.getElementById('photo-placeholder-icon');
        const labelText = document.getElementById('photo-label-text');

        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
        icon.classList.add('hidden');
        labelText.textContent = file.name;
    }
</script>
@endsection