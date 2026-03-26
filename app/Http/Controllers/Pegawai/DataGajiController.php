<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PotonganGaji;
use App\Models\Kehadiran;
use Carbon\Carbon;

class DataGajiController extends Controller
{
    public function index(Request $request)
    {
        $karyawan = Auth::user()->karyawan;
        if (!$karyawan || !$karyawan->jabatan) {
            return view('pegawai.dashboard_kosong');
        }

        $currentYear = (int)date('Y');
        $currentMonth = (int)date('m');

        $startDate = $karyawan->tanggal_masuk;
        $startYear = $startDate ? (int)Carbon::parse($startDate)->format('Y') : $currentYear;
        $startMonth = $startDate ? (int)Carbon::parse($startDate)->format('m') : 1;

        $tahun = (int)$request->input('tahun', $currentYear);
        $bulan = (int)$request->input('bulan', $currentMonth);

        $isBulanBerjalan = ($tahun == $currentYear && $bulan == $currentMonth);
        $isAkhirBulan = Carbon::now()->isLastOfMonth();
        // $isLocked = ($isBulanBerjalan && !$isAkhirBulan);
        $isLocked = ($tahun == $currentYear && $bulan == $currentMonth && !\Carbon\Carbon::now()->isLastOfMonth());

        $detailGaji = null;
        // $isLocked = false;
        if (!$isLocked) {
            $detailGaji = $this->hitungGajiPeriode($karyawan, $bulan, $tahun);
        }

        if ($tahun < $startYear || $tahun > $currentYear) {
            $tahun = $currentYear;
        }

        if ($isBulanBerjalan && !$isAkhirBulan) {
            $isLocked = true;
        } else {
            $detailGaji = $this->hitungGajiPeriode($karyawan, $bulan, $tahun);
        }

        if ($request->ajax()) {
            if ($isLocked) {
                return "<div class='alert alert-warning text-center'>Slip gaji bulan ini hanya dapat dilihat pada akhir bulan.</div>";
            }
            return view('pegawai.gaji.partials.slip', compact('karyawan', 'detailGaji', 'bulan', 'tahun'))->render();
        }

        // Validate month range based on selected year
        $minMonth = ($tahun == $startYear) ? $startMonth : 1;
        $maxMonth = ($tahun == $currentYear) ? $currentMonth : 12;

        if ($bulan < $minMonth || $bulan > $maxMonth) {
            $bulan = $maxMonth;
        }

        // Generate years array from start year to current year
        $years = range($startYear, $currentYear);
        rsort($years); // Show latest years first

        // Calculate salary details
        $detailGaji = $this->hitungGajiPeriode($karyawan, $bulan, $tahun);

        if ($request->ajax()) {
            if ($isLocked) {
                return "<div class='alert alert-warning text-center mt-4'>
                        Slip gaji bulan ini hanya dapat dilihat pada akhir bulan.
                    </div>";
            }
            return view('pegawai.gaji.partials.slip', compact('karyawan', 'detailGaji', 'bulan', 'tahun'))->render();
        }
        $years = range($karyawan->tanggal_masuk ? (int)Carbon::parse($karyawan->tanggal_masuk)->format('Y') : $currentYear, $currentYear);
        rsort($years);

        return view('pegawai.gaji.index', [
            'karyawan' => $karyawan,
            'detailGaji' => $detailGaji,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'years' => $years,
            'startYear' => $startYear,
            'currentYear' => $currentYear,
            'startMonth' => $startMonth,
            'currentMonth' => $currentMonth,
            'minMonth' => $minMonth,
            'maxMonth' => $maxMonth,
            'isLocked' => $isLocked, // Adding
            'startDate' => $startDate
        ]);
    }

    public function printSlip(Request $request)
    {
        $request->validate([
            'bulan' => 'required',
            'tahun' => 'required|numeric|digits:4',
        ]);

        $currentYear = (int)date('Y');
        $currentMonth = (int)date('m');


        $bulan = $request->bulan;
        $tahun = $request->tahun;

        if (($tahun == $currentYear && $bulan == $currentMonth) && !Carbon::now()->isLastOfMonth()) {
            return redirect()->back()->with('error', 'Slip gaji bulan berjalan belum dapat dicetak.');
        }

        $karyawan = Auth::user()->karyawan;
        $slipData = $this->hitungGajiPeriode($karyawan, $bulan, $tahun, true);
        return view('admin.slip-gaji.print', compact('slipData'));
    }

    // public function printSlip(Request $request)
    // {
    //     $request->validate([
    //         'bulan' => 'required',
    //         'tahun' => 'required|numeric|digits:4',
    //     ]);

    //     // Get fresh data to avoid any caching issues
    //     $karyawan = Auth::user()->karyawan->fresh(['jabatan']);
    //     $bulan = $request->bulan;
    //     $tahun = $request->tahun;

    //     // Disable query log to prevent memory issues
    //     DB::connection()->disableQueryLog();

    //     // Calculate the salary data
    //     $slipData = $this->hitungGajiPeriode($karyawan, $bulan, $tahun, true);
    //     // Log the data for debugging
    //     Log::info('Print Slip Data:', $slipData);


    //     return view('admin.slip-gaji.print', compact('slipData'));
    // }

    // private function hitungGajiPeriode($karyawan, $bulan, $tahun, $forPrint = false)
    // {
    //     if (!$karyawan->jabatan) {
    //         return null;
    //     }

