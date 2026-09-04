@extends('layouts.admin')

@section('title', 'Edit Rekomendasi Makeup')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <!-- Header Section -->
    <div class="flex items-center justify-between pb-4 border-b border-gold/10">
        <div>
            <p class="uppercase tracking-[0.25em] text-xs font-bold text-gold/80 mb-1 flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-gold animate-pulse"></span>
                Manajemen Konten
            </p>
            <h1 class="font-display text-3xl font-bold text-ivory tracking-tight">Edit Rekomendasi Makeup</h1>
        </div>
        <a href="{{ route('admin.recommendations.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold tracking-wider text-ink/70 hover:text-ivory bg-surface/50 hover:bg-surface border border-gold/10 hover:border-gold/30 rounded-xl transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="flex items-start gap-3 bg-rose/10 border border-rose/30 text-rose text-sm rounded-2xl p-4 backdrop-blur-sm">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="space-y-1">
                <p class="font-semibold">Terjadi kesalahan validasi:</p>
                <p class="text-xs opacity-90">{{ $errors->first() }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-start gap-3 bg-rose/10 border border-rose/30 text-rose text-sm rounded-2xl p-4 backdrop-blur-sm">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="space-y-1">
                <p class="font-semibold">Pemberitahuan:</p>
                <p class="text-xs opacity-90">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.recommendations.update', $recommendation) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Card 1: Kriteria Kulit -->
        <div class="bg-plum/80 backdrop-blur-md border border-gold/15 rounded-3xl p-6 md:p-8 space-y-6 shadow-xl shadow-black/40">
            <div class="flex items-center gap-3 pb-4 border-b border-gold/10">
                <div class="p-2.5 rounded-xl bg-gold/10 text-gold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-ivory">Kriteria & Kondisi Kulit</h2>
                    <p class="text-xs text-ink/60">Tentukan kualifikasi tipe dan masalah kulit sasaran</p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Jenis Kulit -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-ink/80">
                        Jenis Kulit Dasar <span class="text-rose">*</span>
                    </label>
                    <div class="relative">
                        <select name="skin_type_id" required
                                class="w-full appearance-none bg-surface/80 border border-gold/20 focus:border-gold focus:ring-2 focus:ring-gold/20 text-ivory rounded-xl text-sm px-4 py-3 pr-10 outline-none transition duration-200">
                            @foreach($skinTypes as $st)
                                <option value="{{ $st->id }}" class="bg-plum text-ivory" {{ old('skin_type_id', $recommendation->skin_type_id) == $st->id ? 'selected' : '' }}>
                                    {{ $st->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gold/70">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Kondisi Tambahan -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-ink/80">Kondisi Tambahan</label>
                    <div class="grid grid-cols-2 gap-3 pt-0.5">
                        <label class="relative flex items-center gap-3 p-3 rounded-xl bg-surface/40 border border-gold/10 hover:border-gold/30 cursor-pointer transition group">
                            <input type="checkbox" name="is_acne" value="1" {{ old('is_acne', $recommendation->is_acne) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gold/30 bg-surface text-gold focus:ring-gold focus:ring-offset-0">
                            <span class="text-sm font-medium text-ivory group-hover:text-gold transition">Berjerawat</span>
                        </label>
                        <label class="relative flex items-center gap-3 p-3 rounded-xl bg-surface/40 border border-gold/10 hover:border-gold/30 cursor-pointer transition group">
                            <input type="checkbox" name="is_sensitive" value="1" {{ old('is_sensitive', $recommendation->is_sensitive) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gold/30 bg-surface text-gold focus:ring-gold focus:ring-offset-0">
                            <span class="text-sm font-medium text-ivory group-hover:text-gold transition">Sensitif</span>
                        </label>
                    </div>
                    <p class="text-[11px] text-ink/50 italic mt-1.5">Kosongkan jika berlaku untuk kulit normal/tanpa kendala khusus.</p>
                </div>
            </div>
        </div>

        <!-- Card 2: Konten & Rekomendasi -->
        <div class="bg-plum/80 backdrop-blur-md border border-gold/15 rounded-3xl p-6 md:p-8 space-y-6 shadow-xl shadow-black/40">
            <div class="flex items-center gap-3 pb-4 border-b border-gold/10">
                <div class="p-2.5 rounded-xl bg-gold/10 text-gold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-ivory">Detail Perawatan & Makeup</h2>
                    <p class="text-xs text-ink/60">Tulis panduan praktis dan rekomendasi produk</p>
                </div>
            </div>

            <!-- Tips Perawatan -->
            <div class="space-y-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-ink/80">
                    Tips Perawatan Kulit <span class="text-rose">*</span>
                </label>
                <textarea name="tips_perawatan" rows="3" required
                          placeholder="Masukkan panduan dasar perawatan kulit..."
                          class="w-full bg-surface/80 border border-gold/20 focus:border-gold focus:ring-2 focus:ring-gold/20 text-ivory rounded-2xl text-sm p-4 outline-none transition duration-200 resize-y placeholder-ink/30 leading-relaxed">{{ old('tips_perawatan', $recommendation->tips_perawatan) }}</textarea>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Makeup Perempuan -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-ink/80 flex items-center gap-2">
                        <span>Rekomendasi Perempuan</span>
                        <span class="text-rose">*</span>
                    </label>
                    <textarea name="makeup_perempuan" rows="5" required
                              placeholder="Produk/teknik makeup untuk perempuan..."
                              class="w-full bg-surface/80 border border-gold/20 focus:border-gold focus:ring-2 focus:ring-gold/20 text-ivory rounded-2xl text-sm p-4 outline-none transition duration-200 resize-y placeholder-ink/30 leading-relaxed">{{ old('makeup_perempuan', $recommendation->makeup_perempuan) }}</textarea>
                </div>

                <!-- Makeup Laki-laki -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-ink/80 flex items-center gap-2">
                        <span>Rekomendasi Laki-laki</span>
                        <span class="text-rose">*</span>
                    </label>
                    <textarea name="makeup_laki_laki" rows="5" required
                              placeholder="Produk/teknik makeup dasar untuk laki-laki..."
                              class="w-full bg-surface/80 border border-gold/20 focus:border-gold focus:ring-2 focus:ring-gold/20 text-ivory rounded-2xl text-sm p-4 outline-none transition duration-200 resize-y placeholder-ink/30 leading-relaxed">{{ old('makeup_laki_laki', $recommendation->makeup_laki_laki) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Sticky Floating Action Bar -->
        <div class="sticky bottom-6 flex items-center justify-between gap-4 bg-plum/90 backdrop-blur-xl border border-gold/20 rounded-2xl p-4 shadow-2xl shadow-black/80">
            <span class="text-xs text-ink/60 hidden sm:inline-block">Pastikan data yang diubah sudah sesuai.</span>
            <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                <a href="{{ route('admin.recommendations.index') }}"
                   class="px-6 py-2.5 rounded-full text-xs font-semibold text-ink/70 hover:text-ivory hover:bg-gold/10 transition duration-150 text-center">
                    Batal
                </a>
                <button type="submit"
                        class="bg-gold hover:bg-gold/90 text-plum font-semibold px-8 py-2.5 rounded-full text-xs transition duration-200 shadow-lg shadow-gold/20 hover:shadow-gold/40 hover:-translate-y-0.5 active:translate-y-0">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection