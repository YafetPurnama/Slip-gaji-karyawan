@extends('layouts.admin')

@section('title', 'Tambah Jabatan')

@section('content')
  <div class="card shadow mb-4">
    <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Form Tambah Jabatan</h6>
    </div>
    <div class="card-body">
    <form action="{{ route('jabatan.store') }}" method="POST">
      @csrf
      <div class="form-group">
      <label for="nama_jabatan">Nama Jabatan</label>
      <input type="text" name="nama_jabatan" class="form-control @error('nama_jabatan') is-invalid @enderror"
        value="{{ old('nama_jabatan') }}" required>
      @error('nama_jabatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="form-group">
      <label for="gaji_pokok">Gaji Pokok</label>
      <input type="number" name="gaji_pokok" class="form-control @error('gaji_pokok') is-invalid @enderror"
        value="{{ old('gaji_pokok') }}" required>
      @error('gaji_pokok')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="form-group">
      <label for="tunjangan_transport">Tunjangan Transport</label>
      <input type="number" name="tunjangan_transport"
        class="form-control @error('tunjangan_transport') is-invalid @enderror"
        value="{{ old('tunjangan_transport') }}" required>
      @error('tunjangan_transport')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="form-group">
      <label for="uang_makan">Uang Makan</label>
      <input type="number" name="uang_makan" class="form-control @error('uang_makan') is-invalid @enderror"
        value="{{ old('uang_makan') }}" required>
      @error('uang_makan')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <button type="submit" class="btn btn-primary">Simpan</button>
      <a href="{{ route('jabatan.index') }}" class="btn btn-secondary">Batal</a>
    </form>
    </div>
  </div>
@endsection