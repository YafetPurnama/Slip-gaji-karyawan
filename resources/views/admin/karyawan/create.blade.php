@extends('layouts.admin')

@section('title', 'Tambah Karyawan')

@section('content')
  <div class="card shadow mb-4">
    <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Form Tambah Karyawan</h6>
    </div>
    <div class="card-body">
    <form action="{{ route('karyawan.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @if ($errors->any())
      <div class="alert alert-danger">
      <ul class="mb-0">
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
      </ul>
      </div>
    @endif

      <div class="row">
      <div class="col-md-6">
        <h6 class="font-weight-bold text-primary">Data Diri Karyawan</h6>
        <hr>
        <div class="form-group">
        <label for="nip">NIP</label>
        <input type="text" name="nip" class="form-control" value="{{ old('nip') }}" required>
        </div>
        <div class="form-group">
        <label for="nama_lengkap">Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}" required>
        </div>
        <div class="form-group">
        <label for="jenis_kelamin">Jenis Kelamin</label>
        <select name="jenis_kelamin" class="form-control" required>
          <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
          <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
        </select>
        </div>
        <div class="form-group">
        <label for="foto">Foto</label>
        <input type="file" name="foto" class="form-control">
        </div>
      </div>
      <div class="col-md-6">
        <h6 class="font-weight-bold text-primary">Data Pekerjaan</h6>
        <hr>
        <div class="form-group">
        <label for="jabatan_id">Jabatan</label>
        <select name="jabatan_id" class="form-control" required>
          <option value="">-- Pilih Jabatan --</option>
          @foreach ($jabatans as $jabatan)
        <option value="{{ $jabatan->id }}" {{ old('jabatan_id') == $jabatan->id ? 'selected' : '' }}>
        {{ $jabatan->nama_jabatan }}
        </option>
      @endforeach
        </select>
        </div>
        <div class="form-group">
        <label for="status">Status</label>
        <input type="text" name="status" class="form-control" value="{{ old('status') }}" required>
        </div>
        <div class="form-group">
        <label for="tanggal_masuk">Tanggal Masuk</label>
        <input type="date" name="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk') }}" required>
        </div>
        <div class="form-group">
        <label for="nomor_telepon">Nomor Telepon</label>
        <input type="text" name="nomor_telepon" class="form-control" value="{{ old('nomor_telepon') }}">
        </div>
      </div>
      </div>

      <hr class="mt-4">
      <h6 class="font-weight-bold text-primary">Akun Login Pegawai</h6>
      <hr>
      <div class="row">
      <div class="col-md-4">
        <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" class="form-control" required>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
        <label for="password_confirmation">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
        </div>
      </div>
      </div>

      <button type="submit" class="btn btn-primary mt-3">Simpan</button>
      <a href="{{ route('karyawan.index') }}" class="btn btn-secondary mt-3">Batal</a>
    </form>
    </div>
  </div>
@endsection