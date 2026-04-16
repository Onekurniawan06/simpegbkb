/**
 * reviewcuti.js - Full Fixed Version
 */

const closeErrorModal = () => {
    const errorModal = document.getElementById('errorModal');
    if (errorModal) {
        errorModal.classList.add('hidden');
        errorModal.classList.remove('flex');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const elements = {
        // Input Fields (Form)
        jenisCutiEl: document.getElementById('jenis_cuti'),
        startDateEl: document.getElementById('tanggal_mulai'),
        endDateEl: document.getElementById('tanggal_selesai'),
        durationEl: document.getElementById('jumlah_cuti'),
        durationDisplayEl: document.getElementById('sisa_cuti'),
        keteranganEl: document.getElementById('keterangan'),

        // Modals & Buttons
        leaveModalEl: document.getElementById('leaveModal'),
        openModalButtonEl: document.getElementById('openModalButton'),
        cancelButtonEl: document.getElementById('cancelButton'),
        submitButtonEl: document.getElementById('submitButton'),
        loadingModalEl: document.getElementById('loadingModal'),
        successModalEl: document.getElementById('successModal'),
        errorModalEl: document.getElementById('errorModal'),
    };

    const populateModalData = () => {
        // 1. Ambil data dari input form
        const jenis = elements.jenisCutiEl ? elements.jenisCutiEl.value : '';
        const tglMulai = elements.startDateEl ? elements.startDateEl.value : '';
        const tglSelesai = elements.endDateEl ? elements.endDateEl.value : '';
        const jumlah = elements.durationEl ? elements.durationEl.value : '0';
        const sisa = elements.durationDisplayEl ? elements.durationDisplayEl.value : '0';
        const ket = elements.keteranganEl ? elements.keteranganEl.value : '-';

        // 2. Isi ke tabel review (Bagian Periode & Alasan)
        const reviewJumlah = document.getElementById('review_jumlah_cuti_display');
        const reviewTmt = document.getElementById('review_tmt_cuti_display');
        const reviewAlasan = document.getElementById('review_alasan_cuti_display');
        const reviewSisa = document.getElementById('review_sisa_cuti_display');
        const reviewDiambil = document.getElementById('review_cuti_diambil_display');

        if (reviewJumlah) reviewJumlah.textContent = jumlah;
        if (reviewAlasan) reviewAlasan.textContent = ket;
        if (reviewSisa) reviewSisa.textContent = sisa;
        if (reviewTmt) reviewTmt.textContent = (tglMulai || '...') + ' - ' + (tglSelesai || '...');

        // Logika Hitung Cuti Diambil (Asumsi kuota tahunan 12)
        if (reviewDiambil && sisa) {
            reviewDiambil.textContent = 12 - parseInt(sisa);
        }

        // 3. Logika Centang (✔)
        // Reset semua centang lama dulu
        const allCheckboxes = document.querySelectorAll('[id^="v_"]');
        allCheckboxes.forEach(el => el.innerHTML = '');

        // Buat ID target (misal: "Cuti Tahunan" jadi "v_cuti_tahunan")
        if (jenis) {
            const slug = jenis.toLowerCase()
                .trim()
                .replace(/\s+/g, '_')
                .replace(/[^\w-]+/g, '');

            const targetId = 'v_' + slug;
            const targetEl = document.getElementById(targetId);
            if (targetEl) {
                targetEl.innerHTML = '<span class="checkmark">&#10003;</span>';
            }
        }
    };

    const openModal = () => {
        populateModalData();
        if (elements.leaveModalEl) {
            elements.leaveModalEl.classList.remove('hidden');
            elements.leaveModalEl.classList.add('flex');
        }
    };

    const closeModal = (e) => {
        if (e) e.preventDefault();
        if (elements.leaveModalEl) {
            elements.leaveModalEl.classList.add('hidden');
            elements.leaveModalEl.classList.remove('flex');
        }
    };

    if (elements.openModalButtonEl) elements.openModalButtonEl.addEventListener('click', openModal);
    if (elements.cancelButtonEl) elements.cancelButtonEl.addEventListener('click', closeModal);

    // Tombol Success/Error
    const closeErrorBtn = document.getElementById('closeErrorModalButton');
    if (closeErrorBtn) closeErrorBtn.addEventListener('click', closeErrorModal);

    const closeSuccessBtn = document.getElementById('closeSuccessModalButton');
    if (closeSuccessBtn) {
        closeSuccessBtn.addEventListener('click', () => {
            elements.successModalEl.classList.add('hidden');
            elements.successModalEl.classList.remove('flex');
        });
    }

    if (elements.submitButtonEl) {
        elements.submitButtonEl.addEventListener('click', function(e) {
            e.preventDefault();
            const form = document.getElementById('leaveForm');
            this.disabled = true;
            this.innerHTML = "Memproses...";
            closeModal();
            if (elements.loadingModalEl) {
                elements.loadingModalEl.classList.remove('hidden');
                elements.loadingModalEl.style.setProperty('display', 'flex', 'important');
            }
            setTimeout(() => { if (form) form.submit(); }, 100);
        });
    }
});
