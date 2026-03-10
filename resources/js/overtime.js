document.addEventListener('DOMContentLoaded', () => {

    // --- BAGIAN 1: LOGIKA PERHITUNGAN JAM LEMBUR ---
    const startInput = document.getElementById('jam_mulai');
    const endInput = document.getElementById('jam_selesai');
    const totalInput = document.getElementById('total_jam');

    if (startInput && endInput && totalInput) {

        const formatMinutesToText = (totalMinutes) => {
            if (totalMinutes < 0) return "Durasi tidak valid";
            const hours = Math.floor(totalMinutes / 60);
            const minutes = Math.floor(totalMinutes % 60);
            return `${hours} jam ${minutes} menit`;
        };

        const calculateDuration = () => {
            const startTime = startInput.value;
            const endTime = endInput.value;

            if (startTime && endTime) {
                const [startH, startM] = startTime.split(':').map(Number);
                const [endH, endM] = endTime.split(':').map(Number);
                let startTotalMinutes = (startH * 60) + startM;
                let endTotalMinutes = (endH * 60) + endM;

                if (endTotalMinutes < startTotalMinutes) {
                    endTotalMinutes += 24 * 60;
                }

                const diffMinutes = endTotalMinutes - startTotalMinutes;
                const formattedText = formatMinutesToText(diffMinutes);
                totalInput.value = formattedText;
            }
        };

        startInput.addEventListener('input', calculateDuration);
        endInput.addEventListener('input', calculateDuration);
    }
    // --- AKHIR BAGIAN 1 ---


    // --- BAGIAN 2: LOGIKA MANAJEMEN FILE DAN MODAL ---
    const fileInput = document.getElementById('file_cuti');
    const pdfStatusArea = document.getElementById('pdf-status-area');
    const emptyInfo = document.getElementById('empty-info');
    const pdfModal = document.getElementById('pdfModal');
    const pdfIframe = document.getElementById('pdfIframe');
    const closePdfModalBtn = document.getElementById('btnClosePdfModal');
    const pdfNameTitle = document.getElementById('pdfNameTitle');
    const downloadFormLink = document.getElementById('btn_download_formulir');
    const formTypeLabel = document.getElementById('label_jenis_cuti');
    const BASE_FORM_URL = "/forms/form-lembur.docx";
    const currentFormType = "LEMBUR";

    if (fileInput) { // Pastikan elemen file ada sebelum melanjutkan

        const updateDownloadLink = () => {
            formTypeLabel.textContent = currentFormType;
            // Gunakan BASE_FORM_URL secara langsung tanpa tambahan apa pun
            downloadFormLink.href = BASE_FORM_URL;
        };

        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0]; // Ambil file pertama

            if (file && file.type === "application/pdf") {
                pdfStatusArea.innerHTML = `
                    <div style="padding: 10px; background: #ecfdf5; border: 1px solid #10b981; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 13px; color: #065f46;">✅ File dipilih: ${file.name}</span>
                        <button type="button" id="btn_preview_pdf" style="background: #3b82f6; color: white; border: none; border-radius: 4px; padding: 4px 10px; cursor: pointer; font-size: 11px;">Pratinjau</button>
                    </div>
                `;

                document.getElementById('btn_preview_pdf').addEventListener('click', () => {
                    const fileURL = URL.createObjectURL(file);
                    pdfIframe.src = fileURL;
                    pdfNameTitle.textContent = `Pratinjau Dokumen: ${file.name}`;
                    pdfModal.style.display = 'flex';
                });

            } else {
                pdfStatusArea.innerHTML = '';
                pdfStatusArea.appendChild(emptyInfo);
                if (file) {
                   // alert("Mohon unggah file dalam format PDF."); // Opsional: Tampilkan alert
                }
            }
        });

        closePdfModalBtn.addEventListener('click', () => {
            pdfModal.style.display = 'none';
            pdfIframe.src = '';
        });

        updateDownloadLink();
    }
    // --- AKHIR BAGIAN 2 ---

});
