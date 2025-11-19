<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kehadiran;
use App\Models\PotonganGaji;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;
        $karyawan = Auth::user()->karyawan;

        if (!$karyawan || !$karyawan->jabatan) {
            return view('pegawai.dashboard_kosong');
        }

        // 1. Ambil Rekap Absensi Bulan Ini
        $rekapKehadiran = Kehadiran::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->get();

        $jumlahHadir = $rekapKehadiran->where('status_kehadiran', 'Hadir')->count();
        $jumlahSakit = $rekapKehadiran->where('status_kehadiran', 'Sakit')->count();
        $jumlahAlpha = $rekapKehadiran->where('status_kehadiran', 'Alpha')->count();

        // 2. Hitung Rincian Gaji Bulan Ini
        $potonganAlphaSetting = 50000; // Asumsi potongan alpha, bisa dibuat dinamis
        $totalPotonganAlpha = $jumlahAlpha * $potonganAlphaSetting;
        $potonganLainnya = PotonganGaji::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->sum('jumlah');
        $totalSemuaPotongan = $totalPotonganAlpha + $potonganLainnya;

        // Hitung uang lembur
        $jumlahLembur = Kehadiran::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('status_lembur', 'Ya')
            ->count();
        $uangLembur = $jumlahLembur * ($karyawan->jabatan->uang_lembur ?? 0);

        $gajiPokok = $karyawan->jabatan->gaji_pokok;
        $tunjanganTransport = $karyawan->jabatan->tunjangan_transport;
        $uangMakan = $karyawan->jabatan->uang_makan;
        $bpjsKetenagakerjaan = $karyawan->jabatan->uang_bpjs ?? 0;

        $gajiKotor = $gajiPokok + $tunjanganTransport + $uangMakan - $bpjsKetenagakerjaan + $uangLembur;
        $gajiBersih = $gajiKotor - $totalSemuaPotongan;

        $dataGaji = (object) [
            'gaji_pokok' => $gajiPokok,
            'tunjangan_transport' => $tunjanganTransport,
            'uang_makan' => $uangMakan,
            'bpjs_ketenagakerjaan' => $bpjsKetenagakerjaan,
            'uang_lembur' => $uangLembur,
            'jumlah_lembur' => $jumlahLembur,
            'gaji_kotor' => $gajiKotor,
            'uang_makan' => $uangMakan,
            'potongan' => $totalSemuaPotongan,
            'gaji_bersih' => $gajiBersih,
        ];

        return view('pegawai.dashboard', compact(
            'karyawan',
            'dataGaji',
            'jumlahHadir',
            'jumlahSakit',
            'jumlahAlpha'
        ));
    }
}
