@extends('layouts.admin')

@section('title', 'Rekap & Input Absensi')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Rekap & Input Absensi</h1>
<p class="mb-4">Halaman ini menampilkan rekap absensi bulanan dan menyediakan form untuk input kehadiran harian.</p>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        {{-- FORM FILTER DAN PENCARIAN --}}
        <div class="row">
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <label for="bulan">Rekap Bulan</label>
                    <select name="bulan" id="bulanFilter" class="form-control">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <label for="tahun">Tahun</label>
                    <input type="number" name="tahun" id="tahunFilter" class="form-control" value="{{ $tahun }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-0">
                    <label for="search">Cari Nama Karyawan</label>
                    <input type="text" name="search" id="searchInput" class="form-control" placeholder="Ketik untuk mencari..." value="{{ request('search') }}">
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="alert alert-info">
            Menampilkan rekap absensi bulan: <strong id="periodeInfo">{{ \Carbon\Carbon::create()->month((int) $bulan)->translatedFormat('F') }} {{ $tahun }}</strong>.
            Form di bawah adalah untuk input kehadiran hari ini: <strong>{{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}</strong>
        </div>

        {{-- Container untuk tabel yang akan di-refresh oleh AJAX --}}
        <div id="absensiTableContainer">
            @include('admin.absensi.partials.table')
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let searchTimeout;

    // Fungsi utama untuk memuat data via AJAX
    function fetchData() {
        let bulan = $('#bulanFilter').val();
        let tahun = $('#tahunFilter').val();
        let search_term = $('#searchInput').val();

        // Tampilkan indikator loading
        $('#absensiTableContainer').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
        
        // Buat URL dengan semua parameter filter
        let url = "{{ route('absensi.index') }}?bulan=" + bulan + "&tahun=" + tahun + "&search=" + search_term;

        $.ajax({
            url: url,
            success: function(data) {
                $('#absensiTableContainer').html(data);
                // Update info periode
                let monthName = $('#bulanFilter option:selected').text();
                $('#periodeInfo').text(monthName.trim() + ' ' + tahun);
            },
            error: function() {
                $('#absensiTableContainer').html('<div class="text-center text-danger py-5">Gagal memuat data. Silakan coba lagi.</div>');
            }
        });
    }

    // Event saat pengguna mengetik di kolom pencarian
    $('#searchInput').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(fetchData, 500); // Jeda 500ms
    });

    // Event saat pengguna mengubah bulan atau tahun
    $('#bulanFilter, #tahunFilter').on('change', function() {
        fetchData();
    });

});
</script>
@endpush