    //     // 1. Hitung Potongan dari Absensi (Alpha)
    //     $jumlahAlpha = Kehadiran::where('karyawan_id', $karyawan->id)
    //         ->whereMonth('tanggal', $bulan)
    //         ->whereYear('tanggal', $tahun)
    //         ->where('status', 'Alpha')
    //         ->count();

    //     $potonganAlpha = $jumlahAlpha * 50000; // Potongan per alpha 50.000

    //     // 2. Hitung Potongan BPJS
    //     $potonganBPJS = 0;
    //     if ($karyawan->bpjs_ketenagakerjaan) {
    //         $potonganBPJS += 30000; // Potongan BPJS Ketenagakerjaan
    //     }
    //     if ($karyawan->bpjs_kesehatan) {
    //         $potonganBPJS += 20000; // Potongan BPJS Kesehatan
    //     }

    //     // 3. Hitung Total Potongan
    //     $totalPotongan = $potonganAlpha + $potonganBPJS;

    //     // 4. Hitung Gaji Pokok dan Tunjangan
    //     $gajiPokok = $karyawan->jabatan->gaji_pokok;
    //     $tunjanganTransport = $karyawan->jabatan->tunjangan_transport ?? 0;
    //     $tunjanganMakan = $karyawan->jabatan->tunjangan_makan ?? 0;

    //     // 5. Hitung Uang Lembur
    //     $totalJamLembur = Kehadiran::where('karyawan_id', $karyawan->id)
    //         ->whereMonth('tanggal', $bulan)
    //         ->whereYear('tanggal', $tahun)
    //         ->sum('jam_lembur');

    //     $uangLembur = $totalJamLembur * 20000; // 20.000 per jam

    //     // 6. Hitung Total Penerimaan
    //     $totalPenerimaan = $gajiPokok + $tunjanganTransport + $tunjanganMakan + $uangLembur;

    //     // 7. Hitung Gaji Bersih
    //     $gajiBersih = $totalPenerimaan - $totalPotongan;

    //     return [
    //         'karyawan' => $karyawan,
    //         'bulan' => $bulan,
    //         'tahun' => $tahun,
    //         'gaji_pokok' => $gajiPokok,
    //         'tunjangan_transport' => $tunjanganTransport,
    //         'tunjangan_makan' => $tunjanganMakan,
    //         'uang_lembur' => $uangLembur,
    //         'total_penerimaan' => $totalPenerimaan,
    //         'potongan_alpha' => $potonganAlpha,
    //         'potongan_bpjs' => $potonganBPJS,
    //         'total_potongan' => $totalPotongan,
    //         'gaji_bersih' => $gajiBersih,
    //         'nama_bulan' => \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y')
    //     ];
    // }

    private function hitungGajiPeriode($karyawan, $bulan, $tahun, $forPrint = false)
    {
        if (!$karyawan->jabatan) {
            return null;
        }

        // 1. Hitung Potongan dari Absensi (Alpha)
        $jumlahAlpha = Kehadiran::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status_kehadiran', 'Alpha')->count();

        $settingPotonganAlpha = 50000; // Sebaiknya ambil dari config/DB agar dinamis
        $totalPotonganAlpha = $jumlahAlpha * $settingPotonganAlpha;

        // 2. Hitung Potongan Lainnya (FIX: Pastikan menggunakan penjumlahan sum positif)
        $potonganLainnya = PotonganGaji::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->sum('jumlah');

        // 3. Ambil Nilai BPJS
        $bpjsKetenagakerjaan = $karyawan->jabatan->uang_bpjs ?? 0;

        // ==================================================================================
        // PERBAIKAN LOGIKA UTAMA DI SINI
        // ==================================================================================

        // A. TOTAL POTONGAN
        // Rumus: Alpha + Potongan Lain + BPJS
        // Menggunakan tanda TAMBAH (+) semua agar tidak error minus, dan mencakup BPJS
        // agar baris "Total Potongan" di slip bernilai 250.000 (jika alpha & lain nol)
        $totalSemuaPotongan = $totalPotonganAlpha + $potonganLainnya + $bpjsKetenagakerjaan;

        // B. HITUNG UANG LEMBUR
        $jumlahLembur = Kehadiran::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status_lembur', 'Ya')
            ->count();
        $uangLembur = $jumlahLembur * ($karyawan->jabatan->uang_lembur ?? 0);

        // C. HITUNG TOTAL PENERIMAAN (GAJI KOTOR)
        // Sesuai screenshot: Gaji Pokok + Transport + Makan + Lembur
        // (BPJS TIDAK dikurangi di sini agar Total Penerimaan tetap 10.400.000)
        $gajiPokok = $karyawan->jabatan->gaji_pokok;
        $tunjanganTransport = $karyawan->jabatan->tunjangan_transport;
        $uangMakan = $karyawan->jabatan->uang_makan;

        $gajiKotor = $gajiPokok + $tunjanganTransport + $uangMakan + $uangLembur;

        // D. HITUNG GAJI BERSIH (TAKE HOME PAY)
        // Gaji Kotor (Penerimaan) - Total Potongan
        $gajiBersih = $gajiKotor - $totalSemuaPotongan;

        $data = (object) [
            'karyawan' => $karyawan,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'gaji_pokok' => $gajiPokok,
            'tunjangan_transport' => $tunjanganTransport,
            'uang_makan' => $uangMakan,
            'bpjs_ketenagakerjaan' => $bpjsKetenagakerjaan,
            'uang_lembur' => $uangLembur,
            'jumlah_lembur' => $jumlahLembur,
            'gaji_kotor' => $gajiKotor,          // Total Penerimaan
            'total_potongan' => $totalSemuaPotongan, // Total Pengurangan (termasuk BPJS)
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
