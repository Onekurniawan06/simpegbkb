// resources/js/approval-handler.js
import Swal from 'sweetalert2';

window.handleApproval = function(status) {
    const isApprove = status === 'disetujui';
    const form = document.getElementById('formApproval');

    const buttons = document.querySelectorAll('.btn-approval');
    buttons.forEach(btn => {
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
    });

    Swal.fire({
        title: 'Konfirmasi Approval Pengajuan',
        html: `
            <div class="text-left border-t border-gray-200 pt-5 mt-2">
                <p class="text-blue-700 font-bold text-sm mb-3">
                    Apakah Anda yakin ingin mengonfirmasi data pengajuan ini?
                </p>
                <p class="text-gray-500 text-[13px] leading-relaxed">
                    Pastikan data yang akan anda konfirmasi saat ini sudah sesuai dengan syarat dan ketentuan pengajuan kantor anda.
                </p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Ya, Yakin',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        buttonsStyling: false,
        width: '550px',
        customClass: {
            popup: 'rounded-xl shadow-xl',
            // Perubahan di sini: text-center, font-semibold (bukan bold), dan warna abu-abu
            title: 'text-center !text-[20px] font-semibold px-6 text-gray-600 mt-4',
            htmlContainer: 'px-10 pb-8 m-0',
            actions: 'flex justify-center gap-4 px-6 pb-10 w-full', // Tombol juga jadi di tengah
            confirmButton: 'bg-[#1d4ed8] hover:bg-blue-800 text-white px-10 py-2.5 rounded-lg text-sm font-semibold transition-colors order-2',
            cancelButton: 'bg-white border border-blue-700 text-blue-700 hover:bg-blue-50 px-10 py-2.5 rounded-lg text-sm font-semibold transition-colors order-1'
        },
        showLoaderOnConfirm: true,
        didClose: () => {
            // Jika user klik "Batal", aktifkan kembali tombolnya
            buttons.forEach(btn => {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            });
        },
        preConfirm: async () => {
            try {
                // 1. Sembunyikan tombol Batal secara manual agar hanya spinner yang terlihat
                const cancelButton = Swal.getCancelButton();
                if (cancelButton) cancelButton.style.display = 'none';

                // 2. Tampilkan Loading Spinner
                Swal.showLoading();

                const formData = new FormData(form);
                formData.append('status', status);

                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                });

                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Gagal memproses data');

                return result;
            } catch (error) {
                // Tampilkan kembali tombol Batal jika terjadi error agar user bisa mencoba lagi
                buttons.forEach(btn => {
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                });

                const cancelButton = Swal.getCancelButton();
                if (cancelButton) cancelButton.style.display = 'inline-block';
                Swal.showValidationMessage(`Gagal: ${error.message}`);
            }
        },
        allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
        if (result.isConfirmed) {
            // Tentukan properti berdasarkan status
            const isApprove = status === 'disetujui';
            const statusColor = isApprove ? 'text-emerald-500' : 'text-red-500';
            const iconColor = isApprove ? '#10b981' : '#ef4444'; // Emerald vs Red
            const iconType = isApprove ? 'success' : 'error';

            Swal.fire({
                icon: iconType,
                iconColor: iconColor,
                html: `
                    <div class="text-center px-4">
                        <h2 class="text-base font-extrabold text-gray-900 leading-tight mb-3">
                            Pengajuan Cuti & Izin Pegawai berhasil dikonfirmasi!!!
                        </h2>
                        <p class="text-gray-500 text-[12px] leading-relaxed mb-8">
                            Data pengajuan Cuti Pegawai yang telah anda <span class="${statusColor} font-bold">${status}</span> akan diteruskan ke pihak selanjutnya untuk mendapatkan persetujuan.
                        </p>
                        <button onclick="window.Swal ? window.Swal.close() : location.reload();"
                                class="w-full bg-[#1d4ed8] hover:bg-blue-800 text-white font-semibold py-3 rounded-lg text-sm transition-colors shadow-sm">
                            Kembali ke Data Pengajuan
                        </button>
                    </div>
                `,
                showConfirmButton: false,
                width: '480px',
                customClass: {
                    popup: 'rounded-2xl p-6',
                    icon: 'mt-2 mb-0 scale-75'
                }
            });
            setTimeout(() => {
                window.location.reload(); // Reload untuk mengunci tombol berdasarkan status terbaru
            }, 2000);
        }
    });
}

