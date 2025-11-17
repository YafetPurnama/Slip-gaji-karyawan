<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Jabatan;
use Illuminate\Http\Request;

class DataGajiController extends Controller
{
    public function index(Request $request)
    {
        // 1. Mulai query Karyawan
        $query = Karyawan::with('jabatan');

        // 2. Terapkan filter pencarian jika ada
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nip', 'like', '%' . $request->search . '%');
        }

        $karyawans = $query->latest()->paginate(10);
        
        // 3. Hitung Gaji Kotor untuk setiap karyawan
        $karyawans->getCollection()->transform(function ($karyawan) {
            if ($karyawan->jabatan instanceof Jabatan) {
                $karyawan->gaji_pokok = $karyawan->jabatan->gaji_pokok;
                $karyawan->tunjangan_transport = $karyawan->jabatan->tunjangan_transport;
                $karyawan->uang_makan = $karyawan->jabatan->uang_makan;
                $karyawan->gaji_kotor = $karyawan->gaji_pokok + $karyawan->tunjangan_transport + $karyawan->uang_makan;
            } else {
                $karyawan->gaji_pokok = 0;
                $karyawan->tunjangan_transport = 0;
                $karyawan->uang_makan = 0;
                $karyawan->gaji_kotor = 0;
            }
            return $karyawan;
        });

        // Jika ini adalah permintaan AJAX, kembalikan hanya bagian tabelnya
        if ($request->ajax()) {
            return view('admin.gaji.partials.table', ['dataGaji' => $karyawans])->render();
        }

        // Jika bukan, kembalikan halaman lengkap
        return view('admin.gaji.index', ['dataGaji' => $karyawans]);
    }
}