@extends('layouts.admin')

@section('title', 'Edit Konten Galeri')

@section('content')
<div class="mb-8">
    <p class="uppercase tracking-[0.2em] text-xs text-gold/70 font-semibold mb-2">Portofolio</p>
    <h1 class="font-display text-3xl text-ivory">Edit Konten Galeri</h1>
</div>

<form action="{{ route('admin.galleries.update', $gallery) }}" method="POST" enctype="multipart/form-data"
      class="bg-plum border border-gold/10 rounded-2xl p-8 w-full space-y-6 shadow-lg shadow-black/20">
    @csrf
    @method('PUT')

    @if ($errors->any())
        <div class="bg-rose/10 border border-rose/30 text-rose text-sm rounded-xl p-4">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Judul -->
    <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-ink/70 mb-2">Judul</label>
        <input type="text" name="title" value="{{ old('title', $gallery->title) }}" required
               class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm px-4 py-2.5 outline-none transition duration-150 placeholder-ink/30">
    </div>

    <!-- Tipe & Kategori (2 Kolom) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wide text-ink/70 mb-2">Tipe</label>
            <select name="type" required
                    class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm px-4 py-2.5 outline-none transition duration-150">
                <option value="photo" {{ old('type', $gallery->type) === 'photo' ? 'selected' : '' }}>Foto</option>
                <option value="video" {{ old('type', $gallery->type) === 'video' ? 'selected' : '' }}>Video</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wide text-ink/70 mb-2">Kategori</label>
            <select name="category" required
                    class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm px-4 py-2.5 outline-none transition duration-150">
                @foreach(['makeup', 'dekor', 'dokumentasi', 'busana_pengantin', 'sertifikat'] as $cat)
                    <option value="{{ $cat }}" {{ old('category', $gallery->category) === $cat ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_',' ',$cat)) }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- File Input + Preview Media Saat Ini -->
    <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-ink/70 mb-2">Ganti File (Opsional)</label>

        {{-- Preview File Lama --}}
        @if ($gallery->file_path)
            <div class="mb-3 p-3 bg-surface border border-gold/15 rounded-xl flex items-center gap-4">
                <div class="w-16 h-16 rounded-lg overflow-hidden bg-plum flex-shrink-0 flex items-center justify-center border border-gold/10">
                    @if ($gallery->type === 'photo')
                        <img src="{{ asset('storage/' . $gallery->file_path) }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-xs text-gold font-semibold uppercase">Video</span>
                    @endif
                </div>
                <div class="text-xs text-ink/70">
                    <p class="font-semibold text-ivory mb-0.5">Media Saat Ini</p>
                    <p class="truncate max-w-xs text-ink/50">{{ basename($gallery->file_path) }}</p>
                </div>
            </div>
        @endif

        {{-- Input File Baru --}}
        <div class="flex items-center justify-center w-full">
            <input type="file" name="file"
                   class="w-full text-sm text-ink/80 border border-gold/20 rounded-xl bg-surface p-2.5
                          file:mr-4 file:py-1.5 file:px-4 file:rounded-full file:border-0
                          file:text-xs file:font-medium file:bg-gold/10 file:text-gold
                          hover:file:bg-gold/20 transition cursor-pointer">
        </div>
    </div>

    <!-- Deskripsi -->
    <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-ink/70 mb-2">Deskripsi</label>
        <textarea name="description" rows="4"
                  class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm p-4 outline-none transition duration-150 resize-none placeholder-ink/30">{{ old('description', $gallery->description) }}</textarea>
    </div>

    <!-- Tombol Aksi -->
    <div class="pt-2 flex items-center gap-3">
        <button type="submit" class="bg-gold text-plum px-8 py-3 rounded-full text-sm font-medium hover:bg-gold/90 transition duration-150 shadow-md hover:shadow-lg">
            Perbarui
        </button>
        <a href="{{ route('admin.galleries.index') }}" class="px-6 py-3 rounded-full text-sm font-medium text-ink/70 hover:text-gold hover:bg-gold/10 transition duration-150">
            Batal
        </a>
    </div>
</form>
@endsection