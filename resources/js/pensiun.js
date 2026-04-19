/**
 * Pensiun Handler Script: Kalkulator, Upload File, Viewer, dan Modal Notifikasi/Review
 */
(function() {
    // ... (Elemen Kalkulator, Upload, Viewer, dan Notifikasi tetap sama di sini) ...

    // --- Elemen Kalkulator Pensiun ---
    const tmtPegawaiInput = document.getElementById('tmt_pegawai');
    const masaKerjaInput = document.getElementById('masa_kerja');
    const tmtPensiunInput = document.getElementById('tmt_pensiun');
    const BUP = 58;

    // --- Elemen Multiple Upload ---
    let selectedFiles = [];
    const MAX_FILES = 5;

    // const fileUploadInput = document.getElementById('file-upload');
    // const fileListContainer = document.getElementById('file-list-container');
    // const fileListUl = document.getElementById('file-list');

    // --- Elemen Modal Viewer ---
    const viewerModal = document.getElementById('document-viewer-modal');
    const viewerIframe = document.getElementById('document-viewer-iframe');
    const viewerCloseButton = document.getElementById('viewer-close-button');
    const MAX_SIZE_MB = 5; // Batas 5MB

    // --- Elemen Modal Notifikasi (Sukses/Error) ---
    const successModalPensiun = document.getElementById('successModalPensiun');
    const errorModalPensiun = document.getElementById('errorModalPensiun');
    const closeSuccessModalButton = document.getElementById('closeSuccessModalButton');
    const closeErrorModalButton = document.getElementById('closeErrorModalButton');

    // --- Elemen Modal Loading (BARU) ---
    const loadingModalPensiun = document.getElementById('loadingModalpensiun');

    // --- Elemen Modal Review Pengajuan ---
    const openModalButtonPensiun = document.getElementById('openModalButtonPensiun');
    const leaveModalPensiun = document.getElementById('leaveModalpensiun');
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
    const reviewTmtPensiun = document.getElementById('review_tmt_pensiun');


    // Variabel untuk menyimpan Object URLs secara global di dalam IIFE agar persisten
    const objectUrls = new Map();

    /**
     * LOGIK KALKULATOR PENSIUN
     */
    function formatTanggalOutput(date) {
        const dd = String(date.getDate()).padStart(2, '0');
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const yyyy = date.getFullYear();
        return `${dd}-${mm}-${yyyy}`;
    }

    function parseDateIndonesia(dateString) {
        if (!dateString) return null;
        const cleanString = dateString.replace(/\//g, '-');
        const parts = cleanString.split('-');
        if (parts.length !== 3) return null;
        const dd = parseInt(parts, 10);
        const mm = parseInt(parts, 10) - 1;
        const yyyy = parseInt(parts, 10);
        const dateObject = new Date(yyyy, mm, dd);
        return isNaN(dateObject.getTime()) ? null : dateObject;
    }

    function hitungOtomatis() {
        if (!tmtPegawaiInput || !tmtPegawaiInput.value) return;
        const tmtDate = parseDateIndonesia(tmtPegawaiInput.value);
        if (!tmtDate) {
            if(masaKerjaInput) masaKerjaInput.value = '';
            if(tmtPensiunInput) tmtPensiunInput.value = '';
            return;
        }
        let pensiunDate = new Date(tmtDate);
        pensiunDate.setFullYear(pensiunDate.getFullYear() + BUP);
        if(tmtPensiunInput) tmtPensiunInput.value = formatTanggalOutput(pensiunDate);

        const today = new Date();
        let years = today.getFullYear() - tmtDate.getFullYear();
        let months = today.getMonth() - tmtDate.getMonth();
        if (months < 0) { years--; months += 12; }
        if(masaKerjaInput) masaKerjaInput.value = `${years} Tahun, ${months} Bulan`;
    }

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
        if (loadingModalPensiun) {
            loadingModalPensiun.classList.remove('hidden');
            loadingModalPensiun.classList.add('flex');
        }
    }

    function hideLoadingModal() {
        if (loadingModalPensiun) {
            loadingModalPensiun.classList.add('hidden');
            loadingModalPensiun.classList.remove('flex');
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
        if (reviewTmtPensiun && document.getElementById('tmt_pensiun')) { reviewTmtPensiun.textContent = document.getElementById('tmt_pensiun').value; }
        if (document.getElementById('review_nama_pegawai_footer')) { document.getElementById('review_nama_pegawai_footer').textContent = document.getElementById('nama_pegawai').value; }
        if (document.getElementById('review_jabatan_footer')) { document.getElementById('review_jabatan_footer').textContent = document.getElementById('jabatan').value; }
    }

    function openReviewModal() {
        if (leaveModalPensiun) {
            // Panggil fungsi pengisi data sebelum menampilkan modal
            fillReviewModalData();
            leaveModalPensiun.classList.remove('hidden');
            leaveModalPensiun.classList.add('flex');
        }
    }

    function closeReviewModal() {
        if (leaveModalPensiun) {
            leaveModalPensiun.classList.add('hidden');
            leaveModalPensiun.classList.remove('flex');
        }
    }

    /**
     * LOGIK MULTIPLE FILE UPLOAD
     */
    // 1. Definisikan fungsi modal secara GLOBAL agar bisa dipanggil onclick dari HTML string
    window.openViewerModal = function(fileUrl) {
        const modal = document.getElementById('document-viewer-modal');
        const iframe = document.getElementById('document-viewer-iframe');
        const title = document.getElementById('pdfNameTitle');

        if (modal && iframe) {
            iframe.src = fileUrl;
            modal.style.display = 'flex';
            if (title) title.innerText = "Pratinjau Dokumen";
        }
    };

    window.closeViewerModal = function() {
        const modal = document.getElementById('document-viewer-modal');
        const iframe = document.getElementById('document-viewer-iframe');
        if (modal) modal.style.display = 'none';
        if (iframe) iframe.src = '';
    };

    function handleFileUpload() {
        const fileUploadInput = document.getElementById('file-upload');
        const fileListUl = document.getElementById('file-list');
        const fileListContainer = document.getElementById('file-list-container');

        // Modal Elements
        const validationModal = document.getElementById('fileValidationErrorModal');
        const validationMessage = document.getElementById('fileValidationMessage');

        if (!fileUploadInput) return;

        fileUploadInput.addEventListener('change', function() {
            const newFiles = Array.from(this.files);

            // VALIDASI: Cek jumlah maksimal file dengan Modal Baru
            if (selectedFiles.length + newFiles.length > MAX_FILES) {
                if (validationModal && validationMessage) {
                    validationMessage.innerHTML = `Anda hanya dapat mengunggah maksimal <b>${MAX_FILES} file</b>.<br>Saat ini sudah ada ${selectedFiles.length} file terupload.`;
                    validationModal.classList.remove('hidden');
                    validationModal.classList.add('flex');
                }
                this.value = ''; // Reset pilihan terakhir
                return;
            }

            // Tambahkan file baru ke array
            newFiles.forEach(file => {
                const isDuplicate = selectedFiles.some(f => f.name === file.name && f.size === file.size);
                if (!isDuplicate) {
                    selectedFiles.push(file);
                }
            });

            updateUIAndInput();
        });

        function updateUIAndInput() {
            fileListUl.innerHTML = '';
            if (selectedFiles.length > 0) {
                fileListContainer.classList.remove('hidden');
            } else {
                fileListContainer.classList.add('hidden');
            }

            selectedFiles.forEach((file, index) => {
                const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                if (!objectUrls.has(file.name)) {
                    objectUrls.set(file.name, URL.createObjectURL(file));
                }
                const fileUrl = objectUrls.get(file.name);

                const li = document.createElement('li');
                li.className = 'px-3 py-2 flex justify-between items-center bg-gray-50 border-b border-gray-100 last:border-0';
                li.innerHTML = `
                    <div class="flex items-center space-x-2 truncate">
                        <span class="truncate w-40 text-gray-700">${file.name}</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-[10px] font-medium text-gray-400">${fileSizeMB} MB</span>
                        <button type="button" class="btn-view text-blue-600 hover:text-blue-800 text-xs font-semibold" onclick="window.openViewerModal('${fileUrl}')">Lihat</button>
                        <button type="button" class="btn-delete text-red-600 hover:text-red-800 text-xs font-semibold" data-index="${index}">Hapus</button>
                    </div>
                `;
                fileListUl.appendChild(li);
            });

            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            fileUploadInput.files = dataTransfer.files;

            // Listener Hapus
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.onclick = function() {
                    const idx = this.getAttribute('data-index');
                    const fileName = selectedFiles[idx].name;
                    URL.revokeObjectURL(objectUrls.get(fileName));
                    objectUrls.delete(fileName);
                    selectedFiles.splice(idx, 1);
                    updateUIAndInput();
                };
            });
        }
    }

    // --- Eksekusi ---
    document.addEventListener('DOMContentLoaded', function() {
        // ... (Inisialisasi Kalkulator) ...
        handleFileUpload();

        // Listener untuk tombol tutup modal viewer
        const closeValidationBtn = document.getElementById('closeFileValidationError');
            const validationModal = document.getElementById('fileValidationErrorModal');
            if (closeValidationBtn && validationModal) {
                closeValidationBtn.addEventListener('click', () => {
                    validationModal.classList.add('hidden');
                    validationModal.classList.remove('flex');
                });
            }

            // Listener untuk tombol tutup modal viewer asli anda
            const viewerCloseButton = document.getElementById('viewer-close-button');
            if (viewerCloseButton) {
                viewerCloseButton.addEventListener('click', () => {
                    const modal = document.getElementById('document-viewer-modal');
                    const iframe = document.getElementById('document-viewer-iframe');
                    modal.style.display = 'none';
                    iframe.src = '';
                });
            }

        // --- Logika Eksekusi Modal Notifikasi ---
        showNotificationModal(successModalPensiun);
        showNotificationModal(errorModalPensiun);

        // Tambahkan event listener untuk tombol tutup modal notifikasi
        closeSuccessModalButton?.addEventListener('click', () => { hideNotificationModal(successModalPensiun); });
        closeErrorModalButton?.addEventListener('click', () => { hideNotificationModal(errorModalPensiun); });

        // --- Logika Eksekusi Modal Review Pengajuan (BARU) ---
        if (openModalButtonPensiun) {
            openModalButtonPensiun.addEventListener('click', openReviewModal);
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

    });

    window.addEventListener('beforeunload', function() {
        objectUrls.forEach(url => URL.revokeObjectURL(url));
    });

})();
