<?php

namespace App\Services;

use App\Models\Consultation;
use App\Models\SkinType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

class ConsultationImportService
{
    protected NaiveBayesService $naiveBayesService;

    /**
     * @var array<string, int>
     */
    protected array $skinTypeIds = [];

    public function __construct(NaiveBayesService $naiveBayesService)
    {
        $this->naiveBayesService = $naiveBayesService;
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
     * Import consultation records from file.
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

        $parsedData = $this->parseFile($filePath, $extension);
        $records = $parsedData['records'];
        $images = $parsedData['images'];

        if (empty($records)) {
            return [
                'success' => false,
                'imported' => 0,
                'skipped' => 0,
                'errors' => ['Tidak ditemukan baris data riwayat konsultasi yang valid pada berkas yang diunggah.'],
            ];
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            if ($replaceExisting) {
                Consultation::query()->delete();
            }

            foreach ($records as $row) {
                $features = [
                    'tingkat_minyak'      => $row['tingkat_minyak'],
                    'tingkat_kering'      => $row['tingkat_kering'],
                    'pori_pori'           => $row['pori_pori'],
                    'penggunaan_skincare' => $row['penggunaan_skincare'],
                    'jerawat'             => $row['jerawat'],
                    'sensitivitas'        => $row['sensitivitas'],
                ];

                // Run Naive Bayes classification to get predicted type and probabilities
                $classification = $this->naiveBayesService->classify($features);
                $predictedSkinTypeId = $classification['skin_type_id'];
                $probabilityDetail = $classification['probabilities'];

                // If user provided a target skin type, fallback if classification is empty
                if (! $predictedSkinTypeId && ! empty($row['skin_type_code'])) {
                    $predictedSkinTypeId = $this->skinTypeIds[$row['skin_type_code']] ?? null;
                }

                // Handle photo if available
                $photoPath = null;
                $rowNum = $row['row_number'] ?? null;
                if ($rowNum && isset($images[$rowNum])) {
                    $imgBinary = $images[$rowNum]['data'];
                    $imgExt = $images[$rowNum]['ext'] ?? 'jpg';
                    $fileName = 'consultations/' . uniqid('import_') . '.' . $imgExt;
                    Storage::disk('public')->put($fileName, $imgBinary);
                    $photoPath = $fileName;
                }

                Consultation::create([
                    'name'                   => $row['name'] ?: 'Pengunjung',
                    'phone'                  => $row['phone'] ?? null,
                    'gender'                 => $row['gender'] ?? 'wanita',
                    'photo_path'             => $photoPath,
                    'tingkat_minyak'         => $features['tingkat_minyak'],
                    'tingkat_kering'         => $features['tingkat_kering'],
                    'pori_pori'              => $features['pori_pori'],
                    'penggunaan_skincare'    => $features['penggunaan_skincare'],
                    'jerawat'                => $features['jerawat'],
                    'sensitivitas'           => $features['sensitivitas'],
                    'predicted_skin_type_id' => $predictedSkinTypeId,
                    'probability_detail'     => $probabilityDetail,
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
                'skipped' => count($records),
                'errors' => ['Terjadi kendala saat menyimpan data riwayat konsultasi: ' . $e->getMessage()],
            ];
        }
    }

    /**
     * Parse File (XLSX / CSV) and extract consultation records & drawings.
     *
     * @return array{records: array<int, array<string, mixed>>, images: array<int, array{data: string, ext: string}>}
     */
    public function parseFile(string $filePath, string $extension): array
    {
        if (in_array($extension, ['xlsx', 'xls'])) {
            return $this->parseXlsxWithImages($filePath);
        }

        return [
            'records' => $this->parseCsv($filePath),
            'images'  => [],
        ];
    }

    /**
     * Parse XLSX with XML/Drawing extractor.
     *
     * @return array{records: array<int, array<string, mixed>>, images: array<int, array{data: string, ext: string}>}
     */
    public function parseXlsxWithImages(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new \RuntimeException('Tidak dapat membuka berkas Excel (.xlsx). Pastikan berkas tidak rusak.');
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

        // Parse drawing relations for embedded images
        $relMap = [];
        if (($drawRelsIdx = $zip->locateName('xl/drawings/_rels/drawing1.xml.rels')) !== false) {
            $relsXml = $zip->getFromIndex($drawRelsIdx);
            preg_match_all('/<Relationship[^>]+Id="([^"]+)"[^>]+Target="([^"]+)"/i', $relsXml, $relMatches, PREG_SET_ORDER);
            foreach ($relMatches as $rm) {
                $relMap[$rm[1]] = str_replace('../', 'xl/', $rm[2]);
            }
        }

        $rowImages = [];
        if (($drawIdx = $zip->locateName('xl/drawings/drawing1.xml')) !== false) {
            $drawContent = $zip->getFromName('xl/drawings/drawing1.xml');
            preg_match_all('/<xdr:twoCellAnchor>.*?<xdr:from>.*?<xdr:row>([0-9]+)<\/xdr:row>.*?<\/xdr:from>.*?r:embed="([^"]+)".*?<\/xdr:twoCellAnchor>/s', $drawContent, $anchorMatches, PREG_SET_ORDER);
            foreach ($anchorMatches as $am) {
                $rowIdx0 = (int)$am[1];
                $rId = $am[2];
                $targetPath = $relMap[$rId] ?? null;
                if ($targetPath && ! isset($rowImages[$rowIdx0 + 1])) {
                    $imgBinary = $zip->getFromName($targetPath);
                    if ($imgBinary) {
                        $ext = pathinfo($targetPath, PATHINFO_EXTENSION) ?: 'jpg';
                        $rowImages[$rowIdx0 + 1] = [
                            'data' => $imgBinary,
                            'ext'  => $ext,
                        ];
                    }
                }
            }
        }

        // Read sheet1.xml
        $sheetXml = simplexml_load_string($zip->getFromIndex($zip->locateName('xl/worksheets/sheet1.xml')));
        $rows = [];
        if ($sheetXml && isset($sheetXml->sheetData->row)) {
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
                    $rows[$rowNum] = $fullRow;
                }
            }
        }

        $zip->close();

        $records = $this->extractConsultationRecords($rows);

        return [
            'records' => $records,
            'images'  => $rowImages,
        ];
    }

