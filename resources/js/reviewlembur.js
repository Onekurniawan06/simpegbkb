document.addEventListener('DOMContentLoaded', (event) => {
    // 1. Dapatkan referensi elemen-elemen DOM yang diperlukan
    const openModalButton = document.getElementById('openModalButtonLembur');
    const leaveModallembur = document.getElementById('leaveModallembur');
    const cancelButton = document.getElementById('cancelButton');

    // --- BARU: Referensi untuk Submit dan Loading ---
    const submitButton = document.getElementById('submitButton');
    const loadingModal = document.getElementById('loadingModalLembur');
    const mainForm = document.getElementById('leaveFormLembur'); // Pastikan <form> Anda punya ID ini

    // Referensi Modal Status
    const successModal = document.getElementById('successModalLembur');
    const errorModal = document.getElementById('errorModalLembur');
    const closeErrorButton = document.getElementById('closeErrorModalButton');

    // 2. Tampilkan otomatis modal apa pun yang punya class 'show-on-load'
    document.querySelectorAll('.show-on-load').forEach(modal => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });

    // --- Target Review Elemen di dalam Modal ---
    const reviewNamaPegawai = document.getElementById('review_nama_pegawai');
    const reviewTanggalLembur = document.getElementById('review_tanggal_lembur');
    const reviewJamMulai = document.getElementById('review_jam_mulai_lembur');
    const reviewJamSelesai = document.getElementById('review_jam_selesai_lembur');
    const reviewTotalLembur = document.getElementById('review_total_lembur');
    const reviewUraianTugas = document.getElementById('review_uraian_tugas');

    // --- Elemen Input Formulir Utama ---
    const inputNamaPegawai = document.getElementById('nama_pegawai');
    const inputTanggalLembur = document.getElementById('tanggal_lembur');
    const inputJamMulai = document.getElementById('jam_mulai');
    const inputJamSelesai = document.getElementById('jam_selesai');
    const inputTotalLembur = document.getElementById('total_jam');
    const inputUraianTugas = document.getElementById('uraian_tugas');

    // 2. Fungsi untuk membuka modal dan mengisi data review
    function openModalAndReview() {
        const namaPegawai = inputNamaPegawai ? inputNamaPegawai.value : 'N/A';
        const tanggalLembur = inputTanggalLembur ? tanggalLemburDateFmt(inputTanggalLembur.value) : 'N/A';
        const jamMulai = inputJamMulai ? inputJamMulai.value : 'N/A';
        const jamSelesai = inputJamSelesai ? inputJamSelesai.value : 'N/A';
        const totalLembur = inputTotalLembur ? inputTotalLembur.value : 'N/A';
        const uraianTugas = inputUraianTugas ? inputUraianTugas.value : 'N/A';

        reviewNamaPegawai.textContent = namaPegawai;
        reviewTanggalLembur.textContent = tanggalLembur;
        reviewJamMulai.textContent = jamMulai;
        reviewJamSelesai.textContent = jamSelesai;
        reviewTotalLembur.textContent = totalLembur;
        reviewUraianTugas.textContent = uraianTugas;

        leaveModallembur.classList.remove('hidden');
    }

    function tanggalLemburDateFmt(dateString) {
        if (!dateString) return 'N/A';
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    }

    // 3. Fungsi untuk menutup modal
    function closeModal() {
        leaveModallembur.classList.add('hidden');
    }

    function closeErrorModal() {
        errorModal.classList.add('hidden');
        errorModal.classList.remove('flex');
    }

    // 4. Tambahkan Event Listeners ke tombol-tombol
    if (openModalButton) {
        openModalButton.addEventListener('click', openModalAndReview);
    }
    if (cancelButton) {
        cancelButton.addEventListener('click', closeModal);
    }

    // --- BARU: Event Listener untuk Tombol Submit (Ya, Ajukan Lembur) ---
    if (submitButton) {
        submitButton.addEventListener('click', function() {
            // Tampilkan loading modal
            loadingModal.classList.remove('hidden');

            // Sembunyikan modal konfirmasi agar tidak tumpang tindih
            leaveModallembur.classList.add('hidden');

            // Jalankan submit form secara manual (Jika tombol bukan type="submit")
            // Pastikan elemen <form> Anda memiliki ID "formLembur"
            if (mainForm) {
                mainForm.submit();
            }
        });
    }

    const closeErrorModalButton = document.getElementById('closeErrorModalButton');
    if (closeErrorModalButton) {
        closeErrorModalButton.addEventListener('click', () => {
            const modal = document.getElementById('errorModalLembur');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });
    }

    // Tombol tutup modal error
    if (closeErrorButton) {
        closeErrorButton.addEventListener('click', closeErrorModal);
    }

    // Ambil referensi tombol Close X
    const closeSuccessButton = document.getElementById('closeSuccessModalButton');

    if (closeSuccessButton) {
        closeSuccessButton.addEventListener('click', () => {
            if (successModal) {
                successModal.classList.add('hidden');
                successModal.classList.remove('flex');
            }
        });
    }

    // Tambahkan juga agar bisa tutup saat klik area luar (backdrop)
    window.addEventListener('click', (e) => {
        if (e.target === leaveModallembur) closeModal();
        if (e.target === errorModal) closeErrorModal();
        // Tambahkan baris ini:
        if (e.target === successModal) {
            successModal.classList.add('hidden');
            successModal.classList.remove('flex');
        }
    });
});
