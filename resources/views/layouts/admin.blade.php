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
            min-width: 16rem;
            min-height: 100vh;
            background-color: #4e73df;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                        min-width 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                        transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1040;
            position: relative;
        }

        /* === SIDEBAR MENU ITEMS - ALIGNMENT FIX === */
        .sidebar .nav-item .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.25rem;
            font-size: 0.88rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.85);
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .sidebar .nav-item .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-item.active > .nav-link {
            color: #fff;
            font-weight: 600;
            background-color: rgba(255, 255, 255, 0.15);
            border-left: 4px solid #fff;
            padding-left: calc(1.25rem - 4px);
        }

        .sidebar .nav-link i {
            width: 1.5rem;
            text-align: center;
            margin-right: 0.65rem;
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        .sidebar .nav-link span {
            line-height: 1.4;
        }

        /* === SIDEBAR HEADING (Section Labels) === */
        .sidebar .sidebar-heading {
            padding: 0.5rem 1.25rem;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(255, 255, 255, 0.45);
            text-align: left;
        }

        /* === SIDEBAR BRAND (Logo) === */
        .sidebar .sidebar-brand {
            padding: 1rem;
            min-height: auto;
        }

        .sidebar .sidebar-brand img {
            height: 80px;
            width: auto;
            max-width: 160px;
        }

        /* === SUBMENU ITEMS === */
        .sidebar .collapse-inner {
            padding: 0.5rem 0;
            margin: 0 0.75rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .sidebar .collapse-item {
            padding: 0.5rem 1rem;
            font-size: 0.82rem;
            font-weight: 500;
            display: block;
            color: #3a3b45;
            transition: all 0.15s ease;
        }

        .sidebar .collapse-item:hover {
            background-color: #eaecf4;
            color: #4e73df;
            text-decoration: none;
        }

        .sidebar .collapse-item.active {
            color: #4e73df;
            font-weight: 700;
            background-color: #e8ecfa;
        }

        /* === SIDEBAR DIVIDER === */
        .sidebar hr.sidebar-divider {
            margin: 0.5rem 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
        }

        /* 3. CONTENT WRAPPER */
        #content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            width: 100%;
            min-width: 0;
            overflow-x: hidden;
        }

        /* --- LOGIKA DESKTOP (Screen > 768px) --- */
        @media (min-width: 768px) {
            .sidebar {
                position: relative;
                margin-left: 0;
            }

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
                top: 0;
                left: 0;
                height: 100vh;
                width: 16rem;
                min-width: 16rem;
                transform: translateX(-100%);
                z-index: 2000;
                box-shadow: 4px 0 24px rgba(0, 0, 0, 0.2);
            }

            body.sidebar-open .sidebar {
                transform: translateX(0);
            }

            body.sidebar-open::after {
                content: "";
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1900;
                backdrop-filter: blur(2px);
                animation: fadeIn 0.3s;
            }

            /* Mobile logo slightly smaller */
            .sidebar .sidebar-brand img {
                height: 60px;
                max-width: 140px;
            }
        }

        /* Animasi Fade In untuk Backdrop */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* 4. SCROLLBAR CANTIK */
        .sidebar-menu-wrapper {
            flex: 1;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
            padding-bottom: 1rem;
        }

        .sidebar-menu-wrapper::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-menu-wrapper::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.25);
            border-radius: 4px;
        }

        /* Style untuk tombol toggle */
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
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script> --}}

    @stack('scripts')


    <script src="{{ asset('js/sidebar.js') }}"></script>

</body>

</html>
