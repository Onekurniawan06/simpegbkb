
let currentPdfUrl = null;

function showErrorModal(message) {
    const errorModal = document.getElementById('errorModal');
    const errorMessageElement = document.getElementById('errorMessage');

    // Mengatur pesan error
    if (errorMessageElement) {
        errorMessageElement.textContent = message;
    }

    // Menampilkan modal dengan menghapus class 'hidden' dari Tailwind
    if (errorModal) {
        errorModal.classList.remove('hidden');
    }
}

export function initCutiHandler() {
    console.log('Cuti Logic Initialized via Vite.');

    // --- 1. Ambil Data dari Jembatan HTML & Konfigurasi ---
    const dataBridge = document.getElementById('cuti-data-bridge');
    if (!dataBridge) return;

    // Data hari libur nasional akan disimpan di sini (FORMAT: 'YYYY-MM-DD')
    let nationalHolidays = [];

    // Fungsi untuk memeriksa apakah suatu tanggal adalah hari libur nasional
    const isHoliday = (dateString) => nationalHolidays.includes(dateString);

    // FUNGSI UNTUK MENYEMBUNYIKAN POPUP MERAH
    const hideCustomErrorPopup = () => {
        const popupEl = document.getElementById('error-popup');
        if (popupEl) popupEl.classList.add('hidden');
    };
    // FUNGSI UNTUK MENAMPILkan POPUP MERAH (Validasi Tanggal)
    const showCustomErrorPopup = (message) => {
         hideCustomWarningPopup(); // Tutup warning jika ada
        const popupEl = document.getElementById('error-popup');
        const messageEl = document.getElementById('error-message');
        if (popupEl && messageEl) {
            messageEl.textContent = message;
            popupEl.classList.remove('hidden');
        }
    };

    // FUNGSI UNTUK MENYEMBUNYIKAN POPUP KUNING
    const hideCustomWarningPopup = () => {
        const popupEl = document.getElementById('warning-popup');
        if (popupEl) popupEl.classList.add('hidden');
    };
    // FUNGSI UNTUK MENAMPILKAN POPUP WARNING KUNING (Notifikasi Gagal API)
    const showCustomWarningPopup = (message) => {
        hideCustomErrorPopup(); // Tutup error jika ada
        const popupEl = document.getElementById('warning-popup');
        const messageEl = document.getElementById('warning-message');
        if (popupEl && messageEl) {
            messageEl.textContent = message;
            popupEl.classList.remove('hidden');
        }
    };

    // Fungsi fetchHolidays yang diperbarui untuk memanggil proxy lokal
    const fetchHolidays = async (year) => {
        try {
            // PERBAIKAN: Gunakan tanda tanya (?) bukan garis miring (/)
            const apiUrl = `https://libur.deno.dev/api?year=${year}`;

            const response = await fetch(apiUrl);

            // Cek dulu apakah response ok (status 200)
            if (!response.ok) {
                throw new Error(`Server error: ${response.status}`);
            }

            const data = await response.json();

            // Ambil list tanggalnya saja
            nationalHolidays = data.map(h => h.date);

            console.log(`Berhasil memuat libur nasional tahun ${year}.`);
            hideCustomWarningPopup(); // Tutup peringatan oranye
            updateCutiFields();

        } catch (error) {
            console.error("Gagal memuat data hari libur nasional:", error);
            // Jika gagal, tampilkan peringatan oranye lagi
            showCustomWarningPopup("Gagal memuat data hari libur nasional. Perhitungan mungkin tidak akurat.");
        }
    };


    const currentYear = new Date().getFullYear();
    fetchHolidays(currentYear);

    const JATAH_CUTI_TAHUNAN_MAKSIMAL = parseInt(dataBridge.dataset.jatahCutiMax || '12', 10);
    let allJenisCuti = [];
    try {
        allJenisCuti = JSON.parse(dataBridge.dataset.allJenisCuti);
    } catch (e) {
        console.error('Gagal parsing data jenis cuti:', e);
    }

    // Fungsi utilitas untuk format tanggal ke YYYY-MM-DD
    const formatDateToISO = (date) => {
        const year = date.getFullYear();
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const day = date.getDate().toString().padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    /**
     * FUNGSI PERHITUNGAN TANGGAL SELESAI BARU (Mengabaikan Hari Minggu & Libur Nasional)
     * Menggunakan durasi hari kerja yang diberikan untuk menghitung tanggal selesai otomatis.
     */
    const calculateEndDateExcludingWeekendsAndHolidays = (startDateString, duration) => {
        if (!startDateString || !duration || isNaN(duration) || duration <= 0) return '';

        const jenisForm = (dataBridge.dataset.jenisPengajuan || 'cuti').toLowerCase();
        const isLembur = (jenisForm === 'lembur');

        let currentDate = new Date(startDateString);
        let daysToCount = parseInt(duration);

        while (daysToCount > 0) {
            const dayOfWeek = currentDate.getDay();
            const dateString = formatDateToISO(currentDate);

            let isWorkingDay = false;

            // Logika pengecekan hari kerja
            if (isLembur) {
                // Lembur: Minggu (0) & Libur Nasional tidak dihitung
                if (dayOfWeek !== 0 && !isHoliday(dateString)) isWorkingDay = true;
            } else {
                // Cuti: Sabtu (6), Minggu (0), & Libur Nasional tidak dihitung
                if (dayOfWeek !== 0 && dayOfWeek !== 6 && !isHoliday(dateString)) isWorkingDay = true;
            }

            if (isWorkingDay) {
                daysToCount--; // Kurangi jatah hari hanya jika ini hari kerja
            }

            // PERUBAHAN PENTING: Hanya tambah hari jika jatah (daysToCount) masih ada
            if (daysToCount > 0) {
                currentDate.setDate(currentDate.getDate() + 1);
            }
        }

        return formatDateToISO(currentDate);
    };


    /**
     * FUNGSI UNTUK MENAMBAHKAN BULAN (Calendar days first)
     */
    const calculateEndDateInMonths = (startDateString, months) => {
        if (!startDateString || !months || isNaN(months) || months <= 0) { return ''; }
        const date = new Date(startDateString);
        date.setMonth(date.getMonth() + parseInt(months));
        date.setDate(date.getDate() - 1);
        return formatDateToISO(date);
    };

    /**
     * FUNGSI UNTUK MENGHITUNG HARI KERJA (Diluar Hari Minggu & Libur Nasional) antara dua tanggal
     */
    const calculateWorkingDaysBetweenDates = (startDateString, endDateString, isLembur = false) => {
        const start = new Date(startDateString);
        const end = new Date(endDateString);
        let count = 0;
        let currentDate = new Date(start);

        while (currentDate <= end) {
            const dayOfWeek = currentDate.getDay(); // 0=Minggu, 6=Sabtu
            const dateString = formatDateToISO(currentDate);

            if (isLembur) {
                // MODE LEMBUR: Sabtu dihitung, Minggu & Libur Nasional dilewati
                if (dayOfWeek !== 0 && !isHoliday(dateString)) {
                    count++;
                }
            } else {
                // MODE CUTI: Sabtu, Minggu, & Libur Nasional dilewati
                if (dayOfWeek !== 0 && dayOfWeek !== 6 && !isHoliday(dateString)) {
                    count++;
                }
            }
            currentDate.setDate(currentDate.getDate() + 1);
        }
        return count;
    };

    // --- 2. Konfigurasi Mapping File ---
    // (Fungsi fileMapping telah dihapus)

    // --- 3. Seleksi Elemen DOM ---
    const elements = {
        jenisCutiEl: document.getElementById('jenis_cuti'),
        durationEl: document.getElementById('jumlah_cuti'),
        remainingCutiEl: document.getElementById('sisa_cuti'),
        durationDisplayEl: document.getElementById('jatah_periode_hari'),
        startDateEl: document.getElementById('tanggal_mulai'),
        endDateEl: document.getElementById('tanggal_selesai'),
        subJenisCutiContainer: document.getElementById('sub_jenis_cuti_container'),
        subJenisCutiEl: document.getElementById('sub_jenis_cuti'),
        // Bagian Download & Upload (file_cuti) telah dihapus
        tnCloseWarningPopup: document.getElementById('close-warning-popup'),
        btnCloseError: document.getElementById('btn-close-error'),
        btnCloseWarning: document.getElementById('btn-close-warning'),
        btnRefresh: document.getElementById('btn_refresh_form'),
    };

    let takenDates = []; // Variabel untuk menyimpan tanggal cuti yang sudah diambil

    const fetchTakenDates = async () => {
        try {
            const response = await fetch('/api/cuti-divisi-dates');
            if (!response.ok) throw new Error('Network response was not ok');
            takenDates = await response.json();
        } catch (error) {
            console.error("Error fetching taken dates:", error);
        }
    };

    const checkForOverlap = (startDate, endDate) => {
        if (!startDate || !endDate || takenDates.length === 0) {
            return false;
        }

        const start = new Date(startDate);
        const end = new Date(endDate);
        let overlapFound = false;

        for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
            const currentDateFormatted = d.toISOString().split('T')[0]; // Menambahkan [0] untuk format YYYY-MM-DD

            if (takenDates.includes(currentDateFormatted)) {
                overlapFound = true;
                break;
            }
        }
        return overlapFound;
    };

    /**
     * FUNGSI HITUNG DURASI & VALIDASI DURASI (USER INPUT MANUAL)
     */
    const calculateDuration = () => {
        hideCustomErrorPopup();
        hideCustomWarningPopup();

        if (elements.startDateEl?.value && elements.endDateEl?.value) {
            // Ambil jenis pengajuan dari bridge, ubah ke huruf kecil semua
            const jenisForm = (dataBridge.dataset.jenisPengajuan || 'cuti').toLowerCase();

            // Cek apakah ini form lembur
            const isLembur = (jenisForm === 'lembur');

            // Hitung hari kerja dengan parameter isLembur
            const workingDaysTaken = calculateWorkingDaysBetweenDates(
                elements.startDateEl.value,
                elements.endDateEl.value,
                isLembur
            );

            // --- Logika Overlap (Tumpang Tindih) ---
            const hasOverlap = checkForOverlap(elements.startDateEl.value, elements.endDateEl.value);
            if (hasOverlap) {
                showCustomErrorPopup('PERINGATAN: Tanggal yang Anda pilih tumpang tindih dengan cuti rekan satu divisi!');
                elements.endDateEl.value = '';
                elements.durationEl.value = 0;
                return;
            }

            if (workingDaysTaken > 0) {
                const selectedCutiName = elements.jenisCutiEl?.value;

                // Validasi khusus Cuti Tahunan (Sabtu/Minggu sudah otomatis tidak dihitung)
                if (selectedCutiName === 'Cuti Tahunan' && !isLembur) {
                    if (workingDaysTaken > JATAH_CUTI_TAHUNAN_MAKSIMAL) {
                        showCustomErrorPopup(`PERINGATAN: Maksimal ${JATAH_CUTI_TAHUNAN_MAKSIMAL} hari kerja.`);
                        elements.endDateEl.value = '';
                        elements.durationEl.value = 0;
                        return;
                    }
                }

                // Set nilai ke input durasi
                if (elements.durationEl) {
                    elements.durationEl.value = workingDaysTaken;
                }
            } else {
                if (elements.durationEl) elements.durationEl.value = 0;
            }
        }
        updateCutiFields();
    };


    /**
     * FUNGSI UPDATE FIELD CUTI (Logika Utama + Perhitungan Tanggal Otomatis)
     */
    const updateCutiFields = () => {
        // Panggil closePopup di sini juga untuk memastikan popup tertutup saat jenis cuti berubah
        hideCustomErrorPopup();
        hideCustomWarningPopup();

        const selectedCutiName = elements.jenisCutiEl?.value;
        if (!selectedCutiName) return;
        const selectedCutiData = allJenisCuti.find(c => c.nama_cuti === selectedCutiName);
        if (!selectedCutiData) return;

        const startDateString = elements.startDateEl?.value;

        // Asumsi data bridge menyediakan durasi_hari untuk Cuti Sakit (misal: durasi_hari: 3)
        const durationDays = selectedCutiData.durasi_hari;
        const durationMonths = selectedCutiData.durasi_bulan;

        if (selectedCutiName === 'Cuti Tahunan') {
            if (elements.endDateEl) elements.endDateEl.readOnly = false;
        }
        // LOGIKA Cuti Besar atau Cuti Melahirkan (berdasarkan durasi_bulan)
        else if (startDateString && (selectedCutiName === 'Cuti Besar' || selectedCutiName === 'Cuti Melahirkan') && durationMonths) {

            // 1. Hitung tanggal selesai kalender (Bulan)
            const calendarEndDateString = calculateEndDateInMonths(startDateString, durationMonths);

            // 2. Hitung total hari (Gunakan fungsi asli Anda, tapi kita tambahkan parameter 'false' atau bypass)
            // Asumsi: Kita buat variabel durasi kalender murni
            const start = new Date(startDateString);
            const end = new Date(calendarEndDateString);
            const totalCalendarDays = Math.ceil(Math.abs(end - start) / (1000 * 60 * 60 * 24)) + 1;

            if (elements.endDateEl) {
                // Langsung gunakan hasil dari calculateEndDateInMonths (Tanpa skip sabtu minggu)
                elements.endDateEl.value = calendarEndDateString;
                elements.endDateEl.readOnly = true;
            }
            if (elements.durationEl) {
                elements.durationEl.value = totalCalendarDays;
            }

        }
        // JENIS CUTI LAIN DENGAN DURASI HARI TETAP (Termasuk Cuti Sakit 3 Hari)
        else if (startDateString && durationDays) {
            const endDate = calculateEndDateExcludingWeekendsAndHolidays(startDateString, durationDays);
            if (elements.endDateEl) {
                elements.endDateEl.value = endDate;
                elements.endDateEl.readOnly = true; // Otomatis
            }
            if (elements.durationEl) {
                elements.durationEl.value = durationDays; // Otomatis terisi 3
            }
        } else {
            // Jenis Cuti Lain TANPA durasi hari/bulan tetap (misal: izin)
            if (elements.endDateEl) {
                elements.endDateEl.value = '';
                elements.endDateEl.readOnly = false;
            }
        }

        // --- LOGIKA SUB JENIS CUTI (TETAP DI-MAINTAIN) ---
        if (selectedCutiName.toLowerCase().includes('penting')) {
            elements.subJenisCutiContainer.style.display = 'block';
        } else {
            elements.subJenisCutiContainer.style.display = 'none';
        }

        // Bagian Logika Link Download dan Download Section telah dihapus sepenuhnya dari sini

        // Update Durasi Maksimal & Sisa Cuti
        if (elements.durationDisplayEl) {
            elements.durationDisplayEl.value = selectedCutiData.durasi_hari ? `${selectedCutiData.durasi_hari} Hari` : (selectedCutiData.durasi_bulan ? `${selectedCutiData.durasi_bulan} Bulan` : '');
        }

        // Logika sisa cuti tahunan
        if (selectedCutiName === 'Cuti Tahunan' && elements.remainingCutiEl) {
            const daysProposed = parseInt(elements.durationEl?.value || '0', 10);
            elements.remainingCutiEl.value = daysProposed > 0 ? (JATAH_CUTI_TAHUNAN_MAKSIMAL - daysProposed) : '';
        } else if (elements.remainingCutiEl) {
            elements.remainingCutiEl.value = '';
        }
    };


    /**
     * Reset Formulir secara manual
     */
    const handleManualRefresh = (event) => {
        event.preventDefault();
        location.reload(); // Cara termudah untuk reset total
    };

    // --- 6. PENDAFTARAN EVENT LISTENERS UTAMA ---

    // Event listeners Form Cuti:
    elements.jenisCutiEl?.addEventListener('change', updateCutiFields);
    elements.subJenisCutiEl?.addEventListener('change', updateCutiFields);
    elements.startDateEl?.addEventListener('change', calculateDuration);
    elements.startDateEl?.addEventListener('change', updateCutiFields);
    elements.endDateEl?.addEventListener('change', calculateDuration);
    elements.btnRefresh?.addEventListener('click', handleManualRefresh);

    // Listener untuk tombol refresh, input file, dan tutup modal PDF telah dihapus

    // TAMBAHKAN EVENT LISTENERS UNTUK TOMBOL TUTUP DI SINI:
    if (elements.btnCloseError) {
        elements.btnCloseError.addEventListener('click', hideCustomErrorPopup);
    }
    if (elements.btnCloseWarning) {
        elements.btnCloseWarning.addEventListener('click', hideCustomWarningPopup);
    }

    // Inisialisasi UI saat pertama kali dimuat
    updateCutiFields();

    // Fungsi clearFileSelection() telah dihapus karena area status PDF ditiadakan

    fetchTakenDates(); // Tumpang tindih waktu tanggal mulai cuti
}

// Jalankan fungsi inisialisasi saat DOM Content dimuat
document.addEventListener('DOMContentLoaded', initCutiHandler);

