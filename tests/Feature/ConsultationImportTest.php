<?php

namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ConsultationImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    public function test_admin_can_download_consultation_excel_template(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user, 'web')->get(route('admin.consultations.template', ['format' => 'xlsx']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=template_riwayat_konsultasi.xlsx');
    }

    public function test_admin_can_download_consultation_csv_template(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user, 'web')->get(route('admin.consultations.template', ['format' => 'csv']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="template_riwayat_konsultasi.csv"');
        $this->assertStringContainsString('Nama', $response->getContent());
        $this->assertStringContainsString('Tingkat Minyak', $response->getContent());
    }

    public function test_admin_can_import_consultations_csv_file_with_append_mode(): void
    {
        $user = User::first() ?? User::factory()->create();
        $initialCount = Consultation::count();

        $csvContent = "Nama,No HP,Gender,Tingkat Minyak,Tingkat Kering,Pori-Pori,Penggunaan Skincare,Jerawat,Sensitivitas\n"
                    . "Dewi,081234567890,Perempuan,Tinggi,Rendah,Besar,Ya,Ya,Tinggi\n"
                    . "Budi,081298765432,Laki-laki,Rendah,Tinggi,Kecil,Tidak,Tidak,Rendah\n";

        $file = UploadedFile::fake()->createWithContent('consultations.csv', $csvContent);

        $response = $this->actingAs($user, 'web')->post(route('admin.consultations.import'), [
            'file' => $file,
            'mode' => 'append',
        ]);

        $response->assertRedirect(route('admin.consultations.index'));
        $response->assertSessionHas('success');

        $this->assertEquals($initialCount + 2, Consultation::count());

        $dewi = Consultation::where('name', 'Dewi')->first();
        $this->assertNotNull($dewi);
        $this->assertEquals('wanita', $dewi->gender);
        $this->assertNotNull($dewi->predicted_skin_type_id);
        $this->assertEquals('oily', $dewi->predictedSkinType->code);
    }

    public function test_admin_can_import_consultations_with_replace_mode(): void
    {
        $user = User::first() ?? User::factory()->create();
        Consultation::create([
            'name' => 'Old Visitor',
            'gender' => 'wanita',
            'tingkat_minyak' => 'sedang',
            'tingkat_kering' => 'rendah',
            'pori_pori' => 'kecil',
            'penggunaan_skincare' => 'ya',
            'jerawat' => 'tidak',
            'sensitivitas' => 'rendah',
        ]);

        $csvContent = "Nama,No HP,Gender,Tingkat Minyak,Tingkat Kering,Pori-Pori,Penggunaan Skincare,Jerawat,Sensitivitas\n"
                    . "Siti,081234567890,Perempuan,Sedang,Rendah,Kecil,Ya,Tidak,Rendah\n";

        $file = UploadedFile::fake()->createWithContent('replace_consultations.csv', $csvContent);

        $response = $this->actingAs($user, 'web')->post(route('admin.consultations.import'), [
            'file' => $file,
            'mode' => 'replace',
        ]);

        $response->assertRedirect(route('admin.consultations.index'));
        $response->assertSessionHas('success');

        $this->assertEquals(1, Consultation::count());
        $this->assertEquals('Siti', Consultation::first()->name);
    }

    public function test_admin_can_import_actual_cek_xlsx_file_with_photos(): void
    {
        $user = User::first() ?? User::factory()->create();
        $sourcePath = 'C:/Users/user/OneDrive/Desktop/Septi-Rina/cek.xlsx';

        if (!file_exists($sourcePath)) {
            $this->markTestSkipped('cek.xlsx not found at ' . $sourcePath);
        }

        $file = new UploadedFile($sourcePath, 'cek.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->actingAs($user, 'web')->post(route('admin.consultations.import'), [
            'file' => $file,
            'mode' => 'replace',
        ]);

        $response->assertRedirect(route('admin.consultations.index'));
        $response->assertSessionHas('success');

        // Verify that at least 8 consultation rows were imported
        $this->assertGreaterThanOrEqual(8, Consultation::count());

        // Verify names like Lisa, Asep, Rasya, Mita, Kania exist
        $this->assertNotNull(Consultation::where('name', 'LIKE', '%Lisa%')->first());
        $this->assertNotNull(Consultation::where('name', 'LIKE', '%Asep%')->first());
        $this->assertNotNull(Consultation::where('name', 'LIKE', '%rasya%')->first());
        $this->assertNotNull(Consultation::where('name', 'LIKE', '%Mita%')->first());
        $this->assertNotNull(Consultation::where('name', 'LIKE', '%Kania%')->first());

        // Verify some consultations have photo_path extracted
        $withPhoto = Consultation::whereNotNull('photo_path')->get();
        $this->assertNotEmpty($withPhoto);
    }
}
