<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GalleryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('web') !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:photo,video'],
            'category' => ['required', 'in:makeup,dekor,dokumentasi,busana_pengantin,sertifikat'],
            'file' => ['required', 'file', 'max:20480', 'mimes:jpg,jpeg,png,webp,mp4,mov'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File foto/video wajib diunggah.',
            'file.max' => 'Ukuran file maksimal 20MB.',
            'file.mimes' => 'Format file harus jpg, jpeg, png, webp, mp4, atau mov.',
        ];
    }
}