<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Kehadiran;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanAbsensiExport;
use Carbon\Carbon;

class LaporanAbsensiController extends Controller
{
    public function index(Request $request)
    {
        $bulan = (int) $request->input('bulan', date('m'));
        $tahun = (int) $request->input('tahun', date('Y'));
        $search = $request->input('search');

        $laporanAbsensi = $this->ambilDataAbsensi($bulan, $tahun, $search);

        if ($request->ajax()) {
            return view('admin.laporan.absensi.partials.table', compact('laporanAbsensi', 'bulan', 'tahun'))->render();
        }

        return view('admin.laporan.absensi.index', compact('laporanAbsensi', 'bulan', 'tahun'));
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

        $laporanAbsensi = $this->ambilDataAbsensi($bulan, $tahun, $search);

        return view('admin.laporan.absensi.print', compact('laporanAbsensi', 'bulan', 'tahun'));
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

        $laporanAbsensi = $this->ambilDataAbsensi($bulan, $tahun, $search);
        $bulanNama = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F');
        $filename = 'Laporan Absensi Karyawan-' . $bulanNama . $tahun . '.xlsx';

        return Excel::download(new LaporanAbsensiExport($laporanAbsensi, $bulan, $tahun), $filename);
    }

    public function getAttendanceDetails($karyawanId, $bulan, $tahun)
    {
        // Pastikan format tanggal valid
        try {
            $dateObj = Carbon::createFromDate($tahun, $bulan, 1);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Tanggal tidak valid'], 400);
        }

        $karyawan = Karyawan::with('jabatan')->findOrFail($karyawanId);
        $daysInMonth = $dateObj->daysInMonth;
        $attendanceData = [];
        $now = now();


        $attendances = Kehadiran::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->tanggal)->format('j'); // Key by day number
            });

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($tahun, $bulan, $day);
            $isWeekend = $date->isWeekend();
            $isFuture = $date->gt($now);
            $attendance = $attendances->get($day);

            $status = null;
            if ($attendance) {
                $status = $attendance->status_kehadiran;
            } elseif ($date->isWeekday() && $date->lte($now)) {
                $status = 'Alpha';
            }

            $attendanceData[] = [
                'day' => $day,
                'day_name' => $date->translatedFormat('D'),
                'status' => $status,
                'is_weekend' => $isWeekend,
                'is_future' => $isFuture,
                'is_today' => $date->isToday(),
            ];
        }

        return response()->json([
            'karyawan' => $karyawan,
            'month_name' => $dateObj->translatedFormat('F Y'),
            'attendance_data' => $attendanceData,
            'total_days' => $daysInMonth,
            'start_day_index' => $dateObj->copy()->startOfMonth()->dayOfWeek, // TAMBAHAN: 0 (Minggu) - 6 (Sabtu)
            'bulan' => $bulan,
            'tahun' => $tahun
        ]);
    }

    /**
     * Get attendance data for a specific employee and month
     *
     * @param int $karyawanId
     * @param string $tahunBulan Format: YYYY-MM
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAttendanceByMonth($karyawanId, $tahunBulan)
    {
        try {
            // Validate the year-month format (YYYY-MM)
            $date = Carbon::createFromFormat('Y-m', $tahunBulan);
            if (!$date) {
                throw new \Exception('Format tanggal tidak valid. Gunakan format YYYY-MM');
            }

            $bulan = $date->month;
            $tahun = $date->year;

            // Get all attendance records for the employee in the specified month
            $attendances = Kehadiran::where('karyawan_id', $karyawanId)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->orderBy('tanggal')
                ->get(['tanggal', 'status_kehadiran', 'status_lembur']);

            // Format the data for FullCalendar
            $formattedData = $attendances->map(function ($attendance) {
                return [
                    'tanggal' => $attendance->tanggal,
                    'status_kehadiran' => $attendance->status_kehadiran,
                    'status_lembur' => $attendance->status_lembur
                ];
            });

            return response()->json($formattedData);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attendance data for the report
     *
     * @param int $bulan
     * @param int $tahun
     * @param string|null $search
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function ambilDataAbsensi($bulan, $tahun, $search = null)
    {
        // 1. OPTIMASI QUERY (Eager Loading)
        // Kita ambil data karyawan BESERTA jabatannya DAN kehadirannya di bulan tsb sekaligus.
        // Perhatikan: kita pakai 'kehadirans' (sesuai nama fungsi di Model Langkah 1)
        $query = Karyawan::with(['jabatan', 'kehadirans' => function ($q) use ($bulan, $tahun) {
            $q->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun);
        }])->orderBy('nama_lengkap');

        // 2. Filter pencarian nama
        if ($search) {
            $query->where('nama_lengkap', 'like', '%' . $search . '%');
        }

        $karyawans = $query->get();

        // Setup variabel waktu
        $now = now();
        $startDate = Carbon::createFromDate($tahun, $bulan, 1);
        $endDate = (clone $startDate)->endOfMonth();

        // Hitung total hari kerja murni dalam sebulan (Logic di luar loop agar cepat)
        $totalWorkingDaysInMonth = 0;
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if ($date->isWeekday()) {
                // Jika bulan ini belum berakhir, jangan hitung hari esok sbg kewajiban kerja
                if (!($tahun == $now->year && $bulan == $now->month && $date->day > $now->day)) {
                    $totalWorkingDaysInMonth++;
                }
            }
        }

        // 3. MAPPING DATA (Di Memory PHP, bukan query DB berulang)
        $karyawans->each(function ($karyawan) use ($totalWorkingDaysInMonth) {

            // AMBIL DARI RELASI YANG SUDAH DI-LOAD (Cepat)
            // Gunakan $karyawan->kehadirans, bukan Kehadiran::where(...)
            $attendances = $karyawan->kehadirans;

            $hadir = $attendances->where('status_kehadiran', 'Hadir')->count();
            $sakit = $attendances->where('status_kehadiran', 'Sakit')->count();
            $alphaDB = $attendances->where('status_kehadiran', 'Alpha')->count();
            $ijin = $attendances->where('status_kehadiran', 'Ijin')->count();
            $cuti = $attendances->where('status_kehadiran', 'Cuti')->count();

            // Kalkulasi Alpha Otomatis
            // Rumus: Hari Kerja Sebulan - (Total Absen tercatat)
            $calculatedAlpha = max(0, $totalWorkingDaysInMonth - ($hadir + $sakit + $ijin + $cuti + $alphaDB));

            // Total Alpha = Alpha manual (diinput admin) + Alpha otomatis (karena tidak absen)
            $totalAlpha = $alphaDB + $calculatedAlpha;

            // Inject data ke object karyawan untuk ditampilkan di View
            $karyawan->jumlah_hadir = $hadir;
            $karyawan->jumlah_sakit = $sakit;
            $karyawan->jumlah_alpha = $totalAlpha;
            $karyawan->jumlah_ijin = $ijin;
            $karyawan->jumlah_cuti = $cuti;
            $karyawan->total_hari_kerja = $totalWorkingDaysInMonth;
        });

        return $karyawans;
    }
}
