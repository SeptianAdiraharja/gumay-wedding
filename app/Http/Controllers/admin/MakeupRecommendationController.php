<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MakeupRecommendationRequest;
use App\Models\MakeupRecommendation;
use App\Models\SkinType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MakeupRecommendationController extends Controller
{
    public function index(): View
    {
        $recommendations = MakeupRecommendation::with('skinType')->latest()->paginate(15);

        return view('admin.recommendations.index', compact('recommendations'));
    }

    public function create(): View
    {
        $skinTypes = SkinType::all();

        return view('admin.recommendations.create', compact('skinTypes'));
    }

    public function store(MakeupRecommendationRequest $request): RedirectResponse
    {
        MakeupRecommendation::create($request->validated());

        return redirect()
            ->route('admin.recommendations.index')
            ->with('success', 'Rekomendasi makeup berhasil ditambahkan.');
    }

    public function edit(MakeupRecommendation $recommendation): View
    {
        $skinTypes = SkinType::all();

        return view('admin.recommendations.edit', compact('recommendation', 'skinTypes'));
    }

    public function update(MakeupRecommendationRequest $request, MakeupRecommendation $recommendation): RedirectResponse
    {
        $recommendation->update($request->validated());

        return redirect()
            ->route('admin.recommendations.index')
            ->with('success', 'Rekomendasi makeup berhasil diperbarui.');
    }

    public function destroy(MakeupRecommendation $recommendation): RedirectResponse
    {
        $recommendation->delete();

        return redirect()
            ->route('admin.recommendations.index')
            ->with('success', 'Rekomendasi makeup berhasil dihapus.');
    }
}