    /**
     * Parse CSV for consultations.
     *
     * @return array<int, array<string, mixed>>
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

        return $this->extractConsultationRecords($rows);
    }

    /**
     * Extract structured consultation records from parsed rows.
     *
     * @param array<int, array<int, string>> $rows
     * @return array<int, array<string, mixed>>
     */
    public function extractConsultationRecords(array $rows): array
    {
        $records = [];
        $headerMap = null;
        $lastCharacteristics = [];

        foreach ($rows as $rowNum => $cells) {
            $nonEmpty = array_filter($cells, fn($v) => $v !== '');
            if (empty($nonEmpty)) {
                continue;
            }

            $joined = strtolower(implode(' ', $nonEmpty));

            // Check if this is the header row
            if (
                (str_contains($joined, 'nama') || str_contains($joined, 'name')) &&
                (str_contains($joined, 'gender') || str_contains($joined, 'kelamin') || str_contains($joined, 'kulit') || str_contains($joined, 'minyak'))
            ) {
                $headerMap = [];
                foreach ($cells as $colIdx => $text) {
                    $norm = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $text));
                    if (empty($norm)) continue;

                    if ($norm === 'nama' || $norm === 'name' || str_contains($norm, 'namapengunjung') || str_contains($norm, 'namaclient')) {
                        $headerMap['name'] = $colIdx;
                    } elseif ($norm === 'gender' || $norm === 'jeniskelamin' || $norm === 'jk' || $norm === 'sex') {
                        $headerMap['gender'] = $colIdx;
                    } elseif (
                        $norm === 'phone' || $norm === 'hp' || $norm === 'nohp' || $norm === 'nomorhp' ||
                        $norm === 'telepon' || $norm === 'notelp' || $norm === 'nomortelepon' ||
                        $norm === 'wa' || $norm === 'nowa' || $norm === 'nomorwa' || $norm === 'whatsapp'
                    ) {
                        $headerMap['phone'] = $colIdx;
                    } elseif ($norm === 'jeniskulit' || $norm === 'skintype' || $norm === 'kulit' || $norm === 'tipekulit') {
                        $headerMap['skin_type'] = $colIdx;
                    } elseif (str_contains($norm, 'karakteristik') || str_contains($norm, 'gejala') || str_contains($norm, 'ciri')) {
                        $headerMap['karakteristik'] = $colIdx;
                    } elseif (str_contains($norm, 'produksiminyak') || str_contains($norm, 'tingkatminyak') || (str_contains($norm, 'minyak') && ! str_contains($norm, 'kondisi') && ! str_contains($norm, 'distribusi') && ! str_contains($norm, 'kulit'))) {
                        $headerMap['tingkat_minyak'] = $colIdx;
                    } elseif (str_contains($norm, 'kelembapan') || str_contains($norm, 'tingkatkering') || str_contains($norm, 'kekeringan') || (str_contains($norm, 'kering') && ! str_contains($norm, 'kulit'))) {
                        $headerMap['tingkat_kering'] = $colIdx;
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
                continue;
            }

            // If header was identified, parse row
            if ($headerMap) {
                $name = isset($headerMap['name']) ? ($cells[$headerMap['name']] ?? '') : ($cells[1] ?? '');
                $genderRaw = isset($headerMap['gender']) ? ($cells[$headerMap['gender']] ?? '') : ($cells[2] ?? '');
                $gender = (str_contains(strtolower($genderRaw), 'laki') || str_contains(strtolower($genderRaw), 'pria') || str_contains(strtolower($genderRaw), 'male')) ? 'pria' : 'wanita';
                $phone = isset($headerMap['phone']) ? ($cells[$headerMap['phone']] ?? null) : null;
                $skinTypeRaw = isset($headerMap['skin_type']) ? ($cells[$headerMap['skin_type']] ?? '') : ($cells[3] ?? '');
                $skinCode = $this->normalizeSkinType($skinTypeRaw);

                // If tabular layout with direct attribute columns
                if (isset($headerMap['tingkat_minyak']) || isset($headerMap['tingkat_kering'])) {
                    $minyak = isset($headerMap['tingkat_minyak']) ? $this->normalizeMinyak($cells[$headerMap['tingkat_minyak']] ?? '') : 'sedang';
                    $kering = isset($headerMap['tingkat_kering']) ? $this->normalizeKering($cells[$headerMap['tingkat_kering']] ?? '', $skinCode) : 'rendah';
                    $pori = isset($headerMap['pori_pori']) ? $this->normalizePori($cells[$headerMap['pori_pori']] ?? '') : 'sedang';
                    $skincare = isset($headerMap['penggunaan_skincare']) ? $this->normalizeSkincare($cells[$headerMap['penggunaan_skincare']] ?? 'ya') : 'ya';
                    $jerawat = isset($headerMap['jerawat']) ? $this->normalizeJerawat($cells[$headerMap['jerawat']] ?? 'tidak') : 'tidak';
                    $sensitif = isset($headerMap['sensitivitas']) ? $this->normalizeSensitivitas($cells[$headerMap['sensitivitas']] ?? 'rendah') : 'rendah';
                } else {
                    // Section/Text-based layout (like cek.xlsx)
                    $charRaw = isset($headerMap['karakteristik']) ? ($cells[$headerMap['karakteristik']] ?? '') : ($cells[4] ?? '');

                    if (! empty($charRaw) && ! str_contains($charRaw, 'Sama')) {
                        $features = $this->parseCharacteristicsText($charRaw, $skinCode);
                        $lastCharacteristics[$skinCode] = $features;
                    } elseif (isset($lastCharacteristics[$skinCode])) {
                        $features = $lastCharacteristics[$skinCode];
                    } else {
                        $features = $this->defaultFeaturesForSkinType($skinCode);
                    }

                    $minyak = $features['tingkat_minyak'];
                    $kering = $features['tingkat_kering'];
                    $pori = $features['pori_pori'];
                    $skincare = $features['penggunaan_skincare'];
                    $jerawat = $features['jerawat'];
                    $sensitif = $features['sensitivitas'];
                }

                if (empty($name)) {
                    $name = 'Pengunjung ' . ($gender === 'pria' ? 'Laki-laki' : 'Perempuan') . ' (' . ucfirst($skinCode ?: 'Kulit') . ')';
                }

                $records[] = [
                    'row_number'          => $rowNum,
                    'name'                => $name,
                    'phone'               => $phone,
                    'gender'              => $gender,
                    'skin_type_code'      => $skinCode,
                    'tingkat_minyak'      => $minyak,
                    'tingkat_kering'      => $kering,
                    'pori_pori'           => $pori,
                    'penggunaan_skincare' => $skincare,
                    'jerawat'             => $jerawat,
                    'sensitivitas'        => $sensitif,
                ];
            }
        }

        return $records;
    }

