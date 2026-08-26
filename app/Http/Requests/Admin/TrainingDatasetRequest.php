<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TrainingDatasetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('web') !== null;
    }

    public function rules(): array
    {
        return [
            'skin_type_id' => ['required', 'exists:skin_types,id'],
            'tingkat_minyak' => ['required', 'in:rendah,sedang,tinggi'],
            'tingkat_kering' => ['required', 'in:rendah,sedang,tinggi'],
            'pori_pori' => ['required', 'in:kecil,sedang,besar'],
            'penggunaan_skincare' => ['required', 'in:ya,tidak'],
            'jerawat' => ['required', 'in:ya,tidak'],
            'sensitivitas' => ['required', 'in:rendah,sedang,tinggi'],
        ];
    }

    public function messages(): array
    {
        return [
            'skin_type_id.required' => 'Jenis kulit (label) wajib dipilih.',
            '*.required' => 'Semua kolom wajib diisi.',
            '*.in' => 'Nilai yang dipilih tidak valid.',
        ];
    }
}