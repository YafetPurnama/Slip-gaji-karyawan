@extends('layouts.admin')

@section('title', 'Laporan Absensi Karyawan')

@section('content')
  <h1 class="h3 mb-2 text-gray-800">Laporan Absensi Karyawan</h1>
  <p class="mb-4">Gunakan filter di bawah untuk menampilkan dan mencetak laporan absensi.</p>

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
    </div>
    <div class="card-body">
      {{-- PERBAIKAN UTAMA ADA PADA STRUKTUR GRID DI BAWAH INI --}}
      <div class="row">
        <div class="col-md-3">
          <div class="form-group">
            <label for="bulanFilter">Bulan</label>
            <select name="bulan" id="bulanFilter" class="form-control">
              @for ($i = 1; $i <= 12; $i++)
                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ ($bulan ?? date('m')) == $i ? 'selected' : '' }}>
                  {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                </option>
              @endfor
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="tahunFilter">Tahun</label>
            <input type="number" name="tahun" id="tahunFilter" class="form-control" value="{{ $tahun ?? date('Y') }}">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="searchInput">Cari Nama Karyawan</label>
            <input type="text" name="search" id="searchInput" class="form-control" placeholder="Ketik untuk mencari...">
          </div>
        </div>
        <div class="col-md-3">
          <label>&nbsp;</label>
          <a href="#" id="printLink" class="btn btn-success btn-block" target="_blank">
            <i class="fas fa-print"></i> Cetak Laporan
          </a>
        </div>
      </div>
    </div>
  </div>

  {{-- Container untuk tabel yang akan di-refresh oleh AJAX --}}
  <div class="card shadow mb-4" id="laporanAbsensiContainer">
    {{-- Konten tabel akan dimuat di sini oleh AJAX --}}
  </div>

@endsection

@push('scripts')
  <script>
    $(document).ready(function () {
      let searchTimeout;

      // Fungsi utama untuk memuat data via AJAX
      function fetchData() {
        let bulan = $('#bulanFilter').val();
        let tahun = $('#tahunFilter').val();
        let search_term = $('#searchInput').val();

        // Update link cetak secara dinamis
        let printUrl = "{{ route('laporan-absensi.print') }}?bulan=" + bulan + "&tahun=" + tahun + "&search=" + search_term;
        $('#printLink').attr('href', printUrl);

        // Tampilkan indikator loading
        $('#laporanAbsensiContainer').html('<div class="card-body text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');

        // Buat URL AJAX dengan semua parameter filter
        let ajaxUrl = "{{ route('laporan-absensi.index') }}?bulan=" + bulan + "&tahun=" + tahun + "&search=" + search_term;

        $.ajax({
          url: ajaxUrl,
          success: function (data) {
            $('#laporanAbsensiContainer').html(data);
          },
          error: function () {
            $('#laporanAbsensiContainer').html('<div class="card-body text-center text-danger py-5">Gagal memuat data. Silakan coba lagi.</div>');
          }
        });
      }

      // Panggil fungsi saat halaman pertama kali dimuat
      fetchData();

      // Event saat pengguna mengetik di kolom pencarian
      $('#searchInput').on('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(fetchData, 500); // Jeda 500ms
      });

      // Event saat pengguna mengubah bulan atau tahun
      $('#bulanFilter, #tahunFilter').on('change', function () {
        fetchData();
      });

    });
  </script>
@endpush