<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MakeupRecommendationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('web') !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_acne' => $this->boolean('is_acne'),
            'is_sensitive' => $this->boolean('is_sensitive'),
        ]);
    }

    public function rules(): array
    {
        $recommendation = $this->route('recommendation');
        $recommendationId = $recommendation instanceof \App\Models\MakeupRecommendation
            ? $recommendation->id
            : $recommendation;

        $uniqueRule = Rule::unique('makeup_recommendations', 'skin_type_id')
            ->where(function ($query) {
                return $query->where('is_acne', $this->boolean('is_acne'))
                    ->where('is_sensitive', $this->boolean('is_sensitive'));
            });

        if ($recommendationId) {
            $uniqueRule->ignore($recommendationId);
        }

        return [
            'skin_type_id' => ['required', 'exists:skin_types,id', $uniqueRule],
            'is_acne' => ['nullable', 'boolean'],
            'is_sensitive' => ['nullable', 'boolean'],
            'tips_perawatan' => ['required', 'string'],
            'makeup_perempuan' => ['required', 'string'],
            'makeup_laki_laki' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'skin_type_id.required' => 'Jenis kulit wajib dipilih.',
            'skin_type_id.exists' => 'Jenis kulit yang dipilih tidak valid.',
            'skin_type_id.unique' => 'Kombinasi jenis kulit dan kondisi ini (jerawat & sensitif) sudah memiliki rekomendasi makeup. Silakan pilih kombinasi lain atau edit data yang sudah ada.',
            'tips_perawatan.required' => 'Tips perawatan wajib diisi.',
            'makeup_perempuan.required' => 'Rekomendasi makeup untuk perempuan wajib diisi.',
            'makeup_laki_laki.required' => 'Rekomendasi makeup untuk laki-laki wajib diisi.',
        ];
    }
}