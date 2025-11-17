<div class="table-responsive">
  <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th>No</th>
        <th>Nama Karyawan</th>
        <th>Tanggal</th>
        <th>Jenis Potongan</th>
        <th>Jumlah</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($potonganGajis as $potongan)
        <tr>
          <td>{{ $loop->iteration + $potonganGajis->firstItem() - 1 }}</td>
          <td>{{ $potongan->karyawan->nama_lengkap ?? 'N/A' }}</td>
          <td>{{ \Carbon\Carbon::parse($potongan->tanggal)->format('d F Y') }}</td>
          <td>{{ $potongan->jenis_potongan }}</td>
          <td>Rp {{ number_format($potongan->jumlah, 0, ',', '.') }}</td>
          <td>
            <form action="{{ route('potongan-gaji.destroy', $potongan->id) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Yakin hapus data ini?');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger btn-circle btn-sm" title="Hapus">
                <i class="fas fa-trash"></i>
              </button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="text-center">
            @if(request('search'))
              Data potongan untuk karyawan "{{ request('search') }}" tidak ditemukan.
            @else
              Belum ada data potongan gaji.
            @endif
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="mt-3">
  {{-- Tambahkan ->appends(request()->query()) agar parameter pencarian tidak hilang saat pindah halaman --}}
  {{ $potonganGajis->appends(request()->query())->links() }}
</div>