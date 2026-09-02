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

        $recommendations = MakeupRecommendation::where(
            'skin_type_id',
            $consultation->predicted_skin_type_id
        )->get();

        return view('consultation.result', compact('consultation', 'recommendations'));
    }
}