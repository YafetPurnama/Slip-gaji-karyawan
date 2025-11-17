@extends('layouts.admin')

@section('title', 'Data Gaji Karyawan')

@section('content')
  <h1 class="h3 mb-2 text-gray-800">Data Gaji Karyawan</h1>
  <p class="mb-4">Daftar gaji kotor (sebelum potongan) untuk semua karyawan berdasarkan jabatan yang berlaku.</p>

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Daftar Gaji Karyawan</h6>
    </div>
    <div class="card-body">
      <!-- Search Form -->
      <div class="row mb-3">
        <div class="col-md-4">
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
      <div id="gajiTableContainer">
        @include('admin.gaji.partials.table')
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
        $('#gajiTableContainer').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');

        $.ajax({
          url: "{{ route('data-gaji.index') }}?page=" + page + "&search=" + search_term,
          success: function (data) {
            $('#gajiTableContainer').html(data);
          },
          error: function () {
            $('#gajiTableContainer').html('<div class="text-center text-danger py-5">Gagal memuat data. Silakan coba lagi.</div>');
          }
        });
      }

      // Event saat pengguna mengetik di kolom pencarian
      $('#searchInput').on('keyup', function () {
        clearTimeout(searchTimeout);
        let search_term = $(this).val();

        searchTimeout = setTimeout(function () {
          fetch_data(1, search_term);
        }, 500); // Jeda 500ms
      });

      // Event untuk paginasi via AJAX
      $(document).on('click', '#gajiTableContainer .pagination a', function (event) {
        event.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        let search_term = $('#searchInput').val();
        fetch_data(page, search_term);
      });
    });
  </script>
@endpush