<?php

namespace Database\Seeders;

use App\Models\SkinType;
use App\Models\TrainingDataset;
use Illuminate\Database\Seeder;

class TrainingDatasetSeeder extends Seeder
{
    public function run(): void
    {
        // format: [tingkat_minyak, tingkat_kering, pori_pori, penggunaan_skincare, jerawat, sensitivitas, kode_skin_type]
        $rows = [
            // ================= NORMAL (12 Data) =================
            ['rendah', 'rendah', 'kecil', 'ya', 'tidak', 'rendah', 'normal'],
            ['sedang', 'rendah', 'sedang', 'ya', 'tidak', 'rendah', 'normal'],
            ['sedang', 'rendah', 'kecil', 'tidak', 'tidak', 'rendah', 'normal'],
            ['rendah', 'sedang', 'sedang', 'ya', 'tidak', 'rendah', 'normal'],
            ['sedang', 'sedang', 'sedang', 'ya', 'tidak', 'sedang', 'normal'],
            ['sedang', 'rendah', 'sedang', 'ya', 'tidak', 'sedang', 'normal'],
            ['rendah', 'rendah', 'sedang', 'tidak', 'tidak', 'rendah', 'normal'],
            ['sedang', 'rendah', 'kecil', 'ya', 'ya', 'rendah', 'normal'],
            ['rendah', 'rendah', 'kecil', 'ya', 'tidak', 'sedang', 'normal'],
            ['sedang', 'sedang', 'kecil', 'tidak', 'tidak', 'sedang', 'normal'],
            ['sedang', 'rendah', 'sedang', 'tidak', 'ya', 'sedang', 'normal'],
            ['rendah', 'rendah', 'kecil', 'ya', 'tidak', 'rendah', 'normal'],

            // ================= DRY / KERING (12 Data) =================
            ['rendah', 'tinggi', 'kecil', 'ya', 'tidak', 'rendah', 'dry'],
            ['rendah', 'tinggi', 'kecil', 'tidak', 'tidak', 'sedang', 'dry'],
            ['rendah', 'tinggi', 'kecil', 'ya', 'tidak', 'tinggi', 'dry'],
            ['rendah', 'sedang', 'kecil', 'tidak', 'tidak', 'sedang', 'dry'],
            ['rendah', 'tinggi', 'sedang', 'ya', 'tidak', 'sedang', 'dry'],
            ['rendah', 'tinggi', 'kecil', 'tidak', 'ya', 'tinggi', 'dry'],
            ['rendah', 'tinggi', 'kecil', 'ya', 'ya', 'sedang', 'dry'],
            ['rendah', 'tinggi', 'kecil', 'tidak', 'tidak', 'tinggi', 'dry'],
            ['rendah', 'sedang', 'kecil', 'ya', 'ya', 'tinggi', 'dry'],
            ['rendah', 'tinggi', 'sedang', 'tidak', 'ya', 'sedang', 'dry'],
            ['rendah', 'tinggi', 'kecil', 'ya', 'tidak', 'rendah', 'dry'],
            ['rendah', 'tinggi', 'kecil', 'ya', 'ya', 'tinggi', 'dry'],

            // ================= OILY / BERMINYAK (12 Data) =================
            ['tinggi', 'rendah', 'besar', 'ya', 'tidak', 'rendah', 'oily'],
            ['tinggi', 'rendah', 'besar', 'tidak', 'ya', 'rendah', 'oily'],
            ['tinggi', 'rendah', 'besar', 'ya', 'ya', 'tinggi', 'oily'],
            ['tinggi', 'rendah', 'sedang', 'ya', 'tidak', 'sedang', 'oily'],
            ['tinggi', 'sedang', 'besar', 'tidak', 'ya', 'sedang', 'oily'],
            ['tinggi', 'rendah', 'besar', 'tidak', 'ya', 'tinggi', 'oily'],
            ['tinggi', 'rendah', 'besar', 'ya', 'tidak', 'tinggi', 'oily'],
            ['tinggi', 'rendah', 'sedang', 'tidak', 'tidak', 'rendah', 'oily'],
            ['tinggi', 'sedang', 'besar', 'ya', 'ya', 'tinggi', 'oily'],
            ['tinggi', 'rendah', 'besar', 'ya', 'ya', 'sedang', 'oily'],
            ['tinggi', 'sedang', 'sedang', 'tidak', 'ya', 'sedang', 'oily'],
            ['tinggi', 'rendah', 'besar', 'ya', 'tidak', 'rendah', 'oily'],

            // ================= COMBINATION / KOMBINASI (12 Data) =================
            ['tinggi', 'sedang', 'besar', 'ya', 'tidak', 'rendah', 'combination'],
            ['sedang', 'sedang', 'sedang', 'ya', 'tidak', 'rendah', 'combination'],
            ['tinggi', 'tinggi', 'sedang', 'ya', 'tidak', 'sedang', 'combination'],
            ['sedang', 'tinggi', 'besar', 'tidak', 'tidak', 'sedang', 'combination'],
            ['tinggi', 'sedang', 'besar', 'ya', 'ya', 'rendah', 'combination'],
            ['tinggi', 'sedang', 'besar', 'tidak', 'ya', 'tinggi', 'combination'],
            ['sedang', 'tinggi', 'sedang', 'ya', 'ya', 'tinggi', 'combination'],
            ['tinggi', 'sedang', 'sedang', 'tidak', 'tidak', 'tinggi', 'combination'],
            ['sedang', 'tinggi', 'sedang', 'tidak', 'ya', 'sedang', 'combination'],
            ['tinggi', 'tinggi', 'besar', 'ya', 'ya', 'sedang', 'combination'],
            ['tinggi', 'sedang', 'besar', 'ya', 'ya', 'tinggi', 'combination'],
            ['sedang', 'tinggi', 'sedang', 'ya', 'tidak', 'rendah', 'combination'],
        ];

        foreach ($rows as $row) {
            $skinType = SkinType::where('code', $row[6])->first();

            if (! $skinType) {
                continue;
            }

            TrainingDataset::create([
                'tingkat_minyak' => $row[0],
                'tingkat_kering' => $row[1],
                'pori_pori' => $row[2],
                'penggunaan_skincare' => $row[3],
                'jerawat' => $row[4],
                'sensitivitas' => $row[5],
                'skin_type_id' => $skinType->id,
            ]);
        }
    }
}