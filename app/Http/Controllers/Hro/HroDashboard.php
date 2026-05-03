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

        // 1. Identifikasi Role menggunakan mapping (Tanpa level_akses)
        $role = DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        $jabatanLogin = $role->role_name ?? 'HRO';

        // 2. Inisialisasi Counter
        $totalMenunggu = 0;
        $totalDisetujui = 0;
        $totalDitolak = 0;

        // 3. Daftar Tabel yang dipantau HRO (Bisa ditambah sesuai kebutuhan)
        $tables = [
            ['name' => 'log_persetujuan_lembur', 'col' => 'status_persetujuan'],
            ['name' => 'log_persetujuan_cuti', 'col' => 'status_pengajuan'],
            ['name' => 'log_persetujuan_pensiun', 'col' => 'status_persetujuan'],
            ['name' => 'log_persetujuan_pangkatgajitunjangan', 'col' => 'status_persetujuan'],
        ];

        foreach ($tables as $table) {
            // Query Dasar: HRO melihat SEMUA DIVISI (Tidak pakai where id_divisi)
            $baseQuery = DB::table($table['name'])
                ->join('users', $table['name'] . '.nomor_urut_pegawai', '=', 'users.nomor_urut_pegawai');

            // A. HITUNG MENUNGGU (Antrean khusus untuk HRO)
            $totalMenunggu += (clone $baseQuery)
                ->where($table['col'], 'diproses')
                ->where($table['name'] . '.tahap_persetujuan', 'HRO')
                ->count();

            // B. HITUNG DISETUJUI (Yang sudah diselesaikan oleh HRO)
            $totalDisetujui += (clone $baseQuery)
                ->where($table['col'], 'disetujui')
                ->where($table['name'] . '.tahap_persetujuan', 'HRO')
                ->count();

            // C. HITUNG DITOLAK (Yang ditolak oleh HRO)
            $totalDitolak += (clone $baseQuery)
                ->where($table['col'], 'ditolak')
                ->where($table['name'] . '.tahap_persetujuan', 'HRO')
                ->count();
        }

        // 4. Data Berita/Pengumuman (Tetap dipertahankan)
        $daftar_berita = Berita::where('tanggal_posting', '>=', now()->subHours(72))
            ->orderBy('tanggal_posting', 'desc')
            ->paginate(5);

        $total_belum_dibaca = Berita::where('tanggal_posting', '>=', now()->subDay())->count();

        // 5. Return ke View khusus HRO
        return view('hro.dashboardhro', compact(
            'totalMenunggu', 'totalDisetujui', 'totalDitolak',
            'daftar_berita', 'total_belum_dibaca', 'jabatanLogin'
        ));
    }

}
