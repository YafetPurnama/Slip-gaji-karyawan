@extends('layouts.admin')

@section('title', 'Data Karyawan')

@section('content')
  <h1 class="h3 mb-2 text-gray-800">Data Karyawan</h1>
  <p class="mb-4">Daftar semua karyawan yang terdaftar dalam sistem.</p>

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <div class="d-flex justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Tabel Data Karyawan</h6>
        <a href="{{ route('karyawan.create') }}" class="btn btn-primary btn-sm">
          <i class="fas fa-plus"></i> Tambah Pegawai
        </a>
      </div>
    </div>
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-4">
          {{-- Form ini sekarang tidak punya tombol submit, AJAX akan menanganinya --}}
          <div class="input-group">
            <input type="text" name="search" id="searchInput" class="form-control"
              placeholder="Ketik untuk mencari Nama/NIP..." value="{{ request('search') }}">
          </div>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      @endif

      {{-- Container untuk tabel yang akan di-refresh oleh AJAX --}}
      <div id="karyawanTableContainer">
        @include('admin.karyawan.partials.table')
      </div>

    </div>
  </div>
@endsection

@push('scripts')
  <script>
    $(document).ready(function () {
      let searchTimeout;

      // Fungsi untuk memuat data tabel via AJAX
      function fetch_data(page, search_term) {
        // Tampilkan loading spinner atau sejenisnya jika perlu
        $('#karyawanTableContainer').html('<div class="text-center">Memuat data...</div>');

        $.ajax({
          url: "{{ route('karyawan.index') }}?page=" + page + "&search=" + search_term,
          success: function (data) {
            $('#karyawanTableContainer').html(data);
          },
          error: function () {
            alert('Gagal memuat data. Silakan coba lagi.');
            $('#karyawanTableContainer').html('<div class="text-center text-danger">Gagal memuat data.</div>');
          }
        });
      }

      // Event saat pengguna mengetik di kolom pencarian
      $('#searchInput').on('keyup', function () {
        clearTimeout(searchTimeout);
        let search_term = $(this).val();

        // Beri jeda 500ms setelah pengguna berhenti mengetik sebelum mencari
        searchTimeout = setTimeout(function () {
          fetch_data(1, search_term);
        }, 500);
      });

      // Event untuk paginasi via AJAX
      $(document).on('click', '.pagination a', function (event) {
        event.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        let search_term = $('#searchInput').val();
        fetch_data(page, search_term);
      });
    });
  </script>
@endpush