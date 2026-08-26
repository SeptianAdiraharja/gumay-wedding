@extends('layouts.admin')

@section('title', 'Tambah Konten Galeri')

@section('content')
<p class="uppercase tracking-[0.2em] text-xs text-gold/70 font-semibold mb-2">Portofolio</p>
<h1 class="font-display text-3xl text-ivory mb-8">Tambah Konten Galeri</h1>

<form action="{{ route('admin.galleries.store') }}" method="POST" enctype="multipart/form-data"
      class="bg-plum border border-gold/10 rounded-2xl p-8 w-full space-y-6 shadow-lg shadow-black/20">
    @csrf

    @if ($errors->any())
        <div class="bg-rose/10 border border-rose/30 text-ink text-sm rounded-xl p-4">{{ $errors->first() }}</div>
    @endif

    <!-- Judul -->
    <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-ink/70 mb-2">Judul</label>
        <input type="text" name="title" value="{{ old('title') }}" required
               class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm px-4 py-2.5 outline-none transition duration-150 placeholder-ink/30">
    </div>

    <!-- Tipe & Kategori (2 Kolom) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wide text-ink/70 mb-2">Tipe</label>
            <select name="type" required
                    class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm px-4 py-2.5 outline-none transition duration-150">
                <option value="photo">Foto</option>
                <option value="video">Video</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wide text-ink/70 mb-2">Kategori</label>
            <select name="category" required
                    class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm px-4 py-2.5 outline-none transition duration-150">
                <option value="makeup">Makeup</option>
                <option value="dekor">Dekor</option>
                <option value="dokumentasi">Dokumentasi</option>
                <option value="busana_pengantin">Busana Pengantin</option>
                <option value="sertifikat">Sertifikat</option>
            </select>
        </div>
    </div>

    <!-- File Input -->
    <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-ink/70 mb-2">File (max 20MB)</label>
        <div class="flex items-center justify-center w-full">
            <input type="file" name="file" required
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
                  class="w-full bg-surface border border-gold/20 focus:border-gold focus:ring-1 focus:ring-gold text-ivory rounded-xl text-sm p-4 outline-none transition duration-150 resize-none placeholder-ink/30">{{ old('description') }}</textarea>
    </div>

    <!-- Tombol Simpan -->
    <div class="pt-2">
        <button type="submit" class="bg-gold text-plum px-8 py-3 rounded-full text-sm font-medium hover:bg-gold/90 transition duration-200 shadow-md hover:shadow-lg">
            Simpan
        </button>
    </div>
</form>
@endsection