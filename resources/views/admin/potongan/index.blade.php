@extends('layouts.admin')

@section('title', 'Data Potongan Gaji')

@section('content')
  <h1 class="h3 mb-2 text-gray-800">Data Potongan Gaji</h1>
  <p class="mb-4">Daftar semua potongan gaji yang telah diinput untuk karyawan.</p>

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <div class="d-flex justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Potongan Gaji</h6>
        <a href="{{ route('potongan-gaji.create') }}" class="btn btn-primary btn-sm">
          <i class="fas fa-plus"></i> Tambah Potongan
        </a>
      </div>
    </div>
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-4">
          <div class="input-group">
            <input type="text" name="search" id="searchInput" class="form-control"
              placeholder="Ketik untuk mencari nama karyawan..." value="{{ request('search') }}">
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
      <div id="potonganTableContainer">
        @include('admin.potongan.partials.table')
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
        // Tampilkan indikator loading
        $('#potonganTableContainer').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');

        $.ajax({
          url: "{{ route('potongan-gaji.index') }}?page=" + page + "&search=" + search_term,
          success: function (data) {
            $('#potonganTableContainer').html(data);
          },
          error: function () {
            $('#potonganTableContainer').html('<div class="text-center text-danger py-5">Gagal memuat data. Silakan coba lagi.</div>');
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
      $(document).on('click', '#potonganTableContainer .pagination a', function (event) {
        event.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        let search_term = $('#searchInput').val();
        fetch_data(page, search_term);
      });
    });
  </script>
@endpush