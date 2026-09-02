<?php

namespace App\Services;

use App\Models\TrainingDataset;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TrainingDatasetExportService
{
    public function exportData(string $format = 'xlsx'): StreamedResponse
    {
        $filename = 'data-latih-naive-bayes-' . date('Y-m-d_H-i-s') . '.' . ($format === 'csv' ? 'csv' : 'csv');
        // Catatan: Jika tidak menggunakan library Maatwebsite/Excel, format CSV adalah yang paling ringan & kompatibel diproses oleh Excel.

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // Tambahkan BOM agar karakter UTF-8 terbaca rapi di Microsoft Excel
            fputs($file, "\xEF\xBB\xBF");

            // Header Kolom
            fputcsv($file, [
                'Jenis Kulit',
                'Tingkat Minyak',
                'Tingkat Kering',
                'Pori-Pori',
                'Penggunaan Skincare',
                'Jerawat',
                'Sensitivitas',
            ]);

            // Stream Data Latih dalam Chunk agar hemat memory
            TrainingDataset::with('skinType')->chunk(200, function ($datasets) use ($file) {
                foreach ($datasets as $row) {
                    fputcsv($file, [
                        $row->skinType->name ?? '',
                        $row->tingkat_minyak,
                        $row->tingkat_kering,
                        $row->pori_pori,
                        $row->penggunaan_skincare,
                        $row->jerawat,
                        $row->sensitivitas,
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}