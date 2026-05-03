/**
 * reviewcuti.js - Full Fixed Version
 * Menangani preview data pengajuan cuti secara real-time ke modal review
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
        // Input Fields (Data dari Form)
        jenisCutiEl: document.getElementById('jenis_cuti'),
        startDateEl: document.getElementById('tanggal_mulai'),
        endDateEl: document.getElementById('tanggal_selesai'),
        durationEl: document.getElementById('jumlah_cuti'),        // Input Lama Cuti
        durationDisplayEl: document.getElementById('sisa_cuti'),   // Input Sisa Cuti
        keteranganEl: document.getElementById('keterangan'),

        // Modals & Buttons
        leaveModalEl: document.getElementById('leaveModal'),
        openModalButtonEl: document.getElementById('openModalButton'),
        cancelButtonEl: document.getElementById('cancelButton'),
        submitButtonEl: document.getElementById('submitButton'),
        loadingModalEl: document.getElementById('loadingModal'),
        successModalEl: document.getElementById('successModal'),
    };

    const populateModalData = () => {
        // 1. Ambil data dari form
        const jenis = elements.jenisCutiEl ? elements.jenisCutiEl.value : '';
        const tglMulai = elements.startDateEl ? elements.startDateEl.value : '';
        const tglSelesai = elements.endDateEl ? elements.endDateEl.value : '';
        const jumlah = elements.durationEl ? elements.durationEl.value : '';
        const sisa = elements.durationDisplayEl ? elements.durationDisplayEl.value : '';
        const ket = elements.keteranganEl ? elements.keteranganEl.value : '-';

        // 2. Bersihkan dulu SEMUA centang, lama cuti, dan sisa cuti di tabel
        document.querySelectorAll('[id^="v_"]').forEach(el => el.innerHTML = '');
        document.querySelectorAll('.review-lama-cuti').forEach(el => el.textContent = '');
        document.querySelectorAll('.review-sisa-cuti').forEach(el => el.textContent = '');

        // 3. Isi data ke baris yang sesuai
        if (jenis) {
            const slug = jenis.toLowerCase().trim().replace(/\s+/g, '_').replace(/[^\w-]+/g, '');
            
            // Isi Centang
            const targetV = document.getElementById(`v_${slug}`);
            if (targetV) targetV.innerHTML = '<span class="checkmark">✓</span>';

            // Isi Lama Cuti di baris tersebut
            const targetLama = document.getElementById(`lama_${slug}`);
            if (targetLama) targetLama.textContent = (jumlah && jumlah !== '0') ? `${jumlah} Hari` : '';

            // Isi Sisa Cuti di baris tersebut
            const targetSisa = document.getElementById(`sisa_${slug}`);
            if (targetSisa) targetSisa.textContent = (sisa !== '' && sisa !== null) ? `${sisa} Hari` : '';
        }

        // 4. Isi data detail di kolom kanan (tetap seperti aslinya)
        const reviewJumlah = document.getElementById('review_jumlah_cuti_display');
        const reviewTmt = document.getElementById('review_tmt_cuti_display');
        const reviewAlasan = document.getElementById('review_alasan_cuti_display');

        if (reviewJumlah) reviewJumlah.textContent = jumlah || '0';
        if (reviewTmt) reviewTmt.textContent = (tglMulai && tglSelesai) ? `${tglMulai} s/d ${tglSelesai}` : '... s/d ...';
        if (reviewAlasan) reviewAlasan.textContent = ket;
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

    // Event Listeners
    if (elements.openModalButtonEl) elements.openModalButtonEl.addEventListener('click', openModal);
    if (elements.cancelButtonEl) elements.cancelButtonEl.addEventListener('click', closeModal);

    // Submission Logic
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
