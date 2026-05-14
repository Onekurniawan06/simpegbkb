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

    // 1. Identifikasi Role & List Divisi
    $listDivisi = \DB::table('divisi')->orderBy('nama_divisi', 'asc')->get();
    $roleMapping = \DB::table('roles_mapping')
        ->where('jabatan_id', $user->jabatan_id)
        ->where('level_id', $user->level_id)
        ->first();

    $roleName = $roleMapping->role_name ?? 'Atasan';
    $isManager = str_contains(strtolower($roleName), 'manager');

    // 2. Konfigurasi Laporan (Status diambil dari tabel UTAMA)
    $reportConfigs = [
        ['tabel' => 'pengajuan_cuti',   'log' => 'log_persetujuan_cuti',   'label' => 'Cuti',         'slug' => 'cuti',    'st_col' => 'status_pengajuan', 'pk' => 'id_cuti'],
        ['tabel' => 'pengajuan_lembur', 'log' => 'log_persetujuan_lembur', 'label' => 'Lembur',       'slug' => 'lembur',  'st_col' => 'status_lembur',    'pk' => 'id_lembur'],
        ['tabel' => 'pengajuan_pensiun', 'log' => 'log_persetujuan_pensiun', 'label' => 'Pensiun',      'slug' => 'pensiun', 'st_col' => 'status_pensiun',   'pk' => 'id_pensiun'],
        ['tabel' => 'pengajuan_pangkatgajitunjangan', 'log' => 'log_persetujuan_pangkatgajitunjangan', 'label' => 'Pangkat/Gaji', 'slug' => 'pangkat', 'st_col' => 'status_kenaikan', 'pk' => 'id_kenaikan'],
    ];

    $queries = [];
    $totalDisetujui = 0; $totalDitolak = 0;
    $detailDisetujui = []; $detailDitolak = [];

    foreach ($reportConfigs as $cfg) {
        // --- 3. BASE QUERY: Ambil dari tabel Utama (Main) ---
        $baseQuery = \DB::table($cfg['tabel'] . ' as main')
            ->join('pegawai as p', 'main.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
            ->join('divisi as d', 'pek.id_divisi', '=', 'd.id_divisi')
            // KUNCI: Cek apakah User Login pernah memproses data ini di tabel Log
            ->whereExists(function ($query) use ($cfg, $user) {
                $query->select(\DB::raw(1))
                    ->from($cfg['log'] . ' as log')
                    ->whereColumn('log.' . $cfg['pk'], 'main.' . $cfg['pk'])
                    ->where('log.nomor_urut_pegawai_penyetuju', $user->nomor_urut_pegawai)
                    ->where('log.tahap_persetujuan', '!=', 'Pengajuan Awal');
            })
            // Ambil yang statusnya sudah final di tabel utama
            ->whereIn('main.' . $cfg['st_col'], ['disetujui', 'ditolak']);

        // Filter Filter
        if ($isManager) { $baseQuery->where('pek.id_divisi', $user->id_divisi); }
        if ($request->filled('divisi_filter')) { $baseQuery->where('d.nama_divisi', $request->divisi_filter); }
        if ($request->filled('search')) {
            $baseQuery->where(function($q) use ($request) {
                $q->where('p.nama', 'like', '%' . $request->search . '%')
                  ->orWhere('p.nomor_urut_pegawai', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('status')) { $baseQuery->where('main.' . $cfg['st_col'], $request->status); }
        if ($request->start_date && $request->end_date) {
            $baseQuery->whereBetween(\DB::raw('DATE(main.created_at)'), [$request->start_date, $request->end_date]);
        }

        // --- 4. HITUNG STATISTIK ---
        $countApprove = (clone $baseQuery)->where('main.' . $cfg['st_col'], 'disetujui')->count();
        $countReject = (clone $baseQuery)->where('main.' . $cfg['st_col'], 'ditolak')->count();
        $totalDisetujui += $countApprove; $totalDitolak += $countReject;
        $detailDisetujui[] = ['label' => $cfg['label'], 'jumlah' => $countApprove];
        $detailDitolak[] = ['label' => $cfg['label'], 'jumlah' => $countReject];

        // --- 5. DINAMISASI KOLOM (Gunakan Main Table) ---
        $jenisSql = match($cfg['slug']) {
            'cuti' => 'main.jenis_cuti', 'pensiun', 'pangkat' => 'main.jenis_pengajuan', default => "'Lembur'"
        };
        $tglAwalSql = match($cfg['slug']) {
            'cuti' => 'main.tanggal_mulai', 'lembur' => 'main.tanggal_lembur', 'pensiun' => 'main.tmt_pensiun', 'pangkat' => 'main.tmt_pegawai', default => 'main.created_at'
        };
        $tglAkhirSql = ($cfg['slug'] === 'cuti') ? 'main.tanggal_selesai' : 'NULL';
        $jamLemburSql = ($cfg['slug'] === 'lembur') ? 'main.total_jam_lembur' : 'NULL';

        $queries[] = $baseQuery->select(
            'main.' . $cfg['pk'] . ' as id_transaksi',
            'main.created_at as tanggal',
            'p.nomor_urut_pegawai as nup', 'p.nama', 'd.nama_divisi', 'pek.jabatan',
            \DB::raw("$jenisSql as jenis"),
            'main.' . $cfg['st_col'] . ' as status',
            \DB::raw("'{$cfg['slug']}' as sumber"),
            \DB::raw("$tglAwalSql as tgl_awal"),
            \DB::raw("$tglAkhirSql as tgl_akhir"),
            \DB::raw("$jamLemburSql as total_jam_lembur")
        );
    }

    // 6. Eksekusi Union & Pagination
    if (empty($queries)) {
        $dataPengajuan = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
    } else {
        $finalQuery = array_shift($queries);
        foreach ($queries as $u) { $finalQuery->unionAll($u); }
        $dataPengajuan = \DB::table(\DB::raw("({$finalQuery->toSql()}) as combined"))
            ->mergeBindings($finalQuery)->orderBy('tanggal', 'desc')->paginate(10)->withQueryString();
    }

    // 7. AMBIL HISTORI UNTUK STEPPER HOVER
    $dataPengajuan->getCollection()->transform(function($row) {
        $logTable = match($row->sumber) {
            'cuti' => 'log_persetujuan_cuti', 'lembur' => 'log_persetujuan_lembur', 'pensiun' => 'log_persetujuan_pensiun', 'pangkat' => 'log_persetujuan_pangkatgajitunjangan', default => 'log_persetujuan_cuti'
        };
        $fkLog = match($row->sumber) {
            'cuti' => 'id_cuti', 'lembur' => 'id_lembur', 'pensiun' => 'id_pensiun', 'pangkat' => 'id_kenaikan', default => 'id'
        };
        $row->histori = \DB::table($logTable . ' as log')
            ->leftJoin('pegawai as p', 'log.nomor_urut_pegawai_penyetuju', '=', 'p.nomor_urut_pegawai')
            ->where('log.' . $fkLog, $row->id_transaksi)
            ->select('log.*', 'p.nama as nama_penyetuju')->orderBy('log.id', 'asc')->get();
        return $row;
    });

    $layout = $user->layout_file;
    $breadcrumbs = ['Beranda' => $user->dashboard_link, "Laporan Persetujuan $roleName" => '#'];

    return view('laporan.laporanpengajuan', compact(
        'breadcrumbs', 'totalDisetujui', 'totalDitolak', 'detailDisetujui', 'detailDitolak',
        'dataPengajuan', 'roleName', 'isManager', 'layout', 'listDivisi'
    ));
}


    public function cetakPDF(Request $request)
    {
        $user = auth()->user();

        // 1. Identifikasi Role (Penting untuk filter data yang boleh dicetak)
        $roleMapping = \DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();
        $isManager = str_contains(strtolower($roleMapping->role_name ?? ''), 'manager');

        // 2. Konfigurasi 4 Tabel (Gunakan kolom status Tabel Utama)
        $reportConfigs = [
            ['tabel' => 'pengajuan_cuti',   'log' => 'log_persetujuan_cuti',   'label' => 'Cuti',         'slug' => 'cuti',    'st_col' => 'status_pengajuan', 'pk' => 'id_cuti'],
            ['tabel' => 'pengajuan_lembur', 'log' => 'log_persetujuan_lembur', 'label' => 'Lembur',       'slug' => 'lembur',  'st_col' => 'status_lembur',    'pk' => 'id_lembur'],
            ['tabel' => 'pengajuan_pensiun', 'log' => 'log_persetujuan_pensiun', 'label' => 'Pensiun',      'slug' => 'pensiun', 'st_col' => 'status_pensiun',   'pk' => 'id_pensiun'],
            ['tabel' => 'pengajuan_pangkatgajitunjangan', 'log' => 'log_persetujuan_pangkatgajitunjangan', 'label' => 'Pangkat/Gaji', 'slug' => 'pangkat', 'st_col' => 'status_kenaikan', 'pk' => 'id_kenaikan'],
        ];

        $queries = [];

        foreach ($reportConfigs as $cfg) {
            // --- FILTER JENIS (Jika user pilih salah satu, skip yang lain) ---
            if ($request->filled('jenis') && strtolower($request->jenis) !== $cfg['slug']) {
                continue;
            }

            $baseQuery = \DB::table($cfg['tabel'] . ' as main')
                ->join('pegawai as p', 'main.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->join('divisi as d', 'pek.id_divisi', '=', 'd.id_divisi')
                ->whereExists(function ($query) use ($cfg, $user) {
                    $query->select(\DB::raw(1))
                        ->from($cfg['log'] . ' as log')
                        ->whereColumn('log.' . $cfg['pk'], 'main.' . $cfg['pk'])
                        ->where('log.nomor_urut_pegawai_penyetuju', $user->nomor_urut_pegawai)
                        ->where('log.tahap_persetujuan', '!=', 'Pengajuan Awal');
                });

            // --- FILTER TAMBAHAN (Sesuai Request) ---
            if ($isManager) {
                $baseQuery->where('pek.id_divisi', $user->id_divisi);
            }
            if ($request->filled('divisi_filter')) {
                $baseQuery->where('d.nama_divisi', $request->divisi_filter);
            }
            if ($request->filled('search')) {
                $baseQuery->where(function($q) use ($request) {
                    $q->where('p.nama', 'like', '%' . $request->search . '%')
                    ->orWhere('p.nomor_urut_pegawai', 'like', '%' . $request->search . '%');
                });
            }
            if ($request->filled('status')) {
                $baseQuery->where('main.' . $cfg['st_col'], $request->status);
            } else {
                $baseQuery->whereIn('main.' . $cfg['st_col'], ['disetujui', 'ditolak']);
            }
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $baseQuery->whereBetween(\DB::raw('DATE(main.created_at)'), [$request->start_date, $request->end_date]);
            }

            // --- DINAMISASI KOLOM UNTUK UNION ---
            $jenisSql = match($cfg['slug']) {
                'cuti'    => 'main.jenis_cuti',
                'pensiun', 'pangkat' => 'main.jenis_pengajuan',
                default   => "'Lembur'"
            };
            $tglAwalSql = match($cfg['slug']) {
                'cuti'    => 'main.tanggal_mulai',
                'lembur'  => 'main.tanggal_lembur',
                'pensiun' => 'main.tmt_pensiun',
                'pangkat' => 'main.tmt_pegawai',
                default   => 'main.created_at'
            };
            $tglAkhirSql = ($cfg['slug'] === 'cuti') ? 'main.tanggal_selesai' : 'NULL';
            $jamLemburSql = ($cfg['slug'] === 'lembur') ? 'main.total_jam_lembur' : 'NULL';

            $q = $baseQuery->select(
                'main.' . $cfg['pk'] . ' as id_transaksi',
                'main.created_at as tanggal',
                'p.nomor_urut_pegawai as nup',
                'p.nama',
                'd.nama_divisi',
                'pek.jabatan',
                \DB::raw("$jenisSql as jenis"),
                'main.' . $cfg['st_col'] . ' as status',
                \DB::raw("'{$cfg['slug']}' as sumber"),
                \DB::raw("$tglAwalSql as tgl_awal"),
                \DB::raw("$tglAkhirSql as tgl_akhir"),
                \DB::raw("$jamLemburSql as total_jam_lembur")
            );

            $queries[] = $q;
        }

        // 3. Eksekusi Gabungan Data
        if (empty($queries)) {
            $dataPengajuan = collect([]);
        } else {
            $finalQuery = array_shift($queries);
            foreach ($queries as $u) { $finalQuery->unionAll($u); }
            $dataPengajuan = \DB::table(\DB::raw("({$finalQuery->toSql()}) as combined"))
                ->mergeBindings($finalQuery)
                ->orderBy('tanggal', 'desc')
                ->get();
        }

        // 4. Generate PDF
        $pdf = Pdf::loadView('laporan.cetak_pdf', [
            'dataPengajuan' => $dataPengajuan,
            'request' => $request,
            'roleName' => $roleMapping->role_name ?? 'Atasan'
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan-Pengajuan-' . now()->format('d-m-Y') . '.pdf');
    }

}
