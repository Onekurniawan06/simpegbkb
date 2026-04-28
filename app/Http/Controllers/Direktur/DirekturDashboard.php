<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Berita; // Pastikan model Berita di-import

class DirekturDashboard extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Ambil data role yang SANGAT SPESIFIK sesuai level dan jabatan user
        $role = DB::table('roles_mapping')
            ->where('level_id', $user->level_id)
            ->where('jabatan_id', $user->jabatan_id)
            ->first();

        // Jika tidak ketemu di mapping, ambil default dari tabel jabatan
        if (!$role) {
            $dataJabatan = DB::table('jabatan')->where('jabatan_id', $user->jabatan_id)->first();
            $jabatanAsli = $dataJabatan->nama_jabatan ?? 'Direktur';
        } else {
            $jabatanAsli = $role->role_name; // Ini akan mengambil "Direktur Operasional" dari tabel Anda
        }

        // 2. NORMALISASI (Agar sinkron dengan teks di tabel Log)
        if (stripos($jabatanAsli, 'utama') !== false) {
            $jabatanLogin = 'Direktur Utama';
        } elseif (stripos($jabatanAsli, 'operasional') !== false) {
            $jabatanLogin = 'Direktur Operasional';
        } elseif (stripos($jabatanAsli, 'kepatuhan') !== false) {
            $jabatanLogin = 'Direktur Kepatuhan';
        } else {
            $jabatanLogin = $jabatanAsli; // Gunakan nama asli jika tidak cocok dengan 3 di atas
        }

        $totalMenunggu = 0;
        $totalDisetujui = 0;
        $totalDitolak = 0;

        // Daftar tabel log yang dipantau Direktur
        $tables = [
            ['name' => 'log_persetujuan_lembur', 'col' => 'status_persetujuan'],
            ['name' => 'log_persetujuan_cuti', 'col' => 'status_pengajuan'],
            ['name' => 'log_persetujuan_pensiun', 'col' => 'status_persetujuan'],
            ['name' => 'log_persetujuan_pangkatgajitunjangan', 'col' => 'status_persetujuan'],
        ];

        foreach ($tables as $table) {
            // Base Query: Direktur TIDAK PAKAI filter id_divisi karena Lintas Divisi
            $baseQuery = DB::table($table['name'])
                ->join('pegawai', $table['name'] . '.nomor_urut_pegawai', '=', 'pegawai.nomor_urut_pegawai')
                // Pengaman: Direktur tidak menghitung pengajuannya sendiri
                ->where('pegawai.nomor_urut_pegawai', '!=', $user->nomor_urut_pegawai);

            // A. HITUNG MENUNGGU (Hanya yang sedang antri di tahap Direktur ini)
            $totalMenunggu += (clone $baseQuery)
                ->where($table['col'], 'diproses')
                ->where($table['name'] . '.tahap_persetujuan', $jabatanLogin)
                ->count();

            // B. HITUNG DISETUJUI (Hanya yang pernah disetujui oleh Direktur ini)
            $totalDisetujui += (clone $baseQuery)
                ->where($table['col'], 'disetujui')
                ->where($table['name'] . '.tahap_persetujuan', $jabatanLogin)
                ->count();

            // C. HITUNG DITOLAK (Hanya yang pernah ditolak oleh Direktur ini)
            $totalDitolak += (clone $baseQuery)
                ->where($table['col'], 'ditolak')
                ->where($table['name'] . '.tahap_persetujuan', $jabatanLogin)
                ->count();
        }

        // Ambil data berita (Sama seperti Manager)
        $daftar_berita = Berita::where('tanggal_posting', '>=', now()->subHours(72))
            ->orderBy('tanggal_posting', 'desc')
            ->paginate(5);

        $total_belum_dibaca = Berita::where('tanggal_posting', '>=', now()->subDay())->count();

        // Nama View diarahkan ke folder direktur
        return view('direktur.dashboarddirektur', compact(
            'totalMenunggu', 'totalDisetujui', 'totalDitolak',
            'daftar_berita', 'total_belum_dibaca', 'jabatanLogin'
        ));
    }
}
