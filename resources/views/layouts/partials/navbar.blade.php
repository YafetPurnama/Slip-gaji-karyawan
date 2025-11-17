<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

  <!-- Sidebar Toggle (Topbar) Kustom untuk Desktop -->
  <button id="customSidebarToggle" class="btn btn-link d-none d-md-inline-block rounded-circle mr-3">
    <i class="fa fa-bars"></i>
  </button>

  <!-- Tombol Toggle untuk Mobile (Bawaan Template) -->
  <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
    <i class="fa fa-bars"></i>
  </button>

  <!-- Topbar Navbar -->
  <ul class="navbar-nav ml-auto">

    <div class="topbar-divider d-none d-sm-block"></div>

    <!-- Nav Item - User Information -->
    <li class="nav-item dropdown no-arrow">
      {{-- Definisikan variabel foto di luar tag 'a' agar lebih rapi --}}
      @php
        $foto = Auth::user()->karyawan->foto ?? null;
      @endphp

      {{-- Tambahkan kelas d-flex dan align-items-center untuk perataan vertikal --}}
      <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button"
        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>

        @if($foto)
          {{-- Jika ada foto di database, tampilkan --}}
          <img class="img-profile rounded-circle" src="{{ asset('storage/' . $foto) }}">
        @else
          {{-- Jika tidak ada, buat gambar dari inisial nama dengan ukuran yang pas --}}
          <img class="img-profile rounded-circle"
            src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4e73df&color=fff&size=32">
        @endif
      </a>

      <!-- Dropdown - User Information -->
      <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
        <a class="dropdown-item" href="{{ route('profile.index') }}">
          <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
          Profile
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="#"
          onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
          Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          @csrf
        </form>
      </div>
    </li>
  </ul>
</nav>
<!-- End of Topbar -->