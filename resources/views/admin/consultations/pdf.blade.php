<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Konsultasi</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #111;
            margin: 0;
            padding: 0;
        }

        /* Kop Surat */
        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .kop-surat h2 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .kop-surat h1 {
            margin: 2px 0;
            font-size: 16pt;
            text-transform: uppercase;
        }
        .kop-surat p {
            margin: 2px 0;
            font-size: 9pt;
            font-style: italic;
        }

        .judul-dokumen {
            text-align: center;
            margin-bottom: 20px;
        }
        .judul-dokumen h3 {
            margin: 0;
            text-transform: uppercase;
            text-decoration: underline;
            font-size: 12pt;
        }
        .judul-dokumen p {
            margin: 4px 0 0 0;
            font-size: 10pt;
        }

        /* Tabel Data */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 10pt;
        }
        table.data-table th {
            background-color: #f2f2f2;
            text-transform: uppercase;
            font-weight: bold;
            text-align: center;
        }

        /* Section Perhitungan */
        .calculation-section {
            page-break-before: always;
            margin-top: 20px;
        }
        .calculation-section:first-of-type {
            page-break-before: auto;
        }

        .consultation-header {
            background-color: #f2f2f2;
            border: 1px solid #000;
            padding: 8px 12px;
            margin-bottom: 10px;
        }
        .consultation-header h4 {
            margin: 0;
            font-size: 11pt;
            text-transform: uppercase;
        }
        .consultation-header p {
            margin: 4px 0 0 0;
            font-size: 9pt;
        }

        .calc-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .calc-table th, .calc-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 9pt;
            vertical-align: top;
        }
        .calc-table th {
            background-color: #e2e8f0;
            text-align: center;
            font-weight: bold;
        }
        .calc-table td.center {
            text-align: center;
        }
        .calc-table td.right {
            text-align: right;
        }

        .class-block {
            border: 1px solid #000;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .class-block-header {
            background-color: #1e293b;
            color: #fff;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 10pt;
            text-transform: uppercase;
        }
        .class-block-body {
            padding: 8px 10px;
        }

        .formula-box {
            background-color: #f8fafc;
            border-left: 3px solid #1e293b;
            padding: 6px 10px;
            margin: 6px 0;
            font-size: 9pt;
            font-family: 'Courier New', monospace;
        }

        .result-box {
            background-color: #fef3c7;
            border: 2px solid #d97706;
            padding: 10px 12px;
            margin: 10px 0;
        }
        .result-box h4 {
            margin: 0 0 6px 0;
            font-size: 11pt;
            color: #92400e;
            text-transform: uppercase;
        }
        .result-box p {
            margin: 2px 0;
            font-size: 10pt;
        }

        .prob-bar-container {
            width: 100%;
            background-color: #e5e7eb;
            height: 14px;
            border-radius: 2px;
            margin: 4px 0;
        }
        .prob-bar {
            height: 14px;
            background-color: #1e293b;
            border-radius: 2px;
        }

        /* Tanda Tangan */
        .ttd-container {
            width: 100%;
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .ttd-box {
            float: right;
            width: 220px;
            text-align: center;
            font-size: 10pt;
        }
        .ttd-space {
            height: 60px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

    <!-- Kop Surat Resmi -->
    <div class="kop-surat">
        <h1>Gumay Wedding</h1>
        <p>Jl. Rancabungur, Kp. Rancabungur, Desa Malakasari, Kec. Baleendah, Kab. Bandung, Jawa Barat. | Email: info@gumaywedding.com</p>
    </div>

    <!-- Judul Dokumen -->
    <div class="judul-dokumen">
        <h3>Laporan Rekapitulasi Riwayat Konsultasi</h3>
        <p>Tanggal Cetak: {{ date('d F Y') }}</p>
    </div>

    <!-- Tabel Content -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Nama Pengunjung</th>
                <th width="15%">Gender</th>
                <th width="15%">No. HP</th>
                <th width="25%">Hasil Diagnosis</th>
                <th width="15%">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($consultations as $index => $c)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $c->name ?? 'Pengunjung' }}</td>
                    <td style="text-align: center;">{{ $c->gender === 'pria' ? 'Laki-laki' : 'Perempuan' }}</td>
                    <td style="text-align: center;">{{ $c->phone ?? '-' }}</td>
                    <td>{{ $c->full_diagnosis_name }}</td>
                    <td style="text-align: center;">{{ $c->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Data riwayat konsultasi tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- ==================== DETAIL PERHITUNGAN NAIVE BAYES ==================== -->
    @if($consultations->isNotEmpty() && !empty($calculations))
        @foreach($consultations as $index => $consultation)
            @php
                $calc = $calculations[$consultation->id] ?? null;
            @endphp

            @if($calc)
                <div class="calculation-section">
                    <!-- Header Konsultasi -->
                    <div class="consultation-header">
                        <h4>Detail Perhitungan Naive Bayes #{{ $index + 1 }}</h4>
                        <p>
                            <strong>Nama:</strong> {{ $consultation->name ?? 'Pengunjung' }} |
                            <strong>Tanggal:</strong> {{ $consultation->created_at->format('d/m/Y H:i') }} |
                            <strong>Hasil:</strong> {{ $consultation->full_diagnosis_name }}
                        </p>
                    </div>

                    <!-- Input Pengguna -->
                    <table class="calc-table">
                        <thead>
                            <tr>
                                <th colspan="2">Input Fitur Pengguna</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($calc['attribute_labels'] as $key => $label)
                                <tr>
                                    <td width="40%">{{ $label }}</td>
                                    <td class="center">{{ $consultation->$key ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Info Umum -->
                    <div class="formula-box">
                        Total Data Latih: <strong>{{ $calc['total_training_samples'] }}</strong> sampel |
                        Jumlah Kelas: <strong>{{ count($calc['classes']) }}</strong>
                    </div>

                    <!-- Loop per Kelas -->
                    @foreach($calc['classes'] as $classId => $class)
                        <div class="class-block">
                            <div class="class-block-header">
                                Kelas: {{ $class['name'] }} ({{ $class['code'] }})
                                — {{ $class['class_count'] }} sampel
                            </div>
                            <div class="class-block-body">

                                <!-- Prior -->
                                <div class="formula-box">
                                    <strong>1. Prior P(H):</strong>
                                    {{ $class['prior_fraction'] }} = {{ number_format($class['prior_val'], 6) }}
                                </div>

                                <!-- Likelihood Table -->
                                <table class="calc-table">
                                    <thead>
                                        <tr>
                                            <th width="25%">Atribut</th>
                                            <th width="15%">Nilai</th>
                                            <th width="15%">Cocok</th>
                                            <th width="20%">Laplace Fraction</th>
                                            <th width="15%">P(x|H)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($class['attribute_likelihoods'] as $attr)
                                            <tr>
                                                <td>{{ $attr['label'] }}</td>
                                                <td class="center">{{ $attr['value'] }}</td>
                                                <td class="center">{{ $attr['matching_count'] }}</td>
                                                <td class="center">{{ $attr['fraction_str'] }}</td>
                                                <td class="center">{{ number_format($attr['prob_val'], 6) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Likelihood Total -->
                                <div class="formula-box">
                                    <strong>2. Likelihood Total:</strong>
                                    Π P(xi|H) = {{ number_format($class['total_likelihood'], 8) }}
                                </div>

                                <!-- Posterior -->
                                <div class="formula-box">
                                    <strong>3. Posterior P(H) × Π P(xi|H):</strong>
                                    {{ number_format($class['prior_val'], 6) }} ×
                                    {{ number_format($class['total_likelihood'], 8) }} =
                                    {{ number_format($class['posterior_val'], 8) }}
                                </div>

                                <!-- Normalized -->
                                <div class="formula-box">
                                    <strong>4. Normalisasi:</strong>
                                    {{ number_format($class['posterior_val'], 8) }} /
                                    {{ number_format($calc['sum_posteriors'], 8) }} =
                                    <strong>{{ number_format($class['normalized_prob'], 4) }}</strong>
                                    ({{ $class['percentage'] }}%)
                                </div>

                                <!-- Progress Bar -->
                                <div class="prob-bar-container">
                                    <div class="prob-bar" style="width: {{ min($class['percentage'], 100) }}%;"></div>
                                </div>

                            </div>
                        </div>
                    @endforeach

                    <!-- Hasil Akhir -->
                    @php
                        $bestClass = collect($calc['classes'])->first();
                    @endphp
                    <div class="result-box">
                        <h4>Kesimpulan</h4>
                        <p>
                            Berdasarkan perhitungan Naive Bayes, klasifikasi tertinggi adalah
                            <strong>{{ $bestClass['name'] }}</strong>
                            dengan probabilitas <strong>{{ $bestClass['percentage'] }}%</strong>.
                        </p>
                        <p style="font-size: 9pt; color: #78350f;">
                            Hasil diagnosis tersimpan: <strong>{{ $consultation->full_diagnosis_name }}</strong>
                        </p>
                    </div>

                </div>
            @endif
        @endforeach
    @endif

    <!-- Tanda Tangan -->
    <div class="ttd-container">
        <div class="ttd-box">
            <p>Bandung, {{ date('d F Y') }}<br>Administrator System,</p>
            <div class="ttd-space"></div>
            <p><strong><u>Septi Rina</u></strong></p>
        </div>
    </div>

</body>
</html>