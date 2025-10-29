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
        return $this->role === $role;
    }
}
