@extends('layouts.admin')

@section('title', 'Dashboard — Admin')

@section('content')

<div class="mb-8">
    <p class="uppercase tracking-[0.2em] text-xs text-gold/70 font-semibold mb-2">Ringkasan</p>
    <h1 class="font-display text-3xl text-ivory">Dashboard</h1>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-10">
    <div class="bg-plum border border-gold/10 rounded-2xl p-6 shadow-lg shadow-black/20 flex items-center justify-between hover:border-gold/20 transition duration-200">
        <div>
            <p class="text-xs uppercase tracking-wider text-ink/50 font-medium mb-1">Total Konsultasi</p>
            <p class="font-display text-4xl text-gold font-bold">{{ $totalConsultations }}</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-gold/10 text-gold flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a.75.75 0 0 1-1.007-.853l.764-3.15C3.89 15.65 3 13.918 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
            </svg>
        </div>
    </div>

    <div class="bg-plum border border-gold/10 rounded-2xl p-6 shadow-lg shadow-black/20 flex items-center justify-between hover:border-gold/20 transition duration-200">
        <div>
            <p class="text-xs uppercase tracking-wider text-ink/50 font-medium mb-1">Total Konten Galeri</p>
            <p class="font-display text-4xl text-gold font-bold">{{ $totalGalleries }}</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-gold/10 text-gold flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
            </svg>
        </div>
    </div>
</div>

<!-- Grid Chart & Table -->
<div class="grid lg:grid-cols-2 gap-6 items-start">
    {{-- Chart Section --}}
    <div class="bg-plum border border-gold/10 rounded-2xl p-6 shadow-lg shadow-black/20">
        <h2 class="text-xs font-semibold text-gold/70 uppercase tracking-wide mb-5">Distribusi Jenis Kulit</h2>
        @if($skinTypeDistribution->isEmpty())
            <div class="py-12 text-center">
                <p class="text-sm text-ink/40">Belum ada data konsultasi untuk ditampilkan.</p>
            </div>
        @else
            <!-- Container khusus agar chart terpusat dan ukurannya stabil -->
            <div class="relative w-full max-w-[280px] mx-auto py-2">
                <canvas id="skinTypeChart"></canvas>
            </div>
        @endif
    </div>

    {{-- Table Section --}}
    <div class="bg-plum border border-gold/10 rounded-2xl p-6 shadow-lg shadow-black/20">
        <h2 class="text-xs font-semibold text-gold/70 uppercase tracking-wide mb-5">Rincian Jumlah</h2>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink/40 border-b border-gold/10">
                    <th class="pb-3 font-semibold">Jenis Kulit</th>
                    <th class="pb-3 text-right font-semibold">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gold/10">
                @php
                    $colors = ['#D4AF37', '#F3EAD8', '#8B6B3D', '#B8860B', '#5C4A2E', '#E8C468'];
                @endphp
                @forelse($skinTypeDistribution as $index => $row)
                    <tr class="hover:bg-gold/5 transition duration-150">
                        <td class="py-3 text-ink font-medium flex items-center gap-2.5">
                            <!-- Dot Warna Senada dengan Chart -->
                            <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $colors[$index % count($colors)] }}"></span>
                            <span>{{ $row->predictedSkinType->name ?? '-' }}</span>
                        </td>
                        <td class="py-3 text-right">
                            <span class="inline-block px-3 py-1 bg-gold/10 text-gold rounded-full text-xs font-semibold">
                                {{ $row->total }} kali
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="py-8 text-ink/40 text-center">Belum ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if(!$skinTypeDistribution->isEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    const ctx = document.getElementById('skinTypeChart');

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($skinTypeDistribution->pluck('predictedSkinType.name')) !!},
            datasets: [{
                data: {!! json_encode($skinTypeDistribution->pluck('total')) !!},
                backgroundColor: ['#D4AF37', '#F3EAD8', '#8B6B3D', '#B8860B', '#5C4A2E', '#E8C468'],
                borderColor: '#0F0D0A',
                borderWidth: 2,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { family: 'Work Sans', size: 12 },
                        color: '#EDE3CF',
                        padding: 16,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            },
            cutout: '68%'
        }
    });
</script>
@endif

@endsection