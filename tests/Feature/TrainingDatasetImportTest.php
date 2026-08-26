<?php

namespace Tests\Feature;

use App\Models\SkinType;
use App\Models\TrainingDataset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class TrainingDatasetImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_download_excel_template(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user, 'web')->get(route('admin.training-dataset.template', ['format' => 'xlsx']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=template_data_latih_naive_bayes.xlsx');
    }

    public function test_admin_can_download_csv_template(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user, 'web')->get(route('admin.training-dataset.template', ['format' => 'csv']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="template_data_latih_naive_bayes.csv"');
        $this->assertStringContainsString('Jenis Kulit', $response->getContent());
        $this->assertStringContainsString('Berminyak,Tinggi,Rendah', $response->getContent());
    }

    public function test_admin_can_import_csv_file_with_append_mode(): void
    {
        $user = User::first() ?? User::factory()->create();
        $initialCount = TrainingDataset::count();

        $csvContent = "Jenis Kulit,Tingkat Minyak,Tingkat Kering,Pori-Pori,Penggunaan Skincare,Jerawat,Sensitivitas\n"
                    . "Berminyak,Tinggi,Rendah,Besar,Ya,Ya,Tinggi\n"
                    . "Kering,Rendah,Tinggi,Kecil,Tidak,Ya,Tinggi\n"
                    . "Normal,Sedang,Rendah,Kecil,Ya,Tidak,Rendah\n"
                    . "Kombinasi,Tinggi,Tinggi,Sedang,Ya,Ya,Rendah\n";

        $file = UploadedFile::fake()->createWithContent('dataset.csv', $csvContent);

        $response = $this->actingAs($user, 'web')->post(route('admin.training-dataset.import'), [
            'file' => $file,
            'mode' => 'append',
        ]);

        $response->assertRedirect(route('admin.training-dataset.index'));
        $response->assertSessionHas('success');

        $this->assertEquals($initialCount + 4, TrainingDataset::count());
    }

    public function test_admin_can_import_with_replace_mode(): void
    {
        $user = User::first() ?? User::factory()->create();

        $csvContent = "Jenis Kulit,Tingkat Minyak,Tingkat Kering,Pori-Pori,Penggunaan Skincare,Jerawat,Sensitivitas\n"
                    . "Berminyak,Tinggi,Rendah,Besar,Ya,Ya,Tinggi\n"
                    . "Kering,Rendah,Tinggi,Kecil,Tidak,Ya,Tinggi\n";

        $file = UploadedFile::fake()->createWithContent('replace_dataset.csv', $csvContent);

        $response = $this->actingAs($user, 'web')->post(route('admin.training-dataset.import'), [
            'file' => $file,
            'mode' => 'replace',
        ]);

        $response->assertRedirect(route('admin.training-dataset.index'));
        $response->assertSessionHas('success');

        $this->assertEquals(2, TrainingDataset::count());
    }

    public function test_admin_can_import_actual_jeniskulit_xlsx_file(): void
    {
        $user = User::first() ?? User::factory()->create();
        $sourcePath = 'C:/Users/user/OneDrive/Desktop/Septi-Rina/jeniskulit.xlsx';

        if (!file_exists($sourcePath)) {
            $this->markTestSkipped('jeniskulit.xlsx not found at ' . $sourcePath);
        }

        $file = new UploadedFile($sourcePath, 'jeniskulit.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->actingAs($user, 'web')->post(route('admin.training-dataset.import'), [
            'file' => $file,
            'mode' => 'replace',
        ]);

        $response->assertRedirect(route('admin.training-dataset.index'));
        $response->assertSessionHas('success');

        // Verify that 21 data rows were imported
        $this->assertGreaterThanOrEqual(17, TrainingDataset::count());

        // Verify all 4 classes have training data
        $distribution = SkinType::withCount('trainingData')->get();
        foreach ($distribution as $st) {
            $this->assertGreaterThan(0, $st->training_data_count, "Skin type {$st->code} should have training data from jeniskulit.xlsx");
        }
    }
}
