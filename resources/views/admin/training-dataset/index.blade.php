@extends('layouts.admin')

@section('title', 'Data Latih Naive Bayes')

@section('content')
<div x-data="{ importModalOpen: false }">

    <!-- Flash Error Alerts -->
    @if ($errors->any())
        <div class="bg-rose/15 border border-rose/30 text-rose text-sm rounded-2xl p-4 mb-6 shadow-lg shadow-black/20">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-rose shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                <div class="space-y-1">
                    <p class="font-semibold">Terdapat kendala saat memproses data:</p>
                    <ul class="list-disc list-inside text-xs space-y-1 opacity-90">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Header & Tombol Aksi -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div>
            <p class="uppercase tracking-[0.2em] text-xs text-gold/70 font-semibold mb-2">Naive Bayes</p>
            <h1 class="font-display text-3xl text-ivory">Data Latih</h1>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <!-- Download Template Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open"
                        class="bg-surface border border-gold/30 text-ivory hover:text-gold hover:border-gold px-4 py-2.5 rounded-full text-sm font-medium transition duration-150 flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4 text-gold" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Unduh Template</span>
                    <svg class="w-3.5 h-3.5 text-ink/60" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
                <div x-show="open" @click.outside="open = false" style="display: none;"
                     class="absolute right-0 mt-2 w-48 bg-plum border border-gold/20 rounded-2xl shadow-xl py-2 z-50">
                    <a href="{{ route('admin.training-dataset.template', ['format' => 'xlsx']) }}"
                       class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-ivory hover:bg-surface hover:text-gold transition">
                        <svg class="w-4 h-4 text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span>Excel (.xlsx)</span>
                    </a>
                    <a href="{{ route('admin.training-dataset.template', ['format' => 'csv']) }}"
                       class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-ivory hover:bg-surface hover:text-gold transition">
                        <svg class="w-4 h-4 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span>CSV (.csv)</span>
                    </a>
                </div>
            </div>

            <!-- Export Data Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open"
                        class="bg-surface border border-gold/30 text-ivory hover:text-gold hover:border-gold px-4 py-2.5 rounded-full text-sm font-medium transition duration-150 flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4 text-gold" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Export Data</span>
                    <svg class="w-3.5 h-3.5 text-ink/60" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
                <div x-show="open" @click.outside="open = false" style="display: none;"
                    class="absolute right-0 mt-2 w-48 bg-plum border border-gold/20 rounded-2xl shadow-xl py-2 z-50">
                    <a href="{{ route('admin.training-dataset.export', ['format' => 'xlsx']) }}"
                    class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-ivory hover:bg-surface hover:text-gold transition">
                        <svg class="w-4 h-4 text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span>Excel (.xlsx)</span>
                    </a>
                    <a href="{{ route('admin.training-dataset.export', ['format' => 'csv']) }}"
                    class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-ivory hover:bg-surface hover:text-gold transition">
                        <svg class="w-4 h-4 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span>CSV (.csv)</span>
                    </a>
                </div>
            </div>

            <!-- Tombol Buka Modal Import -->
            <button type="button" @click="importModalOpen = true"
                    class="bg-surface border border-gold/40 text-gold hover:bg-gold hover:text-plum px-5 py-2.5 rounded-full text-sm font-medium transition duration-150 flex items-center gap-2 shadow-md">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                </svg>
                <span>Import Excel / CSV</span>
            </button>

            <!-- Tombol Tambah Manual -->
            <a href="{{ route('admin.training-dataset.create') }}"
               class="bg-gold text-plum px-5 py-2.5 rounded-full text-sm font-medium hover:bg-gold/90 transition duration-150 flex items-center gap-2 shadow-md">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Data</span>
            </a>
        </div>
    </div>

    <!-- Ringkasan Keseimbangan Data Per Kelas -->
    <div class="bg-plum border border-gold/10 rounded-2xl p-6 mb-8 shadow-lg shadow-black/20">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-gold" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
            </svg>
            <h2 class="text-xs font-semibold text-ink/70 uppercase tracking-wide">Jumlah Data per Jenis Kulit</h2>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-3">
            @foreach($distribution as $st)
                <div class="text-center bg-surface border border-gold/15 rounded-xl py-3.5 px-2 hover:border-gold/30 transition duration-150">
                    <p class="font-display text-2xl text-gold font-bold">{{ $st->training_data_count }}</p>
                    <p class="text-xs font-medium text-ink/70 mt-0.5 truncate" title="{{ $st->name }}">{{ $st->name }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-4 flex items-start gap-2 bg-surface/50 border border-gold/10 rounded-xl p-3 text-xs text-ink/70">
            <svg class="w-4 h-4 text-gold shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
            </svg>
            <span>
                Untuk hasil klasifikasi yang akurat dan adil, usahakan jumlah sampel tiap kelas seimbang.
                Sistem mendukung impor dari berkas Excel (.xlsx, .xls) maupun CSV (.csv).
            </span>
        </div>
    </div>

    <!-- Tabel Data Latih -->
    <div class="bg-plum border border-gold/10 rounded-2xl overflow-hidden shadow-lg shadow-black/20">
        <table class="w-full text-sm">
            <thead class="bg-surface/50 text-left text-ink/70 text-xs uppercase tracking-wide border-b border-gold/10">
                <tr>
                    <th class="px-5 py-3.5">Label (Kelas)</th>
                    <th class="px-5 py-3.5">Minyak</th>
                    <th class="px-5 py-3.5">Kering</th>
                    <th class="px-5 py-3.5">Pori-pori</th>
                    <th class="px-5 py-3.5">Skincare</th>
                    <th class="px-5 py-3.5">Jerawat</th>
                    <th class="px-5 py-3.5">Sensitif</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gold/10 text-ivory">
                @forelse($data as $row)
                    <tr class="hover:bg-surface/30 transition duration-150">
                        <td class="px-5 py-3.5 font-semibold">
                            <span class="inline-block px-3 py-1 bg-gold/10 text-gold rounded-full text-xs font-semibold border border-gold/20">
                                {{ $row->skinType->name }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 capitalize text-ink/80">{{ $row->tingkat_minyak }}</td>
                        <td class="px-5 py-3.5 capitalize text-ink/80">{{ $row->tingkat_kering }}</td>
                        <td class="px-5 py-3.5 capitalize text-ink/80">{{ $row->pori_pori }}</td>
                       <td class="px-5 py-3.5 capitalize text-ink/80"> {{ match($row->penggunaan_skincare) { 'ya' => 'Rutin', 'dokter' => 'Dari Dokter', default => 'Tidak', } }} </td>
                        <td class="px-5 py-3.5 capitalize text-ink/80">{{ $row->jerawat }}</td>
                        <td class="px-5 py-3.5 capitalize text-ink/80">{{ $row->sensitivitas }}</td>
                        <td class="px-5 py-3.5 text-right font-medium">
                            <div class="flex items-center justify-end gap-4 text-xs">
                                <!-- Tombol Edit -->
                                <a href="{{ route('admin.training-dataset.edit', $row) }}"
                                   class="inline-flex items-center gap-1 text-gold hover:text-gold/80 transition duration-150">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                    <span>Edit</span>
                                </a>

                                <!-- Tombol Hapus -->
                                <form action="{{ route('admin.training-dataset.destroy', $row) }}" method="POST" class="inline-flex" onsubmit="return confirm('Hapus data latih ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 text-rose hover:text-rose/80 transition duration-150">
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
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-ink/40 text-center">Belum ada data latih. Klik "Tambah Data" atau "Import Excel / CSV" untuk mengunggah sampel baru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Custom Styled Pagination -->
    <div class="mt-6 custom-pagination">
        {{ $data->links() }}
    </div>

    <!-- MODAL IMPORT DATASET -->
    <div x-show="importModalOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-xs"
         style="display: none;">

        <div @click.outside="importModalOpen = false"
             class="bg-plum border border-gold/30 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl space-y-6 relative overflow-hidden">

            <!-- Decorative Glow -->
            <div class="absolute -top-20 -right-20 w-48 h-48 bg-gold/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex items-center justify-between pb-3 border-b border-gold/15">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-gold/80">Batch Upload</span>
                    <h3 class="font-display text-2xl text-ivory">Import Data Latih</h3>
                </div>
                <button type="button" @click="importModalOpen = false" class="text-ink/50 hover:text-ivory p-1.5 rounded-full hover:bg-surface transition">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.training-dataset.import') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- File Input Area -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-ink/70 mb-2">
                        Pilih Berkas Excel (.xlsx, .xls) atau CSV (.csv) <span class="text-rose">*</span>
                    </label>
                    <div class="relative group border-2 border-dashed border-gold/30 hover:border-gold rounded-2xl p-6 text-center bg-surface/50 hover:bg-surface transition cursor-pointer">
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                               @change="$refs.filename.innerText = $event.target.files[0] ? $event.target.files[0].name : 'Pilih file atau drag & drop di sini'">

                        <div class="flex flex-col items-center justify-center pointer-events-none">
                            <div class="w-12 h-12 rounded-2xl bg-gold/10 border border-gold/20 flex items-center justify-center text-gold mb-3 group-hover:scale-110 transition duration-200">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                            </div>
                            <p x-ref="filename" class="text-sm font-medium text-ivory">Pilih file atau seret file ke sini</p>
                            <p class="text-xs text-ink/50 mt-1">Maksimal 10 MB (.xlsx, .xls, .csv)</p>
                        </div>
                    </div>
                </div>

                <!-- Mode Opsi Impor -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-ink/70 mb-2">
                        Opsi Impor
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-start gap-3 p-3 bg-surface border border-gold/15 rounded-xl cursor-pointer hover:border-gold/40 transition">
                            <input type="radio" name="mode" value="append" checked class="mt-1 text-gold focus:ring-gold bg-plum">
                            <div>
                                <p class="text-xs font-semibold text-ivory">Tambahkan (Append)</p>
                                <p class="text-[11px] text-ink/60">Data baru akan ditambahkan ke data latih yang ada.</p>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3 bg-surface border border-gold/15 rounded-xl cursor-pointer hover:border-rose/40 transition">
                            <input type="radio" name="mode" value="replace" class="mt-1 text-rose focus:ring-rose bg-plum">
                            <div>
                                <p class="text-xs font-semibold text-rose">Ganti Semua (Replace)</p>
                                <p class="text-[11px] text-ink/60">Hapus data lama dan gantikan dengan data file ini.</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Panduan Struktur Kolom -->
                <div class="bg-surface/60 border border-gold/10 rounded-2xl p-4 text-xs space-y-2 text-ink/75">
                    <p class="font-semibold text-gold flex items-center gap-1.5">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                        </svg>
                        <span>Panduan Format Kolom:</span>
                    </p>
                    <p class="leading-relaxed font-light">
                        File harus memuat kolom: <span class="text-ivory font-mono font-medium">Jenis Kulit, Tingkat Minyak, Tingkat Kering, Pori-Pori, Penggunaan Skincare, Jerawat, Sensitivitas</span>.
                    </p>
                    <div class="pt-1 flex items-center gap-2">
                        <a href="{{ route('admin.training-dataset.template', ['format' => 'xlsx']) }}" class="text-gold underline hover:text-gold/80 text-[11px]">
                            Download Template Excel (.xlsx)
                        </a>
                        <span class="text-ink/30">•</span>
                        <a href="{{ route('admin.training-dataset.template', ['format' => 'csv']) }}" class="text-gold underline hover:text-gold/80 text-[11px]">
                            Download Template CSV (.csv)
                        </a>
                    </div>
                </div>

                <!-- Tombol Aksi Modal -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gold/15">
                    <button type="button" @click="importModalOpen = false"
                            class="px-5 py-2.5 rounded-full text-xs font-medium text-ink/60 hover:text-ivory hover:bg-surface transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="bg-gold text-plum px-7 py-2.5 rounded-full text-xs font-semibold hover:bg-gold/90 transition shadow-md flex items-center gap-2">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                        </svg>
                        <span>Mulai Impor Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection