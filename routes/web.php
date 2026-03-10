<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\JsonResponse;
// Import controller bawaan Laravel untuk Password Reset
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChangePasswordController;

// LEVEL AKSES ADMINISTRATOR/HRO
use App\Http\Controllers\Admin\DashboardController;
// END LEVEL AKSES ADMINISTRATOR/HRO

// LEVEL AKSES KEPALA SKKMR
use App\Http\Controllers\KepalaSKKMR\SkkmrDashboard;
use App\Http\Controllers\KepalaSKKMR\CreatePengajuanSkkmr;
// END LEVEL AKSES KEPALA SKKMR

// LEVEL AKSES MANAGER
use App\Http\Controllers\Manager\ManagerDashboard;
use App\Http\Controllers\Manager\CreatePengajuan;
use App\Http\Controllers\Manager\ManagerApproval;
use App\Http\Controllers\Laporan\laporanpengajuan;
// END LEVEL AKSES MANAGER

// START LEVEL AKSES PEGAWAI
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\ProfileController;
use App\Models\Pegawai;
use App\Http\Controllers\Cuti\CutiController;
use App\Http\Controllers\Lembur\PengajuanLemburController;
use App\Http\Controllers\Pensiun\PengajuanPensiunController;
use App\Http\Controllers\KenaikanPangkatGajiTunjangan\KenaikanPangkatgajitunjangan;
use App\Http\Controllers\Api\CutiDivisiController;
use App\Http\Controllers\DataPengajuanController;
use App\Models\FilePersyaratanpangkatgajitunjangan;
use App\Models\FilePersyaratanPensiun;
// END LEVEL AKSES PEGAWAI

// Rute Halaman Utama (Login)
Route::get('/', function () {
    return view('login');
})->name('login');

// Rute Otentikasi
Route::post('/login', [AuthController::class, 'login']);

// Rute Registrasi
Route::get('/register', [AuthController::class, 'showRegistrationForm']);
Route::post('/register', [AuthController::class, 'register']);

// Rute Logout
Route::get('/logout', [AuthController::class, 'logout']);

// RUTE LUPA PASSWORD BARU DIMULAI DI SINI
// Menggunakan middleware 'guest' agar hanya bisa diakses saat belum login

// 1. Menampilkan form "Lupa Password" (view auth.forgot-password yang dibuat sebelumnya)
Route::get('/forgot-password', function () {
    return view('forgot-password');
})->middleware('guest')->name('password.request');

// 2. Menangani pengiriman email reset password
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->middleware('guest')->name('password.email');

