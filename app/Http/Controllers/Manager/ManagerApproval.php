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

        // 1. Tentukan Keyword Tahap (Sesuai Logika Lu)
        $isLintasDivisi = false;
        $allowedSources = [];
        $searchTahap = [];

        // --- 1. Tentukan Keyword Tahap (HRO HARUS DI ATAS) ---
        if (str_contains($namaJabatanLower, 'hro') || str_contains($namaJabatanLower, 'human resources')) {
            $isLintasDivisi = true;
            $allowedSources = ['cuti', 'lembur', 'pensiun', 'pangkatgajitunjangan'];

            // Keyword pencarian tahap untuk HRO
            $searchTahap = ['%HRO%', 'HRO', '%Human Resources%'];

        } elseif (str_contains($namaJabatanLower, 'kepatuhan') || str_contains($namaJabatanLower, 'skk')) {
            $isLintasDivisi = true;
            $allowedSources = ['lembur', 'pensiun', 'pangkatgajitunjangan']; // Sesuaikan slug-nya
            $searchTahap = ['Kepala SKK & SKKMR', 'Kepala Satker Kepatuhan & M.R.', 'Pengajuan Awal'];

        } else {
            $isLintasDivisi = false;
            $allowedSources = ['cuti', 'lembur'];
            $searchTahap = ['Manager', 'Pengajuan Awal', '%Audit%'];
        }

        // 2. Konfigurasi Tabel Log (Sesuai Logika Lu)
        $configTables = [
            ['log' => 'log_persetujuan_cuti', 'st' => 'status_pengajuan', 'lb' => 'Cuti', 'time' => 'updated_at', 'slug' => 'cuti'],
            ['log' => 'log_persetujuan_lembur', 'st' => 'status_persetujuan', 'lb' => 'Lembur', 'time' => 'updated_at', 'slug' => 'lembur'],
            ['log' => 'log_persetujuan_pensiun', 'st' => 'status_persetujuan', 'lb' => 'Pensiun', 'time' => 'update_at', 'slug' => 'pensiun'],
            ['log' => 'log_persetujuan_pangkatgajitunjangan', 'st' => 'status_persetujuan', 'lb' => 'Pangkat/Gaji/Tunjangan', 'time' => 'updated_at', 'slug' => 'pangkatgajitunjangan'],
        ];

        $queries = [];
        $totalMenunggu = 0; $detailMenunggu = [];

        foreach ($configTables as $cfg) {
            if (!in_array($cfg['slug'], $allowedSources)) { continue; }

            $baseQuery = \DB::table($cfg['log'] . ' as log')
                ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->join('divisi as d', 'pek.id_divisi', '=', 'd.id_divisi')
                ->where('log.' . $cfg['st'], 'diproses')
                // KUNCI 1: Harus Belum Disentuh (Penyetuju NULL)
                ->whereNull('log.nomor_urut_pegawai_penyetuju')
                ->where('p.nomor_urut_pegawai', '!=', $user->nomor_urut_pegawai);

            // KUNCI 2: BUNGKUS LOGIKA TAHAP DALAM GROUP WHERE
            $baseQuery->where(function($queryGroup) use ($searchTahap, $cfg, $namaJabatanLower) {
                $queryGroup->where(function($sub) use ($searchTahap, $namaJabatanLower) {
                    foreach ($searchTahap as $st) {
                        // Sekarang $namaJabatanLower sudah dikenal di dalam sini
                        if ((str_contains($namaJabatanLower, 'skk') || str_contains($namaJabatanLower, 'kepatuhan')) && $st === 'Pengajuan Awal') {
                            $sub->orWhere(function($qLevel) {
                                // SKK cuma narik 'Pengajuan Awal' punya Manager (Level 2)
                                // p.level_id ini ada di tabel pegawai yang sudah lu join sebagai 'p'
                                $qLevel->where('log.tahap_persetujuan', 'Pengajuan Awal')
                                    ->where('p.level_id', 2);
                            });
                        } else {
                            $sub->orWhere('log.tahap_persetujuan', 'LIKE', $st);
                        }
                    }
                });

                // Tambahan Khusus SKK: Cegah liat jatah Direktur
                if (str_contains($namaJabatanLower, 'skk') || str_contains($namaJabatanLower, 'kepatuhan')) {
                    if (in_array($cfg['slug'], ['pensiun', 'pangkatgajitunjangan'])) {
                        $queryGroup->orWhere('log.tahap_persetujuan', 'Pengajuan Awal');
                    }
                    // KUNCI 3: Akun SKK dilarang narik jatah Direktur biar data gak nyangkut
                    $queryGroup->where('log.tahap_persetujuan', 'NOT LIKE', '%Direktur%');
                }
            });

            if (!$isLintasDivisi) { $baseQuery->where('pek.id_divisi', $idDivisiManager); }

            $countWait = (clone $baseQuery)->count();
            $totalMenunggu += $countWait;
            $detailMenunggu[] = ['label' => $cfg['lb'], 'jumlah' => $countWait];
            $columnId = ($cfg['slug'] === 'cuti') ? 'id_cuti' : 'id';

            $q = (clone $baseQuery)->select(
                "log.{$columnId} as id_transaksi", // UPDATE: Menggunakan kolom yang benar berdasarkan tabel
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

        // Variabel Pendukung Blade (UTUH)
        $totalDisetujui = 0; $totalDitolak = 0;
        $detailDisetujui = []; $detailDitolak = [];
        $isHRO = str_contains($namaJabatanLower, 'hro');
        $isSKK = str_contains($namaJabatanLower, 'kepatuhan') || str_contains($namaJabatanLower, 'skk');
        $isManager = !$isHRO && !$isSKK;

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
        $primaryKeyLog = ($sumber === 'cuti') ? 'id_cuti' : 'id';
        $logData = \DB::table($tabelLog)->where($primaryKeyLog, $id_log)->first();
        if (!$logData) return redirect()->back()->with('error', 'Log tidak ditemukan.');

        if ($sumber === 'cuti') {
            $query = \DB::table('pengajuan_cuti as pc')
                ->join($tabelLog . ' as log', 'pc.id_cuti', '=', 'log.id_cuti')
                ->leftJoin('pegawai as p', 'pc.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->leftJoin('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->leftJoin('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
                ->leftJoin('jenis_cuti as jc', 'pc.jenis_cuti', '=', 'jc.id')
                ->where('log.id_cuti', $id_log);

        } elseif ($sumber === 'lembur') {
            $query = \DB::table('pengajuan_lembur as pl')
                ->join($tabelLog . ' as log', 'pl.id_lembur', '=', 'log.id_lembur')
                ->leftJoin('pegawai as p', 'pl.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->leftJoin('pekerjaan as pek', 'pl.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->leftJoin('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
                ->where('log.id', $id_log);

        } elseif ($sumber === 'pensiun') {
            $query = \DB::table('pengajuan_pensiun as main')
                ->join('log_persetujuan_pensiun as log', 'main.id_pensiun', '=', 'log.id_pensiun') // ✨ Diganti id_pensiun
                ->leftJoin('pegawai as p', 'main.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->leftJoin('pekerjaan as pek', 'main.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->leftJoin('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
                ->where('log.id', $id_log);

            $query->select('p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi', 'main.*', 'log.status_persetujuan as status', 'log.' . $kolomWaktu . ' as tanggal_proses', 'log.tahap_persetujuan', 'log.id_pensiun'); // ✨ Diganti id_pensiun

        } else {
            // Ini biarkan untuk Pangkat/Gaji/Tunjangan (Jika belum diubah DB-nya)
            $query = \DB::table('pengajuan_pangkatgajitunjangan as main')
                ->join('log_persetujuan_pangkatgajitunjangan as log', 'main.id_pengajuan', '=', 'log.id_pengajuan')
                ->leftJoin('pegawai as p', 'main.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->leftJoin('pekerjaan as pek', 'main.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->leftJoin('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
                ->where('log.id', $id_log);

            $query->select('p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi', 'main.*', 'log.status_persetujuan as status', 'log.updated_at as tanggal_proses', 'log.tahap_persetujuan', 'log.id_pengajuan');
        }

        // Filter Akses Manager (Biar gak mental)
        if (!$isLintasDivisi) {
            $query->where(function($q) use ($idDivisiManager, $user) {
                $q->where('pek.id_divisi', $idDivisiManager)
                ->orWhere('log.nomor_urut_pegawai_penyetuju', $user->nomor_urut_pegawai);
            });
        }

        if ($sumber === 'cuti') {
            $query->select('p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi', 'pc.*', 'jc.nama_cuti as jenis_cuti_nama', 'log.status_pengajuan as status', 'log.updated_at as tanggal_proses', 'log.tahap_persetujuan', 'log.komentar');
        } elseif ($sumber === 'lembur') {
            $query->select('p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi', 'pl.*', 'log.status_persetujuan as status', 'log.updated_at as tanggal_proses', 'log.tahap_persetujuan', 'log.id_lembur');
        } else {
            $query->select('p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi', 'main.*', 'log.status_persetujuan as status', 'log.' . $kolomWaktu . ' as tanggal_proses', 'log.tahap_persetujuan', 'log.id_pensiun');
        }

        $data = $query->first();

        if (!$data) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // 5. AMBIL SEMUA HISTORI LOG (Untuk Stepper Tracking)
        $queryHistory = \DB::table($tabelLog);

        if ($sumber === 'cuti') {
            $queryHistory->where('id_cuti', $data->id_cuti);
        } elseif ($sumber === 'lembur' && isset($data->id_lembur)) {
            $queryHistory->where('id_lembur', $data->id_lembur);
        } elseif ($sumber === 'pensiun' && isset($data->id_pensiun)) {
            $queryHistory->where('id_pensiun', $data->id_pensiun);
        } else {
            // Ini untuk Pangkat/Gaji/Tunjangan yang masih pakai id_pengajuan
            if (isset($data->id_pengajuan)) {
                $queryHistory->where('id_pengajuan', $data->id_pengajuan);
            }
        }
        $orderByCol = ($sumber === 'cuti') ? 'id_cuti' : 'id';
        $historiLog = $queryHistory->orderBy($orderByCol, 'asc')->get();

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
        // 1. Validasi Input
        $rules = [
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string'
        ];

        if ($sumber === 'lembur' && $request->status === 'disetujui') {
            $rules['jam_mulai'] = 'nullable';
            $rules['jam_selesai'] = 'nullable';
            $rules['total_jam_lembur'] = 'nullable';
        }

        $request->validate($rules);
        $user = auth()->user();

        // 2. Tentukan Tabel Log & Kolom Status
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

        $primaryKeyLog = ($sumber === 'cuti') ? 'id_cuti' : 'id';
        $logLama = \DB::table($tabel)->where($primaryKeyLog, $id_log)->first();
        if (!$logLama) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);

        // --- 3. LOGIKA KHUSUS LEMBUR (UPDATE TABEL UTAMA) ---
        if ($sumber === 'lembur' && $request->status === 'disetujui') {
            $pengajuanAsli = \DB::table('pengajuan_lembur')->where('id_lembur', $logLama->id_lembur)->first();

            $jamMulaiFinal = $request->jam_mulai ?? $pengajuanAsli->jam_mulai;
            $jamSelesaiFinal = $request->jam_selesai ?? $pengajuanAsli->jam_selesai;
            $totalJamFinal = $request->total_jam_lembur ?? $pengajuanAsli->total_jam_lembur;

            \DB::table('pengajuan_lembur')
                ->where('id_lembur', $logLama->id_lembur)
                ->update([
                    'jam_mulai' => $jamMulaiFinal,
                    'jam_selesai' => $jamSelesaiFinal,
                    'total_jam_lembur' => $totalJamFinal,
                    'updated_at' => now()
                ]);
        }

        $role = \DB::table('roles_mapping')->where('jabatan_id', $user->jabatan_id)->where('level_id', $user->level_id)->first();
        $namaTahapAksi = $role->role_name ?? 'Manager';

        if (str_contains(strtolower($namaTahapAksi), 'manajer') || str_contains(strtolower($namaTahapAksi), 'manager')) {
            $namaTahapAksi = 'Manager';
        }

        // Jika catatan kosong, buat teks default sesuai status
        $teksDefault = ($request->status === 'disetujui')
                        ? "Disetujui oleh " . $namaTahapAksi
                        : "Ditolak oleh " . $namaTahapAksi;

        $komentarFinal = $request->filled('catatan') ? $request->catatan : $teksDefault;

        // --- 4. UPDATE BARIS LOG SAAT INI ---
        \DB::table($tabel)->where($primaryKeyLog, $id_log)->update([
            $kolomStatus => $request->status,
            'nomor_urut_pegawai_penyetuju' => $user->nomor_urut_pegawai,
            'komentar' => $komentarFinal,
            $kolomWaktu => now(),
        ]);

        // --- 5. LOGIKA JIKA DITOLAK ---
        if ($request->status === 'ditolak') {
            if ($request->ajax() || $request->wantsJson()) return response()->json(['success' => true]);
            return redirect()->route('manager.manajemenpengajuan')->with('error', 'Pengajuan telah ditolak.');
        }

        // --- 6. LOGIKA DISETUJUI (ESTAFET FLOW) ---
        if ($request->status === 'disetujui') {
            $pemohon = \DB::table('users')->where('nomor_urut_pegawai', $logLama->nomor_urut_pegawai)->first();
            $isManagerPemohon = ($pemohon && $pemohon->level_id == 2);

            // A. Penentuan Flow
            if (ucfirst($sumber) === 'Lembur') {
                $isAudit = ($pemohon && str_contains(strtolower($pemohon->jabatan ?? ''), 'audit'));
                if ($isAudit) {
                    $flow = ['Pengajuan Awal', 'Kepala SK Audit', 'Kepala SKK & SKKMR', 'Direktur Operasional', 'HRO'];
                } elseif ($isManagerPemohon) {
                    $flow = ['Pengajuan Awal', 'Kepala SKK & SKKMR', 'Direktur Operasional', 'HRO'];
                } else {
                    $flow = ['Pengajuan Awal', 'Manager', 'Kepala SKK & SKKMR', 'Direktur Operasional', 'HRO'];
                }
            } elseif (ucfirst($sumber) === 'Cuti') {
                $flow = $isManagerPemohon ? ['Pengajuan Awal', 'Direktur Operasional', 'HRO'] : ['Pengajuan Awal', 'Manager', 'Direktur Operasional', 'HRO'];
            } else {
                $flow = ['Pengajuan Awal', 'Kepala SKK & SKKMR', 'Direktur Kepatuhan', 'Direktur Utama', 'HRO'];
            }

            $isRoleManager = str_contains(strtolower($namaTahapAksi), 'manager');
            $isRoleSKK = str_contains(strtolower($namaTahapAksi), 'skk') || str_contains(strtolower($namaTahapAksi), 'kepatuhan');

            // C. Cari Tahap Selanjutnya
            $tahapLama = $logLama->tahap_persetujuan;
            $currentIndex = array_search($tahapLama, $flow);
            if ($currentIndex === false && $isRoleSKK) $currentIndex = array_search('Kepala SKK & SKKMR', $flow);

            $nextTahap = ($currentIndex !== false && isset($flow[$currentIndex + 1])) ? $flow[$currentIndex + 1] : 'Selesai';

            // D. Persiapan Insert Base
            $insertBase = [
                'nomor_urut_pegawai' => $logLama->nomor_urut_pegawai,
                $kolomWaktu => now()
            ];

            if ($sumber === 'cuti') {
                $insertBase['id_cuti'] = $logLama->id_cuti;
            } elseif ($sumber === 'lembur') {
                $insertBase['id_lembur'] = $logLama->id_lembur;
            } else {
                $insertBase['id_pengajuan'] = $logLama->id_pengajuan ?? null;
            }

            // E. INSERT BUKTI ACTION
            if ($logLama->tahap_persetujuan === 'Pengajuan Awal') {
                \DB::table($tabel)->insert(array_merge($insertBase, [
                    'tahap_persetujuan' => $namaTahapAksi,
                    'nomor_urut_pegawai_penyetuju' => $user->nomor_urut_pegawai,
                    $kolomStatus => 'disetujui',
                    'komentar' => $komentarFinal,
                ]));

                $isNextSelf = ($nextTahap === 'Manager' && $isRoleManager) || ($nextTahap === 'Kepala SKK & SKKMR' && $isRoleSKK);
                if ($isNextSelf) $nextTahap = $flow[$currentIndex + 2] ?? 'Selesai';
            }

            // F. INSERT ANTRIAN TAHAP BERIKUTNYA
            if ($nextTahap !== 'Selesai') {
                \DB::table($tabel)->insert(array_merge($insertBase, [
                    'tahap_persetujuan' => $nextTahap,
                    'nomor_urut_pegawai_penyetuju' => null,
                    $kolomStatus => 'diproses',
                    'komentar' => 'Menunggu verifikasi ' . $nextTahap,
                ]));
            }
        }

        if ($request->ajax() || $request->wantsJson()) return response()->json(['success' => true]);
        return redirect()->route('manager.manajemenpengajuan')->with('success', 'Berhasil diproses.');
    }

}

