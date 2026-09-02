<?php

namespace App\Services;

use App\Models\SkinAttribute;
use App\Models\SkinType;
use App\Models\TrainingDataset;
use Illuminate\Support\Collection;

class NaiveBayesService
{
    /**
     * Atribut yang dipakai sebagai fitur klasifikasi.
     * Urutan ini harus sama dengan kolom di tabel training_dataset & consultations.
     */
    private array $attributeKeys = [
        'tingkat_minyak',
        'tingkat_kering',
        'pori_pori',
        'penggunaan_skincare',
        'jerawat',
        'sensitivitas',
    ];

    /**
     * Klasifikasikan jenis kulit berdasarkan input pengguna
     * menggunakan Categorical Naive Bayes + Laplace Smoothing.
     *
     * @param  array  $input  ['tingkat_minyak' => 'tinggi', 'tingkat_kering' => 'rendah', ...]
     * @return array  ['skin_type_id' => int, 'probabilities' => ['normal' => 0.12, 'oily' => 0.63, ...]]
     */
    public function classify(array $input): array
    {
        $skinTypes = SkinType::all();
        $trainingData = TrainingDataset::all();
        $totalTraining = $trainingData->count();

        // Guard: kalau data latih kosong, tidak bisa menghitung apa pun
        if ($totalTraining === 0 || $skinTypes->isEmpty()) {
            $fallback = $skinTypes->first();

            return [
                'skin_type_id' => $fallback?->id,
                'probabilities' => [],
            ];
        }

        // Cache jumlah kemungkinan nilai tiap atribut, mis. tingkat_minyak = [rendah, sedang, tinggi] -> 3
        $possibleValueCounts = $this->getPossibleValueCounts();

        $posteriors = [];

        foreach ($skinTypes as $skinType) {
            $posteriors[$skinType->id] = $this->calculatePosterior(
                $skinType,
                $trainingData,
                $totalTraining,
                $input,
                $possibleValueCounts
            );
        }

        return $this->normalizeAndPickBest($posteriors, $skinTypes);
    }

    /**
     * Hitung P(H) x Π P(xi|H) untuk satu kelas (skin type) tertentu.
     */
    private function calculatePosterior(
        SkinType $skinType,
        Collection $trainingData,
        int $totalTraining,
        array $input,
        array $possibleValueCounts
    ): float {
        $classData = $trainingData->where('skin_type_id', $skinType->id);
        $classCount = $classData->count();

        // Prior: P(H) = jumlah data kelas ini / total data latih
        $prior = $classCount / $totalTraining;

        // Likelihood: Π P(xi|H) dengan Laplace Smoothing
        $likelihood = 1.0;

        foreach ($this->attributeKeys as $key) {
            $matchingValueCount = $classData->where($key, $input[$key])->count();
            $possibleValues = $possibleValueCounts[$key] ?? 1;

            // Laplace smoothing: (jumlah cocok + 1) / (jumlah kelas + jumlah kemungkinan nilai)
            $likelihood *= ($matchingValueCount + 1) / ($classCount + $possibleValues);
        }

        return $prior * $likelihood;
    }

    /**
     * Normalisasi hasil posterior semua kelas jadi probabilitas (total = 1),
     * lalu tentukan kelas dengan probabilitas tertinggi.
     */
    private function normalizeAndPickBest(array $posteriors, Collection $skinTypes): array
    {
        $sum = array_sum($posteriors);

        $probabilities = [];
        foreach ($posteriors as $skinTypeId => $value) {
            $probabilities[$skinTypeId] = $sum > 0 ? round($value / $sum, 4) : 0;
        }

        // Urutkan dari probabilitas tertinggi
        arsort($probabilities);
        $predictedId = array_key_first($probabilities);

        // Ubah key dari id -> code (mis. 'oily') supaya mudah dibaca di database/JSON
        $detail = [];
        foreach ($probabilities as $skinTypeId => $prob) {
            $code = $skinTypes->firstWhere('id', $skinTypeId)?->code ?? $skinTypeId;
            $detail[$code] = $prob;
        }

        return [
            'skin_type_id' => $predictedId,
            'probabilities' => $detail,
        ];
    }

