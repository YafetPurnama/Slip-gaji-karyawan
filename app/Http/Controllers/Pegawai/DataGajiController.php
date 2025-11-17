<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PotonganGaji;
use App\Models\Kehadiran;

class DataGajiController extends Controller
{
    public function index(Request $request)
    {
        $karyawan = Auth::user()->karyawan;
        
        if (!$karyawan || !$karyawan->jabatan) {
            return view('pegawai.dashboard_kosong');
        }

        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
            
        $detailGaji = $this->hitungGajiPeriode($karyawan, $bulan, $tahun);

        if ($request->ajax()) {
            return view('pegawai.gaji.partials.slip', compact('karyawan', 'detailGaji', 'bulan', 'tahun'))->render();
        }

        return view('pegawai.gaji.index', compact('karyawan', 'detailGaji', 'bulan', 'tahun'));
    }

    public function printSlip(Request $request)
    {
        $request->validate([
            'bulan' => 'required',
            'tahun' => 'required|numeric|digits:4',
        ]);

        $karyawan = Auth::user()->karyawan;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $slipData = $this->hitungGajiPeriode($karyawan, $bulan, $tahun, true);

        return view('admin.slip-gaji.print', compact('slipData'));
    }

    private function hitungGajiPeriode($karyawan, $bulan, $tahun, $forPrint = false)
    {
        if (!$karyawan->jabatan) {
            return null;
        }

        // A. Hitung Potongan dari Absensi (Alpha)
        $jumlahAlpha = Kehadiran::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status_kehadiran', 'Alpha')->count();
        $settingPotonganAlpha = 50000; // Asumsi, bisa dibuat dinamis
        $totalPotonganAlpha = $jumlahAlpha * $settingPotonganAlpha;

        // B. Hitung Potongan Lainnya
        $potonganLainnya = PotonganGaji::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->sum('jumlah');

        // C. Jumlahkan semua potongan
        $totalSemuaPotongan = $totalPotonganAlpha + $potonganLainnya;
        
        // D. Hitung Gaji
        $gajiPokok = $karyawan->jabatan->gaji_pokok;
        $tunjanganTransport = $karyawan->jabatan->tunjangan_transport;
        $uangMakan = $karyawan->jabatan->uang_makan;
        $gajiKotor = $gajiPokok + $tunjanganTransport + $uangMakan;
        $gajiBersih = $gajiKotor - $totalSemuaPotongan;

        $data = (object) [
            'karyawan' => $karyawan,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'gaji_pokok' => $gajiPokok,
            'tunjangan_transport' => $tunjanganTransport,
            'uang_makan' => $uangMakan,
            'gaji_kotor' => $gajiKotor,
            'total_potongan' => $totalSemuaPotongan,
            'gaji_bersih' => $gajiBersih,
        ];

        // Jika untuk dicetak, tambahkan detail rincian
        if ($forPrint) {
            $data->potongan_alpha = (object) [
                'jumlah_hari' => $jumlahAlpha,
                'per_hari' => $settingPotonganAlpha,
                'total' => $totalPotonganAlpha,
            ];
            $data->rincian_potongan_lainnya = PotonganGaji::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->get();
        }

        return $data;
    }
}