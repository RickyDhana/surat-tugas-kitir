<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratTugas extends Model
{
    use HasFactory;

    protected $table = 'surat_tugas';
    protected $fillable = [
        'nomor_pesanan', 'nomor_kt', 'tanggal', 'uraian_pekerjaan',
        'rencana_jam_orang', 'rencana_mulai', 'rencana_selesai',
        'kabiro_kalibrasi', 'status'
    ];

    public function suratTugas()
    {
        return $this->belongsTo(SuratTugas::class, 'surat_tugas_id');
    }
}