<?php

namespace Database\Seeders;

use App\Models\SkinType;
use Illuminate\Database\Seeder;

class SkinTypeSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'code' => 'normal',
                'name' => 'Kulit Normal',
                'description' => 'Keseimbangan kadar minyak dan kelembapan yang baik, elastisitas kenyal, dan relatif tidak memiliki permasalahan kulit yang berarti.',
            ],
            [
                'code' => 'dry',
                'name' => 'Kulit Kering',
                'description' => 'Kulit terasa kencang, kasar, mudah mengelupas, dan memerlukan kelembapan ekstra agar makeup tidak mudah pecah (cakey).',
            ],
            [
                'code' => 'oily',
                'name' => 'Kulit Berminyak',
                'description' => 'Produksi sebum berlebihan sehingga wajah terlihat mengilap secara merata dan membutuhkan teknik penguncian minyak optimal.',
            ],
            [
                'code' => 'combination',
                'name' => 'Kulit Kombinasi',
                'description' => 'Perpaduan area berminyak di T-Zone (dahi, hidung, dagu) dan area kering atau normal pada bagian pipi (U-Zone).',
            ],
        ];

        foreach ($data as $item) {
            SkinType::updateOrCreate(['code' => $item['code']], $item);
        }
    }
}