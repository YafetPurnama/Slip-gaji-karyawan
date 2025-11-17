<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Kehadiran;

class LaporanAbsensiController extends Controller
{
    public function index(Request $request)
    {
        // Ambil filter, jika tidak ada, gunakan bulan & tahun saat ini
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        $search = $request->input('search');
        
        // Ambil dan hitung data absensi
        $laporanAbsensi = $this->ambilDataAbsensi($bulan, $tahun, $search);

        // Jika ini adalah permintaan AJAX, kembalikan hanya bagian tabelnya
        if ($request->ajax()) {
            return view('admin.laporan.absensi.partials.table', compact('laporanAbsensi', 'bulan', 'tahun'))->render();
        }

        // Jika bukan, kembalikan halaman lengkap
        return view('admin.laporan.absensi.index', compact('laporanAbsensi', 'bulan', 'tahun'));
    }

    public function print(Request $request)
    {
        $request->validate([
            'bulan' => 'required',
            'tahun' => 'required|numeric|digits:4',
        ]);

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $search = $request->input('search');
        
        $laporanAbsensi = $this->ambilDataAbsensi($bulan, $tahun, $search);
        
        return view('admin.laporan.absensi.print', compact('laporanAbsensi', 'bulan', 'tahun'));
    }

    private function ambilDataAbsensi($bulan, $tahun, $search = null)
    {
        // 1. Mulai query Karyawan dengan relasi jabatan
        $query = Karyawan::with('jabatan')->orderBy('nama_lengkap');

        // 2. Terapkan filter pencarian jika ada
        if ($search) {
            $query->where('nama_lengkap', 'like', '%' . $search . '%');
        }
        
        $karyawans = $query->get();

        // 3. Untuk setiap karyawan, hitung rekap absensinya
        $karyawans->each(function ($karyawan) use ($bulan, $tahun) {
            $karyawan->jumlah_hadir = Kehadiran::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->where('status_kehadiran', 'Hadir')->count();
            
            $karyawan->jumlah_sakit = Kehadiran::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->where('status_kehadiran', 'Sakit')->count();

            $karyawan->jumlah_alpha = Kehadiran::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->where('status_kehadiran', 'Alpha')->count();
        });

        return $karyawans;
    }
}