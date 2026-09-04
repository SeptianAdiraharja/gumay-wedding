@extends('layouts.admin')

@section('title', 'Tambah Rekomendasi Makeup')

@section('content')
<div class="mb-8">
    <p class="uppercase tracking-[0.2em] text-xs text-gold/70 font-semibold mb-2">Rekomendasi</p>
    <h1 class="font-display text-3xl text-ivory">Tambah Rekomendasi Makeup</h1>
</div>

<form action="{{ route('admin.recommendations.store') }}" method="POST"
      class="bg-plum border border-gold/10 rounded-2xl p-8 w-full space-y-6 shadow-lg shadow-black/20">
    @csrf

    @if ($errors->any())
        <div class="bg-rose/10 border border-rose/30 text-rose text-sm rounded-xl p-4">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose/10 border border-rose/30 text-rose text-sm rounded-xl p-4">
            {{ session('error') }}
        </div>
    @endif

    @if(isset($existingRecommendations) && $existingRecommendations->isNotEmpty())
        <div class="bg-surface/60 border border-gold/15 rounded-xl p-4 text-xs text-ink/70 space-y-2">
            <p class="font-semibold text-gold flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Kombinasi Rekomendasi yang Sudah Terdaftar ({{ $existingRecommendations->count() }}):
            </p>
            <p class="text-[11px] text-ink/50">Kombinasi berikut sudah memiliki rekomendasi. Jika ingin memperbarui, klik nama kombinasi untuk mengeditnya:</p>
            <div class="flex flex-wrap gap-1.5 pt-1">
                @foreach($existingRecommendations as $ex)
                    <a href="{{ route('admin.recommendations.edit', $ex) }}"
                       title="Klik untuk edit data ini"
                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-plum/90 border border-gold/20 text-ivory hover:border-gold hover:text-gold transition text-[11px]">
                        <span>{{ $ex->full_diagnosis_name }}</span>
                        <svg class="w-3 h-3 text-gold/60" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Jenis Kulit -->
    <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-ink/70 mb-2">Jenis Kulit Dasar</label>
        <select name="skin_type_id" required
                class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm px-4 py-2.5 outline-none transition duration-150 @error('skin_type_id') border-rose/50 @enderror">
            <option value="">Pilih Jenis Kulit</option>
            @foreach($skinTypes as $st)
                <option value="{{ $st->id }}" {{ old('skin_type_id') == $st->id ? 'selected' : '' }}>
                    {{ $st->name }}
                </option>
            @endforeach
        </select>
        @error('skin_type_id')
            <p class="text-rose text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Kondisi Tambahan (Jerawat & Sensitif) -->
    <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-ink/70 mb-2">Kondisi Tambahan</label>
        <div class="flex flex-wrap gap-6">
            <label class="inline-flex items-center gap-2 text-sm text-ivory cursor-pointer">
                <input type="checkbox" name="is_acne" value="1" {{ old('is_acne') ? 'checked' : '' }}
                       class="rounded border-gold/30 bg-surface text-gold focus:ring-gold w-4 h-4">
                Berjerawat
            </label>
            <label class="inline-flex items-center gap-2 text-sm text-ivory cursor-pointer">
                <input type="checkbox" name="is_sensitive" value="1" {{ old('is_sensitive') ? 'checked' : '' }}
                       class="rounded border-gold/30 bg-surface text-gold focus:ring-gold w-4 h-4">
                Sensitif
            </label>
        </div>
        <p class="text-xs text-ink/50 mt-2">Kosongkan keduanya jika rekomendasi ini untuk kondisi kulit tanpa kondisi tambahan.</p>
    </div>

    <!-- Tips Perawatan -->
    <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-ink/70 mb-2">Tips Perawatan</label>
        <textarea name="tips_perawatan" rows="4" required
                  class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm p-4 outline-none transition duration-150 resize-none placeholder-ink/30 @error('tips_perawatan') border-rose/50 @enderror">{{ old('tips_perawatan') }}</textarea>
        @error('tips_perawatan')
            <p class="text-rose text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Makeup Perempuan -->
    <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-ink/70 mb-2">Rekomendasi Makeup Perempuan</label>
        <textarea name="makeup_perempuan" rows="4" required
                  class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm p-4 outline-none transition duration-150 resize-none placeholder-ink/30 @error('makeup_perempuan') border-rose/50 @enderror">{{ old('makeup_perempuan') }}</textarea>
        @error('makeup_perempuan')
            <p class="text-rose text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Makeup Laki-laki -->
    <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-ink/70 mb-2">Rekomendasi Makeup Laki-laki</label>
        <textarea name="makeup_laki_laki" rows="4" required
                  class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm p-4 outline-none transition duration-150 resize-none placeholder-ink/30 @error('makeup_laki_laki') border-rose/50 @enderror">{{ old('makeup_laki_laki') }}</textarea>
        @error('makeup_laki_laki')
            <p class="text-rose text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Tombol Aksi -->
    <div class="pt-2 flex items-center gap-3">
        <button type="submit" class="bg-gold text-plum px-8 py-3 rounded-full text-sm font-medium hover:bg-gold/90 transition duration-150 shadow-md hover:shadow-lg">
            Simpan
        </button>
        <a href="{{ route('admin.recommendations.index') }}" class="px-6 py-3 rounded-full text-sm font-medium text-ink/70 hover:text-gold hover:bg-gold/10 transition duration-150">
            Batal
        </a>
    </div>
</form>
@endsection