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
    'tanggal' => $request->tanggal ?? now(),
    ]);

    $surat = SuratTugas::create($dataToSave);

    // Simpan daftar penera (jika dipilih)
    if ($request->filled('penera')) {
    // Mapping nama -> NIP
    $nipMap = [
        'Pak Candra' => '105184526',
        'Pak Rizqi'  => '022106772',
        'Pak Rino'   => '221017000',
    ];

    foreach ($request->input('penera') as $p) {
        // Normalisasi nama agar format seragam (misal "penera rino" jadi "Pak Rino")
        $nama = strtolower(trim($p));
        if (str_contains($nama, 'candra')) {
            $namaPenera = 'Pak Candra';
        } elseif (str_contains($nama, 'rizqi') || str_contains($nama, 'rizky')) {
            $namaPenera = 'Pak Rizqi';
        } elseif (str_contains($nama, 'rino')) {
            $namaPenera = 'Pak Rino';
        } else {
            $namaPenera = $p;
        }

        $surat->peneras()->create([
            'nama_penera' => $namaPenera,
            'nip' => $nipMap[$namaPenera] ?? '-', // otomatis isi NIP sesuai nama
        ]);
        }
    }

    return redirect()->route('dashboard')->with('msg', '✅ Surat tugas berhasil dibuat.');
}

public function show($id)
{
    $surat = SuratTugas::with('peneras')->findOrFail($id);
    $role = Auth::user()->role ?? null;
    $user = Auth::user();

    // Normalisasi nama login agar cocok dengan database
    $namaLogin = strtolower(trim($user->nama));
    if (str_contains($namaLogin, 'rino')) {
        $namaPenera = 'Pak Rino';
    } elseif (str_contains($namaLogin, 'rizqi') || str_contains($namaLogin, 'rizky')) {
        $namaPenera = 'Pak Rizqi';
    } elseif (str_contains($namaLogin, 'candra')) {
        $namaPenera = 'Pak Candra';
    } else {
        $namaPenera = $user->nama; // fallback
    }

    // Cek data penera dari relasi surat_tugas
    $peneraTugas = $surat->peneras;
    $peneraUser = $peneraTugas->where('nama_penera', $namaPenera)->first();

    // Jika belum ada data penera, buat instance kosong (biar view gak error)
    if (!$peneraUser) {
        $peneraUser = new \App\Models\PeneraTugas([
            'nama_penera' => $namaPenera,
            'nip' => match($namaPenera) {
                'Pak Candra' => '105184526',
                'Pak Rizqi'  => '022106772',
                'Pak Rino'   => '221017000',
                default => '-',
            },
        ]);
    }

    return view('surat_tugas.detail', compact('surat', 'role', 'peneraTugas', 'peneraUser'));
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

    // 🧩 Normalisasi nama penera
    $nama = strtolower(trim($user->nama));
    if (str_contains($nama, 'rino')) {
        $namaPenera = 'Pak Rino';
    } elseif (str_contains($nama, 'rizqi') || str_contains($nama, 'rizky')) {
        $namaPenera = 'Pak Rizqi';
    } elseif (str_contains($nama, 'candra')) {
        $namaPenera = 'Pak Candra';
    } else {
        $namaPenera = 'Pak Rizqi';
    }

    // 🧩 Ambil atau buat record penera
    $penera = PeneraTugas::firstOrNew([
        'surat_tugas_id' => $id,
        'nama_penera' => $namaPenera,
    ]);

    // ✅ Simpan hanya jika field diisi (biar tidak ketimpa null)
    for ($i = 1; $i <= 10; $i++) {
        $key = "realisasi_tgl_b{$i}";
        if ($request->filled($key)) {
            $penera->$key = $request->input($key);
        }
    }

    // ✅ Simpan N/L (prefix tiap penera)
    $mapPrefix = [
        'Pak Candra' => 'c',
        'Pak Rizqi'  => 'r',
        'Pak Rino'   => 'd',
    ];
    $prefix = $mapPrefix[$namaPenera] ?? null;

    if ($prefix) {
        for ($i = 1; $i <= 10; $i++) {
            foreach ([1, 2] as $n) {
                $field = "realisasi_b{$i}_{$prefix}{$n}";
                if ($request->filled($field)) {
                    $penera->$field = $request->input($field);
                }
            }
        }
    }

    // ✅ Update catatan dan bagian realisasi umum hanya jika ada isinya
    foreach (['catatan', 'realisasi_jam_orang', 'realisasi_mulai', 'realisasi_selesai'] as $field) {
        if ($request->filled($field)) {
            $penera->$field = $request->input($field);
        }
    }

    // ✅ Isi NIP otomatis
    $nipMap = [
        'Pak Candra' => '105184526',
        'Pak Rizqi'  => '022106772',
        'Pak Rino'   => '221017000',
    ];
    $penera->nip = $nipMap[$namaPenera] ?? '-';

    // 💾 Simpan data
    $penera->save();

    return redirect()
        ->route('surat_tugas.show', $id)
        ->with('msg', "✅ Data realisasi untuk {$namaPenera} berhasil diperbarui tanpa menghapus data lain.");
}


public function preview(Request $request, $id)
{
    $surat = SuratTugas::findOrFail($id);

    // Ambil semua penera untuk surat ini
    $peneraTugas = \App\Models\PeneraTugas::where('surat_tugas_id', $id)->get();

    // Mapping NIP agar tetap muncul walau NULL
    $nipMap = [
        'Pak Candra' => '105184526',
        'Pak Rizqi'  => '022106772',
        'Pak Rino'   => '221017000',
        'Penera Rino' => '221017000',
    ];

    foreach ($peneraTugas as $p) {
        if (empty($p->nip)) {
            $p->nip = $nipMap[$p->nama_penera] ?? '-';
        }
    }

    // 🧠 Cek apakah ada parameter download
    if ($request->query('download') === 'true') {
        // ✅ Generate PDF pakai DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('surat_tugas.preview', compact('surat', 'peneraTugas'))
            ->setPaper([0, 0, 595.28, 842], 'portrait'); // A4 ukuran vertikal

        // ✅ Kembalikan file download
        return $pdf->download('Surat_Tugas_' . ($surat->nomor_pesanan ?? $surat->id) . '.pdf');
    }

    // Kalau tidak ada ?download=true → tampilkan preview biasa
    return view('surat_tugas.preview', compact('surat', 'peneraTugas'));
}


        public function downloadPdf($id)
    {
        $surat = SuratTugas::with('peneras')->findOrFail($id);
        $peneraTugas = $surat->peneras;

        // ✅ gunakan view yang sama dengan preview
        $pdf = Pdf::loadView('surat_tugas.preview', compact('surat', 'peneraTugas'))
            ->setPaper([0, 0, 595.28, 420.94], 'landscape');

        // download file dengan nama rapi
        return $pdf->download('Surat_Tugas_' . ($surat->nomor_pesanan ?? $surat->id) . '.pdf');
    }

    public function destroy($id)
    {
        $surat = SuratTugas::findOrFail($id);
        $surat->delete();

        return redirect()->route('surat_tugas.index')->with('msg', 'Surat tugas dihapus.');
    }
}