    /**
     * Ambil rincian lengkap langkah perhitungan matematis Naive Bayes
     * (Prior, Likelihood per atribut dengan Laplace Smoothing, Posterior, dan Normalisasi Probabilitas)
     * untuk ditampilkan pada halaman detail riwayat konsultasi.
     */
    public function getDetailedCalculation(array $input): array
    {
        $skinTypes = SkinType::all();
        $trainingData = TrainingDataset::all();
        $totalTraining = $trainingData->count();
        $possibleValueCounts = $this->getPossibleValueCounts();

        $attributeLabels = [
            'tingkat_minyak'      => 'Tingkat Minyak',
            'tingkat_kering'      => 'Tingkat Kering',
            'pori_pori'           => 'Pori-Pori',
            'penggunaan_skincare' => 'Penggunaan Skincare',
            'jerawat'             => 'Kondisi Jerawat',
            'sensitivitas'        => 'Tingkat Sensitivitas',
        ];

        $classBreakdowns = [];
        $rawPosteriors = [];

        foreach ($skinTypes as $skinType) {
            $classData = $trainingData->where('skin_type_id', $skinType->id);
            $classCount = $classData->count();

            // 1. Prior
            $prior = $totalTraining > 0 ? ($classCount / $totalTraining) : 0;

            // 2. Likelihood per attribute
            $likelihood = 1.0;
            $attributeLikelihoods = [];

            foreach ($this->attributeKeys as $key) {
                $userValue = $input[$key] ?? '';
                $matchingCount = $classData->where($key, $userValue)->count();
                $possibleCount = $possibleValueCounts[$key] ?? 1;

                // Laplace formula: (Nic + 1) / (Nc + |Vi|)
                $numerator = $matchingCount + 1;
                $denominator = $classCount + $possibleCount;
                $attrProb = $denominator > 0 ? ($numerator / $denominator) : 0;

                $likelihood *= $attrProb;

                $attributeLikelihoods[$key] = [
                    'label'          => $attributeLabels[$key] ?? $key,
                    'value'          => $userValue,
                    'matching_count' => $matchingCount,
                    'class_count'    => $classCount,
                    'possible_count' => $possibleCount,
                    'numerator'      => $numerator,
                    'denominator'    => $denominator,
                    'fraction_str'   => "({$matchingCount} + 1) / ({$classCount} + {$possibleCount})",
                    'prob_val'       => $attrProb,
                ];
            }

            $posterior = $prior * $likelihood;
            $rawPosteriors[$skinType->id] = $posterior;

            $classBreakdowns[$skinType->id] = [
                'skin_type_id'          => $skinType->id,
                'name'                  => $skinType->name,
                'code'                  => $skinType->code,
                'class_count'           => $classCount,
                'total_training'        => $totalTraining,
                'prior_fraction'        => "{$classCount} / {$totalTraining}",
                'prior_val'             => $prior,
                'attribute_likelihoods' => $attributeLikelihoods,
                'total_likelihood'      => $likelihood,
                'posterior_val'         => $posterior,
            ];
        }

        $sumPosteriors = array_sum($rawPosteriors);

        // Normalize
        foreach ($classBreakdowns as $id => &$cb) {
            $normalized = $sumPosteriors > 0 ? ($cb['posterior_val'] / $sumPosteriors) : 0;
            $cb['normalized_prob'] = round($normalized, 4);
            $cb['percentage'] = round($normalized * 100, 2);
        }
        unset($cb);

        // Sort by percentage descending
        uasort($classBreakdowns, fn ($a, $b) => $b['normalized_prob'] <=> $a['normalized_prob']);

        return [
            'total_training_samples' => $totalTraining,
            'sum_posteriors'         => $sumPosteriors,
            'classes'                => $classBreakdowns,
            'attribute_labels'       => $attributeLabels,
        ];
    }

    /**
     * Ambil jumlah kemungkinan nilai untuk tiap atribut dari tabel skin_attributes.
     * Dilengkapi default fallback jika tabel belum di-seed.
     */
    public function getPossibleValueCounts(): array
    {
        $defaults = [
            'tingkat_minyak'      => 3,
            'tingkat_kering'      => 3,
            'pori_pori'           => 3,
            'penggunaan_skincare' => 3,
            'jerawat'             => 2,
            'sensitivitas'        => 3,
        ];

        try {
            $dbCounts = SkinAttribute::whereIn('attribute_key', $this->attributeKeys)
                ->get()
                ->mapWithKeys(fn ($attr) => [$attr->attribute_key => is_array($attr->options) ? count($attr->options) : 3])
                ->toArray();

            return array_merge($defaults, $dbCounts);
        } catch (\Throwable) {
            return $defaults;
        }
    }
}