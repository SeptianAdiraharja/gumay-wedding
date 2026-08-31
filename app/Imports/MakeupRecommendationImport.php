<?php

namespace App\Imports;

use App\Models\MakeupRecommendation;
use App\Models\SkinType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class MakeupRecommendationImport implements ToCollection
{
    private const BASE_TYPE_MAP = [
        'normal' => 'normal',
        'kering' => 'dry',
        'berminyak' => 'oily',
        'kombinasi' => 'combination',
    ];

    private array $skinTypeIds = [];

    public int $imported = 0;
    public int $skipped = 0;
    public array $errors = [];

    public function __construct()
    {
        $this->skinTypeIds = SkinType::pluck('id', 'code')->all();
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $no = $row[0] ?? null;

            // Lewati baris judul section, baris header kolom, dan baris kosong pemisah
            if (!is_numeric($no)) {
                continue;
            }

            $outputSistem = trim((string) ($row[1] ?? ''));
            $tipsPerawatan = trim((string) ($row[2] ?? ''));
            $makeupPerempuan = trim((string) ($row[3] ?? ''));
            $makeupLakiLaki = trim((string) ($row[4] ?? ''));

            if ($outputSistem === '' || $tipsPerawatan === '') {
                $this->skipped++;
                $this->errors[] = "Baris " . ($index + 1) . ": data tidak lengkap, dilewati.";
                continue;
            }

            [$skinTypeId, $isAcne, $isSensitive] = $this->parseOutputSistem($outputSistem);

            if ($skinTypeId === null) {
                $this->skipped++;
                $this->errors[] = "Baris " . ($index + 1) . ": jenis kulit dasar pada '{$outputSistem}' tidak dikenali.";
                continue;
            }

            MakeupRecommendation::updateOrCreate(
                [
                    'skin_type_id' => $skinTypeId,
                    'is_acne' => $isAcne,
                    'is_sensitive' => $isSensitive,
                ],
                [
                    'tips_perawatan' => $tipsPerawatan,
                    'makeup_perempuan' => $makeupPerempuan,
                    'makeup_laki_laki' => $makeupLakiLaki,
                ]
            );

            $this->imported++;
        }
    }

    private function parseOutputSistem(string $text): array
    {
        $parts = array_map('trim', explode('+', $text));
        $baseName = strtolower($parts[0] ?? '');

        $code = self::BASE_TYPE_MAP[$baseName] ?? null;
        $skinTypeId = $code ? ($this->skinTypeIds[$code] ?? null) : null;

        $isAcne = false;
        $isSensitive = false;

        foreach (array_slice($parts, 1) as $condition) {
            $condition = strtolower($condition);

            if (str_contains($condition, 'jerawat')) {
                $isAcne = true;
            }

            if (str_contains($condition, 'sensitif')) {
                $isSensitive = true;
            }
        }

        return [$skinTypeId, $isAcne, $isSensitive];
    }
}