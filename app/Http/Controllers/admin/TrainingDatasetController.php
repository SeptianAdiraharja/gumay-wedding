<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TrainingDatasetRequest;
use App\Models\SkinType;
use App\Models\TrainingDataset;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Http\Requests\Admin\TrainingDatasetImportRequest;
use App\Services\TrainingDatasetExportService;
use App\Services\TrainingDatasetImportService;
use Illuminate\Http\Request;

class TrainingDatasetController extends Controller
{
    public function index(): View
    {
        $data = TrainingDataset::with('skinType')->latest()->paginate(20);

        // Ringkasan jumlah data per kelas, untuk cek keseimbangan data latih
        $distribution = SkinType::withCount('trainingData')->get();

        return view('admin.training-dataset.index', compact('data', 'distribution'));
    }

    public function create(): View
    {
        $skinTypes = SkinType::all();

        return view('admin.training-dataset.create', compact('skinTypes'));
    }

    public function store(TrainingDatasetRequest $request): RedirectResponse
    {
        TrainingDataset::create($request->validated());

        return redirect()
            ->route('admin.training-dataset.index')
            ->with('success', 'Data latih berhasil ditambahkan.');
    }

    public function edit(TrainingDataset $trainingDataset): View
    {
        $skinTypes = SkinType::all();

        return view('admin.training-dataset.edit', compact('trainingDataset', 'skinTypes'));
    }

    public function update(TrainingDatasetRequest $request, TrainingDataset $trainingDataset): RedirectResponse
    {
        $trainingDataset->update($request->validated());

        return redirect()
            ->route('admin.training-dataset.index')
            ->with('success', 'Data latih berhasil diperbarui.');
    }

    public function destroy(TrainingDataset $trainingDataset): RedirectResponse
    {
        $trainingDataset->delete();

        return redirect()
            ->route('admin.training-dataset.index')
            ->with('success', 'Data latih berhasil dihapus.');
    }

    /**
     * Handle bulk import from Excel / CSV file.
     */
    public function import(TrainingDatasetImportRequest $request, TrainingDatasetImportService $service): RedirectResponse
    {
        $file = $request->file('file');
        $replaceExisting = $request->input('mode') === 'replace';

        $result = $service->import($file, $replaceExisting);

        if (! $result['success']) {
            return redirect()
                ->route('admin.training-dataset.index')
                ->withErrors($result['errors']);
        }

        $message = "Berhasil mengimpor {$result['imported']} baris data latih.";
        if ($result['skipped'] > 0) {
            $message .= " ({$result['skipped']} baris dilewati karena format tidak sesuai)";
        }

        return redirect()
            ->route('admin.training-dataset.index')
            ->with('success', $message);
    }

    /**
     * Download format template for Excel / CSV import.
     */
    public function template(Request $request, TrainingDatasetImportService $service)
    {
        $format = $request->query('format', 'xlsx');

        return $service->exportTemplate($format === 'csv' ? 'csv' : 'xlsx');
    }

    public function export(Request $request, TrainingDatasetExportService $service)
    {
        $format = $request->query('format', 'xlsx');

        return $service->exportData($format);
    }
}
