<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menampilkan form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Menangani proses login.
     */
    public function login(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba untuk melakukan otentikasi
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            // 3. Tentukan URL redirect berdasarkan peran
            $redirectUrl = $user->role === 'admin' 
                ? route('admin.dashboard') 
                : route('pegawai.dashboard');

            // 4. Jika ini permintaan AJAX, kirim URL redirect dalam format JSON
            if ($request->ajax()) {
                return response()->json(['redirect' => $redirectUrl]);
            }

            // Jika bukan, lakukan redirect biasa
            return redirect()->intended($redirectUrl);
        }

        // 5. Jika login gagal
        $errorMessage = 'Email atau password yang Anda masukkan salah.';

        // Jika ini permintaan AJAX, kirim pesan error dalam format JSON
        if ($request->ajax()) {
            return response()->json(['message' => $errorMessage], 422); // 422 Unprocessable Entity
        }

        // Jika bukan, kembalikan ke halaman login dengan pesan error
        return back()->withErrors(['email' => $errorMessage])->onlyInput('email');
    }

    /**
     * Menangani proses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}