<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function dataPegawaiGlobal(Request $request)
{
    $user = auth()->user();
    $search = $request->input('search');
    $divisiFilter = $request->input('divisi');

    // 1. Ambil semua divisi untuk dropdown
    $allDivisi = DB::table('divisi')->orderBy('nama_divisi', 'asc')->get();

    // 2. Ambil data role mapping untuk cek otoritas rute
    $roleMapping = DB::table('roles_mapping')
        ->where('jabatan_id', $user->jabatan_id)
        ->where('level_id', $user->level_id)
        ->first();

    // --- KUNCI LOGIKA: Bedakan antara "Manager Divisi" dan "Kepala Satker/Lainnya" ---
    // Kita cek apakah rute dashboard-nya mengandung kata 'manager'
    $isRestrictedManager = $roleMapping && str_contains($roleMapping->route_name, 'manager');

    $dashboardRoute = $roleMapping->route_name ?? 'pegawai.dashboard';

    // 3. QUERY UTAMA
    $query = DB::table('pegawai')
        ->leftJoin('pekerjaan', 'pegawai.nomor_urut_pegawai', '=', 'pekerjaan.nomor_urut_pegawai')
        ->leftJoin('divisi', 'pekerjaan.id_divisi', '=', 'divisi.id_divisi');

    // 4. LOGIKA FILTER AKSES
    if ($isRestrictedManager) {
        // Jika dia MANAGER (Kredit/Umum/dll): Kunci ke divisinya sendiri
        $query->where('pekerjaan.id_divisi', $user->id_divisi);
    } else {
        // Jika dia KEPALA SATKER KEPATUHAN / DIREKTUR: Bisa lintas divisi
        if ($divisiFilter) {
            $query->where('pekerjaan.id_divisi', $divisiFilter);
        }
    }

    // 5. FILTER PENCARIAN (Global)
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('pegawai.nama', 'like', "%{$search}%")
              ->orWhere('pegawai.nomor_urut_pegawai', 'like', "%{$search}%");
        });
    }

    // 6. QUERY STATISTIK (Samakan filternya agar angka SINKRON)
    $statsQuery = DB::table('pekerjaan')
        ->join('pegawai', 'pekerjaan.nomor_urut_pegawai', '=', 'pegawai.nomor_urut_pegawai');

    if ($isRestrictedManager) {
        $statsQuery->where('pekerjaan.id_divisi', $user->id_divisi);
    } elseif ($divisiFilter) {
        $statsQuery->where('pekerjaan.id_divisi', $divisiFilter);
    }

    $stats = $statsQuery->select('status_pegawai', DB::raw('count(*) as total'))
        ->groupBy('status_pegawai')->pluck('total', 'status_pegawai');

    // 7. EKSEKUSI FINAL
    $pegawaiDivisi = $query->select('pegawai.*', 'pekerjaan.*', 'divisi.nama_divisi')
        ->orderByRaw("CASE WHEN pegawai.nomor_urut_pegawai REGEXP '^[0-9]+$' THEN 1 ELSE 2 END ASC")
        ->orderByRaw('CAST(pegawai.nomor_urut_pegawai AS UNSIGNED) ASC')
        ->paginate(20)
        ->withQueryString();

    $pageTitle = 'Data Pegawai';
    $breadcrumbs = [
        'Beranda' => route($dashboardRoute, ['divisi' => $user->id_divisi]),
        'Data Pegawai' => null,
    ];
    $layout = $user->layout_file;

    return view('pegawai.datapegawai', compact('pegawaiDivisi', 'stats', 'allDivisi', 'pageTitle', 'breadcrumbs', 'layout'));
}




    public function detailPegawai($nup)
    {
        $user = auth()->user();

        // 1. Ambil data pegawai (Gunakan Left Join agar tidak mental/rollback)
        $pegawai = DB::table('pegawai')
            ->leftJoin('pekerjaan', 'pegawai.nomor_urut_pegawai', '=', 'pekerjaan.nomor_urut_pegawai')
            ->leftJoin('detail_pribadi', 'pegawai.nomor_urut_pegawai', '=', 'detail_pribadi.nomor_urut_pegawai')
            ->leftJoin('divisi', 'pekerjaan.id_divisi', '=', 'divisi.id_divisi')
            ->where('pegawai.nomor_urut_pegawai', $nup)
            ->select('pegawai.*', 'pekerjaan.*', 'detail_pribadi.*', 'divisi.nama_divisi')
            ->first();

        // 2. Jika data benar-benar tidak ada di tabel induk (pegawai)
        if (!$pegawai) {
            return redirect()->route('pegawai.data')->with('error', 'Data pegawai tidak ditemukan.');
        }

        // 3. Logika Dashboard Route untuk Breadcrumbs (Opsional tapi bagus agar link Beranda aktif)
        $roleMapping = DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        $isManagerOrKepala = $roleMapping && str_contains($roleMapping->route_name, 'manager');
        $dashboardRoute = $isManagerOrKepala ? ($roleMapping->route_name ?? 'manager.dashboardmanagerumum') : 'pegawai.dashboard';

        // 4. Pengaturan View
        $pageTitle = 'Detail Pegawai';
        $breadcrumbs = [
            'Beranda' => route($dashboardRoute, ['divisi' => $user->id_divisi]),
            'Data Pegawai' => route('pegawai.data'),
            $pegawai->nama => null,
        ];

        $layout = $user->layout_file;

        return view('pegawai.pegawaidetail', compact('pegawai', 'pageTitle', 'breadcrumbs', 'layout'));
    }

}
