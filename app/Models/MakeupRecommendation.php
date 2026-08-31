<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MakeupRecommendation extends Model
{
    protected $fillable = [
        'skin_type_id',
        'is_acne',
        'is_sensitive',
        'tips_perawatan',
        'makeup_perempuan',
        'makeup_laki_laki',
    ];

    protected function casts(): array
    {
        return [
            'is_acne' => 'boolean',
            'is_sensitive' => 'boolean',
        ];
    }

    public function skinType(): BelongsTo
    {
        return $this->belongsTo(SkinType::class);
    }

    /**
     * Nama diagnosa lengkap, konsisten dengan Consultation::getFullDiagnosisNameAttribute()
     */
    public function getFullDiagnosisNameAttribute(): string
    {
        $baseName = $this->skinType?->name ?? 'Jenis Kulit Tidak Diketahui';
        $conditions = [];

        if ($this->is_acne) {
            $conditions[] = 'Berjerawat';
        }

        if ($this->is_sensitive) {
            $conditions[] = 'Sensitif';
        }

        if (empty($conditions)) {
            return $baseName;
        }

        return $baseName . ' (' . implode(' & ', $conditions) . ')';
    }
}