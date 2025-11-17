<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\PotonganGaji;
use App\Models\Kehadiran;
use App\Models\Jabatan;

class SlipGajiController extends Controller
{
    public function index()
    {
        $karyawans = Karyawan::orderBy('nama_lengkap')->get();
        return view('admin.slip-gaji.index', compact('karyawans'));
    }

    public function print(Request $request)
    {
        $validatedData = $request->validate([
            'bulan' => 'required',
            'tahun' => 'required|numeric|digits:4',
            'karyawan_id' => 'required|exists:karyawans,id',
        ]);

        $bulan = $validatedData['bulan'];
        $tahun = $validatedData['tahun'];
        $karyawan = Karyawan::with('jabatan')->findOrFail($validatedData['karyawan_id']);
        
        // --- LOGIKA PERHITUNGAN GAJI YANG DIPERBARUI ---
        if (!$karyawan->jabatan instanceof Jabatan) {
            return redirect()->back()->withErrors(['karyawan_id' => 'Karyawan yang dipilih belum memiliki jabatan.']);
        }

        // A. Hitung Potongan dari Absensi (Alpha)
        // Pengaturan potongan 'Alpha' tidak lagi diambil dari tabel potongan_gajis, melainkan dari tabel kehadirans
        // Jika Anda ingin membuatnya dinamis, Anda bisa mengambilnya dari tabel 'potongan_gajis'
        $jumlahAlpha = Kehadiran::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status_kehadiran', 'Alpha')->count();
        // Asumsi potongan per alpha adalah 50000, atau ambil dari database jika ada
        $settingPotonganAlpha = 50000; 
        $totalPotonganAlpha = $jumlahAlpha * $settingPotonganAlpha;

        // B. Hitung Potongan Lainnya dari tabel potongan_gajis
        $potonganLainnya = PotonganGaji::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->sum('jumlah');

        // C. Jumlahkan semua potongan
        $totalSemuaPotongan = $totalPotonganAlpha + $potonganLainnya;

        // D. Ambil detail rincian potongan lainnya untuk ditampilkan di slip
        $rincianPotonganLainnya = PotonganGaji::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();
        
        // E. Hitung Gaji
        $gajiPokok = $karyawan->jabatan->gaji_pokok;
        $tunjanganTransport = $karyawan->jabatan->tunjangan_transport;
        $uangMakan = $karyawan->jabatan->uang_makan;
        $gajiKotor = $gajiPokok + $tunjanganTransport + $uangMakan;
        $gajiBersih = $gajiKotor - $totalSemuaPotongan;

        $slipData = (object) [
            'karyawan' => $karyawan,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'gaji_pokok' => $gajiPokok,
            'tunjangan_transport' => $tunjanganTransport,
            'uang_makan' => $uangMakan,
            'gaji_kotor' => $gajiKotor,
            'potongan_alpha' => (object) [
                'jumlah_hari' => $jumlahAlpha,
                'per_hari' => $settingPotonganAlpha,
                'total' => $totalPotonganAlpha,
            ],
            'rincian_potongan_lainnya' => $rincianPotonganLainnya,
            'total_potongan' => $totalSemuaPotongan,
            'gaji_bersih' => $gajiBersih,
        ];
        
        return view('admin.slip-gaji.print', compact('slipData'));
    }
}