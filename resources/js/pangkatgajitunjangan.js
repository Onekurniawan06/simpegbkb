// --- Elemen Modal Notifikasi (Sukses/Error) ---
const successModalPangkatGajiTunjangan = document.getElementById('successModalPangkatGajiTunjangan');
const errorModalPangkatGajiTunjangan = document.getElementById('errorModalPangkatGajiTunjangan');
const closeSuccessModalButton = document.getElementById('closeSuccessModalButton');
const closeErrorModalButton = document.getElementById('closeErrorModalButton');

// --- Elemen Modal Loading (BARU) ---
const loadingModalPangkatGajiTunjangan = document.getElementById('loadingModalPangkatGajiTunjangan');

// --- Elemen Modal Review Pengajuan ---
const openModalButtonPangkatGajiTunjangan = document.getElementById('openModalButtonPangkatGajiTunjangan');
const leaveModalPangkatGajiTunjangan = document.getElementById('leaveModalPangkatGajiTunjangan');
const reviewCancelButton = document.getElementById('cancelButton');
const submitButton = document.getElementById('submitButton');

// --- Elemen Target Review Modal (BARU: Referensi elemen output di modal) ---
const reviewNamaPegawai = document.getElementById('review_nama_pegawai');
const reviewNUP = document.getElementById('review_nup');
const reviewUnitKerja = document.getElementById('review_unit_kerja');
const reviewJenisPengajuan = document.getElementById('review_jenis_pengajuan');
const reviewStatusPegawai = document.getElementById('review_status_pegawai');
const reviewJabatan = document.getElementById('review_jabatan');
const reviewPangkat = document.getElementById('review_pangkat');
const reviewGrade = document.getElementById('review_grade');
const reviewTmtPegawai = document.getElementById('review_tmt_pegawai');
const reviewMasaKerja = document.getElementById('review_masa_kerja');

/**
     * LOGIK MODAL VIEWER
     */
    function openViewerModal(url) {
        if (viewerModal && viewerIframe) {
            viewerIframe.src = url;
            viewerModal.style.display = 'flex';
        } else {
            console.error("Kesalahan: Elemen HTML Modal Viewer tidak ditemukan!");
        }
    }

    function closeViewerModal() {
        if (viewerModal && viewerIframe) {
            viewerModal.style.display = 'none';
            viewerIframe.src = '';
        }
    }

    /**
     * LOGIK MODAL NOTIFIKASI
     */
    const showNotificationModal = (modalElement) => {
        if (modalElement && modalElement.classList.contains('show-on-load')) {
            modalElement.classList.remove('hidden');
            modalElement.classList.add('flex');
        }
    };

    const hideNotificationModal = (modalElement) => {
        if (modalElement) {
            modalElement.classList.add('hidden');
            modalElement.classList.remove('flex');
        }
    };

    /**
     * LOGIK MODAL LOADING (BARU)
     */
    function showLoadingModal() {
        if (loadingModalPangkatGajiTunjangan) {
            loadingModalPangkatGajiTunjangan.classList.remove('hidden');
            loadingModalPangkatGajiTunjangan.classList.add('flex');
        }
    }

    function hideLoadingModal() {
        if (loadingModalPangkatGajiTunjangan) {
            loadingModalPangkatGajiTunjangan.classList.add('hidden');
            loadingModalPangkatGajiTunjangan.classList.remove('flex');
        }
    }

    /**
     * LOGIK MODAL REVIEW PENGAJUAN (BARU)
     */
    function fillReviewModalData() {
        // Menggunakan variabel konstanta yang dideklarasikan di bagian atas file JS (CamelCase)
        if (reviewNamaPegawai && document.getElementById('nama_pegawai')) { reviewNamaPegawai.textContent = document.getElementById('nama_pegawai').value; }
        if (reviewNUP && document.getElementById('nomor_urut_pegawai')) { reviewNUP.textContent = document.getElementById('nomor_urut_pegawai').value; }
        if (reviewJenisPengajuan && document.getElementById('jenis_pengajuan')) { reviewJenisPengajuan.textContent = document.getElementById('jenis_pengajuan').value; }
        if (reviewUnitKerja && document.getElementById('unit_kerja')) {reviewUnitKerja.textContent = document.getElementById('unit_kerja').value; }
        // ... Lanjutkan untuk semua field lainnya menggunakan CamelCase ...
        if (reviewStatusPegawai && document.getElementById('status_pegawai')) { reviewStatusPegawai.textContent = document.getElementById('status_pegawai').value; }
        if (reviewJabatan && document.getElementById('jabatan')) { reviewJabatan.textContent = document.getElementById('jabatan').value; }
        if (reviewPangkat && document.getElementById('pangkat')) { reviewPangkat.textContent = document.getElementById('pangkat').value; }
        if (reviewGrade && document.getElementById('grade')) { reviewGrade.textContent = document.getElementById('grade').value; }
        if (reviewTmtPegawai && document.getElementById('tmt_pegawai')) { reviewTmtPegawai.textContent = document.getElementById('tmt_pegawai').value; }
        if (reviewMasaKerja && document.getElementById('masa_kerja')) { reviewMasaKerja.textContent = document.getElementById('masa_kerja').value; }
        if (document.getElementById('review_nama_pegawai_footer')) { document.getElementById('review_nama_pegawai_footer').textContent = document.getElementById('nama_pegawai').value; }
        if (document.getElementById('review_jabatan_footer')) { document.getElementById('review_jabatan_footer').textContent = document.getElementById('jabatan').value; }
    }

    function openReviewModal() {
        if (leaveModalPangkatGajiTunjangan) {
            // Panggil fungsi pengisi data sebelum menampilkan modal
            fillReviewModalData();
            leaveModalPangkatGajiTunjangan.classList.remove('hidden');
            leaveModalPangkatGajiTunjangan.classList.add('flex');
        }
    }

    function closeReviewModal() {
        if (leaveModalPangkatGajiTunjangan) {
            leaveModalPangkatGajiTunjangan.classList.add('hidden');
            leaveModalPangkatGajiTunjangan.classList.remove('flex');
        }
    }

