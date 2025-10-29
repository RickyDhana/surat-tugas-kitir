<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kitir;
use App\Models\SuratTugas;

class DashboardController extends Controller
{
    public function index()
    {
        $kitirs = Kitir::latest()->get();
        $suratTugas = SuratTugas::latest()->get();
        return view('dashboard', compact('kitirs', 'suratTugas'));
    }
}
