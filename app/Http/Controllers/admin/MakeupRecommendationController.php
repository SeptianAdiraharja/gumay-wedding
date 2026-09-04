<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MakeupRecommendationRequest;
use App\Http\Requests\Admin\ImportMakeupRecommendationRequest;
use App\Imports\MakeupRecommendationImport;
use App\Models\MakeupRecommendation;
use App\Models\SkinType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class MakeupRecommendationController extends Controller
{
    public function index(): View
    {
        $recommendations = MakeupRecommendation::with('skinType')->latest()->paginate(15);

        return view('admin.recommendations.index', compact('recommendations'));
    }

    public function create(): View
    {
        $skinTypes = SkinType::all();
        $existingRecommendations = MakeupRecommendation::with('skinType')->get();

        return view('admin.recommendations.create', compact('skinTypes', 'existingRecommendations'));
    }

    public function store(MakeupRecommendationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_acne'] = $request->boolean('is_acne');
        $validated['is_sensitive'] = $request->boolean('is_sensitive');

        // Pengecekan keamanan ganda terhadap kombinasi yang sudah ada
        $existing = MakeupRecommendation::where('skin_type_id', $validated['skin_type_id'])
            ->where('is_acne', $validated['is_acne'])
            ->where('is_sensitive', $validated['is_sensitive'])
            ->first();

        if ($existing) {
            return back()
                ->withInput()
                ->with('error', 'Kombinasi jenis kulit dan kondisi ini sudah terdaftar dalam sistem. Silakan edit data tersebut.');
        }

        try {
            MakeupRecommendation::create($validated);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return back()
                ->withInput()
                ->with('error', 'Kombinasi jenis kulit dan kondisi ini sudah terdaftar dalam sistem. Silakan edit data tersebut.');
        }

        return redirect()
            ->route('admin.recommendations.index')
            ->with('success', 'Rekomendasi makeup berhasil ditambahkan.');
    }

    public function edit(MakeupRecommendation $recommendation): View
    {
        $skinTypes = SkinType::all();

        return view('admin.recommendations.edit', compact('recommendation', 'skinTypes'));
    }

    public function update(MakeupRecommendationRequest $request, MakeupRecommendation $recommendation): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_acne'] = $request->boolean('is_acne');
        $validated['is_sensitive'] = $request->boolean('is_sensitive');

        // Cek apakah kombinasi sudah digunakan oleh record lain
        $existing = MakeupRecommendation::where('skin_type_id', $validated['skin_type_id'])
            ->where('is_acne', $validated['is_acne'])
            ->where('is_sensitive', $validated['is_sensitive'])
            ->where('id', '!=', $recommendation->id)
            ->first();

        if ($existing) {
            return back()
                ->withInput()
                ->with('error', 'Kombinasi jenis kulit dan kondisi ini sudah digunakan untuk rekomendasi lain.');
        }

        try {
            $recommendation->update($validated);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return back()
                ->withInput()
                ->with('error', 'Kombinasi jenis kulit dan kondisi ini sudah digunakan untuk rekomendasi lain.');
        }

        return redirect()
            ->route('admin.recommendations.index')
            ->with('success', 'Rekomendasi makeup berhasil diperbarui.');
    }

    public function destroy(MakeupRecommendation $recommendation): RedirectResponse
    {
        $recommendation->delete();

        return redirect()
            ->route('admin.recommendations.index')
            ->with('success', 'Rekomendasi makeup berhasil dihapus.');
    }

    public function importForm(): View
    {
        return view('admin.recommendations.import');
    }

    public function importStore(ImportMakeupRecommendationRequest $request): RedirectResponse
    {
        $import = new MakeupRecommendationImport();

        Excel::import($import, $request->file('file'));

        $message = "Import selesai: {$import->imported} data berhasil disimpan.";
        if ($import->skipped > 0) {
            $message .= " {$import->skipped} baris dilewati.";
        }

        return redirect()
            ->route('admin.recommendations.index')
            ->with('success', $message)
            ->with('import_errors', $import->errors);
    }
}