<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Kehadiran;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahPegawai = Karyawan::count();
        $jumlahAdmin = User::where('role', 'admin')->count();
        $jumlahJabatan = Jabatan::count();

        $jumlahKehadiran = Kehadiran::where('status_kehadiran', 'Hadir')
            ->whereDate('tanggal', Carbon::today())
            ->count();

        // Mengirim semua data ke view
        return view('admin.dashboard', compact(
            'jumlahPegawai',
            'jumlahAdmin',
            'jumlahJabatan',
            'jumlahKehadiran'
        ));
    }
}
