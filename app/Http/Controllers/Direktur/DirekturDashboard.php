<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Berita;

class DirekturDashboard extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Identifikasi Role (Tetap menggunakan logika mapping kamu yang sudah sangat spesifik)
        $role = DB::table('roles_mapping')
            ->where('level_id', $user->level_id)
            ->where('jabatan_id', $user->jabatan_id)
            ->first();

        $jabatanAsli = $role ? $role->role_name : (DB::table('jabatan')->where('jabatan_id', $user->jabatan_id)->value('nama_jabatan') ?? 'Direktur');

        // 2. NORMALISASI Nama Jabatan (Sangat Penting untuk Query Tabel Log)
        if (stripos($jabatanAsli, 'utama') !== false) {
            $jabatanLogin = 'Direktur Utama';
        } elseif (stripos($jabatanAsli, 'operasional') !== false) {
            $jabatanLogin = 'Direktur Operasional';
        } elseif (stripos($jabatanAsli, 'kepatuhan') !== false) {
            $jabatanLogin = 'Direktur Kepatuhan';
        } else {
            $jabatanLogin = $jabatanAsli;
        }

        // 3. Konfigurasi Tabel (Lengkap 4 Jenis)
        $config = [
            ['log' => 'log_persetujuan_cuti',    'st_log' => 'status_pengajuan'],
            ['log' => 'log_persetujuan_lembur',  'st_log' => 'status_persetujuan'],
            ['log' => 'log_persetujuan_pensiun', 'st_log' => 'status_persetujuan'],
            ['log' => 'log_persetujuan_pangkatgajitunjangan', 'st_log' => 'status_persetujuan'],
        ];

        $totalMenunggu = 0;
        $totalDisetujui = 0;
        $totalDitolak = 0;

        foreach ($config as $cfg) {
            // --- A. HITUNG MENUNGGU (Inbox Meja Direktur Saat Ini) ---
            $totalMenunggu += DB::table($cfg['log'])
                ->where('tahap_persetujuan', 'LIKE', '%' . $jabatanLogin . '%')
                ->where($cfg['st_log'], 'diproses')
                ->whereNull('nomor_urut_pegawai_penyetuju')
                ->where('nomor_urut_pegawai', '!=', $user->nomor_urut_pegawai)
                ->count();

            // --- B. HITUNG DISETUJUI (Kinerja Pribadi Direktur Ini) ---
            $totalDisetujui += DB::table($cfg['log'])
                ->where('nomor_urut_pegawai_penyetuju', $user->nomor_urut_pegawai)
                ->where($cfg['st_log'], 'disetujui')
                ->count();

            // --- C. HITUNG DITOLAK (Kinerja Pribadi Direktur Ini) ---
            $totalDitolak += DB::table($cfg['log'])
                ->where('nomor_urut_pegawai_penyetuju', $user->nomor_urut_pegawai)
                ->where($cfg['st_log'], 'ditolak')
                ->count();
        }

        // 4. Data Berita (Gunakan paginate agar tidak error di Blade)
        $daftar_berita = Berita::where('tanggal_posting', '>=', now()->subHours(72))
            ->orderBy('tanggal_posting', 'desc')
            ->paginate(5);

        $total_belum_dibaca = Berita::where('tanggal_posting', '>=', now()->subDay())->count();

        return view('direktur.dashboarddirektur', compact(
            'totalMenunggu', 'totalDisetujui', 'totalDitolak',
            'daftar_berita', 'total_belum_dibaca', 'jabatanLogin'
        ));
    }
}
