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


class SlipGajiController extends Controller
{
    public function index()
    {
        // $karyawans = Karyawan::orderBy('nama_lengkap')->get();
        // return view('admin.slip-gaji.index', compact('karyawans'));
        $karyawans = Karyawan::with('user')->orderBy('nama_lengkap')->get();
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
        $jumlahAlpha = Kehadiran::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status_kehadiran', 'Alpha')->count();
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

        // D. Hitung Uang Lembur
        $jumlahLembur = Kehadiran::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status_lembur', 'Ya')
            ->count();
        $uangLembur = $jumlahLembur * ($karyawan->jabatan->uang_lembur ?? 0);

        // E. Hitung Gaji
        $gajiPokok = $karyawan->jabatan->gaji_pokok;
        $tunjanganTransport = $karyawan->jabatan->tunjangan_transport;
        $uangMakan = $karyawan->jabatan->uang_makan;
        $bpjsKetenagakerjaan = $karyawan->jabatan->uang_bpjs ?? 0;
        $gajiKotor = $gajiPokok + $tunjanganTransport + $uangMakan - $bpjsKetenagakerjaan + $uangLembur;
        $gajiBersih = $gajiKotor - $totalSemuaPotongan;

        $slipData = (object) [
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
                'per_hari' => $settingPotonganAlpha,
                'total' => $totalPotonganAlpha,
            ],
            'rincian_potongan_lainnya' => $rincianPotonganLainnya,
            'total_potongan' => $totalSemuaPotongan,
            'gaji_bersih' => $gajiBersih,
        ];

        return view('admin.slip-gaji.print', compact('slipData'));
    }

    public function sendEmail(Request $request)
    {
        // 1. Validasi Input
        $validatedData = $request->validate([
            'bulan' => 'required',
            'tahun' => 'required|numeric|digits:4',
            'karyawan_id' => 'required|exists:karyawans,id',
            'email' => 'required|email',
        ]);

        $bulan = $validatedData['bulan'];
        $tahun = $validatedData['tahun'];
        $emailTujuan = $validatedData['email'];

        // 2. Ambil Data Karyawan
        $karyawan = Karyawan::with('jabatan')->find($validatedData['karyawan_id']);

        if (!$karyawan->jabatan instanceof Jabatan) {
            return back()->with('error', 'Karyawan yang dipilih belum memiliki jabatan.');
        }

        // ==========================================
        // --- LOGIC PERHITUNGAN DARI FUNCTION PRINT ---
        // ==========================================

        // A. Hitung Alpha
        $jumlahAlpha = Kehadiran::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status_kehadiran', 'Alpha')->count();

        $settingPotonganAlpha = 50000;
        $totalPotonganAlpha = $jumlahAlpha * $settingPotonganAlpha;

        // B. Hitung Potongan Lainnya
        $potonganLainnya = PotonganGaji::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->sum('jumlah');

        // C. Total Potongan
        $totalSemuaPotongan = $totalPotonganAlpha + $potonganLainnya;

        // D. Rincian Potongan (Untuk ditampilkan di tabel slip)
        $rincianPotonganLainnya = PotonganGaji::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        // E. Hitung Gaji Bersih
        $gajiPokok = $karyawan->jabatan->gaji_pokok;
        $tunjanganTransport = $karyawan->jabatan->tunjangan_transport;
        $uangMakan = $karyawan->jabatan->uang_makan;
        $bpjsKetenagakerjaan = $karyawan->jabatan->uang_bpjs ?? 0;

        // Hitung Uang Lembur
        $jumlahLembur = Kehadiran::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status_lembur', 'Ya')
            ->count();
        $uangLembur = $jumlahLembur * ($karyawan->jabatan->uang_lembur ?? 0);

        $gajiKotor = $gajiPokok + $tunjanganTransport + $uangMakan - $bpjsKetenagakerjaan + $uangLembur;
        $gajiBersih = $gajiKotor - $totalSemuaPotongan;

        // 3. Bungkus Data (Struktur HARUS SAMA PERSIS dengan view print.blade.php)
        $slipData = (object) [
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
                'per_hari' => $settingPotonganAlpha,
                'total' => $totalPotonganAlpha,
            ],
            'rincian_potongan_lainnya' => $rincianPotonganLainnya,
            'total_potongan' => $totalSemuaPotongan,
            'gaji_bersih' => $gajiBersih,
        ];

        // 4. Kirim Email
        try {
            $monthNumber = date_parse($bulan)['month'];

            Mail::to($emailTujuan)->send(new SlipGajiEmail([
                'data_gaji' => $slipData,
                'bulan' => $monthNumber,
                'tahun' => (int)$tahun
            ]));

            return back()->with('success', 'Slip Gaji berhasil dikirim ke ' . $emailTujuan);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }
}