    /**
     * Parse unstructured characteristic text block (from cek.xlsx).
     *
     * @return array<string, string>
     */
    protected function parseCharacteristicsText(string $text, string $skinType): array
    {
        $t = strtolower($text);

        // Minyak
        $minyak = 'sedang';
        if (str_contains($t, 'minyak: tinggi') || str_contains($t, 'minyak tinggi') || str_contains($t, 'sedang–tinggi') || str_contains($t, 'sedang-tinggi') || str_contains($skinType, 'oily')) {
            $minyak = 'tinggi';
        } elseif (str_contains($t, 'minyak: rendah') || str_contains($t, 'minyak rendah') || str_contains($skinType, 'dry')) {
            $minyak = 'rendah';
        } elseif (str_contains($skinType, 'combination')) {
            $minyak = 'tinggi';
        }

        // Kering
        $kering = 'rendah';
        if (str_contains($t, 'tidak berminyak') || str_contains($t, 'kekeringan: tinggi') || str_contains($skinType, 'dry')) {
            $kering = 'tinggi';
        } elseif (str_contains($t, 'dominan t-zone') || str_contains($skinType, 'combination')) {
            $kering = 'tinggi';
        } elseif (str_contains($skinType, 'normal')) {
            $kering = 'rendah';
        }

        // Pori
        $pori = 'sedang';
        if (str_contains($t, 'terlihat jelas') || str_contains($t, 'besar') || str_contains($skinType, 'oily')) {
            $pori = 'besar';
        } elseif (str_contains($t, 'tidak terlihat') || str_contains($skinType, 'dry')) {
            $pori = 'kecil';
        }

        // Jerawat
        $jerawat = (str_contains($t, 'berjerawat') && ! str_contains($t, 'tidak ada')) ? 'ya' : 'tidak';

        // Sensitivitas
        $sensitif = (str_contains($t, 'sensitif: ya') || str_contains($t, 'sensitivitas: ya')) ? 'tinggi' : 'rendah';

        return [
            'tingkat_minyak'      => $minyak,
            'tingkat_kering'      => $kering,
            'pori_pori'           => $pori,
            'penggunaan_skincare' => 'ya',
            'jerawat'             => $jerawat,
            'sensitivitas'        => $sensitif,
        ];
    }

