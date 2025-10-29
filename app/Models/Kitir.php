<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kitir extends Model
{
    use HasFactory;

    protected $fillable = [
        'cal_request_no',
        'tgl_penyelesaian',
        'status',
        'created_by',
        'catatan', // ✅ tambahkan ini
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
