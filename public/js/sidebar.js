document.addEventListener('DOMContentLoaded', function () {
    const body = document.body;
    const sidebar = document.querySelector('.sidebar');
    
    // Gunakan ID yang spesifik, bebas dari bawaan sb-admin-2.js
    const btnDesktop = document.getElementById('customSidebarToggle');
    const btnMobile = document.getElementById('btnMobileToggle');

    // 1. CEK MEMORI LOCAL STORAGE (Hanya untuk Desktop)
    const isSidebarToggled = localStorage.getItem('sb|sidebar-toggle') === 'true';
    if (window.innerWidth >= 768 && isSidebarToggled) {
        body.classList.add('sidebar-toggled');
    }

    // 2. EVENT LISTENER DESKTOP
    if (btnDesktop) {
        btnDesktop.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation(); // Cegah event bubbling
            
            body.classList.toggle('sidebar-toggled');

            // Simpan status terbaru ke Local Storage browser
            if (body.classList.contains('sidebar-toggled')) {
                localStorage.setItem('sb|sidebar-toggle', 'true');
            } else {
                localStorage.removeItem('sb|sidebar-toggle');
            }
        });
    }

    // 3. EVENT LISTENER MOBILE
    if (btnMobile) {
        btnMobile.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Mobile menggunakan class tersendiri: sidebar-open
            body.classList.toggle('sidebar-open');
        });
    }

    // 4. TUTUP SIDEBAR SAAT KLIK AREA LUAR (Di Mobile View)
    document.addEventListener('click', function (e) {
        const isMobile = window.innerWidth < 768;
        if (!isMobile) return;
        
        const hasOpenClass = body.classList.contains('sidebar-open');
        const clickedInsideSidebar = sidebar && sidebar.contains(e.target);
        const clickedOnMobileBtn = btnMobile && btnMobile.contains(e.target);

        // Jika sidebar terbuka, dan klik di luar sidebar/tombol -> TUTUP
        if (hasOpenClass && !clickedInsideSidebar && !clickedOnMobileBtn) {
            body.classList.remove('sidebar-open');
        }
    });

    // 5. PREVENT LAYOUT ISSUE ON RESIZE (Debounce)
    let resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            const w = window.innerWidth;
            if (w >= 768) {
                // Di desktop view, hapus class mobile
                body.classList.remove('sidebar-open');
            }
        }, 250);
    });
});
