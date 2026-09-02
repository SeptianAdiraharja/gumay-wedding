<?php

namespace Database\Seeders;

use App\Models\SkinAttribute;
use Illuminate\Database\Seeder;

class SkinAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'attribute_key' => 'tingkat_minyak',
                'question_text' => 'Bagaimana produksi minyak atau kilap pada wajah Anda beberapa jam setelah dibersihkan / beraktivitas?',
                'options' => ['rendah', 'sedang', 'tinggi'],
            ],
            [
                'attribute_key' => 'tingkat_kering',
                'question_text' => 'Seberapa sering kulit wajah Anda terasa kering, kencang/tertarik, atau mudah mengelupas?',
                'options' => ['rendah', 'sedang', 'tinggi'],
            ],
            [
                'attribute_key' => 'pori_pori',
                'question_text' => 'Bagaimana tampilan ukuran pori-pori pada kulit wajah Anda?',
                'options' => ['kecil', 'sedang', 'besar'],
            ],
            [
                'attribute_key' => 'penggunaan_skincare',
                'question_text' => 'Apakah Anda rutin menggunakan produk skincare untuk perawatan kulit sehari-hari?',
                'options' => ['ya', 'tidak', 'dokter'],
            ],
            [
                'attribute_key' => 'jerawat',
                'question_text' => 'Apakah saat ini Anda memiliki jerawat aktif, beruntusan, atau komedo di wajah?',
                'options' => ['ya', 'tidak'],
            ],
            [
                'attribute_key' => 'sensitivitas',
                'question_text' => 'Seberapa mudah kulit Anda mengalami kemerahan, rasa perih, atau gatal saat memakai produk baru atau terkena panas?',
                'options' => ['rendah', 'sedang', 'tinggi'],
            ],
        ];

        foreach ($data as $item) {
            SkinAttribute::updateOrCreate(
                ['attribute_key' => $item['attribute_key']],
                $item
            );
        }
    }
}