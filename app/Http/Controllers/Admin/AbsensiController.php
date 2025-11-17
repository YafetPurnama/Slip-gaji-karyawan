<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Kehadiran;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        $search = $request->input('search');
        $hariIni = Carbon::today()->toDateString();

        $absensiHariIni = Kehadiran::where('tanggal', $hariIni)
            ->pluck('status_kehadiran', 'karyawan_id')
            ->all();

        // 1. Mulai query Karyawan
        $query = Karyawan::with('jabatan')->orderBy('nama_lengkap');

        // 2. Terapkan filter pencarian jika ada
        if ($search) {
            $query->where('nama_lengkap', 'like', '%' . $search . '%');
        }
        
        $rekapAbsensi = $query->get();

        // 3. Hitung rekap absensi untuk setiap karyawan yang ditemukan
        $rekapAbsensi->each(function ($karyawan) use ($bulan, $tahun) {
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

        // Jika ini adalah permintaan AJAX, kembalikan hanya bagian tabelnya saja
        if ($request->ajax()) {
            return view('admin.absensi.partials.table', compact('rekapAbsensi', 'absensiHariIni'))->render();
        }
            
        return view('admin.absensi.index', compact('rekapAbsensi', 'absensiHariIni', 'bulan', 'tahun'));
    }

    public function store(Request $request)
    {
        $request->validate(['status' => 'required|array']);

        $tanggal = Carbon::today()->toDateString();
        $statuses = $request->input('status');

        foreach ($statuses as $karyawanId => $status) {
            Kehadiran::updateOrCreate(
                ['karyawan_id' => $karyawanId, 'tanggal' => $tanggal],
                ['status_kehadiran' => $status]
            );
        }

        return redirect()->back()->with('success', 'Data absensi hari ini berhasil disimpan/diperbarui.');
    }
}