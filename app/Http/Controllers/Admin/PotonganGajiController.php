<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\PotonganGaji;
use Illuminate\Http\Request;

class PotonganGajiController extends Controller
{
    public function index(Request $request)
    {
        // Mulai query dengan eager loading relasi karyawan
        $query = PotonganGaji::with('karyawan')->latest();

        // Terapkan filter pencarian jika ada
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->whereHas('karyawan', function ($q) use ($searchTerm) {
                $q->where('nama_lengkap', 'like', '%' . $searchTerm . '%');
            });
        }

        $potonganGajis = $query->paginate(10);

        // Jika ini adalah permintaan AJAX, kembalikan hanya bagian tabelnya saja
        if ($request->ajax()) {
            return view('admin.potongan.partials.table', compact('potonganGajis'))->render();
        }

        return view('admin.potongan.index', compact('potonganGajis'));
    }

    public function create()
    {
        $karyawans = Karyawan::orderBy('nama_lengkap')->get();
        return view('admin.potongan.create', compact('karyawans'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'jenis_potongan' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'karyawan_ids' => 'required|array|min:1',
            'karyawan_ids.*' => 'string',
        ]);

        $karyawanIds = $validatedData['karyawan_ids'];
        $dataToInsert = [];
        $now = now();

        if (in_array('all', $karyawanIds)) {
            $karyawans = Karyawan::all();
        } else {
            $karyawans = Karyawan::whereIn('id', $karyawanIds)->get();
        }

        foreach ($karyawans as $karyawan) {
            $dataToInsert[] = [
                'karyawan_id' => $karyawan->id,
                'jenis_potongan' => $validatedData['jenis_potongan'],
                'jumlah' => $validatedData['jumlah'],
                'tanggal' => $validatedData['tanggal'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($dataToInsert)) {
            PotonganGaji::insert($dataToInsert);
        }

        return redirect()->route('potongan-gaji.index')->with('success', 'Data potongan gaji berhasil ditambahkan.');
    }

    public function destroy(PotonganGaji $potonganGaji)
    {
        $potonganGaji->delete();
        return redirect()->route('potongan-gaji.index')->with('success', 'Data potongan gaji berhasil dihapus.');
    }
}