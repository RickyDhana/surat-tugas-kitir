<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeneraTugas extends Model
{
    use HasFactory;

    protected $table = 'penera_tugas';
    protected $fillable = [
        'surat_tugas_id', 'nama_penera', 'nip',
        'realisasi_tanggal', 'catatan',
        'realisasi_jam_orang', 'realisasi_mulai', 'realisasi_selesai'
    ];

    public function suratTugas()
    {
        return $this->belongsTo(SuratTugas::class);
    }
}
