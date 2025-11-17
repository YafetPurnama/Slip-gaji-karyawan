<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function index(Request $request)
    {
        $query = Jabatan::query();

        // Tambahkan logika pencarian
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_jabatan', 'like', '%' . $request->search . '%');
        }

        $jabatans = $query->latest()->paginate(10);
        if ($request->ajax()) {
            return view('admin.jabatan.partials.table', compact('jabatans'))->render();
        }
        return view('admin.jabatan.index', compact('jabatans'));
    }

    public function create()
    {
        return view('admin.jabatan.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_jabatan' => 'required|string|unique:jabatans,nama_jabatan|max:255',
            'gaji_pokok' => 'required|string',
            'tunjangan_transport' => 'required|string',
            'uang_makan' => 'required|string',
        ]);

        $validatedData['gaji_pokok'] = (int) str_replace('.', '', $validatedData['gaji_pokok']);
        $validatedData['tunjangan_transport'] = (int) str_replace('.', '', $validatedData['tunjangan_transport']);
        $validatedData['uang_makan'] = (int) str_replace('.', '', $validatedData['uang_makan']);

        Jabatan::create($validatedData);

        return redirect()->route('jabatan.index')->with('success', 'Data jabatan berhasil ditambahkan.');
    }

    public function edit(Jabatan $jabatan)
    {
        return view('admin.jabatan.edit', compact('jabatan'));
    }

    public function update(Request $request, Jabatan $jabatan)
    {
        $validatedData = $request->validate([
            'nama_jabatan' => 'required|string|max:255|unique:jabatans,nama_jabatan,' . $jabatan->id,
            'gaji_pokok' => 'required|numeric',
            'tunjangan_transport' => 'required|numeric',
            'uang_makan' => 'required|numeric',
        ]);

        $jabatan->update($validatedData);

        return redirect()->route('jabatan.index')->with('success', 'Data jabatan berhasil diperbarui.');
    }

    public function destroy(Jabatan $jabatan)
    {
        $jabatan->delete();
        return redirect()->route('jabatan.index')->with('success', 'Data jabatan berhasil dihapus.');
    }
}
