<?php

namespace App\Http\Controllers\Hro;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Berita;

class HroDashboard extends Controller
{
    public function dashboardhro()
    {
        $user = auth()->user();

        // 1. Konfigurasi 4 Tabel (Gunakan kolom status di TABEL UTAMA)
        $configs = [
            ['main' => 'pengajuan_cuti',   'st' => 'status_pengajuan', 'log' => 'log_persetujuan_cuti',   'st_log' => 'status_pengajuan'],
            ['main' => 'pengajuan_lembur', 'st' => 'status_lembur',    'log' => 'log_persetujuan_lembur', 'st_log' => 'status_persetujuan'],
            ['main' => 'pengajuan_pensiun', 'st' => 'status_pensiun',   'log' => 'log_persetujuan_pensiun', 'st_log' => 'status_persetujuan'],
            ['main' => 'pengajuan_pangkatgajitunjangan', 'st' => 'status_kenaikan', 'log' => 'log_persetujuan_pangkatgajitunjangan', 'st_log' => 'status_persetujuan'],
        ];

        $totalMenunggu = 0;
        $totalDisetujui = 0;
        $totalDitolak = 0;

        foreach ($configs as $cfg) {
            // --- A. HITUNG MENUNGGU (WAJIB CEK TABEL LOG) ---
            $totalMenunggu += DB::table($cfg['log'])
                ->where('tahap_persetujuan', 'HRO')
                ->where($cfg['st_log'], 'diproses')
                ->whereNull('nomor_urut_pegawai_penyetuju')
                ->count();

            // --- B. HITUNG DISETUJUI (WAJIB CEK TABEL UTAMA) ---
            $totalDisetujui += DB::table($cfg['main'])
                ->where($cfg['st'], 'disetujui')
                ->count();

            // --- C. HITUNG DITOLAK (WAJIB CEK TABEL UTAMA) ---
            $totalDitolak += DB::table($cfg['main'])
                ->where($cfg['st'], 'ditolak')
                ->count();
        }

        // 2. Berita & Informasi
        $daftar_berita = Berita::orderBy('tanggal_posting', 'desc')->paginate(5);
        $total_belum_dibaca = Berita::where('tanggal_posting', '>=', now()->subDay())->count();

        return view('hro.dashboardhro', compact(
            'totalMenunggu', 'totalDisetujui', 'totalDitolak',
            'daftar_berita', 'total_belum_dibaca'
        ));
    }


}
