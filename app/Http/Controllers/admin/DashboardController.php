<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Gallery;
use App\Models\SkinType;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalConsultations = Consultation::count();
        $totalGalleries = Gallery::count();
        $skinTypeDistribution = Consultation::selectRaw('predicted_skin_type_id, count(*) as total')
            ->groupBy('predicted_skin_type_id')
            ->with('predictedSkinType')
            ->get();

        return view('admin.dashboard', compact(
            'totalConsultations',
            'totalGalleries',
            'skinTypeDistribution'
        ));
    }
}