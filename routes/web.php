<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\JabatanController;
use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\PotonganGajiController;
use App\Http\Controllers\Admin\DataGajiController;
use App\Http\Controllers\Admin\LaporanGajiController;
use App\Http\Controllers\Admin\LaporanAbsensiController;
use App\Http\Controllers\Admin\SlipGajiController;
use App\Http\Controllers\Pegawai\GantiPasswordController;
use App\Http\Controllers\Pegawai\DashboardController as PegawaiDashboardController;
use App\Http\Controllers\Pegawai\DataGajiController as PegawaiDataGajiController;

// --- Rute Halaman Utama ---
Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->role == 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif (Auth::user()->role == 'pegawai') {
            return redirect()->route('pegawai.dashboard');
        }
    }
    return redirect()->route('login');
});


// API untuk generate NIP
Route::get('/api/get-last-nip', function () {
    $lastKaryawan = \App\Models\Karyawan::orderBy('id', 'desc')->first();
    return response()->json([
        'last_nip' => $lastKaryawan ? $lastKaryawan->nip : '0000000000'
    ]);
})->name('api.get-last-nip');

// --- Rute Otentikasi ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

// ======================================================================
// --- GRUP ROUTE UNTUK ADMIN ---
// ======================================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // CRUD Master Data
    Route::resource('karyawan', KaryawanController::class);
    Route::resource('jabatan', JabatanController::class);
    Route::resource('potongan-gaji', PotonganGajiController::class)->except('show');

    // Fitur Absensi
    Route::get('rekap-absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('rekap-absensi', [AbsensiController::class, 'store'])->name('absensi.store');

    // Fitur Data Gaji
    Route::get('data-gaji', [DataGajiController::class, 'index'])->name('data-gaji.index');
    Route::get('data-gaji/slip/{karyawan}/{bulan}/{tahun}', [DataGajiController::class, 'printSlip'])->name('data-gaji.slip');

    // Fitur Laporan
    Route::get('laporan-gaji', [LaporanGajiController::class, 'index'])->name('laporan-gaji.index');
    Route::get('laporan-gaji/print', [LaporanGajiController::class, 'print'])->name('laporan-gaji.print');
    Route::get('laporan-gaji/export', [LaporanGajiController::class, 'export'])->name('laporan-gaji.export');
    Route::get('laporan-absensi', [LaporanAbsensiController::class, 'index'])->name('laporan-absensi.index');
    Route::get('laporan-absensi/print', [LaporanAbsensiController::class, 'print'])->name('laporan-absensi.print');
    Route::get('laporan-absensi/export', [LaporanAbsensiController::class, 'export'])->name('laporan-absensi.export');

    // API for attendance calendar
    Route::get('/kehadiran/{karyawanId}/bulan/{tahunBulan}', [LaporanAbsensiController::class, 'getAttendanceByMonth'])->name('kehadiran.bulan');
    // Route::get('laporan-absensi/attendance-details/{karyawanId}/{bulan}/{tahun}', [LaporanAbsensiController::class, 'getAttendanceDetails'])->name('laporan-absensi.attendance-details');
    Route::get('admin/laporan-absensi/detail/{karyawanId}/{bulan}/{tahun}', [App\Http\Controllers\Admin\LaporanAbsensiController::class, 'getAttendanceDetails'])->name('laporan.absensi.detail');

    // Fitur Slip Gaji
    Route::get('slip-gaji', [SlipGajiController::class, 'index'])->name('slip-gaji.index');
    Route::post('slip-gaji/print', [SlipGajiController::class, 'print'])->name('slip-gaji.print');
    Route::post('slip-gaji/send-email', [SlipGajiController::class, 'sendEmail'])->name('slip-gaji.send-email');
});


// ======================================================================
// --- GRUP ROUTE UNTUK PEGAWAI / KARYAWAN ---
// ======================================================================
Route::middleware(['auth', 'role:pegawai'])->prefix('pegawai')->group(function () {
    Route::get('/dashboard', [PegawaiDashboardController::class, 'index'])->name('pegawai.dashboard');
    Route::get('/data-gaji', [PegawaiDataGajiController::class, 'index'])->name('pegawai.gaji.index');
    Route::get('/data-gaji/print', [PegawaiDataGajiController::class, 'printSlip'])->name('pegawai.gaji.print');
    Route::get('/ganti-password', [GantiPasswordController::class, 'index'])->name('pegawai.password.index');
    Route::post('/ganti-password', [GantiPasswordController::class, 'update'])->name('pegawai.password.update');
});
