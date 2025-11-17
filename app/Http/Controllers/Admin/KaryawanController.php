<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Jabatan; // <-- Tambahkan Jabatan model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::query()->with(['user', 'jabatan']);

        if ($request->has('search')) {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nip', 'like', '%' . $request->search . '%');
        }

        $karyawans = $query->latest()->paginate(10);

        // Jika ini adalah permintaan AJAX, kembalikan hanya bagian tabelnya saja
        if ($request->ajax()) {
            return view('admin.karyawan.partials.table', compact('karyawans'))->render();
        }

        // Jika bukan, kembalikan halaman lengkap seperti biasa
        return view('admin.karyawan.index', compact('karyawans'));
    }

    public function create()
    {
        // Ambil data jabatan untuk dropdown
        $jabatans = Jabatan::orderBy('nama_jabatan')->get();
        return view('admin.karyawan.create', compact('jabatans'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nip' => 'required|unique:karyawans,nip|max:20',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'jabatan_id' => 'required|exists:jabatans,id', // Validasi jabatan_id
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|string|max:255',
            'tanggal_masuk' => 'required|date',
            'nomor_telepon' => 'nullable|string|max:15',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::transaction(function () use ($request, $validatedData) {
            $user = User::create([
                'name' => $validatedData['nama_lengkap'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'role' => 'pegawai',
            ]);

            if ($request->hasFile('foto')) {
                $validatedData['foto'] = $request->file('foto')->store('karyawan-foto', 'public');
            }

            $karyawanData = $validatedData;
            $karyawanData['user_id'] = $user->id;

            Karyawan::create($karyawanData);
        });

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan dan akun login berhasil ditambahkan.');
    }

    public function edit(Karyawan $karyawan)
    {
        $jabatans = Jabatan::orderBy('nama_jabatan')->get();
        
        // Ambil semua user 'pegawai' yang BELUM memiliki data karyawan (user_id nya null)
        $usersBelumTertaut = User::where('role', 'pegawai')
                                ->whereDoesntHave('karyawan')
                                ->get();

        return view('admin.karyawan.edit', compact('karyawan', 'jabatans', 'usersBelumTertaut'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $validatedData = $request->validate([
            'nip' => 'required|max:20|unique:karyawans,nip,' . $karyawan->id,
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'jabatan_id' => 'required|exists:jabatans,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|string|max:255',
            'tanggal_masuk' => 'required|date',
            'nomor_telepon' => 'nullable|string|max:15',
            'user_id' => 'nullable|exists:users,id', // Validasi untuk user_id
        ]);

        if ($request->hasFile('foto')) {
            if ($karyawan->foto) {
                Storage::delete($karyawan->foto);
            }
            $validatedData['foto'] = $request->file('foto')->store('karyawan-foto', 'public');
        }

        // Tautkan user_id jika dipilih
        if ($request->filled('user_id')) {
            $karyawan->user_id = $request->user_id;
        }

        $karyawan->update($validatedData);

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Karyawan $karyawan)
    {
        DB::transaction(function () use ($karyawan) {
            if ($karyawan->foto) {
                Storage::delete($karyawan->foto);
            }
            $karyawan->user()->delete();
            $karyawan->delete();
        });

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan dan akun login terkait berhasil dihapus.');
    }
}
