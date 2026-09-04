<?php

namespace Tests\Feature;

use App\Models\MakeupRecommendation;
use App\Models\SkinType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MakeupRecommendationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_view_recommendations_index(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user, 'web')->get(route('admin.recommendations.index'));

        $response->assertStatus(200);
        $response->assertSee('Rekomendasi Makeup');
    }

    public function test_admin_can_create_new_recommendation(): void
    {
        $user = User::first() ?? User::factory()->create();
        $skinType = SkinType::where('code', 'combination')->first();

        // Pastikan kombinasi belum ada
        MakeupRecommendation::where('skin_type_id', $skinType->id)
            ->where('is_acne', true)
            ->where('is_sensitive', false)
            ->delete();

        $payload = [
            'skin_type_id' => $skinType->id,
            'is_acne' => '1',
            'tips_perawatan' => 'Tips perawatan kulit kombinasi berjerawat.',
            'makeup_perempuan' => 'Rekomendasi makeup perempuan.',
            'makeup_laki_laki' => 'Rekomendasi makeup laki-laki.',
        ];

        $response = $this->actingAs($user, 'web')->post(route('admin.recommendations.store'), $payload);

        $response->assertRedirect(route('admin.recommendations.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('makeup_recommendations', [
            'skin_type_id' => $skinType->id,
            'is_acne' => 1,
            'is_sensitive' => 0,
        ]);
    }

    public function test_duplicate_combination_is_rejected_with_validation_error(): void
    {
        $user = User::first() ?? User::factory()->create();
        $skinType = SkinType::where('code', 'combination')->first();

        // Pastikan data pertama ada
        MakeupRecommendation::updateOrCreate(
            [
                'skin_type_id' => $skinType->id,
                'is_acne' => true,
                'is_sensitive' => false,
            ],
            [
                'tips_perawatan' => 'Tips lama',
                'makeup_perempuan' => 'Makeup lama perempuan',
                'makeup_laki_laki' => 'Makeup lama laki-laki',
            ]
        );

        $initialCount = MakeupRecommendation::count();

        // Coba masukkan data kedua dengan kombinasi yang sama persis (skin_type: 4, acne: 1, sens: 0)
        $payload = [
            'skin_type_id' => $skinType->id,
            'is_acne' => '1',
            'tips_perawatan' => 'sadakmd',
            'makeup_perempuan' => 'mslakdml',
            'makeup_laki_laki' => 'kaksdmaklk',
        ];

        $response = $this->actingAs($user, 'web')->post(route('admin.recommendations.store'), $payload);

        // Harus dialihkan kembali dengan error validasi, BUKAN 500 Internal Server Error
        $response->assertSessionHasErrors('skin_type_id');
        $this->assertDatabaseCount('makeup_recommendations', $initialCount);
    }

    public function test_admin_can_update_own_recommendation_without_duplicate_error(): void
    {
        $user = User::first() ?? User::factory()->create();
        $skinType = SkinType::first();

        $recommendation = MakeupRecommendation::firstOrCreate(
            [
                'skin_type_id' => $skinType->id,
                'is_acne' => false,
                'is_sensitive' => false,
            ],
            [
                'tips_perawatan' => 'Tips awal',
                'makeup_perempuan' => 'Makeup awal',
                'makeup_laki_laki' => 'Makeup awal',
            ]
        );

        $payload = [
            'skin_type_id' => $recommendation->skin_type_id,
            'is_acne' => $recommendation->is_acne ? '1' : '0',
            'is_sensitive' => $recommendation->is_sensitive ? '1' : '0',
            'tips_perawatan' => 'Tips yang baru diperbarui',
            'makeup_perempuan' => 'Makeup perempuan baru',
            'makeup_laki_laki' => 'Makeup laki-laki baru',
        ];

        $response = $this->actingAs($user, 'web')->put(route('admin.recommendations.update', $recommendation), $payload);

        $response->assertRedirect(route('admin.recommendations.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('makeup_recommendations', [
            'id' => $recommendation->id,
            'tips_perawatan' => 'Tips yang baru diperbarui',
        ]);
    }

    public function test_admin_cannot_update_recommendation_to_another_existing_combination(): void
    {
        $user = User::first() ?? User::factory()->create();
        $skinTypes = SkinType::all();

        $reco1 = MakeupRecommendation::firstOrCreate(
            ['skin_type_id' => $skinTypes[0]->id, 'is_acne' => false, 'is_sensitive' => false],
            ['tips_perawatan' => 'Tips 1', 'makeup_perempuan' => 'M1', 'makeup_laki_laki' => 'L1']
        );

        $reco2 = MakeupRecommendation::firstOrCreate(
            ['skin_type_id' => $skinTypes[1]->id, 'is_acne' => true, 'is_sensitive' => false],
            ['tips_perawatan' => 'Tips 2', 'makeup_perempuan' => 'M2', 'makeup_laki_laki' => 'L2']
        );

        // Coba ubah reco2 agar menggunakan kombinasi reco1
        $payload = [
            'skin_type_id' => $reco1->skin_type_id,
            'is_acne' => $reco1->is_acne ? '1' : '0',
            'is_sensitive' => $reco1->is_sensitive ? '1' : '0',
            'tips_perawatan' => 'Tips konflik',
            'makeup_perempuan' => 'Makeup konflik',
            'makeup_laki_laki' => 'Makeup konflik',
        ];

        $response = $this->actingAs($user, 'web')->put(route('admin.recommendations.update', $reco2), $payload);

        $response->assertSessionHasErrors('skin_type_id');
    }
}
