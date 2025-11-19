@extends('layouts.admin')

{{-- @push('styles')
    <style>
        /* Ensure the content takes at least the full viewport height minus header and footer */
        #content-wrapper {
            min-height: calc(100vh - 56px);
            /* 56px is the height of the navbar */
            display: flex;
            flex-direction: column;
        }

        /* Make the main content area grow to push the footer down */
        #content {
            flex: 1 0 auto;
        }

        /* Ensure the footer stays at the bottom */
        .sticky-footer {
            flex-shrink: 0;
        }
    </style>
@endpush --}}

@section('title', 'Laporan Absensi Karyawan')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Laporan Absensi Karyawan</h1>
    <p class="mb-4">Gunakan filter di bawah untuk menampilkan dan mencetak laporan absensi.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="bulanFilter">Bulan</label>
                        <select name="bulan" id="bulanFilter" class="form-control">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                                    {{ ($bulan ?? date('m')) == $i ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tahunFilter">Tahun</label>
                        <input type="number" name="tahun" id="tahunFilter" class="form-control"
                            value="{{ $tahun ?? date('Y') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="searchInput">Cari Nama Karyawan</label>
                        <div class="form-group mb-0">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0"
                                        style="border-radius: 0.35rem 0 0 0.35rem !important; height: 38px; display: flex; align-items: center;">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                </div>
                                <input type="text" name="search" id="searchInput" class="form-control"
                                    placeholder="Ketik untuk mencari..." value="{{ request('search') }}"
                                    style="border-radius: 0 !important; height: 38px;">
                                <div class="input-group-append">
                                    <span class="input-group-text bg-white border-left-0 text-muted small"
                                        style="border-radius: 0 0.35rem 0.35rem 0 !important; height: 38px; display: flex; align-items: center;">
                                        <kbd class="bg-light text-dark border"
                                            style="padding: 0.1rem 0.3rem; border-radius: 0.2rem;">Ctrl</kbd> +
                                        <kbd class="bg-light text-dark border"
                                            style="padding: 0.1rem 0.3rem; border-radius: 0.2rem;">K</kbd>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <a href="#" id="printLink" class="btn btn-primary btn-block mb-2" target="_blank">
                        <i class="fas fa-print"></i> Cetak
                    </a>
                    <a href="#" id="exportLink" class="btn btn-success btn-block"
                        style="background-color: #1b5e20; border-color: #1b5e20;">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                </div>
            </div>
        </div>

        {{-- AJAX --}}
        {{-- <div class="card shadow mb-4" id="laporanAbsensiContainer" style="min-height: 45vh;">
            <div class="card-body d-flex flex-column align-items-center justify-content-center text-muted"
                style="height: 100%;">
                <i class="fas fa-file-alt fa-3x mb-3 text-gray-300"></i>
                <p>Data laporan akan muncul di sini setelah dimuat.</p>
            </div>
        </div> --}}

        {{-- <div class="card shadow mb-4" id="laporanAbsensiContainer" style="min-height: auto;">
            <div class="card-body d-flex flex-column align-items-center justify-content-center text-muted"
                style="height: 100%; min-height: 65vh;">
                <i class="fas fa-file-alt fa-4x mb-3 text-gray-300"></i>
                <h5 class="font-weight-bold">Belum ada data yang ditampilkan</h5>
                <p>Silakan pilih Bulan & Tahun, lalu data akan muncul di sini.</p>
            </div>
        </div> --}}

        <div class="card shadow mb-4" id="laporanAbsensiContainer" style="min-height: auto;">

            {{-- Inner Content juga set min-height agar vertikal align center bekerja --}}
            <div class="card-body d-flex flex-column align-items-center justify-content-center text-muted"
                style="height: 100%; min-height: auto;">

                <i class="fas fa-file-alt fa-4x mb-3 text-gray-300"></i>
                <h5 class="font-weight-bold">Belum ada data yang ditampilkan</h5>
                <p>Silakan pilih Bulan & Tahun, lalu data akan muncul di sini.</p>
            </div>

        </div>


        {{-- <div class="card shadow mb-4" id="laporanAbsensiContainer">
        </div> --}}

    @endsection

    @push('scripts')
        <script>
            $(document).ready(function() {
                let searchTimeout;

                // AJAX
                function fetchData() {
                    let bulan = $('#bulanFilter').val();
                    let tahun = $('#tahunFilter').val();
                    let search_term = $('#searchInput').val();

                    let printUrl = "{{ route('laporan-absensi.print') }}?bulan=" + bulan + "&tahun=" + tahun +
                        "&search=" + search_term;
                    let exportUrl = "{{ route('laporan-absensi.export') }}?bulan=" + bulan + "&tahun=" + tahun +
                        "&search=" + search_term;

                    $('#printLink').attr('href', printUrl);
                    $('#exportLink').attr('href', exportUrl);
                    $('#laporanAbsensiContainer').html(
                        // '<div class="card-body text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>'
                        '<div class="card-body d-flex align-items-center justify-content-center" style="min-height: 75vh;">' +
                        '<div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">' +
                        '<span class="sr-only">Loading...</span>' +
                        '</div></div>'

                    );

                    let ajaxUrl = "{{ route('laporan-absensi.index') }}?bulan=" + bulan + "&tahun=" + tahun +
                        "&search=" + search_term;

                    $.ajax({
                        url: ajaxUrl,
                        success: function(data) {
                            $('#laporanAbsensiContainer').html(data);
                        },
                        error: function() {
                            $('#laporanAbsensiContainer').html(
                                '<div class="card-body text-center text-danger py-5">Gagal memuat data. Silakan coba lagi.</div>'
                            );
                        }
                    });
                }

                fetchData();

                $('#searchInput').on('keyup', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(fetchData, 500); // Jeda 500ms
                });

                $('#bulanFilter, #tahunFilter').on('change', function() {
                    fetchData();
                });

            });
        </script>
    @endpush
