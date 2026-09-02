<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consultation extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'gender',
        'photo_path',
        'tingkat_minyak',
        'tingkat_kering',
        'pori_pori',
        'penggunaan_skincare',
        'jerawat',
        'sensitivitas',
        'predicted_skin_type_id',
        'probability_detail',
    ];

    protected function casts(): array
    {
        return [
            'probability_detail' => 'array',
        ];
    }

    /**
     * Relasi ke jenis kulit hasil prediksi.
     */
    public function predictedSkinType(): BelongsTo
    {
        return $this->belongsTo(
            SkinType::class,
            'predicted_skin_type_id'
        );
    }

    /**
     * Nama diagnosa lengkap yang menggabungkan
     * jenis kulit dasar dengan kondisi jerawat dan sensitivitas.
     */
    public function getFullDiagnosisNameAttribute(): string
    {
        $baseName = $this->predictedSkinType?->name
            ?? 'Jenis Kulit Tidak Teridentifikasi';

        $conditions = [];

        if ($this->jerawat === 'ya') {
            $conditions[] = 'Berjerawat';
        }

        if ($this->sensitivitas === 'tinggi') {
            $conditions[] = 'Sensitif';
        }

        if (empty($conditions)) {
            return $baseName;
        }

        return $baseName . ' (' . implode(' & ', $conditions) . ')';
    }

    /**
     * Daftar badge kondisi kulit.
     */
    public function getConditionBadgesAttribute(): array
    {
        $badges = [];

        // 1. Jenis Kulit Dasar
        $badges[] = [
            'type' => 'skin_type',
            'label' => $this->predictedSkinType?->name ?? 'Tipe Dasar',
            'value' => $this->predictedSkinType?->code ?? 'normal',
            'bg' => 'bg-gold/15 text-gold border-gold/30',
        ];

        // 2. Kondisi Jerawat
        if ($this->jerawat === 'ya') {
            $badges[] = [
                'type' => 'acne',
                'label' => 'Kondisi: Berjerawat',
                'value' => 'ya',
                'bg' => 'bg-rose-500/20 text-rose-300 border-rose-500/30',
            ];
        } else {
            $badges[] = [
                'type' => 'acne',
                'label' => 'Kondisi: Bebas Jerawat',
                'value' => 'tidak',
                'bg' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
            ];
        }

        // 3. Kondisi Sensitivitas
        if ($this->sensitivitas === 'tinggi') {
            $badges[] = [
                'type' => 'sensitivity',
                'label' => 'Sensitivitas: Tinggi (Sensitif)',
                'value' => 'tinggi',
                'bg' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
            ];
        } elseif ($this->sensitivitas === 'sedang') {
            $badges[] = [
                'type' => 'sensitivity',
                'label' => 'Sensitivitas: Sedang',
                'value' => 'sedang',
                'bg' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
            ];
        } else {
            $badges[] = [
                'type' => 'sensitivity',
                'label' => 'Sensitivitas: Rendah (Stabil)',
                'value' => 'rendah',
                'bg' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
            ];
        }

        // 4. Penggunaan Skincare
        if ($this->penggunaan_skincare === 'ya') {
            $badges[] = [
                'type' => 'skincare',
                'label' => 'Skincare: Rutin Basic',
                'value' => 'ya',
                'bg' => 'bg-purple-500/20 text-purple-300 border-purple-500/30',
            ];
        } elseif ($this->penggunaan_skincare === 'dokter') {
            $badges[] = [
                'type' => 'skincare',
                'label' => 'Skincare: Dari Dokter',
                'value' => 'dokter',
                'bg' => 'bg-cyan-500/20 text-cyan-300 border-cyan-500/30',
            ];
        } else {
            $badges[] = [
                'type' => 'skincare',
                'label' => 'Skincare: Tidak Sama Sekali',
                'value' => 'tidak',
                'bg' => 'bg-stone-500/20 text-stone-300 border-stone-500/30',
            ];
        }

        return $badges;
    }

    /**
     * Panduan skin preparation dan teknik makeup
     * berdasarkan kondisi pengguna.
     */
    public function getPreparationAdviceAttribute(): array
    {
        $advice = [];

        // 1. Panduan Jerawat
        if ($this->jerawat === 'ya') {
            $advice[] = [
                'icon' => 'acne',
                'title' => 'Teknik Riasan untuk Kondisi Berjerawat',
                'desc' => 'Gunakan color corrector hijau secara presisi untuk menetralkan kemerahan jerawat. Aplikasikan concealer berformula non-comedogenic dengan teknik spot-dabbing (ditepuk halus, bukan digeser) menggunakan kuas atau spons yang bersih agar tidak memperparah kondisi kulit.',
            ];
        }

        // 2. Panduan Kulit Sensitif
        if ($this->sensitivitas === 'tinggi') {
            $advice[] = [
                'icon' => 'sensitive',
                'title' => 'Penanganan Kulit Sensitif & Mudah Iritasi',
                'desc' => 'Gunakan produk kosmetik yang sesuai untuk kulit sensitif, seperti produk hypoallergenic, bebas pewangi sintetis (fragrance-free), dan minim bahan yang berpotensi menyebabkan iritasi. Hindari penggunaan produk baru secara langsung sebelum acara.',
            ];
        }

        // 3. Panduan Penggunaan Skincare
        if ($this->penggunaan_skincare === 'tidak') {
            $advice[] = [
                'icon' => 'skincare',
                'title' => 'Persiapan Kulit Ekstra',
                'desc' => 'Karena belum menggunakan skincare secara rutin, kulit memerlukan persiapan hidrasi yang cukup sebelum pengaplikasian makeup. Gunakan pelembap dan skin preparation yang sesuai agar makeup dapat menempel lebih baik dan tidak mudah terlihat kering.',
            ];
        } elseif ($this->penggunaan_skincare === 'dokter') {
            $advice[] = [
                'icon' => 'skincare',
                'title' => 'Perhatian untuk Pengguna Skincare Dokter',
                'desc' => 'Pertahankan penggunaan skincare dari dokter sesuai dengan petunjuk yang diberikan. Hindari menghentikan atau mengganti produk secara tiba-tiba menjelang acara. Informasikan kepada MUA mengenai penggunaan skincare dokter agar produk makeup dan skin preparation dapat disesuaikan.',
            ];
        } else {
            $advice[] = [
                'icon' => 'skincare',
                'title' => 'Optimasi Skin Preparation yang Sudah Rutin',
                'desc' => 'Pertahankan rutinitas skincare basic seperti cleanser, moisturizer, dan sunscreen menjelang hari H. Hindari mencoba produk aktif baru atau melakukan perawatan yang berpotensi menyebabkan iritasi agar kondisi skin barrier tetap optimal saat dirias.',
            ];
        }

        return $advice;
    }
}