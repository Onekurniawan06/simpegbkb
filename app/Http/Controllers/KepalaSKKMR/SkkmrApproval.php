<?php

namespace App\Http\Controllers\KepalaSKKMR;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SkkmrApproval extends Controller
{
    public function skkmrManagementPersetujuan(Request $request)
    {
        $user = auth()->user();

        // 1. Kunci hak akses untuk Pensiun, Pangkat, dan LEMBUR
        $allowedSources = ['lembur', 'pensiun', 'pangkat'];

        // Tahap yang boleh diverifikasi oleh SKKMR
        $searchTahap = ['Kepala SKK & SKKMR', 'Kepala Satker Kepatuhan & M.R.', 'Pengajuan Awal'];

        // 2. Konfigurasi Tabel Log (Mendaftarkan kembali Lembur)
        $configTables = [
            ['log' => 'log_persetujuan_lembur', 'st' => 'status_persetujuan', 'lb' => 'Lembur', 'time' => 'updated_at', 'slug' => 'lembur'],
            ['log' => 'log_persetujuan_pensiun', 'st' => 'status_persetujuan', 'lb' => 'Pensiun', 'time' => 'updated_at', 'slug' => 'pensiun'],
            ['log' => 'log_persetujuan_pangkatgajitunjangan', 'st' => 'status_persetujuan', 'lb' => 'Pangkat/Gaji/Tunjangan', 'time' => 'updated_at', 'slug' => 'pangkat'],
        ];

        $queries = [];
        $totalMenunggu = 0;
        $detailMenunggu = [];

        foreach ($configTables as $cfg) {
            if (!in_array($cfg['slug'], $allowedSources)) { continue; }

            $baseQuery = \DB::table($cfg['log'] . ' as log')
                ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->join('divisi as d', 'pek.id_divisi', '=', 'd.id_divisi')
                ->where('log.' . $cfg['st'], 'diproses')
                ->whereNull('log.nomor_urut_pegawai_penyetuju')
                ->where('p.nomor_urut_pegawai', '!=', $user->nomor_urut_pegawai);

            // KUNCI 2: Pencarian Tahap Persetujuan
            $baseQuery->where(function($queryGroup) use ($searchTahap, $cfg) {
                $queryGroup->where(function($sub) use ($searchTahap) {
                    foreach ($searchTahap as $st) {
                        if ($st === 'Pengajuan Awal') {
                            $sub->orWhere(function($qLevel) {
                                // SKKMR menarik 'Pengajuan Awal' milik Manager (Level 2)
                                $qLevel->where('log.tahap_persetujuan', 'Pengajuan Awal')
                                    ->where('p.level_id', 2);
                            });
                        } else {
                            $sub->orWhere('log.tahap_persetujuan', 'LIKE', $st);
                        }
                    }
                });

                $queryGroup->orWhere('log.tahap_persetujuan', 'Pengajuan Awal');
                $queryGroup->where('log.tahap_persetujuan', 'NOT LIKE', '%Direktur%');
            });

            $countWait = (clone $baseQuery)->count();
            $totalMenunggu += $countWait;
            $detailMenunggu[] = ['label' => $cfg['lb'], 'jumlah' => $countWait];

            // Kunci ID Log ('id') untuk tombol aksi detail
            $columnId = 'id';

            $q = (clone $baseQuery)->select(
                "log.{$columnId} as id_transaksi",
                'log.' . $cfg['time'] . ' as tanggal',
                'log.nomor_urut_pegawai as nup',
                'p.nama',
                'd.nama_divisi',
                'pek.jabatan',
                \DB::raw("'{$cfg['lb']}' as jenis"),
                'log.' . $cfg['st'] . ' as status',
                \DB::raw("'". $cfg['slug'] ."' as sumber")
            );
            $queries[] = $q;
        }

        if (empty($queries)) {
            $dataPengajuan = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        } else {
            $finalQuery = array_shift($queries);
            foreach ($queries as $u) { $finalQuery->union($u); }
            $dataPengajuan = \DB::table(\DB::raw("({$finalQuery->toSql()}) as combined"))
                ->mergeBindings($finalQuery)
                ->orderBy('tanggal', 'desc')
                ->paginate(10);
        }

        $dashboardRoute = 'skkmr.dashboardskkmr';

        $totalDisetujui = 0; $totalDitolak = 0;
        $detailDisetujui = []; $detailDitolak = [];
        $isHRO = false;
        $isSKK = true;
        $isManager = false;

        return view('skkmr.manajemenpengajuanpegawai', compact(
            'totalMenunggu', 'detailMenunggu', 'totalDisetujui', 'totalDitolak',
            'detailDisetujui', 'detailDitolak', 'dataPengajuan', 'dashboardRoute', 'isHRO', 'isSKK', 'isManager'
        ));
    }

