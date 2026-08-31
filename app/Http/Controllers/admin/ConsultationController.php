<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\ConsultationImportRequest;
use App\Http\Requests\Admin\ConsultationExportRequest;
use App\Services\ConsultationImportService;
use App\Services\NaiveBayesService;
use App\Exports\ConsultationsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ConsultationController extends Controller
{
    public function index(): View
    {
        $consultations = Consultation::with('predictedSkinType')
            ->latest()
            ->paginate(15);

        return view('admin.consultations.index', compact('consultations'));
    }

    public function show(Consultation $consultation, NaiveBayesService $naiveBayesService): View
    {
        $consultation->load('predictedSkinType');

        $calculation = $naiveBayesService->getDetailedCalculation([
            'tingkat_minyak'      => $consultation->tingkat_minyak,
            'tingkat_kering'      => $consultation->tingkat_kering,
            'pori_pori'           => $consultation->pori_pori,
            'penggunaan_skincare' => $consultation->penggunaan_skincare,
            'jerawat'             => $consultation->jerawat,
            'sensitivitas'        => $consultation->sensitivitas,
        ]);

        return view('admin.consultations.show', compact('consultation', 'calculation'));
    }

    /**
     * Export Detail Konsultasi ke PDF
     */
    public function exportDetailPdf(Consultation $consultation, NaiveBayesService $naiveBayesService)
    {
        $consultation->load('predictedSkinType');

        $calculation = $naiveBayesService->getDetailedCalculation([
            'tingkat_minyak'      => $consultation->tingkat_minyak,
            'tingkat_kering'      => $consultation->tingkat_kering,
            'pori_pori'           => $consultation->pori_pori,
            'penggunaan_skincare' => $consultation->penggunaan_skincare,
            'jerawat'             => $consultation->jerawat,
            'sensitivitas'        => $consultation->sensitivitas,
        ]);

        $fileName = 'Detail_Konsultasi_' . Str::slug($consultation->name ?? 'Pengunjung') . '.pdf';

        $pdf = Pdf::loadView('admin.consultations.show-pdf', compact('consultation', 'calculation'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($fileName);
    }

    public function import(ConsultationImportRequest $request, ConsultationImportService $service): RedirectResponse
    {
        $file = $request->file('file');
        $replaceExisting = $request->input('mode') === 'replace';

        $result = $service->import($file, $replaceExisting);

        if (! $result['success']) {
            return redirect()
                ->route('admin.consultations.index')
                ->withErrors($result['errors']);
        }

        $message = "Berhasil mengimpor {$result['imported']} riwayat konsultasi.";
        if ($result['skipped'] > 0) {
            $message .= " ({$result['skipped']} baris dilewati karena format tidak sesuai)";
        }

        return redirect()
            ->route('admin.consultations.index')
            ->with('success', $message);
    }

    public function template(Request $request, ConsultationImportService $service)
    {
        $format = $request->query('format', 'xlsx');

        return $service->exportTemplate($format === 'csv' ? 'csv' : 'xlsx');
    }

    /**
     * Handle Export data ke Excel atau PDF
     */
    public function export(ConsultationExportRequest $request)
    {
        $validated = $request->validated();
        $format    = $validated['format'];
        $startDate = $validated['start_date'] ?? null;
        $endDate   = $validated['end_date'] ?? null;

        $fileName = 'Riwayat_Konsultasi_' . date('Ymd_His');

        if ($format === 'excel') {
            return Excel::download(new ConsultationsExport($startDate, $endDate), $fileName . '.xlsx');
        }

        if ($format === 'pdf') {
            $consultations = Consultation::with('predictedSkinType')
                ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
                ->latest()
                ->get();

            $pdf = Pdf::loadView('admin.consultations.pdf', compact('consultations'))
                ->setPaper('a4', 'portrait');

            return $pdf->download($fileName . '.pdf');
        }
    }
}