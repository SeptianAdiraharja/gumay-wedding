<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GalleryStoreRequest;
use App\Http\Requests\Admin\GalleryUpdateRequest;
use App\Models\Gallery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        $galleries = Gallery::latest()->paginate(12);

        return view('admin.galleries.index', compact('galleries'));
    }

    public function create(): View
    {
        return view('admin.galleries.create');
    }

    public function store(GalleryStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['file_path'] = $request->file('file')->store('galleries', 'public');
        unset($validated['file']);

        Gallery::create($validated);

        return redirect()
            ->route('admin.galleries.index')
            ->with('success', 'Konten galeri berhasil ditambahkan.');
    }

    public function edit(Gallery $gallery): View
    {
        return view('admin.galleries.edit', compact('gallery'));
    }

    public function update(GalleryUpdateRequest $request, Gallery $gallery): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($gallery->file_path);
            $validated['file_path'] = $request->file('file')->store('galleries', 'public');
        }
        unset($validated['file']);

        $gallery->update($validated);

        return redirect()
            ->route('admin.galleries.index')
            ->with('success', 'Konten galeri berhasil diperbarui.');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        Storage::disk('public')->delete($gallery->file_path);
        $gallery->delete();

        return redirect()
            ->route('admin.galleries.index')
            ->with('success', 'Konten galeri berhasil dihapus.');
    }
}