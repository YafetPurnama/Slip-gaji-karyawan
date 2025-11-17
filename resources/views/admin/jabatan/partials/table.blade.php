<div class="table-responsive">
  <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th>No</th>
        <th>Nama Jabatan</th>
        <th>Gaji Pokok</th>
        <th>Tj. Transport</th>
        <th>Uang Makan</th>
        <th>Total</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($jabatans as $jabatan)
        <tr>
          <td>{{ $loop->iteration + $jabatans->firstItem() - 1 }}</td>
          <td>{{ $jabatan->nama_jabatan }}</td>
          <td>Rp {{ number_format($jabatan->gaji_pokok, 0, ',', '.') }}</td>
          <td>Rp {{ number_format($jabatan->tunjangan_transport, 0, ',', '.') }}</td>
          <td>Rp {{ number_format($jabatan->uang_makan, 0, ',', '.') }}</td>
          <td><b>Rp
              {{ number_format($jabatan->gaji_pokok + $jabatan->tunjangan_transport + $jabatan->uang_makan, 0, ',', '.') }}</b>
          </td>
          <td>
            <a href="{{ route('jabatan.edit', $jabatan->id) }}" class="btn btn-info btn-circle btn-sm" title="Edit">
              <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('jabatan.destroy', $jabatan->id) }}" method="POST" class="d-inline"
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
          <td colspan="7" class="text-center">Tidak ada data jabatan ditemukan.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="mt-3">
  {{ $jabatans->links() }}
</div>