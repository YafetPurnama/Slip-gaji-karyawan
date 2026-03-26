<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\PotonganGaji;
use App\Models\Kehadiran;
use App\Models\Jabatan;
use Illuminate\Support\Facades\Mail;
use App\Mail\SlipGajiEmail;
use Carbon\Carbon;

class SlipGajiController extends Controller
{
    public function index()
    {
        $karyawans = Karyawan::with('user')->orderBy('nama_lengkap')->get();
        return view('admin.slip-gaji.index', compact('karyawans'));
    }

    public function print(Request $request)
    {
        // 1. Validasi
        $validated = $this->validateRequest($request);

        // 2. Panggil Central Logic
        $result = $this->hitungGaji($validated['karyawan_id'], $validated['bulan'], $validated['tahun']);

        // 3. Cek Error (Misal: Belum punya jabatan)
        if (isset($result['error'])) {
            return redirect()->back()->withErrors(['karyawan_id' => $result['error']]);
        }

        // 4. Return View
        return view('admin.slip-gaji.print', ['slipData' => $result['data']]);
    }

    public function sendEmail(Request $request)
    {
        // 1. Validasi (termasuk email)
        $validated = $this->validateRequest($request, true);

        // 2. Panggil Central Logic (Hasil PASTI SAMA dengan Print)
        $result = $this->hitungGaji($validated['karyawan_id'], $validated['bulan'], $validated['tahun']);

        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }

        // 3. Kirim Email
        try {
            $monthNumber = $this->parseMonthToNumber($validated['bulan']);

            Mail::to($validated['email'])->send(new SlipGajiEmail([
                'data_gaji' => $result['data'],
                'bulan' => $monthNumber,
                'tahun' => (int)$validated['tahun']
            ]));

            return back()->with('success', 'Slip Gaji berhasil dikirim ke ' . $validated['email']);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // CORE LOGIC (Jantung Perbaikan)
    // =========================================================================

    private function hitungGaji($karyawanId, $bulanRaw, $tahun)
    {
        $karyawan = Karyawan::with('jabatan')->find($karyawanId);

        if (!$karyawan || !$karyawan->jabatan instanceof Jabatan) {
            return ['error' => 'Karyawan tidak ditemukan atau belum memiliki jabatan.'];
        }

        // 1. Konversi Bulan ke Angka & Persiapan Tanggal
        $bulanAngka = $this->parseMonthToNumber($bulanRaw);

        // Buat tanggal periode slip (tgl 1 bulan tsb)
        $periodeSlip = Carbon::createFromDate($tahun, $bulanAngka, 1)->startOfDay();

        // Buat tanggal masuk karyawan (set ke tgl 1 bulan tsb agar perbandingan head-to-head bulan)
        // Logika: Jika slip Mei 2024, Karyawan masuk 15 Mei 2024 -> Masih Valid (dihitung)
        // Jika slip April 2024, Karyawan masuk 15 Mei 2024 -> Invalid (0)
        $tanggalMasuk = Carbon::parse($karyawan->tanggal_masuk)->startOfDay();
        $bulanMasuk = $tanggalMasuk->copy()->startOfMonth();

        // ============================================================
        // LOGIC FIX: CEK APAKAH PERIODE SEBELUM TANGGAL MASUK?
        // ============================================================
        if ($periodeSlip->lt($bulanMasuk)) {
            // Jika periode slip kurang dari bulan masuk, return data KOSONG (0)
            return ['data' => (object) [
                'karyawan' => $karyawan,
                'bulan' => $bulanRaw,
                'tahun' => $tahun,
                'gaji_pokok' => 0,
                'tunjangan_transport' => 0,
                'uang_makan' => 0,
                'bpjs_ketenagakerjaan' => 0,
                'uang_lembur' => 0,
                'jumlah_lembur' => 0,
                'gaji_kotor' => 0,
                'potongan_alpha' => (object) ['jumlah_hari' => 0, 'per_hari' => 0, 'total' => 0],
                'rincian_potongan_lainnya' => collect([]), // Collection kosong
                'total_potongan' => 0,
                'gaji_bersih' => 0,
                'catatan' => 'Periode slip gaji ini sebelum tanggal masuk karyawan (' . $karyawan->tanggal_masuk . ').' // Opsional: pesan debug
            ]];
        }

        // 2. Logic Perhitungan Normal (Jika periode valid)
        $settingPotonganAlpha = 50000; // Sebaiknya ambil dari config/DB

        // A. Hitung Alpha
        $jumlahAlpha = Kehadiran::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulanAngka)
            ->whereYear('tanggal', $tahun)
            ->where('status_kehadiran', 'Alpha')->count();
        $totalPotonganAlpha = $jumlahAlpha * $settingPotonganAlpha;

        // B. Hitung Potongan Lain
        $potonganLainnya = PotonganGaji::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulanAngka)
            ->whereYear('tanggal', $tahun)
            ->sum('jumlah');

        $rincianPotonganLainnya = PotonganGaji::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulanAngka)
            ->whereYear('tanggal', $tahun)
            ->get();


        // C. Hitung Pendapatan
        $gajiPokok = $karyawan->jabatan->gaji_pokok;
        $tunjanganTransport = $karyawan->jabatan->tunjangan_transport;
        $uangMakan = $karyawan->jabatan->uang_makan;
        $bpjsKetenagakerjaan = $karyawan->jabatan->uang_bpjs ?? 0;

        $jumlahLembur = Kehadiran::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulanAngka)
            ->whereYear('tanggal', $tahun)
            ->where('status_lembur', 'Ya')
            ->count();
        $uangLembur = $jumlahLembur * ($karyawan->jabatan->uang_lembur ?? 0);

        // D. Kalkulasi Akhir
        // Total potongan sekarang termasuk BPJS
        $totalSemuaPotongan = $totalPotonganAlpha + $potonganLainnya + $bpjsKetenagakerjaan;
        // Gaji kotor tidak mengurangkan BPJS, agar Total Penerimaan tetap sesuai
        $gajiKotor = $gajiPokok + $tunjanganTransport + $uangMakan + $uangLembur;
        $gajiBersih = $gajiKotor - $totalSemuaPotongan;

        // E. Bungkus Data
        return ['data' => (object) [
            'karyawan' => $karyawan,
            'bulan' => $bulanRaw,
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
                'per_hari' => $settingPotonganAlpha,
                'total' => $totalPotonganAlpha,
            ],
            'rincian_potongan_lainnya' => $rincianPotonganLainnya,
            'total_potongan' => $totalSemuaPotongan,
            'gaji_bersih' => $gajiBersih,
        ]];
    }

    /**
     * Helper: Validasi Request
     */
    private function validateRequest($request, $isEmail = false)
    {
        $rules = [
            'bulan' => 'required',
            'tahun' => 'required|numeric|digits:4',
            'karyawan_id' => 'required|exists:karyawans,id',
        ];

        if ($isEmail) {
            $rules['email'] = 'required|email';
        }

        return $request->validate($rules);
    }

    /**
     * Helper: Konversi Bulan (String Indo/Inggris/Angka) ke Integer (1-12)
     */
    private function parseMonthToNumber($bulan)
    {
        if (is_numeric($bulan)) {
            return (int)$bulan;
        }

        // Coba parsing standar (Inggris)
        $parsed = date_parse($bulan);
        if ($parsed['month']) {
            return $parsed['month'];
        }

        // Manual Mapping (Bahasa Indonesia)
        $bulanIndo = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];

        return $bulanIndo[$bulan] ?? date('m'); // Default ke bulan ini jika gagal
    }
}
