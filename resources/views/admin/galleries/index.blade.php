@extends('layouts.admin')

@section('title', 'Kelola Galeri')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <p class="uppercase tracking-[0.2em] text-xs text-gold/70 font-semibold mb-2">Portofolio</p>
        <h1 class="font-display text-3xl text-ivory">Kelola Galeri</h1>
    </div>
    <a href="{{ route('admin.galleries.create') }}" class="bg-gold text-plum px-6 py-3 rounded-full text-sm font-medium hover:bg-gold/90 transition duration-150 flex items-center gap-2 shadow-md hover:shadow-lg">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        <span>Tambah Konten</span>
    </a>
</div>

<!-- Grid diubah menjadi maks 3 kolom (lg:grid-cols-3) dengan gap lebih besar (gap-6) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($galleries as $g)
        <div class="bg-plum border border-gold/10 rounded-2xl overflow-hidden shadow-lg shadow-black/20 flex flex-col justify-between hover:border-gold/20 transition duration-200">
            <div>
                <!-- Area Media: Menggunakan aspect-video & object-contain agar media tampil utuh 100% tanpa terpotong -->
                <div class="w-full aspect-video bg-surface/60 flex items-center justify-center overflow-hidden">
                    @if($g->type === 'photo')
                        <img src="{{ asset('storage/' . $g->file_path) }}" class="w-full h-full object-contain">
                    @else
                        <video class="w-full h-full object-contain" controls>
                            <source src="{{ asset('storage/' . $g->file_path) }}">
                        </video>
                    @endif
                </div>

                <!-- Informasi Konten -->
                <div class="p-5">
                    <p class="text-base font-medium text-ink truncate mb-1" title="{{ $g->title }}">{{ $g->title }}</p>
                    <p class="text-xs text-ink/50 capitalize">{{ str_replace('_', ' ', $g->category) }}</p>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="px-5 pb-5 pt-3 flex items-center gap-4 border-t border-gold/10 text-xs font-medium">
                <a href="{{ route('admin.galleries.edit', $g) }}"
                   class="flex items-center gap-1.5 text-gold hover:text-ivory transition duration-150">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                    <span>Edit</span>
                </a>

                <form action="{{ route('admin.galleries.destroy', $g) }}" method="POST" onsubmit="return confirm('Hapus konten ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex items-center gap-1.5 text-rose hover:text-rose/70 transition duration-150">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                        <span>Hapus</span>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-plum border border-gold/10 rounded-2xl p-12 text-center">
            <p class="text-ink/40 text-sm">Belum ada konten galeri yang diunggah.</p>
        </div>
    @endforelse
</div>

<div class="mt-8">{{ $galleries->links() }}</div>
@endsection