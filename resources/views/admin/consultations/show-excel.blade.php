<table>
    <!-- Judul Header -->
    <tr>
        <th colspan="11" style="font-size: 16px; font-weight: bold; text-align: center; height: 30px;">
            LAPORAN DETAIL KONSULTASI &amp; PERHITUNGAN NAIVE BAYES
        </th>
    </tr>
    <tr>
        <td colspan="11" style="font-size: 10px; text-align: center; color: #666666;">
            Waktu Unduh: {{ date('d F Y, H:i:s') }}
        </td>
    </tr>
    <tr><td colspan="11"></td></tr>

    <!-- Bagian 1: Informasi Pengunjung & Hasil Diagnosis -->
    <tr>
        <th colspan="11" style="font-weight: bold; background-color: #D4AF37; color: #000000; font-size: 12px;">
            1. INFORMASI PENGUNJUNG &amp; HASIL DIAGNOSIS
        </th>
    </tr>
    <tr>
        <td style="font-weight: bold; background-color: #F8F9FA;">ID Konsultasi</td>
        <td colspan="4">{{ $consultation->id }}</td>
        <td style="font-weight: bold; background-color: #F8F9FA;">Tanggal Konsultasi</td>
        <td colspan="5">{{ $consultation->created_at->format('d/m/Y H:i') }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold; background-color: #F8F9FA;">Nama Pengunjung</td>
        <td colspan="4">{{ $consultation->name ?? '-' }}</td>
        <td style="font-weight: bold; background-color: #F8F9FA;">Jenis Kelamin</td>
        <td colspan="5">{{ ucfirst($consultation->gender ?? 'Wanita') }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold; background-color: #F8F9FA;">No. Handphone / WA</td>
        <td colspan="4">{{ $consultation->phone ?? '-' }}</td>
        <td style="font-weight: bold; background-color: #F8F9FA;">Total Data Latih</td>
        <td colspan="5">{{ $calculation['total_training_samples'] ?? 0 }} sampel</td>
    </tr>
    <tr>
        <td style="font-weight: bold; background-color: #E2E8F0;">Hasil Diagnosis Utama</td>
        <td colspan="10" style="font-weight: bold; font-size: 13px; color: #1E293B;">
            {{ $consultation->full_diagnosis_name }}
        </td>
    </tr>
    <tr><td colspan="11"></td></tr>

    <!-- Bagian 2: Parameter Jawaban Pengunjung (6 Fitur) -->
    <tr>
        <th colspan="11" style="font-weight: bold; background-color: #D4AF37; color: #000000; font-size: 12px;">
            2. PARAMETER JAWABAN KULIT PENGUNJUNG (6 FITUR)
        </th>
    </tr>
    <tr style="background-color: #F1F5F9; font-weight: bold; text-align: center;">
        <th colspan="2">Tingkat Minyak</th>
        <th colspan="2">Tingkat Kering</th>
        <th colspan="2">Pori-Pori</th>
        <th colspan="2">Penggunaan Skincare</th>
        <th>Jerawat</th>
        <th colspan="2">Sensitivitas</th>
    </tr>
    <tr style="text-align: center;">
        <td colspan="2">{{ ucfirst($consultation->tingkat_minyak ?? '-') }}</td>
        <td colspan="2">{{ ucfirst($consultation->tingkat_kering ?? '-') }}</td>
        <td colspan="2">{{ ucfirst($consultation->pori_pori ?? '-') }}</td>
        <td colspan="2">{{ $consultation->penggunaan_skincare === 'ya' ? 'Rutin' : 'Tidak' }}</td>
        <td>{{ ucfirst($consultation->jerawat ?? '-') }}</td>
        <td colspan="2">{{ ucfirst($consultation->sensitivitas ?? '-') }}</td>
    </tr>
    <tr><td colspan="11"></td></tr>

    <!-- Bagian 3: Ringkasan Distribusi Probabilitas Akhir (P(C|X)) -->
    <tr>
        <th colspan="11" style="font-weight: bold; background-color: #D4AF37; color: #000000; font-size: 12px;">
            3. HASIL DISTRIBUSI PROBABILITAS KELAS NAIVE BAYES (P(C|X))
        </th>
    </tr>
    <tr style="background-color: #F1F5F9; font-weight: bold; text-align: center;">
        <th>Ranking</th>
        <th colspan="3">Jenis Kulit (Kelas)</th>
        <th colspan="2">Prior P(C)</th>
        <th colspan="2">Posterior Raw</th>
        <th colspan="2">Probabilitas Akhir (%)</th>
        <th>Status</th>
    </tr>
    @php $rank = 1; @endphp
    @if(!empty($calculation['classes']))
        @foreach($calculation['classes'] as $cls)
            @php
                $isWinner = ($consultation->predicted_skin_type_id == $cls['skin_type_id']) || ($cls['code'] == ($consultation->predictedSkinType?->code));
            @endphp
            <tr style="text-align: center; {{ $isWinner ? 'background-color: #FEF3C7; font-weight: bold;' : '' }}">
                <td>{{ $rank++ }}</td>
                <td colspan="3" style="text-align: left;">{{ $cls['name'] }}</td>
                <td colspan="2">{{ $cls['prior_fraction'] }} ({{ round($cls['prior_val'], 4) }})</td>
                <td colspan="2">{{ sprintf('%.6e', $cls['posterior_val']) }}</td>
                <td colspan="2" style="font-weight: bold; color: {{ $isWinner ? '#B45309' : '#1E293B' }};">
                    {{ $cls['percentage'] }}%
                </td>
                <td>{{ $isWinner ? 'TERPILIH (PEMENANG)' : '-' }}</td>
            </tr>
        @endforeach
    @endif
    <tr><td colspan="11"></td></tr>

    <!-- Bagian 4: Matriks Perhitungan Likelihood Tiap Atribut (Laplace Smoothing) -->
    <tr>
        <th colspan="11" style="font-weight: bold; background-color: #D4AF37; color: #000000; font-size: 12px;">
            4. MATRIKS PERHITUNGAN DETAIL LIKELIHOOD &amp; LAPLACE SMOOTHING PER ATRIBUT
        </th>
    </tr>
    <tr style="background-color: #F1F5F9; font-weight: bold; text-align: center; font-size: 10px;">
        <th>Kelas (C)</th>
        <th>Prior P(C)</th>
        <th>P(Minyak|C)</th>
        <th>P(Kering|C)</th>
        <th>P(Pori|C)</th>
        <th>P(Skincare|C)</th>
        <th>P(Jerawat|C)</th>
        <th>P(Sensitif|C)</th>
        <th>Total Likelihood</th>
        <th>Posterior Raw</th>
        <th>Probabilitas (%)</th>
    </tr>
    @if(!empty($calculation['classes']))
        @foreach($calculation['classes'] as $cls)
            @php
                $isWinner = ($consultation->predicted_skin_type_id == $cls['skin_type_id']) || ($cls['code'] == ($consultation->predictedSkinType?->code));
            @endphp
            <tr style="text-align: center; font-size: 10px; {{ $isWinner ? 'background-color: #FEF3C7;' : '' }}">
                <td style="font-weight: bold; text-align: left;">{{ $cls['name'] }}</td>
                <td>{{ round($cls['prior_val'], 4) }}</td>
                @foreach(['tingkat_minyak', 'tingkat_kering', 'pori_pori', 'penggunaan_skincare', 'jerawat', 'sensitivitas'] as $featKey)
                    @php $attr = $cls['attribute_likelihoods'][$featKey] ?? null; @endphp
                    <td>
                        {{ round($attr['prob_val'] ?? 0, 4) }}
                        <br>
                        <span style="font-size: 8px; color: #64748B;">({{ $attr['matching_count'] ?? 0 }}+1)/({{ $attr['class_count'] ?? 0 }}+{{ $attr['possible_count'] ?? 0 }})</span>
                    </td>
                @endforeach
                <td>{{ sprintf('%.4e', $cls['total_likelihood']) }}</td>
                <td style="font-weight: bold;">{{ sprintf('%.4e', $cls['posterior_val']) }}</td>
                <td style="font-weight: bold; color: {{ $isWinner ? '#B45309' : '#000000' }};">
                    {{ $cls['percentage'] }}%
                </td>
            </tr>
        @endforeach
    @endif
    <tr><td colspan="11"></td></tr>

    <!-- Bagian 5: Formula dan Keterangan -->
    <tr>
        <th colspan="11" style="font-weight: bold; background-color: #E2E8F0; color: #000000; font-size: 11px;">
            5. FORMULA &amp; METODOLOGI PERHITUNGAN
        </th>
    </tr>
    <tr>
        <td colspan="11" style="font-size: 10px; color: #334155;">
            * <strong>Laplace Smoothing per Atribut:</strong> P(x_i | C) = (N_ic + 1) / (N_c + |V_i|)<br>
            * Dimana N_ic = Jumlah sampel cocok di data latih kelas C, N_c = Total sampel kelas C, |V_i| = Jumlah variasi opsi fitur.<br>
            * <strong>Total Likelihood:</strong> &prod; P(x_i | C) = P(x_1|C) &times; P(x_2|C) &times; P(x_3|C) &times; P(x_4|C) &times; P(x_5|C) &times; P(x_6|C)<br>
            * <strong>Posterior Raw:</strong> P(C) &times; &prod; P(x_i | C)<br>
            * <strong>Normalisasi Probabilitas:</strong> P(C | X) = Posterior Raw(C) / &sum; Posterior Raw semua kelas
        </td>
    </tr>
</table>
