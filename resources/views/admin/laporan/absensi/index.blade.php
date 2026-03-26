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

    {{-- Calendar Modal (must be in index, not AJAX partial) --}}
    <div class="modal fade" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="calendarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="calendarModalLabel">Detail Kehadiran: <span id="modalKaryawanNama"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="calendarContainer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .cal-wrapper { max-width:100%; font-family:'Segoe UI',system-ui,sans-serif; }
        .cal-header { display:flex; align-items:center; justify-content:space-between; background:linear-gradient(135deg,#43a047,#2e7d32); color:#fff; padding:14px 20px; border-radius:10px 10px 0 0; font-size:1.2rem; font-weight:700; letter-spacing:1px; }
        .cal-nav-btn { background:rgba(255,255,255,.2); border:none; color:#fff; width:36px; height:36px; border-radius:50%; cursor:pointer; font-size:1rem; transition:background .2s; display:flex; align-items:center; justify-content:center; }
        .cal-nav-btn:hover { background:rgba(255,255,255,.35); }
        .cal-month-title { user-select:none; }
        .cal-day-names { display:grid; grid-template-columns:repeat(7,1fr); background:#f1f8e9; border-left:1px solid #e0e0e0; border-right:1px solid #e0e0e0; }
        .cal-day-name { text-align:center; padding:10px 0; font-weight:600; font-size:.85rem; color:#2e7d32; }
        .weekend-name { color:#e53935; }
        .cal-grid { display:grid; grid-template-columns:repeat(7,1fr); border-left:1px solid #e0e0e0; border-bottom:1px solid #e0e0e0; }
        .cal-cell { min-height:72px; padding:6px; border-right:1px solid #e0e0e0; border-top:1px solid #e0e0e0; display:flex; flex-direction:column; align-items:center; transition:background .15s; position:relative; }
        .cal-empty { background:#fafafa; }
        .cal-day-num { font-weight:600; font-size:.95rem; margin-bottom:4px; color:#333; }
        .cal-badge { font-size:.65rem; padding:2px 6px; border-radius:10px; color:#fff; font-weight:600; white-space:nowrap; margin-top:auto; }
        .cal-hadir { background:#e8f5e9; } .cal-hadir .cal-day-num { color:#2e7d32; } .badge-hadir { background:#43a047; }
        .cal-sakit { background:#e3f2fd; } .cal-sakit .cal-day-num { color:#1565c0; } .badge-sakit { background:#1e88e5; }
        .cal-alpha { background:#ffebee; } .cal-alpha .cal-day-num { color:#c62828; } .badge-alpha { background:#e53935; }
        .cal-ijin { background:#fff8e1; } .cal-ijin .cal-day-num { color:#f57f17; } .badge-ijin { background:#fdd835; color:#333!important; }
        .cal-cuti { background:#f3e5f5; } .cal-cuti .cal-day-num { color:#6a1b9a; } .badge-cuti { background:#8e24aa; }
        .cal-lembur { background:#fff3e0; } .cal-lembur .cal-day-num { color:#e65100; } .badge-lembur { background:#fb8c00; }
        .cal-weekend { background:#eceff1; } .cal-weekend .cal-day-num { color:#90a4ae; }
        .cal-holiday { background:#fce4ec; } .cal-holiday .cal-day-num { color:#c62828; } .badge-holiday { background:#e53935; }
        .cal-today { box-shadow:inset 0 0 0 2px #43a047; border-radius:4px; }
        .cal-today .cal-day-num { background:#43a047; color:#fff!important; width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; }
        .cal-legend { display:flex; flex-wrap:wrap; gap:12px 20px; justify-content:center; padding:12px 16px; margin-top:12px; background:#f5f5f5; border-radius:8px; }
        .cal-legend-item { display:flex; align-items:center; gap:6px; font-size:.8rem; color:#555; }
        .legend-dot { width:14px; height:14px; border-radius:3px; display:inline-block; }
        .bg-hadir { background:#43a047; } .bg-sakit { background:#1e88e5; } .bg-alpha { background:#e53935; }
        .bg-ijin { background:#fdd835; } .bg-cuti { background:#8e24aa; } .bg-lembur { background:#fb8c00; }
        .bg-weekend { background:#b0bec5; } .bg-holiday { background:#e53935; border:2px dashed #c62828; }
        @media(max-width:576px) { .cal-cell{min-height:56px;padding:4px;} .cal-day-num{font-size:.8rem;} .cal-badge{font-size:.55rem;padding:1px 4px;} .cal-header{font-size:1rem;padding:10px 14px;} }
    </style>
    @endpush

    @push('scripts')
        <script>
            $(document).ready(function() {
                let searchTimeout;

                // ==================== TABLE AJAX ====================
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
                            initCalendar(); // Re-bind after AJAX load
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
                    searchTimeout = setTimeout(fetchData, 500);
                });

                $('#bulanFilter, #tahunFilter').on('change', function() {
                    fetchData();
                });

                // ==================== CALENDAR ====================
                let currentKaryawanId, currentBulan, currentTahun, currentKaryawanNama;

                const holidays2025 = {
                    '2025-01-01':'Tahun Baru','2025-01-27':'Isra Mi\'raj','2025-01-29':'Tahun Baru Imlek',
                    '2025-03-29':'Nyepi','2025-03-30':'Idul Fitri','2025-03-31':'Idul Fitri',
                    '2025-04-01':'Cuti Bersama','2025-04-02':'Cuti Bersama','2025-04-03':'Cuti Bersama',
                    '2025-04-18':'Jumat Agung','2025-05-01':'Hari Buruh','2025-05-12':'Waisak',
                    '2025-05-29':'Kenaikan Isa Almasih','2025-06-01':'Hari Pancasila','2025-06-06':'Idul Adha',
                    '2025-06-27':'Tahun Baru Hijriyah','2025-08-17':'HUT RI','2025-09-05':'Maulid Nabi',
                    '2025-12-25':'Natal','2025-12-26':'Cuti Bersama Natal'
                };
                const holidays2026 = {
                    '2026-01-01':'Tahun Baru','2026-01-16':'Isra Mi\'raj','2026-02-17':'Imlek',
                    '2026-03-19':'Nyepi','2026-03-20':'Idul Fitri','2026-03-21':'Idul Fitri',
                    '2026-04-03':'Jumat Agung','2026-05-01':'Hari Buruh','2026-05-02':'Waisak',
                    '2026-05-14':'Kenaikan Isa Almasih','2026-05-27':'Idul Adha','2026-06-01':'Hari Pancasila',
                    '2026-06-17':'Tahun Baru Hijriyah','2026-08-17':'HUT RI','2026-08-26':'Maulid Nabi',
                    '2026-12-25':'Natal'
                };
                const allHolidays = {...holidays2025, ...holidays2026};
                const dayShort = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
                const monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

                function getHoliday(d, m, y) {
                    return allHolidays[`${y}-${String(m).padStart(2,'0')}-${String(d).padStart(2,'0')}`] || null;
                }

                function buildCalendar(bulan, tahun, data) {
                    const dim = new Date(tahun, bulan, 0).getDate();
                    const fdow = new Date(tahun, bulan-1, 1).getDay();
                    const map = {};
                    data.forEach(i => { map[new Date(i.tanggal).getDate()] = i; });
                    const now = new Date();

                    let h = `<div class="cal-wrapper"><div class="cal-header">
                        <button type="button" class="cal-nav-btn" id="calPrev"><i class="fas fa-chevron-left"></i></button>
                        <span class="cal-month-title">${monthNames[bulan-1].toUpperCase()} ${tahun}</span>
                        <button type="button" class="cal-nav-btn" id="calNext"><i class="fas fa-chevron-right"></i></button>
                    </div><div class="cal-day-names">`;

                    dayShort.forEach((n,i) => { h += `<div class="cal-day-name${i===0||i===6?' weekend-name':''}">${n}</div>`; });
                    h += '</div><div class="cal-grid">';

                    for (let i=0; i<fdow; i++) h += '<div class="cal-cell cal-empty"></div>';

                    for (let d=1; d<=dim; d++) {
                        const dt = new Date(tahun, bulan-1, d);
                        const we = dt.getDay()===0||dt.getDay()===6;
                        const td = now.getDate()===d && now.getMonth()===bulan-1 && now.getFullYear()===tahun;
                        const past = dt<=now;
                        const att = map[d];
                        const hol = getHoliday(d, bulan, tahun);
                        let cls='cal-cell', badge='', tip='';

                        if (hol) { cls+=' cal-holiday'; badge='<span class="cal-badge badge-holiday">Libur</span>'; tip=`title="${hol}"`; }
                        else if (we) { cls+=' cal-weekend'; }
                        else if (att) {
                            const s=att.status_kehadiran, lb=att.status_lembur==='Ya';
                            if(s==='Hadir') cls+=lb?' cal-lembur':' cal-hadir';
                            else if(s==='Sakit') cls+=' cal-sakit';
                            else if(s==='Alpha') cls+=' cal-alpha';
                            else if(s==='Ijin') cls+=' cal-ijin';
                            else if(s==='Cuti') cls+=' cal-cuti';
                            badge=`<span class="cal-badge badge-${s.toLowerCase()}">${s}${lb?' <i class="fas fa-clock"></i>':''}</span>`;
                        } else if (past && !we) { cls+=' cal-alpha'; badge='<span class="cal-badge badge-alpha">Alpha</span>'; }
                        if (td) cls+=' cal-today';

                        h += `<div class="${cls}" ${tip}><div class="cal-day-num">${d}</div>${badge}</div>`;
                    }

                    h += '</div><div class="cal-legend">';
                    h += '<div class="cal-legend-item"><span class="legend-dot bg-hadir"></span> Hadir</div>';
                    h += '<div class="cal-legend-item"><span class="legend-dot bg-sakit"></span> Sakit</div>';
                    h += '<div class="cal-legend-item"><span class="legend-dot bg-alpha"></span> Alpha</div>';
                    h += '<div class="cal-legend-item"><span class="legend-dot bg-ijin"></span> Ijin</div>';
                    h += '<div class="cal-legend-item"><span class="legend-dot bg-cuti"></span> Cuti</div>';
                    h += '<div class="cal-legend-item"><span class="legend-dot bg-lembur"></span> Lembur</div>';
                    h += '<div class="cal-legend-item"><span class="legend-dot bg-weekend"></span> Weekend</div>';
                    h += '<div class="cal-legend-item"><span class="legend-dot bg-holiday"></span> Libur Nasional</div>';
                    h += '</div></div>';
                    return h;
                }

                function loadCal() {
                    const c = $('#calendarContainer');
                    c.html('<div class="text-center py-5"><div class="spinner-border text-primary" style="width:3rem;height:3rem;" role="status"></div><p class="mt-2 text-muted">Memuat data kehadiran...</p></div>');
                    const ym = `${currentTahun}-${String(currentBulan).padStart(2,'0')}`;

                    $.getJSON(`/admin/kehadiran/${currentKaryawanId}/bulan/${ym}`)
                        .done(function(data) {
                            c.html(buildCalendar(currentBulan, currentTahun, data));
                            $('#calPrev').on('click', function() { navCal(-1); });
                            $('#calNext').on('click', function() { navCal(1); });
                        })
                        .fail(function(xhr) {
                            c.html('<div class="alert alert-danger">Gagal memuat data. Status: '+xhr.status+'</div>');
                        });
                }

                function navCal(dir) {
                    currentBulan += dir;
                    if (currentBulan<1) { currentBulan=12; currentTahun--; }
                    if (currentBulan>12) { currentBulan=1; currentTahun++; }
                    $('#modalKaryawanNama').text(currentKaryawanNama+' — '+monthNames[currentBulan-1]+' '+currentTahun);
                    loadCal();
                }

                function initCalendar() {
                    $(document).off('click', '.view-calendar');
                    $(document).on('click', '.view-calendar', function(e) {
                        e.preventDefault();
                        currentKaryawanId = $(this).data('karyawan-id');
                        currentKaryawanNama = $(this).data('karyawan-nama');
                        currentBulan = parseInt($(this).data('bulan'));
                        currentTahun = parseInt($(this).data('tahun'));

                        $('#modalKaryawanNama').text(currentKaryawanNama+' — '+monthNames[currentBulan-1]+' '+currentTahun);
                        $('#calendarModal').modal('show');
                        loadCal();
                    });
                }

                initCalendar();
            });
        </script>
    @endpush
