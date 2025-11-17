@extends('layouts.admin')

@section('title', 'Tambah Potongan Gaji')

@section('content')
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Form Tambah Potongan Gaji</h6>
    </div>
    <div class="card-body">
      <form action="{{ route('potongan-gaji.store') }}" method="POST">
        @csrf
        <div class="form-group">
          <label for="jenis_potongan">Jenis Potongan</label>
          <input type="text" name="jenis_potongan" class="form-control @error('jenis_potongan') is-invalid @enderror"
            value="{{ old('jenis_potongan') }}" required>
          @error('jenis_potongan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label for="jumlah">Jumlah Potongan (Rp)</label>
          <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror"
            value="{{ old('jumlah') }}" required>
          @error('jumlah')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label for="tanggal">Tanggal Potongan</label>
          <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
            value="{{ old('tanggal', date('Y-m-d')) }}" required>
          @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label for="karyawan_ids">Berlaku Untuk Karyawan</label>
          <select name="karyawan_ids[]" id="karyawan_ids" class="form-control" multiple required>
            <option value="all">-- SEMUA KARYAWAN --</option>
            @foreach ($karyawans as $karyawan)
              <option value="{{ $karyawan->id }}">{{ $karyawan->nama_lengkap }} ({{ $karyawan->nip }})</option>
            @endforeach
          </select>
          <small class="form-text text-muted">Tahan tombol Ctrl (atau Cmd di Mac) untuk memilih lebih dari satu
            karyawan.</small>
          @error('karyawan_ids')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-primary">Simpan Potongan</button>
        <a href="{{ route('potongan-gaji.index') }}" class="btn btn-secondary">Batal</a>
      </form>
    </div>
  </div>
@endsection