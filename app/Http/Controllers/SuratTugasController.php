<?php

namespace App\Http\Controllers;

use App\Models\SuratTugas;
use App\Models\PeneraTugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class SuratTugasController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'penera') {
            $suratTugas = SuratTugas::whereHas('peneras', function ($q) use ($user) {
                $q->where('nama_penera', $user->nama);
            })->get();
        } else {
            $suratTugas = SuratTugas::with('peneras')->latest()->get();
        }

        return view('surat_tugas.index', compact('suratTugas', 'user'));
    }

    public function create()
    {
        return view('surat_tugas.detail', ['suratTugas' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_pesanan' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'uraian_pekerjaan' => 'required|string|max:250',
        ]);

        SuratTugas::create([
            'nomor_pesanan' => $request->nomor_pesanan,
            'tanggal' => $request->tanggal,
            'uraian_pekerjaan' => $request->uraian_pekerjaan,
            'kabiro_kalibrasi' => Auth::user()->nama,
            'status' => 'Draft'
        ]);

        return redirect()->route('surat_tugas.index')
            ->with('success', '✅ Surat Tugas berhasil dibuat!');
    }

    public function show($id)
    {
        $suratTugas = SuratTugas::with('peneras')->findOrFail($id);
        $user = Auth::user();
        return view('surat_tugas.detail', compact('suratTugas', 'user'));
    }

    public function edit($id)
    {
        $suratTugas = SuratTugas::with('peneras')->findOrFail($id);
        return view('surat_tugas.detail', compact('suratTugas'));
    }

    public function update(Request $request, $id)
    {
        $surat = SuratTugas::findOrFail($id);
        $surat->update($request->all());

        return redirect()->route('surat_tugas.index')
            ->with('success', '✅ Surat berhasil diperbarui!');
    }

    public function preview($id)
    {
        $suratTugas = SuratTugas::with('peneras')->findOrFail($id);
        return view('surat_tugas.preview', compact('suratTugas'));
    }

    public function destroy($id)
    {
        SuratTugas::findOrFail($id)->delete();
        return back()->with('success', '🗑️ Surat berhasil dihapus');
    }

    public function downloadPdf($id)
    {
        $suratTugas = SuratTugas::with('peneras')->findOrFail($id);

        $pdf = Pdf::loadView('surat_tugas.preview', compact('suratTugas'))
                ->setPaper('A4', 'portrait');

        return $pdf->download('Surat_Tugas_' . $suratTugas->nomor_pesanan . '.pdf');
    }
}