// --- Logika Eksekusi Modal Notifikasi ---
showNotificationModal(successModalPangkatGajiTunjangan);
showNotificationModal(errorModalPangkatGajiTunjangan);

// Tambahkan event listener untuk tombol tutup modal notifikasi
closeSuccessModalButton?.addEventListener('click', () => { hideNotificationModal(successModalPangkatGajiTunjangan); });
closeErrorModalButton?.addEventListener('click', () => { hideNotificationModal(errorModalPangkatGajiTunjangan); });

// --- Logika Eksekusi Modal Review Pengajuan (BARU) ---
if (openModalButtonPangkatGajiTunjangan) {
    openModalButtonPangkatGajiTunjangan.addEventListener('click', openReviewModal);
}

if (reviewCancelButton) {
    reviewCancelButton.addEventListener('click', closeReviewModal);
}

if (submitButton) {
    submitButton.addEventListener('click', function(event) {

        // Temukan form terdekat dari tombol submit
        const form = event.target.closest('form');

        if (form) {
            // Sembunyikan modal review saat loading dimulai
            closeReviewModal();

            // Tampilkan modal loading
            showLoadingModal();

            // Kirim formulir secara manual menggunakan JavaScript
            form.submit();
        } else {
            console.error("Kesalahan: Tombol submit tidak berada di dalam elemen <form>.");
        }
    });
}

window.addEventListener('beforeunload', function() {
    objectUrls.forEach(url => URL.revokeObjectURL(url));
});

// upload handler
const documentConfigs = {
    'Kenaikan Pangkat Reguler': ['Surat Permohonan', 'Salinan Surat Keputusan Pengangkatan Pertama', 'Salinan Surat Keputusan Kenaikan Gaji Pokok Berkala Terakhir', 'Salinan Surat Keputusan Kenaikan Pangkat Terakhir', 'Daftar Penilaian Kinerja 2 (dua) Tahun Terakhir Berpredikat Rata Rata Baik'],
    'Kenaikan Pangkat Penyesuaian': ['Surat Permohonan', 'Salinan Surat Keputusan Pengangkatan Pertama', 'Salinan Surat Keputusan Kenaikan Gaji Pokok Berkala Terakhir', 'Salinan Surat Keputusan Kenaikan Pangkat Terakhir', 'Surat Tugas Belajar (STB) atau Surat Izin Belajar (SIB) untuk melanjutkan Pendidikan', 'Salinan Ijazah dan Transkip Nilai', 'Salinan Akreditasi Institusi Termasuk Fakultas/Prodi/Jurusan', 'Daftar Penilaian Kinerja 2 (dua) Tahun Terakhir Berpredikat Rata Rata Baik'],
    'Kenaikan Pangkat Istimewa': ['Surat Permohonan', 'Salinan Surat Keputusan Pengangkatan Pertama', 'Salinan Surat Keputusan Kenaikan Gaji Pokok Berkala Terakhir', 'Salinan Surat Keputusan Kenaikan Pangkat Terakhir', 'Dokumen Tentang Prestasi Kerja atau Penemuan Baru yang bermanfaat bagi pengembangan pekerjaan usaha Bank Kota Bogor', 'Daftar Penilaian Kinerja 2 (dua) Tahun Terakhir Berpredikat Rata Rata Baik'],
    'Kenaikan Gaji Pokok Berkala': ['Surat Permohonan', 'Salinan Surat Keputusan Pengangkatan Pertama', 'Salinan Surat Keputusan Kenaikan Gaji Pokok Berkala Terakhir', 'Salinan Surat Keputusan Kenaikan Pangkat Terakhir', 'Daftar Penilaian Kinerja 2 (dua) Tahun Terakhir Berpredikat Rata Rata Baik'],
    'Tunjangan Keluarga (Suami/Istri)': ['Surat Permohonan', 'Salinan Buku Nikah', 'Salinan Kartu Tanda Penduduk (KTP Suami/ Istri)', 'Salinan Kartu Keluarga (KK)'],
    'Tunjangan Keluarga (Anak)': ['Surat Permohonan',  'Salinan Akta Kelahiran','Salinan Kartu Keluarga (KK)']
};

