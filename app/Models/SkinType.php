<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkinType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    public function trainingData(): HasMany
    {
        return $this->hasMany(TrainingDataset::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class, 'predicted_skin_type_id');
    }

    public function makeupRecommendations(): HasMany
    {
        return $this->hasMany(MakeupRecommendation::class);
    }
}
