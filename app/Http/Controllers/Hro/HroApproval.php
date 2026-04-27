<?php

namespace App\Http\Controllers\Hro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HroApproval extends Controller
{
    /**
     * Menampilkan daftar antrean pengajuan yang harus diproses oleh HRO
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Ambil Data Divisi untuk dropdown filter di Blade
        $listDivisi = DB::table('divisi')->orderBy('nama_divisi', 'asc')->get();

        $roleMapping = DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        $roleName = $roleMapping->role_name ?? 'HRO';

        // 2. Konfigurasi Tabel Log (Lengkap dengan Pensiun dan Pangkat)
        $configTables = [
            ['log' => 'log_persetujuan_cuti', 'st' => 'status_pengajuan', 'lb' => 'Cuti', 'time' => 'updated_at', 'slug' => 'cuti'],
            ['log' => 'log_persetujuan_lembur', 'st' => 'status_persetujuan', 'lb' => 'Lembur', 'time' => 'updated_at', 'slug' => 'lembur'],
            ['log' => 'log_persetujuan_pensiun', 'st' => 'status_persetujuan', 'lb' => 'Pensiun', 'time' => 'update_at', 'slug' => 'pensiun'],
            ['log' => 'log_persetujuan_pangkatgajitunjangan', 'st' => 'status_persetujuan', 'lb' => 'Pangkat/Gaji/Tunjangan', 'time' => 'updated_at', 'slug' => 'pangkat'],
        ];

        $queries = [];
        $totalMenunggu = 0;
        $detailMenunggu = [];

        foreach ($configTables as $cfg) {
            $baseQuery = DB::table($cfg['log'] . ' as log')
                ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->join('divisi as d', 'pek.id_divisi', '=', 'd.id_divisi')
                ->where('log.' . $cfg['st'], 'diproses')
                ->where('log.tahap_persetujuan', 'HRO')
                ->whereNull('log.nomor_urut_pegawai_penyetuju');

            // --- FILTER DIVISI ---
            if ($request->filled('divisi_filter')) {
                $baseQuery->where('pek.id_divisi', $request->divisi_filter);
            }

            // --- FILTER JENIS PENGAJUAN ---
            if ($request->filled('jenis_filter')) {
                if ($request->jenis_filter === 'pangkat_pensiun') {
                    // Filter gabungan Pensiun dan Pangkat
                    if (!in_array($cfg['slug'], ['pensiun', 'pangkat'])) {
                        continue;
                    }
                } else {
                    // Filter tunggal (Cuti atau Lembur)
                    if ($cfg['slug'] !== $request->jenis_filter) {
                        continue;
                    }
                }
            }

            $countWait = (clone $baseQuery)->count();
            $totalMenunggu += $countWait;
            $detailMenunggu[] = ['label' => $cfg['lb'], 'jumlah' => $countWait];

            $columnId = ($cfg['slug'] === 'cuti') ? 'id_cuti' : 'id';

            $q = (clone $baseQuery)->select(
                "log.{$columnId} as id_transaksi",
                "log.{$cfg['time']} as tanggal",
                'log.nomor_urut_pegawai as nup',
                'p.nama',
                'd.nama_divisi',
                'pek.jabatan',
                DB::raw("'{$cfg['lb']}' as jenis"),
                "log.{$cfg['st']} as status",
                DB::raw("'{$cfg['slug']}' as sumber")
            );

            if ($request->filled('search')) {
                $q->where(function($sub) use ($request) {
                    $sub->where('p.nama', 'LIKE', '%' . $request->search . '%')
                        ->orWhere('p.nomor_urut_pegawai', 'LIKE', '%' . $request->search . '%');
                });
            }
            $queries[] = $q;
        }

        if (empty($queries)) {
            $dataPengajuan = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        } else {
            $finalQuery = array_shift($queries);
            foreach ($queries as $u) { $finalQuery->unionAll($u); }

            $dataPengajuan = DB::table(DB::raw("({$finalQuery->toSql()}) as combined"))
                ->mergeBindings($finalQuery)
                ->orderBy('tanggal', 'desc')
                ->paginate(10)
                ->withQueryString();
        }

        return view('hro.manajemenpengajuanpegawai', compact(
            'dataPengajuan', 'totalMenunggu', 'detailMenunggu', 'roleName', 'listDivisi'
        ));
    }

        /**
     * Menampilkan Detail Pengajuan untuk Verifikasi Akhir HRO
     */
    public function detailApproval($sumber, $id_log)
    {
        $user = auth()->user();

        // 1. Identifikasi Role (HRO)
        $roleMapping = \DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        $roleName = $roleMapping->role_name ?? 'HRO';
        $dashboardRoute = $roleMapping->route_name ?? 'hro.dashboardhro';

        // 2. Tentukan Tabel Log
        $tabelLog = match($sumber) {
            'cuti' => 'log_persetujuan_cuti',
            'lembur' => 'log_persetujuan_lembur',
            'pensiun' => 'log_persetujuan_pensiun',
            'pangkat' => 'log_persetujuan_pangkatgajitunjangan',
            default => 'log_persetujuan_cuti'
        };

        $primaryKeyLog = ($sumber === 'cuti') ? 'id_cuti' : 'id';

        // 3. Query Detail Data (HRO Lintas Divisi)
        // Ambil log yang sedang diproses oleh HRO
        $logData = \DB::table($tabelLog)
            ->where($primaryKeyLog, $id_log)
            ->where('tahap_persetujuan', 'HRO')
            ->first();

        if (!$logData) return redirect()->back()->with('error', 'Data antrean HRO tidak ditemukan.');

        if ($sumber === 'cuti') {
            $query = \DB::table('pengajuan_cuti as pc')
                ->join($tabelLog . ' as log', 'pc.id_cuti', '=', 'log.id_cuti')
                ->leftJoin('pegawai as p', 'pc.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->leftJoin('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->leftJoin('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
                ->leftJoin('jenis_cuti as jc', 'pc.jenis_cuti', '=', 'jc.id')
                ->where('log.id_cuti', $id_log)
                ->where('log.tahap_persetujuan', 'HRO');

            $query->select('p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi', 'pc.*', 'jc.nama_cuti as jenis_cuti_nama', 'log.status_pengajuan as status', 'log.updated_at as tanggal_proses', 'log.tahap_persetujuan', 'log.komentar');
        } else {
            // Logika untuk Lembur/Pensiun/Pangkat (disingkat agar fokus ke Cuti)
            $tabelUtama = ($sumber === 'lembur') ? 'pengajuan_lembur' : (($sumber === 'pensiun') ? 'pengajuan_pensiun' : 'pengajuan_pangkatgajitunjangan');
            $idUtama = ($sumber === 'lembur') ? 'id_lembur' : (($sumber === 'pensiun' || $sumber === 'pangkat') ? 'id_pengajuan' : 'id');
            $idLogRef = ($sumber === 'lembur') ? 'id_lembur' : 'id_pengajuan';

            $query = \DB::table($tabelUtama . ' as main')
                ->join($tabelLog . ' as log', "main.{$idUtama}", '=', "log.{$idLogRef}")
                ->leftJoin('pegawai as p', 'main.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->leftJoin('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->leftJoin('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
                ->where("log.id", $id_log)
                ->where('log.tahap_persetujuan', 'HRO');

            $query->select('p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi', 'main.*', 'log.status_persetujuan as status', 'log.updated_at as tanggal_proses', 'log.tahap_persetujuan', 'log.komentar');
        }

        $data = $query->first();
        if (!$data) return redirect()->back()->with('error', 'Detail data tidak ditemukan.');

        // 4. Ambil Histori (Tracking)
        $queryHistory = \DB::table($tabelLog);
        if ($sumber === 'cuti') {
            $queryHistory->where('id_cuti', $data->id_cuti);
        } elseif ($sumber === 'lembur') {
            $queryHistory->where('id_lembur', $data->id_lembur);
        } else {
            $queryHistory->where('id_pengajuan', $data->id_pengajuan);
        }
        $historiLog = $queryHistory->orderBy('id', 'asc')->get();

        $tahapTeks = "Verifikasi HRO";
        $pageTitle = 'Detail Verifikasi HRO - ' . ucfirst($sumber);
        $breadcrumbs = [
            'Beranda' => route($dashboardRoute),
            'Manajemen Pengajuan' => route('hro.manajemenpengajuan'),
            'Detail Verifikasi' => '#',
        ];

        return view('hro.detail_approval', compact('data', 'sumber', 'pageTitle', 'id_log', 'breadcrumbs', 'roleName', 'historiLog', 'tahapTeks'));
    }

    /**
     * Update Status Final oleh HRO (Termasuk Potong Cuti)
     */
    public function updateStatus(Request $request, $sumber, $id_log)
    {
        try {
            $status = $request->status;
            $user = auth()->user();

            // 1. Tentukan Tabel Log & Kolom PK secara dinamis
            $tabel = match($sumber) {
                'cuti'    => 'log_persetujuan_cuti',
                'lembur'   => 'log_persetujuan_lembur',
                'pensiun'  => 'log_persetujuan_pensiun',
                'pangkat'  => 'log_persetujuan_pangkatgajitunjangan',
                default    => null
            };

            if (!$tabel) throw new \Exception("Tipe pengajuan tidak valid.");

            $kolomStatus = ($sumber === 'cuti') ? 'status_pengajuan' : 'status_persetujuan';
            $kolomWaktu = ($sumber === 'pensiun') ? 'update_at' : 'updated_at';
            $primaryKeyLog = ($sumber === 'cuti') ? 'id_cuti' : 'id';

            // Ambil data log HRO yang sedang diproses
            $logHro = \DB::table($tabel)
                ->where($primaryKeyLog, $id_log)
                ->where('tahap_persetujuan', 'HRO')
                ->first();

            if (!$logHro) throw new \Exception("Antrean HRO tidak ditemukan.");

            \DB::beginTransaction();

            // 2. Logika Catatan (Gunakan input catatan, jika kosong pakai default)
            $teksDefault = ($status === 'disetujui') ? "Disetujui & Diselesaikan oleh HRO" : "Ditolak oleh HRO";
            $komentarFinal = $request->filled('catatan') ? $request->catatan : $teksDefault;

            // 3. UPDATE BARIS LOG HRO (Tahap Penutup)
            \DB::table($tabel)->where('id', $logHro->id)->update([
                $kolomStatus => $status,
                'nomor_urut_pegawai_penyetuju' => $user->nomor_urut_pegawai,
                'komentar' => $komentarFinal,
                $kolomWaktu => now(),
            ]);

            // 4. JIKA DISETUJUI -> TAMBAHKAN BARIS "SELESAI"
            if ($status === 'disetujui') {

            // --- PROTEKSI ANTI-DOUBLE: Cek apakah baris 'Selesai' sudah pernah dibuat ---
            $cekSelesai = \DB::table($tabel)
                ->where($primaryKeyLog, $id_log)
                ->where('tahap_persetujuan', 'Selesai')
                ->exists();

            if (!$cekSelesai) {
                // Hanya insert jika belum ada baris 'Selesai'
                $insertSelesai = [
                    'nomor_urut_pegawai' => $logHro->nomor_urut_pegawai,
                    'tahap_persetujuan'  => 'Selesai',
                    $kolomStatus         => 'disetujui',
                    'komentar'           => 'Seluruh tahapan pengajuan telah selesai diproses.',
                    'nomor_urut_pegawai_penyetuju' => $user->nomor_urut_pegawai,
                    $kolomWaktu          => now()
                ];

                if ($sumber === 'cuti') {
                    $insertSelesai['id_cuti'] = $logHro->id_cuti;
                } elseif ($sumber === 'lembur') {
                    $insertSelesai['id_lembur'] = $logHro->id_lembur;
                } else {
                    $insertSelesai['id_pengajuan'] = $logHro->id_pengajuan ?? null;
                }

                \DB::table($tabel)->insert($insertSelesai);
            }
        }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Pengajuan telah selesai diproses.',
                'redirect' => route('hro.manajemenpengajuan')
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

}
