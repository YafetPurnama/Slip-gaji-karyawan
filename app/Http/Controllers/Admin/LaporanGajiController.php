<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\PotonganGaji;
use App\Models\Kehadiran;
use App\Models\Jabatan;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanGajiExport;
use App\Mail\SlipGajiEmail;
use Illuminate\Support\Facades\Mail;

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

    public function export(Request $request)
    {
        $request->validate([
            'bulan' => 'required',
            'tahun' => 'required|numeric|digits:4',
        ]);

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $search = $request->input('search');

        $laporanGaji = $this->hitungGajiPeriode($bulan, $tahun, $search);
        $bulanNama = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F');
        $filename = 'Laporan Gaji Karyawan-' . $bulanNama . $tahun . '.xlsx';

        return Excel::download(new LaporanGajiExport($laporanGaji, $bulan, $tahun), $filename);
    }

    // private function hitungGajiPeriode($bulan, $tahun, $search = null)
    // {
    //     $query = Karyawan::with('jabatan');

    //     if ($search) {
    //         $query->where('nama_lengkap', 'like', '%' . $search . '%');
    //     }

    //     $karyawans = $query->get();
    //     $potonganAlphaSetting = PotonganGaji::where('jenis_potongan', 'like', '%Alpha%')->first();
    private function hitungGajiPeriode($bulan, $tahun, $search = null)
    {
        $endOfSelectedMonth = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        
        $query = Karyawan::with('jabatan')
            ->whereDate('tanggal_masuk', '<=', $endOfSelectedMonth);

        if ($search) {
            $query->where('nama_lengkap', 'like', '%' . $search . '%');
        }

        // Hitung total hari kerja (Senin–Jumat) di bulan & tahun yang dipilih.
        // Kalau bulan itu adalah bulan berjalan, batasi sampai hari ini saja.
        $startOfMonth = Carbon::createFromDate($tahun, $bulan, 1);
        $endOfMonth   = (clone $startOfMonth)->endOfMonth();
        $today        = Carbon::today();

        if ($startOfMonth->isSameYear($today) && $startOfMonth->isSameMonth($today) && $today->lt($endOfMonth)) {
            $endRange = $today;
        } else {
            $endRange = $endOfMonth;
        }

        $totalHariKerja = 0;
        for ($date = $startOfMonth->copy(); $date->lte($endRange); $date->addDay()) {
            if ($date->isWeekday()) { // Senin–Jumat
                $totalHariKerja++;
            }
        }

        $karyawans = $query->get();
        $potonganAlphaSetting = PotonganGaji::where('jenis_potongan', 'like', '%Alpha%')->first();
        $dataGaji = [];

        foreach ($karyawans as $karyawan) {
            if (! $karyawan->jabatan instanceof Jabatan) {
                continue;
            }

            // ========================
            // 1) DATA ABSENSI BULANAN
            // ========================

            // Jumlah Alpha di bulan & tahun tersebut
            $jumlahAlpha = Kehadiran::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->where('status_kehadiran', 'Alpha')
                ->count();

            // Jumlah hari lembur (status_lembur = 'Ya')
            $jumlahLembur = Kehadiran::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->where('status_lembur', 'Ya')
                ->count();

            // ========================
            // 2) POTONGAN LAINNYA (MANUAL)
            // ========================

            // Potongan lain yang tercatat di tabel potongan_gajis, selain Alpha
            $potonganLainnya = PotonganGaji::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->where(function ($q) {
                    $q->whereNull('jenis_potongan')
                        ->orWhere('jenis_potongan', 'not like', '%Alpha%');
                })
                ->sum('jumlah');

            // ========================
            // 3) KOMPONEN GAJI DARI JABATAN
            // ========================

            $gajiPokok           = $karyawan->jabatan->gaji_pokok;
            $tunjanganTransport  = $karyawan->jabatan->tunjangan_transport;
            $uangMakan           = $karyawan->jabatan->uang_makan;
            $uangBpjs            = $karyawan->jabatan->uang_bpjs ?? 0;
            $uangLemburPerHari   = $karyawan->jabatan->uang_lembur ?? 0;

            // ========================
            // 4) POTONGAN KARENA ALPHA
            // ========================

            if ($totalHariKerja > 0) {
                // Fixed potongan Alpha sebesar Rp 50.000 per absen
                $potonganPerAlpha = 50000;
                $potonganAlpha    = $jumlahAlpha * $potonganPerAlpha;
            } else {
                $potonganAlpha = 0;
            }

            // ========================
            // 5) LEMBUR
            // ========================

            $totalLembur = $jumlahLembur * $uangLemburPerHari;

            // ========================
            // 6) GAJI BERSIH
            // ========================

            // Gaji kotor = gaji_pokok + tunjangan transport + uang makan - BPJS Ketenagakerjaan + uang lembur
            $gajiKotor = $gajiPokok
                + $tunjanganTransport
                + $uangMakan
                - $uangBpjs
                + $totalLembur;

            // Gaji bersih = gaji kotor - potongan Alpha - potongan lain
            $gajiBersih = $gajiKotor - $potonganAlpha - $potonganLainnya;

            $dataGaji[] = (object) [
                'karyawan'   => $karyawan,
                'gaji_bersih' => $gajiBersih,
            ];
        }

        // foreach ($karyawans as $karyawan) {
        //     if (!$karyawan->jabatan instanceof Jabatan) {
        //         continue;
        //     }

        //     $jumlahAlpha = Kehadiran::where('karyawan_id', $karyawan->id)
        //         ->whereMonth('tanggal', $bulan)
        //         ->whereYear('tanggal', $tahun)
        //         ->where('status_kehadiran', 'Alpha')->count();

        //     $potonganLainnya = PotonganGaji::where('karyawan_id', $karyawan->id)
        //         ->whereMonth('tanggal', $bulan)
        //         ->whereYear('tanggal', $tahun)
        //         ->sum('jumlah');

        //     $totalPotongan = ($jumlahAlpha * ($potonganAlphaSetting->jumlah ?? 0)) + $potonganLainnya;

        //     $gajiPokok = $karyawan->jabatan->gaji_pokok;
        //     $tunjanganTransport = $karyawan->jabatan->tunjangan_transport;
        //     $uangMakan = $karyawan->jabatan->uang_makan;
        //     $gajiBersih = ($gajiPokok + $tunjanganTransport + $uangMakan) - $totalPotongan;

        //     $dataGaji[] = (object) [
        //         'karyawan' => $karyawan,
        //         'gaji_bersih' => $gajiBersih,
        //     ];
        // }

        return $dataGaji;
    }

    // public function sendEmail(Request $request)
    // {
    //     // 1. Validasi
    //     $validatedData = $request->validate([
    //         'bulan' => 'required',
    //         'tahun' => 'required|numeric|digits:4',
    //         'karyawan_id' => 'required|exists:karyawans,id',
    //         'email' => 'required|email',
    //     ]);

    //     // 2. Ambil Data
    //     $karyawan = Karyawan::with('jabatan')->find($validatedData['karyawan_id']);

    //     if (!$karyawan->jabatan instanceof Jabatan) {
    //         return back()->with('error', 'Karyawan belum memiliki jabatan.');
    //     }

    //     // 3. HITUNG GAJI (Gunakan fungsi yang sama, jadi tidak perlu copy paste logic!)
    //     // Ini adalah penerapan DRY (Don't Repeat Yourself)
    //     $slipData = $this->hitungGajiPeriode($karyawan, $validatedData['bulan'], $validatedData['tahun']);

    //     // 4. Kirim Email
    //     try {
    //         // Konversi bulan angka ke nama bulan (opsional, untuk subjek email)
    //         $monthName = \Carbon\Carbon::createFromFormat('m', $validatedData['bulan'])->translatedFormat('F');

    //         Mail::to($validatedData['email'])->send(new SlipGajiEmail([
    //             'slip_object' => $slipData, // Kita kirim object hasil perhitungan ke Mailable
    //             'bulan' => $monthName,
    //             'tahun' => $validatedData['tahun']
    //         ]));

    //         return back()->with('success', 'Slip Gaji berhasil dikirim ke ' . $validatedData['email']);
    //     } catch (\Exception $e) {
    //         return back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
    //     }
    // }

    private function hitungDetailGajiSingle($karyawan, $bulan, $tahun)
    {
        // 1. Hitung Total Hari Kerja (Sama persis dengan logic hitungGajiPeriode Anda)
        $startOfMonth = Carbon::createFromDate($tahun, $bulan, 1);
        $endOfMonth   = (clone $startOfMonth)->endOfMonth();
        $today        = Carbon::today();
        $endRange     = ($startOfMonth->isSameYear($today) && $startOfMonth->isSameMonth($today) && $today->lt($endOfMonth)) ? $today : $endOfMonth;

        $totalHariKerja = 0;
        for ($date = $startOfMonth->copy(); $date->lte($endRange); $date->addDay()) {
            if ($date->isWeekday()) $totalHariKerja++;
        }

        // 2. Data Absensi
        $jumlahAlpha = Kehadiran::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status_kehadiran', 'Alpha')->count();

        $jumlahLembur = Kehadiran::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status_lembur', 'Ya')->count();

        // 3. Data Jabatan & Komponen
        $gajiPokok = $karyawan->jabatan->gaji_pokok;
        $tunjanganTransport = $karyawan->jabatan->tunjangan_transport;
        $uangMakan = $karyawan->jabatan->uang_makan;
        $bpjsKetenagakerjaan = $karyawan->jabatan->uang_bpjs ?? 0;
        $uangLembur = $jumlahLembur * ($karyawan->jabatan->uang_lembur ?? 0);

        // 4. Hitung Potongan Alpha (Menggunakan Logic Pintar Anda)
        $potonganAlphaSetting = PotonganGaji::where('jenis_potongan', 'like', '%Alpha%')->first();
        if ($totalHariKerja > 0) {
            $settingPotonganAlpha = $potonganAlphaSetting->jumlah ?? ($gajiPokok / $totalHariKerja);
            $totalPotonganAlpha = $jumlahAlpha * $settingPotonganAlpha;
        } else {
            $settingPotonganAlpha = 0;
            $totalPotonganAlpha = 0;
        }

        // 5. Potongan Lainnya
        $potonganLainnya = PotonganGaji::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where(function ($q) {
                $q->whereNull('jenis_potongan')->orWhere('jenis_potongan', 'not like', '%Alpha%');
            })->sum('jumlah');

        $rincianPotonganLainnya = PotonganGaji::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where(function ($q) {
                $q->whereNull('jenis_potongan')->orWhere('jenis_potongan', 'not like', '%Alpha%');
            })->get();

        // 6. Finalisasi
        $totalSemuaPotongan = $totalPotonganAlpha + $potonganLainnya;
        $gajiKotor = $gajiPokok + $tunjanganTransport + $uangMakan + $bpjsKetenagakerjaan + $uangLembur;
        $gajiBersih = $gajiKotor - $totalSemuaPotongan;

        // RETURN FORMAT LENGKAP (Sesuai kebutuhan view admin.slip-gaji.print)
        return (object) [
            'karyawan' => $karyawan,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'gaji_pokok' => $gajiPokok,
            'tunjangan_transport' => $tunjanganTransport,
            'uang_makan' => $uangMakan,
            'bpjs_ketenagakerjaan' => $bpjsKetenagakerjaan,
            'uang_lembur' => $uangLembur,
            'jumlah_lembur' => $jumlahLembur,
            'gaji_kotor' => $gajiKotor,
            'potongan_alpha' => (object) [
                'jumlah_hari' => $jumlahAlpha,
                'per_hari' => $settingPotonganAlpha, // Harga per alpha
                'total' => $totalPotonganAlpha,
            ],
            'rincian_potongan_lainnya' => $rincianPotonganLainnya,
            'total_potongan' => $totalSemuaPotongan,
            'gaji_bersih' => $gajiBersih,
        ];
    }

    public function sendEmail(Request $request)
    {
        // 1. Validasi
        $validatedData = $request->validate([
            'bulan' => 'required',
            'tahun' => 'required|numeric|digits:4',
            'karyawan_id' => 'required|exists:karyawans,id',
            'email' => 'required|email',
        ]);

        // 2. Ambil Data
        $karyawan = Karyawan::with('jabatan')->find($validatedData['karyawan_id']);

        if (!$karyawan->jabatan instanceof Jabatan) {
            return back()->with('error', 'Karyawan belum memiliki jabatan.');
        }

        // 3. HITUNG GAJI DETAIL (Gunakan helper baru khusus single user)
        // Jangan pakai hitungGajiPeriode disini karena outputnya beda format
        $slipData = $this->hitungDetailGajiSingle($karyawan, $validatedData['bulan'], $validatedData['tahun']);

        // 4. Kirim Email
        try {
            $monthName = \Carbon\Carbon::createFromFormat('m', $validatedData['bulan'])->translatedFormat('F');

            // Kirim object lengkap ke Mailable
            Mail::to($validatedData['email'])->send(new SlipGajiEmail([
                'slip_object' => $slipData,
                'bulan' => $monthName,
                'tahun' => $validatedData['tahun']
            ]));

            return back()->with('success', 'Slip Gaji berhasil dikirim ke ' . $validatedData['email']);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }
}
