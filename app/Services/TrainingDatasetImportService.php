<?php

namespace App\Services;

use App\Models\SkinType;
use App\Models\TrainingDataset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

class TrainingDatasetImportService
{
    /**
     * Cache SkinType id by code.
     *
     * @var array<string, int>
     */
    protected array $skinTypeIds = [];

    public function __construct()
    {
        $this->loadSkinTypes();
    }

    protected function loadSkinTypes(): void
    {
        $types = SkinType::all();
        foreach ($types as $type) {
            $this->skinTypeIds[$type->code] = $type->id;
        }
    }

    /**
     * Import dataset from an uploaded file or file path.
     *
     * @param UploadedFile|string $file
     * @param bool $replaceExisting
     * @return array{success: bool, imported: int, skipped: int, errors: array<string>}
     */
    public function import(UploadedFile|string $file, bool $replaceExisting = false): array
    {
        $filePath = is_string($file) ? $file : $file->getRealPath();
        $extension = strtolower(is_string($file) ? pathinfo($file, PATHINFO_EXTENSION) : $file->getClientOriginalExtension());

        if (empty($extension)) {
            $extension = 'xlsx';
        }

        $rawRows = $this->parseRowsFromFile($filePath, $extension);
        $extractedRows = $this->extractDatasets($rawRows);

        if (empty($extractedRows)) {
            return [
                'success' => false,
                'imported' => 0,
                'skipped' => 0,
                'errors' => ['Tidak ditemukan baris data yang valid dalam file yang diunggah. Pastikan format kolom sesuai template.'],
            ];
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            if ($replaceExisting) {
                TrainingDataset::query()->delete();
            }

            foreach ($extractedRows as $row) {
                $skinCode = $row['skin_type_code'];
                $skinTypeId = $this->skinTypeIds[$skinCode] ?? null;

                if (! $skinTypeId) {
                    $skipped++;
                    $errors[] = "Baris {$row['row_number']}: Jenis kulit '{$skinCode}' tidak dikenali.";
                    continue;
                }

                TrainingDataset::create([
                    'skin_type_id'        => $skinTypeId,
                    'tingkat_minyak'      => $row['tingkat_minyak'],
                    'tingkat_kering'      => $row['tingkat_kering'],
                    'pori_pori'           => $row['pori_pori'],
                    'penggunaan_skincare' => $row['penggunaan_skincare'],
                    'jerawat'             => $row['jerawat'],
                    'sensitivitas'        => $row['sensitivitas'],
                ]);

                $imported++;
            }

            DB::commit();

            return [
                'success' => true,
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => $errors,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'success' => false,
                'imported' => 0,
                'skipped' => count($extractedRows),
                'errors' => ['Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()],
            ];
        }
    }

    /**
     * Parse raw rows from XLSX or CSV file.
     *
     * @return array<int, array<int, string>>
     */
    public function parseRowsFromFile(string $filePath, string $extension): array
    {
        if (in_array($extension, ['xlsx', 'xls'])) {
            return $this->parseXlsx($filePath);
        }

        return $this->parseCsv($filePath);
    }

    /**
     * Native XLSX parser using ZipArchive & SimpleXML.
     *
     * @return array<int, array<int, string>>
     */
    public function parseXlsx(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new \RuntimeException('Tidak dapat membuka file Excel (.xlsx). Pastikan file tidak rusak.');
        }

        $strings = [];
        if (($idx = $zip->locateName('xl/sharedStrings.xml')) !== false) {
            $xml = simplexml_load_string($zip->getFromIndex($idx));
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $si) {
                    $t = '';
                    if (isset($si->t)) {
                        $t = (string)$si->t;
                    } elseif (isset($si->r)) {
                        foreach ($si->r as $r) {
                            $t .= (string)$r->t;
                        }
                    }
                    $strings[] = $t;
                }
            }
        }

        $allRows = [];

        // Scan sheets in workbook
        for ($s = 1; $s <= 10; $s++) {
            $sheetPath = "xl/worksheets/sheet{$s}.xml";
            if (($sheetIdx = $zip->locateName($sheetPath)) !== false) {
                $sheetXml = simplexml_load_string($zip->getFromIndex($sheetIdx));
                if (! $sheetXml || ! isset($sheetXml->sheetData->row)) {
                    continue;
                }

                foreach ($sheetXml->sheetData->row as $row) {
                    $rowNum = (int)$row['r'];
                    $cells = [];

                    foreach ($row->c as $c) {
                        $cellRef = (string)$c['r'];
                        $colLetters = preg_replace('/[0-9]/', '', $cellRef);
                        $colIdx = 0;
                        for ($k = 0; $k < strlen($colLetters); $k++) {
                            $colIdx = $colIdx * 26 + (ord(strtoupper($colLetters[$k])) - ord('A') + 1);
                        }
                        $colIdx -= 1;

                        $val = (string)$c->v;
                        if (isset($c['t']) && (string)$c['t'] === 's') {
                            $val = $strings[(int)$val] ?? $val;
                        } elseif (isset($c->is->t)) {
                            $val = (string)$c->is->t;
                        }
                        $cells[$colIdx] = trim($val);
                    }

                    if (! empty($cells)) {
                        $max = max(array_keys($cells));
                        $fullRow = [];
                        for ($col = 0; $col <= $max; $col++) {
                            $fullRow[$col] = $cells[$col] ?? '';
                        }
                        $allRows[$rowNum] = $fullRow;
                    }
                }
            }
        }

        $zip->close();

        return $allRows;
    }

    /**
     * Parse CSV file with auto delimiter detection.
     *
     * @return array<int, array<int, string>>
     */
    public function parseCsv(string $filePath): array
    {
        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            $firstLine = fgets($handle);
            rewind($handle);

            $delimiter = ',';
            if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                $delimiter = ';';
            }
            if (substr_count($firstLine, "\t") > substr_count($firstLine, $delimiter)) {
                $delimiter = "\t";
            }

            $line = 1;
            while (($data = fgetcsv($handle, 10000, $delimiter)) !== false) {
                $rows[$line++] = array_map('trim', $data);
            }
            fclose($handle);
        }

        return $rows;
    }

    /**
     * Extract structured dataset rows from raw table matrix.
     * Supports both standard tabular layout and section-based sheets like jeniskulit.xlsx.
     *
     * @param array<int, array<int, string>> $rawRows
     * @return array<int, array<string, mixed>>
     */
    public function extractDatasets(array $rawRows): array
    {
        $datasets = [];
        $currentHeader = null;

        foreach ($rawRows as $rowNum => $cells) {
            $rowValues = array_values(array_filter(array_map('trim', $cells), fn($v) => $v !== ''));
            if (empty($rowValues)) {
                continue;
            }

            $joined = strtolower(implode(' ', $rowValues));

            // Check if this row is a column header row
            if (
                (str_contains($joined, 'minyak') || str_contains($joined, 'oil')) &&
                (str_contains($joined, 'pori') || str_contains($joined, 'jerawat') || str_contains($joined, 'label') || str_contains($joined, 'kulit'))
            ) {
                $headerMap = [];
                foreach ($cells as $colIdx => $text) {
                    $norm = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $text));
                    if (empty($norm)) {
                        continue;
                    }

                    if (
                        str_contains($norm, 'label') ||
                        str_contains($norm, 'jeniskulit') ||
                        str_contains($norm, 'skintype') ||
                        str_contains($norm, 'kelas') ||
                        $norm === 'jenis'
                    ) {
                        $headerMap['skin_type'] = $colIdx;
                    } elseif (
                        str_contains($norm, 'produksiminyak') ||
                        str_contains($norm, 'tingkatminyak') ||
                        (str_contains($norm, 'minyak') && ! str_contains($norm, 'kondisi'))
                    ) {
                        $headerMap['tingkat_minyak'] = $colIdx;
                    } elseif (
                        str_contains($norm, 'kelembapan') ||
                        str_contains($norm, 'tingkatkering') ||
                        str_contains($norm, 'kekeringan') ||
                        (str_contains($norm, 'kering') && ! str_contains($norm, 'kulit'))
                    ) {
                        $headerMap['tingkat_kering'] = $colIdx;
                    } elseif (str_contains($norm, 'kondisiminyak')) {
                        $headerMap['kondisi_minyak'] = $colIdx;
                    } elseif (str_contains($norm, 'pori') || str_contains($norm, 'pore')) {
                        $headerMap['pori_pori'] = $colIdx;
                    } elseif (str_contains($norm, 'skincare') || str_contains($norm, 'rutin')) {
                        $headerMap['penggunaan_skincare'] = $colIdx;
                    } elseif (str_contains($norm, 'jerawat') || str_contains($norm, 'acne')) {
                        $headerMap['jerawat'] = $colIdx;
                    } elseif (str_contains($norm, 'sensitif') || str_contains($norm, 'sensitivitas') || str_contains($norm, 'sensitive')) {
                        $headerMap['sensitivitas'] = $colIdx;
                    }
                }

                if (! empty($headerMap)) {
                    $currentHeader = $headerMap;
                }
                continue;
            }

            if ($currentHeader && isset($currentHeader['skin_type'])) {
                $rawSkin = $cells[$currentHeader['skin_type']] ?? '';
                if (
                    $rawSkin === '' ||
                    strtolower($rawSkin) === 'label' ||
                    str_contains(strtolower($rawSkin), 'jenis') ||
                    str_contains(strtolower($rawSkin), 'kelas')
                ) {
                    continue;
                }

                $skinCode = $this->normalizeSkinType($rawSkin);
                if (! in_array($skinCode, ['normal', 'dry', 'oily', 'combination'])) {
                    continue;
                }

                $rawMinyak = isset($currentHeader['tingkat_minyak']) ? ($cells[$currentHeader['tingkat_minyak']] ?? '') : '';
                $rawKering = isset($currentHeader['tingkat_kering']) ? ($cells[$currentHeader['tingkat_kering']] ?? '') : '';
                if ($rawKering === '' && isset($currentHeader['kondisi_minyak'])) {
                    $rawKering = $cells[$currentHeader['kondisi_minyak']] ?? '';
                }
                $rawPori = isset($currentHeader['pori_pori']) ? ($cells[$currentHeader['pori_pori']] ?? '') : '';
                $rawSkincare = isset($currentHeader['penggunaan_skincare']) ? ($cells[$currentHeader['penggunaan_skincare']] ?? 'ya') : 'ya';
                $rawJerawat = isset($currentHeader['jerawat']) ? ($cells[$currentHeader['jerawat']] ?? '') : '';
                $rawSensitif = isset($currentHeader['sensitivitas']) ? ($cells[$currentHeader['sensitivitas']] ?? '') : '';

                $datasets[] = [
                    'row_number'          => $rowNum,
                    'skin_type_code'      => $skinCode,
                    'tingkat_minyak'      => $this->normalizeMinyak($rawMinyak),
                    'tingkat_kering'      => $this->normalizeKering($rawKering, $skinCode),
                    'pori_pori'           => $this->normalizePori($rawPori),
                    'penggunaan_skincare' => $this->normalizeSkincare($rawSkincare),
                    'jerawat'             => $this->normalizeJerawat($rawJerawat),
                    'sensitivitas'        => $this->normalizeSensitivitas($rawSensitif),
                ];
            }
        }

        return $datasets;
    }

    /**
     * Normalizers
     */
    public function normalizeSkinType(string $val): string
    {
        $v = strtolower(trim($val));
        if (str_contains($v, 'minyak') || str_contains($v, 'oily')) return 'oily';
        if (str_contains($v, 'kering') || str_contains($v, 'dry')) return 'dry';
        if (str_contains($v, 'kombinasi') || str_contains($v, 'comb')) return 'combination';
        if (str_contains($v, 'normal')) return 'normal';
        return $v;
    }

    public function normalizeMinyak(string $val): string
    {
        $v = strtolower(trim($val));
        if (str_contains($v, 'tinggi') || str_contains($v, 'high') || str_contains($v, 'banyak')) return 'tinggi';
        if (str_contains($v, 'sedang') || str_contains($v, 'medium') || str_contains($v, 'seimbang')) return 'sedang';
        if (str_contains($v, 'rendah') || str_contains($v, 'low') || str_contains($v, 'tidak')) return 'rendah';
        return 'sedang';
    }

    public function normalizeKering(string $val, ?string $skinType = null): string
    {
        $v = strtolower(trim($val));
        if (str_contains($v, 'tinggi') || str_contains($v, 'high') || str_contains($v, 'kasar') || str_contains($v, 'sangat')) return 'tinggi';
        if (str_contains($v, 'sedang') || str_contains($v, 'medium') || str_contains($v, 'berbeda')) return 'sedang';
        if (str_contains($v, 'rendah') || str_contains($v, 'low') || $v === 'tidak') return 'rendah';
        if (str_contains($v, 'tidak berminyak')) return ($skinType === 'dry') ? 'tinggi' : 'sedang';
        if (str_contains($v, 'dominan t-zone')) return 'tinggi';
        if (str_contains($v, 'merata')) return 'rendah';
        return 'rendah';
    }

    public function normalizePori(string $val): string
    {
        $v = strtolower(trim($val));
        if (str_contains($v, 'besar') || str_contains($v, 'terlihat jelas') || str_contains($v, 'large')) return 'besar';
        if (str_contains($v, 'sedang') || str_contains($v, 'medium')) return 'sedang';
        if (str_contains($v, 'kecil') || str_contains($v, 'tidak terlihat') || str_contains($v, 'halus') || str_contains($v, 'small')) return 'kecil';
        return 'sedang';
    }

    public function normalizeSkincare(string $val): string
    {
        $v = strtolower(trim($val));

        if (str_contains($v, 'dokter') || str_contains($v, 'resep') || str_contains($v, 'medis') || str_contains($v, 'dermatolog')) {
            return 'dokter';
        }

        if (str_contains($v, 'tidak') || str_contains($v, 'no') || $v === '0' || $v === 'false') {
            return 'tidak';
        }

        return 'ya';
    }
    public function normalizeJerawat(string $val): string
    {
        $v = strtolower(trim($val));
        if (str_contains($v, 'tidak') || $v === '0' || $v === 'false' || str_contains($v, 'none')) return 'tidak';
        if (str_contains($v, 'ya') || str_contains($v, 'ringan') || str_contains($v, 'sedang') || str_contains($v, 'berat') || str_contains($v, 'ada') || $v === '1') return 'ya';
        return 'tidak';
    }

    public function normalizeSensitivitas(string $val): string
    {
        $v = strtolower(trim($val));
        if (str_contains($v, 'tinggi') || str_contains($v, 'ya') || str_contains($v, 'sensitif') || str_contains($v, 'high')) return 'tinggi';
        if (str_contains($v, 'sedang') || str_contains($v, 'medium') || str_contains($v, 'kadang')) return 'sedang';
        if (str_contains($v, 'rendah') || str_contains($v, 'tidak') || str_contains($v, 'low')) return 'rendah';
        return 'rendah';
    }

    /**
     * Downloadable template sample data.
     *
     * @return array<string, array<int, string>>
     */
    public function getTemplateHeaders(): array
    {
        return [
            'Jenis Kulit',
            'Tingkat Minyak',
            'Tingkat Kering',
            'Pori-Pori',
            'Penggunaan Skincare',
            'Jerawat',
            'Sensitivitas',
        ];
    }

    /**
     * Template sample rows.
     *
     * @return array<int, array<int, string>>
     */
    public function getTemplateSampleRows(): array
    {
        return [
            ['Berminyak', 'Tinggi', 'Rendah', 'Besar', 'Ya', 'Ya', 'Tinggi'],
            ['Berminyak', 'Tinggi', 'Rendah', 'Sedang', 'Ya', 'Tidak', 'Rendah'],
            ['Kering', 'Rendah', 'Tinggi', 'Kecil', 'Tidak', 'Ya', 'Tinggi'],
            ['Kering', 'Rendah', 'Tinggi', 'Kecil', 'Dokter', 'Tidak', 'Rendah'],
            ['Normal', 'Sedang', 'Rendah', 'Kecil', 'Ya', 'Tidak', 'Rendah'],
            ['Normal', 'Sedang', 'Sedang', 'Sedang', 'Ya', 'Ya', 'Rendah'],
            ['Kombinasi', 'Tinggi', 'Tinggi', 'Sedang', 'Dokter', 'Ya', 'Tinggi'],
            ['Kombinasi', 'Tinggi', 'Tinggi', 'Besar', 'Tidak', 'Tidak', 'Rendah'],
        ];
    }

    /**
     * Export / Download Template File.
     */
    public function exportTemplate(string $format = 'xlsx'): BinaryFileResponse|Response
    {
        $headers = $this->getTemplateHeaders();
        $sampleRows = $this->getTemplateSampleRows();

        if ($format === 'csv') {
            $filename = 'template_data_latih_naive_bayes.csv';
            $handle = fopen('php://temp', 'r+');
            fputcsv($handle, $headers);
            foreach ($sampleRows as $row) {
                fputcsv($handle, $row);
            }
            rewind($handle);
            $csvContent = stream_get_contents($handle);
            fclose($handle);

            return response($csvContent, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
        }

        // Generate XLSX
        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'template_' . uniqid() . '.xlsx';
        $this->createXlsxFile($tempPath, $headers, $sampleRows);

        return response()->download($tempPath, 'template_data_latih_naive_bayes.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Create clean XLSX file.
     */
    protected function createXlsxFile(string $filePath, array $headers, array $rows): bool
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
    <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>';

        $rootRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';

        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';

        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets>
        <sheet name="Data Latih" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>';

        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <fonts count="2">
        <font><sz val="11"/><name val="Calibri"/></font>
        <font><b/><sz val="11"/><name val="Calibri"/><color rgb="FFFFFFFF"/></font>
    </fonts>
    <fills count="3">
        <fill><patternFill patternType="none"/></fill>
        <fill><patternFill patternType="gray125"/></fill>
        <fill><patternFill patternType="solid"><fgColor rgb="FF3D182B"/></patternFill></fill>
    </fills>
    <borders count="1">
        <border><left/><right/><top/><bottom/></border>
    </borders>
    <cellXfs count="2">
        <xf fontId="0" fillId="0" borderId="0"/>
        <xf fontId="1" fillId="2" borderId="0" applyFont="1" applyFill="1"/>
    </cellXfs>
</styleSheet>';

        $sheetData = '<sheetData>';
        $rNum = 1;

        // Headers
        $sheetData .= '<row r="1">';
        foreach ($headers as $cIdx => $h) {
            $colLetter = chr(ord('A') + $cIdx);
            $val = htmlspecialchars($h, ENT_XML1);
            $sheetData .= '<c r="' . $colLetter . '1" t="inlineStr" s="1"><is><t>' . $val . '</t></is></c>';
        }
        $sheetData .= '</row>';

        // Sample Data Rows
        foreach ($rows as $row) {
            $rNum++;
            $sheetData .= '<row r="' . $rNum . '">';
            foreach ($row as $cIdx => $val) {
                $colLetter = chr(ord('A') + $cIdx);
                $escaped = htmlspecialchars($val, ENT_XML1);
                $sheetData .= '<c r="' . $colLetter . $rNum . '" t="inlineStr"><is><t>' . $escaped . '</t></is></c>';
            }
            $sheetData .= '</row>';
        }
        $sheetData .= '</sheetData>';

        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    ' . $sheetData . '
</worksheet>';

        $zip->addFromString('[Content_Types].xml', $contentTypes);
        $zip->addFromString('_rels/.rels', $rootRels);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);
        $zip->addFromString('xl/workbook.xml', $workbook);
        $zip->addFromString('xl/styles.xml', $styles);
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->close();

        return true;
    }
}
