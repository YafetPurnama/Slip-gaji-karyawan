<div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">
        Laporan Absensi Bulan: {{ \Carbon\Carbon::create()->month((int) $bulan)->translatedFormat('F') }}
        {{ $tahun }}
    </h6>
</div>
<div class="card-body">
    <div class="table-responsive">
        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Karyawan</th>
                    <th>Jabatan</th>
                    <th class="text-center">Hadir</th>
                    <th class="text-center">Sakit</th>
                    <th class="text-center">Alpha</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($laporanAbsensi as $absensi)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $absensi->nama_lengkap }}</td>
                        <td>{{ $absensi->jabatan->nama_jabatan ?? 'N/A' }}</td>
                        <td class="text-center">{{ $absensi->jumlah_hadir }}</td>
                        <td class="text-center">{{ $absensi->jumlah_sakit }}</td>
                        <td class="text-center">{{ $absensi->jumlah_alpha }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info view-calendar" data-karyawan-id="{{ $absensi->id }}"
                                data-karyawan-nama="{{ $absensi->nama_lengkap }}" data-bulan="{{ $bulan }}"
                                data-tahun="{{ $tahun }}">
                                <i class="fas fa-calendar-alt"></i> Lihat
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            @if (request('search'))
                                Karyawan dengan nama "{{ request('search') }}" tidak ditemukan.
                            @else
                                Tidak ada data absensi untuk ditampilkan pada periode ini.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
