<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ManagerApproval extends Controller
{
    public function formManagementPersetujuan(Request $request)
    {
        $user = auth()->user();
        $slugDivisi = \Illuminate\Support\Str::slug($user->divisi->nama_divisi ?? 'dashboard');

        // 1. Ambil Dashboard Route secara Dinamis dari Mapping (Sama seperti AuthController)
        $mapping = \DB::table('roles_mapping')
            ->where('level_id', $user->level_id)
            ->where(function($q) use ($user) {
                $q->where('jabatan_id', $user->jabatan_id)->orWhereNull('jabatan_id');
            })
            ->where(function($q) use ($user) {
                $q->where('id_divisi', $user->id_divisi)->orWhereNull('id_divisi');
            })
            ->orderBy('priority', 'asc')
            ->first();

        $dashboardRoute = $mapping->route_name ?? 'dashboard';

        // 2. Inisialisasi Statistik
        $totalMenunggu = 0; $totalDisetujui = 0; $totalDitolak = 0;
        $detailMenunggu = []; $detailDisetujui = []; $detailDitolak = [];

        // 3. Ambil data pengajuan hanya untuk DIVISI yang sama dengan user yang login
        $idDivisiManager = auth()->user()->id_divisi;
        $myLevel = auth()->user()->level_id;

        $tables = [
            ['name' => 'log_persetujuan_lembur', 'col' => 'status_persetujuan', 'label' => 'Lembur'],
            ['name' => 'log_persetujuan_cuti', 'col' => 'status_pengajuan', 'label' => 'Cuti'],
        ];

        foreach ($tables as $table) {
            $baseQuery = \DB::table($table['name'])
                ->join('users', $table['name'] . '.nomor_urut_pegawai', '=', 'users.nomor_urut_pegawai')
                ->join('pekerjaan', 'users.nomor_urut_pegawai', '=', 'pekerjaan.nomor_urut_pegawai')
                // FILTER KUNCI: Samakan divisi & pastikan hanya level bawahan (bukan manager)
                ->where('pekerjaan.id_divisi', $idDivisiManager)
                ->where('users.level_id', '!=', $myLevel);

            $countWait = (clone $baseQuery)->where($table['col'], 'diproses')->count();

            $countApprove = (clone $baseQuery)->where($table['col'], 'disetujui')
                ->where('tahap_persetujuan', '!=', 'Pengajuan Awal')->count();

            $countReject = (clone $baseQuery)->where($table['col'], 'ditolak')
                ->where('tahap_persetujuan', '!=', 'Pengajuan Awal')->count();

            $totalMenunggu += $countWait;
            $totalDisetujui += $countApprove;
            $totalDitolak += $countReject;

            $detailMenunggu[] = ['label' => $table['label'], 'jumlah' => $countWait];
            $detailDisetujui[] = ['label' => $table['label'], 'jumlah' => $countApprove];
            $detailDitolak[] = ['label' => $table['label'], 'jumlah' => $countReject];
        }

        $listDivisi = \DB::table('divisi')->get();

    // 1. QUERY UNTUK CUTI
    $queryCuti = \DB::table('pengajuan_cuti as pc')
        ->join('log_persetujuan_cuti as lpc', 'pc.nomor_urut_pegawai', '=', 'lpc.nomor_urut_pegawai')
        ->join('pegawai as p', 'pc.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
        // Kita join ke tabel pekerjaan untuk cek divisi pegawai
        ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
        ->join('divisi as d', 'pek.id_divisi', '=', 'd.id_divisi')
        ->join('users as u', 'p.nomor_urut_pegawai', '=', 'u.nomor_urut_pegawai')
        // KUNCI UTAMA: Hanya ambil pegawai yang divisinya SAMA dengan Manager & Levelnya BUKAN Manager
        ->where('pek.id_divisi', '=', $idDivisiManager)
        ->where('u.level_id', '!=', $myLevel)
        ->whereIn('lpc.tahap_persetujuan', ['Manager', 'manager', 'Pengajuan Awal'])
        ->where('lpc.status_pengajuan', 'diproses')
        ->when($request->search, function($q) use ($request) {
            $q->where(function($sub) use ($request) {
                $sub->where('p.nama', 'like', '%' . $request->search . '%')
                    ->orWhere('p.nomor_urut_pegawai', 'like', '%' . $request->search . '%');
            });
        })
        ->when($request->jenis && $request->jenis !== 'Cuti', function($q) {
            $q->whereRaw('1 = 0');
        })
        ->when($request->start_date && $request->end_date, function($q) use ($request) {
            $q->whereBetween(\DB::raw('DATE(pc.created_at)'), [$request->start_date, $request->end_date]);
        })
        ->select(
            'lpc.id as id_transaksi', 'pc.created_at as tanggal', 'pc.nomor_urut_pegawai as nup',
            'p.nama', 'd.nama_divisi', 'pek.jabatan', 'pc.Jenis_cuti as jenis',
            'lpc.status_pengajuan as status', \DB::raw("'cuti' as sumber")
        );

    // 2. QUERY UNTUK LEMBUR
    $dataPengajuan = \DB::table('pengajuan_lembur as pl')
        ->join('log_persetujuan_lembur as lpl', 'pl.nomor_urut_pegawai', '=', 'lpl.nomor_urut_pegawai')
        ->join('pegawai as p2', 'pl.nomor_urut_pegawai', '=', 'p2.nomor_urut_pegawai')
        // Gunakan join yang sama persis untuk filter divisi
        ->join('pekerjaan as pek2', 'p2.nomor_urut_pegawai', '=', 'pek2.nomor_urut_pegawai')
        ->join('divisi as d2', 'pek2.id_divisi', '=', 'd2.id_divisi')
        ->join('users as u2', 'p2.nomor_urut_pegawai', '=', 'u2.nomor_urut_pegawai')
        // KUNCI UTAMA: Filter divisi dan level di sini juga
        ->where('pek2.id_divisi', '=', $idDivisiManager)
        ->where('u2.level_id', '!=', $myLevel)
        ->whereIn('lpl.tahap_persetujuan', ['Manager', 'manager', 'Pengajuan Awal'])
        ->where('lpl.status_persetujuan', 'diproses')
        ->when($request->search, function($q) use ($request) {
            $q->where(function($sub) use ($request) {
                $sub->where('p2.nama', 'like', '%' . $request->search . '%')
                    ->orWhere('p2.nomor_urut_pegawai', 'like', '%' . $request->search . '%');
            });
        })
        ->when($request->jenis && $request->jenis !== 'Lembur', function($q) {
            $q->whereRaw('1 = 0');
        })
        ->when($request->start_date && $request->end_date, function($q) use ($request) {
            $q->whereBetween(\DB::raw('DATE(pl.created_at)'), [$request->start_date, $request->end_date]);
        })
        ->select(
            'lpl.id as id_transaksi', 'pl.created_at as tanggal', 'pl.nomor_urut_pegawai as nup',
            'p2.nama', 'd2.nama_divisi', 'pek2.jabatan', \DB::raw("'Lembur' as jenis"),
            'lpl.status_persetujuan as status', \DB::raw("'lembur' as sumber")
        )
        ->union($queryCuti)
        ->orderBy('tanggal', 'desc')
        ->paginate(10)
        ->withQueryString();

        $namaDivisiReal = \DB::table('divisi')
            ->where('id_divisi', $idDivisiManager)
            ->value('nama_divisi') ?? 'Divisi';

        $pageTitle = 'Manajemen Pengajuan';
        $breadcrumbs = [
            'Beranda' => ($dashboardRoute === 'manager.dashboardmanager')
                        ? route($dashboardRoute, ['divisi' => $slugDivisi])
                        : route($dashboardRoute),

            // Menggunakan variabel dinamis $namaDivisiReal
            "Manajemen Pengajuan ↦ Approval Pengajuan Pegawai Divisi $namaDivisiReal" => '#',
        ];

        return view('manager.manajemenpengajuanpegawai', compact(
            'pageTitle', 'breadcrumbs', 'totalMenunggu', 'totalDisetujui',
            'totalDitolak', 'detailMenunggu', 'detailDisetujui', 'detailDitolak',
            'dataPengajuan', 'listDivisi', 'dashboardRoute'
        ));
    }

    public function detailApproval($sumber, $id_log)
    {
        $user = auth()->user();
        $idDivisiManager = $user->id_divisi;

        $divisiData = \DB::table('divisi')->where('id_divisi', $idDivisiManager)->first();
        $slugDivisi = \Illuminate\Support\Str::slug($divisiData->nama_divisi ?? 'dashboard');
        $namaDivisiReal = $divisiData->nama_divisi ?? 'Divisi';

         // 1. Ambil Dashboard Route secara Dinamis
        $mapping = \DB::table('roles_mapping')
            ->where('level_id', $user->level_id)
            ->where(function($q) use ($user) {
                $q->where('jabatan_id', $user->jabatan_id)->orWhereNull('jabatan_id');
            })
            ->where(function($q) use ($user) {
                $q->where('id_divisi', $user->id_divisi)->orWhereNull('id_divisi');
            })
            ->orderBy('priority', 'asc')
            ->first();

        $dashboardRoute = $mapping->route_name ?? 'dashboard';

        // 2. Query Detail dengan Filter Divisi
        if ($sumber === 'cuti') {
            $data = DB::table('pengajuan_cuti as pc')
                ->join('pegawai as p', 'pc.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->join('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
                ->join('log_persetujuan_cuti as lpc', 'pc.nomor_urut_pegawai', '=', 'lpc.nomor_urut_pegawai')
                // KUNCI KEAMANAN: Cek apakah divisi pegawai sama dengan divisi manager yang login
                ->where('lpc.id', $id_log)
                ->where('pek.id_divisi', $idDivisiManager)
                ->select(
                    'p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi',
                    'pc.Jenis_cuti', 'pc.tanggal_mulai', 'pc.tanggal_selesai', 'pc.keterangan',
                    'lpc.status_pengajuan as status', 'lpc.updated_at as tanggal_proses'
                )
                ->first();
        } else {
            $data = DB::table('pengajuan_lembur as pl')
                ->join('pegawai as p', 'pl.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->join('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
                ->join('log_persetujuan_lembur as lpl', 'pl.nomor_urut_pegawai', '=', 'lpl.nomor_urut_pegawai')
                // KUNCI KEAMANAN: Cek apakah divisi pegawai sama dengan divisi manager yang login
                ->where('lpl.id', $id_log)
                ->where('pek.id_divisi', $idDivisiManager)
                ->select(
                    'p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi',
                    'pl.uraian_tugas', 'lpl.status_persetujuan as status', 'lpl.updated_at as tanggal_proses'
                )
                ->first();
        }

        if (!$data) {
            return redirect()->back()->with('error', 'Data tidak ditemukan atau Anda tidak memiliki akses ke divisi ini.');
        }

        $pageTitle = 'Detail Pengajuan ' . ucfirst($sumber);

        // FIX BREADCRUMBS: Tambahkan parameter ['divisi' => $slugDivisi]
        $breadcrumbs = [
            'Beranda' => ($dashboardRoute === 'manager.dashboardmanager')
                        ? route($dashboardRoute, ['divisi' => $slugDivisi])
                        : route($dashboardRoute),

            "Manajemen Pengajuan ↦ Approval Pengajuan Pegawai Divisi $namaDivisiReal" => route('manager.manajemenpengajuan'),
            'Detail Pengajuan ' . ucfirst($sumber) => '#',
        ];

        return view('manager.detail_approval', compact('data', 'sumber', 'pageTitle', 'id_log', 'breadcrumbs', 'dashboardRoute'));
    }

    public function updateStatus(Request $request, $sumber, $id_log)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string'
        ]);

        $user = auth()->user();
        $idDivisiManager = $user->id_divisi; // Ambil divisi manager yang login

        $tabel = ($sumber === 'cuti') ? 'log_persetujuan_cuti' : 'log_persetujuan_lembur';
        $kolomStatus = ($sumber === 'cuti') ? 'status_pengajuan' : 'status_persetujuan';

        // 1. Ambil data asli & VALIDASI DIVISI (Keamanan)
        $logLama = \DB::table($tabel)
            ->join('pekerjaan', $tabel . '.nomor_urut_pegawai', '=', 'pekerjaan.nomor_urut_pegawai')
            ->where($tabel . '.id', $id_log)
            ->where('pekerjaan.id_divisi', $idDivisiManager) // Filter divisi
            ->select($tabel . '.*')
            ->first();

        if (!$logLama) return redirect()->back()->with('error', 'Data tidak ditemukan atau akses ditolak.');

        // 2. UPDATE baris "Pengajuan Awal"
        \DB::table($tabel)->where('id', $id_log)->update([
            $kolomStatus => $request->status,
            // KUNCI: Gunakan DB::raw agar MySQL TIDAK mengubah waktu ke detik ini
            'updated_at' => \DB::raw('updated_at'),
        ]);

        // 3. LOGIKA UNTUK BARIS BARU (INSERT)
        $dataInsert = [
            'nomor_urut_pegawai' => $logLama->nomor_urut_pegawai,
            'tahap_persetujuan' => 'Manager',
            'nomor_urut_pegawai_penyetuju' => $user->nomor_urut_pegawai,
            $kolomStatus => $request->status,
            'updated_at' => now(), // Log Manager tetap pakai waktu sekarang
        ];

        if ($request->status === 'disetujui') {
            $flow = ($sumber === 'cuti')
                ? ['Pengajuan Awal', 'Manager', 'Direktur Operasional', 'HRO']
                : ['Pengajuan Awal', 'Manager', 'Kepala SKK MR', 'HRO'];

            $currentIndex = array_search($logLama->tahap_persetujuan, $flow);
            $nextTahap = ($currentIndex !== false && isset($flow[$currentIndex + 1])) ? $flow[$currentIndex + 1] : 'Selesai';

            $dataInsert['komentar'] = $request->catatan ?? 'Menunggu verifikasi ' . $nextTahap;
        } else {
            $dataInsert['komentar'] = 'Ditolak: ' . ($request->catatan ?? 'Tidak ada alasan spesifik');
        }

        if ($sumber === 'lembur' && isset($logLama->lembur_id)) {
            $dataInsert['lembur_id'] = $logLama->lembur_id;
        }

        // EKSEKUSI INSERT
        \DB::table($tabel)->insert($dataInsert);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('manager.manajemenpengajuan')->with('success', 'Data berhasil diproses');
    }

}

