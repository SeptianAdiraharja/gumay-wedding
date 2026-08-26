<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Konsultasi - {{ $consultation->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-b: 2px solid #ddd; padding-bottom: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .table th, .table td { border: 1px solid #ccc; padding: 6px 8px; font-size: 11px; }
        .table th { background-color: #f4f4f4; text-align: left; }
        .badge { background: #e2e8f0; padding: 3px 6px; border-radius: 4px; font-size: 10px; }
        .section-title { font-weight: bold; margin-top: 15px; margin-bottom: 5px; font-size: 13px; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0;">LAPORAN DETAIL KONSULTASI KULIT</h2>
        <p style="margin:4px 0 0 0;">Tanggal: {{ $consultation->created_at->format('d F Y, H:i') }}</p>
    </div>

    <div class="section-title">Informasi Pengunjung</div>
    <table class="table">
        <tr><th width="30%">Nama</th><td>{{ $consultation->name ?? '-' }}</td></tr>
        <tr><th>No. Handphone</th><td>{{ $consultation->phone ?? '-' }}</td></tr>
        <tr><th>Jenis Kelamin</th><td>{{ ucfirst($consultation->gender ?? 'Wanita') }}</td></tr>
        <tr><th>Hasil Diagnosis</th><td><strong>{{ $consultation->full_diagnosis_name }}</strong></td></tr>
    </table>

    <div class="section-title">Parameter Jawaban (6 Fitur)</div>
    <table class="table">
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

    <div class="section-title">Hasil Perhitungan Naive Bayes</div>
    <table class="table">
        <thead>
            <tr>
                <th>Kelas Tipe Kulit</th>
                <th>Prior P(C)</th>
                <th>Likelihood Total</th>
                <th>Posterior</th>
                <th>Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($calculation['classes'] as $cls)
            <tr>
                <td><strong>{{ $cls['name'] }}</strong></td>
                <td>{{ round($cls['prior_val'], 4) }}</td>
                <td>{{ sprintf('%.3e', $cls['total_likelihood']) }}</td>
                <td>{{ sprintf('%.3e', $cls['posterior_val']) }}</td>
                <td><strong>{{ $cls['percentage'] }}%</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>