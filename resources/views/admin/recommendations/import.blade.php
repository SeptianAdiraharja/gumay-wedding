@extends('layouts.admin')

@section('title', 'Import Rekomendasi Makeup')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header Section -->
    <div class="flex items-center justify-between pb-4 border-b border-gold/10">
        <div>
            <p class="uppercase tracking-[0.25em] text-xs font-bold text-gold/80 mb-1 flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-gold animate-pulse"></span>
                Manajemen Konten
            </p>
            <h1 class="font-display text-3xl font-bold text-ivory tracking-tight">Import dari Excel</h1>
        </div>
        <a href="{{ route('admin.recommendations.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold tracking-wider text-ink/70 hover:text-ivory bg-surface/50 hover:bg-surface border border-gold/10 hover:border-gold/30 rounded-xl transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>

    <!-- Alert System Errors -->
    @if ($errors->any())
        <div class="flex items-start gap-3 bg-rose/10 border border-rose/30 text-rose text-sm rounded-2xl p-4 backdrop-blur-sm">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="space-y-1">
                <p class="font-semibold">Terjadi kesalahan saat unggah:</p>
                <p class="text-xs opacity-90">{{ $errors->first() }}</p>
            </div>
        </div>
    @endif

    <!-- Alert Import Skipping Errors -->
    @if (session('import_errors') && count(session('import_errors')) > 0)
        <div class="bg-amber-500/10 border border-amber-500/30 text-amber-300 rounded-2xl p-4 backdrop-blur-sm space-y-2">
            <div class="flex items-center gap-2 text-amber-400 font-semibold text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Beberapa baris dilewati ({{ count(session('import_errors')) }} baris):</span>
            </div>
            <div class="max-h-36 overflow-y-auto pr-2 custom-scrollbar">
                <ul class="list-disc list-inside text-xs space-y-1 opacity-90">
                    @foreach (session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="grid md:grid-cols-3 gap-6">
        <!-- Form Import (2 Kolom) -->
        <form action="{{ route('admin.recommendations.import.store') }}" method="POST" enctype="multipart/form-data"
              class="md:col-span-2 bg-plum/80 backdrop-blur-md border border-gold/15 rounded-3xl p-6 md:p-8 space-y-6 shadow-xl shadow-black/40">
            @csrf

            <div class="flex items-center gap-3 pb-4 border-b border-gold/10">
                <div class="p-2.5 rounded-xl bg-gold/10 text-gold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-ivory">Unggah Berkas Excel</h2>
                    <p class="text-xs text-ink/60">Pilih berkas spreadsheet berkestensi .xlsx atau .xls</p>
                </div>
            </div>

            <!-- Custom File Input Area / Dropzone -->
            <div class="space-y-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-ink/80">File Excel (.xlsx)</label>
                <div class="relative group">
                    <input type="file" name="file" id="excel_file" accept=".xlsx,.xls" required
                           onchange="document.getElementById('file-name').textContent = this.files[0] ? this.files[0].name : 'Pilih atau drop file di sini'"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                    <div class="border-2 border-dashed border-gold/20 group-hover:border-gold/50 bg-surface/40 group-hover:bg-surface/70 rounded-2xl p-6 text-center transition-all duration-200 flex flex-col items-center justify-center space-y-3">
                        <div class="p-3 rounded-full bg-gold/5 text-gold/80 group-hover:bg-gold/10 group-hover:scale-110 transition duration-200">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="space-y-1">
                            <p id="file-name" class="text-sm font-medium text-ivory truncate max-w-xs">Klik untuk memilih file Excel</p>
                            <p class="text-[11px] text-ink/50">Maksimal ukuran file menyesuaikan pengaturan server</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-gold/10">
                <a href="{{ route('admin.recommendations.index') }}"
                   class="px-6 py-2.5 rounded-full text-xs font-semibold text-ink/70 hover:text-ivory hover:bg-gold/10 transition duration-150">
                    Batal
                </a>
                <button type="submit"
                        class="bg-gold hover:bg-gold/90 text-plum font-semibold px-8 py-2.5 rounded-full text-xs transition duration-200 shadow-lg shadow-gold/20 hover:shadow-gold/40 hover:-translate-y-0.5 active:translate-y-0">
                    Proses Import
                </button>
            </div>
        </form>

        <!-- Informational Guide Card (1 Kolom) -->
        <div class="bg-plum/50 border border-gold/10 rounded-3xl p-6 space-y-4 h-fit">
            <div class="flex items-center gap-2 text-gold font-semibold text-xs uppercase tracking-wider">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Format Spreadsheet</span>
            </div>

            <p class="text-xs text-ink/70 leading-relaxed">
                Pastikan baris pertama Excel Anda berisi header kolom berikut secara berurutan:
            </p>

            <ol class="space-y-2 text-xs text-ivory/90">
                <li class="flex items-start gap-2 bg-surface/30 p-2 rounded-lg border border-gold/5">
                    <span class="font-mono text-gold font-bold">1.</span>
                    <span><strong class="text-ivory">No</strong></span>
                </li>
                <li class="flex items-start gap-2 bg-surface/30 p-2 rounded-lg border border-gold/5">
                    <span class="font-mono text-gold font-bold">2.</span>
                    <span><strong class="text-ivory">Output Sistem</strong></span>
                </li>
                <li class="flex items-start gap-2 bg-surface/30 p-2 rounded-lg border border-gold/5">
                    <span class="font-mono text-gold font-bold">3.</span>
                    <span><strong class="text-ivory">Tips Perawatan</strong></span>
                </li>
                <li class="flex items-start gap-2 bg-surface/30 p-2 rounded-lg border border-gold/5">
                    <span class="font-mono text-gold font-bold">4.</span>
                    <span><strong class="text-ivory">Makeup Perempuan</strong></span>
                </li>
                <li class="flex items-start gap-2 bg-surface/30 p-2 rounded-lg border border-gold/5">
                    <span class="font-mono text-gold font-bold">5.</span>
                    <span><strong class="text-ivory">Makeup Laki-laki</strong></span>
                </li>
            </ol>
        </div>
    </div>
</div>
@endsection