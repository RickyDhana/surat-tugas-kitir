<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeneraTugas extends Model
{
    use HasFactory;

    protected $table = 'penera_tugas';

    protected $fillable = [
        'surat_tugas_id',
        'nama_penera',
        'nip',
        'catatan',
        'realisasi_jam_orang',
        'realisasi_mulai',
        'realisasi_selesai',
        // Tanggal-tanggal
        'realisasi_tgl_b1','realisasi_tgl_b2','realisasi_tgl_b3','realisasi_tgl_b4','realisasi_tgl_b5',
        'realisasi_tgl_b6','realisasi_tgl_b7','realisasi_tgl_b8','realisasi_tgl_b9','realisasi_tgl_b10',
        // Nilai N/L
        'realisasi_b1_c1','realisasi_b1_c2','realisasi_b1_r1','realisasi_b1_r2','realisasi_b1_d1','realisasi_b1_d2',
        'realisasi_b2_c1','realisasi_b2_c2','realisasi_b2_r1','realisasi_b2_r2','realisasi_b2_d1','realisasi_b2_d2',
        'realisasi_b3_c1','realisasi_b3_c2','realisasi_b3_r1','realisasi_b3_r2','realisasi_b3_d1','realisasi_b3_d2',
        'realisasi_b4_c1','realisasi_b4_c2','realisasi_b4_r1','realisasi_b4_r2','realisasi_b4_d1','realisasi_b4_d2',
        'realisasi_b5_c1','realisasi_b5_c2','realisasi_b5_r1','realisasi_b5_r2','realisasi_b5_d1','realisasi_b5_d2',
        'realisasi_b6_c1','realisasi_b6_c2','realisasi_b6_r1','realisasi_b6_r2','realisasi_b6_d1','realisasi_b6_d2',
        'realisasi_b7_c1','realisasi_b7_c2','realisasi_b7_r1','realisasi_b7_r2','realisasi_b7_d1','realisasi_b7_d2',
        'realisasi_b8_c1','realisasi_b8_c2','realisasi_b8_r1','realisasi_b8_r2','realisasi_b8_d1','realisasi_b8_d2',
        'realisasi_b9_c1','realisasi_b9_c2','realisasi_b9_r1','realisasi_b9_r2','realisasi_b9_d1','realisasi_b9_d2',
        'realisasi_b10_c1','realisasi_b10_c2','realisasi_b10_r1','realisasi_b10_r2','realisasi_b10_d1','realisasi_b10_d2',
    ];

    public function suratTugas()
    {
        return $this->belongsTo(SuratTugas::class, 'surat_tugas_id');
    }
}
