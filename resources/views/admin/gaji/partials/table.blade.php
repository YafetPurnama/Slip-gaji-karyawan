<div class="table-responsive">
  <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th>No</th>
        <th>Nama Karyawan</th>
        <th>Jabatan</th>
        <th class="text-right">Gaji Pokok</th>
        <th class="text-right">Tunjangan</th>
        <th class="text-right">Uang Makan</th>
        <th class="text-right">Gaji Kotor</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($dataGaji as $gaji)
        <tr>
          <td>{{ $loop->iteration + $dataGaji->firstItem() - 1 }}</td>
          <td>{{ $gaji->nama_lengkap }}</td>
          <td>{{ $gaji->jabatan->nama_jabatan ?? 'N/A' }}</td>
          <td class="text-right">Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}</td>
          <td class="text-right">Rp {{ number_format($gaji->tunjangan_transport, 0, ',', '.') }}</td>
          <td class="text-right">Rp {{ number_format($gaji->uang_makan, 0, ',', '.') }}</td>
          <td class="text-right"><b>Rp {{ number_format($gaji->gaji_kotor, 0, ',', '.') }}</b></td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="text-center">
            @if(request('search'))
              Karyawan dengan nama "{{ request('search') }}" tidak ditemukan.
            @else
              Tidak ada data karyawan.
            @endif
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="mt-3">
  {{-- Tambahkan ->appends() agar parameter pencarian tidak hilang saat pindah halaman --}}
  {{ $dataGaji->appends(request()->query())->links() }}
</div>