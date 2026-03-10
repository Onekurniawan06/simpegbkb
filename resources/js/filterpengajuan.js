// resources/js/filter_scripts.js

// 1. FUNGSI FILTER
function resetFormFilters() {
    const dariTanggal = document.getElementById('dari_tanggal');
    const hinggaTanggal = document.getElementById('hingga_tanggal');
    const jenisPengajuan = document.getElementById('jenis_pengajuan');
    const statusFilter = document.getElementById('status_pengajuan_filter');

    if (dariTanggal) dariTanggal.value = '';
    if (hinggaTanggal) hinggaTanggal.value = '';
    if (jenisPengajuan) jenisPengajuan.selectedIndex = 0;
    if (statusFilter) statusFilter.selectedIndex = 0;
}

// 2. FUNGSI MODAL & DETAIL SURAT
const modal = document.getElementById('view-leave-modal');
const modalContentArea = document.getElementById('modal-content-area');
const btnDownload = document.getElementById('btn-download');
const btnSpinner = document.getElementById('loading-spinner');
const btnText = document.getElementById('btn-text');

// Ambil Base URL dari meta tag atau window.location (untuk fetch API)
const APP_URL = window.appUrl || window.location.origin;

// FUNGSI SPINNER (Diperlukan agar fetchAndOpenModal tidak error)
function showSpinner() {
    // Tampilkan pesan loading di dalam area konten saat fetch dimulai
    modalContentArea.innerHTML = `
        <div class="flex flex-col items-center justify-center h-48 text-gray-500">
            <!-- Asumsi Anda memiliki ikon spinner CSS atau SVG di sini -->
            <svg class="animate-spin -ml-1 mr-3 h-10 w-10 text-blue-500" xmlns="http://www.w3.org" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-2 text-sm font-semibold">Memuat data surat...</p>
        </div>
    `;
    // openModal() harus dipanggil SEBELUM ini berjalan jika modal startnya hidden
}

function hideSpinner() {
    // console.log('menyembunyikan spinner loading...');
    // Tambahkan logika visual hide spinner Anda di sini jika diperlukan
}

function openModal() {
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
}

function closeModal() {
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        modalContentArea.innerHTML = `
            <div class="flex flex-col items-center justify-center h-18 text-gray-500">
                <p class="text-sm font-semibold">Memuat data surat...</p>
            </div>
        `;

        // Reset tombol
        if (btnDownload) {
            btnDownload.disabled = false;
            btnSpinner.classList.add('hidden');
            btnText.innerText = 'Download PDF';
        }
    }
}

// Tambahkan parameter typeName di sini
async function fetchAndOpenModal(employeenup, typeSegment, typeName) {
    const apiUrl = `/${typeSegment}/${employeenup}/detail-surat`;

    // --- Update Judul Modal Secara Dinamis ---
    const titleElement = document.getElementById('modal-title-text');
    if (titleElement && typeName) {
        let formattedTitle = typeName.replace(/([A-Z])/g, ' $1').trim();

        // Tambahkan "Kenaikan" khusus untuk tipe PangkatGajiTunjangan
        if (typeName === 'PangkatGajiTunjangan') {
            formattedTitle = 'Kenaikan ' + formattedTitle;
        }
        titleElement.innerText = ' - ' + formattedTitle;
    }
    openModal();
    showSpinner();
    try {
        const response = await fetch(apiUrl);
        if (!response.ok) {
        }

        const htmlContent = await response.text();

        // Menggunakan variabel Anda yang benar: modalContentArea
        if (modalContentArea) {
            // Konten surat akan menimpa pesan loading
            modalContentArea.innerHTML = htmlContent;
        }

        // --- Logika Tambahan untuk Download PDF ---
        if (typeof prepareDownloadUrl === 'function') {
            let type = typeSegment.split('-').pop();
            type = type.charAt(0).toUpperCase() + type.slice(1);
            prepareDownloadUrl(type, employeenup);
        }

    } catch (error) {
        console.error(error);
        if (modalContentArea) {
        }

    } finally {
        hideSpinner();
    }
}

