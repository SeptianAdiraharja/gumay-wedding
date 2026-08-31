@extends('layouts.admin')

@section('title', 'Rekomendasi Makeup')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gold/20">
        <div>
            <p class="uppercase tracking-[0.25em] text-xs font-bold text-gold-dark mb-1 flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-gold-dark animate-pulse"></span>
                Manajemen Konten
            </p>
            <h1 class="font-display text-3xl font-bold text-dark tracking-tight">Rekomendasi Makeup</h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.recommendations.import') }}"
               class="inline-flex items-center gap-2 border border-gold/40 text-gold-dark hover:bg-gold/15 px-5 py-2.5 rounded-xl text-xs font-semibold tracking-wider uppercase transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Import Excel
            </a>
            <a href="{{ route('admin.recommendations.create') }}"
               class="inline-flex items-center gap-2 bg-gold-dark hover:bg-gold text-bgmain px-5 py-2.5 rounded-xl text-xs font-bold tracking-wider uppercase transition duration-200 shadow-md hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah
            </a>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-surface border border-gold/30 rounded-3xl overflow-hidden shadow-md">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-card/70 text-gold-dark text-[11px] uppercase tracking-widest font-bold border-b border-gold/20">
                    <tr>
                        <th scope="col" class="px-6 py-4">Jenis Kulit</th>
                        <th scope="col" class="px-6 py-4">Kondisi Tambahan</th>
                        <th scope="col" class="px-6 py-4">Tips Perawatan</th>
                        <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gold/15">
                    @forelse($recommendations as $r)
                        <tr class="hover:bg-gold/10 transition duration-150 group">
                            <!-- Jenis Kulit -->
                            <td class="px-6 py-4 font-semibold text-dark whitespace-nowrap">
                                {{ $r->skinType->name }}
                            </td>

                            <!-- Kondisi Tambahan Badges -->
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @if($r->is_acne)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-rose-100 text-rose-700 border border-rose-200">
                                            Berjerawat
                                        </span>
                                    @endif
                                    @if($r->is_sensitive)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                            Sensitif
                                        </span>
                                    @endif
                                    @if(!$r->is_acne && !$r->is_sensitive)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-card text-ink/70 border border-gold/20">
                                            Normal / Tanpa Kondisi
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Tips Perawatan -->
                            <td class="px-6 py-4 text-ink max-w-xs md:max-w-md leading-relaxed">
                                {{ \Illuminate\Support\Str::limit($r->tips_perawatan, 85) }}
                            </td>

                            <!-- Action Buttons -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.recommendations.edit', $r) }}"
                                       title="Edit Data"
                                       class="p-2 rounded-xl text-gold-dark hover:text-dark hover:bg-gold/20 border border-transparent hover:border-gold/30 transition duration-150">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </a>

                                    <form action="{{ route('admin.recommendations.destroy', $r) }}" method="POST" class="inline-flex" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rekomendasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                title="Hapus Data"
                                                class="p-2 rounded-xl text-rose-600 hover:text-rose-800 hover:bg-rose-50 border border-transparent hover:border-rose-200 transition duration-150">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <div class="p-3 rounded-full bg-gold/10 text-gold-dark border border-gold/20">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    </div>
                                    <p class="text-sm text-ink/70 font-medium">Belum ada data rekomendasi makeup.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Container -->
    @if($recommendations->hasPages())
        <div class="pt-2">
            {{ $recommendations->links() }}
        </div>
    @endif
</div>
@endsection