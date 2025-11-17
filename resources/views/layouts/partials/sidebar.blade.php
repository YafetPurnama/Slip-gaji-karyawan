<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center"
    href="{{ Auth::user()->role == 'admin' ? route('admin.dashboard') : route('pegawai.dashboard') }}">
    <div class="sidebar-brand-icon rotate-n-15">
      <i class="fas fa-wallet"></i>
    </div>
    <div class="sidebar-brand-text mx-3">PENGGAJIAN</div>
  </a>

  <!-- Divider -->
  <hr class="sidebar-divider my-0">

  <!-- Wrapper untuk Menu yang Bisa di-Scroll -->
  <div class="sidebar-menu-wrapper">
    {{-- ====================================================== --}}
    {{-- MENU UNTUK ADMIN --}}
    {{-- ====================================================== --}}
    @if(Auth::user()->role == 'admin')
      <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
      </li>
      {{-- ... (Semua menu admin lainnya) ... --}}
      <hr class="sidebar-divider">
      <div class="sidebar-heading">Master Data</div>
      <li class="nav-item {{ request()->routeIs('karyawan.*') ? 'active' : '' }}"><a class="nav-link"
          href="{{ route('karyawan.index') }}"><i class="fas fa-fw fa-users"></i><span>Data Karyawan</span></a></li>
      <li class="nav-item {{ request()->routeIs('jabatan.*') ? 'active' : '' }}"><a class="nav-link"
          href="{{ route('jabatan.index') }}"><i class="fas fa-fw fa-briefcase"></i><span>Data Jabatan</span></a></li>
      <hr class="sidebar-divider">
      <div class="sidebar-heading">Transaksi</div>
      <li class="nav-item {{ request()->routeIs(['absensi.*', 'potongan-gaji.*', 'data-gaji.*']) ? 'active' : '' }}"><a
          class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRekap" aria-expanded="true"
          aria-controls="collapseRekap"><i class="fas fa-fw fa-money-check-alt"></i><span>Rekap Data</span></a>
        <div id="collapseRekap"
          class="collapse {{ request()->routeIs(['absensi.*', 'potongan-gaji.*', 'data-gaji.*']) ? 'show' : '' }}"
          aria-labelledby="headingRekap" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded"><a
              class="collapse-item {{ request()->routeIs('absensi.*') ? 'active' : '' }}"
              href="{{ route('absensi.index') }}">Rekap Absensi</a><a
              class="collapse-item {{ request()->routeIs('potongan-gaji.*') ? 'active' : '' }}"
              href="{{ route('potongan-gaji.index') }}">Potongan Gaji</a><a
              class="collapse-item {{ request()->routeIs('data-gaji.*') ? 'active' : '' }}"
              href="{{ route('data-gaji.index') }}">Data Gaji</a></div>
        </div>
      </li>
      <li
        class="nav-item {{ request()->routeIs(['laporan-gaji.*', 'laporan-absensi.*', 'slip-gaji.*']) ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaporan" aria-expanded="true"
          aria-controls="collapseLaporan"><i class="far fa-fw fa-copy"></i><span>Laporan</span></a>
        <div id="collapseLaporan"
          class="collapse {{ request()->routeIs(['laporan-gaji.*', 'laporan-absensi.*', 'slip-gaji.*']) ? 'show' : '' }}"
          aria-labelledby="headingLaporan" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded"><a
              class="collapse-item {{ request()->routeIs('laporan-gaji.*') ? 'active' : '' }}"
              href="{{ route('laporan-gaji.index') }}">Laporan Gaji</a><a
              class="collapse-item {{ request()->routeIs('laporan-absensi.*') ? 'active' : '' }}"
              href="{{ route('laporan-absensi.index') }}">Laporan Absensi</a><a
              class="collapse-item {{ request()->routeIs('slip-gaji.*') ? 'active' : '' }}"
              href="{{ route('slip-gaji.index') }}">Slip Gaji</a></div>
        </div>
      </li>
    @elseif(Auth::user()->role == 'pegawai')
      <li class="nav-item {{ request()->routeIs('pegawai.dashboard') ? 'active' : '' }}"><a class="nav-link"
          href="{{ route('pegawai.dashboard') }}"><i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span></a>
      </li>
      <hr class="sidebar-divider">
      <div class="sidebar-heading">Data Saya</div>
      <li class="nav-item"><a class="nav-link" href="{{ route('pegawai.gaji.index') }}"><i
            class="fas fa-fw fa-wallet"></i><span>Data Gaji</span></a></li>
      <li class="nav-item {{ request()->routeIs('pegawai.password.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('pegawai.password.index') }}">
          <i class="fas fa-fw fa-lock"></i>
          <span>Ganti Password</span>
        </a>
      </li>
    @endif
  </div>
  <!-- End of Wrapper Menu -->

  <!-- Divider -->
  <hr class="sidebar-divider d-none d-md-block">

  <!-- Sidebar Toggler (Sidebar) ASLI TAPI DISEMBUNYIKAN -->
  <div class="text-center d-none d-md-inline" style="display: none !important;">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
  </div>

</ul>
<!-- End of Sidebar -->