<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $galleries = Gallery::latest()->get()->groupBy('category');

        return view('home', compact('galleries'));
    }
}