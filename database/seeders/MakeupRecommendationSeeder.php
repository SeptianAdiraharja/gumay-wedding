<?php

namespace Database\Seeders;

use App\Imports\MakeupRecommendationImport;
use App\Models\MakeupRecommendation;
use App\Models\SkinType;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class MakeupRecommendationSeeder extends Seeder
{
    public function run(): void
    {
        $xlsxPath = base_path('../Rekomendasi.xlsx');
        if (!file_exists($xlsxPath)) {
            $xlsxPath = 'C:/Users/user/OneDrive/Desktop/Septi-Rina/Rekomendasi.xlsx';
        }

        if (file_exists($xlsxPath)) {
            $import = new MakeupRecommendationImport();
            Excel::import($import, $xlsxPath);
            return;
        }

        // Fallback jika file excel tidak ditemukan
        $skinTypes = SkinType::pluck('id', 'code')->all();
        $combos = [
            ['normal', false, false, 'Pertahankan kebersihan kulit dengan pembersih yang lembut, gunakan pelembap untuk mempertahankan hidrasi, serta gunakan tabir surya pada siang hari.'],
            ['dry', false, false, 'Gunakan pembersih yang lembut, hindari pembersihan berlebihan, gunakan pelembap secara rutin untuk membantu mempertahankan kelembapan kulit.'],
            ['oily', false, false, 'Bersihkan wajah dengan pembersih lembut untuk membantu menghilangkan minyak berlebih tanpa membersihkan secara agresif.'],
            ['combination', false, false, 'Gunakan pembersih lembut, pelembap, dan tabir surya. Perawatan dapat disesuaikan berdasarkan area wajah (T-zone & U-zone).'],
            ['normal', true, false, 'Gunakan pembersih lembut, pelembap, dan tabir surya. Hindari memencet atau menggosok area berjerawat.'],
            ['dry', true, false, 'Gunakan pembersih lembut dan pelembap untuk membantu mempertahankan hidrasi. Hindari pembersihan agresif.'],
            ['oily', true, false, 'Gunakan pembersih lembut untuk membantu mengurangi minyak berlebih tanpa pembersihan agresif.'],
            ['combination', true, false, 'Gunakan pembersih lembut, pelembap, dan tabir surya. Perhatikan perbedaan kondisi T-zone dan area lainnya.'],
            ['normal', false, true, 'Pertahankan rutinitas sederhana dengan pembersih lembut, pelembap, dan tabir surya. Hindari bahan yang menimbulkan rasa perih.'],
            ['dry', false, true, 'Gunakan pembersih yang sangat lembut, pelembap yang menenangkan, serta hindari produk berbahan keras atau alkohol.'],
            ['oily', false, true, 'Gunakan pembersih lembut bebas minyak, pelembap ringan berbahan dasar air, dan hindari eksfoliasi fisik yang kasar.'],
            ['combination', false, true, 'Gunakan produk lembut yang menenangkan pada area sensitif dan formulasi ringan pada area berminyak.'],
            ['normal', true, true, 'Gunakan produk khusus kulit berjerawat yang hypoallergenic, hindari pemencetan jerawat dan zat iritatif.'],
            ['dry', true, true, 'Gunakan pelembap intensif yang ramah barrier kulit, aplikasikan perawatan jerawat secara lokal (spot treatment).'],
            ['oily', true, true, 'Gunakan gel pembersih lembut, pelembap oil-free, dan produk jerawat yang tidak mengikis barrier kulit.'],
            ['combination', true, true, 'Sesuaikan perawatan pada area berminyak/jerawat dengan tekstur ringan dan berikan kelembapan menenangkan pada area kering.'],
        ];

        foreach ($combos as $c) {
            $typeId = $skinTypes[$c[0]] ?? null;
            if (!$typeId) continue;

            MakeupRecommendation::updateOrCreate(
                [
                    'skin_type_id' => $typeId,
                    'is_acne' => $c[1],
                    'is_sensitive' => $c[2],
                ],
                [
                    'tips_perawatan' => $c[3],
                    'makeup_perempuan' => 'Gunakan produk kosmetik yang sesuai dengan tipe kulit dan formula yang telah teruji secara dermatologis.',
                    'makeup_laki_laki' => 'Gunakan perawatan dasar (skin prep) dan produk complexion ringan untuk hasil rapi dan natural.',
                ]
            );
        }
    }
}
