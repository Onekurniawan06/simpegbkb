// resources/js/sidebar-toggle.js

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebar-toggle-btn');
    const sidebarTexts = document.querySelectorAll('.sidebar-text');
    const bankName = document.getElementById('bank-name');
    const logoSection = document.getElementById('logo-section');

    const toggleSidebar = () => {
        // Toggle lebar sidebar dari w-72 (expanded) menjadi w-20 (collapsed)
        sidebar.classList.toggle('w-72');
        sidebar.classList.toggle('w-20');

        // Sembunyikan/tampilkan teks menu dan nama bank
        sidebarTexts.forEach(span => {
            span.classList.toggle('hidden');
        });
        bankName.classList.toggle('hidden');

        // Sesuaikan posisi logo saat mini
        logoSection.classList.toggle('justify-center');

        // Putar ikon panah toggle
        toggleBtn.querySelector('svg').classList.toggle('rotate-180');
    };

    if (toggleBtn) {
        toggleBtn.addEventListener('click', toggleSidebar);
    }
});
