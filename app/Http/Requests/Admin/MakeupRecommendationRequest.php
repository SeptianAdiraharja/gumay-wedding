<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MakeupRecommendationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('web') !== null;
    }

    public function rules(): array
    {
        return [
            'skin_type_id' => ['required', 'exists:skin_types,id'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'in:primer,foundation,concealer,bedak,blush,eye,lip,setting_spray'],
        ];
    }

    public function messages(): array
    {
        return [
            'skin_type_id.required' => 'Jenis kulit wajib dipilih.',
            'skin_type_id.exists' => 'Jenis kulit yang dipilih tidak valid.',
            'title.required' => 'Judul rekomendasi wajib diisi.',
            'category.required' => 'Kategori produk wajib dipilih.',
        ];
    }
}