@extends('layouts.admin')

@section('title', 'Tambah Karyawan')

@section('content')
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Generate NIP logika OLD
                // document.getElementById('generateNip').addEventListener('click', function() {
                //     // Get the latest NIP from the database or use default starting value
                //     fetch('{{ route('api.get-last-nip') }}')
                //         .then(response => response.json())
                //         .then(data => {
                //             let lastNip = data.last_nip || '0000000000000';
                //             let nextNip = (parseInt(lastNip) + 1).toString().padStart(13, '0');
                //             document.getElementById('nip').value = nextNip;
                //         })
                //         .catch(error => {
                //             // If API fails, generate a random 13-digit number
                //             let randomNip = '001' + Math.floor(1000000000 + Math.random() * 9000000000)
                //                 .toString();
                //             document.getElementById('nip').value = randomNip;
                //         });
                // });

                // Generate NIP logika baru dengan format tahun + 13 digit acak
                document.getElementById('generateNip').addEventListener('click', function() {
                    const jabatanSelect = document.querySelector('[name="jabatan_id"]');
                    const status = document.querySelector('[name="status"]').value;
                    const tanggalMasuk = document.querySelector('[name="tanggal_masuk"]').value;

                    if (!jabatanSelect.value) {
                        alert('Pilih jabatan terlebih dahulu!');
                        return;
                    }

                    if (!status) {
                        alert('Pilih status terlebih dahulu!');
                        return;
                    }

                    if (!tanggalMasuk) {
                        alert('Isi tanggal masuk terlebih dahulu!');
                        return;
                    }

                    // ========================
                    // FORMAT TANGGAL (YYMM)
                    // ========================
                    const date = new Date(tanggalMasuk);
                    const year = date.getFullYear().toString().slice(-2);
                    const month = String(date.getMonth() + 1).padStart(2, '0');

                    // ========================
                    // MAPPING STATUS (2 DIGIT)
                    // ========================
                    const statusMap = {
                        'Kontrak': '01',
                        'Tetap': '02',
                        'Magang': '03',
                        'Paruh Waktu': '04',
                        'Outsourcing': '05',
                        'Pekerja Lepas': '06',
                        'Harian Lepas': '07'
                    };

                    const kodeStatus = statusMap[status] || '00';

                    // ========================
                    // JABATAN (2 DIGIT)
                    // ========================
                    // const kodeJabatan = String(jabatan).padStart(2, '0');
                    const kodeJabatan = String(jabatanSelect.value).padStart(2, '0');

                    // ========================
                    // AMBIL URUTAN TERAKHIR
                    // ========================
                    fetch('{{ route('api.get-last-nip') }}')
                        .then(res => res.json())
                        .then(data => {

                            let lastNumber = 0;

                            if (data.last_nip) {
                                lastNumber = parseInt(data.last_nip.slice(-5)) || 0;
                            }

                            let nextNumber = String(lastNumber + 1).padStart(5, '0');

                            let nipBaru = `${year}${month}${kodeStatus}${kodeJabatan}${nextNumber}`;

                            document.getElementById('nip').value = nipBaru;
                        })
                        .catch(() => {
                            // let random = Math.floor(10000 + Math.random() * 90000);
                            let random = String(Math.floor(1 + Math.random() * 99999)).padStart(5, '0');
                            let nipBaru = `${year}${month}${kodeStatus}${kodeJabatan}${random}`;
                            document.getElementById('nip').value = nipBaru;
                        });
                });

                const editBtn = document.getElementById('editNipBtn');

                if (editBtn) {
                    editBtn.addEventListener('click', function() {
                        const confirmEdit = confirm('Apakah yakin ingin mengubah NIP ini?');

                        if (confirmEdit) {
                            const nipInput = document.getElementById('nip');
                            nipInput.removeAttribute('readonly');
                            nipInput.focus();
                        }
                    });
                }

                // Format NIP input to only allow numbers and max 13 digits
                document.getElementById('nip').addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13);
                });

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
                // =========================
                // AUTO GENERATE NIP
                // =========================
                function autoGenerate() {
                    const jabatan = document.querySelector('[name="jabatan_id"]').value;
                    const status = document.querySelector('[name="status"]').value;
                    const tanggal = document.querySelector('[name="tanggal_masuk"]').value;

                    if (jabatan && status && tanggal) {
                        document.getElementById('generateNip').click();
                    }
                }

                // Trigger saat berubah
                document.querySelector('[name="jabatan_id"]').addEventListener('change', autoGenerate);
                document.querySelector('[name="status"]').addEventListener('change', autoGenerate);
                document.querySelector('[name="tanggal_masuk"]').addEventListener('change', autoGenerate);

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

                    {{-- ================= LEFT: DATA PEKERJAAN ================= --}}
                    {{-- <div class="col-md-6"> --}}
                    <div class="col-md-6 pr-3">
                        <h6 class="font-weight-bold text-primary">Data Pekerjaan</h6>
                        <hr>

                        {{-- 1. Jabatan --}}
                        <div class="form-group">
                            <label for="jabatan_id">Jabatan</label>
                            <select name="jabatan_id" id="jabatan_id" class="form-control" required>
                                <option value="">-- Pilih Jabatan --</option>
                                @foreach ($jabatans as $jabatan)
                                    <option value="{{ $jabatan->id }}"
                                        {{ old('jabatan_id') == $jabatan->id ? 'selected' : '' }}>
                                        {{ $jabatan->nama_jabatan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 2. Tanggal Masuk --}}
                        <div class="form-group">
                            <label for="tanggal_masuk">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control"
                                value="{{ old('tanggal_masuk') }}" required>
                        </div>

                        {{-- 3. Status (ENUM) --}}
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="">-- Pilih Status --</option>
                                @foreach (['Kontrak', 'Tetap', 'Magang', 'Paruh Waktu', 'Outsourcing', 'Pekerja Lepas', 'Harian Lepas'] as $status)
                                    <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 4. Nomor Telepon --}}
                        <div class="form-group">
                            <label for="nomor_telepon">Nomor Telepon</label>
                            <input type="text" name="nomor_telepon" class="form-control"
                                value="{{ old('nomor_telepon') }}">
                        </div>
                    </div>


                    {{-- ================= RIGHT: DATA DIRI ================= --}}
                    {{-- <div class="col-md-6"> --}}
                    <div class="col-md-6 pl-3">
                        <h6 class="font-weight-bold text-primary">Data Diri Karyawan</h6>
                        <hr>

                        {{-- 1. NIP --}}

                        {{-- //LOGIKA LAMA --}}
                        {{-- <div class="input-group">
                                <input type="text" name="nip" id="nip" class="form-control"
                                    value="{{ old('nip') }}" required maxlength="13" minlength="8" pattern="[0-9]{8,13}"
                                    inputmode="numeric" placeholder="Masukkan 8-13 digit NIP">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="generateNip">
                                        <i class="fas fa-sync-alt"></i> Generate
                                    </button>
                                </div>
                            </div> --}}


                        <div class="form-group">
                            <label for="nip">NIP</label>

                            <div class="input-group">
                                <input type="text" name="nip" id="nip" class="form-control"
                                    value="{{ old('nip') }}" maxlength="13" minlength="8" pattern="[0-9A-Za-z]{8,13}"
                                    readonly>

                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="generateNip">
                                        <i class="fas fa-sync-alt"></i> Generate
                                    </button>
                                </div>
                                <div class="input-group-append">
                                    {{-- <button type="button" class="btn btn-warning" id="editNipBtn">
                                        Edit
                                    </button> --}}
                                    <button type="button" class="btn btn-warning" id="editNipBtn" title="Edit NIP">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </div>

                            <small class="form-text text-muted">Klik tombol edit untuk mengubah NIP</small>
                        </div>

                        {{-- 2. Nama --}}
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control"
                                value="{{ old('nama_lengkap') }}" required>
                        </div>

                        {{-- 3. Jenis Kelamin --}}
                        <div class="form-group">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control" required>
                                <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                        </div>

                        {{-- 4. Foto --}}
                        <div class="form-group">
                            <label for="foto">Foto</label>
                            <input type="file" name="foto" id="foto" class="form-control"
                                accept="image/png, image/jpeg, image/jpg">
                            <small class="text-danger d-none" id="foto-error">Ukuran foto maksimal 5MB!</small>
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
