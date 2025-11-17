<div class="table-responsive">
  <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th>No</th>
        <th>NIP</th>
        <th>Nama Pegawai</th>
        <th>Jabatan</th>
        <th>Foto</th>
        <th>Status</th>
        <th>Email</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($karyawans as $karyawan)
        <tr>
          <td>{{ $loop->iteration + $karyawans->firstItem() - 1 }}</td>
          <td>{{ $karyawan->nip }}</td>
          <td>{{ $karyawan->nama_lengkap }}</td>
          <td>{{ $karyawan->jabatan->nama_jabatan ?? 'Belum Diatur' }}</td>
          <td class="text-center">
            @if($karyawan->foto)
              <img src="{{ asset('storage/' . $karyawan->foto) }}" alt="{{ $karyawan->nama_lengkap }}" width="50"
                class="img-thumbnail rounded-circle">
            @else
              <img src="https://ui-avatars.com/api/?name={{ urlencode($karyawan->nama_lengkap) }}"
                alt="{{ $karyawan->nama_lengkap }}" width="50" class="img-thumbnail rounded-circle">
            @endif
          </td>
          <td><span
              class="badge badge-pill {{ $karyawan->status == 'Pegawai Tetap' ? 'badge-success' : 'badge-warning' }}">{{ $karyawan->status }}</span>
          </td>
          <td>{{ $karyawan->user->email ?? 'Belum ada' }}</td>
          <td>
            <a href="{{ route('karyawan.edit', $karyawan->id) }}" class="btn btn-info btn-circle btn-sm" title="Edit">
              <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('karyawan.destroy', $karyawan->id) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Yakin ingin menghapus karyawan dan akun loginnya?');">
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
          <td colspan="8" class="text-center">Tidak ada data karyawan ditemukan.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="mt-3">
  {{ $karyawans->links() }}
</div>