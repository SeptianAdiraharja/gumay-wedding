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

        /* Tanda Tangan */
        .ttd-container {
            width: 100%;
            margin-top: 40px;
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

    <!-- Tanda Tangan -->
    <div class="ttd-container">
        <div class="ttd-box">
            <p>Bandung, {{ date('d F Y') }}<br>Administrator System,</p>
            <div class="ttd-space"></div>
            <p><strong><u>Septian Adiraharja</u></strong></p>
        </div>
    </div>

</body>
</html>