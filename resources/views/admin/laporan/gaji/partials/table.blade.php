<div class="card-header py-3">
  <h6 class="m-0 font-weight-bold text-primary">
    Laporan Gaji Bulan: {{ \Carbon\Carbon::create()->month((int) $bulan)->translatedFormat('F') }} {{ $tahun }}
  </h6>
</div>
<div class="card-body">
  <div class="table-responsive">
    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
      <thead>
        <tr>
          <th>No</th>
          <th>NIP</th>
          <th>Nama Karyawan</th>
          <th>Jabatan</th>
          <th>Gaji Bersih</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($laporanGaji as $gaji)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $gaji->karyawan->nip }}</td>
            <td>{{ $gaji->karyawan->nama_lengkap }}</td>
            <td>{{ $gaji->karyawan->jabatan->nama_jabatan ?? 'N/A' }}</td>
            <td><b>Rp {{ number_format($gaji->gaji_bersih, 0, ',', '.') }}</b></td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center">
              @if(request('search'))
                Karyawan dengan nama "{{ request('search') }}" tidak ditemukan.
              @else
                Tidak ada data untuk ditampilkan pada periode ini.
              @endif
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>