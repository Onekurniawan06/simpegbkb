<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DirekturApproval extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Ambil data role yang SANGAT SPESIFIK sesuai level dan jabatan user
        $role = DB::table('roles_mapping')
            ->where('level_id', $user->level_id)
            ->where('jabatan_id', $user->jabatan_id)
            ->first();

        // Jika tidak ketemu di mapping, ambil default dari tabel jabatan
        if (!$role) {
            $dataJabatan = DB::table('jabatan')->where('jabatan_id', $user->jabatan_id)->first();
            $jabatanAsli = $dataJabatan->nama_jabatan ?? 'Direktur';
        } else {
            $jabatanAsli = $role->role_name; // Ini akan mengambil "Direktur Operasional" dari tabel Anda
        }

        // 2. NORMALISASI (Agar sinkron dengan teks di tabel Log)
        if (stripos($jabatanAsli, 'utama') !== false) {
            $jabatanLogin = 'Direktur Utama';
        } elseif (stripos($jabatanAsli, 'operasional') !== false) {
            $jabatanLogin = 'Direktur Operasional';
        } elseif (stripos($jabatanAsli, 'kepatuhan') !== false) {
            $jabatanLogin = 'Direktur Kepatuhan';
        } else {
            $jabatanLogin = $jabatanAsli; // Gunakan nama asli jika tidak cocok dengan 3 di atas
        }

        // 2. DEFINISI TABEL UNTUK STATISTIK & QUERY
        $tables = [
            ['name' => 'log_persetujuan_lembur', 'jenis' => 'Lembur', 'status_col' => 'status_persetujuan', 'sumber' => 'lembur'],
            ['name' => 'log_persetujuan_cuti', 'jenis' => 'Cuti', 'status_col' => 'status_pengajuan', 'sumber' => 'cuti'],
            ['name' => 'log_persetujuan_pensiun', 'jenis' => 'Pensiun', 'status_col' => 'status_persetujuan', 'sumber' => 'pensiun'],
            ['name' => 'log_persetujuan_pangkatgajitunjangan', 'jenis' => 'Kenaikan', 'status_col' => 'status_persetujuan', 'sumber' => 'pangkat'],
        ];

        $queries = [];
        $totalMenunggu = 0;
        $detailMenunggu = [];

        // dd($jabatanLogin);
        foreach ($tables as $t) {
            $jabatanUser = strtolower($jabatanLogin); // Sekarang isinya pasti ada kata 'operasional' dsb.

            // --- HAK AKSES KHUSUS DIREKTUR ---
            if (in_array($t['jenis'], ['Lembur', 'Cuti'])) {
                if (strpos($jabatanUser, 'operasional') === false) {
                    continue;
                }
            }

            if (in_array($t['jenis'], ['Pensiun', 'Kenaikan'])) {
                if (strpos($jabatanUser, 'kepatuhan') === false) {
                    continue;
                }
            }

            // --- CEK KOLOM TANGGAL SECARA REAL-TIME ---
            $dateColumn = null;
            if (Schema::hasColumn($t['name'], 'created_at')) {
                $dateColumn = $t['name'] . '.created_at';
            } elseif (Schema::hasColumn($t['name'], 'updated_at')) {
                $dateColumn = $t['name'] . '.updated_at';
            } else {
                $dateColumn = 'NULL';
            }

            // A. HITUNG STATISTIK
            $count = DB::table($t['name'])
                ->where('tahap_persetujuan', 'LIKE', '%' . $jabatanLogin . '%')
                ->where($t['status_col'], 'diproses')
                ->count();

            $totalMenunggu += $count;
            $detailMenunggu[] = ['label' => $t['jenis'], 'jumlah' => $count];

            // B. LOGIKA FILTER & TABEL
            if ($request->filled('jenis') && $request->jenis != $t['jenis']) {
                continue;
            }

            $columnId = ($t['sumber'] === 'cuti') ? 'id_cuti' : 'id';
            $query = DB::table($t['name'])
            ->join('pegawai', $t['name'] . '.nomor_urut_pegawai', '=', 'pegawai.nomor_urut_pegawai')
            ->select(
                $t['name'] . ".{$columnId} as id_transaksi", // UPDATE: Dinamis menggunakan id_cuti untuk cuti
                DB::raw("$dateColumn as tanggal"),
                'pegawai.nomor_urut_pegawai as nup',
                'pegawai.nama as nama',
                DB::raw("'" . $t['jenis'] . "' as jenis"),
                $t['name'] . '.' . $t['status_col'] . ' as status',
                DB::raw("'" . $t['sumber'] . "' as sumber")
            )
            ->where($t['name'] . '.tahap_persetujuan', 'LIKE', '%' . $jabatanLogin . '%')
            ->where($t['name'] . '.' . $t['status_col'], 'diproses');

            // Filter Search
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('pegawai.nama', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('pegawai.nomor_urut_pegawai', 'LIKE', '%' . $request->search . '%');
                });
            }

            // Filter Tanggal (Hanya jika kolom tanggal ditemukan)
            if ($dateColumn !== 'NULL' && $request->filled('start_date')) {
                $query->whereDate($dateColumn, '>=', $request->start_date);
            }
            if ($dateColumn !== 'NULL' && $request->filled('end_date')) {
                $query->whereDate($dateColumn, '<=', $request->end_date);
            }

            $queries[] = $query;
        }

        // 3. EKSEKUSI DATA TABEL
        if (empty($queries)) {
            $dataPengajuan = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        } else {
            $fullQuery = array_shift($queries);
            foreach ($queries as $q) { $fullQuery->unionAll($q); }

            // Bungkus dalam DB::table agar bisa dipaginate dengan benar setelah Union
            $dataPengajuan = DB::table(DB::raw("({$fullQuery->toSql()}) as combined"))
                ->mergeBindings($fullQuery)
                ->orderBy('tanggal', 'desc')
                ->paginate(10) // <-- GANTI DISINI (Misal 10 data per halaman)
                ->withQueryString(); // Agar filter pencarian tidak hilang saat pindah halaman
        }

        return view('direktur.manajemenpengajuan', compact(
            'dataPengajuan',
            'totalMenunggu',
            'detailMenunggu',
            'jabatanLogin'
        ));
    }

    public function direkturapproval($sumber, $id_log)
    {
        $user = auth()->user();
        $id_cuti = $id_log; // Alias untuk memperjelas

        // 1. Tentukan Tabel Log
        $tabelLog = match($sumber) {
            'cuti'   => 'log_persetujuan_cuti',
            'lembur'  => 'log_persetujuan_lembur',
            default   => 'log_persetujuan_cuti'
        };

        // 2. AMBIL DATA LOG (KUNCI: Jangan filter status 'diproses' di sini!)
        $primaryKeyLog = ($sumber === 'cuti') ? 'id_cuti' : 'id';

        $logData = \DB::table($tabelLog)
            ->where($primaryKeyLog, $id_log)
            ->where('tahap_persetujuan', 'LIKE', '%Direktur Operasional%')
            ->first();

        if (!$logData) {
            // Pakai abort agar jika data memang tidak ada, loop langsung putus (muncul 404)
            abort(404, 'Data log tidak ditemukan atau sudah diproses ke tahap selanjutnya.');
        }

        // 3. QUERY UTAMA UNTUK VIEW (Sama seperti sebelumnya, tapi tanpa filter status diproses)
        if ($sumber === 'cuti') {
            $query = \DB::table('pengajuan_cuti as pc')
                ->join($tabelLog . ' as log', 'pc.id_cuti', '=', 'log.id_cuti')
                ->leftJoin('pegawai as p', 'pc.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->leftJoin('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->leftJoin('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
                ->leftJoin('jenis_cuti as jc', 'pc.jenis_cuti', '=', 'jc.id')
                ->where('log.id_cuti', $id_log);

            $data = $query->select(
                'p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi',
                'pc.*', 'jc.nama_cuti as jenis_cuti_nama',
                'log.status_pengajuan as status',
                'log.updated_at as tanggal_proses', 'log.tahap_persetujuan', 'log.komentar'
            )->first();

        } elseif ($sumber === 'lembur') {
            $query = \DB::table('pengajuan_lembur as pl')
                ->join($tabelLog . ' as log', 'pl.id_lembur', '=', 'log.id_lembur') // ✨ Sesuai database baru
                ->leftJoin('pegawai as p', 'pl.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->leftJoin('pekerjaan as pek', 'pl.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->leftJoin('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
                ->where('log.id', $id_log);

            $data = $query->select(
                'p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi',
                'pl.*', 'log.status_persetujuan as status',
                'log.updated_at as tanggal_proses', 'log.tahap_persetujuan', 'log.komentar', 'log.id_lembur'
            )->first();
        }

        if (!$data) abort(404, 'Data detail pengajuan tidak ditemukan.');

        // 4. AMBIL HISTORI LOG
        $queryHistory = \DB::table($tabelLog);
        if ($sumber === 'cuti') {
            $queryHistory->where('id_cuti', $data->id_cuti);
        } else {
            $queryHistory->where('id_lembur', $data->id_lembur);
        }
        $historiLog = $queryHistory->orderBy('id', 'asc')->get();

        // 5. Variabel Pendukung Blade (TETAP UTUH)
        $pageTitle = 'Detail Pengajuan ' . ucfirst($sumber);
        $tahapTeks = "Verifikasi Direktur Operasional";
        $breadcrumbs = [
            'Beranda' => route('direktur.dashboarddirektur'),
            'Manajemen Pengajuan' => route('direktur.manajemenpengajuanpegawai'),
            'Detail Pengajuan' => '#',
        ];

        return view('direktur.direktur_approval', compact(
            'data', 'sumber', 'pageTitle', 'id_log',
            'breadcrumbs', 'tahapTeks', 'historiLog', 'tabelLog'
        ));
    }

    public function updateStatus(Request $request, $id, $jenis)
    {
        try {
            $status = $request->status;
            $tabelLog = '';
            $kolomStatusLog = 'status_persetujuan';

            $jenisNormalized = ucfirst(strtolower($jenis));

            // 1. Tentukan Tabel Log & Kolom PK yang benar
            $primaryKeyLog = ($jenisNormalized === 'Cuti') ? 'id_cuti' : 'id';

            switch ($jenisNormalized) {
                case 'Lembur':
                    $tabelLog = 'log_persetujuan_lembur';
                    break;
                case 'Cuti':
                    $tabelLog = 'log_persetujuan_cuti';
                    $kolomStatusLog = 'status_pengajuan';
                    break;
                case 'Pensiun':
                    $tabelLog = 'log_persetujuan_pensiun';
                    break;
                case 'Pangkat':
                    $tabelLog = 'log_persetujuan_pangkatgajitunjangan';
                    break;
            }

            // Ambil data lama berdasarkan filter tahap agar tidak salah baris
            $oldLog = \DB::table($tabelLog)
                ->where($primaryKeyLog, $id)
                ->where('tahap_persetujuan', 'LIKE', '%Direktur Operasional%')
                ->first();

            if (!$oldLog) throw new \Exception("Data log tidak ditemukan atau sudah diproses.");

            \DB::beginTransaction();

            // --- LOGIKA CATATAN OTOMATIS DIREKTUR ---
            $teksDefault = ($status === 'disetujui')
                            ? 'Disetujui oleh Direktur Operasional'
                            : 'Ditolak oleh Direktur Operasional';

            $komentarFinal = $request->filled('catatan') ? $request->catatan : $teksDefault;

            // 2. ACTION 1: UPDATE BARIS DIREKTUR
            $updateLog = [
                $kolomStatusLog => $status,
                'nomor_urut_pegawai_penyetuju' => auth()->user()->nomor_urut_pegawai,
                'komentar' => $komentarFinal, // Menggunakan hasil filter catatan tadi
            ];

            if (\Schema::hasColumn($tabelLog, 'updated_at')) {
                $updateLog['updated_at'] = now();
            } elseif (\Schema::hasColumn($tabelLog, 'update_at')) {
                $updateLog['update_at'] = now();
            }

            \DB::table($tabelLog)->where('id', $oldLog->id)->update($updateLog);

            // 3. ACTION 2: JIKA DISETUJUI -> TAMBAH BARIS BARU UNTUK HRO
            if ($status === 'disetujui') {
                $dataInsert = [
                    'nomor_urut_pegawai' => $oldLog->nomor_urut_pegawai,
                    'tahap_persetujuan'  => 'HRO',
                    $kolomStatusLog      => 'diproses',
                    'komentar'           => 'Menunggu verifikasi HRO',
                ];

                if ($jenisNormalized === 'Cuti') {
                    $dataInsert['id_cuti'] = $oldLog->id_cuti;
                }

                if (isset($oldLog->id_lembur)) { $dataInsert['id_lembur'] = $oldLog->id_lembur; }
                if (isset($oldLog->id_pengajuan)) { $dataInsert['id_pengajuan'] = $oldLog->id_pengajuan; }
                if (isset($oldLog->user_id)) { $dataInsert['user_id'] = $oldLog->user_id; }

                // Set waktu insert
                if (\Schema::hasColumn($tabelLog, 'updated_at')) {
                    $dataInsert['updated_at'] = now();
                }

                \DB::table($tabelLog)->insert($dataInsert);
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Pengajuan berhasil " . strtoupper($status),
                'redirect' => '/direktur/manajemenpengajuan'
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }


}
