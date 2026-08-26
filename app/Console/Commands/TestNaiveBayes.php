<?php

namespace App\Console\Commands;

use App\Models\SkinType;
use App\Services\NaiveBayesService;
use Illuminate\Console\Command;

class TestNaiveBayes extends Command
{
    protected $signature = 'naivebayes:test
        {tingkat_minyak : rendah|sedang|tinggi}
        {tingkat_kering : rendah|sedang|tinggi}
        {pori_pori : kecil|sedang|besar}
        {penggunaan_skincare : ya|tidak}
        {jerawat : ya|tidak}
        {sensitivitas : rendah|sedang|tinggi}';

    protected $description = 'Uji klasifikasi Naive Bayes dengan input manual dari terminal';

    public function handle(NaiveBayesService $service): int
    {
        $input = [
            'tingkat_minyak' => $this->argument('tingkat_minyak'),
            'tingkat_kering' => $this->argument('tingkat_kering'),
            'pori_pori' => $this->argument('pori_pori'),
            'penggunaan_skincare' => $this->argument('penggunaan_skincare'),
            'jerawat' => $this->argument('jerawat'),
            'sensitivitas' => $this->argument('sensitivitas'),
        ];

        $result = $service->classify($input);
        $predicted = SkinType::find($result['skin_type_id']);

        $this->newLine();
        $this->info('Input: ' . json_encode($input));
        $this->newLine();

        $this->table(
            ['Jenis Kulit', 'Probabilitas'],
            collect($result['probabilities'])->map(fn ($prob, $code) => [
                $code,
                number_format($prob * 100, 2) . '%',
            ])->values()
        );

        $this->newLine();
        $this->info('>> Hasil klasifikasi: ' . ($predicted->name ?? '-'));
        $this->newLine();

        return self::SUCCESS;
    }
}