<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingDataset extends Model
{
    protected $table = 'training_dataset';

    protected $fillable = [
        'tingkat_minyak',
        'tingkat_kering',
        'pori_pori',
        'penggunaan_skincare',
        'jerawat',
        'sensitivitas',
        'skin_type_id',
    ];

    public function skinType(): BelongsTo
    {
        return $this->belongsTo(SkinType::class);
    }
}
