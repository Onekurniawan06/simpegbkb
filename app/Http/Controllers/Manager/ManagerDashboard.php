<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Berita;

class ManagerDashboard extends Controller
{
    public function index()
{
    $user = auth()->user();

    // 1. Deteksi Jabatan & Normalisasi (Agar fleksibel Manager/Manajer)
    $role = DB::table('roles_mapping')
        ->where('jabatan_id', $user->jabatan_id)
        ->where('level_id', $user->level_id)
        ->first();

    $jabatanAsli = $role->role_name ?? 'Manager';
    // Kita buat keyword pencarian yang mencakup Manager dan Manajer
    $isManajerSpelling = str_contains(strtolower($jabatanAsli), 'manajer');
    $keyword = $isManajerSpelling ? 'Manajer' : 'Manager';

    // 2. Ambil ID Divisi (Tetap pakai cara akuratmu)
    $pekerjaanManager = DB::table('pekerjaan')->where('nomor_urut_pegawai', $user->nomor_urut_pegawai)->first();
    $idDivisi = $pekerjaanManager->id_divisi ?? null;

    // 3. Ambil Daftar NUP Bawahan
    $nupBawahan = DB::table('pekerjaan')
        ->where('id_divisi', $idDivisi)
        ->where('nomor_urut_pegawai', '!=', $user->nomor_urut_pegawai)
        ->distinct()
        ->pluck('nomor_urut_pegawai')
        ->toArray();

    $totalMenunggu = 0; $totalDisetujui = 0; $totalDitolak = 0;

    if (!empty($nupBawahan)) {
        // --- A. PERHITUNGAN CUTI ---
        // Menunggu: Cari yang tahapannya Manager/Manajer ATAU Pengajuan Awal dari Level 1
        $totalMenunggu += DB::table('log_persetujuan_cuti as log')
            ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->whereIn('log.nomor_urut_pegawai', $nupBawahan)
            ->where(function($q) use ($keyword) {
                $q->where('tahap_persetujuan', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('tahap_persetujuan', 'Pengajuan Awal');
            })
            ->where('status_pengajuan', 'diproses')
            ->whereNull('nomor_urut_pegawai_penyetuju')
            ->count();

        // Hasil Akhir (Tabel Utama)
        $totalDisetujui += DB::table('pengajuan_cuti')->whereIn('nomor_urut_pegawai', $nupBawahan)->where('status_pengajuan', 'disetujui')->count();
        $totalDitolak += DB::table('pengajuan_cuti')->whereIn('nomor_urut_pegawai', $nupBawahan)->where('status_pengajuan', 'ditolak')->count();

        // --- B. PERHITUNGAN LEMBUR ---
        $totalMenunggu += DB::table('log_persetujuan_lembur as log')
            ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->whereIn('log.nomor_urut_pegawai', $nupBawahan)
            ->where(function($q) use ($keyword) {
                $q->where('tahap_persetujuan', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('tahap_persetujuan', 'Pengajuan Awal');
            })
            ->where('status_persetujuan', 'diproses')
            ->whereNull('nomor_urut_pegawai_penyetuju')
            ->count();

        // Hasil Akhir (Tabel Utama)
        $totalDisetujui += DB::table('pengajuan_lembur')->whereIn('nomor_urut_pegawai', $nupBawahan)->where('status_lembur', 'disetujui')->count();
        $totalDitolak += DB::table('pengajuan_lembur')->whereIn('nomor_urut_pegawai', $nupBawahan)->where('status_lembur', 'ditolak')->count();
    }

    // 4. Berita
    $daftar_berita = Berita::where('tanggal_posting', '>=', now()->subHours(72))->orderBy('tanggal_posting', 'desc')->paginate(5);
    $total_belum_dibaca = Berita::where('tanggal_posting', '>=', now()->subDay())->count();

    return view('manager.dashboardmanager', compact('totalMenunggu', 'totalDisetujui', 'totalDitolak', 'daftar_berita', 'total_belum_dibaca'));
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
