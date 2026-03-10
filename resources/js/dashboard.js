// resources/js/dashboard.js

function switchTab(tabName) {
    const pengajuanContent = document.getElementById('content-data-pengajuan');
    const absensiContent = document.getElementById('content-absensi-kehadiran');
    const pengajuanTab = document.getElementById('tab-pengajuan');
    const absensiTab = document.getElementById('tab-absensi');

    // Fungsi helper untuk beralih konten dengan transisi fade
    function transitionToTab(contentToShow, contentToHide, activeTabBtn, inactiveTabBtn) {
        // Fade out konten saat ini
        contentToHide.classList.remove('opacity-100');
        contentToHide.classList.add('opacity-0');

        // Setelah transisi (300ms, sesuai duration-300), sembunyikan sepenuhnya dan tampilkan yang baru
        setTimeout(() => {
            contentToHide.classList.add('hidden');

            // Tampilkan konten baru (dimulai dari opacity-0, lalu di-fade-in)
            contentToShow.classList.remove('hidden');
            // Timeout kecil lagi untuk memastikan browser merender sebelum transisi opacity dimulai
            setTimeout(() => {
                contentToShow.classList.remove('opacity-0');
                contentToShow.classList.add('opacity-100');
            }, 10);

        }, 300); // Sesuaikan dengan durasi transisi Tailwind (duration-300 = 300ms)

        // Kelola styling tombol tab (ini instan)
        activeTabBtn.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
        activeTabBtn.classList.remove('text-gray-500');
        inactiveTabBtn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
        inactiveTabBtn.classList.add('text-gray-500');
    }

    if (tabName === 'data-pengajuan') {
        // Hanya transisi jika tab yang sama tidak diklik ulang
        if (pengajuanContent.classList.contains('hidden')) {
            transitionToTab(pengajuanContent, absensiContent, pengajuanTab, absensiTab);
        }
    } else if (tabName === 'absensi-kehadiran') {
        if (absensiContent.classList.contains('hidden')) {
            transitionToTab(absensiContent, pengajuanContent, absensiTab, pengajuanTab);
        }
    }
}

// Ekspos fungsi ke window
window.switchTab = switchTab;

document.addEventListener('DOMContentLoaded', (event) => {
    // Ambil elemen-elemen dashboard
    const pengajuanContent = document.getElementById('content-data-pengajuan');
    const absensiContent = document.getElementById('content-absensi-kehadiran');
    const pengajuanTab = document.getElementById('tab-pengajuan');

    // CEK: Hanya jalankan logika jika elemen dashboard MEMANG ADA di halaman tersebut
    if (pengajuanContent && absensiContent && pengajuanTab) {
        pengajuanContent.classList.remove('opacity-0', 'hidden');
        pengajuanContent.classList.add('opacity-100');
        absensiContent.classList.add('hidden');

        pengajuanTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
        pengajuanTab.classList.remove('text-gray-500');

        console.log("Dashboard tabs initialized");
    } else {
        // Jika tidak ada, script tidak akan error dan tidak akan mengganggu halaman lain
        console.log("Bukan halaman dashboard, melewati inisialisasi tab dashboard.");
    }
});
