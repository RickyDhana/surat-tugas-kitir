<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KitirStep extends Model
{
    use HasFactory;

    protected $table = 'kitir_steps';

    protected $fillable = [
        'kitir_id',
        'step_no',
        'posisi',
        'tanggal',
        'waktu',
        'paraf',
        'user_id'
    ];

    public function kitir()
    {
        return $this->belongsTo(Kitir::class, 'kitir_id');
    }
}
