<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPengajuan extends Controller
{
    public function formLaporanPersetujuan(Request $request)
    {
        $user = auth()->user();

        // 1. Ambil data Role secara dinamis (Mapping Dashboard & Slug Divisi)
        $roleMapping = \DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        // Cek apakah user adalah Manager/Kepala berdasarkan route_name
        $isManagerOrKepala = $roleMapping && str_contains($roleMapping->route_name, 'manager');

        // Ambil Nama Divisi untuk Parameter Route & Breadcrumb
        $divisiData = \DB::table('divisi')->where('id_divisi', $user->id_divisi)->first();
        $namaDivisiReal = $divisiData->nama_divisi ?? 'Divisi';
        $slugDivisi = \Illuminate\Support\Str::slug($namaDivisiReal);

        // Tentukan rute dashboard secara dinamis
        $dashboardRoute = $isManagerOrKepala ? ($roleMapping->route_name ?? 'manager.dashboardmanager') : 'pegawai.dashboard';

        // 2. Inisialisasi Statistik (Hanya untuk Manager)
        $totalDisetujui = 0; $totalDitolak = 0;
        $detailDisetujui = []; $detailDitolak = [];

        if ($isManagerOrKepala) {
            $tables = [
                ['name' => 'log_persetujuan_lembur', 'col' => 'status_persetujuan', 'label' => 'Lembur'],
                ['name' => 'log_persetujuan_cuti', 'col' => 'status_pengajuan', 'label' => 'Cuti'],
            ];

            foreach ($tables as $table) {
                $baseQuery = \DB::table($table['name'])
                    ->join('users', $table['name'] . '.nomor_urut_pegawai', '=', 'users.nomor_urut_pegawai')
                    // Pastikan filter divisi agar manager hanya melihat divisinya sendiri
                    ->join('pekerjaan', 'users.nomor_urut_pegawai', '=', 'pekerjaan.nomor_urut_pegawai')
                    ->where('pekerjaan.id_divisi', $user->id_divisi)
                    ->where('users.level_id', '!=', $user->level_id); // Filter bawahan

                $countApprove = (clone $baseQuery)->where($table['col'], 'disetujui')->where('tahap_persetujuan', '!=', 'Pengajuan Awal')->count();
                $countReject = (clone $baseQuery)->where($table['col'], 'ditolak')->where('tahap_persetujuan', '!=', 'Pengajuan Awal')->count();

                $totalDisetujui += $countApprove;
                $totalDitolak += $countReject;
                $detailDisetujui[] = ['label' => $table['label'], 'jumlah' => $countApprove];
                $detailDitolak[] = ['label' => $table['label'], 'jumlah' => $countReject];
            }
        }

        // $listDivisi = \DB::table('divisi')->get();

        // 1. QUERY UNTUK CUTI
        $queryCuti = \DB::table('pengajuan_cuti as pc')
            ->join('log_persetujuan_cuti as lpc', 'pc.nomor_urut_pegawai', '=', 'lpc.nomor_urut_pegawai')
            ->join('pegawai as p', 'pc.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
            ->join('divisi as d', 'pek.id_divisi', '=', 'd.id_divisi')
            ->join('users as u', 'p.nomor_urut_pegawai', '=', 'u.nomor_urut_pegawai')
            ->where('u.level_akses', 'pegawai')
            ->whereIn('lpc.status_pengajuan', ['disetujui', 'ditolak'])
            ->where('lpc.tahap_persetujuan', '!=', 'Pengajuan Awal') // Filter tahap awal
            ->when($request->search, function($q) use ($request) {
                $q->where(function($sub) use ($request) {
                    $sub->where('p.nama', 'like', '%' . $request->search . '%')
                        ->orWhere('p.nomor_urut_pegawai', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->divisi, function($q) use ($request) {
                $q->where('d.nama_divisi', $request->divisi);
            })
            ->when($request->jenis && $request->jenis !== 'Cuti', function($q) {
                $q->whereRaw('1 = 0');
            })
            ->when($request->start_date && $request->end_date, function($q) use ($request) {
                $q->whereBetween(\DB::raw('DATE(pc.created_at)'), [$request->start_date, $request->end_date]);
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('lpc.status_pengajuan', $request->status);
            })
            ->where('pek.id_divisi', $user->id_divisi)
            ->select(
                'lpc.id as id_transaksi',
                'pc.created_at as tanggal',
                'pc.tanggal_mulai as tgl_awal',      // Alias tgl_awal
                'pc.tanggal_selesai as tgl_akhir',   // Alias tgl_akhir
                \DB::raw('NULL as total_jam_lembur'),
                'pc.nomor_urut_pegawai as nup',
                'p.nama',
                'd.nama_divisi',
                'pek.jabatan',
                'pc.Jenis_cuti as jenis',
                'lpc.status_pengajuan as status',
                \DB::raw("'cuti' as sumber")
            );

        // 2. QUERY UNTUK LEMBUR
        $dataPengajuan = \DB::table('pengajuan_lembur as pl')
            ->join('log_persetujuan_lembur as lpl', 'pl.nomor_urut_pegawai', '=', 'lpl.nomor_urut_pegawai')
            ->join('pegawai as p2', 'pl.nomor_urut_pegawai', '=', 'p2.nomor_urut_pegawai')
            ->join('pekerjaan as pek2', 'p2.nomor_urut_pegawai', '=', 'pek2.nomor_urut_pegawai')
            ->join('divisi as d2', 'pek2.id_divisi', '=', 'd2.id_divisi')
            ->join('users as u2', 'p2.nomor_urut_pegawai', '=', 'u2.nomor_urut_pegawai')
            ->where('u2.level_akses', 'pegawai')
            ->whereIn('lpl.status_persetujuan', ['disetujui', 'ditolak'])
            ->where('lpl.tahap_persetujuan', '!=', 'Pengajuan Awal') // Filter tahap awal
            ->when($request->search, function($q) use ($request) {
                $q->where(function($sub) use ($request) {
                    $sub->where('p2.nama', 'like', '%' . $request->search . '%')
                        ->orWhere('p2.nomor_urut_pegawai', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->divisi, function($q) use ($request) {
                $q->where('d2.nama_divisi', $request->divisi);
            })
            ->when($request->jenis && $request->jenis !== 'Lembur', function($q) {
                $q->whereRaw('1 = 0');
            })
            ->when($request->start_date && $request->end_date, function($q) use ($request) {
                $q->whereBetween(\DB::raw('DATE(pl.created_at)'), [$request->start_date, $request->end_date]);
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('lpl.status_persetujuan', $request->status);
            })
            ->select(
                'lpl.id as id_transaksi',
                'pl.created_at as tanggal',
                'pl.tanggal_lembur as tgl_awal',     // Tanggal lembur jadi tgl_awal
                \DB::raw('NULL as tgl_akhir'),
                'pl.total_jam_lembur', // Mengambil kolom asli dari DB
                'pl.nomor_urut_pegawai as nup',
                'p2.nama',
                'd2.nama_divisi',
                'pek2.jabatan',
                \DB::raw("'Lembur' as jenis"),
                'lpl.status_persetujuan as status',
                \DB::raw("'lembur' as sumber")
            )
            ->where('pek2.id_divisi', $user->id_divisi)
            ->union($queryCuti)
            ->orderBy('tanggal', 'desc')
            ->paginate(10)
            ->withQueryString();

        // 4. Judul dan Breadcrumbs Dinamis
        // $pageTitle = 'Laporan Pengajuan Pegawai';

        // Penentuan Route Beranda dengan Parameter Divisi jika Manager
        $berandaParams = ($dashboardRoute === 'manager.dashboardmanager') ? ['divisi' => $slugDivisi] : [];

        $breadcrumbs = [
            'Beranda' => route($dashboardRoute, $berandaParams),
            "Laporan ↦ Laporan Pengajuan Pegawai Divisi $namaDivisiReal" => '#',
        ];

        // 5. Tentukan Layout secara otomatis (Gunakan layout_file dari Accessor User)
        $layout = $user->layout_file;

        return view('laporan.laporanpengajuan', compact(
            'breadcrumbs', 'totalDisetujui', 'totalDitolak',
            'detailDisetujui', 'detailDitolak', 'dataPengajuan', 'layout', 'isManagerOrKepala'
        ));
    }

    public function cetakPDF(Request $request)
    {
        // 1. Tangkap parameter filter (Gunakan lowercase agar pengecekan aman)
        $jenis_filter = strtolower($request->get('jenis'));
        $status_filter = strtolower($request->get('status'));

        // 2. DEFINISIKAN QUERY CUTI (Jangan pakai ->get() di sini)
        $queryCuti = \DB::table('pengajuan_cuti as pc')
            ->join('log_persetujuan_cuti as lpc', 'pc.nomor_urut_pegawai', '=', 'lpc.nomor_urut_pegawai')
            ->join('pegawai as p', 'pc.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
            ->join('divisi as d', 'pek.id_divisi', '=', 'd.id_divisi')
            ->join('users as u', 'p.nomor_urut_pegawai', '=', 'u.nomor_urut_pegawai')
            ->where('lpc.tahap_persetujuan', '!=', 'Pengajuan Awal')
            ->where('u.level_akses', 'pegawai')
            ->whereIn('lpc.status_pengajuan', ['disetujui', 'ditolak'])
            ->select(
                'lpc.id as id_transaksi',
                'pc.created_at as tanggal',
                'pc.nomor_urut_pegawai as nup',
                'p.nama', 'd.nama_divisi', 'pek.jabatan',
                'pc.Jenis_cuti as jenis',
                'lpc.status_pengajuan as status',
                \DB::raw("'cuti' as sumber")
            );

        // 3. DEFINISIKAN QUERY LEMBUR (Jangan pakai ->get() di sini)
        $queryLembur = \DB::table('pengajuan_lembur as pl')
            ->join('log_persetujuan_lembur as lpl', 'pl.nomor_urut_pegawai', '=', 'lpl.nomor_urut_pegawai')
            ->join('pegawai as p2', 'pl.nomor_urut_pegawai', '=', 'p2.nomor_urut_pegawai')
            ->join('pekerjaan as pek2', 'p2.nomor_urut_pegawai', '=', 'pek2.nomor_urut_pegawai')
            ->join('divisi as d2', 'pek2.id_divisi', '=', 'd2.id_divisi')
            ->join('users as u2', 'p2.nomor_urut_pegawai', '=', 'u2.nomor_urut_pegawai')
            ->where('lpl.tahap_persetujuan', '!=', 'Pengajuan Awal')
            ->where('u2.level_akses', 'pegawai')
            ->whereIn('lpl.status_persetujuan', ['disetujui', 'ditolak'])
            ->select(
                'lpl.id as id_transaksi',
                'pl.created_at as tanggal',
                'pl.nomor_urut_pegawai as nup',
                'p2.nama', 'd2.nama_divisi', 'pek2.jabatan',
                \DB::raw("'Lembur' as jenis"),
                'lpl.status_persetujuan as status',
                \DB::raw("'lembur' as sumber")
            );

        // 4. TERAPKAN FILTER STATUS (Jika user memilih Disetujui/Ditolak)
        if ($status_filter) {
            $queryCuti->where('lpc.status_pengajuan', $status_filter);
            $queryLembur->where('lpl.status_persetujuan', $status_filter);
        } else {
            // Default jika status kosong: hanya ambil yang sudah diproses (bukan draft)
            $queryCuti->whereIn('lpc.status_pengajuan', ['disetujui', 'ditolak']);
            $queryLembur->whereIn('lpl.status_persetujuan', ['disetujui', 'ditolak']);
        }

        // 5. LOGIKA EKSEKUSI BERDASARKAN JENIS
        if ($jenis_filter === 'cuti') {
            $dataPengajuan = $queryCuti->orderBy('tanggal', 'desc')->get();
        } elseif ($jenis_filter === 'lembur') {
            $dataPengajuan = $queryLembur->orderBy('tanggal', 'desc')->get();
        } else {
            // Gabungkan keduanya jika filter jenis kosong atau 'Semua'
            $dataPengajuan = $queryLembur->union($queryCuti)->orderBy('tanggal', 'desc')->get();
        }

        // 6. Cetak ke PDF
        $pdf = Pdf::loadView('laporan.cetak_pdf', compact('dataPengajuan', 'request'))
                ->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan-Pengajuan-' . now()->format('Y-m-d') . '.pdf');
    }


}
