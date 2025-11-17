@extends('layouts.admin')

@section('title', 'Cetak Slip Gaji')

@section('content')
  <h1 class="h3 mb-2 text-gray-800">Slip Gaji Karyawan</h1>
  <p class="mb-4">Gunakan form di bawah untuk mencari dan mencetak slip gaji untuk karyawan tertentu pada periode yang
    dipilih.</p>

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Filter Pencarian Slip Gaji</h6>
    </div>
    <div class="card-body">
      <form action="{{ route('slip-gaji.print') }}" method="POST" target="_blank">
        @csrf
        @if($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="bulan">Pilih Bulan</label>
            <select name="bulan" id="bulan" class="form-control" required>
              <option value="">-- Pilih --</option>
              @for ($i = 1; $i <= 12; $i++)
                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">
                  {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                </option>
              @endfor
            </select>
          </div>
          <div class="form-group col-md-4">
            <label for="tahun">Masukkan Tahun</label>
            <input type="number" name="tahun" id="tahun" class="form-control" placeholder="Contoh: {{ date('Y') }}"
              value="{{ date('Y') }}" required>
          </div>
          <div class="form-group col-md-4">
            <label for="karyawan_id">Pilih Karyawan</label>
            <select name="karyawan_id" id="karyawan_id" class="form-control" required>
              <option value="">-- Pilih --</option>
              @foreach ($karyawans as $karyawan)
                <option value="{{ $karyawan->id }}">{{ $karyawan->nama_lengkap }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <button type="submit" class="btn btn-success w-100 mt-3"><i class="fas fa-print"></i> Cetak Slip Gaji</button>
      </form>
    </div>
  </div>
@endsection