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
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Jenis Kulit & Kategori Produk (2 Kolom) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wide text-ink/70 mb-2">Jenis Kulit</label>
            <select name="skin_type_id" required
                    class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm px-4 py-2.5 outline-none transition duration-150">
                @foreach($skinTypes as $st)
                    <option value="{{ $st->id }}" {{ old('skin_type_id') == $st->id ? 'selected' : '' }}>
                        {{ $st->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wide text-ink/70 mb-2">Kategori Produk</label>
            <select name="category" required
                    class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm px-4 py-2.5 outline-none transition duration-150">
                @foreach(['primer','foundation','concealer','bedak','blush','eye','lip','setting_spray'] as $cat)
                    <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>
                        {{ str_replace('_', ' ', ucfirst($cat)) }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Judul -->
    <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-ink/70 mb-2">Judul</label>
        <input type="text" name="title" value="{{ old('title') }}" required
               class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm px-4 py-2.5 outline-none transition duration-150 placeholder-ink/30">
    </div>

    <!-- Deskripsi -->
    <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-ink/70 mb-2">Deskripsi</label>
        <textarea name="description" rows="4"
                  class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm p-4 outline-none transition duration-150 resize-none placeholder-ink/30">{{ old('description') }}</textarea>
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