@extends('layouts.admin')

@section('title', 'Ganti Password')

@section('content')

  <!-- Page Heading -->
  <h1 class="h3 mb-2 text-gray-800">Ganti Password</h1>
  <p class="mb-4">Untuk menjaga keamanan akun, ganti password Anda secara berkala.</p>

  <div class="row">
    <div class="col-lg-6">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Formulir Ganti Password</h6>
        </div>
        <div class="card-body">

          {{-- Container untuk menampilkan notifikasi sukses/gagal dari AJAX --}}
          <div id="responseMessage" class="mb-3"></div>

          <form id="changePasswordForm" action="{{ route('pegawai.password.update') }}" method="POST">
            @csrf
            <div class="form-group">
              <label for="current_password">Password Saat Ini</label>
              <input type="password" name="current_password" id="current_password" class="form-control" required>
              <small class="form-text text-danger" id="error_current_password"></small>
            </div>

            <div class="form-group">
              <label for="password">Password Baru</label>
              <input type="password" name="password" id="password" class="form-control" required>
              <small class="form-text text-danger" id="error_password"></small>
            </div>

            <div class="form-group">
              <label for="password_confirmation">Konfirmasi Password Baru</label>
              <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                required>
            </div>

            <button type="submit" class="btn btn-primary">
              <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
              Ubah Password
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    $(document).ready(function () {
      // Intercept form submission
      $('#changePasswordForm').on('submit', function (event) {
        event.preventDefault(); // Mencegah form dikirim secara tradisional

        let form = $(this);
        let button = form.find('button[type="submit"]');
        let spinner = button.find('.spinner-border');

        // Hapus pesan error lama
        $('.form-text.text-danger').text('');
        $('#responseMessage').html('');

        // Tampilkan loading spinner dan nonaktifkan tombol
        spinner.removeClass('d-none');
        button.attr('disabled', true);

        $.ajax({
          url: form.attr('action'),
          method: 'POST',
          data: form.serialize(),
          success: function (response) {
            // Tampilkan pesan sukses
            $('#responseMessage').html('<div class="alert alert-success">' + response.message + '</div>');
            // Kosongkan form
            form[0].reset();
          },
          error: function (xhr) {
            // Tampilkan pesan error validasi
            if (xhr.status === 422) {
              let errors = xhr.responseJSON.errors;
              $.each(errors, function (key, value) {
                $('#error_' + key).text(value[0]);
              });
            } else {
              // Tampilkan pesan error umum
              $('#responseMessage').html('<div class="alert alert-danger">Terjadi kesalahan. Silakan coba lagi.</div>');
            }
          },
          complete: function () {
            // Sembunyikan loading spinner dan aktifkan kembali tombol
            spinner.addClass('d-none');
            button.attr('disabled', false);
          }
        });
      });
    });
  </script>
@endpush