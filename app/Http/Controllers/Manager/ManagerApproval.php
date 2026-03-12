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
        $allowedSources = ['cuti', 'lembur', 'pensiun', 'pangkatgajitunjangan'];
        $searchTahap = [];

        if (str_contains($namaJabatanLower, 'kepatuhan') || str_contains($namaJabatanLower, 'skk')) {
            // KEPALA SATKER: Cari yang mengandung kata 'SKK' atau 'MR'
            $searchTahap = ['%SKK%', '%MR%', '%Kepatuhan%'];
            $allowedSources = ['lembur', 'pensiun', 'pangkatgajitunjangan'];
            $isLintasDivisi = true;
        } elseif (str_contains($namaJabatanLower, 'hro') || str_contains($namaJabatanLower, 'human resources')) {
            $searchTahap = ['%HRO%'];
            $isLintasDivisi = true;
        } elseif (str_contains($namaJabatanLower, 'direktur')) {
            $searchTahap = ['%Direktur%'];
            $isLintasDivisi = true;
        } else {
            // Manager Biasa
            $searchTahap = ['Manager', 'Pengajuan Awal'];
            $isLintasDivisi = false;
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
                ->where(function($q) use ($searchTahap) {
                    foreach ($searchTahap as $st) {
                        $q->orWhere('log.tahap_persetujuan', 'LIKE', $st);
                    }
                })
                ->where('log.' . $cfg['st'], 'diproses');

            // Lintas Divisi: Jika bukan SKK/Direktur, filter per divisi
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

        return view('manager.manajemenpengajuanpegawai', compact(
            'totalMenunggu', 'detailMenunggu', 'totalDisetujui', 'totalDitolak',
            'detailDisetujui', 'detailDitolak', 'dataPengajuan', 'dashboardRoute'
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
            $tahapTeks = "Verifikasi Kepala SKK & MR";
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
                ->join($tabelLog . ' as log', 'pc.nomor_urut_pegawai', '=', 'log.nomor_urut_pegawai')
                ->where('log.id', $id_log)
                ->select('p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi', 'pc.*', 'log.status_pengajuan as status', 'log.updated_at as tanggal_proses', 'log.tahap_persetujuan');
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

        // KUNCI 1: Nama kolom status dan waktu berbeda di tiap tabel Anda
        $kolomStatus = ($sumber === 'cuti') ? 'status_pengajuan' : 'status_persetujuan';
        $kolomWaktu = ($sumber === 'pensiun') ? 'update_at' : 'updated_at';

        // 1. Ambil data log yang sedang diproses
        $logLama = \DB::table($tabel)->where('id', $id_log)->first();
        if (!$logLama) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);

        // 2. UPDATE baris yang sedang dibuka menjadi disetujui/ditolak
        \DB::table($tabel)->where('id', $id_log)->update([
            $kolomStatus => $request->status,
            'nomor_urut_pegawai_penyetuju' => $user->nomor_urut_pegawai,
            'komentar' => $request->catatan ?? "Disetujui",
            $kolomWaktu => now(),
        ]);

        // 3. LOGIKA ESTAFET (Hanya jika disetujui)
        if ($request->status === 'disetujui') {
            $pemohon = \DB::table('users')->where('nomor_urut_pegawai', $logLama->nomor_urut_pegawai)->first();
            $isManager = ($pemohon && $pemohon->level_id == 2);
            $type = ucfirst($sumber);

            // KUNCI 2: Penentuan Flow sesuai kesepakatan
            $flow = ['Pengajuan Awal', 'Kepala SKK & MR', 'Direktur Kepatuhan', 'Direktur Utama', 'HRO'];
            if ($isManager) {
                if ($type === 'Pangkatgajitunjangan') $flow = ['Pengajuan Awal', 'Direktur Kepatuhan', 'Direktur Utama', 'HRO'];
                elseif ($type === 'Pensiun') $flow = ['Pengajuan Awal', 'Kepala SKK & MR', 'Direktur Kepatuhan', 'Direktur Utama', 'HRO'];
                elseif ($type === 'Cuti') $flow = ['Pengajuan Awal', 'Direktur Operasional', 'HRO'];
                else $flow = ['Pengajuan Awal', 'Direktur Operasional', 'Direktur Utama', 'HRO']; // Lembur Manager
            } else {
                if (in_array($type, ['Cuti', 'Lembur'])) {
                    $flow = ['Pengajuan Awal', 'Manager', 'Direktur Operasional', 'HRO'];
                    if ($type === 'Lembur') {
                        $flow = array_values(array_map(fn($v) => ($v === 'Direktur Operasional') ? 'Kepala SKK MR' : $v, $flow));
                    }
                }
            }

            $currentIndex = array_search($logLama->tahap_persetujuan, $flow);
            $nextTahap = $flow[$currentIndex + 1] ?? 'Selesai';

            // KUNCI 3: Penanganan ID Relasi (Bebas Error Unknown Column)
            $insertBase = [
                'nomor_urut_pegawai' => $logLama->nomor_urut_pegawai,
                $kolomWaktu => now(),
            ];

            // Hanya masukkan kolom yang ada di tabelnya masing-masing
            if ($sumber === 'lembur') {
                $insertBase['lembur_id'] = $logLama->lembur_id;
            } else {
                // Untuk Cuti, Pensiun, PangkatGaji menggunakan id_pengajuan
                $insertBase['id_pengajuan'] = $logLama->id_pengajuan ?? null;
            }

            // A. JIKA MANAGER APPROVE PENGUJUAN AWAL, LOMPAT KE TAHAP 3
            if ($logLama->tahap_persetujuan === 'Pengajuan Awal' && $nextTahap === 'Manager') {
                // Insert baris Manager (Setuju)
                \DB::table($tabel)->insert(array_merge($insertBase, [
                    'tahap_persetujuan' => 'Manager',
                    'nomor_urut_pegawai_penyetuju' => $user->nomor_urut_pegawai,
                    $kolomStatus => 'disetujui',
                    'komentar' => 'Disetujui oleh Manager',
                ]));

                // Insert Tahap 3 (Diproses)
                $afterManager = $flow[$currentIndex + 2] ?? 'Selesai';
                if ($afterManager !== 'Selesai') {
                    \DB::table($tabel)->insert(array_merge($insertBase, [
                        'tahap_persetujuan' => $afterManager,
                        'nomor_urut_pegawai_penyetuju' => null,
                        $kolomStatus => 'diproses',
                        'komentar' => 'Menunggu verifikasi ' . $afterManager,
                    ]));
                }
            } else {
                // B. LOGIKA STANDAR (Untuk tahap SKK, Direktur, Utama, HRO)
                if ($nextTahap !== 'Selesai') {
                    \DB::table($tabel)->insert(array_merge($insertBase, [
                        'tahap_persetujuan' => $nextTahap,
                        'nomor_urut_pegawai_penyetuju' => null,
                        $kolomStatus => 'diproses',
                        'komentar' => 'Menunggu verifikasi ' . $nextTahap,
                    ]));
                } else {
                    // Final Approved di Tabel Utama
                    $tabelUtama = 'pengajuan_' . $sumber;
                    \DB::table($tabelUtama)->where('nomor_urut_pegawai', $logLama->nomor_urut_pegawai)->update(['status' => 'disetujui']);
                }
            }
        }

        if ($request->ajax() || $request->wantsJson()) return response()->json(['success' => true]);
        return redirect()->route('manager.manajemenpengajuan')->with('success', 'Data berhasil diproses.');
    }

}

