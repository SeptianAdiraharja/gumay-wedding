<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsultationStoreRequest;
use App\Models\Consultation;
use App\Models\MakeupRecommendation;
use App\Models\SkinAttribute;
use Database\Seeders\SkinAttributeSeeder;
use App\Services\NaiveBayesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConsultationController extends Controller
{
    public function __construct(private NaiveBayesService $naiveBayes)
    {
    }

    public function create(): View
    {
        $questions = SkinAttribute::all();

        if ($questions->isEmpty()) {
            (new SkinAttributeSeeder())->run();
            $questions = SkinAttribute::all();
        }

        return view('consultation.create', compact('questions'));
    }

    public function store(ConsultationStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('consultations', 'public');
        }

        $result = $this->naiveBayes->classify($validated);

        $consultation = Consultation::create([
            'name' => $validated['name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'gender' => $validated['gender'] ?? 'wanita',
            'photo_path' => $photoPath,
            'tingkat_minyak' => $validated['tingkat_minyak'],
            'tingkat_kering' => $validated['tingkat_kering'],
            'pori_pori' => $validated['pori_pori'],
            'penggunaan_skincare' => $validated['penggunaan_skincare'],
            'jerawat' => $validated['jerawat'],
            'sensitivitas' => $validated['sensitivitas'],
            'predicted_skin_type_id' => $result['skin_type_id'],
            'probability_detail' => $result['probabilities'],
        ]);

        return redirect()->route('consultation.result', $consultation->id);
    }

    public function result(Consultation $consultation): View
    {
        $consultation->load('predictedSkinType');

        // Ambil rekomendasi berdasarkan skin type dan kondisi
        $recommendations = MakeupRecommendation::where('skin_type_id', $consultation->predicted_skin_type_id)
            ->when($consultation->jerawat === 'ya', function ($query) {
                return $query->where('is_acne', true);
            })
            ->when($consultation->jerawat === 'tidak', function ($query) {
                return $query->where('is_acne', false);
            })
            ->when($consultation->sensitivitas === 'tinggi', function ($query) {
                return $query->where('is_sensitive', true);
            })
            ->when($consultation->sensitivitas !== 'tinggi', function ($query) {
                return $query->where('is_sensitive', false);
            })
            ->get();

        // Jika tidak ada rekomendasi spesifik, ambil rekomendasi general (is_acne = false, is_sensitive = false)
        if ($recommendations->isEmpty()) {
            $recommendations = MakeupRecommendation::where('skin_type_id', $consultation->predicted_skin_type_id)
                ->where('is_acne', false)
                ->where('is_sensitive', false)
                ->get();
        }

        // Filter rekomendasi berdasarkan gender
        $filteredRecommendations = $recommendations->map(function ($rec) use ($consultation) {
            // Jika gender pria, tampilkan makeup_laki_laki
            if ($consultation->gender === 'pria') {
                $rec->makeup_text = $rec->makeup_laki_laki;
                $rec->makeup_label = 'Rekomendasi Makeup & Skincare (Pria)';
                $rec->makeup_icon = '🧔';
            } else {
                // Default untuk wanita
                $rec->makeup_text = $rec->makeup_perempuan;
                $rec->makeup_label = 'Rekomendasi Makeup (Wanita)';
                $rec->makeup_icon = '💄';
            }
            return $rec;
        });

        return view('consultation.result', compact('consultation', 'filteredRecommendations'));
    }
}