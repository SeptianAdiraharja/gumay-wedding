<?php

use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ConsultationController as AdminConsultationController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\MakeupRecommendationController;
use App\Http\Controllers\Admin\TrainingDatasetController;
use App\Http\Middleware\PreventBackHistoryCache;
use Illuminate\Support\Facades\Route;

// Publik
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/konsultasi', [ConsultationController::class, 'create'])->name('consultation.create');
Route::post('/konsultasi', [ConsultationController::class, 'store'])->name('consultation.store');
Route::get('/konsultasi/{consultation}/hasil', [ConsultationController::class, 'result'])->name('consultation.result');

// Admin - auth
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Admin - protected
    Route::middleware('auth:web', PreventBackHistoryCache::class)->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('konsultasi/template', [AdminConsultationController::class, 'template'])->name('consultations.template');
        Route::post('konsultasi/import', [AdminConsultationController::class, 'import'])->name('consultations.import');
        Route::get('konsultasi', [AdminConsultationController::class, 'index'])->name('consultations.index');
        Route::get('konsultasi/{consultation}', [AdminConsultationController::class, 'show'])->name('consultations.show');
        Route::get('consultations/export', [AdminConsultationController::class, 'export'])->name('consultations.export');

        Route::get('konsultasi/{consultation}/export-pdf', [AdminConsultationController::class, 'exportDetailPdf'])->name('consultations.export-pdf');

        Route::resource('galeri', GalleryController::class)->except('show')->parameters(['galeri' => 'gallery'])->names('galleries');
        Route::resource('rekomendasi', MakeupRecommendationController::class)->except('show')->parameters(['rekomendasi' => 'recommendation'])->names('recommendations');

        Route::get('data-latih/template', [TrainingDatasetController::class, 'template'])->name('training-dataset.template');
        Route::post('data-latih/import', [TrainingDatasetController::class, 'import'])->name('training-dataset.import');
        Route::resource('data-latih', TrainingDatasetController::class)
            ->except('show')
            ->parameters(['data-latih' => 'trainingDataset'])
            ->names('training-dataset');
    });
});