@extends('layouts.admin')

@section('title', 'Edit Karyawan')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Karyawan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('karyawan.update', $karyawan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
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
                    {{-- <div class="col-md-6">
                        <h6 class="font-weight-bold text-primary">Data Diri Karyawan</h6>
                        <hr>
                        <div class="form-group">
                            <label for="nip">NIP</label>
                            <input type="text" name="nip" id="nip" class="form-control"
                                value="{{ old('nip', $karyawan->nip) }}" required
                                maxlength="13" minlength="8" pattern="[0-9]{8,13}"
                                inputmode="numeric" placeholder="Masukkan 8-13 digit NIP">
                            <small class="form-text text-muted">Format: 8-13 digit angka</small>
                        </div>
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control"
                                value="{{ old('nama_lengkap', $karyawan->nama_lengkap) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control" required>
                                <option value="Laki-laki" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="foto">Ganti Foto (Opsional)</label>
                            <input type="file" name="foto" id="foto" class="form-control" accept="image/png, image/jpeg, image/jpg">
                            <small class="text-danger d-none" id="foto-error">Ukuran foto maksimal 5MB!</small>
                            @if ($karyawan->foto)
                                <img src="{{ asset('storage/' . $karyawan->foto) }}" class="mt-2 img-thumbnail" width="100">
                            @endif
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
                                    <option value="{{ $jabatan->id }}" {{ old('jabatan_id', $karyawan->jabatan_id) == $jabatan->id ? 'selected' : '' }}>
                                        {{ $jabatan->nama_jabatan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <input type="text" name="status" class="form-control"
                                value="{{ old('status', $karyawan->status) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_masuk">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" class="form-control"
                                value="{{ old('tanggal_masuk', $karyawan->tanggal_masuk) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="nomor_telepon">Nomor Telepon</label>
                            <input type="text" name="nomor_telepon" class="form-control"
                                value="{{ old('nomor_telepon', $karyawan->nomor_telepon) }}">
                        </div>
                    </div> --}}
                    <div class="col-md-6 pr-3">
                        <h6 class="font-weight-bold text-primary">Data Pekerjaan</h6>
                        <hr>

                        {{-- 1. Jabatan --}}
                        <div class="form-group">
                            <label>Jabatan</label>
                            <select name="jabatan_id" class="form-control" required>
                                <option value="">-- Pilih Jabatan --</option>
                                @foreach ($jabatans as $jabatan)
                                    <option value="{{ $jabatan->id }}"
                                        {{ old('jabatan_id', $karyawan->jabatan_id) == $jabatan->id ? 'selected' : '' }}>
                                        {{ $jabatan->nama_jabatan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 2. Tanggal Masuk --}}
                        <div class="form-group">
                            <label>Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" class="form-control"
                                value="{{ old('tanggal_masuk', $karyawan->tanggal_masuk) }}" required>
                        </div>

                        {{-- 3. Status (ENUM FIX) --}}
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                @foreach (['Kontrak', 'Tetap', 'Magang', 'Paruh Waktu', 'Outsourcing', 'Pekerja Lepas', 'Harian Lepas'] as $status)
                                    <option value="{{ $status }}"
                                        {{ old('status', $karyawan->status) == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 4. Nomor Telepon --}}
                        <div class="form-group">
                            <label>Nomor Telepon</label>
                            <input type="text" name="nomor_telepon" class="form-control"
                                value="{{ old('nomor_telepon', $karyawan->nomor_telepon) }}">
                        </div>
                    </div>
                    <div class="col-md-6 pl-3">
                        <h6 class="font-weight-bold text-primary">Data Diri Karyawan</h6>
                        <hr>

                        {{-- 1. NIP --}}
                        <div class="form-group">
                            <label>NIP</label>

                            <div class="input-group">
                                <input type="text" name="nip" id="nip" class="form-control"
                                    value="{{ old('nip', $karyawan->nip) }}" maxlength="13" readonly>

                                <div class="input-group-append">
                                    <button type="button" class="btn btn-warning" id="editNipBtn" title="Edit NIP">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </div>

                            <small class="form-text text-muted">Klik tombol edit untuk mengubah NIP</small>
                        </div>

                        {{-- 2. Nama --}}
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control"
                                value="{{ old('nama_lengkap', $karyawan->nama_lengkap) }}" required>
                        </div>

                        {{-- 3. Jenis Kelamin --}}
                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control" required>
                                <option value="Laki-laki"
                                    {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki
                                </option>
                                <option value="Perempuan"
                                    {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan
                                </option>
                            </select>
                        </div>

                        {{-- 4. Foto --}}
                        {{-- <div class="form-group">
                            <label>Ganti Foto</label>
                            <input type="file" name="foto" id="foto" class="form-control">
                            @if ($karyawan->foto)
                                <img src="{{ asset('storage/' . $karyawan->foto) }}" class="mt-2 img-thumbnail"
                                    width="100">
                            @endif
                        </div> --}}
                        <div class="form-group">
                            <label for="foto">Foto</label>
                            <input type="file" name="foto" id="foto" class="form-control"
                                accept="image/png, image/jpeg, image/jpg">
                            <small class="text-danger d-none" id="foto-error">Ukuran foto maksimal 5MB!</small>
                        </div>
                    </div>
                </div>
                <hr>
                <h6 class="font-weight-bold text-primary">Tautkan Akun Login</h6>
                <div class="form-group">
                    <label for="user_id">Akun Pegawai</label>
                    <select name="user_id" id="user_id" class="form-control">
                        @if ($karyawan->user)
                            <option value="{{ $karyawan->user_id }}" selected>{{ $karyawan->user->name }}
                                ({{ $karyawan->user->email }}) - Sudah Tertaut</option>
                        @else
                            <option value="">-- Pilih Akun untuk Ditautkan --</option>
                            @foreach ($usersBelumTertaut as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        @endif
                    </select>
                    <small class="form-text text-muted">Pilih akun login dari daftar user yang belum memiliki data
                        karyawan.</small>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Update</button>
                <a href="{{ route('karyawan.index') }}" class="btn btn-secondary mt-3">Batal</a>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Format NIP input to only allow numbers and max 13 digits
                const nipInput = document.getElementById('nip');
                if (nipInput) {
                    nipInput.addEventListener('input', function(e) {
                        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13);
                    });
                }

                // File size validation to avoid PHP limitation
                const fotoInput = document.getElementById('foto');
                if (fotoInput) {
                    fotoInput.addEventListener('change', function() {
                        const file = this.files[0];
                        const errorMsg = document.getElementById('foto-error');
                        if (file && file.size > 10 * 1024 * 1024) { // 10MB
                            errorMsg.classList.remove('d-none');
                            this.value = ''; // Reset the input
                        } else {
                            errorMsg.classList.add('d-none');
                        }
                    });
                }

                const editBtn = document.getElementById('editNipBtn');

                if (editBtn) {
                    editBtn.addEventListener('click', function() {
                        const confirmEdit = confirm('Apakah yakin ingin mengubah NIP karyawan ini?');

                        if (confirmEdit) {
                            const nipInput = document.getElementById('nip');
                            nipInput.removeAttribute('readonly');
                            nipInput.focus();
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
