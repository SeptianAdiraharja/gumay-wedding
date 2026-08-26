<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ConsultationImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('web') !== null;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv,txt',
                'max:10240', // 10MB
            ],
            'mode' => [
                'nullable',
                'in:append,replace',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File riwayat konsultasi wajib diunggah.',
            'file.file'     => 'Berkas yang diunggah tidak valid.',
            'file.mimes'    => 'Format file harus berupa Excel (.xlsx, .xls) atau CSV (.csv).',
            'file.max'      => 'Ukuran file maksimal adalah 10 MB.',
            'mode.in'       => 'Pilihan mode impor tidak valid.',
        ];
    }
}