function prepareDownloadUrl(type, employeenup) {
    let downloadUrl;
    let fileName;

    switch (type) {
        case 'Cuti':
            downloadUrl = `/download-surat-cuti/${employeenup}`;
            fileName = `Surat_Pengajuan_Cuti.pdf`;
            break;
        case 'Pensiun':
            downloadUrl = `/download-surat-pensiun/${employeenup}`;
            fileName = `Surat_Pengajuan_Pensiun.pdf`;
            break;

        // Tambahkan case 'Pangkat' untuk mencegah error "Jenis pengajuan tidak dikenal: Pangkat"
        case 'Pangkat':
        case 'PangkatGajiTunjangan':
            // PERBAIKI URL INI agar sesuai dengan rute Laravel Anda
            downloadUrl = `/download-surat-pangkat/${employeenup}`;
            // Nama file di sini akan digantikan oleh nama file yang dikirim dari controller PHP
            fileName = `Surat_Pengajuan_Kenaikan_${type}.pdf`;
            break;

        case 'Lembur':
            downloadUrl = `/download-surat-lembur/${employeenup}`;
            fileName = `Surat_Pengajuan_Lembur.pdf`;
            break;

        default:
            console.error('Jenis pengajuan tidak dikenal: ' + type);
            downloadUrl = null;
            break;
    }

    if (btnDownload) {
        btnDownload.setAttribute('data-download-url', downloadUrl);
        btnDownload.setAttribute('data-file-name', fileName);
    }
}

// FUNGSI INI YANG DIREVISI:
async function downloadPDF() {
    if (!btnDownload) return;

    // Ambil URL dan NAMA FILE dari atribut data-* yang sudah disiapkan
    // sebelumnya oleh fungsi prepareDownloadUrl()
    const url = btnDownload.getAttribute('data-download-url');
    // Jika nama file tidak ditemukan, gunakan default "download.pdf"
    const fileName = btnDownload.getAttribute('data-file-name') || "download.pdf";

    if (!url || url === 'null' || url === 'undefined') {
        alert('URL download belum diatur atau tidak valid untuk jenis pengajuan ini.');
        return;
    }

    // Tampilkan status loading
    btnDownload.disabled = true;
    btnSpinner.classList.remove('hidden'); // Asumsi class 'hidden' mengontrol tampilan spinner
    btnText.innerText = 'Mengunduh...';

    try {
        // Gunakan URL yang sudah diambil dari atribut
        const response = await fetch(url);

        if (!response.ok) {
            // Ini akan menangkap error 404, 500, dll.
            throw new Error('Gagal mengunduh file. Status: ' + response.status);
        }

        const blob = await response.blob();
        const downloadUrl = window.URL.createObjectURL(blob);

        // Buat tautan download sementara
        const a = document.createElement('a');
        a.href = downloadUrl;
        a.download = fileName; // Menggunakan nama file yang dinamis di sini
        document.body.appendChild(a);
        a.click();

        // Bersihkan objek URL setelah selesai
        window.URL.revokeObjectURL(downloadUrl);
        a.remove();

    } catch (error) {
        alert('Terjadi kesalahan saat mengunduh PDF: ' + error.message);
    } finally {
        // Kembalikan tombol ke kondisi semula
        btnDownload.disabled = false;
        btnSpinner.classList.add('hidden');
        btnText.innerText = 'Download PDF';
    }
}

// Event listener untuk tutup modal klik luar
window.onclick = function(event) {
    if (event.target === modal) {
        closeModal();
    }
}

// EKSPOR KE GLOBAL WINDOW
window.resetFormFilters = resetFormFilters;
window.openModal = openModal;
window.closeModal = closeModal;
window.fetchAndOpenModal = fetchAndOpenModal;
window.prepareDownloadUrl = prepareDownloadUrl;
window.downloadPDF = downloadPDF;