    /**
     * Fallback features for standard skin type.
     *
     * @return array<string, string>
     */
    protected function defaultFeaturesForSkinType(string $skinType): array
    {
        $st = strtolower($skinType);
        if (str_contains($st, 'oily') || str_contains($st, 'minyak')) {
            return ['tingkat_minyak' => 'tinggi', 'tingkat_kering' => 'rendah', 'pori_pori' => 'besar', 'penggunaan_skincare' => 'ya', 'jerawat' => 'tidak', 'sensitivitas' => 'rendah'];
        }
        if (str_contains($st, 'dry') || str_contains($st, 'kering')) {
            return ['tingkat_minyak' => 'rendah', 'tingkat_kering' => 'tinggi', 'pori_pori' => 'kecil', 'penggunaan_skincare' => 'ya', 'jerawat' => 'tidak', 'sensitivitas' => 'rendah'];
        }
        if (str_contains($st, 'comb') || str_contains($st, 'kombinasi')) {
            return ['tingkat_minyak' => 'tinggi', 'tingkat_kering' => 'tinggi', 'pori_pori' => 'sedang', 'penggunaan_skincare' => 'ya', 'jerawat' => 'tidak', 'sensitivitas' => 'rendah'];
        }
        return ['tingkat_minyak' => 'sedang', 'tingkat_kering' => 'rendah', 'pori_pori' => 'kecil', 'penggunaan_skincare' => 'ya', 'jerawat' => 'tidak', 'sensitivitas' => 'rendah'];
    }

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
        if (str_contains($v, 'tidak') || str_contains($v, 'no') || $v === '0' || $v === 'false') return 'tidak';
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
     * @return array<int, string>
     */
    public function getTemplateHeaders(): array
    {
        return [
            'Nama',
            'No HP',
            'Gender',
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
            ['Lisa', '081234567890', 'Perempuan', 'Sedang', 'Rendah', 'Kecil', 'Ya', 'Tidak', 'Rendah'],
            ['Asep', '081298765432', 'Laki-laki', 'Sedang', 'Rendah', 'Kecil', 'Ya', 'Tidak', 'Rendah'],
            ['Rasya', '082134567891', 'Perempuan', 'Rendah', 'Tinggi', 'Kecil', 'Tidak', 'Tidak', 'Rendah'],
            ['Mita', '085712345678', 'Perempuan', 'Tinggi', 'Rendah', 'Besar', 'Ya', 'Ya', 'Tinggi'],
            ['Kania', '087812345679', 'Perempuan', 'Tinggi', 'Tinggi', 'Sedang', 'Ya', 'Tidak', 'Rendah'],
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
            $filename = 'template_riwayat_konsultasi.csv';
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
        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'template_konsultasi_' . uniqid() . '.xlsx';
        $this->createXlsxFile($tempPath, $headers, $sampleRows);

        return response()->download($tempPath, 'template_riwayat_konsultasi.xlsx', [
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
        <sheet name="Riwayat Konsultasi" sheetId="1" r:id="rId1"/>
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
                $escaped = htmlspecialchars((string)$val, ENT_XML1);
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
