/**
 * Logika Pengelolaan Modal Tracking Cuti
 */

// Gunakan fungsi yang diekspos ke window agar bisa dipanggil oleh atribut onclick di HTML
window.openTrackingModal = function(submissionId) {
    const modal = document.getElementById('trackingModal');
    const modalBody = document.getElementById('modalBodyContent');

    if (!modal || !modalBody) return;

    // Tampilkan modal & reset konten
    modal.classList.remove('hidden');
    modalBody.innerHTML = `
        <div class="flex flex-col items-center justify-center p-10">
            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
            <p class="mt-4 text-gray-500">Memuat status pengajuan...</p>
        </div>
    `;

    // Fetch data dari endpoint Laravel
    // Catatan: Pastikan base URL sesuai jika aplikasi tidak di root (misal: /subfolder/public)
    fetch(`/api/cuti/tracking/${submissionId}`)
        .then(response => {
            if (!response.ok) throw new Error('Gagal mengambil data');
            return response.text();
        })
        .then(html => {
            modalBody.innerHTML = html;
        })
        .catch(err => {
            console.error('Error:', err);
            modalBody.innerHTML = `
                <div class="p-4 bg-red-50 text-red-700 rounded-md">
                    <p>Terjadi kesalahan saat memuat data. Silakan coba lagi.</p>
                </div>
            `;
        });
};

window.closeTrackingModal = function() {
    const modal = document.getElementById('trackingModal');
    if (modal) {
        modal.classList.add('hidden');
    }
};

// Menutup modal saat area luar modal diklik
window.onclick = function(event) {
    const modal = document.getElementById('trackingModal');
    if (event.target == modal) {
        closeTrackingModal();
    }
};

// Menutup modal dengan tombol ESC
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closeTrackingModal();
    }
});