// 3. Menampilkan form "Reset Password" setelah user mengklik link di email
// Anda perlu membuat view 'auth.reset-password'
Route::get('/reset-password/{token}', function ($token) {
    return view('reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

// 4. Menangani proses reset password (update password baru ke database)
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->middleware('guest')->name('password.update');

Route::middleware(['auth'])->group(function () {
    // Rute untuk menampilkan form ubah password
    Route::get('/change-password', [ChangePasswordController::class, 'showChangePasswordForm'])->name('password.change.form');

    // Rute untuk memproses pengiriman form ubah password (POST request)
    Route::post('/change-password', [ChangePasswordController::class, 'changePassword'])->name('password.change');

});

Route::middleware('auth')->group(function () {
    // Satu route untuk semua role
    Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile.showProfile');
});

// Route::middleware('auth:sanctum')->get('/cuti-divisi-dates', [CutiDivisiController::class, 'index']);

// Route::middleware('auth:sanctum')->get('/cuti-divisi-dates', [CutiDivisiController::class, 'index']);
Route::get('/api/cuti-divisi-dates', [CutiDivisiController::class, 'index']);

// Pengajuan
Route::middleware(['auth'])->group(function () {

    // CUTI IZIN
    // Route Tampilan Form
    Route::get('cuti.formCutiIzin', [CutiController::class, 'formCutiIzin'])->name('cuti.formCutiIzin');

    // Route Proses Simpan (Pastikan name ini dipakai di <form action="...">)
    Route::post('cuti.updateCutiizin', [CutiController::class, 'updateCutiizin'])->name('cuti.updateCutiizin');

    // Tracking data pengajuan cuti izin
    // Route::get('/datapengajuan/dataPengajuan', [DataPengajuanController::class, 'formDataPengajuan'])->name('datapengajuan.formDataPengajuan');

    // Route untuk level Manager
    // Di routes/web.php
    // Pastikan namanya 'manager.pilihpengajuan' (pakai titik)
    Route::get('manager/pengajuanmanager', [CreatePengajuan::class, 'buatPengajuanManager'])
        ->name('manager.pilihpengajuan');

    // Route untuk level Pegawai (URL lama Anda)
    Route::get('/datapengajuan/dataPengajuan', [DataPengajuanController::class, 'formDataPengajuan'])
        ->name('datapengajuan.formDataPengajuan');

    // Tracking Pengajuan Cuti Izin
    Route::get('/datapengajuan/lacakpengajuan-cuti/{nip}', [CutiController::class, 'statuscuti'])->name('datapengajuan.statuscuti');

    // LEMBUR
    // Route Tampilan Form
    // Route::get('/pegawai/formLembur/{type?}', [PengajuanLemburController::class, 'formlembur'])->name('pegawai.formLembur');
    Route::get('lembur.formLembur', [PengajuanLemburController::class, 'formLembur'])->name('lembur.formLembur');

    // Route Proses Simpan (Pastikan name ini dipakai di <form action="...">)
    Route::put('lembur.updateLembur', [PengajuanLemburController::class, 'updateLembur'])->name('lembur.updateLembur');

    // Tracking Pengajuan Lembur
    Route::get('/datapengajuan/lacakpengajuan-lembur/{nip}', [PengajuanLemburController::class, 'statuslembur'])->name('datapengajuan.statuslembur');

    // PENSIUN
    // Route Tampilan Form
    Route::get('pensiun.formPensiun', [PengajuanPensiunController::class, 'formPensiun'])->name('pensiun.formPensiun');

    // Route Proses Simpan (Pastikan name ini dipakai di <form action="...">)
    Route::put('pensiun.updatePensiun', [PengajuanPensiunController::class, 'updatePensiun'])->name('pensiun.updatePensiun');

    // Kenaikan Pangkat/Gaji/Tunjangan
    // Route Tampilan Form
    Route::get('kenaikanpangkatgajitunjangan.formPangkatGajiTunjangan', [KenaikanPangkatgajitunjangan::class, 'formPangkatGajiTunjangan'])->name('kenaikanpangkatgajitunjangan.pangkatgajitunjangan'); // <-- Ubah namanya di sini

    // Rute yang mengarah ke controller baru di dalam sub-folder Pegawai
    Route::put('kenaikanpangkatgajitunjangan.updatePangkatGajiTunjangan', [KenaikanPangkatgajitunjangan::class, 'updatePangkatGajiTunjangan'])->name('kenaikanpangkatgajitunjangan.updatePangkatGajiTunjangan');

});

// ==== START LEVEL ADMIN/HRO ====
// Rute Dashboard Administrator
Route::get('/admin/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'no_back_button']);

// Rute Berita
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

// ==== END LEVEL ADMIN/HRO ====

// ==== START LEVEL KEPALA SKKMR ====
// Rute Dashboard SKKMR
Route::get('/skkmr/dashboardskkmr', [SkkmrDashboard::class, 'FormDashboarSkkmr'])
    ->middleware(['auth', 'no_back_button']);

// Rute Berita
Route::get('/skkmr/dashboardskkmr', [SkkmrDashboard::class, 'FormDashboarSkkmr'])->name('skkmr.dashboardskkmr');

Route::get('skkmr/pengajuanskkmr', [CreatePengajuanSkkmr::class, 'buatPengajuanSkkmr'])
        ->name('skkmr.pilihpengajuan');

// ==== END LEVEL SKKMR ====

// ==== START LEVEL MANAGER UMUM ====
Route::middleware(['auth'])->group(function () {
    // Rute Dashboard Manager
    // Route::get('/manager/dashboardmanager', [ManagerDashboard::class, 'index'])
    //     ->middleware(['no_back_button'])
    //     ->name('manager.dashboardmanager');

    // // Rute Pengajuan (Diri Sendiri)
    // Route::get('/manager/pengajuanmanager', [CreatePengajuan::class, 'buatPengajuanManager'])
    //     ->name('manager.pilihpengajuan');

    Route::get('/manager/dashboard/{divisi}', [ManagerDashboard::class, 'index'])->name('manager.dashboardmanager');
    // Route::get('/manager/kredit', [ManagerDashboard::class, 'index'])->name('manager.dashboardkredit');
    // Route::get('/manager/dana', [ManagerDashboard::class, 'index'])->name('manager.dashboarddana');

    // Route::get('/manager/pegawai-divisi', [ManagerDashboard::class, 'dataPegawaiDivisi'])
    // ->name('manager.pegawaidivisi');

    // Route Daftar Pegawai Global
    Route::get('/manager/pegawai', [ManagerDashboard::class, 'dataPegawaiGlobal'])
        ->name('manager.pegawaidivisi');

    // Route Detail Pegawai
    Route::get('/manager/pegawai/detail/{nup}', [ManagerDashboard::class, 'detailPegawai'])
        ->name('manager.pegawai.detail');


    // Rute Manajemen Approval (Daftar Pengajuan Pegawai)
    Route::get('/manager/manajemenpengajuanmanager', [ManagerApproval::class, 'formManagementPersetujuan'])
        ->name('manager.manajemenpengajuan');

    // --- Rute Tambahan Detail & Update Approval ---

    // Menampilkan Detail Pengajuan (Cuti/Lembur)
    Route::get('/manager/manajemenpengajuan/detail/{sumber}/{id}', [ManagerApproval::class, 'detailApproval'])
        ->name('manager.detailApproval');

    // Memproses Simpan Persetujuan (Approve/Reject)
    Route::put('/manager/manajemenpengajuan/update/{sumber}/{id}', [ManagerApproval::class, 'updateStatus'])
        ->name('manager.updateStatus');

    // Route untuk menampilkan halaman laporan
    Route::get('/laporan-pengajuan', [laporanpengajuan::class, 'formLaporanPersetujuan'])
        ->name('laporan.index');

    // Route untuk cetak PDF (sesuai request sebelumnya)
    Route::get('/laporan-pengajuan/cetak', [laporanpengajuan::class, 'cetakPDF'])
        ->name('laporan.cetak');

});
// ==== END LEVEL MANAGER ====


// 1. Rute untuk Menampilkan Halaman Profil (GET)
// Route::get('/pegawai/profile', [ProfileController::class, 'showProfile'])
//     ->middleware(['auth'])
//     ->name('pegawai.profile.view');

    // Rute untuk update photoprofile
// Route::middleware(['auth'])->group(function () {
//     // Menggunakan patch untuk update sebagian data (foto_selfie)
//     // Kami merekomendasikan penamaan URL yang juga mencerminkan struktur rutenya
//     Route::patch('/pegawai/profile/update-photo', [ProfileController::class, 'updatePhoto'])
//         ->name('pegawai.profile.updatePhoto');
// });

// ==== RUTE PROFIL (Dapat diakses Manager & Pegawai) ====
Route::middleware(['auth'])->group(function () {

    // Rute utama menampilkan profil
    Route::get('/profile', [ProfileController::class, 'showProfile'])
        ->name('profile.edit'); // Nama rute universal

    // Rute proses update data (PUT)
    Route::put('/profile/detail', [ProfileController::class, 'updateProfile'])
        ->name('profile.update');

    Route::put('/profile/update-keluarga', [ProfileController::class, 'updateKeluarga'])
        ->name('profile.update-keluarga');

    Route::put('/profile/update-pekerjaan', [ProfileController::class, 'updatePekerjaan'])
        ->name('profile.update-pekerjaan');

    Route::put('/profile/update-reward', [ProfileController::class, 'updateReward'])
        ->name('profile.update-reward');

    Route::put('/profile/update-punishment', [ProfileController::class, 'updatePunishment'])
        ->name('profile.update-punishment');

    Route::patch('/profile/update-photo', [ProfileController::class, 'updatePhoto'])
    ->name('profile.update-photo');
});

// Rute Berita
Route::get('/pegawai/dashboard', [BeritaController::class, 'index'])->name('pegawai.dashboard');

// Route untuk pengecekan AJAX
Route::get('/cek-pegawai/{nomor}', function ($nomor) {
    try {
        // 1. Cek Tabel Users (Pastikan kolom nomor_urut_pegawai ada di tabel users)
        $sudahDaftar = \DB::table('users')->where('nomor_urut_pegawai', $nomor)->exists();
        if ($sudahDaftar) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor urut ini sudah terdaftar. Silakan login.'
            ]);
        }

        // 2. Ambil data gabungan (Pegawai + Pekerjaan + Divisi)
        // Saya asumsikan tabel utamanya adalah 'pegawai'
        $data = \DB::table('pegawai')
            ->leftJoin('pekerjaan', 'pegawai.nomor_urut_pegawai', '=', 'pekerjaan.nomor_urut_pegawai')
            ->leftJoin('divisi', 'pekerjaan.id_divisi', '=', 'divisi.id_divisi')
            ->where('pegawai.nomor_urut_pegawai', $nomor)
            ->select(
                'pegawai.nama',
                'pekerjaan.jabatan',
                'divisi.nama_divisi'
            )
            ->first();

        if ($data) {
            return response()->json([
                'success' => true,
                'nama'    => $data->nama,
                'jabatan' => $data->jabatan ?? '-', // Kasih default jika null
                'divisi'  => $data->nama_divisi ?? '-'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Data tidak ditemukan di database pegawai.']);

    } catch (\Exception $e) {
        // Log error agar Anda bisa cek di storage/logs/laravel.log
        \Log::error("Error Cek Pegawai: " . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan internal: ' . $e->getMessage()
        ], 500);
    }
});


// Pastikan rute ini dilindungi oleh middleware 'auth' Form cuti Izin
Route::middleware(['auth'])->group(function () {
    // Rute untuk menampilkan formulir (GET method)
    // Route::get('/pegawai/formCutiIzin', [CutiController::class, 'formCutiIzin'])->name('pegawai.formCutiIzin');

    // Mengubah URI menjadi spesifik untuk Cuti Izin
    // Route::post('/pegawai/cuti-izin', [CutiController::class, 'updateCutiizin'])->name('pegawai.updateCutiizin');

    Route::get('/api/get-cuti-details', [CutiController::class, 'getCutiDetails'])->name('api.get.cuti.details');

    // // Tracking Pengajuan Cuti Izin
    // Route::get('/pegawai/lacakpengajuan-cuti/{nip}', [CutiController::class, 'statuscuti'])->name('pengajuan.statuscuti');

    // Rute spesifik untuk Cuti
    Route::get('/pengajuan-cuti/{nup}/detail-surat', [DataPengajuanController::class, 'getLetterDetailsByNup']);

    // Route::get('/pegawai/surat-cuti/{id}/download-pdf', [DataPengajuanController::class, 'downloadPdf'])->name('pegawai.downloadPdf');
    // Rute baru untuk mendownload surat dalam format PDF berdasarkan NUP
    // Pastikan name('pengajuan.download') sesuai dengan yang dipanggil di view
    Route::get('/download-surat-cuti/{nup}', [DataPengajuanController::class, 'downloadLetterPdf'])->name('pengajuan.download');
});

// Dalam grup middleware 'auth' (contoh penempatan)
Route::middleware(['auth'])->group(function () {
    // Rute GET untuk menampilkan form lembur
    // Route::get('/pegawai/formLembur/{type?}', [PengajuanLemburController::class, 'formlembur'])->name('pegawai.formLembur');

    // Mengubah URI menjadi spesifik untuk Lembur
    // Route::put('/pegawai/lembur', [PengajuanLemburController::class, 'updateLembur'])->name('pegawai.updateLembur');

    // Tracking Pengajuan Lembur
    Route::get('/pegawai/lacakpengajuan-lembur/{nip}', [PengajuanLemburController::class, 'statuslembur'])->name('pengajuan.statuslembur');

    // Rute spesifik Surat untuk Lembur
    Route::get('/pengajuan-lembur/{nup}/detail-surat', [DataPengajuanController::class, 'getLemburLetterDetailsByNup']);

    Route::get('/download-surat-lembur/{nup}', [DataPengajuanController::class, 'downloadLetterPdfLembur'])->name('pengajuan.download.lembur');
});

// Dalam grup middleware 'auth' (contoh penempatan)
Route::middleware(['auth'])->group(function () {
    // Rute GET untuk menampilkan form pensiun
    // Route::get('/pegawai/formPensiun', [PengajuanPensiunController::class, 'formPensiun'])->name('pegawai.formPensiun');

    // Mengubah URI menjadi spesifik untuk Pensiun
    // Route::put('/pegawai/pensiun', [PengajuanPensiunController::class, 'updatePensiun'])->name('pegawai.updatePensiun');

    // Rute untuk Pensiun (URL diubah)
    Route::middleware(['auth'])->group(function () {
    Route::get('/view-document-pensiun/{id}', [PengajuanPensiunController::class, 'lihatDokumen'])->name('view.document.pensiun'); // Nama rute unik

    // Tracking Pengajuan Pensiun
    // Route::get('/pegawai/lacakpengajuan-pensiun/{nip}', [PengajuanPensiunController::class, 'statuspensiun'])->name('pengajuan.statuspensiun');
    Route::get('/pegawai/lacakpengajuan-pensiun/{nip}', [PengajuanPensiunController::class, 'statuspensiun'])
      ->name('datapengajuan.statuspensiun'); // Tambahkan 'datapengajuan.' di sini

    // Rute spesifik Surat untuk Pensiun
    Route::get('/pengajuan-pensiun/{nup}/detail-surat', [DataPengajuanController::class, 'getPensiunLetterDetailsByNup']);
    Route::get('/download-surat-pensiun/{nup}', [DataPengajuanController::class, 'downloadLetterPdfPensiun'])->name('pengajuan.download.pensiun');
});

// Route::middleware(['auth'])->group(function () {
//     Route::get('/pegawai/dataPengajuan', [DataPengajuanController::class, 'formDataPengajuan'])->name('pegawai.formDataPengajuan');
// });

Route::middleware(['auth'])->group(function () {
    // Rute GET untuk menampilkan form lembur
    Route::get('/pegawai/formPangkatGajiTunjangan', [KenaikanPangkatgajitunjangan::class, 'formPangkatGajiTunjangan'])->name('pegawai.pangkatgajitunjangan'); // <-- Ubah namanya di sini

    // Rute yang mengarah ke controller baru di dalam sub-folder Pegawai
    Route::post('/pegawai/kenaikanpangkatgajitunjangan', [KenaikanPangkatgajitunjangan::class, 'updatePangkatGajiTunjangan'])->name('pegawai.updatePangkatGajiTunjangan');

    // Tracking Pengajuan Pensiun
    // Route::get('/pegawai/lacakpengajuan-pangkatgajitunjangan/{nip}', [KenaikanPangkatgajitunjangan::class, 'statuspangkatgajitunjangan'])->name('pengajuan.statuspangkatgajitunjangan');
    Route::get('/pegawai/lacakpengajuan-pangkatgajitunjangan/{nip}', [KenaikanPangkatgajitunjangan::class, 'statuspangkatgajitunjangan'])
      ->name('datapengajuan.statuspangkatgajitunjangan'); // Ubah nama di sini agar sinkron

    Route::get('/pegawai/lihat-dokumen-pangkat/{id}', [KenaikanPangkatgajitunjangan::class, 'lihatDokumen'])->name('pegawai.lihat-dokumen-pangkat');

    // Rute spesifik Surat untuk Pensiun
    Route::get('/pengajuan-pangkat/{nup}/detail-surat', [DataPengajuanController::class, 'getPangkatgajitunjanganLetterDetailsByNup']);
    Route::get('/download-surat-pangkat/{nup}', [DataPengajuanController::class, 'downloadLetterPdfPangkatGajiTunjangan'])->name('pengajuan.download.pangkat');

});

// Rute untuk Pangkat/Gaji/Tunjangan (biarkan seperti ini)
Route::middleware(['auth'])->group(function () {
    Route::get('/view-document/{fileId}', function ($fileId) {
        $doc = FilePersyaratanpangkatgajitunjangan::findOrFail($fileId);
        $full_path = storage_path('app/private/') . $doc->path_file_server;
        if (!file_exists($full_path)) {
            abort(404, 'Dokumen tidak ditemukan di lokasi: ' . $full_path);
        }
        return response()->file($full_path);
    })->name('view.document'); // Konflik nama rute tetap ada di sini
});

// ==== END LEVEL PEGAWAI ====

});
