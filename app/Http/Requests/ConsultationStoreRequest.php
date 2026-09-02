<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsultationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // form publik, semua pengunjung boleh mengisi
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['required', 'in:pria,wanita'],
            'photo'  => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'tingkat_minyak' => ['required', 'in:rendah,sedang,tinggi'],
            'tingkat_kering' => ['required', 'in:rendah,sedang,tinggi'],
            'pori_pori' => ['required', 'in:kecil,sedang,besar'],
            'penggunaan_skincare' => ['required', 'in:ya,tidak,dokter'],
            'jerawat' => ['required', 'in:ya,tidak'],
            'sensitivitas' => ['required', 'in:rendah,sedang,tinggi'],
        ];
    }

    public function messages(): array
    {
        return [
            'tingkat_minyak.required' => 'Mohon pilih kondisi minyak/kilap pada wajah Anda.',
            'tingkat_kering.required' => 'Mohon pilih tingkat kekeringan pada wajah Anda.',
            'pori_pori.required' => 'Mohon pilih ukuran pori-pori wajah Anda.',
            'penggunaan_skincare.required' => 'Mohon pilih kebiasaan penggunaan skincare Anda.',
            'jerawat.required' => 'Mohon pilih kondisi jerawat pada wajah Anda.',
            'sensitivitas.required' => 'Mohon pilih tingkat sensitivitas kulit Anda.',
            '*.in' => 'Jawaban yang dipilih tidak valid.',
        ];
    }
}