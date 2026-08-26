<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MakeupRecommendation extends Model
{
    protected $fillable = [
        'skin_type_id',
        'title',
        'description',
        'category',
    ];

    public function skinType(): BelongsTo
    {
        return $this->belongsTo(SkinType::class);
    }
}
