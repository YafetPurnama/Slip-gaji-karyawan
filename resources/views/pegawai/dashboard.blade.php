@extends('layouts.admin')

@section('title', 'Dashboard Pegawai')

@section('content')

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    <span class="text-muted">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
  </div>

  <div class="row">
    <div class="col-xl-4 col-lg-5">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Profil Saya</h6>
        </div>
        <div class="card-body text-center">
          @if($karyawan->foto)
            <img class="img-fluid rounded-circle mb-3" src="{{ asset('storage/' . $karyawan->foto) }}" alt="Foto Profil"
              style="width: 150px; height: 150px; object-fit: cover;">
          @else
            <img class="img-fluid rounded-circle mb-3"
              src="https://ui-avatars.com/api/?name={{ urlencode($karyawan->nama_lengkap) }}&size=150&background=4e73df&color=fff"
              alt="Foto Profil">
          @endif
          <h5 class="font-weight-bold">{{ $karyawan->nama_lengkap }}</h5>
          <p class="text-muted mb-1">{{ $karyawan->jabatan->nama_jabatan }}</p>
          <p class="text-muted small">NIP: {{ $karyawan->nip }}</p>
        </div>
      </div>
    </div>

    <div class="col-xl-8 col-lg-7">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Rincian Gaji Bulan Ini
            ({{ \Carbon\Carbon::now()->translatedFormat('F Y') }})</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-borderless">
              <tbody>
                <tr>
                  <td>Gaji Pokok</td>
                  <td class="text-right font-weight-bold">Rp {{ number_format($dataGaji->gaji_pokok, 0, ',', '.') }}</td>
                </tr>
                <tr>
                  <td>Tunjangan Transport</td>
                  <td class="text-right font-weight-bold">Rp
                    {{ number_format($dataGaji->tunjangan_transport, 0, ',', '.') }}</td>
                </tr>
                <tr>
                  <td>Uang Makan</td>
                  <td class="text-right font-weight-bold">Rp {{ number_format($dataGaji->uang_makan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                  <td>Potongan</td>
                  <td class="text-right font-weight-bold text-danger">- Rp
                    {{ number_format($dataGaji->potongan, 0, ',', '.') }}</td>
                </tr>
                <tr class="bg-light">
                  <th class="h5">Gaji Bersih</th>
                  <th class="text-right h5">Rp {{ number_format($dataGaji->gaji_bersih, 0, ',', '.') }}</th>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4 mb-4">
          <div class="card border-left-success h-100 py-2">
            <div class="card-body">
              <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hadir</div>
              <div class="h5 mb-0 font-weight-bold">{{ $jumlahHadir }} Hari</div>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card border-left-info h-100 py-2">
            <div class="card-body">
              <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sakit</div>
              <div class="h5 mb-0 font-weight-bold">{{ $jumlahSakit }} Hari</div>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card border-left-danger h-100 py-2">
            <div class="card-body">
              <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Alpha</div>
              <div class="h5 mb-0 font-weight-bold">{{ $jumlahAlpha }} Hari</div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
@endsection