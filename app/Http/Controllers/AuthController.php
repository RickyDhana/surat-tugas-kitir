<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Redirect sesuai role
            if ($user->role === 'admin') {
    return redirect()->route('dashboard');
} elseif ($user->role === 'kepala_biro') {
    return redirect()->route('dashboard');
} else {
    return redirect()->route('dashboard');
}
        }

        return back()->withErrors(['login' => 'Username atau password salah!']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login.form');
    }
}
