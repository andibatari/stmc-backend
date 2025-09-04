document.addEventListener('DOMContentLoaded', function() {
    var toggleKaryawan = document.getElementById('toggleKaryawan');
    var submenuKaryawan = document.getElementById('submenuKaryawan');
    var sidebarToggle = document.getElementById('sidebarToggle'); // Tombol hamburger
    var sidebar = document.getElementById('sidebar'); // Sidebar itu sendiri
    var sidebarOverlay = document.getElementById('sidebarOverlay'); // Overlay
    var mainContentArea = document.getElementById('mainContentArea'); // Konten utama

    // Mengatur status awal submenu
    if (toggleKaryawan.classList.contains('active')) {
        submenuKaryawan.style.display = "block";
    }

    // Event listener untuk toggle submenu Karyawan
    toggleKaryawan.addEventListener('click', function() {
        if (submenuKaryawan.style.display === "block") {
            submenuKaryawan.style.display = "none";
        } else {
            submenuKaryawan.style.display = "block";
        }
    });

    // Event listener untuk toggle sidebar (hamburger menu)
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('sidebar-open'); // Toggle kelas untuk sembunyi/tampil
        sidebarOverlay.classList.toggle('show'); // Toggle overlay
        
        // Atur margin kiri main content berdasarkan status sidebar
        // Ini akan diatur oleh CSS media query untuk desktop, jadi hanya perlu untuk mobile
        if (window.innerWidth < 1024) { // Hanya berlaku untuk layar mobile/tablet
            if (sidebar.classList.contains('sidebar-open')) {
                mainContentArea.style.marginLeft = '16rem'; // Tampil, margin 16rem
            } else {
                mainContentArea.style.marginLeft = '0'; // Sembunyi, margin 0
            }
        }
    });

    // Sembunyikan sidebar dan overlay jika overlay diklik (hanya di mobile)
    sidebarOverlay.addEventListener('click', function() {
        sidebar.classList.remove('sidebar-open');
        sidebarOverlay.classList.remove('show');
        if (window.innerWidth < 1024) {
            mainContentArea.style.marginLeft = '0'; // Pastikan margin kembali 0 di mobile
        }
    });

    // Sesuaikan margin main content saat ukuran layar berubah (khususnya dari mobile ke desktop)
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) { // Jika layar desktop (lg breakpoint)
            sidebar.classList.remove('sidebar-open'); // Pastikan sidebar terlihat (CSS handles transform)
            sidebarOverlay.classList.remove('show'); // Pastikan overlay tersembunyi
            // mainContentArea.style.marginLeft akan diatur oleh CSS media query
        } else {
            // Di mobile, jika sidebar tidak terbuka, pastikan margin 0
            if (!sidebar.classList.contains('sidebar-open')) {
                mainContentArea.style.marginLeft = '0';
            }
        }
    });
});