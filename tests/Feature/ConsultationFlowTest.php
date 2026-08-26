<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Consultation;
use App\Models\MakeupRecommendation;
use App\Models\SkinType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_consultation_create_page_renders_with_6_questions(): void
    {
        $response = $this->get(route('consultation.create'));

        $response->assertStatus(200);
        $response->assertSee('Cek Jenis Kulit');
        $response->assertSee('penggunaan_skincare');
        $response->assertSee('tingkat_minyak');
        $response->assertSee('tingkat_kering');
        $response->assertSee('pori_pori');
        $response->assertSee('jerawat');
        $response->assertSee('sensitivitas');
    }

    public function test_consultation_submission_for_oily_acne_sensitive(): void
    {
        $payload = [
            'name' => 'Rina',
            'phone' => '08123456789',
            'gender' => 'wanita',
            'tingkat_minyak' => 'tinggi',
            'tingkat_kering' => 'rendah',
            'pori_pori' => 'besar',
            'penggunaan_skincare' => 'ya',
            'jerawat' => 'ya',
            'sensitivitas' => 'tinggi',
        ];

        $response = $this->post(route('consultation.store'), $payload);

        $consultation = Consultation::latest()->first();
        $this->assertNotNull($consultation);
        $response->assertRedirect(route('consultation.result', $consultation->id));

        $this->assertEquals('oily', $consultation->predictedSkinType->code);
        $this->assertEquals('Kulit Berminyak (Berjerawat & Sensitif)', $consultation->full_diagnosis_name);

        $resultPage = $this->get(route('consultation.result', $consultation->id));
        $resultPage->assertStatus(200);
        $resultPage->assertSee('Kulit Berminyak (Berjerawat &amp; Sensitif)', false);
        $resultPage->assertSee('Rekomendasi Formulasi Makeup');
        $resultPage->assertDontSee('Tips Perawatan Kulit');
    }

    public function test_consultation_submission_for_dry_acne_sensitive(): void
    {
        $payload = [
            'name' => 'Dewi',
            'phone' => '08123456780',
            'gender' => 'wanita',
            'tingkat_minyak' => 'rendah',
            'tingkat_kering' => 'tinggi',
            'pori_pori' => 'kecil',
            'penggunaan_skincare' => 'tidak',
            'jerawat' => 'ya',
            'sensitivitas' => 'tinggi',
        ];

        $response = $this->post(route('consultation.store'), $payload);

        $consultation = Consultation::latest()->first();
        $this->assertEquals('dry', $consultation->predictedSkinType->code);
        $this->assertEquals('Kulit Kering (Berjerawat & Sensitif)', $consultation->full_diagnosis_name);

        $resultPage = $this->get(route('consultation.result', $consultation->id));
        $resultPage->assertStatus(200);
        $resultPage->assertSee('Kulit Kering (Berjerawat &amp; Sensitif)', false);
    }

    public function test_consultation_submission_for_combination_acne(): void
    {
        $payload = [
            'name' => 'Maya',
            'phone' => '08123456781',
            'gender' => 'wanita',
            'tingkat_minyak' => 'tinggi',
            'tingkat_kering' => 'tinggi',
            'pori_pori' => 'sedang',
            'penggunaan_skincare' => 'ya',
            'jerawat' => 'ya',
            'sensitivitas' => 'rendah',
        ];

        $response = $this->post(route('consultation.store'), $payload);

        $consultation = Consultation::latest()->first();
        $this->assertEquals('combination', $consultation->predictedSkinType->code);
        $this->assertEquals('Kulit Kombinasi (Berjerawat)', $consultation->full_diagnosis_name);
    }

    public function test_admin_cannot_access_deleted_tips_route(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user, 'web')->get('/admin/tips');
        $response->assertStatus(404);
    }

    public function test_admin_can_view_consultation_show_page_with_detailed_calculations(): void
    {
        $user = User::first() ?? User::factory()->create();
        $consultation = Consultation::create([
            'name' => 'Lisa',
            'gender' => 'wanita',
            'tingkat_minyak' => 'sedang',
            'tingkat_kering' => 'rendah',
            'pori_pori' => 'kecil',
            'penggunaan_skincare' => 'ya',
            'jerawat' => 'tidak',
            'sensitivitas' => 'rendah',
            'predicted_skin_type_id' => SkinType::where('code', 'normal')->first()->id,
        ]);

        $response = $this->actingAs($user, 'web')->get(route('admin.consultations.show', $consultation));

        $response->assertStatus(200);
        $response->assertSee('Perhitungan Naive Bayes');
        $response->assertSee('Matriks Likelihood');
        $response->assertSee('Terpilih (Pemenang)');
    }

    public function test_admin_can_export_single_consultation_to_excel_with_calculations(): void
    {
        $user = User::first() ?? User::factory()->create();
        $consultation = Consultation::create([
            'name' => 'Lisa',
            'gender' => 'wanita',
            'tingkat_minyak' => 'sedang',
            'tingkat_kering' => 'rendah',
            'pori_pori' => 'kecil',
            'penggunaan_skincare' => 'ya',
            'jerawat' => 'tidak',
            'sensitivitas' => 'rendah',
            'predicted_skin_type_id' => SkinType::where('code', 'normal')->first()->id,
        ]);

        $response = $this->actingAs($user, 'web')->get(route('admin.consultations.export-excel', $consultation));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition');
    }

    public function test_admin_can_export_single_consultation_to_pdf(): void
    {
        $user = User::first() ?? User::factory()->create();
        $consultation = Consultation::create([
            'name' => 'Lisa',
            'gender' => 'wanita',
            'tingkat_minyak' => 'sedang',
            'tingkat_kering' => 'rendah',
            'pori_pori' => 'kecil',
            'penggunaan_skincare' => 'ya',
            'jerawat' => 'tidak',
            'sensitivitas' => 'rendah',
            'predicted_skin_type_id' => SkinType::where('code', 'normal')->first()->id,
        ]);

        $response = $this->actingAs($user, 'web')->get(route('admin.consultations.export-pdf', $consultation));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition');
    }
}
