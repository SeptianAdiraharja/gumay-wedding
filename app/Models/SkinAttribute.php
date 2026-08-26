<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkinAttribute extends Model
{
    protected $fillable = [
        'attribute_key',
        'question_text',
        'options',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
        ];
    }
}
