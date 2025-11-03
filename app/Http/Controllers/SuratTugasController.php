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

        // jika penera, tampilkan hanya yang ditugaskan kepadanya
        if ($user && $user->isRole('penera')) {
            $suratTugas = SuratTugas::whereHas('peneras', function($q) use ($user) {
                $q->where('nama_penera', $user->nama);
            })->with('peneras')->latest()->get();
        } else {
            $suratTugas = SuratTugas::with('peneras')->latest()->get();
        }

        return view('surat_tugas.index', compact('suratTugas', 'user'));
    }

    public function create()
    {
        // form kosong (kepala biro)
        return view('surat_tugas.detail', ['suratTugas' => null]);
    }

    public function store(Request $request)
    {
        // toleran terhadap nama field (nomor_pesanan atau nomor_surat)
        $payload = $request->all();

        // Normalize: jika user mengirim 'nomor_surat', ubah jadi 'nomor_pesanan'
        if (isset($payload['nomor_surat']) && !isset($payload['nomor_pesanan'])) {
            $payload['nomor_pesanan'] = $payload['nomor_surat'];
        }

        $validated = $request->validate([
            'nomor_pesanan' => 'required|string|max:100',
            'tanggal' => 'nullable|date',
            'nomor_kt' => 'nullable|string|max:100',
            'uraian_pekerjaan' => 'nullable|string|max:250',
            // tambahkan rule lain jika perlu
        ]);

        // Gunakan hasil validasi + allow fallback fields
        $dataToSave = array_merge($validated, [
            'kabiro_kalibrasi' => $payload['kabiro_kalibrasi'] ?? ($request->user()->nama ?? 'Dwi Adi'),
            'status' => $payload['status'] ?? 'Draft',
        ]);

        $surat = SuratTugas::create($dataToSave);

        // Jika ada daftar penera (misal multi-select dari form), simpan relasi (jika form kirim penera[]).
        if ($request->has('penera') && is_array($request->input('penera'))) {
            foreach ($request->input('penera') as $p) {
                // $p bisa berupa array ['nama' => 'Candra', 'nip' => '123']
                if (is_array($p)) {
                    $surat->peneras()->create([
                        'nama_penera' => $p['nama'] ?? null,
                        'nip' => $p['nip'] ?? null,
                    ]);
                } else {
                    // jika hanya nama
                    $surat->peneras()->create([
                        'nama_penera' => $p,
                    ]);
                }
            }
        }

        return redirect()->route('dashboard')->with('surat_message', 'Surat tugas berhasil dibuat.');
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

        $payload = $request->all();
        if (isset($payload['nomor_surat']) && !isset($payload['nomor_pesanan'])) {
            $payload['nomor_pesanan'] = $payload['nomor_surat'];
        }

        $validated = $request->validate([
            'nomor_pesanan' => 'required|string|max:100',
            'tanggal' => 'nullable|date',
            'nomor_kt' => 'nullable|string|max:100',
            'uraian_pekerjaan' => 'nullable|string|max:250',
        ]);

        $surat->update($validated);

        // (Opsional) update peneras jika dikirim — implementasi tergantung form
        return redirect()->route('surat_tugas.index')->with('surat_message', 'Surat tugas diperbarui.');
    }

    public function preview($id)
    {
        $suratTugas = SuratTugas::with('peneras')->findOrFail($id);
        return view('surat_tugas.preview', compact('suratTugas'));
    }

    public function downloadPdf($id)
    {
        $suratTugas = SuratTugas::with('peneras')->findOrFail($id);
        $pdf = Pdf::loadView('surat_tugas.preview', compact('suratTugas'))
            ->setPaper([0, 0, 595.28, 420.94], 'landscape');
        return $pdf->download('Surat_Tugas_' . ($suratTugas->nomor_pesanan ?? $suratTugas->id) . '.pdf');
    }

    public function destroy($id)
    {
        $surat = SuratTugas::findOrFail($id);
        $surat->delete();
        return redirect()->route('surat_tugas.index')->with('surat_message', 'Surat tugas dihapus.');
    }
}
