<div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">
        Laporan Absensi Bulan: {{ \Carbon\Carbon::create()->month((int) $bulan)->translatedFormat('F') }}
        {{ $tahun }}
    </h6>
</div>
<div class="card-body">
    <div class="table-responsive">
        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Karyawan</th>
                    <th>Jabatan</th>
                    <th class="text-center">Hadir</th>
                    <th class="text-center">Sakit</th>
                    <th class="text-center">Alpha</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($laporanAbsensi as $absensi)
                    <tr class="attendance-row" data-karyawan-id="{{ $absensi->id }}"
                        data-karyawan-nama="{{ $absensi->nama_lengkap }}" data-bulan="{{ $bulan }}"
                        data-tahun="{{ $tahun }}">
                        {{-- class="attendance-row" data-karyawan-id="{{ $absensi->id }}"
                        data-karyawan-nama="{{ $absensi->nama_lengkap }}" data-bulan="{{ $bulan }}"
                        data-tahun="{{ $tahun }}" --}}
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $absensi->nama_lengkap }}</td>
                        <td>{{ $absensi->jabatan->nama_jabatan ?? 'N/A' }}</td>
                        <td class="text-center">{{ $absensi->jumlah_hadir }}</td>
                        <td class="text-center">{{ $absensi->jumlah_sakit }}</td>
                        <td class="text-center">{{ $absensi->jumlah_alpha }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info view-calendar" 
                                    data-karyawan-id="{{ $absensi->id }}"
                                    data-karyawan-nama="{{ $absensi->nama_lengkap }}"
                                    data-bulan="{{ $bulan }}"
                                    data-tahun="{{ $tahun }}">
                                <i class="fas fa-calendar-alt"></i> Lihat
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            @if (request('search'))
                                Karyawan dengan nama "{{ request('search') }}" tidak ditemukan.
                            @else
                                Tidak ada data absensi untuk ditampilkan pada periode ini.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Calendar Modal -->
<div class="modal fade" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="calendarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="calendarModalLabel">Detail Kehadiran Karyawan: <span id="modalKaryawanNama"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="calendar"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
    .fc-event {
        cursor: pointer;
        font-size: 0.85em;
        padding: 2px 4px;
    }
    .fc-daygrid-day-number {
        font-size: 1.1em;
    }
    .fc-day-sun, .fc-day-sat {
        background-color: #f8f9fa;
    }
    .fc-toolbar-title {
        font-size: 1.5em;
    }
</style>
@endpush

@push('scripts')
<!-- Make sure jQuery is loaded before Bootstrap -->
@if(!isset($scripts_loaded) || !$scripts_loaded)
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    @php $scripts_loaded = true @endphp
@endif
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js"></script>
<script>
    // Function to initialize calendar
    function initCalendar() {
        console.log('Initializing calendar...');
        
        // Initialize FullCalendar only once
        let calendarEl = document.getElementById('calendar');
        if (!calendarEl) {
            console.error('Calendar element not found!');
            return null;
        }

        // Create a new calendar instance
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,dayGridWeek,dayGridDay'
            },
            locale: 'id',
            buttonText: {
                today: 'Hari Ini',
                month: 'Bulan',
                week: 'Minggu',
                day: 'Hari'
            },
            dayMaxEvents: true,
            events: []
        });
        
        // Render the calendar
        calendar.render();
        
        console.log('Calendar initialized successfully');
        return calendar;
    }
    
    // Function to handle view button click
    function handleViewButtonClick(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('View button clicked');
        
        const button = e.currentTarget;
        const karyawanId = button.getAttribute('data-karyawan-id');
        const karyawanNama = button.getAttribute('data-karyawan-nama');
        const bulan = button.getAttribute('data-bulan');
        const tahun = button.getAttribute('data-tahun');
        
        console.log('View calendar for:', { karyawanId, karyawanNama, bulan, tahun });
        
        // Update modal title
        const modalTitle = document.getElementById('modalKaryawanNama');
        if (modalTitle) {
            modalTitle.textContent = `${karyawanNama} - ${new Date(tahun, bulan - 1).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' })}`;
        }
        
        // Show the modal first
        const modalElement = document.getElementById('calendarModal');
        if (!modalElement) {
            console.error('Modal element not found!');
            return;
        }
        
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        
        // Initialize or get calendar instance
        if (!window.attendanceCalendar) {
            console.log('Creating new calendar instance...');
            window.attendanceCalendar = initCalendar();
        }
        
        const calendar = window.attendanceCalendar;
        if (!calendar) {
            console.error('Failed to initialize calendar');
            return;
        }
        
        // Show loading state
        calendar.removeAllEvents();
        calendar.addEvent({
            title: 'Memuat data...',
            start: new Date(),
            allDay: true,
            backgroundColor: '#f8f9fa',
            textColor: '#6c757d',
            borderColor: '#dee2e6'
        });
        
        // Fetch attendance data
        console.log('Fetching attendance data...');
        fetch(`/admin/laporan-absensi/data-kehadiran/${karyawanId}/${tahun}-${bulan.padStart(2, '0')}`)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data);
                
                const events = data.map(item => ({
                    title: item.status_kehadiran + (item.status_lembur === 'Ya' ? ' (Lembur)' : ''),
                    start: item.tanggal,
                    allDay: true,
                    backgroundColor: getStatusColor(item.status_kehadiran, item.status_lembur),
                    textColor: '#fff',
                    borderColor: 'rgba(0,0,0,0.1)'
                }));
                
                calendar.removeAllEvents();
                if (events.length > 0) {
                    calendar.addEventSource(events);
                } else {
                    calendar.addEvent({
                        title: 'Tidak ada data kehadiran',
                        start: new Date(tahun, bulan - 1, 1),
                        allDay: true,
                        backgroundColor: '#f8f9fa',
                        textColor: '#6c757d',
                        borderColor: '#dee2e6'
                    });
                }
                calendar.gotoDate(`${tahun}-${bulan.padStart(2, '0')}-01`);
            })
            .catch(error => {
                console.error('Error fetching attendance data:', error);
                calendar.removeAllEvents();
                calendar.addEvent({
                    title: 'Gagal memuat data: ' + error.message,
                    start: new Date(),
                    allDay: true,
                    backgroundColor: '#dc3545',
                    textColor: '#fff'
                });
            });
    }
    
    // Function to get status color
    function getStatusColor(status, lembur = 'Tidak') {
        if (lembur === 'Ya') return '#fd7e14'; // Orange for overtime
        
        switch(status) {
            case 'Hadir': return '#28a745'; // Green
            case 'Sakit': return '#17a2b8'; // Blue
            case 'Alpha': return '#dc3545'; // Red
            default: return '#6c757d'; // Gray
        }
    }
    
    // Initialize when DOM is fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM fully loaded, initializing...');
        
        // Initialize calendar
        window.attendanceCalendar = initCalendar();
        
        // Add click event listeners to all view buttons
        const viewButtons = document.querySelectorAll('.view-calendar');
        console.log(`Found ${viewButtons.length} view buttons`);
        
        viewButtons.forEach(button => {
            button.addEventListener('click', handleViewButtonClick);
        });
        
        console.log('Initialization complete');
    });
</script>
@endpush
