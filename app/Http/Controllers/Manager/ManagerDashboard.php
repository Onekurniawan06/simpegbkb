<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Berita;

class ManagerDashboard extends Controller
{
    public function index($divisi = null)
    {
        $user = auth()->user(); // Bisa Manajer atau Kepala yang sedang login

        // 1. Logika Keamanan: Cek slug divisi agar tidak nyasar ke dashboard divisi lain
        $slugUser = strtolower(str_replace(' ', '-', $user->divisi->nama_divisi));
        if ($divisi && $divisi !== $slugUser) {
            return redirect()->route('manager.dashboardmanager', ['divisi' => $slugUser]);
        }

        $totalMenunggu = 0;
        $totalDisetujui = 0;
        $totalDitolak = 0;

        $tables = [
            ['name' => 'log_persetujuan_lembur', 'col' => 'status_persetujuan'],
            ['name' => 'log_persetujuan_cuti', 'col' => 'status_pengajuan'],
        ];

        foreach ($tables as $table) {
            // QUERY UTAMA: Join log ke tabel users
            $baseQuery = DB::table($table['name'])
                ->join('users', $table['name'] . '.nomor_urut_pegawai', '=', 'users.nomor_urut_pegawai')
                // Filter Divisi: Mengambil pegawai di divisi yang sama dengan user login (Manager/Kepala)
                ->where('users.id_divisi', $user->id_divisi)
                // Filter Level: HANYA menghitung level_id = 1 (Pegawai)
                // Ini otomatis mengabaikan user dengan level 2 (Manajer/Kepala)
                ->where('users.level_id', 1);

            // A. HITUNG MENUNGGU: Semua status 'diproses' (termasuk tahap Pengajuan Awal)
            $totalMenunggu += (clone $baseQuery)
                ->where($table['col'], 'diproses')
                ->count();

            // B. HITUNG DISETUJUI: Kecuali tahap 'Pengajuan Awal'
            $totalDisetujui += (clone $baseQuery)
                ->where($table['col'], 'disetujui')
                ->where($table['name'] . '.tahap_persetujuan', '!=', 'Pengajuan Awal')
                ->count();

            // C. HITUNG DITOLAK: Kecuali tahap 'Pengajuan Awal'
            $totalDitolak += (clone $baseQuery)
                ->where($table['col'], 'ditolak')
                ->where($table['name'] . '.tahap_persetujuan', '!=', 'Pengajuan Awal')
                ->count();
        }

        // Ambil data tambahan (Berita)
        $daftar_berita = Berita::where('tanggal_posting', '>=', now()->subHours(72))
            ->orderBy('tanggal_posting', 'desc')
            ->paginate(5);

        $total_belum_dibaca = Berita::where('tanggal_posting', '>=', now()->subDay())->count();

        return view('manager.dashboardmanager', compact(
            'totalMenunggu', 'totalDisetujui', 'totalDitolak',
            'daftar_berita', 'total_belum_dibaca'
        ));
    }

    public function dataPegawaiGlobal(Request $request)
    {
        $user = auth()->user();

        // 1. Logika Role Mapping Dinamis
        $roleMapping = \DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        $isManagerOrKepala = $roleMapping && str_contains($roleMapping->route_name, 'manager');
        $dashboardRoute = $isManagerOrKepala ? ($roleMapping->route_name ?? 'manager.dashboardmanagerumum') : 'pegawai.dashboard';

        // 2. Query Utama
        $query = \DB::table('pegawai')
            ->leftJoin('pekerjaan', 'pegawai.nomor_urut_pegawai', '=', 'pekerjaan.nomor_urut_pegawai')
            ->leftJoin('divisi', 'pekerjaan.id_divisi', '=', 'divisi.id_divisi');

        // 3. FILTER DINAMIS
        if ($isManagerOrKepala) {
            $query->where('pekerjaan.id_divisi', $user->id_divisi)
                ->where('pegawai.level_id', 1);
        }

        // 4. Statistik
        $statsQuery = \DB::table('pekerjaan');
        if ($isManagerOrKepala) {
            $statsQuery->where('id_divisi', $user->id_divisi);
        }
        $stats = $statsQuery->select('status_pegawai', \DB::raw('count(*) as total'))
            ->groupBy('status_pegawai')->pluck('total', 'status_pegawai');

        // 5. Breadcrumbs & Page Title
        $pageTitle = 'Data Pegawai';

        // PERBAIKAN: Tambahkan parameter divisi di route Beranda
        $breadcrumbs = [
            'Beranda' => route($dashboardRoute, ['divisi' => $user->id_divisi]),
            'Data Pegawai' => null,
        ];

        $pegawaiDivisi = $query->select('pegawai.*', 'pekerjaan.*', 'divisi.nama_divisi')
            ->orderBy('pegawai.nama', 'asc')
            ->paginate(18);

        // 6. Tambahkan variabel layout agar view tidak error
        $layout = $user->layout_file;

        return view('manager.pegawaidivisi', compact('pegawaiDivisi', 'stats', 'pageTitle', 'breadcrumbs', 'layout'));
    }


    // 2. Fungsi Detail Pegawai Global
    public function detailPegawai($nup)
    {
        $user = auth()->user();

        $roleMapping = \DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        $isManagerOrKepala = $roleMapping && str_contains($roleMapping->route_name, 'manager');

        // Tentukan rute dashboard secara dinamis
        $dashboardRoute = $isManagerOrKepala ? ($roleMapping->route_name ?? 'manager.dashboardmanagerumum') : 'pegawai.dashboard';

        $query = \DB::table('pegawai')
            ->join('pekerjaan', 'pegawai.nomor_urut_pegawai', '=', 'pekerjaan.nomor_urut_pegawai')
            ->join('detail_pribadi', 'pegawai.nomor_urut_pegawai', '=', 'detail_pribadi.nomor_urut_pegawai')
            ->join('divisi', 'pekerjaan.id_divisi', '=', 'divisi.id_divisi')
            ->where('pegawai.nomor_urut_pegawai', $nup);

        if ($isManagerOrKepala) {
            $query->where('pekerjaan.id_divisi', $user->id_divisi);
        }

        $pegawai = $query->select('pegawai.*', 'pekerjaan.*', 'detail_pribadi.*', 'divisi.nama_divisi')->first();


        if (!$pegawai) {
            return redirect()->route('manager.pegawaidivisi')->with('error', 'Akses ditolak.');
        }

        $pageTitle = 'Detail Pegawai';

        $breadcrumbs = [
            // Pastikan parameter divisi disertakan agar tidak error lagi
            'Beranda' => route($dashboardRoute, ['divisi' => $user->id_divisi]),
            'Data Pegawai' => route('manager.pegawaidivisi'),
            $pegawai->nama => null,
        ];

        // 8. Tentukan Layout (Ambil dari logika kode referensi Anda)
        $layout = $user->layout_file;

        return view('manager.pegawaidetail', compact('pegawai', 'pageTitle', 'breadcrumbs', 'layout'));
    }


}
