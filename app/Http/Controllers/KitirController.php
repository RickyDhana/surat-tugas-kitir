<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kitir;
use App\Models\KitirStep;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class KitirController extends Controller
{
    public function index()
    {
        $kitirs = Kitir::orderBy('id', 'desc')->get();
        return view('kitir.index', compact('kitirs'));
    }

    public function create()
    {
        return view('kitir.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cal_request_no' => 'required|string|max:100',
        ]);

        $createdBy = Auth::check() ? Auth::id() : 1;

        $kitir = Kitir::create([
            'cal_request_no'   => $request->cal_request_no,
            'tgl_penyelesaian' => null,
            'status'           => 'draft',
            'created_by'       => $createdBy,
        ]);

        for ($i = 1; $i <= 7; $i++) {
            KitirStep::create([
                'kitir_id' => $kitir->id,
                'step_no'  => $i,
                'posisi'   => 'Y1',
            ]);

            KitirStep::create([
                'kitir_id' => $kitir->id,
                'step_no'  => $i,
                'posisi'   => 'Y2',
            ]);
        }

        return redirect()->route('dashboard')->with('message', 'Kitir baru berhasil dibuat!');
    }

    public function show($id)
    {
        $kitir = Kitir::findOrFail($id);
        $steps = KitirStep::where('kitir_id', $kitir->id)->get()->groupBy('step_no');

        $proses = [
            1 => 'Bag. Penerimaan alat kirim Cal. Req ke AM. Kalibrasi',
            2 => 'AM. Kalibrasi mendistribusikan KT ke Penera untuk proses Kalibrasi',
            3 => 'Penera menyerahkan draft kalibrasi ke bagian pengetikan',
            4 => 'Verifikasi hasil pengetikan dan tanda tangan oleh Penera',
            5 => 'Penera memverifikasi Calibrator Report dan tanda tangan oleh Karo Kalibrasi',
            6 => 'Sertifikat Kalibrasi yang telah ditandatangani AM. Kalibrasi diserahkan ke Bagian Pelayanan Jasa',
            7 => 'Pelayanan Jasa menyerahkan memo kalibrasi ke User',
        ];

        return view('kitir.detail', compact('kitir', 'steps', 'proses'));
    }

     public function preview($id)
    {
        $kitir = Kitir::findOrFail($id);
        $steps = KitirStep::where('kitir_id', $kitir->id)->get()->groupBy('step_no');

        $proses = [
            1 => 'Bag. Penerimaan alat kirim Cal. Req ke AM. Kalibrasi',
            2 => 'AM. Kalibrasi mendistribusikan KT ke Penera untuk proses Kalibrasi',
            3 => 'Penera menyerahkan draft kalibrasi ke bagian pengetikan',
            4 => 'Verifikasi hasil pengetikan dan tanda tangan oleh Penera',
            5 => 'Penera memverifikasi Calibrator Report dan tanda tangan oleh Karo Kalibrasi',
            6 => 'Sertifikat Kalibrasi yang telah ditandatangani AM. Kalibrasi diserahkan ke Bagian Pelayanan Jasa',
            7 => 'Pelayanan Jasa menyerahkan memo kalibrasi ke User',
        ];

        return view('kitir.preview', compact('kitir', 'steps', 'proses'));
    }

    public function storeCatatan(Request $request, $id)
{
    $request->validate([
        'catatan' => 'nullable|string|max:1000',
    ]);

    $kitir = Kitir::findOrFail($id);
    $kitir->catatan = $request->catatan;
    $kitir->save();

    return redirect()->route('kitir.show', $id)->with('success', 'Catatan berhasil disimpan.');
}

     public function downloadPdf($id)
{
    $kitir = Kitir::findOrFail($id);
    $steps = \App\Models\KitirStep::where('kitir_id', $kitir->id)->get()->groupBy('step_no');

    $proses = [
        1 => 'Bag. Penerimaan alat kirim Cal. Req ke AM. Kalibrasi',
        2 => 'AM. Kalibrasi mendistribusikan KT ke Penera untuk proses Kalibrasi',
        3 => 'Penera menyerahkan draft kalibrasi ke bagian pengetikan',
        4 => 'Verifikasi hasil pengetikan dan tanda tangan oleh Penera',
        5 => 'Penera memverifikasi Calibrator Report dan tanda tangan oleh Karo Kalibrasi',
        6 => 'Sertifikat Kalibrasi yang telah ditandatangani AM. Kalibrasi diserahkan ke Bagian Pelayanan Jasa',
        7 => 'Pelayanan Jasa menyerahkan memo kalibrasi ke User',
    ];

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('kitir.preview', [
        'kitir' => $kitir,
        'steps' => $steps,
        'proses' => $proses,
        'isPdf' => true,
    ])->setPaper([0, 0, 595.28, 420.94], 'landscape'); // A5 Landscape (1 halaman pas)

    return $pdf->download('Kartu_Kalibrasi_' . $kitir->cal_request_no . '.pdf');
}


    public function updateCatatan(Request $request, $id)
    {
        $kitir = Kitir::findOrFail($id);
        $kitir->update(['catatan' => $request->catatan]);
        return back()->with('success', 'Catatan berhasil disimpan.');
    }

    public function storeStep(Request $request, $id)
    {
        $request->validate([
            'step_id' => 'required|integer|exists:kitir_steps,id',
        ]);

        $step = KitirStep::findOrFail($request->step_id);
        $user = Auth::user();

        // Pastikan step memang milik kitir yang sesuai (safety)
        if ($step->kitir_id != $id) {
            return back()->with('error', 'Step tidak sesuai dengan Kitir yang dipilih.');
        }

        // Hak akses per role (seperti sebelumnya)
        $allowed = [
            'admin' => [
                ['step' => 1, 'pos' => 'Y1'], ['step' => 1, 'pos' => 'Y2'],
                ['step' => 6, 'pos' => 'Y2'],
                ['step' => 7, 'pos' => 'Y1'], ['step' => 7, 'pos' => 'Y2'],
            ],
            'kepala_biro' => [
                ['step' => 2, 'pos' => 'Y1'],
                ['step' => 5, 'pos' => 'Y2'],
                ['step' => 6, 'pos' => 'Y1'],
            ],
            'penera' => [
                ['step' => 2, 'pos' => 'Y2'],
                ['step' => 3, 'pos' => 'Y1'], ['step' => 3, 'pos' => 'Y2'],
                ['step' => 4, 'pos' => 'Y1'], ['step' => 4, 'pos' => 'Y2'],
                ['step' => 5, 'pos' => 'Y1'],
            ],
        ];

        // Cek role boleh isi step ini
        $canFill = false;
        foreach ($allowed[$user->role] ?? [] as $rule) {
            if ($rule['step'] == $step->step_no && $rule['pos'] == $step->posisi) {
                $canFill = true;
                break;
            }
        }
        if (!$canFill) {
            return back()->with('error', 'Anda tidak berhak menyelesaikan langkah ini.');
        }

        // Cek sudah diisi sebelumnya
        if ($step->tanggal !== null) {
            return back()->with('error', 'Step ini sudah diselesaikan.');
        }

        // === LOGIKA URUTAN KETAT ===
        // Jika posisi Y1: pastikan Y2 dari langkah sebelumnya sudah selesai (kecuali langkah 1)
        if ($step->posisi === 'Y1') {
            if ($step->step_no > 1) {
                $prevY2 = KitirStep::where('kitir_id', $step->kitir_id)
                    ->where('step_no', $step->step_no - 1)
                    ->where('posisi', 'Y2')
                    ->first();

                if (!$prevY2 || $prevY2->tanggal === null) {
                    return back()->with('error', 'Langkah sebelumnya belum selesai. Lengkapi langkah sebelumnya terlebih dahulu.');
                }
            }
        }

        // Jika posisi Y2: pastikan Y1 dari langkah yang sama sudah selesai
        if ($step->posisi === 'Y2') {
            $currY1 = KitirStep::where('kitir_id', $step->kitir_id)
                ->where('step_no', $step->step_no)
                ->where('posisi', 'Y1')
                ->first();

            if (!$currY1 || $currY1->tanggal === null) {
                return back()->with('error', 'Kolom "Diserahkan" (Y1) harus diselesaikan terlebih dahulu sebelum "Diterima" (Y2).');
            }
        }

        // Semua pengecekan OK -> simpan timestamp, waktu, paraf, user_id
        $step->update([
            'tanggal' => Carbon::now()->toDateString(),
            'waktu'   => Carbon::now()->toTimeString(),
            'paraf'   => $user->nama ?? $user->username,
            'user_id' => $user->id,
        ]);

        // Update status kitir (in_progress / selesai) dan tgl_penyelesaian jika lengkap
        $kitir = Kitir::find($step->kitir_id);
        $totalSteps = KitirStep::where('kitir_id', $kitir->id)->count();
        $filledSteps = KitirStep::where('kitir_id', $kitir->id)->whereNotNull('tanggal')->count();

        if ($totalSteps == $filledSteps) {
            $kitir->update([
                'status' => 'selesai',
                'tgl_penyelesaian' => Carbon::now()->toDateString(),
            ]);
        } else {
            $kitir->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'Step berhasil disimpan!');
    }


}
