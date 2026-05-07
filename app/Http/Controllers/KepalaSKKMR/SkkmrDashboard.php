<?php

namespace App\Http\Controllers\KepalaSKKMR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Berita;
use Illuminate\Support\Facades\DB;

class SkkmrDashboard extends Controller
{
    public function FormDashboarSkkmr()
{
    $user = auth()->user();

    // 1. NORMALISASI JABATAN (Gunakan LIKE yang lebih fleksibel agar kena semua variasi nama)
    // Kita cari yang mengandung kata 'SKK' atau 'Kepatuhan'
    $jabatanKeywords = ['SKK', 'Kepatuhan'];

    // 2. Konfigurasi Tabel
    $config = [
        ['label' => 'Lembur',  'main' => 'pengajuan_lembur', 'log' => 'log_persetujuan_lembur', 'pk' => 'id_lembur',  'st_main' => 'status_lembur',    'st_log' => 'status_persetujuan'],
        ['label' => 'Pensiun', 'main' => 'pengajuan_pensiun', 'log' => 'log_persetujuan_pensiun', 'pk' => 'id_pensiun', 'st_main' => 'status_pensiun',   'st_log' => 'status_persetujuan'],
        ['label' => 'Pangkat', 'main' => 'pengajuan_pangkatgajitunjangan', 'log' => 'log_persetujuan_pangkatgajitunjangan', 'pk' => 'id_kenaikan', 'st_main' => 'status_kenaikan', 'st_log' => 'status_persetujuan'],
    ];

    $totalMenunggu = 0; $totalDisetujui = 0; $totalDitolak = 0;
    $detailMenunggu = []; $detailDisetujui = []; $detailDitolak = [];

    foreach ($config as $cfg) {
        // --- A. HITUNG MENUNGGU (Antrean Meja SKKMR) ---
        $countWait = DB::table($cfg['log'] . ' as log')
            ->join('users as u', 'log.nomor_urut_pegawai', '=', 'u.nomor_urut_pegawai')
            ->where(function($query) {
                $query->where(function($q) {
                    // 1. Ambil yang ada kata SKK atau Kepatuhan
                    $q->where('tahap_persetujuan', 'LIKE', '%SKK%')
                    ->orWhere('tahap_persetujuan', 'LIKE', '%Kepatuhan%');
                })
                ->where('tahap_persetujuan', 'NOT LIKE', '%Direktur%')

                // 2. ATAU: Ambil 'Pengajuan Awal' milik Manager (Level 2)
                ->orWhere(function($q) {
                    $q->where('tahap_persetujuan', 'Pengajuan Awal')
                    ->where('u.level_id', 2);
                });
            })
            ->where('log.' . $cfg['st_log'], 'diproses')
            ->whereNull('log.nomor_urut_pegawai_penyetuju')
            ->count();

        // --- B. HITUNG DISETUJUI & DITOLAK (Dari Tabel Utama) ---
        $countApprove = DB::table($cfg['main'])->where($cfg['st_main'], 'disetujui')->count();
        $countReject = DB::table($cfg['main'])->where($cfg['st_main'], 'ditolak')->count();

        // Akumulasi
        $totalMenunggu += $countWait;
        $totalDisetujui += $countApprove;
        $totalDitolak += $countReject;

        $detailMenunggu[]  = ['label' => $cfg['label'], 'jumlah' => $countWait];
        $detailDisetujui[] = ['label' => $cfg['label'], 'jumlah' => $countApprove];
        $detailDitolak[]   = ['label' => $cfg['label'], 'jumlah' => $countReject];
    }

    $daftar_berita = Berita::orderBy('tanggal_posting', 'desc')->paginate(5);
    $total_belum_dibaca = Berita::where('tanggal_posting', '>=', now()->subDay())->count();

    return view('skkmr.dashboardskkmr', compact(
        'totalMenunggu', 'totalDisetujui', 'totalDitolak',
        'detailMenunggu', 'detailDisetujui', 'detailDitolak',
        'daftar_berita', 'total_belum_dibaca'
    ));
}

}
