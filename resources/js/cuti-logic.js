
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

    const SALDO_TERAKHIR_DATABASE = parseInt(dataBridge.dataset.sisaCutiTahunIni || '12', 10);
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

    // --- 3. Seleksi Elemen DOM ---
    const elements = {
        jenisCutiEl: document.getElementById('jenis_cuti'),
        durationEl: document.getElementById('jumlah_cuti'),
        remainingCutiEl: document.getElementById('sisa_cuti'),
        durationDisplayEl: document.getElementById('jatah_periode_hari'),
        saldoAwalEl: document.getElementById('saldo_awal'),
        startDateEl: document.getElementById('tanggal_mulai'),
        endDateEl: document.getElementById('tanggal_selesai'),
        subJenisCutiContainer: document.getElementById('sub_jenis_cuti_container'),
        subJenisCutiEl: document.getElementById('sub_jenis_cuti'),
        tnCloseWarningPopup: document.getElementById('close-warning-popup'),
        btnCloseError: document.getElementById('btn-close-error'),
        btnCloseWarning: document.getElementById('btn-close-warning'),
        btnRefresh: document.getElementById('btn_refresh_form'),
    };

    let takenDates = [];

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
            const currentDateFormatted = d.toISOString().split('T')[0];

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
            const jenisForm = (dataBridge.dataset.jenisPengajuan || 'cuti').toLowerCase();

            // Cek apakah ini form lembur
            const isLembur = (jenisForm === 'lembur');
            const workingDaysTaken = calculateWorkingDaysBetweenDates(
                elements.startDateEl.value,
                elements.endDateEl.value,
                isLembur
            );

            const hasOverlap = checkForOverlap(elements.startDateEl.value, elements.endDateEl.value);
            if (hasOverlap) {
                showCustomErrorPopup('PERINGATAN: Tanggal yang Anda pilih tumpang tindih dengan cuti rekan satu divisi!');
                elements.endDateEl.value = '';
                elements.durationEl.value = 0;
                return;
            }

            if (workingDaysTaken > 0) {
                const selectedCutiName = elements.jenisCutiEl?.value;
                if (selectedCutiName === 'Cuti Tahunan' && !isLembur) {
                    const saldoAwalSekarang = parseInt(document.getElementById('saldo_awal').value) || 0;

                    if (workingDaysTaken > saldoAwalSekarang) {
                        showCustomErrorPopup(`PERINGATAN: Sisa cuti Anda tinggal ${saldoAwalSekarang} hari. Anda mencoba mengambil ${workingDaysTaken} hari.`);
                        elements.endDateEl.value = '';
                        if (elements.durationEl) elements.durationEl.value = 0;
                        return;
                    }
                }
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
        hideCustomErrorPopup();
        hideCustomWarningPopup();

        const selectedCutiName = elements.jenisCutiEl?.value;
        if (!selectedCutiName) {
            if (elements.saldoAwalEl) elements.saldoAwalEl.value = '';
            return;
        }

        const selectedCutiData = allJenisCuti.find(c => c.nama_cuti === selectedCutiName);
        if (!selectedCutiData) return;

        // --- LOGIKA UNTUK MENGISI KOTAK "SALDO CUTI SAAT INI" ---
        if (elements.saldoAwalEl) {
            if (selectedCutiName === 'Cuti Tahunan') {
                elements.saldoAwalEl.value = dataBridge.dataset.sisaCutiTahunIni || '12';
            } else {
                elements.saldoAwalEl.value = selectedCutiData.durasi_hari || selectedCutiData.durasi_bulan || 0;
            }
        }
        // -------------------------------------------------------

        const startDateString = elements.startDateEl?.value;
        const durationDays = selectedCutiData.durasi_hari;
        const durationMonths = selectedCutiData.durasi_bulan;

        if (selectedCutiName === 'Cuti Tahunan') {
            if (elements.endDateEl) elements.endDateEl.readOnly = false;
        }
        else if (startDateString && (selectedCutiName === 'Cuti Besar' || selectedCutiName === 'Cuti Melahirkan') && durationMonths) {
            const calendarEndDateString = calculateEndDateInMonths(startDateString, durationMonths);
            const start = new Date(startDateString);
            const end = new Date(calendarEndDateString);
            const totalCalendarDays = Math.ceil(Math.abs(end - start) / (1000 * 60 * 60 * 24)) + 1;

            if (elements.endDateEl) {
                elements.endDateEl.value = calendarEndDateString;
                elements.endDateEl.readOnly = true;
            }
            if (elements.durationEl) {
                elements.durationEl.value = totalCalendarDays;
            }
        }
        else if (startDateString && durationDays) {
            const endDate = calculateEndDateExcludingWeekendsAndHolidays(startDateString, durationDays);
            if (elements.endDateEl) {
                elements.endDateEl.value = endDate;
                elements.endDateEl.readOnly = true;
            }
            if (elements.durationEl) {
                elements.durationEl.value = durationDays;
            }
        } else {
            if (elements.endDateEl) {
                if (selectedCutiName !== 'Cuti Tahunan' && !startDateString) {
                    elements.endDateEl.value = '';
                }
                elements.endDateEl.readOnly = false;
            }
        }

        if (selectedCutiName.toLowerCase().includes('penting')) {
            elements.subJenisCutiContainer.style.display = 'block';
        } else {
            elements.subJenisCutiContainer.style.display = 'none';
        }

        if (elements.durationDisplayEl) {
            elements.durationDisplayEl.value = selectedCutiData.durasi_hari ? `${selectedCutiData.durasi_hari} Hari` : (selectedCutiData.durasi_bulan ? `${selectedCutiData.durasi_bulan} Bulan` : '');
        }

        // --- LOGIKA HITUNG SISA CUTI (MENGGUNAKAN SALDO BERJALAN) ---
        if (elements.remainingCutiEl) {
            const saldoAwalSekarang = parseInt(elements.saldoAwalEl?.value) || 0;
            const daysProposed = parseInt(elements.durationEl?.value || '0', 10);

            // Hanya isi kotak "Sisa Cuti Nanti" jika jumlah hari yang diambil > 0
            if (daysProposed > 0) {
                elements.remainingCutiEl.value = saldoAwalSekarang - daysProposed;
            } else {
                // Jika belum pilih tanggal atau durasi masih 0, kosongkan kotaknya
                elements.remainingCutiEl.value = ''; 
            }
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
    if (elements.btnCloseError) {
        elements.btnCloseError.addEventListener('click', hideCustomErrorPopup);
    }
    if (elements.btnCloseWarning) {
        elements.btnCloseWarning.addEventListener('click', hideCustomWarningPopup);
    }

    if (elements.saldoAwalEl) {
        elements.saldoAwalEl.value = '';
    }

    updateCutiFields();
    fetchTakenDates();
}

document.addEventListener('DOMContentLoaded', initCutiHandler);

