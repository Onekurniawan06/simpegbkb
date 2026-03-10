// resources/js/flyout-edge-detection.js

document.addEventListener('DOMContentLoaded', () => {

    // --- FUNGSI EDGE DETECTION ---
    const adjustFlyoutPosition = (submenuId, containerId) => {
        const submenu = document.getElementById(submenuId);
        const container = document.getElementById(containerId);

        if (!submenu || !container) return;

        // Logika JS untuk hover dan edge detection
        container.addEventListener('mouseenter', () => {
            submenu.classList.remove('invisible', 'opacity-0');
            submenu.classList.add('visible', 'opacity-100');

            submenu.style.top = '0px';
            const submenuRect = submenu.getBoundingClientRect();
            const viewportHeight = window.innerHeight;

            if (submenuRect.bottom > viewportHeight) {
                const overflowAmount = submenuRect.bottom - viewportHeight;
                submenu.style.top = `-${overflowAmount + 10}px`;
            }
        });

        container.addEventListener('mouseleave', () => {
             submenu.classList.remove('visible', 'opacity-100');
             submenu.classList.add('invisible', 'opacity-0');
             submenu.style.top = '0px';
        });
    };

    // Logika Accordion (Submenu Collapse Klik) TELAH DIHAPUS TOTAL dari sini

    // --- INISIALISASI SEMUA FUNGSI FLYOUT ---

    // Aktifkan Edge Detection untuk semua submenu flyout (samping)
    adjustFlyoutPosition('laporan-submenu', 'laporan-menu-container');
    // Catatan: container-info-a/b dan flyout-info-a/b di bawah ini mungkin sudah tidak diperlukan jika menunya flat sekarang
    adjustFlyoutPosition('flyout-info-1', 'container-info-1');
    adjustFlyoutPosition('flyout-info-2', 'container-info-2');
    adjustFlyoutPosition('flyout-info-a', 'container-info-a');
    adjustFlyoutPosition('flyout-info-b', 'container-info-b');

    // Inisialisasi untuk menu Manajemen Pengajuan, Pegawai, dan Informasi !!!
    adjustFlyoutPosition('pengajuan-submenu', 'pengajuan-menu-container');
    adjustFlyoutPosition('pegawai-submenu', 'pegawai-menu-container');
    adjustFlyoutPosition('informasi-submenu', 'informasi-menu-container');
});
