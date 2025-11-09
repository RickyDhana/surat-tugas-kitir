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

        // Jika penera, tampilkan hanya surat tugas yang ditugaskan padanya
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
        $surat = new SuratTugas(); // biar tidak null di view
        return view('surat_tugas.detail', [
            'surat' => $surat,
            'role' => Auth::user()->role ?? null,
            'peneraTugas' => collect()
        ]);
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'nomor_pesanan' => 'required|string|max:100',
        'tanggal' => 'nullable|date',
        'nomor_kt' => 'nullable|string|max:100',
        'uraian_pekerjaan' => 'nullable|string|max:250',
        'rencana_jam_orang' => 'nullable|string|max:100',
        'rencana_mulai' => 'nullable|date',
        'rencana_selesai' => 'nullable|date',
    ]);

    $dataToSave = array_merge($validated, [
        'kabiro_kalibrasi' => $request->user()->nama ?? 'Dwi Adi',
        'status' => 'Draft',
        'tanggal' => now(),
    ]);

    $surat = SuratTugas::create($dataToSave);

    // Simpan daftar penera (jika dipilih)
    if ($request->filled('penera')) {
        foreach ($request->input('penera') as $p) {
            $surat->peneras()->create([
                'nama_penera' => $p,
            ]);
        }
    }

    return redirect()->route('surat_tugas.index')->with('msg', '✅ Surat tugas berhasil dibuat.');
}

    public function show($id)
    {
        $surat = SuratTugas::with('peneras')->findOrFail($id);
        $role = Auth::user()->role ?? null;
        $peneraTugas = $surat->peneras;

        return view('surat_tugas.detail', compact('surat', 'role', 'peneraTugas'));
    }

    public function edit($id)
    {
        $surat = SuratTugas::with('peneras')->findOrFail($id);
        $role = Auth::user()->role ?? null;
        $peneraTugas = $surat->peneras;

        return view('surat_tugas.detail', compact('surat', 'role', 'peneraTugas'));
    }

    public function update(Request $request, $id)
{
    $surat = SuratTugas::findOrFail($id);

    $validated = $request->validate([
        'nomor_pesanan' => 'required|string|max:100',
        'tanggal' => 'nullable|date',
        'nomor_kt' => 'nullable|string|max:100',
        'uraian_pekerjaan' => 'nullable|string|max:250',
        'rencana_jam_orang' => 'nullable|string|max:100',
        'rencana_mulai' => 'nullable|date',
        'rencana_selesai' => 'nullable|date',
    ]);

    $surat->update($validated);

    // Update daftar penera (hapus lama dulu)
    $surat->peneras()->delete();
    if ($request->filled('penera')) {
        foreach ($request->input('penera') as $p) {
            $surat->peneras()->create([
                'nama_penera' => $p,
            ]);
        }
    }

    return redirect()->route('surat_tugas.show', $surat->id)
        ->with('msg', '✅ Data surat tugas berhasil diperbarui.');
}

    public function realisasiUpdate(Request $request, $id)
{
    $surat = SuratTugas::findOrFail($id);
    $user = Auth::user();

    // Cari penera yang sedang login
    $penera = PeneraTugas::where('surat_tugas_id', $id)
        ->where('nama_penera', $user->nama)
        ->first();

    // Jika belum ada, buat baru
    if (!$penera) {
        $penera = new PeneraTugas();
        $penera->surat_tugas_id = $id;
        $penera->nama_penera = $user->nama;
        $penera->nip = $user->nip ?? '-';
    }

    // Ambil semua input realisasi yang ada
    $fields = [
        'catatan', 'realisasi_jam_orang',
        'realisasi_mulai', 'realisasi_selesai'
    ];

    // Tambahkan kolom dinamis B1–B10
    for ($i = 1; $i <= 10; $i++) {
        foreach (['c1','c2','r1','r2','d1','d2'] as $suffix) {
            $fields[] = "realisasi_b{$i}_{$suffix}";
        }
    }

    // Update semua field yang ada di request
    foreach ($fields as $f) {
        if ($request->has($f)) {
            $penera->$f = $request->input($f);
        }
    }

    $penera->save();

    return redirect()->route('surat_tugas.show', $id)
        ->with('msg', '✅ Data realisasi berhasil diperbarui.');
}

    public function preview($id)
    {
        $surat = SuratTugas::with('peneras')->findOrFail($id);
        return view('surat_tugas.preview', compact('surat'));
    }

    public function downloadPdf($id)
    {
        $surat = SuratTugas::with('peneras')->findOrFail($id);
        $pdf = Pdf::loadView('surat_tugas.preview', compact('surat'))
            ->setPaper([0, 0, 595.28, 420.94], 'landscape');

        return $pdf->download('Surat_Tugas_' . ($surat->nomor_pesanan ?? $surat->id) . '.pdf');
    }

    public function destroy($id)
    {
        $surat = SuratTugas::findOrFail($id);
        $surat->delete();

        return redirect()->route('surat_tugas.index')->with('msg', 'Surat tugas dihapus.');
    }
}
