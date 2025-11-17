@extends('layouts.admin')

@section('title', 'Tambah Karyawan')

@section('content')
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Generate NIP button
                document.getElementById('generateNip').addEventListener('click', function() {
                    // Get the latest NIP from the database or use default starting value
                    fetch('{{ route('api.get-last-nip') }}')
                        .then(response => response.json())
                        .then(data => {
                            let lastNip = data.last_nip || '0000000000';
                            let nextNip = (parseInt(lastNip) + 1).toString().padStart(10, '0');
                            document.getElementById('nip').value = nextNip;
                        })
                        .catch(error => {
                            // If API fails, generate a random 10-digit number starting with 001
                            let randomNip = '001' + Math.floor(1000000 + Math.random() * 9000000)
                                .toString();
                            document.getElementById('nip').value = randomNip;
                        });
                });

                // Format NIP input to only allow numbers and max 10 digits
                document.getElementById('nip').addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
                });
            });
        </script>
    @endpush
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
                            <div class="input-group">
                                <input type="text" name="nip" id="nip" class="form-control"
                                    value="{{ old('nip') }}" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="generateNip">
                                        <i class="fas fa-sync-alt"></i> Generate
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">Format: 10 digit (contoh: 0012345678)</small>
                        </div>
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control" required>
                                <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan</option>
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
                                    <option value="{{ $jabatan->id }}"
                                        {{ old('jabatan_id') == $jabatan->id ? 'selected' : '' }}>
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
                            <input type="date" name="tanggal_masuk" class="form-control"
                                value="{{ old('tanggal_masuk') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="nomor_telepon">Nomor Telepon</label>
                            <input type="text" name="nomor_telepon" class="form-control"
                                value="{{ old('nomor_telepon') }}">
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
