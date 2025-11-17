<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Admin Dashboard') - Sistem Penggajian</title>

  <!-- Fonts and Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link
    href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
    rel="stylesheet">

  <!-- SB Admin 2 CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css"
    rel="stylesheet">

  {{-- Kumpulan Style Kustom --}}
  <style>
    /* Membuat layout fixed/sticky */
    #content-wrapper {
      height: 100vh;
      overflow-y: auto;
    }

    .topbar {
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    footer.sticky-footer {
      position: sticky;
      bottom: 0;
      z-index: 1000;
      padding: 1.25rem 0;
    }

    /* Membuat area menu sidebar bisa di-scroll */
    .sidebar-menu-wrapper {
      height: calc(100vh - 120px);
      overflow-y: auto;
      overflow-x: hidden;
    }

    .sidebar-menu-wrapper::-webkit-scrollbar {
      width: 6px;
    }

    .sidebar-menu-wrapper::-webkit-scrollbar-track {
      background: transparent;
    }

    .sidebar-menu-wrapper::-webkit-scrollbar-thumb {
      background-color: rgba(255, 255, 255, 0.2);
      border-radius: 10px;
    }

    /* Mencegah scroll horizontal di seluruh halaman */
    html,
    body {
      overflow-x: hidden;
    }

    /* Style untuk tombol toggle baru */
    .sidebar-toggler {
      color: rgba(255, 255, 255, 0.5);
      padding-right: 0.75rem;
    }

    .sidebar-toggler:hover,
    .sidebar-toggler:focus {
      color: #fff;
      box-shadow: none;
    }
  </style>

  @stack('styles')
</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    @include('layouts.partials.sidebar')

    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        @include('layouts.partials.navbar')
        <div class="container-fluid">
          @yield('content')
        </div>
      </div>
      @include('layouts.partials.footer')
    </div>
  </div>

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

  <!-- Core JavaScript -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script>

  @stack('scripts')

  {{-- JAVASCRIPT UNTUK TOMBOL TOGGLE BARU --}}
  <script>
    $(document).ready(function () {
      // Saat tombol baru (#customSidebarToggle) di-klik
      $('#customSidebarToggle').on('click', function (e) {
        e.preventDefault();
        // Jalankan klik pada tombol asli (#sidebarToggle) yang tersembunyi
        $('#sidebarToggle').click();
      });
    });
  </script>

</body>

</html>