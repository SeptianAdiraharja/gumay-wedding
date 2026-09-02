@extends('layouts.admin')

@section('title', 'Edit Data Latih — Naive Bayes')

@section('content')
<!-- Tombol Kembali -->
<a href="{{ route('admin.training-dataset.index') }}"
   class="inline-flex items-center gap-2 text-gold text-sm font-medium hover:text-gold/80 transition duration-150 mb-6">
    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
    </svg>
    <span>Kembali ke Data Latih</span>
</a>

<div class="mb-8">
    <p class="uppercase tracking-[0.2em] text-xs text-gold/70 font-semibold mb-2">Dataset Naive Bayes</p>
    <h1 class="font-display text-3xl text-ivory">Edit Data Latih</h1>
</div>

<!-- Card Form Melebar -->
<form action="{{ route('admin.training-dataset.update', $trainingDataset) }}" method="POST"
      class="bg-plum border border-gold/10 rounded-3xl p-8 md:p-10 w-full space-y-8 shadow-lg shadow-black/20">
    @csrf
    @method('PUT')

    <!-- Alert Error -->
    @if ($errors->any())
        <div class="bg-rose/10 border border-rose/30 text-rose text-xs sm:text-sm rounded-2xl p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-rose shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <div>
                <p class="font-semibold mb-1">Periksa kembali data Anda:</p>
                <ul class="list-disc list-inside space-y-0.5 text-xs text-ivory/80">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Target Label / Kelas -->
    <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-ink/70 mb-2">
            Target Label Jenis Kulit (Kelas) <span class="text-rose">*</span>
        </label>
        <select name="skin_type_id" required
                class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm px-4 py-3 outline-none transition duration-150 cursor-pointer">
            @foreach($skinTypes as $st)
                <option value="{{ $st->id }}" {{ old('skin_type_id', $trainingDataset->skin_type_id) == $st->id ? 'selected' : '' }} class="bg-plum text-ivory">
                    {{ $st->name }}
                </option>
            @endforeach
        </select>
    </div>

    <hr class="border-gold/10">

    <!-- Parameter Fitur (Grid 3 Kolom) -->
    <div>
        <h2 class="text-xs font-semibold uppercase tracking-wide text-ink/70 mb-4">Fitur / Atribut Gejala Kulit</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <!-- Tingkat Minyak -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-ink/60 mb-2">
                    Tingkat Minyak <span class="text-rose">*</span>
                </label>
                <select name="tingkat_minyak" required
                        class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm px-4 py-3 outline-none transition duration-150 cursor-pointer">
                    @foreach(['rendah','sedang','tinggi'] as $opt)
                        <option value="{{ $opt }}" {{ old('tingkat_minyak', $trainingDataset->tingkat_minyak) === $opt ? 'selected' : '' }} class="bg-plum text-ivory">
                            {{ ucfirst($opt) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tingkat Kering -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-ink/60 mb-2">
                    Tingkat Kering <span class="text-rose">*</span>
                </label>
                <select name="tingkat_kering" required
                        class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm px-4 py-3 outline-none transition duration-150 cursor-pointer">
                    @foreach(['rendah','sedang','tinggi'] as $opt)
                        <option value="{{ $opt }}" {{ old('tingkat_kering', $trainingDataset->tingkat_kering) === $opt ? 'selected' : '' }} class="bg-plum text-ivory">
                            {{ ucfirst($opt) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Pori-pori -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-ink/60 mb-2">
                    Ukuran Pori-pori <span class="text-rose">*</span>
                </label>
                <select name="pori_pori" required
                        class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm px-4 py-3 outline-none transition duration-150 cursor-pointer">
                    @foreach(['kecil','sedang','besar'] as $opt)
                        <option value="{{ $opt }}" {{ old('pori_pori', $trainingDataset->pori_pori) === $opt ? 'selected' : '' }} class="bg-plum text-ivory">
                            {{ ucfirst($opt) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Penggunaan Skincare -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-ink/60 mb-2">
                    Penggunaan Skincare <span class="text-rose">*</span>
                </label>
                <select name="penggunaan_skincare" required
                        class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm px-4 py-3 outline-none transition duration-150 cursor-pointer">
                    @foreach(['ya' => 'Rutin', 'tidak' => 'Tidak Rutin', 'dokter' => 'Dari Dokter'] as $opt => $label)
                        <option value="{{ $opt }}" {{ old('penggunaan_skincare', $trainingDataset->penggunaan_skincare) === $opt ? 'selected' : '' }} class="bg-plum text-ivory">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Jerawat -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-ink/60 mb-2">
                    Jerawat <span class="text-rose">*</span>
                </label>
                <select name="jerawat" required
                        class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm px-4 py-3 outline-none transition duration-150 cursor-pointer">
                    @foreach(['ya','tidak'] as $opt)
                        <option value="{{ $opt }}" {{ old('jerawat', $trainingDataset->jerawat) === $opt ? 'selected' : '' }} class="bg-plum text-ivory">
                            {{ ucfirst($opt) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Sensitivitas -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-ink/60 mb-2">
                    Sensitivitas <span class="text-rose">*</span>
                </label>
                <select name="sensitivitas" required
                        class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm px-4 py-3 outline-none transition duration-150 cursor-pointer">
                    @foreach(['rendah','sedang','tinggi'] as $opt)
                        <option value="{{ $opt }}" {{ old('sensitivitas', $trainingDataset->sensitivitas) === $opt ? 'selected' : '' }} class="bg-plum text-ivory">
                            {{ ucfirst($opt) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Tombol Aksi Perbarui & Batal -->
    <div class="flex items-center gap-4 pt-4 border-t border-gold/10">
        <button type="submit"
                class="inline-flex items-center gap-2 bg-gold text-plum px-8 py-3 rounded-full text-sm font-medium hover:bg-gold/90 transition duration-150 shadow-md hover:shadow-lg">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            <span>Perbarui Data Latih</span>
        </button>

        <a href="{{ route('admin.training-dataset.index') }}"
           class="px-6 py-3 rounded-full text-sm font-medium text-ink/60 hover:text-ivory hover:bg-surface/50 transition duration-150">
            Batal
        </a>
    </div>
</form>
@endsection