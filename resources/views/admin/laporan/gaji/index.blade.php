@extends('layouts.admin')

@section('title', 'Laporan Gaji Karyawan')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Laporan Gaji Karyawan</h1>
    <p class="mb-4">Gunakan filter di bawah untuk menampilkan dan mencetak laporan gaji.</p>

    <!-- Filter Card -->
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
                                    {{ $bulan == $i ? 'selected' : '' }}>
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
                            value="{{ $tahun }}">
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
                        {{-- <input type="text" name="search" id="searchInput" class="form-control"
                            placeholder="Ketik untuk mencari..."> --}}
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
    </div>

    <div class="card shadow mb-4" id="laporanGajiContainer">
        @include('admin.laporan.gaji.partials.table')
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let searchTimeout;

            function fetchData() {
                let bulan = $('#bulanFilter').val();
                let tahun = $('#tahunFilter').val();
                let search_term = $('#searchInput').val();
                let printUrl = "{{ route('laporan-gaji.print') }}?bulan=" + bulan + "&tahun=" + tahun + "&search=" +
                    search_term;
                let exportUrl = "{{ route('laporan-gaji.export') }}?bulan=" + bulan + "&tahun=" + tahun +
                    "&search=" + search_term;

                $('#printLink').attr('href', printUrl);
                $('#exportLink').attr('href', exportUrl);

                $('#laporanGajiContainer').html(
                    '<div class="card-body text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>'
                );

                let ajaxUrl = "{{ route('laporan-gaji.index') }}?bulan=" + bulan + "&tahun=" + tahun + "&search=" +
                    search_term;

                $.ajax({
                    url: ajaxUrl,
                    success: function(data) {
                        $('#laporanGajiContainer').html(data);
                    },
                    error: function() {
                        $('#laporanGajiContainer').html(
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

            // 🔥 Shortcut keyboard: Ctrl+K
            $(document).on('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                    e.preventDefault();
                    const $input = $('#searchInput');

                    $input.focus();
                    $input.select();
                }
            });

        });
    </script>
@endpush
