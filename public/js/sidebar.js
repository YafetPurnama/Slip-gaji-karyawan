document.addEventListener('DOMContentLoaded', function () {
    const body = document.body;
    const sidebar = document.querySelector('.sidebar');
    const toggleBtns = document.querySelectorAll('#sidebarToggle, #sidebarToggleTop, #customSidebarToggle');

    // 1. CEK MEMORI LOCAL STORAGE (Fitur Senior)
    const isSidebarToggled = localStorage.getItem('sb|sidebar-toggle') === 'true';
    if (isSidebarToggled) {
        body.classList.add('sidebar-toggled');
    }

    // Fungsi utama toggle
    function toggleSidebar() {
        body.classList.toggle('sidebar-toggled');

        // Simpan status terbaru ke Local Storage browser
        // Agar saat refresh/pindah halaman, posisi sidebar tidak reset
        if (body.classList.contains('sidebar-toggled')) {
            localStorage.setItem('sb|sidebar-toggle', 'true');
        } else {
            localStorage.removeItem('sb|sidebar-toggle');
        }
    }

    // 2. EVENT LISTENER TOMBOL
    toggleBtns.forEach(btn => {
        if (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation(); // Cegah event bubbling
                toggleSidebar();
            });
        }
    });

    // 3. LOGIKA MOBILE (Tutup sidebar jika klik konten luar)
    document.addEventListener('click', function (e) {
        const isMobile = window.innerWidth < 768;
        const hasToggleClass = body.classList.contains('sidebar-toggled');
        const clickedInsideSidebar = sidebar && sidebar.contains(e.target);
        const clickedOnButton = [...toggleBtns].some(btn => btn && btn.contains(e.target));

        // Logika Mobile: Jika sidebar terbuka, dan klik di luar -> TUTUP
        if (isMobile && hasToggleClass && !clickedInsideSidebar && !clickedOnButton) {
            body.classList.remove('sidebar-toggled');
            // Tidak perlu simpan state di mobile karena sifatnya sementara
        }
    });

    // 4. PREVENT SCROLL ISSUE ON RESIZE (Dengan Debounce sederhana)
    let resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            const w = window.innerWidth;
            if (w >= 768) {
                // Optional: Paksa reset atau biarkan user decide.
                // Disini kita hapus class hanya jika user TIDAK menyimpannya di localstorage
                if (localStorage.getItem('sb|sidebar-toggle') !== 'true') {
                    body.classList.remove('sidebar-toggled');
                }
            }
        }, 250);
    });
});
