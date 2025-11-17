@extends('layouts.admin')

@section('title', 'Data Gaji Saya')

@section('content')

  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data Gaji Saya</h1>
  </div>

  <div class="row">
    <div class="col-lg-12">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Filter Riwayat Gaji</h6>
        </div>
        <div class="card-body">
          <div class="form-row align-items-end">
            <div class="col-md-5">
              <div class="form-group">
                <label for="bulanFilter">Bulan</label>
                <select name="bulan" id="bulanFilter" class="form-control">
                  @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == $i ? 'selected' : '' }}>
                      {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                    </option>
                  @endfor
                </select>
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group">
                <label for="tahunFilter">Tahun</label>
                <input type="number" name="tahun" id="tahunFilter" class="form-control" value="{{ $tahun }}">
              </div>
            </div>
            {{-- Tombol Tampilkan tidak diperlukan lagi dengan AJAX --}}
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Container untuk slip gaji yang akan di-refresh oleh AJAX --}}
  <div class="row">
    <div class="col-lg-12">
      <div class="card shadow mb-4" id="slipGajiContainer">
        @include('pegawai.gaji.partials.slip')
      </div>
    </div>
  </div>

@endsection

@push('scripts')
  <script>
    $(document).ready(function () {
      // Fungsi utama untuk memuat data via AJAX
      function fetchData() {
        let bulan = $('#bulanFilter').val();
        let tahun = $('#tahunFilter').val();

        // Tampilkan indikator loading
        $('#slipGajiContainer').html('<div class="card-body text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');

        let ajaxUrl = "{{ route('pegawai.gaji.index') }}?bulan=" + bulan + "&tahun=" + tahun;

        $.ajax({
          url: ajaxUrl,
          success: function (data) {
            $('#slipGajiContainer').html(data);
          },
          error: function () {
            $('#slipGajiContainer').html('<div class="card-body text-center text-danger py-5">Gagal memuat data. Silakan coba lagi.</div>');
          }
        });
      }

      // Event saat pengguna mengubah bulan atau tahun
      $('#bulanFilter, #tahunFilter').on('change', function () {
        fetchData();
      });
    });
  </script>
@endpush