    public function detailApproval($sumber, $id_log)
    {
        $user = auth()->user();

        // 1. Kunci Identitas & Teks Tahap Khusus SKKMR
        $tahapTeks = "Verifikasi Kepala SKK & SKKMR";

        // 2. Mapping Dashboard & Route
        $dashboardRoute = 'skkmr.dashboardskkmr';

        // 3. Tentukan Tabel Log & Kolom Waktu secara Global
        $tabelLog = match($sumber) {
            'lembur' => 'log_persetujuan_lembur',
            'pensiun' => 'log_persetujuan_pensiun',
            'pangkat' => 'log_persetujuan_pangkatgajitunjangan',
            default => abort(404, 'Tipe pengajuan tidak didukung untuk level Anda.')
        };
        $kolomWaktu = 'updated_at';

        // 4. Query Detail Data Berdasarkan Sumber
        if ($sumber === 'pensiun') {
        $query = \DB::table('pengajuan_pensiun as main')
            ->join('log_persetujuan_pensiun as log', 'main.id_pensiun', '=', 'log.id_pensiun')
            ->leftJoin('pegawai as p', 'main.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->leftJoin('pekerjaan as pek', 'main.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
            ->leftJoin('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
            ->where('log.id', $id_log);

        $query->select('p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi', 'main.*', 'log.status_persetujuan as status', 'log.' . $kolomWaktu . ' as tanggal_proses', 'log.tahap_persetujuan', 'log.id_pensiun');

        } elseif ($sumber === 'lembur') {
            $query = \DB::table('pengajuan_lembur as pl')
                ->join($tabelLog . ' as log', 'pl.id_lembur', '=', 'log.id_lembur')
                ->leftJoin('pegawai as p', 'pl.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->leftJoin('pekerjaan as pek', 'pl.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->leftJoin('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
                ->where('log.id', $id_log);

            $query->select('p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi', 'pl.*', 'log.status_persetujuan as status', 'log.updated_at as tanggal_proses', 'log.tahap_persetujuan', 'log.id_lembur');

        } else {
            $query = \DB::table('pengajuan_pangkatgajitunjangan as main')
                ->join('log_persetujuan_pangkatgajitunjangan as log', 'main.id_kenaikan', '=', 'log.id_kenaikan')
                ->leftJoin('pegawai as p', 'main.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->leftJoin('pekerjaan as pek', 'main.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->leftJoin('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
                ->where('log.id', $id_log);

            $query->select('p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi', 'main.*', 'log.status_persetujuan as status', 'log.updated_at as tanggal_proses', 'log.tahap_persetujuan', 'log.id_kenaikan');
        }

        $data = $query->first();
        if (!$data) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // 5. AMBIL SEMUA HISTORI LOG (Untuk Stepper Tracking)
        $queryHistory = \DB::table($tabelLog);
        if ($sumber === 'pensiun' && isset($data->id_pensiun)) {
            $queryHistory->where('id_pensiun', $data->id_pensiun);
        } elseif ($sumber === 'lembur' && isset($data->id_lembur)) {
            $queryHistory->where('id_lembur', $data->id_lembur);
        } else {
            if (isset($data->id_kenaikan)) {
                $queryHistory->where('id_kenaikan', $data->id_kenaikan);
            }
        }

        $historiLog = $queryHistory->orderBy('id', 'asc')->get();

        // 6. AMBIL FILE (Sesuai Slug Sumber)
        $files = [];
        if ($sumber === 'pensiun' && isset($data->id_pensiun)) {
            // Mengambil file untuk Pensiun
            $files = \DB::table('file_persyaratanpensiun')
                ->where('id_pensiun', $data->id_pensiun)
                ->get();

        } elseif ($sumber === 'pangkat' && isset($data->id_kenaikan)) {
            // 👈 Mengambil file untuk Pangkat, Gaji & Tunjangan
            $files = \DB::table('file_persyaratanpangkatgajitunjangan')
                ->where('id_kenaikan', $data->id_kenaikan)
                ->get();
        }

        // 7. Kirim Variabel ke View (Tambahkan 'files' ke dalam compact)
        $sumberNames = [
            'pangkat' => 'Pangkat, Gaji, dan Tunjangan',
            'cuti' => 'Cuti',
            'lembur' => 'Lembur',
            'pensiun' => 'Pensiun',
        ];

        // 8. Paksa $sumber menjadi huruf kecil saat dicocokkan
        $formattedSumber = $sumberNames[strtolower($sumber)] ?? ucfirst($sumber);

        // 9. Kirim Variabel ke View
        $pageTitle = 'Detail Pengajuan ' . $formattedSumber;
        $breadcrumbs = [
            'Beranda' => route($dashboardRoute),
            "Manajemen Pengajuan" => route('skkmr.manajemenpengajuan'),
            'Detail Pengajuan ' . $formattedSumber => '#',
        ];

        return view('skkmr.detail_approval', compact('data', 'sumber', 'pageTitle', 'id_log', 'breadcrumbs', 'dashboardRoute', 'tahapTeks', 'historiLog', 'files'));
    }

    public function updateStatus(Request $request, $sumber, $id_log)
{
    $request->validate([
        'status' => 'required|in:disetujui,ditolak',
        'catatan' => 'nullable|string'
    ]);

    $user = auth()->user();
    $sumber = strtolower($sumber);

    $tabel = match($sumber) {
        'lembur' => 'log_persetujuan_lembur',
        'pensiun' => 'log_persetujuan_pensiun',
        'pangkat', 'pangkatgajitunjangan' => 'log_persetujuan_pangkatgajitunjangan',
        default => null
    };

    if (!$tabel) return response()->json(['success' => false, 'message' => 'Tipe tidak valid'], 400);

    $kolomStatus = 'status_persetujuan';
    $kolomWaktu = 'updated_at';

    $logLama = \DB::table($tabel)->where('id', $id_log)->first();
    if (!$logLama) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);

    $namaTahapAksi = 'Kepala SKK & SKKMR';
    $teksDefault = ($request->status === 'disetujui') ? "Disetujui oleh " . $namaTahapAksi : "Ditolak oleh " . $namaTahapAksi;
    $komentarFinal = $request->filled('catatan') ? $request->catatan : $teksDefault;

    \DB::beginTransaction();
    try {
        // --- 4. UPDATE BARIS LOG SAAT INI ---
        \DB::table($tabel)->where('id', $id_log)->update([
            $kolomStatus => $request->status,
            'nomor_urut_pegawai_penyetuju' => $user->nomor_urut_pegawai,
            'komentar' => $komentarFinal,
            $kolomWaktu => now(),
        ]);

        // --- 5. LOGIKA JIKA DITOLAK ---
        if ($request->status === 'ditolak') {
            if ($sumber === 'lembur') {
                \DB::table('pengajuan_lembur')->where('id_lembur', $logLama->id_lembur)
                    ->update(['status_lembur' => 'ditolak', 'updated_at' => now()]);
            } elseif ($sumber === 'pensiun') {
                \DB::table('pengajuan_pensiun')->where('id_pensiun', $logLama->id_pensiun)
                    ->update(['status_pensiun' => 'ditolak', 'updated_at' => now()]);
            }
            // ➕ TAMBAHKAN SINKRONISASI PANGKAT (Ditolak)
            elseif ($sumber === 'pangkat' || $sumber === 'pangkatgajitunjangan') {
                \DB::table('pengajuan_pangkatgajitunjangan')->where('id_kenaikan', $logLama->id_kenaikan)
                    ->update(['status_kenaikan' => 'ditolak', 'updated_at' => now()]);
            }

            \DB::commit();
            if ($request->ajax() || $request->wantsJson()) return response()->json(['success' => true]);
            return redirect()->route('skkmr.dashboardskkmr')->with('error', 'Pengajuan telah ditolak.');
        }

        // --- 6. LOGIKA DISETUJUI (ESTAFET FLOW) ---
        if ($request->status === 'disetujui') {

            // SINKRONISASI TABEL UTAMA (Status tetap diproses karena estafet berlanjut)
            if ($sumber === 'lembur') {
                \DB::table('pengajuan_lembur')->where('id_lembur', $logLama->id_lembur)
                    ->update(['status_lembur' => 'diproses', 'updated_at' => now()]);
            } elseif ($sumber === 'pensiun') {
                \DB::table('pengajuan_pensiun')->where('id_pensiun', $logLama->id_pensiun)
                    ->update(['status_pensiun' => 'diproses', 'updated_at' => now()]);
            }
            // ➕ TAMBAHKAN SINKRONISASI PANGKAT (Diproses)
            elseif ($sumber === 'pangkat' || $sumber === 'pangkatgajitunjangan') {
                \DB::table('pengajuan_pangkatgajitunjangan')->where('id_kenaikan', $logLama->id_kenaikan)
                    ->update(['status_kenaikan' => 'diproses', 'updated_at' => now()]);
            }

            // ... (Kode Ambil Data Pemohon dan Penentuan Flow tetap sama) ...
            $pemohon = \DB::table('users')->where('nomor_urut_pegawai', $logLama->nomor_urut_pegawai)->first();
            $isManagerPemohon = ($pemohon && $pemohon->level_id == 2);

            if ($sumber === 'lembur') {
                $flow = $isManagerPemohon
                    ? ['Pengajuan Awal', 'Kepala SKK & SKKMR', 'Direktur Kepatuhan', 'Direktur Operasional', 'HRO']
                    : ['Pengajuan Awal', 'Manager', 'Kepala SKK & SKKMR', 'Direktur Kepatuhan', 'Direktur Operasional', 'HRO'];
            } else {
                // Untuk Pensiun & Pangkat: Kepatuhan -> Utama -> HRO
                $flow = ['Pengajuan Awal', 'Kepala SKK & SKKMR', 'Direktur Kepatuhan', 'Direktur Utama', 'HRO'];
            }

            $tahapLama = $logLama->tahap_persetujuan;
            $currentIndex = array_search($tahapLama, $flow);
            $nextTahap = ($currentIndex !== false && isset($flow[$currentIndex + 1])) ? $flow[$currentIndex + 1] : 'Selesai';

            $insertBase = [
                'nomor_urut_pegawai' => $logLama->nomor_urut_pegawai,
                $kolomWaktu => now()
            ];

            if ($sumber === 'pensiun') { $insertBase['id_pensiun'] = $logLama->id_pensiun; }
            elseif ($sumber === 'lembur') { $insertBase['id_lembur'] = $logLama->id_lembur; }
            else { $insertBase['id_kenaikan'] = $logLama->id_kenaikan; }

            // INSERT BUKTI ACTION (Insert baris log SKKMR sendiri jika tahap awal)
            if ($logLama->tahap_persetujuan === 'Pengajuan Awal') {
                \DB::table($tabel)->insert(array_merge($insertBase, [
                    'tahap_persetujuan' => $namaTahapAksi,
                    'nomor_urut_pegawai_penyetuju' => $user->nomor_urut_pegawai,
                    $kolomStatus => 'disetujui',
                    'komentar' => $komentarFinal,
                ]));

                if ($nextTahap === 'Kepala SKK & SKKMR') {
                    $nextTahap = $flow[$currentIndex + 2] ?? 'Selesai';
                }
            }

            // INSERT ANTRIAN TAHAP BERIKUTNYA
            if ($nextTahap !== 'Selesai') {
                \DB::table($tabel)->insert(array_merge($insertBase, [
                    'tahap_persetujuan' => $nextTahap,
                    'nomor_urut_pegawai_penyetuju' => null,
                    $kolomStatus => 'diproses',
                    'komentar' => 'Menunggu verifikasi ' . $nextTahap,
                ]));
            }
        }

        \DB::commit();
        if ($request->ajax() || $request->wantsJson()) return response()->json(['success' => true]);
        return redirect()->route('skkmr.dashboardskkmr')->with('success', 'Berhasil diproses.');

    } catch (\Exception $e) {
        \DB::rollBack();
        return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
    }
}



    public function lihatDokumen($id)
{
    // 1. Cari data di database (Pensiun atau Pangkat)
    $file = \DB::table('file_persyaratanpensiun')->where('id', $id)->first()
            ?? \DB::table('file_persyaratanpangkatgajitunjangan')->where('id', $id)->first();

    if (!$file) {
        abort(404, 'Data dokumen tidak ditemukan di database.');
    }

    // 2. Penentuan Path yang Akurat
    $path = storage_path('app/' . $file->path_file_server);

    // 🛡️ Cek fisik file (Safety Net)
    if (!file_exists($path)) {
        // Cek cadangan jika folder private-nya tidak tercatat di DB tapi ada di folder asli
        $pathAlt = storage_path('app/private/' . $file->path_file_server);

        if (file_exists($pathAlt)) {
            $path = $pathAlt;
        } else {
            \Log::error("Berkas fisik tidak ditemukan di: " . $path);
            abort(404, 'Berkas fisik tidak ditemukan di server.');
        }
    }

    // 3. Ambil Mime Type Otomatis
    $mimeType = \Illuminate\Support\Facades\File::mimeType($path) ?? 'application/pdf';

    // 4. Return File dengan Header yang Benar
    return response()->file($path, [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline; filename="'.$file->nama_file_asli.'"'
    ]);
}



}

