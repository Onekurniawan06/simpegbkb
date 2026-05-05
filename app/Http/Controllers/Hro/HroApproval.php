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
            ['log' => 'log_persetujuan_pensiun', 'st' => 'status_persetujuan', 'lb' => 'Pensiun', 'time' => 'updated_at', 'slug' => 'pensiun'],
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

            $columnId = 'id';

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

    // 2. Tentukan Tabel Log & Kolom Waktu (PENTING: Pensiun pakai 'update_at')
    $tabelLog = match($sumber) {
        'cuti' => 'log_persetujuan_cuti',
        'lembur' => 'log_persetujuan_lembur',
        'pensiun' => 'log_persetujuan_pensiun',
        'pangkat' => 'log_persetujuan_pangkatgajitunjangan',
        default => 'log_persetujuan_cuti'
    };

    // Deteksi nama kolom waktu secara dinamis agar tidak error lagi
    $kolomWaktu = 'updated_at';

    // 3. Query Detail Data
    if ($sumber === 'cuti') {
        $query = \DB::table('pengajuan_cuti as pc')
            ->join($tabelLog . ' as log', 'pc.id_cuti', '=', 'log.id_cuti')
            ->leftJoin('pegawai as p', 'pc.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->leftJoin('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
            ->leftJoin('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
            ->leftJoin('jenis_cuti as jc', 'pc.jenis_cuti', '=', 'jc.id');

        $query->where(function($q) use ($id_log) {
            $q->where('log.id', $id_log)         // Cek apakah itu ID Log
              ->orWhere('log.id_cuti', $id_log); // Atau ID Cuti
        })
        ->where('log.tahap_persetujuan', 'HRO'); // KUNCI: Harus filter tahapannya!

        $query->select(
            'p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi',
            'pc.*', 'jc.nama_cuti as jenis_cuti_nama',
            'log.status_pengajuan as status',
            "log.{$kolomWaktu} as tanggal_proses",
            'log.tahap_persetujuan', 'log.komentar'
        );

    } elseif ($sumber === 'pensiun') {
        $query = \DB::table('pengajuan_pensiun as main')
            ->join($tabelLog . ' as log', 'main.id_pensiun', '=', 'log.id_pensiun')
            ->leftJoin('pegawai as p', 'main.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->leftJoin('pekerjaan as pek', 'main.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
            ->leftJoin('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
            ->where('log.id', $id_log)
            ->where('log.tahap_persetujuan', 'HRO');

        // Pastikan nama kolom status di select sesuai dengan log_persetujuan_pensiun
        $query->select('p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi', 'main.*', 'log.status_persetujuan as status', "log.{$kolomWaktu} as tanggal_proses", 'log.tahap_persetujuan', 'log.komentar', 'log.id_pensiun');

    } elseif ($sumber === 'pangkat') {
        $query = \DB::table('pengajuan_pangkatgajitunjangan as main')
            ->join($tabelLog . ' as log', 'main.id_kenaikan', '=', 'log.id_kenaikan')
            ->leftJoin('pegawai as p', 'main.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->leftJoin('pekerjaan as pek', 'main.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
            ->leftJoin('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
            ->where('log.id', $id_log)
            ->where('log.tahap_persetujuan', 'HRO');

        $query->select('p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi', 'main.*', 'log.status_persetujuan as status', "log.{$kolomWaktu} as tanggal_proses", 'log.tahap_persetujuan', 'log.komentar', 'main.id_kenaikan');

    } else { // Lembur
        $query = \DB::table('pengajuan_lembur as pl')
            ->join($tabelLog . ' as log', 'pl.id_lembur', '=', 'log.id_lembur')
            ->leftJoin('pegawai as p', 'pl.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->leftJoin('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
            ->leftJoin('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
            ->where("log.id", $id_log)
            ->where('log.tahap_persetujuan', 'HRO');

        $query->select('p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi', 'pl.*', 'log.status_persetujuan as status', "log.{$kolomWaktu} as tanggal_proses", 'log.tahap_persetujuan', 'log.komentar');
    }

    $data = $query->first();
    if (!$data) return redirect()->back()->with('error', 'Detail data tidak ditemukan.');

    // 4. Ambil Histori secara Dinamis
    $queryHistory = \DB::table($tabelLog);
    if ($sumber === 'cuti') {
        $queryHistory->where('id_cuti', $data->id_cuti);
        // Khusus cuti, urutkan berdasarkan id_cuti (karena ini PK log-nya)
        $historiLog = $queryHistory->orderBy('id_cuti', 'asc')->get();
    } elseif ($sumber === 'lembur') {
        $queryHistory->where('id_lembur', $data->id_lembur);
        $historiLog = $queryHistory->orderBy('id', 'asc')->get();
    } elseif ($sumber === 'pensiun') {
        $queryHistory->where('id_pensiun', $data->id_pensiun);
        $historiLog = $queryHistory->orderBy('id', 'asc')->get();
    } else {
        $queryHistory->where('id_kenaikan', $data->id_kenaikan);
        $historiLog = $queryHistory->orderBy('id', 'asc')->get();
    }

    // 5. Kirim File Persyaratan (Pensiun & Pangkat)
    $files = [];
    if ($sumber === 'pensiun') {
        $files = \DB::table('file_persyaratanpensiun')->where('id_pensiun', $data->id_pensiun)->get();
    } elseif ($sumber === 'pangkat') {
        $files = \DB::table('file_persyaratanpangkatgajitunjangan')->where('id_kenaikan', $data->id_kenaikan)->get();
    }

    $tahapTeks = "Verifikasi HRO";
    $pageTitle = 'Detail Verifikasi HRO - ' . ucfirst($sumber);
    $breadcrumbs = [
        'Beranda' => route($dashboardRoute),
        'Manajemen Pengajuan' => route('hro.manajemenpengajuan'),
        'Detail Verifikasi' => '#',
    ];

    return view('hro.detail_approval', compact('data', 'sumber', 'pageTitle', 'id_log', 'breadcrumbs', 'roleName', 'historiLog', 'tahapTeks', 'files'));
}


    /**
     * Update Status Final oleh HRO (Termasuk Potong Cuti)
     */
    public function updateStatus(Request $request, $sumber, $id_log)
{
    try {
        $status = $request->status;
        $user = auth()->user();
        $sumber = strtolower($sumber);

        // 1. Tentukan Tabel Log
        $tabel = match($sumber) {
            'cuti'    => 'log_persetujuan_cuti',
            'lembur'   => 'log_persetujuan_lembur',
            'pensiun'  => 'log_persetujuan_pensiun',
            'pangkat'  => 'log_persetujuan_pangkatgajitunjangan',
            default    => null
        };

        if (!$tabel) throw new \Exception("Tipe pengajuan tidak valid.");

        $kolomStatus = ($sumber === 'cuti') ? 'status_pengajuan' : 'status_persetujuan';
        $kolomWaktu = 'updated_at';
        // if (\Schema::hasColumn($tabelLog, $kolomWaktu)) {
        //     $updateLog[$kolomWaktu] = now();
        // }

        // Ambil baris log HRO yang sedang diproses
        $logHro = \DB::table($tabel)->where('id', $id_log)->where('tahap_persetujuan', 'HRO')->first();

        // Backup jika ID yang dikirim adalah ID Cuti/Lembur (seperti logika aslimu)
        if (!$logHro) {
            $columnRef = ($sumber === 'cuti') ? 'id_cuti' : (($sumber === 'lembur') ? 'id_lembur' : 'id');
            $logHro = \DB::table($tabel)
                ->where($columnRef, $id_log)
                ->where('tahap_persetujuan', 'HRO')
                ->whereNull('nomor_urut_pegawai_penyetuju')
                ->first();
        }

        if (!$logHro) throw new \Exception("Data antrean HRO tidak ditemukan.");

        \DB::beginTransaction();

        // =====================================================================
        // --- 1. SINKRONISASI STATUS FINAL KE TABEL UTAMA (CUTI & LEMBUR) ---
        // =====================================================================
        if ($sumber === 'cuti') {
            \DB::table('pengajuan_cuti')
                ->where('id_cuti', $logHro->id_cuti)
                ->update([
                    'status_pengajuan' => ($status === 'disetujui') ? 'disetujui' : 'ditolak',
                    'updated_at' => now()
                ]);
        } elseif ($sumber === 'lembur') {
            \DB::table('pengajuan_lembur')
                ->where('id_lembur', $logHro->id_lembur)
                ->update([
                    'status_lembur' => ($status === 'disetujui') ? 'disetujui' : 'ditolak',
                    'updated_at' => now()
                ]);
        } elseif ($sumber === 'pensiun') {
            \DB::table('pengajuan_pensiun')
                ->where('id_pensiun', $logHro->id_pensiun)
                ->update([
                    'status_pensiun' => ($status === 'disetujui') ? 'disetujui' : 'ditolak',
                    'updated_at' => now()
                ]);
        } elseif ($sumber === 'pangkat') {
            \DB::table('pengajuan_pangkatgajitunjangan')
                ->where('id_kenaikan', $logHro->id_kenaikan)
                ->update([
                    'status_kenaikan' => ($status === 'disetujui') ? 'disetujui' : 'ditolak',
                    'updated_at' => now()
                ]);
        }
        // =====================================================================

        // 2. Logika Catatan
        $teksDefault = ($status === 'disetujui') ? "Disetujui & Diselesaikan oleh HRO" : "Ditolak oleh HRO";
        $komentarFinal = $request->filled('catatan') ? $request->catatan : $teksDefault;

        // 3. UPDATE BARIS LOG HRO (Closing)
        \DB::table($tabel)->where('id', $logHro->id)->update([
            $kolomStatus => $status,
            'nomor_urut_pegawai_penyetuju' => $user->nomor_urut_pegawai,
            'komentar' => $komentarFinal,
            $kolomWaktu => now(),
        ]);

        // 4. JIKA DISETUJUI -> TAMBAHKAN BARIS "SELESAI" (Sebagai Bukti di Stepper)
        if ($status === 'disetujui') {
            $idColumn = ($sumber === 'cuti') ? 'id_cuti' :
                        (($sumber === 'lembur') ? 'id_lembur' :
                        (($sumber === 'pensiun') ? 'id_pensiun' : 'id_kenaikan'));

            $idValue = $logHro->$idColumn;

            $insertSelesai = [
                'nomor_urut_pegawai' => $logHro->nomor_urut_pegawai,
                'tahap_persetujuan'  => 'Selesai',
                $kolomStatus         => 'disetujui',
                'komentar'           => 'Seluruh tahapan pengajuan telah selesai diproses oleh HRO.',
                'nomor_urut_pegawai_penyetuju' => $user->nomor_urut_pegawai,
                $kolomWaktu          => now(),
                $idColumn            => $idValue
            ];

            \DB::table($tabel)->insert($insertSelesai);
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



    public function lihatDokumen($id)
{
    // 1. Cari di tabel pensiun, jika tidak ada cari di tabel pangkat
    $file = \DB::table('file_persyaratanpensiun')->where('id', $id)->first()
            ?? \DB::table('file_persyaratanpangkatgajitunjangan')->where('id', $id)->first();

    if (!$file) {
        abort(404, 'Data dokumen tidak ditemukan di database.');
    }

    // 2. Penentuan Path (Menangani kemungkinan double folder 'private')
    // Jika di DB sudah tersimpan 'private/dokumen...', maka cukup storage_path('app/')
    $path = storage_path('app/' . $file->path_file_server);

    // 🛡️ Safety Net: Jika file tidak ketemu, coba cek manual di folder private
    if (!file_exists($path)) {
        $path = storage_path('app/private/' . $file->path_file_server);
    }

    // Cek terakhir, jika benar-benar tidak ada di semua lokasi
    if (!file_exists($path)) {
        \Log::error("File tidak ditemukan di server: " . $path);
        abort(404, 'Berkas fisik tidak ditemukan di server. Silakan hubungi admin.');
    }

    // 3. ✨ PERBAIKAN KRUSIAL: Deteksi Mime-Type Otomatis
    // Jangan dipaksa PDF, agar file gambar (JPG/PNG) bisa tampil di modal
    $mimeType = \Illuminate\Support\Facades\File::mimeType($path) ?? 'application/pdf';

    return response()->file($path, [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline; filename="'.$file->nama_file_asli.'"'
    ]);
}


}
