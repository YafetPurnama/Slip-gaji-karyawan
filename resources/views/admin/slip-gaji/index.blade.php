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
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
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
                        <input type="number" name="tahun" id="tahun" class="form-control"
                            placeholder="Contoh: {{ date('Y') }}" value="{{ date('Y') }}" required>
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
                {{-- <button type="submit" class="btn btn-success w-100 mt-3"><i class="fas fa-print"></i> Cetak Slip
                    Gaji</button> --}}
                <div class="d-flex mt-3">
                    <button type="submit" class="btn btn-success flex-fill mr-2">
                        <i class="fas fa-print"></i> Cetak Slip Gaji
                    </button>
                    <button type="button" class="btn btn-primary flex-fill ml-2" data-toggle="modal"
                        data-target="#modalBagikan">
                        <i class="fas fa-paper-plane"></i> Bagikan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalBagikan" tabindex="-1" aria-labelledby="modalBagikanLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalBagikanLabel">Bagikan Slip Gaji via Email</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('slip-gaji.send-email') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="small text-muted">Pilih parameter slip gaji yang akan dikirim.</p>

                        <div class="form-group">
                            <label>Bulan</label>
                            <select name="bulan" class="form-control" required>
                                <option value="">-- Pilih Bulan --</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">
                                        {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Tahun</label>
                            <input type="number" name="tahun" class="form-control" value="{{ date('Y') }}" required>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label>Pilih Karyawan (Otomatis isi email)</label>
                            <select id="modal_karyawan_id" class="form-control" onchange="updateEmail()">
                                <option value="" data-email="">-- Pilih Karyawan --</option>
                                @foreach ($karyawans as $karyawan)
                                    <option value="{{ $karyawan->id }}" data-email="{{ $karyawan->user->email ?? '' }}">
                                        {{ $karyawan->nama_lengkap }}
                                        ({{ $karyawan->user->email ?? 'Email tidak ditemukan' }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="karyawan_id" id="final_karyawan_id">
                        </div>

                        <div class="form-group">
                            <label>Alamat Email Tujuan</label>
                            <input type="email" name="email" id="email_tujuan" class="form-control"
                                placeholder="johndoe@example.com" required>
                            <small class="text-muted">Email akan terisi otomatis jika memilih karyawan, atau ketik
                                manual.</small>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Kirim
                            Email</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateEmail() {
            var select = document.getElementById("modal_karyawan_id");
            var emailInput = document.getElementById("email_tujuan");
            var hiddenId = document.getElementById("final_karyawan_id");

            var selectedOption = select.options[select.selectedIndex];
            var email = selectedOption.getAttribute("data-email");
            var id = select.value;


            if (email) {
                emailInput.value = email;
            } else {
                emailInput.value = "";
            }
            hiddenId.value = id;
        }
    </script>


@endsection
