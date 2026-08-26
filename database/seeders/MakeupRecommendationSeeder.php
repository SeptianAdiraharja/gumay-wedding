<?php

namespace Database\Seeders;

use App\Models\MakeupRecommendation;
use App\Models\SkinType;
use Illuminate\Database\Seeder;

class MakeupRecommendationSeeder extends Seeder
{
    public function run(): void
    {
        $recommendations = [
            'normal' => [
                [
                    'category' => 'primer',
                    'title' => 'Hydrating & Illuminating Primer',
                    'description' => 'Memberikan dasar riasan yang halus dengan kilau alami tanpa membuat wajah terasa berat.',
                ],
                [
                    'category' => 'foundation',
                    'title' => 'Satin / Radiant Finish Liquid Foundation',
                    'description' => 'Formula medium-to-full coverage yang menyatu sempurna dengan kulit dan menjaga kilau alami sepanjang acara.',
                ],
                [
                    'category' => 'bedak',
                    'title' => 'Translucent Micro-Setting Powder',
                    'description' => 'Mengunci riasan secara halus tanpa menumpuk dan mempertahankan efek kulit sehat bercahaya.',
                ],
                [
                    'category' => 'setting_spray',
                    'title' => 'Dewy Radiant Fixing Mist',
                    'description' => 'Mengunci makeup agar tahan lama sekaligus memberi kesegaran bercahaya sepanjang hari.',
                ],
            ],
            'dry' => [
                [
                    'category' => 'primer',
                    'title' => 'Ultra-Hydrating & Barrier Primer',
                    'description' => 'Mengandung pelembap intensif (hyaluronic acid / ceramide) untuk mencegah makeup pecah (cakey) di area kering.',
                ],
                [
                    'category' => 'foundation',
                    'title' => 'Creamy Dewy / Nourishing Liquid Foundation',
                    'description' => 'Formula berbasis hidrasi tinggi yang memberikan kelembapan ekstra dan hasil akhir kenyal tanpa mempertegas tekstur kering.',
                ],
                [
                    'category' => 'bedak',
                    'title' => 'Lightweight Hydrating Loose Powder',
                    'description' => 'Diaplikasikan sangat tipis hanya di area tertentu agar kelembapan kulit tetap terjaga dan tidak terasa tertarik.',
                ],
                [
                    'category' => 'setting_spray',
                    'title' => 'Hydrating Dewy Glow Mist',
                    'description' => 'Melembapkan lapisan makeup dan menyatukan bedak sehingga riasan tampak menyatu sempurna dengan kulit.',
                ],
            ],
            'oily' => [
                [
                    'category' => 'primer',
                    'title' => 'Pore-Blurring & Mattifying Primer',
                    'description' => 'Mengontrol produksi minyak berlebih secara intensif di seluruh wajah dan menyamarkan tampilan pori-pori besar.',
                ],
                [
                    'category' => 'foundation',
                    'title' => 'Oil-Free Matte Long-Wear Foundation',
                    'description' => 'Formula matte berkekuatan tahan lama, transferproof, dan tidak mudah luntur oleh keringat atau sebum wajah.',
                ],
                [
                    'category' => 'bedak',
                    'title' => 'Oil-Control Translucent Baking Powder',
                    'description' => 'Menyerap sebum secara optimal dan mengunci foundation dengan teknik baking agar tetap flawless hingga akhir acara.',
                ],
                [
                    'category' => 'setting_spray',
                    'title' => 'Ultra-Matte Seal & Fix Spray',
                    'description' => 'Memberikan lapisan pelindung tahan minyak dan tahan air sepanjang hari untuk riasan pernikahan.',
                ],
            ],
            'combination' => [
                [
                    'category' => 'primer',
                    'title' => 'Balancing Dual-Action Primer',
                    'description' => 'Mengontrol kilap di area T-Zone (dahi, hidung, dagu) sekaligus menjaga kelembapan di area pipi (U-Zone).',
                ],
                [
                    'category' => 'foundation',
                    'title' => 'Demi-Matte / Satin Balancing Foundation',
                    'description' => 'Menyeimbangkan area berminyak dan kering dengan hasil akhir natural velvety yang elegan.',
                ],
                [
                    'category' => 'bedak',
                    'title' => 'Sheer Setting Powder (Zoned Application)',
                    'description' => 'Teknik penguncian dengan fokus baking di area T-Zone dan sapuan ringan pada area pipi.',
                ],
                [
                    'category' => 'setting_spray',
                    'title' => 'Balance All-Day Setting Spray',
                    'description' => 'Menjaga riasan tetap tahan lama tanpa membuat area kering menjadi kusam atau T-Zone berminyak.',
                ],
            ],
        ];

        foreach ($recommendations as $code => $items) {
            $skinType = SkinType::where('code', $code)->first();
            if (! $skinType) {
                continue;
            }

            foreach ($items as $item) {
                MakeupRecommendation::updateOrCreate(
                    [
                        'skin_type_id' => $skinType->id,
                        'category' => $item['category'],
                        'title' => $item['title'],
                    ],
                    [
                        'description' => $item['description'],
                    ]
                );
            }
        }
    }
}
