<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class GantiPasswordController extends Controller
{
    /**
     * Menampilkan halaman form ganti password.
     */
    public function index()
    {
        return view('pegawai.ganti-password.index');
    }

    /**
     * Mengupdate password pengguna melalui permintaan AJAX.
     */
    public function update(Request $request)
    {
        // Validasi input
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        // $user = Auth::user();
        // $user->password = Hash::make($request->password);
        // $user->save();
        $user = \App\Models\User::find(Auth::id());
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password Anda berhasil diubah.'
        ]);
    }
}
