/**
 * reviewcuti.js
 */

// Fungsi closeErrorModal yang bersifat lokal (di dalam scope ini)
const closeErrorModal = () => {
    const errorModal = document.getElementById('errorModal');
    if (errorModal) {
        errorModal.classList.add('hidden');
        errorModal.classList.remove('flex');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const elements = {
        jenisCutiEl: document.getElementById('jenis_cuti'),
        startDateEl: document.getElementById('tanggal_mulai'),
        endDateEl: document.getElementById('tanggal_selesai'),
        durationEl: document.getElementById('jumlah_cuti'),
        durationDisplayEl: document.getElementById('sisa_cuti'),
        keteranganEl: document.getElementById('keterangan'),

        leaveModalEl: document.getElementById('leaveModal'),
        openModalButtonEl: document.getElementById('openModalButton'),
        cancelButtonEl: document.getElementById('cancelButton'),
        submitButtonEl: document.getElementById('submitButton'),
        loadingModalEl: document.getElementById('loadingModal'),
        successModalEl: document.getElementById('successModal'),
        errorModalEl: document.getElementById('errorModal'),

        reviewJenisCutiEl: document.getElementById('review_jenis_cuti'),
        reviewTanggalMulaiEl: document.getElementById('review_tanggal_mulai'),
        reviewTanggalSelesaiEl: document.getElementById('review_tanggal_selesai'),
        reviewJumlahCutiEl: document.getElementById('review_jumlah_cuti'),
        reviewSisaCutiEl: document.getElementById('review_sisa_cuti'),
        reviewKeteranganEl: document.getElementById('review_keterangan'),
    };

    const populateModalData = () => {
        if (!elements.reviewJenisCutiEl) return;
        elements.reviewJenisCutiEl.textContent = elements.jenisCutiEl.value;
        elements.reviewTanggalMulaiEl.textContent = elements.startDateEl.value;
        elements.reviewTanggalSelesaiEl.textContent = elements.endDateEl.value;
        elements.reviewJumlahCutiEl.textContent = elements.durationEl.value;
        elements.reviewSisaCutiEl.textContent = elements.durationDisplayEl.value;
        elements.reviewKeteranganEl.textContent = elements.keteranganEl.value;
    };

    const openModal = () => {
        populateModalData();
        elements.leaveModalEl.classList.remove('hidden');
        elements.leaveModalEl.classList.add('flex');
    };

    const closeModal = (event) => {
        if (event) {
            event.preventDefault();
        }
        elements.leaveModalEl.classList.add('hidden');
        elements.leaveModalEl.classList.remove('flex');
    };

    if (elements.openModalButtonEl) elements.openModalButtonEl.addEventListener('click', openModal);
    if (elements.cancelButtonEl) elements.cancelButtonEl.addEventListener('click', closeModal);

    // --- SISIPKAN KODE BARU DI SINI ---
    const closeErrorModalButton = document.getElementById('closeErrorModalButton');
    if (closeErrorModalButton) {
        closeErrorModalButton.addEventListener('click', closeErrorModal);
    }

    // --- TAMBAHKAN INI UNTUK TOMBOL SUKSES ---
    const closeSuccessModalButton = document.getElementById('closeSuccessModalButton');
    if (closeSuccessModalButton) {
        closeSuccessModalButton.addEventListener('click', () => {
            elements.successModalEl.classList.add('hidden');
            elements.successModalEl.classList.remove('flex');
        });
    }
    // ------------------------------------------

    if (elements.submitButtonEl) {
        elements.submitButtonEl.addEventListener('click', function(e) {
            e.preventDefault();
            const form = document.getElementById('leaveForm');
            this.disabled = true;
            this.innerHTML = "Memproses...";
            if (elements.leaveModalEl) closeModal();
            if (elements.loadingModalEl) {
                elements.loadingModalEl.classList.remove('hidden');
                elements.loadingModalEl.style.setProperty('display', 'flex', 'important');
                elements.loadingModalEl.style.zIndex = '99999';
            }
            setTimeout(() => { if (form) form.submit(); }, 50);
        });
    }

    // Menangani Modal Sukses/Gagal (Pengganti @if Laravel)
    if (elements.successModalEl && elements.successModalEl.classList.contains('show-on-load')) {
        elements.successModalEl.classList.remove('hidden');
        elements.successModalEl.classList.add('flex');
    }
    if (elements.errorModalEl && elements.errorModalEl.classList.contains('show-on-load')) {
        elements.errorModalEl.classList.remove('hidden');
        elements.errorModalEl.classList.add('flex');
    }
});

