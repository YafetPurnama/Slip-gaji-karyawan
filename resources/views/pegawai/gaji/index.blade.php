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
                                <label for="tahunFilter">Tahun</label>
                                <select name="tahun" id="tahunFilter" class="form-control">
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="bulanFilter">Bulan</label>
                                <select name="bulan" id="bulanFilter" class="form-control">
                                    @for ($i = $minMonth; $i <= $maxMonth; $i++)
                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                                            {{ $bulan == $i ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
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
        $(document).ready(function() {
            // Inisialisasi variabel
            const startDate = new Date('{{ $startDate }}');
            const startYear = startDate ? startDate.getFullYear() : {{ $startYear }};
            const currentDate = new Date();
            const currentYear = currentDate.getFullYear();
            const startMonth = startDate ? startDate.getMonth() + 1 : 1;

            // Fungsi untuk update bulan berdasarkan tahun yang dipilih
            function updateMonthDropdown(selectedYear) {
                let startMonth = 1;
                let endMonth = 12;
                const selectedYearInt = parseInt(selectedYear);

                // Jika tahun yang dipilih sama dengan tahun masuk
                if (selectedYearInt === startYear) {
                    startMonth = startDate ? startDate.getMonth() + 1 : 1; // getMonth() dimulai dari 0
                }

                // Jika tahun yang dipilih adalah tahun sekarang
                if (selectedYearInt === currentYear) {
                    endMonth = currentDate.getMonth() + 1; // Hingga bulan sekarang
                }

                // Update dropdown bulan
                let monthSelect = $('#bulanFilter');
                monthSelect.empty();

                for (let i = startMonth; i <= endMonth; i++) {
                    let monthName = new Date(2000, i - 1, 1).toLocaleString('id-ID', {
                        month: 'long'
                    });
                    let monthValue = i.toString().padStart(2, '0');
                    let selected = (i === {{ $bulan }}) ? 'selected' : '';
                    monthSelect.append(new Option(monthName, monthValue, false, selected));
                }
            }

            // Inisialisasi bulan saat halaman dimuat
            updateMonthDropdown($('#tahunFilter').val());

            // Fungsi utama untuk memuat data via AJAX
            function fetchData() {
                let bulan = $('#bulanFilter').val();
                let tahun = $('#tahunFilter').val();

                // Tampilkan indikator loading
                $('#slipGajiContainer').html(
                    '<div class="card-body text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>'
                );

                // Pastikan bulan dan tahun memiliki nilai yang valid
                if (!bulan || !tahun) {
                    bulan = '{{ $bulan }}';
                    tahun = '{{ $tahun }}';
                }

                // Gunakan URL dengan parameter query
                let ajaxUrl = "{{ route('pegawai.gaji.index') }}";
                
                // Gunakan method GET dengan data parameter
                $.ajax({
                    url: ajaxUrl,
                    method: 'GET',
                    data: {
                        bulan: bulan,
                        tahun: tahun,
                        ajax: 1
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(data) {
                        $('#slipGajiContainer').html(data);
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        $('#slipGajiContainer').html(
                            '<div class="card-body text-center text-danger py-5">Gagal memuat data. Silakan refresh halaman dan coba lagi.</div>'
                        );
                    }
                });
            }

            // Event saat pengguna mengubah tahun
            $('#tahunFilter').on('change', function() {
                updateMonthDropdown($(this).val());
                fetchData();
            });

            // Event saat pengguna mengubah bulan
            $('#bulanFilter').on('change', function() {
                fetchData();
            });
        });
    </script>
@endpush
