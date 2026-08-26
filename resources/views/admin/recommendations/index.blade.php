@extends('layouts.admin')

@section('title', 'Rekomendasi Makeup')

@section('content')
<div class="flex items-center justify-between mb-8">
    <h1 class="font-display text-3xl text-ivory">Rekomendasi Makeup</h1>
    <a href="{{ route('admin.recommendations.create') }}" class="bg-gold text-plum px-5 py-2.5 rounded-full text-sm font-medium hover:bg-gold/90 transition duration-150 shadow-md hover:shadow-lg">+ Tambah</a>
</div>

<div class="bg-plum border border-gold/10 rounded-2xl overflow-hidden shadow-lg shadow-black/20">
    <table class="w-full text-sm">
        <thead class="bg-surface/60 text-left text-gold/70 text-xs uppercase tracking-wide border-b border-gold/10">
            <tr>
                <th class="px-5 py-3.5">Jenis Kulit</th>
                <th class="px-5 py-3.5">Kategori</th>
                <th class="px-5 py-3.5">Judul</th>
                <th class="px-5 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gold/10">
            @forelse($recommendations as $r)
                <tr class="hover:bg-gold/5 transition duration-150">
                    <td class="px-5 py-3.5 text-ink">{{ $r->skinType->name }}</td>
                    <td class="px-5 py-3.5 text-ink capitalize">{{ $r->category }}</td>
                    <td class="px-5 py-3.5 text-ink">{{ $r->title }}</td>
                   <td class="px-5 py-3.5 font-medium">
                        <div class="flex items-center gap-4 text-xs">
                            <!-- Tombol Edit -->
                            <a href="{{ route('admin.recommendations.edit', $r) }}"
                            class="inline-flex items-center gap-1.5 text-gold hover:text-ivory transition duration-150">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                                <span>Edit</span>
                            </a>

                            <!-- Tombol Hapus -->
                            <form action="{{ route('admin.recommendations.destroy', $r) }}" method="POST" class="inline-flex" onsubmit="return confirm('Hapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1.5 text-rose hover:text-rose/70 transition duration-150">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-10 text-ink/40 text-center">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $recommendations->links() }}</div>
@endsection