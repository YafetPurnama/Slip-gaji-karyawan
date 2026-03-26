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
        $now = Carbon::now(); // Tanggal dan waktu saat ini
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;
        $karyawan = Auth::user()->karyawan;

        if (!$karyawan || !$karyawan->jabatan) {
            return view('pegawai.dashboard_kosong');
        }

        $isAkhirBulan = $now->isLastOfMonth(); // Cek apakah hari ini adalah hari terakhir bulan ini

        // 1. Ambil Rekap Absensi Bulan Ini
        $rekapKehadiran = Kehadiran::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->get();

        $jumlahHadir = $rekapKehadiran->where('status_kehadiran', 'Hadir')->count();
        $jumlahSakit = $rekapKehadiran->where('status_kehadiran', 'Sakit')->count();
        $jumlahAlpha = $rekapKehadiran->where('status_kehadiran', 'Alpha')->count();

        // 2. Hitung Rincian Gaji Bulan Ini
        $dataGaji = null;
        if ($isAkhirBulan) {
            $potonganAlphaSetting = 50000;
            $totalPotonganAlpha = $jumlahAlpha * $potonganAlphaSetting;
            $potonganLainnya = PotonganGaji::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', $bulanIni)
                ->whereYear('tanggal', $tahunIni)
                ->sum('jumlah');

            $bpjsKetenagakerjaan = $karyawan->jabatan->uang_bpjs ?? 0;
            $totalSemuaPotongan = $totalPotonganAlpha + $potonganLainnya + $bpjsKetenagakerjaan;

            $jumlahLembur = $rekapKehadiran->where('status_lembur', 'Ya')->count();
            $uangLembur = $jumlahLembur * ($karyawan->jabatan->uang_lembur ?? 0);

            $gajiPokok = $karyawan->jabatan->gaji_pokok;
            $tunjanganTransport = $karyawan->jabatan->tunjangan_transport;
            $uangMakan = $karyawan->jabatan->uang_makan;

            $gajiKotor = $gajiPokok + $tunjanganTransport + $uangMakan + $uangLembur;
            $gajiBersih = $gajiKotor - $totalSemuaPotongan;

            $dataGaji = (object) [
                'gaji_pokok' => $gajiPokok,
                'tunjangan_transport' => $tunjanganTransport,
                'uang_makan' => $uangMakan,
                'bpjs_ketenagakerjaan' => $bpjsKetenagakerjaan,
                'uang_lembur' => $uangLembur,
                'jumlah_lembur' => $jumlahLembur,
                'gaji_kotor' => $gajiKotor,
                'potongan' => $totalSemuaPotongan,
                'gaji_bersih' => $gajiBersih,
            ];
        }

        // $potonganAlphaSetting = 50000;
        // $totalPotonganAlpha = $jumlahAlpha * $potonganAlphaSetting;
        // $potonganLainnya = PotonganGaji::where('karyawan_id', $karyawan->id)
        //     ->whereMonth('tanggal', $bulanIni)
        //     ->whereYear('tanggal', $tahunIni)
        //     ->sum('jumlah');
        // $totalSemuaPotongan = $totalPotonganAlpha + $potonganLainnya;

        // // Hitung uang lembur
        // $jumlahLembur = Kehadiran::where('karyawan_id', $karyawan->id)
        //     ->whereMonth('tanggal', $bulanIni)
        //     ->whereYear('tanggal', $tahunIni)
        //     ->where('status_lembur', 'Ya')
        //     ->count();
        // $uangLembur = $jumlahLembur * ($karyawan->jabatan->uang_lembur ?? 0);

        // $gajiPokok = $karyawan->jabatan->gaji_pokok;
        // $tunjanganTransport = $karyawan->jabatan->tunjangan_transport;
        // $uangMakan = $karyawan->jabatan->uang_makan;
        // $bpjsKetenagakerjaan = $karyawan->jabatan->uang_bpjs ?? 0;

        // // Sertakan BPJS ke dalam total potongan agar konsisten dengan halaman Data Gaji
        // $totalSemuaPotongan += $bpjsKetenagakerjaan;

        // $gajiKotor = $gajiPokok + $tunjanganTransport + $uangMakan - $bpjsKetenagakerjaan + $uangLembur;
        // $gajiBersih = $gajiKotor - $totalSemuaPotongan;

        // // Total pendapatan (gaji_kotor) tidak mengurangi BPJS, BPJS hanya ada di Total Potongan
        // $gajiKotor = $gajiPokok + $tunjanganTransport + $uangMakan + $uangLembur;
        // $gajiBersih = $gajiKotor - $totalSemuaPotongan;

        // $dataGaji = (object) [
        //     'gaji_pokok' => $gajiPokok,
        //     'tunjangan_transport' => $tunjanganTransport,
        //     'uang_makan' => $uangMakan,
        //     'bpjs_ketenagakerjaan' => $bpjsKetenagakerjaan,
        //     'uang_lembur' => $uangLembur,
        //     'jumlah_lembur' => $jumlahLembur,
        //     'gaji_kotor' => $gajiKotor,
        //     'uang_makan' => $uangMakan,
        //     'potongan' => $totalSemuaPotongan,
        //     'gaji_bersih' => $gajiBersih,
        // ];

        return view('pegawai.dashboard', compact(
            'karyawan',
            'dataGaji',
            'jumlahHadir',
            'jumlahSakit',
            'jumlahAlpha',
            'isAkhirBulan' // Tambahan
        ));
    }
}
