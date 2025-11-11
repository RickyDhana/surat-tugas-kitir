<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nama', 'username', 'password', 'role'
    ];

    protected $hidden = [
        'password',
    ];

    public function isRole($role)
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        }
        return $this->role === $role;
    }

    public function getNamaFormalAttribute()
{
    $nama = strtolower($this->nama);
    if (str_contains($nama, 'rino')) {
        return 'Pak Rino';
    } elseif (str_contains($nama, 'rizqi') || str_contains($nama, 'rizky')) {
        return 'Pak Rizqi';
    } elseif (str_contains($nama, 'candra')) {
        return 'Pak Candra';
    }
    return $this->nama;
}
}
