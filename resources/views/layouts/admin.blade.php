<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Sistem Penggajian</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('Favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('Favicon.ico') }}" type="image/x-icon">

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
        /* 1. RESET & CORE LAYOUT */
        html,
        body {
            height: 100%;
            overflow-x: hidden;
        }

        #wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* 2. SIDEBAR BASE (Desktop Default: Muncul) */
        .sidebar {
            width: 16rem;
            min-width: 16rem; /* Mencegah penyusutan tak wajar */
            /* 250px */
            min-height: 100vh;
            background-color: #4e73df;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), min-width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            /* Animasi lebih natural */
            z-index: 1040;
            /* Pastikan di atas konten standard */
            position: relative;
        }

        /* 3. CONTENT WRAPPER */
        #content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            width: 100%;
            /* Fix flexbox overflow issue */
            min-width: 0;
            overflow-x: hidden;
        }

        /* --- LOGIKA DESKTOP (Screen > 768px) --- */
        @media (min-width: 768px) {
            .sidebar {
                position: relative;
                /* Sidebar ikut aliran dokumen */
                margin-left: 0;
                /* Default Muncul */
            }

            /* Saat tombol ditekan di desktop (Sidebar disembunyikan area width jadi 0) */
            body.sidebar-toggled .sidebar {
                width: 0 !important;
                min-width: 0 !important;
                overflow: hidden;
            }
        }

        /* --- LOGIKA MOBILE (Screen < 768px) --- */
        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                /* Melayang */
                top: 0;
                left: 0;
                height: 100vh;
                transform: translateX(-100%);
                /* Sembunyi di kiri dengan transform yang smooth */
                z-index: 2000;
                /* WAJIB Lebih tinggi dari Navbar */
                box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
            }

            /* Saat tombol ditekan di mobile (Sidebar dimunculkan) */
            body.sidebar-open .sidebar {
                transform: translateX(0);
                /* Geser masuk ke layar */
            }

            /* Backdrop Gelap saat menu mobile aktif */
            body.sidebar-open::after {
                content: "";
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1900;
                /* Di bawah sidebar, di atas konten */
                backdrop-filter: blur(2px);
                /* Efek blur modern */
                animation: fadeIn 0.3s;
            }
        }

        /* Animasi Fade In untuk Backdrop */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* 4. SCROLLBAR CANTIK */
        .sidebar-menu-wrapper {
            height: calc(100vh - 4.375rem);
            overflow-y: auto;
            scrollbar-width: none;
            /* Firefox */
        }

        /* ::-webkit-scrollbar  */
        .sidebar-menu-wrapper {
            /* display: none; */
            /* Chrome */
            flex: 1;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.2) transparent
        }

        /* Style untuk tombol toggle baru */
        .sidebar-toggler {
            color: rgba(255, 255, 255, 0.5);
            padding-right: 0.75rem;
        }

        .sidebar-toggler:hover,
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
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script> --}}

    @stack('scripts')


    <script src="{{ asset('js/sidebar.js') }}"></script>

</body>

</html>
