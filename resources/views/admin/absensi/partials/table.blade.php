<form action="{{ route('absensi.store') }}" method="POST">
  @csrf
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
          <th class="text-center bg-light" width="25%">Kehadiran Hari Ini</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($rekapAbsensi as $rekap)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $rekap->nama_lengkap }}</td>
            <td>{{ $rekap->jabatan->nama_jabatan ?? 'N/A' }}</td>
            <td class="text-center">{{ $rekap->jumlah_hadir }}</td>
            <td class="text-center">{{ $rekap->jumlah_sakit }}</td>
            <td class="text-center">{{ $rekap->jumlah_alpha }}</td>
            <td class="text-center">
              @php
                $statusHariIni = $absensiHariIni[$rekap->id] ?? 'Hadir';
              @endphp
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="status[{{ $rekap->id }}]" id="hadir_{{ $rekap->id }}"
                  value="Hadir" {{ $statusHariIni == 'Hadir' ? 'checked' : '' }}>
                <label class="form-check-label" for="hadir_{{ $rekap->id }}">H</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="status[{{ $rekap->id }}]" id="sakit_{{ $rekap->id }}"
                  value="Sakit" {{ $statusHariIni == 'Sakit' ? 'checked' : '' }}>
                <label class="form-check-label" for="sakit_{{ $rekap->id }}">S</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="status[{{ $rekap->id }}]" id="alpha_{{ $rekap->id }}"
                  value="Alpha" {{ $statusHariIni == 'Alpha' ? 'checked' : '' }}>
                <label class="form-check-label" for="alpha_{{ $rekap->id }}">A</label>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center">
              @if(request('search'))
                Karyawan dengan nama "{{ request('search') }}" tidak ditemukan.
              @else
                Data absensi tidak ditemukan untuk periode ini.
              @endif
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if(count($rekapAbsensi) > 0)
    <button type="submit" class="btn btn-success mt-3 float-right"><i class="fa fa-save"></i> Simpan Absensi Hari
      Ini</button>
  @endif
</form>