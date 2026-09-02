<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Detail Konsultasi - {{ $consultation->name }}</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #2d3748;
            line-height: 1.5;
            background-color: #ffffff;
        }

        /* Header Section */
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 12px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 10px;
            color: #64748b;
        }

        /* Section Titles */
        .section-title {
            font-weight: 700;
            margin-top: 20px;
            margin-bottom: 8px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #334155;
            border-left: 3px solid #0284c7;
            padding-left: 8px;
        }

        /* Table Styling */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .table th, .table td {
            border: 1px solid #e2e8f0;
            padding: 8px 10px;
            font-size: 10.5px;
            vertical-align: middle;
        }
        .table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-align: left;
        }
        .table-center th, .table-center td {
            text-align: center;
        }

        /* Badges & Highlights */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-primary {
            background-color: #e0f2fe;
            color: #0369a1;
        }
        .text-highlight {
            color: #0369a1;
            font-weight: 700;
        }

        /* Summary Box */
        .summary-box {
            margin-top: 15px;
            padding: 12px 15px;
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 6px;
            text-align: justify;
            font-size: 11px;
            color: #0c4a6e;
            line-height: 1.6;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <h2>Laporan Detail Konsultasi Kulit</h2>
        <p>Tanggal Diterbitkan: {{ $consultation->created_at->format('d F Y, H:i') }} WIB</p>
    </div>

    <!-- Informasi Pengunjung -->
    <div class="section-title">Informasi Pengunjung</div>
    <table class="table">
        <tr>
            <th width="25%">Nama Lengkap</th>
            <td width="25%">{{ $consultation->name ?? '-' }}</td>
            <th width="25%">Jenis Kelamin</th>
            <td width="25%">{{ ucfirst($consultation->gender ?? 'Wanita') }}</td>
        </tr>
        <tr>
            <th>No. Handphone</th>
            <td>{{ $consultation->phone ?? '-' }}</td>
            <th>Probabilitas Jenis Kulit</th>
            <td><strong class="text-highlight">{{ $consultation->full_diagnosis_name }}</strong></td>
        </tr>
    </table>

    <!-- Parameter Jawaban -->
    <div class="section-title">Parameter Jawaban (6 Fitur)</div>
    <table class="table table-center">
        <thead>
            <tr>
                <th>Minyak</th>
                <th>Kering</th>
                <th>Pori-Pori</th>
                <th>Skincare</th>
                <th>Jerawat</th>
                <th>Sensitivitas</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ ucfirst($consultation->tingkat_minyak ?? '-') }}</td>
                <td>{{ ucfirst($consultation->tingkat_kering ?? '-') }}</td>
                <td>{{ ucfirst($consultation->pori_pori ?? '-') }}</td>
                <td>{{ $consultation->penggunaan_skincare === 'ya' ? 'Rutin' : 'Tidak' }}</td>
                <td>{{ ucfirst($consultation->jerawat ?? '-') }}</td>
                <td>{{ ucfirst($consultation->sensitivitas ?? '-') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Hasil Perhitungan Naive Bayes -->
    <div class="section-title">Hasil Perhitungan Naive Bayes</div>
    <table class="table table-center">
        <thead>
            <tr>
                <th style="text-align: left;">Kelas Tipe Kulit</th>
                <th>Prior P(C)</th>
                <th>Likelihood Total</th>
                <th>Posterior</th>
                <th>Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($calculation['classes'] as $cls)
            <tr @if($loop->first) style="background-color: #f0fdf4;" @endif>
                <td style="text-align: left;">
                    <strong>{{ $cls['name'] }}</strong>
                </td>
                <td>{{ round($cls['prior_val'], 4) }}</td>
                <td>{{ sprintf('%.3e', $cls['total_likelihood']) }}</td>
                <td>{{ sprintf('%.3e', $cls['posterior_val']) }}</td>
                <td>
                    <span class="badge {{ $loop->first ? 'badge-primary' : '' }}">
                        {{ $cls['percentage'] }}%
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Kesimpulan -->
    @php $highestProbability = collect($calculation['classes'])->first(); @endphp
    <div class="summary-box">
        <strong>Kesimpulan:</strong> Berdasarkan hasil perhitungan metode <strong>Naive Bayes</strong>, kategori <strong>{{ $highestProbability['name'] }}</strong> memiliki nilai probabilitas tertinggi sebesar <strong>{{ number_format($highestProbability['percentage'], 2) }}%</strong>. Oleh karena itu, kondisi kulit atas nama <strong>{{ $consultation->name }}</strong> diklasifikasikan ke dalam tipe kulit <strong>{{ $highestProbability['name'] }}</strong>.
    </div>

</body>
</html>