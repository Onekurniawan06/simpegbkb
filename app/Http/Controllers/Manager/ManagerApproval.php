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
        $idDivisiManager = $user->id_divisi;

        $namaJabatan = \DB::table('jabatan')->where('jabatan_id', $user->jabatan_id)->value('nama_jabatan') ?? '';
        $namaJabatanLower = strtolower($namaJabatan);

        // 1. Tentukan Keyword Tahap (Gunakan LIKE agar fleksibel)
        $isLintasDivisi = false;
        $allowedSources = [];
        $searchTahap = [];

        if (str_contains($namaJabatanLower, 'kepatuhan') || str_contains($namaJabatanLower, 'skk')) {
            $isLintasDivisi = true;
            $allowedSources = ['lembur', 'pensiun', 'pangkatgajitunjangan'];

            // KUNCI: SKK boleh lihat 'Pengajuan Awal' HANYA untuk Pensiun & PangkatGaji.
            // Untuk Lembur, SKK hanya boleh lihat jika tahapnya sudah 'Kepala SKK & SKKMR'.
            $searchTahap = ['%SKK%', '%MR%', '%Kepatuhan%'];
        } elseif (str_contains($namaJabatanLower, 'hro')) {
            $isLintasDivisi = true;
            $allowedSources = ['cuti', 'lembur', 'pensiun', 'pangkatgajitunjangan'];
            $searchTahap = ['%HRO%', 'Selesai'];
        } else {
            // MANAGER DIVISI
            $isLintasDivisi = false;
            $allowedSources = ['cuti', 'lembur'];
            $searchTahap = ['Manager', 'Pengajuan Awal'];
        }

        // 2. Konfigurasi Tabel Log
        $configTables = [
            ['log' => 'log_persetujuan_cuti', 'st' => 'status_pengajuan', 'lb' => 'Cuti', 'time' => 'updated_at', 'slug' => 'cuti'],
            ['log' => 'log_persetujuan_lembur', 'st' => 'status_persetujuan', 'lb' => 'Lembur', 'time' => 'updated_at', 'slug' => 'lembur'],
            ['log' => 'log_persetujuan_pensiun', 'st' => 'status_persetujuan', 'lb' => 'Pensiun', 'time' => 'update_at', 'slug' => 'pensiun'],
            ['log' => 'log_persetujuan_pangkatgajitunjangan', 'st' => 'status_persetujuan', 'lb' => 'Pangkat/Gaji/Tunjangan', 'time' => 'updated_at', 'slug' => 'pangkatgajitunjangan'],
        ];

        $queries = [];
        $totalMenunggu = 0; $detailMenunggu = [];
        $totalDisetujui = 0; $totalDitolak = 0;
        $detailDisetujui = []; $detailDitolak = [];

        foreach ($configTables as $cfg) {
            if (!in_array($cfg['slug'], $allowedSources)) { continue; }

            $baseQuery = \DB::table($cfg['log'] . ' as log')
                ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->join('divisi as d', 'pek.id_divisi', '=', 'd.id_divisi')
                ->where('log.' . $cfg['st'], 'diproses')
                ->where('p.nomor_urut_pegawai', '!=', $user->nomor_urut_pegawai);

            // LOGIKA FILTER TAHAP YANG LEBIH PINTAR
            $baseQuery->where(function($q) use ($searchTahap, $cfg) {
                // Jalankan filter keyword utama (SKK/MR/HRO/Manager)
                foreach ($searchTahap as $st) {
                    $q->orWhere('log.tahap_persetujuan', 'LIKE', $st);
                }

                // TAMBAHAN KHUSUS SKK: Boleh tarik 'Pengajuan Awal' jika sumbernya Pensiun/PangkatGaji
                $userJabatan = strtolower(auth()->user()->jabatan->nama_jabatan ?? '');
                if (str_contains($userJabatan, 'skk') || str_contains($userJabatan, 'kepatuhan')) {
                    if (in_array($cfg['slug'], ['pensiun', 'pangkatgajitunjangan'])) {
                        $q->orWhere('log.tahap_persetujuan', 'Pengajuan Awal');
                    }
                }
            });

            if (!$isLintasDivisi) {
                $baseQuery->where('pek.id_divisi', $idDivisiManager);
            }

            // Statistik
            $countWait = (clone $baseQuery)->count();
            $totalMenunggu += $countWait;
            $detailMenunggu[] = ['label' => $cfg['lb'], 'jumlah' => $countWait];
            $detailDisetujui[] = ['label' => $cfg['lb'], 'jumlah' => 0];
            $detailDitolak[] = ['label' => $cfg['lb'], 'jumlah' => 0];

            $q = (clone $baseQuery)->select(
                'log.id as id_transaksi',
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

        $mapping = \DB::table('roles_mapping')->where('level_id', $user->level_id)->first();
        $dashboardRoute = $mapping->route_name ?? 'dashboard';

        $isHRO = str_contains($namaJabatanLower, 'hro') || str_contains($namaJabatanLower, 'human resources');
        $isSKK = str_contains($namaJabatanLower, 'kepatuhan') || str_contains($namaJabatanLower, 'skk');
        $isManager = !$isHRO && !$isSKK; // Selain HRO & SKK dianggap Manager Divisi

        return view('manager.manajemenpengajuanpegawai', compact(
            'totalMenunggu', 'detailMenunggu', 'totalDisetujui', 'totalDitolak',
            'detailDisetujui', 'detailDitolak', 'dataPengajuan', 'dashboardRoute', 'isHRO', 'isSKK', 'isManager'
        ));
    }


    public function detailApproval($sumber, $id_log)
    {
        $user = auth()->user();
        $idDivisiManager = $user->id_divisi;

        // 1. Identifikasi Jabatan & Teks Dinamis
        $jabatanUser = \DB::table('jabatan')->where('jabatan_id', $user->jabatan_id)->first();
        $namaJabatanLower = strtolower($jabatanUser->nama_jabatan ?? '');

        if (str_contains($namaJabatanLower, 'kepatuhan') || str_contains($namaJabatanLower, 'skk')) {
            $tahapTeks = "Verifikasi Kepala SKK & SKKMR";
        } elseif (str_contains($namaJabatanLower, 'kepala skai') || str_contains($namaJabatanLower, 'skai')) {
            $tahapTeks = "Verifikasi Kepala SKAI";
        } elseif (str_contains($namaJabatanLower, 'human resources') || str_contains($namaJabatanLower, 'hro')) {
            $tahapTeks = "Verifikasi HRO";
        } elseif (str_contains($namaJabatanLower, 'direktur')) {
            $tahapTeks = "Verifikasi " . ($jabatanUser->nama_jabatan ?? 'Direktur');
        } else {
            $tahapTeks = "Verifikasi Manager";
        }

        // 2. Mapping Dashboard & Route
        $mapping = \DB::table('roles_mapping')
            ->where('level_id', $user->level_id)
            ->where(function($q) use ($user) {
                $q->where('jabatan_id', $user->jabatan_id)->orWhereNull('jabatan_id');
            })
            ->orderBy('priority', 'asc')
            ->first();
        $dashboardRoute = $mapping->route_name ?? 'dashboard';

        $divisiData = \DB::table('divisi')->where('id_divisi', $idDivisiManager)->first();
        $namaDivisiReal = $divisiData->nama_divisi ?? 'Divisi';
        $slugDivisi = \Illuminate\Support\Str::slug($namaDivisiReal);

        $isLintasDivisi = str_contains($namaJabatanLower, 'kepatuhan') || str_contains($namaJabatanLower, 'skk') || str_contains($namaJabatanLower, 'direktur') || str_contains($namaJabatanLower, 'hro');

        // 3. Tentukan Tabel Log & Kolom Waktu secara Global
        $tabelLog = match($sumber) {
            'cuti' => 'log_persetujuan_cuti',
            'lembur' => 'log_persetujuan_lembur',
            'pensiun' => 'log_persetujuan_pensiun',
            'pangkatgajitunjangan' => 'log_persetujuan_pangkatgajitunjangan',
            default => 'log_persetujuan_cuti'
        };
        $kolomWaktu = ($sumber === 'pensiun') ? 'update_at' : 'updated_at';

        // 4. Query Detail Data Berdasarkan Sumber
        if ($sumber === 'cuti') {
            $query = \DB::table('pengajuan_cuti as pc')
                ->join('pegawai as p', 'pc.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->join('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
                // TAMBAHKAN JOIN KE TABEL JENIS_CUTI SESUAI IMAGE
                ->join('jenis_cuti as jc', 'pc.jenis_cuti', '=', 'jc.id')
                ->join($tabelLog . ' as log', 'pc.nomor_urut_pegawai', '=', 'log.nomor_urut_pegawai')
                ->where('log.id', $id_log)
                // Ambil jc.nama_cuti sebagai 'Jenis_cuti' agar sinkron dengan Blade kamu
                ->select('p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi', 'pc.*', 'jc.nama_cuti as Jenis_cuti', 'log.status_pengajuan as status', 'log.updated_at as tanggal_proses', 'log.tahap_persetujuan');
        } elseif ($sumber === 'lembur') {
            $query = \DB::table('pengajuan_lembur as pl')
                ->join('pegawai as p', 'pl.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->join('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
                ->join($tabelLog . ' as log', 'pl.nomor_urut_pegawai', '=', 'log.nomor_urut_pegawai')
                ->where('log.id', $id_log)
                ->select('p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi', 'pl.uraian_tugas', 'log.status_persetujuan as status', 'log.updated_at as tanggal_proses', 'log.tahap_persetujuan', 'log.lembur_id');
        } else {
            $tabelUtama = ($sumber === 'pensiun') ? 'pengajuan_pensiun' : 'pengajuan_pangkatgajitunjangan';
            $query = \DB::table($tabelUtama . ' as main')
                ->join('pegawai as p', 'main.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->join('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
                ->join($tabelLog . ' as log', 'main.nomor_urut_pegawai', '=', 'log.nomor_urut_pegawai')
                ->where('log.id', $id_log)
                // Pastikan main.jenis_pengajuan ikut terambil
                ->select('p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi', 'main.*', 'log.status_persetujuan as status', 'log.' . $kolomWaktu . ' as tanggal_proses', 'log.tahap_persetujuan', 'log.id_pengajuan');
        }

        if (!$isLintasDivisi) {
            $query->where('pek.id_divisi', $idDivisiManager);
        }

        $data = $query->first();

        if (!$data) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // 5. AMBIL SEMUA HISTORI LOG (Untuk Stepper Tracking)
        // Sekarang variabel $data sudah pasti ada, jadi query history aman ditaruh di sini
        $queryHistory = \DB::table($tabelLog)->where('nomor_urut_pegawai', $data->nomor_urut_pegawai);

        if ($sumber === 'lembur') {
            $queryHistory->where('lembur_id', $data->lembur_id);
        } else {
            $queryHistory->where('id_pengajuan', $data->id_pengajuan);
        }

        $historiLog = $queryHistory->orderBy('id', 'asc')->get();

        // 6. Kirim Variabel ke View
        $pageTitle = 'Detail Pengajuan ' . ucfirst($sumber);
        $breadcrumbs = [
            'Beranda' => ($dashboardRoute === 'manager.dashboardmanager') ? route($dashboardRoute, ['divisi' => $slugDivisi]) : route($dashboardRoute),
            "Manajemen Pengajuan ↦ Approval Pegawai Divisi $namaDivisiReal" => route('manager.manajemenpengajuan'),
            'Detail Pengajuan ' . ucfirst($sumber) => '#',
        ];

        return view('manager.detail_approval', compact('data', 'sumber', 'pageTitle', 'id_log', 'breadcrumbs', 'dashboardRoute', 'tahapTeks', 'historiLog'));
    }

    public function updateStatus(Request $request, $sumber, $id_log)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string'
        ]);

        $user = auth()->user();
        $tabel = match($sumber) {
            'cuti' => 'log_persetujuan_cuti',
            'lembur' => 'log_persetujuan_lembur',
            'pensiun' => 'log_persetujuan_pensiun',
            'pangkatgajitunjangan' => 'log_persetujuan_pangkatgajitunjangan',
            default => null
        };

        if (!$tabel) return response()->json(['success' => false, 'message' => 'Tipe tidak valid'], 400);

        $kolomStatus = ($sumber === 'cuti') ? 'status_pengajuan' : 'status_persetujuan';
        $kolomWaktu = ($sumber === 'pensiun') ? 'update_at' : 'updated_at';

        $logLama = \DB::table($tabel)->where('id', $id_log)->first();
        if (!$logLama) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);

        // --- PREPARASI DATA INSERT BASE ---
        $insertBase = [
            'nomor_urut_pegawai' => $logLama->nomor_urut_pegawai,
            $kolomWaktu => now(),
        ];
        if ($sumber === 'lembur') { $insertBase['lembur_id'] = $logLama->lembur_id; }
        else { $insertBase['id_pengajuan'] = $logLama->id_pengajuan ?? null; }

        // 1. UPDATE baris log saat ini (Kalau ditolak ya DITOLAK, gak gue ganti jadi disetujui lagi!)
        \DB::table($tabel)->where('id', $id_log)->update([
            $kolomStatus => $request->status,
            'nomor_urut_pegawai_penyetuju' => $user->nomor_urut_pegawai,
            'komentar' => $request->catatan ?? ucfirst($request->status),
            $kolomWaktu => now(),
        ]);

        // 2. LOGIKA JIKA DITOLAK (INSERT BARIS BARU SEBAGAI MANAGER)
        if ($request->status === 'ditolak') {
            \DB::table($tabel)->insert(array_merge($insertBase, [
                'tahap_persetujuan' => 'Manager', // GUE KUNCI JADI MANAGER, BUKAN MANAJER UMUM
                'nomor_urut_pegawai_penyetuju' => $user->nomor_urut_pegawai,
                $kolomStatus => 'ditolak',
                'komentar' => $request->catatan ?? "Ditolak oleh Manager",
            ]));

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Pengajuan ditolak oleh Manager.']);
            }
            return redirect()->route('manager.manajemenpengajuan')->with('error', 'Pengajuan ditolak.');
        }

        // 3. LOGIKA ESTAFET (Hanya jika disetujui)
        if ($request->status === 'disetujui') {
            $pemohon = \DB::table('users')->where('nomor_urut_pegawai', $logLama->nomor_urut_pegawai)->first();
            $isManagerPemohon = ($pemohon && $pemohon->level_id == 2);
            $type = ucfirst($sumber);

            // --- PENENTUAN FLOW DINAMIS ---
            if ($type === 'Lembur') {
                $flow = $isManagerPemohon
                    ? ['Pengajuan Awal', 'Kepala SKK & SKKMR', 'Direktur Operasional', 'HRO']
                    : ['Pengajuan Awal', 'Manager', 'Kepala SKK & SKKMR', 'Direktur Operasional', 'HRO'];
            }
            elseif ($type === 'Pangkatgajitunjangan' || $type === 'Pensiun') {
                $flow = ['Pengajuan Awal', 'Kepala SKK & SKKMR', 'Direktur Kepatuhan', 'Direktur Utama', 'HRO'];
            }
            elseif ($type === 'Cuti') {
                $flow = ['Pengajuan Awal', 'Manager', 'HRO'];
            } else {
                $flow = ['Pengajuan Awal', 'HRO'];
            }

            $currentIndex = array_search($logLama->tahap_persetujuan, $flow);
            $nextTahap = ($currentIndex !== false && isset($flow[$currentIndex + 1])) ? $flow[$currentIndex + 1] : 'Selesai';

            // --- LOGIKA LOMPATAN ---
            if ($logLama->tahap_persetujuan === 'Pengajuan Awal' && $nextTahap === 'Manager') {
                // Insert bukti Manager Setuju (PAKAI NAMA 'Manager')
                \DB::table($tabel)->insert(array_merge($insertBase, [
                    'tahap_persetujuan' => 'Manager',
                    'nomor_urut_pegawai_penyetuju' => $user->nomor_urut_pegawai,
                    $kolomStatus => 'disetujui',
                    'komentar' => 'Disetujui oleh Manager',
                ]));

                // Kirim ke Tahap setelah Manager
                $afterManager = $flow[$currentIndex + 2] ?? 'Selesai';
                if ($afterManager !== 'Selesai') {
                    \DB::table($tabel)->insert(array_merge($insertBase, [
                        'tahap_persetujuan' => $afterManager,
                        $kolomStatus => 'diproses',
                        'komentar' => 'Menunggu verifikasi ' . $afterManager,
                    ]));
                }
            } else {
                // --- LOGIKA STANDAR ---
                if ($nextTahap !== 'Selesai') {
                    \DB::table($tabel)->insert(array_merge($insertBase, [
                        'tahap_persetujuan' => $nextTahap,
                        $kolomStatus => 'diproses',
                        'komentar' => 'Menunggu verifikasi ' . $nextTahap,
                    ]));
                } else {
                    // Final Approved di Tabel Utama
                    $tabelUtama = 'pengajuan_' . $sumber;
                    \DB::table($tabelUtama)->where('nomor_urut_pegawai', $logLama->nomor_urut_pegawai)->update(['status' => 'disetujui']);

                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['success' => true, 'message' => 'Pengajuan Selesai Disetujui.']);
                    }
                    return redirect()->route('manager.manajemenpengajuan')->with('success', 'Pengajuan Selesai.');
                }
            }
        }

        if ($request->ajax() || $request->wantsJson()) return response()->json(['success' => true]);
        return redirect()->route('manager.manajemenpengajuan')->with('success', 'Data berhasil diproses.');
    }

}

