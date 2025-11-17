<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\PotonganGaji;
use App\Models\Kehadiran;
use App\Models\Jabatan;

class LaporanGajiController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        $search = $request->input('search');
        
        $laporanGaji = $this->hitungGajiPeriode($bulan, $tahun, $search);

        if ($request->ajax()) {
            return view('admin.laporan.gaji.partials.table', compact('laporanGaji', 'bulan', 'tahun'))->render();
        }

        return view('admin.laporan.gaji.index', compact('laporanGaji', 'bulan', 'tahun'));
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
        
        $laporanGaji = $this->hitungGajiPeriode($bulan, $tahun, $search);
        
        return view('admin.laporan.gaji.print', compact('laporanGaji', 'bulan', 'tahun'));
    }

    private function hitungGajiPeriode($bulan, $tahun, $search = null)
    {
        $query = Karyawan::with('jabatan');

        if ($search) {
            $query->where('nama_lengkap', 'like', '%' . $search . '%');
        }
        
        $karyawans = $query->get();
        $potonganAlphaSetting = PotonganGaji::where('jenis_potongan', 'like', '%Alpha%')->first();
        $dataGaji = [];

        foreach ($karyawans as $karyawan) {
            if (!$karyawan->jabatan instanceof Jabatan) {
                continue;
            }

            $jumlahAlpha = Kehadiran::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->where('status_kehadiran', 'Alpha')->count();

            $potonganLainnya = PotonganGaji::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->sum('jumlah');

            $totalPotongan = ($jumlahAlpha * ($potonganAlphaSetting->jumlah ?? 0)) + $potonganLainnya;
            
            $gajiPokok = $karyawan->jabatan->gaji_pokok;
            $tunjanganTransport = $karyawan->jabatan->tunjangan_transport;
            $uangMakan = $karyawan->jabatan->uang_makan;
            $gajiBersih = ($gajiPokok + $tunjanganTransport + $uangMakan) - $totalPotongan;

            $dataGaji[] = (object) [
                'karyawan' => $karyawan,
                'gaji_bersih' => $gajiBersih,
            ];
        }

        return $dataGaji;
    }
}