const selectElement = document.getElementById('jenis_pengajuan');
const container = document.getElementById('document-list-container');
const modal = document.getElementById('document-viewer-modal');
const iframe = document.getElementById('document-viewer-iframe');
const closeBtn = document.getElementById('viewer-close-button');
const MAX_FILE_SIZE_MB = 5;
const MAX_FILE_SIZE_BYTES = MAX_FILE_SIZE_MB * 1024 * 1024;

if (selectElement) {
    selectElement.addEventListener('change', function() {
        const selectedType = this.value;
        container.innerHTML = '';
        container.classList.remove('hidden');

        if (documentConfigs[selectedType]) {
            documentConfigs[selectedType].forEach((docName, index) => {
                const safeDocName = docName.replace(/[^a-zA-Z0-9]/g, '_');
                const docId = `upload-${selectedType}-${index}`;
                const row = document.createElement('div');
                row.className = 'flex items-center justify-between p-4 border-b last:border-b-0 bg-white hover:bg-gray-50';

                row.innerHTML = `
                    <span class="text-sm text-gray-700">${index + 1}. ${docName}</span>
                    <div class="flex items-center space-x-3">
                        <span id="status-${docId}" class="text-xs font-semibold text-red-600">Belum diunggah</span>

                        <button type="button" id="view-btn-${docId}" class="hidden bg-green-500 hover:bg-green-600 text-white text-xs font-semibold py-1 px-3 rounded shadow-sm" onclick="viewDocument(event)">
                            Lihat Dokumen
                        </button>

                        <!-- Atribut accept di sini diubah menjadi HANYA .pdf -->
                        <input type="file" id="${docId}" name="documents[${safeDocName}]" accept=".pdf" class="hidden">
                        <label for="${docId}" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-2 px-4 rounded shadow-md transition duration-150 ease-in-out">
                            Upload File
                        </label>
                    </div>
                `;
                container.appendChild(row);

                document.getElementById(docId).addEventListener('change', (event) => handleFileUpload(event, docId));
            });
        }
    });
}

// Fungsi untuk menangani unggahan file dan update status
function handleFileUpload(event, docId) {
    const fileInput = event.target;
    const statusSpan = document.getElementById(`status-${docId}`);
    const viewBtn = document.getElementById(`view-btn-${docId}`);

    if (fileInput.files && fileInput.files[0]) {
        const file = fileInput.files[0];

        // Validasi Tipe File (Hanya PDF)
        if (file.type !== 'application/pdf') {
            statusSpan.textContent = `Error: Hanya format PDF`;
            statusSpan.className = 'text-xs font-semibold text-red-600';
            viewBtn.classList.add('hidden');
            fileInput.value = '';
            alert(`File "${file.name}" bukan format PDF yang didukung.`);
            return;
        }

        // Validasi Ukuran File (Max 5MB)
        if (file.size > MAX_FILE_SIZE_BYTES) {
            statusSpan.textContent = `Error: Maksimal ${MAX_FILE_SIZE_MB} MB`;
            statusSpan.className = 'text-xs font-semibold text-red-600';
            viewBtn.classList.add('hidden');
            fileInput.value = '';
            alert(`File "${file.name}" terlalu besar. Maksimal ukuran file adalah ${MAX_FILE_SIZE_MB} MB.`);
            return;
        }

        // File valid
        statusSpan.textContent = `File: ${file.name}`;
        statusSpan.className = 'text-xs font-semibold text-green-600 max-w-[150px] truncate';
        viewBtn.classList.remove('hidden');

        const fileUrl = URL.createObjectURL(file);
        viewBtn.setAttribute('data-file-url', fileUrl);
    }
}

// ... (window.viewDocument, closeBtn event listener, window.onclick tetap sama) ...
window.viewDocument = function(event) {
    const viewBtn = event.target;
    const fileUrl = viewBtn.getAttribute('data-file-url');

    if (fileUrl) {
        iframe.src = fileUrl;
        modal.style.display = 'flex';
    }
}

if (closeBtn) {
    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
        iframe.src = '';
    });
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
        iframe.src = '';
    }
}


