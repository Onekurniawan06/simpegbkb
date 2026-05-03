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

        // 1. Ambil data role
        $role = DB::table('roles_mapping')
            ->where('level_id', $user->level_id)
            ->where('jabatan_id', $user->jabatan_id)
            ->first();

        if (!$role) {
            $dataJabatan = DB::table('jabatan')->where('jabatan_id', $user->jabatan_id)->first();
            $jabatanAsli = $dataJabatan->nama_jabatan ?? 'Direktur';
        } else {
            $jabatanAsli = $role->role_name;
        }

        // 2. NORMALISASI
        if (stripos($jabatanAsli, 'utama') !== false) {
            $jabatanLogin = 'Direktur Utama';
        } elseif (stripos($jabatanAsli, 'operasional') !== false) {
            $jabatanLogin = 'Direktur Operasional';
        } elseif (stripos($jabatanAsli, 'kepatuhan') !== false) {
            $jabatanLogin = 'Direktur Kepatuhan';
        } else {
            $jabatanLogin = $jabatanAsli;
        }

        // 3. DEFINISI TABEL
        $tables = [
            ['name' => 'log_persetujuan_pensiun', 'jenis' => 'Pensiun', 'status_col' => 'status_persetujuan', 'sumber' => 'pensiun'],
            ['name' => 'log_persetujuan_lembur', 'jenis' => 'Lembur', 'status_col' => 'status_persetujuan', 'sumber' => 'lembur'],
            ['name' => 'log_persetujuan_cuti', 'jenis' => 'Cuti', 'status_col' => 'status_pengajuan', 'sumber' => 'cuti'],
            ['name' => 'log_persetujuan_pangkatgajitunjangan', 'jenis' => 'Kenaikan', 'status_col' => 'status_persetujuan', 'sumber' => 'pangkat'],
        ];

        $queries = [];
        $totalMenunggu = 0;
        $detailMenunggu = [];

        foreach ($tables as $t) {
            $jabatanUser = strtolower($jabatanLogin);

            // --- 4. HAK AKSES KHUSUS DIREKTUR (BAGIAN INI YANG SAYA PERBAIKI) ---
            if ($t['jenis'] === 'Cuti') {
                if (strpos($jabatanUser, 'operasional') === false) continue;
            }

            if ($t['jenis'] === 'Lembur') {
                if (strpos($jabatanUser, 'kepatuhan') === false && strpos($jabatanUser, 'operasional') === false) continue;
            }

            if (in_array($t['jenis'], ['Pensiun', 'Kenaikan'])) {
                if (strpos($jabatanUser, 'kepatuhan') === false && strpos($jabatanUser, 'utama') === false) continue;
            }

            // --- 5. CEK KOLOM TANGGAL (TETAP PAKAI LOGIKA LAMA BIAR KAMU GAK BINGUNG) ---
            $dateColumn = null;
            if (Schema::hasColumn($t['name'], 'updated_at')) {
                $dateColumn = $t['name'] . '.updated_at';
            } elseif (Schema::hasColumn($t['name'], 'created_at')) {
                $dateColumn = $t['name'] . '.created_at';
            } else {
                $dateColumn = 'NULL';
            }

            // A. HITUNG STATISTIK (DITAMBAH whereNull BIAR GAK MUNCUL DATA YANG SUDAH DI-APPROVE)
            $count = DB::table($t['name'])
                ->where('tahap_persetujuan', 'LIKE', '%' . $jabatanLogin . '%')
                ->where($t['status_col'], 'diproses')
                ->whereNull('nomor_urut_pegawai_penyetuju')
                ->count();

            $totalMenunggu += $count;
            $detailMenunggu[] = ['label' => $t['jenis'], 'jumlah' => $count];

            if ($request->filled('jenis') && $request->jenis != $t['jenis']) continue;

            $columnId = 'id';

            $query = DB::table($t['name'])
            ->join('pegawai', $t['name'] . '.nomor_urut_pegawai', '=', 'pegawai.nomor_urut_pegawai')
            ->select(
                $t['name'] . ".{$columnId} as id_transaksi",
                DB::raw("$dateColumn as tanggal"),
                'pegawai.nomor_urut_pegawai as nup',
                'pegawai.nama as nama',
                DB::raw("'" . $t['jenis'] . "' as jenis"),
                $t['name'] . '.' . $t['status_col'] . ' as status',
                DB::raw("'" . $t['sumber'] . "' as sumber")
            )
            ->where($t['name'] . '.tahap_persetujuan', 'LIKE', '%' . $jabatanLogin . '%')
            ->where($t['name'] . '.' . $t['status_col'], 'diproses')
            ->whereNull($t['name'] . '.nomor_urut_pegawai_penyetuju'); // Tambahan: Biar gak muncul data lama

            // Filter Search & Tanggal (TETAP SEPERTI KODEMU)
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('pegawai.nama', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('pegawai.nomor_urut_pegawai', 'LIKE', '%' . $request->search . '%');
                });
            }
            if ($dateColumn !== 'NULL' && $request->filled('start_date')) {
                $query->whereDate($dateColumn, '>=', $request->start_date);
            }
            if ($dateColumn !== 'NULL' && $request->filled('end_date')) {
                $query->whereDate($dateColumn, '<=', $request->end_date);
            }

            $queries[] = $query;
        }

        // 6. EKSEKUSI DATA TABEL (TETAP PAKAI UNIONALL SEPERTI KODEMU)
        if (empty($queries)) {
            $dataPengajuan = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        } else {
            $fullQuery = array_shift($queries);
            foreach ($queries as $q) { $fullQuery->unionAll($q); }

            $dataPengajuan = DB::table(DB::raw("({$fullQuery->toSql()}) as combined"))
                ->mergeBindings($fullQuery)
                ->orderBy('tanggal', 'desc')
                ->paginate(10)
                ->withQueryString();
        }

        return view('direktur.manajemenpengajuan', compact(
            'dataPengajuan', 'totalMenunggu', 'detailMenunggu', 'jabatanLogin'
        ));
    }

    public function detailApproval($sumber, $id_log)
    {
        $user = auth()->user();
        $dashboardRoute = 'direktur.dashboarddirektur';

        // 1. Tentukan Tabel Log
        $tabelLog = match(strtolower($sumber)) {
            'cuti' => 'log_persetujuan_cuti',
            'lembur' => 'log_persetujuan_lembur',
            'pensiun' => 'log_persetujuan_pensiun',
            'pangkatgajitunjangan', 'pangkat' => 'log_persetujuan_pangkatgajitunjangan',
            default => abort(404, 'Tipe pengajuan tidak didukung.')
        };

        // 2. QUERY UTAMA UNTUK VIEW
        if (strtolower($sumber) === 'cuti') {
            $query = \DB::table('pengajuan_cuti as pc')
                ->join($tabelLog . ' as log', 'pc.id_cuti', '=', 'log.id_cuti')
                ->leftJoin('pegawai as p', 'pc.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->leftJoin('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->leftJoin('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
                ->leftJoin('jenis_cuti as jc', 'pc.jenis_cuti', '=', 'jc.nama_cuti')
                // PERBAIKAN: Gunakan log.id untuk mencari baris spesifik,
                // bukan log.id_cuti karena satu cuti bisa punya banyak baris log.
                ->where('log.id', $id_log);

            $data = $query->select(
                'p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi',
                'pc.*', // Mengambil saldo_awal, saldo_akhir, jumlah_cuti
                'jc.nama_cuti as jenis_cuti_nama',
                'log.status_pengajuan as status',
                'log.updated_at as tanggal_proses', 'log.tahap_persetujuan', 'log.komentar',
                'log.id as id_log_asli' // PK dari tabel log untuk proses update nanti
            )->first();

        } elseif ($sumber === 'lembur') {
            $query = \DB::table('pengajuan_lembur as pl')
                ->join($tabelLog . ' as log', 'pl.id_lembur', '=', 'log.id_lembur')
                ->leftJoin('pegawai as p', 'pl.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->leftJoin('pekerjaan as pek', 'pl.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->leftJoin('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
                ->where('log.id', $id_log);

            $data = $query->select(
                'p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi',
                'pl.*', 'log.status_persetujuan as status',
                'log.updated_at as tanggal_proses', 'log.tahap_persetujuan', 'log.komentar', 'log.id_lembur'
            )->first();

        } elseif ($sumber === 'pensiun') {
            $query = \DB::table('pengajuan_pensiun as main')
                ->join($tabelLog . ' as log', 'main.id_pensiun', '=', 'log.id_pensiun')
                ->leftJoin('pegawai as p', 'main.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->leftJoin('pekerjaan as pek', 'main.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->leftJoin('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
                ->where('log.id', $id_log);

            $data = $query->select(
                'p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi',
                'main.*', 'log.status_persetujuan as status',
                'log.updated_at as tanggal_proses', 'log.tahap_persetujuan', 'log.komentar', 'log.id_pensiun'
            )->first();

        } else {
            $query = \DB::table('pengajuan_pangkatgajitunjangan as main')
                ->join($tabelLog . ' as log', 'main.id_kenaikan', '=', 'log.id_kenaikan')
                ->leftJoin('pegawai as p', 'main.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->leftJoin('pekerjaan as pek', 'main.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->leftJoin('divisi as div', 'pek.id_divisi', '=', 'div.id_divisi')
                ->where('log.id', $id_log);

            $data = $query->select(
                'p.nama', 'p.nomor_urut_pegawai', 'pek.jabatan', 'div.nama_divisi',
                'main.*', 'log.status_persetujuan as status',
                'log.updated_at as tanggal_proses', 'log.tahap_persetujuan', 'log.komentar', 'log.id_kenaikan'
            )->first();
        }

        if (!$data) abort(404, 'Data detail pengajuan tidak ditemukan.');

        // 4. AMBIL HISTORI LOG
        $queryHistory = \DB::table($tabelLog);

        if ($sumber === 'cuti') {
            $queryHistory->where('id_cuti', $data->id_cuti);
        } elseif ($sumber === 'lembur') {
            $queryHistory->where('id_lembur', $data->id_lembur);
        } elseif ($sumber === 'pensiun') {
            $queryHistory->where('id_pensiun', $data->id_pensiun);
        } else {
            $queryHistory->where('id_kenaikan', $data->id_kenaikan);
        }

        $historiLog = $queryHistory->orderBy('id', 'asc')->get();

        $files = [];
        if ($sumber === 'pensiun' && isset($data->id_pensiun)) {
            $files = \DB::table('file_persyaratanpensiun')
                ->where('id_pensiun', $data->id_pensiun)
                ->get();
        }
        elseif (in_array(strtolower($sumber), ['pangkat', 'pangkatgajitunjangan']) && isset($data->id_kenaikan)) {
            $files = \DB::table('file_persyaratanpangkatgajitunjangan')
                ->where('id_kenaikan', $data->id_kenaikan)
                ->get();
        }

        // 6. Kamus Nama (Pastikan Key-nya sinkron dengan slug baru)
        $sumberNames = [
            'pangkat' => 'Pangkat, Gaji, dan Tunjangan', // ➕ Tambahkan key ringkas
            'pangkatgajitunjangan' => 'Pangkat, Gaji, dan Tunjangan',
            'cuti' => 'Cuti',
            'lembur' => 'Lembur',
            'pensiun' => 'Pensiun',
        ];

        // 6. Kirim Variabel ke View (Tambahkan 'files' ke dalam compact)
        $sumberNames = [
            'pangkatgajitunjangan' => 'Pangkat, Gaji, dan Tunjangan',
            'cuti' => 'Cuti',
            'lembur' => 'Lembur',
            'pensiun' => 'Pensiun',
        ];

        // 7. Paksa $sumber menjadi huruf kecil saat dicocokkan
        $formattedSumber = $sumberNames[strtolower($sumber)] ?? ucfirst($sumber);

        // 8. Kirim Variabel ke View
        $pageTitle = 'Detail Pengajuan ' . $formattedSumber;
        $breadcrumbs = [
            'Beranda' => route($dashboardRoute),
            "Manajemen Pengajuan" => route('direktur.manajemenpengajuan'),
            'Detail Pengajuan ' . $formattedSumber => '#',
        ];
        $tahapTeks = ($data->tahap_persetujuan) ? 'Tahap: ' . $data->tahap_persetujuan : 'Tahap tidak diketahui';

        return view('direktur.detail_approval', compact(
            'data', 'sumber', 'pageTitle', 'id_log',
            'breadcrumbs', 'tahapTeks', 'historiLog', 'tabelLog', 'files'
        ));
    }

    public function updateStatus(Request $request, $sumber, $id_log)
    {
        try {
            $status = $request->status;
            $tabelLog = '';
            $kolomStatusLog = 'status_persetujuan';
            $sumber = strtolower($sumber);

            // 1. Tentukan Tabel Log & Kolom PK
            $primaryKeyLog = ($sumber === 'cuti') ? 'id_cuti' : 'id';

            switch ($sumber) {
                case 'lembur':
                    $tabelLog = 'log_persetujuan_lembur';
                    break;
                case 'cuti':
                    $tabelLog = 'log_persetujuan_cuti';
                    $kolomStatusLog = 'status_pengajuan';
                    break;
                case 'pensiun':
                    $tabelLog = 'log_persetujuan_pensiun';
                    break;
                case 'pangkatgajitunjangan':
                case 'pangkat':
                    $tabelLog = 'log_persetujuan_pangkatgajitunjangan';
                    break;
                default:
                    return response()->json(['success' => false, 'message' => 'Tipe tidak valid: ' . $sumber], 400);
            }

            $oldLog = \DB::table($tabelLog)->where('id', $id_log)->first();

            if (!$oldLog) {
                return response()->json(['success' => false, 'message' => "Data log tidak ditemukan"], 404);
            }

            \DB::beginTransaction();

            // --- 2. SINKRONISASI KE TABEL UTAMA (AGAR STATUS DI DASHBOARD PEGAWAI UPDATE) ---
            if ($sumber === 'cuti') {
                \DB::table('pengajuan_cuti')
                    ->where('id_cuti', $oldLog->id_cuti)
                    ->update([
                        'status_pengajuan' => ($status === 'disetujui') ? 'diproses' : 'ditolak',
                        'updated_at' => now()
                    ]);
            } elseif ($sumber === 'lembur') {
                \DB::table('pengajuan_lembur')
                    ->where('id_lembur', $oldLog->id_lembur)
                    ->update([
                        'status_lembur' => ($status === 'disetujui') ? 'diproses' : 'ditolak',
                        'updated_at' => now()
                    ]);
            } elseif ($sumber === 'pensiun') {
                \DB::table('pengajuan_pensiun')
                    ->where('id_pensiun', $oldLog->id_pensiun)
                    ->update([
                        // Sama seperti lainnya: 'diproses' jika setuju (estafet), 'ditolak' jika tidak
                        'status_pensiun' => ($status === 'disetujui') ? 'diproses' : 'ditolak',
                        'updated_at' => now()
                    ]);
            }elseif ($sumber === 'pangkat' || $sumber === 'pangkatgajitunjangan') {
                \DB::table('pengajuan_pangkatgajitunjangan')
                    ->where('id_kenaikan', $oldLog->id_kenaikan)
                    ->update([
                        // Jika disetujui (Kepatuhan/Utama), status tetap 'diproses' sampai di HRO
                        // Jika ditolak oleh salah satu Direktur, status langsung 'ditolak'
                        'status_kenaikan' => ($status === 'disetujui') ? 'diproses' : 'ditolak',
                        'updated_at' => now()
                    ]);
            }

            // --- 3. LOGIKA CATATAN OTOMATIS ---
            $tahapSekarang = $oldLog->tahap_persetujuan;
            $teksDefault = ($status === 'disetujui')
                            ? 'Disetujui oleh ' . $tahapSekarang
                            : 'Ditolak oleh ' . $tahapSekarang;

            $komentarFinal = $request->filled('catatan') ? $request->catatan : $teksDefault;

            // --- 4. UPDATE BARIS LOG SAAT INI ---
            $updateLog = [
                $kolomStatusLog => $status,
                'nomor_urut_pegawai_penyetuju' => auth()->user()->nomor_urut_pegawai,
                'komentar' => $komentarFinal,
            ];

            $kolomWaktu = 'updated_at';
            if (\Schema::hasColumn($tabelLog, $kolomWaktu)) {
                $updateLog[$kolomWaktu] = now();
            }

            \DB::table($tabelLog)->where('id', $oldLog->id)->update($updateLog);

            // --- 5. JIKA DISETUJUI -> LEMPAR KE TAHAP BERIKUTNYA (ESTAFET FLOW) ---
            if ($status === 'disetujui') {

                // A. Tentukan Tahap Selanjutnya
                $nextTahap = 'HRO';

                if ($tahapSekarang === 'Direktur Kepatuhan') {
                    if ($sumber === 'lembur' || $sumber === 'cuti') {
                        $nextTahap = 'Direktur Operasional';
                    }
                    elseif ($sumber === 'pensiun' || $sumber === 'pangkat' || $sumber === 'pangkatgajitunjangan') {
                        $nextTahap = 'Direktur Utama';
                    }
                }
                elseif ($tahapSekarang === 'Direktur Operasional' || $tahapSekarang === 'Direktur Utama') {
                    $nextTahap = 'HRO';
                }

                // B. Siapkan Data Insert Log Baru
                $dataInsert = [
                    'nomor_urut_pegawai' => $oldLog->nomor_urut_pegawai,
                    'tahap_persetujuan'  => $nextTahap,
                    $kolomStatusLog      => 'diproses',
                    'komentar'           => 'Menunggu verifikasi ' . $nextTahap,
                    'updated_at'         => now()
                ];

                // C. Mapping ID Transaksi
                if ($sumber === 'cuti') {
                    $dataInsert['id_cuti'] = $oldLog->id_cuti;
                } elseif ($sumber === 'pensiun') {
                    $dataInsert['id_pensiun'] = $oldLog->id_pensiun;
                }
                elseif (in_array($sumber, ['pangkat', 'pangkatgajitunjangan'])) {
                    $dataInsert['id_kenaikan'] = $oldLog->id_kenaikan;
                } elseif ($sumber === 'lembur') {
                    $dataInsert['id_lembur'] = $oldLog->id_lembur;
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

    public function lihatDokumen($id)
    {
        $file = \DB::table('file_persyaratanpensiun')->where('id', $id)->first()
                ?? \DB::table('file_persyaratanpangkatgajitunjangan')->where('id', $id)->first();

        if (!$file) {
            abort(404, 'Data dokumen tidak ditemukan di database.');
        }

        $path = storage_path('app/private/' . $file->path_file_server);

        if (!file_exists($path)) {
            $path = storage_path('app/' . $file->path_file_server);
        }

        if (!file_exists($path)) {
            abort(404, 'Berkas fisik tidak ditemukan di server.');
        }

        $mimeType = \Illuminate\Support\Facades\File::mimeType($path) ?? 'application/pdf';

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.$file->nama_file_asli.'"'
        ]);
    }

}
