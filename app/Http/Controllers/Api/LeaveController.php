<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JenisCuti;
use App\Models\LogPersetujuanCuti;
use App\Models\PengajuanCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'context' => 'required|in:me,approval'
        ]);

        $context = $request->context;
        $leaves = [];

        if ($context == 'me') {
            $request->validate([
                'employee_id' => 'required|integer',
            ]);

            $leaves = PengajuanCuti::where('nomor_urut_pegawai', $request->employee_id)
                ->with('logs.penyetuju')
                ->get();
        } else {
            return response()->json([
                'message' => 'Context tidak valid',
                'data' => [],
            ], 422);
        }

        return response()->json([
            'message' => 'List leave retrieved',
            'data' => $leaves
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'doc' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $jenisCuti = JenisCuti::where('nama_cuti', $request->leave_type)->first();
        if (!$jenisCuti) {
            return response()->json([
                'message' => 'Jenis cuti tidak valid',
                'data' => [],
            ], 422);
        }

        $tanggalMulai = Carbon::parse($request->start_date)->startOfDay();
        $tanggalSelesai = Carbon::parse($request->end_date)->startOfDay();

        $jumlahCuti = $tanggalMulai->diffInDays($tanggalSelesai) + 1;
        $jatahPeriodeHari = (int) ($jenisCuti->durasi_hari ?? 0);
        $saldoAwal = (int) PengajuanCuti::hitungSaldoAwal($request->employee_id, $request->leave_type);
        $saldoAkhir = $saldoAwal - $jumlahCuti;

        if ($jumlahCuti < 1) {
            return response()->json([
                'message' => 'Jumlah cuti tidak valid',
                'data' => [],
            ], 422);
        }

        if ($saldoAkhir < 0) {
            return response()->json([
                'message' => 'Saldo cuti tidak mencukupi',
                'data' => [
                    'saldo_awal' => $saldoAwal,
                    'jumlah_cuti' => $jumlahCuti,
                    'saldo_akhir' => $saldoAkhir,
                ],
            ], 422);
        }

        $docPath = null;

        if ($request->hasFile('doc')) {
            $docPath = $request->file('doc')->store('leave', 'public');
        }

        DB::beginTransaction();

        try {
            $leave = PengajuanCuti::create([
                'nomor_urut_pegawai' => $request->employee_id,
                'jenis_cuti' => $request->leave_type,
                'tanggal_mulai' => $tanggalMulai->toDateString(),
                'tanggal_selesai' => $tanggalSelesai->toDateString(),
                'jumlah_cuti' => $jumlahCuti,
                'jatah_periode_hari' => $jatahPeriodeHari,
                'saldo_awal' => $saldoAwal,
                'saldo_akhir' => $saldoAkhir,
                'status_pengajuan' => 'diproses',
                'keterangan' => $request->description,
                'jalur_dokumen_pendukung' => $docPath,
            ]);

            LogPersetujuanCuti::create([
                'id_cuti' => $leave->id_cuti,
                'nomor_urut_pegawai' => $request->employee_id,
                'tahap_persetujuan' => 'Pengajuan Awal',
                'status_pengajuan' => 'diproses',
                'komentar' => 'Menunggu verifikasi.',
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Pengajuan cuti berhasil',
                'data' => $leave
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Pengajuan cuti gagal',
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $leave = PengajuanCuti::with('logs.penyetuju')->where('id_cuti', $id)->first();

        if (!$leave) {
            return response()->json([
                'message' => 'Pengajuan cuti tidak ditemukan',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'message' => 'Pengajuan cuti berhasil didapatkan',
            'data' => $leave
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function leaveTypes()
    {
        $leaveTypes = JenisCuti::with('subJenisCuti')->get();

        return response()->json([
            'message' => 'Jenis cuti berhasil didapatkan',
            'data' => $leaveTypes
        ]);
    }

    public function balances(Request $request)
    {
        $balances = DB::select('SELECT * FROM view_sisa_cuti WHERE nomor_urut_pegawai = ?', [$request->employee_id]);

        return response()->json([
            'message' => 'Balance cuti berhasil didapatkan',
            'data' => $balances
        ]);
    }

    public function managerApprovals(Request $request)
    {
        $user = $request->user();
        if (!$this->isManagerUser($user)) {
            return response()->json([
                'message' => 'Akses ditolak',
                'data' => [],
            ], 403);
        }

        $request->validate([
            'source' => 'nullable|in:cuti,lembur',
            'status' => 'nullable|in:pending,processed,all',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $source = $request->input('source');
        $status = strtolower((string) $request->input('status', 'all'));
        $perPage = (int) $request->input('per_page', 20);

        $finalQuery = $this->buildManagerApprovalsUnionQuery($user, $source, $status);
        if (!$finalQuery) {
            return response()->json([
                'message' => 'Data approval retrieved',
                'data' => [],
                'summary' => $this->buildManagerApprovalsSummary($user),
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage,
                    'total' => 0,
                ],
            ]);
        }

        $paginator = DB::table(DB::raw("({$finalQuery->toSql()}) as combined"))
            ->mergeBindings($finalQuery)
            ->orderBy('tanggal', 'desc')
            ->paginate($perPage);

        return response()->json([
            'message' => 'Data approval retrieved',
            'data' => $paginator->items(),
            'summary' => $this->buildManagerApprovalsSummary($user),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function managerApprovalsLatest(Request $request)
    {
        $user = $request->user();
        if (!$this->isManagerUser($user)) {
            return response()->json([
                'message' => 'Akses ditolak',
                'data' => [],
            ], 403);
        }

        $request->validate([
            'source' => 'nullable|in:cuti,lembur',
            'limit' => 'nullable|integer|min:1|max:10',
        ]);

        $source = $request->input('source');
        $limit = (int) $request->input('limit', 3);

        $finalQuery = $this->buildManagerPendingApprovalsUnionQuery($user, $source);
        if (!$finalQuery) {
            return response()->json([
                'message' => 'Data approval retrieved',
                'data' => [],
            ]);
        }

        $items = DB::table(DB::raw("({$finalQuery->toSql()}) as combined"))
            ->mergeBindings($finalQuery)
            ->orderBy('tanggal', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'message' => 'Data approval retrieved',
            'data' => $items,
        ]);
    }

    public function managerApprovalsSummary(Request $request)
    {
        $user = $request->user();
        if (!$this->isManagerUser($user)) {
            return response()->json([
                'message' => 'Akses ditolak',
                'data' => [],
            ], 403);
        }

        return response()->json([
            'message' => 'Approval summary retrieved',
            'data' => $this->buildManagerApprovalsSummary($user),
        ]);
    }

    public function managerApprovalDetail(Request $request, string $source, int $submissionId)
    {
        $user = $request->user();
        if (!$this->isManagerUser($user)) {
            return response()->json([
                'message' => 'Akses ditolak',
                'data' => [],
            ], 403);
        }

        $source = strtolower($source);
        if (!in_array($source, ['cuti', 'lembur'], true)) {
            return response()->json([
                'message' => 'Tipe tidak valid',
                'data' => [],
            ], 422);
        }

        $searchTahap = ['%Manager%', 'Pengajuan Awal', '%Audit%'];

        if ($source === 'cuti') {
            $query = DB::table('log_persetujuan_cuti as log')
                ->join('pengajuan_cuti as pc', 'log.id_cuti', '=', 'pc.id_cuti')
                ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->join('divisi as d', 'pek.id_divisi', '=', 'd.id_divisi')
                ->where('pc.id_cuti', $submissionId)
                ->where('log.status_pengajuan', 'diproses')
                ->whereNull('log.nomor_urut_pegawai_penyetuju')
                ->where('p.nomor_urut_pegawai', '!=', $user->nomor_urut_pegawai)
                ->where('pek.id_divisi', $user->id_divisi)
                ->where(function ($q) use ($searchTahap) {
                    foreach ($searchTahap as $st) {
                        if (str_contains($st, '%')) {
                            $q->orWhere('log.tahap_persetujuan', 'LIKE', $st);
                        } else {
                            $q->orWhere('log.tahap_persetujuan', $st);
                        }
                    }
                })
                ->orderBy('log.id', 'asc')
                ->select(
                    DB::raw("'cuti' as sumber"),
                    'pc.id_cuti as submission_id',
                    'log.id as log_id',
                    'log.updated_at as tanggal',
                    'log.tahap_persetujuan',
                    'log.komentar',
                    'log.status_pengajuan as status',
                    'p.nomor_urut_pegawai',
                    'p.nama',
                    'd.nama_divisi',
                    'pek.jabatan',
                    'pc.jenis_cuti',
                    'pc.tanggal_mulai',
                    'pc.tanggal_selesai',
                    'pc.jumlah_cuti',
                    'pc.saldo_awal',
                    'pc.saldo_akhir',
                    'pc.keterangan',
                    'pc.jalur_dokumen_pendukung'
                );
        } else {
            $query = DB::table('log_persetujuan_lembur as log')
                ->join('pengajuan_lembur as pl', 'log.id_lembur', '=', 'pl.id_lembur')
                ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->join('divisi as d', 'pek.id_divisi', '=', 'd.id_divisi')
                ->where('pl.id_lembur', $submissionId)
                ->where('log.status_persetujuan', 'diproses')
                ->whereNull('log.nomor_urut_pegawai_penyetuju')
                ->where('p.nomor_urut_pegawai', '!=', $user->nomor_urut_pegawai)
                ->where('pek.id_divisi', $user->id_divisi)
                ->where(function ($q) use ($searchTahap) {
                    foreach ($searchTahap as $st) {
                        if (str_contains($st, '%')) {
                            $q->orWhere('log.tahap_persetujuan', 'LIKE', $st);
                        } else {
                            $q->orWhere('log.tahap_persetujuan', $st);
                        }
                    }
                })
                ->orderBy('log.id', 'asc')
                ->select(
                    DB::raw("'lembur' as sumber"),
                    'pl.id_lembur as submission_id',
                    'log.id as log_id',
                    'log.updated_at as tanggal',
                    'log.tahap_persetujuan',
                    'log.komentar',
                    'log.status_persetujuan as status',
                    'p.nomor_urut_pegawai',
                    'p.nama',
                    'd.nama_divisi',
                    'pek.jabatan',
                    'pl.tanggal_lembur',
                    'pl.jam_mulai',
                    'pl.jam_selesai',
                    'pl.total_jam_lembur',
                    'pl.uraian_tugas'
                );
        }

        $data = $query->first();
        if (!$data) {
            return response()->json([
                'message' => 'Data tidak ditemukan',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'message' => 'Approval detail retrieved',
            'data' => $data,
        ]);
    }

    public function managerUpdateApproval(Request $request, string $source, int $logId)
    {
        return $this->updateApproval($request, 'manager', $source, $logId);
    }

    public function approvals(Request $request, string $level)
    {
        $user = $request->user();
        $level = $this->normalizeApprovalLevel($level);

        if (!$this->isApprovalUser($user, $level)) {
            return response()->json([
                'message' => 'Akses ditolak',
                'data' => [],
            ], 403);
        }

        $request->validate([
            'source' => 'nullable|in:cuti,lembur',
            'status' => 'nullable|in:pending,processed,all',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $source = $request->input('source');
        $status = strtolower((string) $request->input('status', 'all'));
        $perPage = (int) $request->input('per_page', 20);

        $finalQuery = $this->buildApprovalsUnionQueryForLevel($user, $level, $source, $status);
        if (!$finalQuery) {
            return response()->json([
                'message' => 'Data approval retrieved',
                'data' => [],
                'summary' => $this->buildApprovalsSummaryForLevel($user, $level),
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage,
                    'total' => 0,
                ],
            ]);
        }

        $paginator = DB::table(DB::raw("({$finalQuery->toSql()}) as combined"))
            ->mergeBindings($finalQuery)
            ->orderBy('tanggal', 'desc')
            ->paginate($perPage);

        return response()->json([
            'message' => 'Data approval retrieved',
            'data' => $paginator->items(),
            'summary' => $this->buildApprovalsSummaryForLevel($user, $level),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function approvalsLatest(Request $request, string $level)
    {
        $user = $request->user();
        $level = $this->normalizeApprovalLevel($level);

        if (!$this->isApprovalUser($user, $level)) {
            return response()->json([
                'message' => 'Akses ditolak',
                'data' => [],
            ], 403);
        }

        $request->validate([
            'source' => 'nullable|in:cuti,lembur',
            'limit' => 'nullable|integer|min:1|max:10',
        ]);

        $source = $request->input('source');
        $limit = (int) $request->input('limit', 3);

        $finalQuery = $this->buildPendingApprovalsUnionQueryForLevel($user, $level, $source);
        if (!$finalQuery) {
            return response()->json([
                'message' => 'Data approval retrieved',
                'data' => [],
            ]);
        }

        $items = DB::table(DB::raw("({$finalQuery->toSql()}) as combined"))
            ->mergeBindings($finalQuery)
            ->orderBy('tanggal', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'message' => 'Data approval retrieved',
            'data' => $items,
        ]);
    }

    public function approvalsSummary(Request $request, string $level)
    {
        $user = $request->user();
        $level = $this->normalizeApprovalLevel($level);

        if (!$this->isApprovalUser($user, $level)) {
            return response()->json([
                'message' => 'Akses ditolak',
                'data' => [],
            ], 403);
        }

        return response()->json([
            'message' => 'Approval summary retrieved',
            'data' => $this->buildApprovalsSummaryForLevel($user, $level),
        ]);
    }

    public function updateApproval(Request $request, string $level, string $source, int $logId)
    {
        $user = $request->user();
        $level = $this->normalizeApprovalLevel($level);

        if (!$this->isApprovalUser($user, $level)) {
            return response()->json([
                'message' => 'Akses ditolak',
                'data' => [],
            ], 403);
        }

        $source = strtolower($source);
        if (!in_array($source, ['cuti', 'lembur'], true)) {
            return response()->json([
                'message' => 'Tipe tidak valid',
                'data' => [],
            ], 422);
        }

        $rules = [
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string',
        ];

        if ($source === 'lembur' && $request->input('status') === 'disetujui') {
            $rules['jam_mulai'] = 'nullable';
            $rules['jam_selesai'] = 'nullable';
            $rules['total_jam_lembur'] = 'nullable';
        }

        $validated = $request->validate($rules);

        $result = DB::transaction(function () use ($validated, $user, $level, $source, $logId) {
            $tabelLog = $source === 'cuti' ? 'log_persetujuan_cuti' : 'log_persetujuan_lembur';
            $kolomStatus = $source === 'cuti' ? 'status_pengajuan' : 'status_persetujuan';

            $logLama = DB::table($tabelLog)
                ->where('id', $logId)
                ->lockForUpdate()
                ->first();

            if (!$logLama) {
                return [
                    'ok' => false,
                    'code' => 404,
                    'message' => 'Data tidak ditemukan',
                ];
            }

            if (!empty($logLama->nomor_urut_pegawai_penyetuju) || ($logLama->{$kolomStatus} ?? null) !== 'diproses') {
                return [
                    'ok' => false,
                    'code' => 409,
                    'message' => 'Pengajuan sudah diproses',
                ];
            }

            $pemohonDivisi = DB::table('pegawai as p')
                ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->where('p.nomor_urut_pegawai', $logLama->nomor_urut_pegawai)
                ->select('pek.id_divisi', 'pek.jabatan', 'p.level_id')
                ->first();

            if (!$pemohonDivisi) {
                return [
                    'ok' => false,
                    'code' => 404,
                    'message' => 'Data pemohon tidak ditemukan',
                ];
            }

            if ($level === 'manager' && (int) $pemohonDivisi->id_divisi !== (int) $user->id_divisi) {
                return [
                    'ok' => false,
                    'code' => 403,
                    'message' => 'Akses ditolak',
                ];
            }

            if ((int) $logLama->nomor_urut_pegawai === (int) $user->nomor_urut_pegawai) {
                return [
                    'ok' => false,
                    'code' => 403,
                    'message' => 'Tidak dapat memproses pengajuan sendiri',
                ];
            }

            $pemohonUser = DB::table('users')->where('nomor_urut_pegawai', $logLama->nomor_urut_pegawai)->first();
            $isManagerPemohon = (int) ($pemohonUser->level_id ?? ($pemohonDivisi->level_id ?? 1)) === 2;

            if (!$this->isLogStageAllowedForLevel($level, $source, (string) ($logLama->tahap_persetujuan ?? ''), $isManagerPemohon)) {
                return [
                    'ok' => false,
                    'code' => 403,
                    'message' => 'Akses ditolak',
                ];
            }

            $namaTahapAksi = $this->resolveTahapAksiName($user, $level);
            $teksDefault = ($validated['status'] === 'disetujui')
                ? 'Disetujui oleh ' . $namaTahapAksi
                : 'Ditolak oleh ' . $namaTahapAksi;

            $komentarFinal = !empty($validated['catatan']) ? $validated['catatan'] : $teksDefault;

            if ($source === 'lembur') {
                $pengajuanAsli = DB::table('pengajuan_lembur')->where('id_lembur', $logLama->id_lembur)->first();
                if (!$pengajuanAsli) {
                    return [
                        'ok' => false,
                        'code' => 404,
                        'message' => 'Data pengajuan tidak ditemukan',
                    ];
                }

                $dataUpdateLembur = [
                    'updated_at' => now(),
                    'status_lembur' => ($validated['status'] === 'disetujui') ? 'diproses' : 'ditolak',
                ];

                if ($validated['status'] === 'disetujui') {
                    $dataUpdateLembur['jam_mulai'] = $validated['jam_mulai'] ?? $pengajuanAsli->jam_mulai;
                    $dataUpdateLembur['jam_selesai'] = $validated['jam_selesai'] ?? $pengajuanAsli->jam_selesai;
                    $dataUpdateLembur['total_jam_lembur'] = $validated['total_jam_lembur'] ?? $pengajuanAsli->total_jam_lembur;
                }

                DB::table('pengajuan_lembur')
                    ->where('id_lembur', $logLama->id_lembur)
                    ->update($dataUpdateLembur);
            }

            if ($source === 'cuti') {
                if ($validated['status'] === 'ditolak') {
                    DB::table('pengajuan_cuti')->where('id_cuti', $logLama->id_cuti)->update([
                        'status_pengajuan' => 'ditolak',
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::table('pengajuan_cuti')->where('id_cuti', $logLama->id_cuti)->update([
                        'status_pengajuan' => 'diproses',
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::table($tabelLog)->where('id', $logId)->update([
                $kolomStatus => $validated['status'],
                'nomor_urut_pegawai_penyetuju' => $user->nomor_urut_pegawai,
                'komentar' => $komentarFinal,
                'updated_at' => now(),
            ]);

            if ($validated['status'] === 'ditolak') {
                if ($source === 'lembur') {
                    DB::table('pengajuan_lembur')->where('id_lembur', $logLama->id_lembur)->update([
                        'status_lembur' => 'ditolak',
                        'updated_at' => now(),
                    ]);
                }

                return [
                    'ok' => true,
                    'code' => 200,
                    'message' => 'Pengajuan telah ditolak',
                    'data' => [
                        'source' => $source,
                        'log_id' => $logId,
                        'status' => 'ditolak',
                        'next_tahap' => null,
                    ],
                ];
            }

            $jabatanPemohonLower = strtolower((string) ($pemohonDivisi->jabatan ?? ''));

            if ($source === 'lembur') {
                $isAudit = str_contains($jabatanPemohonLower, 'audit') || str_contains($jabatanPemohonLower, 'skai');
                if ($isAudit) {
                    $flow = ['Pengajuan Awal', 'Kepala SK Audit', 'Kepala SKK & SKKMR', 'Direktur Kepatuhan', 'Direktur Operasional', 'HRO'];
                } elseif ($isManagerPemohon) {
                    $flow = ['Pengajuan Awal', 'Kepala SKK & SKKMR', 'Direktur Kepatuhan', 'Direktur Operasional', 'HRO'];
                } else {
                    $flow = ['Pengajuan Awal', 'Manager', 'Kepala SKK & SKKMR', 'Direktur Kepatuhan', 'Direktur Operasional', 'HRO'];
                }
            } else {
                $flow = $isManagerPemohon ? ['Pengajuan Awal', 'Direktur Operasional', 'HRO'] : ['Pengajuan Awal', 'Manager', 'Direktur Operasional', 'HRO'];
            }

            $tahapLama = (string) ($logLama->tahap_persetujuan ?? '');
            $currentIndex = array_search($tahapLama, $flow, true);
            if ($currentIndex === false) {
                foreach ($flow as $i => $stage) {
                    if ($stage !== '' && stripos($tahapLama, $stage) !== false) {
                        $currentIndex = $i;
                        break;
                    }
                }
            }

            $nextTahap = ($currentIndex !== false && isset($flow[$currentIndex + 1])) ? $flow[$currentIndex + 1] : 'Selesai';

            $insertBase = [
                'nomor_urut_pegawai' => $logLama->nomor_urut_pegawai,
                'updated_at' => now(),
            ];

            if ($source === 'cuti') {
                $insertBase['id_cuti'] = $logLama->id_cuti;
            } else {
                $insertBase['id_lembur'] = $logLama->id_lembur;
            }

            if ($tahapLama === 'Pengajuan Awal') {
                DB::table($tabelLog)->insert(array_merge($insertBase, [
                    'tahap_persetujuan' => $namaTahapAksi,
                    'nomor_urut_pegawai_penyetuju' => $user->nomor_urut_pegawai,
                    $kolomStatus => 'disetujui',
                    'komentar' => $komentarFinal,
                ]));

                if ($nextTahap === $namaTahapAksi) {
                    $nextTahap = $flow[$currentIndex + 2] ?? 'Selesai';
                }
            }

            if ($nextTahap !== 'Selesai') {
                DB::table($tabelLog)->insert(array_merge($insertBase, [
                    'tahap_persetujuan' => $nextTahap,
                    'nomor_urut_pegawai_penyetuju' => null,
                    $kolomStatus => 'diproses',
                    'komentar' => 'Menunggu verifikasi ' . $nextTahap,
                ]));

                if ($source === 'cuti') {
                    DB::table('pengajuan_cuti')->where('id_cuti', $logLama->id_cuti)->update([
                        'status_pengajuan' => 'diproses',
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::table('pengajuan_lembur')->where('id_lembur', $logLama->id_lembur)->update([
                        'status_lembur' => 'diproses',
                        'updated_at' => now(),
                    ]);
                }
            } else {
                if ($source === 'cuti') {
                    DB::table('pengajuan_cuti')->where('id_cuti', $logLama->id_cuti)->update([
                        'status_pengajuan' => 'disetujui',
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::table('pengajuan_lembur')->where('id_lembur', $logLama->id_lembur)->update([
                        'status_lembur' => 'disetujui',
                        'updated_at' => now(),
                    ]);
                }
            }

            return [
                'ok' => true,
                'code' => 200,
                'message' => 'Pengajuan berhasil diproses',
                'data' => [
                    'source' => $source,
                    'log_id' => $logId,
                    'status' => 'disetujui',
                    'next_tahap' => $nextTahap === 'Selesai' ? null : $nextTahap,
                ],
            ];
        });

        if (!($result['ok'] ?? false)) {
            return response()->json([
                'message' => $result['message'] ?? 'Gagal memproses',
                'data' => [],
            ], (int) ($result['code'] ?? 400));
        }

        return response()->json([
            'message' => $result['message'],
            'data' => $result['data'],
        ], 200);
    }

    private function normalizeApprovalLevel(string $level): string
    {
        $level = strtolower(trim($level));
        $allowed = [
            'manager',
            'skkmr',
            'audit',
            'direktur-operasional',
            'direktur-kepatuhan',
            'hro',
        ];

        return in_array($level, $allowed, true) ? $level : 'unknown';
    }

    private function isApprovalUser($user, string $level): bool
    {
        if ($level === 'unknown') {
            return false;
        }

        if ($level === 'manager') {
            return $this->isManagerUser($user);
        }

        $mapping = DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        $roleName = strtolower((string) ($mapping->role_name ?? ''));
        $routeName = strtolower((string) ($mapping->route_name ?? ''));
        $jabatanId = (int) ($user->jabatan_id ?? 0);

        return match ($level) {
            'hro' => $jabatanId === 16 || str_contains($roleName, 'hro') || str_contains($roleName, 'human resources') || str_contains($routeName, 'hro'),
            'direktur-operasional' => $jabatanId === 11 || str_contains($roleName, 'direktur operasional'),
            'direktur-kepatuhan' => $jabatanId === 10 || str_contains($roleName, 'direktur kepatuhan'),
            'audit' => $jabatanId === 19 || str_contains($roleName, 'audit') || str_contains($roleName, 'skai'),
            'skkmr' => $jabatanId === 18 || str_contains($roleName, 'kepala skk') || str_contains($roleName, 'kepatuhan'),
            default => false,
        };
    }

    private function resolveTahapAksiName($user, string $level): string
    {
        $mapping = DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        $roleName = (string) ($mapping->role_name ?? '');
        $lower = strtolower($roleName);

        if ($level === 'manager') {
            return 'Manager';
        }
        if ($level === 'skkmr') {
            return 'Kepala SKK & SKKMR';
        }
        if ($level === 'audit') {
            return 'Kepala SK Audit';
        }
        if ($level === 'hro') {
            return 'HRO';
        }
        if ($level === 'direktur-operasional') {
            return 'Direktur Operasional';
        }
        if ($level === 'direktur-kepatuhan') {
            return 'Direktur Kepatuhan';
        }

        if ($roleName !== '') {
            if (str_contains($lower, 'direktur operasional')) {
                return 'Direktur Operasional';
            }
            if (str_contains($lower, 'direktur kepatuhan')) {
                return 'Direktur Kepatuhan';
            }
            if (str_contains($lower, 'direktur utama')) {
                return 'Direktur Utama';
            }
            if (str_contains($lower, 'hro') || str_contains($lower, 'human resources')) {
                return 'HRO';
            }
            if (str_contains($lower, 'skk') || str_contains($lower, 'kepatuhan')) {
                return 'Kepala SKK & SKKMR';
            }
            if (str_contains($lower, 'audit') || str_contains($lower, 'skai')) {
                return 'Kepala SK Audit';
            }
        }

        return 'Penyetuju';
    }

    private function isLogStageAllowedForLevel(string $level, string $source, string $tahap, bool $isManagerPemohon): bool
    {
        $tahapLower = strtolower($tahap);

        if ($level === 'manager') {
            if ($tahapLower === 'pengajuan awal') {
                return !$isManagerPemohon;
            }
            return str_contains($tahapLower, 'manager') || str_contains($tahapLower, 'manajer') || str_contains($tahapLower, 'audit') || str_contains($tahapLower, 'skai');
        }

        if ($level === 'skkmr') {
            if ($source !== 'lembur') {
                return false;
            }
            if ($tahapLower === 'pengajuan awal') {
                return $isManagerPemohon;
            }
            return str_contains($tahapLower, 'kepala skk') || str_contains($tahapLower, 'kepatuhan');
        }

        if ($level === 'audit') {
            if ($source !== 'lembur') {
                return false;
            }
            return str_contains($tahapLower, 'audit');
        }

        if ($level === 'direktur-operasional') {
            if ($tahapLower === 'pengajuan awal') {
                return $source === 'cuti' && $isManagerPemohon;
            }
            return str_contains($tahapLower, 'direktur operasional');
        }

        if ($level === 'direktur-kepatuhan') {
            if ($source !== 'lembur') {
                return false;
            }
            return str_contains($tahapLower, 'direktur kepatuhan');
        }

        if ($level === 'hro') {
            return str_contains($tahapLower, 'hro') || str_contains($tahapLower, 'human resources');
        }

        return false;
    }

    private function buildPendingApprovalsUnionQueryForLevel($user, string $level, ?string $source)
    {
        $queries = [];

        if ((!$source || $source === 'cuti') && $this->levelAllowsSource($level, 'cuti')) {
            $queries[] = $this->buildPendingApprovalsQuery($user, $level, 'cuti');
        }

        if ((!$source || $source === 'lembur') && $this->levelAllowsSource($level, 'lembur')) {
            $queries[] = $this->buildPendingApprovalsQuery($user, $level, 'lembur');
        }

        if (empty($queries)) {
            return null;
        }

        $finalQuery = array_shift($queries);
        foreach ($queries as $u) {
            $finalQuery->unionAll($u);
        }

        return $finalQuery;
    }

    private function buildProcessedApprovalsUnionQueryForLevel($user, string $level, ?string $source)
    {
        $queries = [];

        if ((!$source || $source === 'cuti') && $this->levelAllowsSource($level, 'cuti')) {
            $queries[] = $this->buildProcessedApprovalsQuery($user, $level, 'cuti');
        }

        if ((!$source || $source === 'lembur') && $this->levelAllowsSource($level, 'lembur')) {
            $queries[] = $this->buildProcessedApprovalsQuery($user, $level, 'lembur');
        }

        if (empty($queries)) {
            return null;
        }

        $finalQuery = array_shift($queries);
        foreach ($queries as $u) {
            $finalQuery->unionAll($u);
        }

        return $finalQuery;
    }

    private function buildApprovalsUnionQueryForLevel($user, string $level, ?string $source, string $status)
    {
        $status = strtolower($status);

        if ($status === 'pending') {
            return $this->buildPendingApprovalsUnionQueryForLevel($user, $level, $source);
        }

        if ($status === 'processed') {
            return $this->buildProcessedApprovalsUnionQueryForLevel($user, $level, $source);
        }

        if ($status === 'all') {
            $pending = $this->buildPendingApprovalsUnionQueryForLevel($user, $level, $source);
            $processed = $this->buildProcessedApprovalsUnionQueryForLevel($user, $level, $source);

            if (!$pending) {
                return $processed;
            }

            if (!$processed) {
                return $pending;
            }

            $pending->unionAll($processed);
            return $pending;
        }

        return null;
    }

    private function buildApprovalsSummaryForLevel($user, string $level): array
    {
        $pendingBySource = [];
        $processedBySource = [];

        foreach (['cuti', 'lembur'] as $source) {
            if (!$this->levelAllowsSource($level, $source)) {
                continue;
            }

            $pendingBySource[$source] = $this->buildPendingCountQuery($user, $level, $source)
                ->distinct()
                ->count($source === 'cuti' ? 'pc.id_cuti' : 'pl.id_lembur');

            $processedBase = $this->buildProcessedCountQuery($user, $level, $source);
            $processedTotal = (clone $processedBase)
                ->distinct()
                ->count($source === 'cuti' ? 'pc.id_cuti' : 'pl.id_lembur');

            $processedDisetujui = (clone $processedBase)
                ->where($source === 'cuti' ? 'log.status_pengajuan' : 'log.status_persetujuan', 'disetujui')
                ->distinct()
                ->count($source === 'cuti' ? 'pc.id_cuti' : 'pl.id_lembur');

            $processedDitolak = (clone $processedBase)
                ->where($source === 'cuti' ? 'log.status_pengajuan' : 'log.status_persetujuan', 'ditolak')
                ->distinct()
                ->count($source === 'cuti' ? 'pc.id_cuti' : 'pl.id_lembur');

            $processedBySource[$source] = [
                'total' => $processedTotal,
                'disetujui' => $processedDisetujui,
                'ditolak' => $processedDitolak,
            ];
        }

        $pendingTotal = array_sum($pendingBySource);
        $processedTotal = array_sum(array_map(fn ($v) => (int) ($v['total'] ?? 0), $processedBySource));

        return [
            'pending' => [
                'total' => $pendingTotal,
                'by_source' => $pendingBySource,
            ],
            'processed' => [
                'total' => $processedTotal,
                'by_source' => $processedBySource,
            ],
        ];
    }

    private function levelAllowsSource(string $level, string $source): bool
    {
        if ($source === 'cuti') {
            return in_array($level, ['manager', 'direktur-operasional', 'hro'], true);
        }

        if ($source === 'lembur') {
            return in_array($level, ['manager', 'skkmr', 'audit', 'direktur-operasional', 'direktur-kepatuhan', 'hro'], true);
        }

        return false;
    }

    private function buildPendingApprovalsQuery($user, string $level, string $source)
    {
        if ($source === 'cuti') {
            $base = DB::table('log_persetujuan_cuti as log')
                ->join('pengajuan_cuti as pc', 'log.id_cuti', '=', 'pc.id_cuti')
                ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->join('divisi as d', 'pek.id_divisi', '=', 'd.id_divisi')
                ->where('log.status_pengajuan', 'diproses')
                ->whereNull('log.nomor_urut_pegawai_penyetuju')
                ->where('p.nomor_urut_pegawai', '!=', $user->nomor_urut_pegawai);

            if ($level === 'manager') {
                $base->where('pek.id_divisi', $user->id_divisi);
            }

            $this->applyTahapFilter($base, $level, $source, 'log', 'p');

            $base->whereIn('log.id', function ($sub) use ($user, $level, $source) {
                $sub->from('log_persetujuan_cuti as l2')
                    ->join('pegawai as p2', 'l2.nomor_urut_pegawai', '=', 'p2.nomor_urut_pegawai')
                    ->join('pekerjaan as pek2', 'p2.nomor_urut_pegawai', '=', 'pek2.nomor_urut_pegawai')
                    ->where('l2.status_pengajuan', 'diproses')
                    ->whereNull('l2.nomor_urut_pegawai_penyetuju')
                    ->where('p2.nomor_urut_pegawai', '!=', $user->nomor_urut_pegawai);

                if ($level === 'manager') {
                    $sub->where('pek2.id_divisi', $user->id_divisi);
                }

                $this->applyTahapFilter($sub, $level, $source, 'l2', 'p2');

                $sub->groupBy('l2.id_cuti')
                    ->selectRaw('MAX(l2.id)');
            });

            return $base->select(
                DB::raw("'cuti' as sumber"),
                'pc.id_cuti as submission_id',
                'log.id as log_id',
                'log.updated_at as tanggal',
                'log.tahap_persetujuan',
                'log.status_pengajuan as status',
                'p.nomor_urut_pegawai',
                'p.nama',
                'd.nama_divisi',
                'pek.jabatan',
                'pc.jenis_cuti',
                'pc.tanggal_mulai',
                'pc.tanggal_selesai',
                'pc.jumlah_cuti',
                DB::raw('null as tanggal_lembur'),
                DB::raw('null as jam_mulai'),
                DB::raw('null as jam_selesai'),
                DB::raw('null as total_jam_lembur')
            );
        }

        $base = DB::table('log_persetujuan_lembur as log')
            ->join('pengajuan_lembur as pl', 'log.id_lembur', '=', 'pl.id_lembur')
            ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
            ->join('divisi as d', 'pek.id_divisi', '=', 'd.id_divisi')
            ->where('log.status_persetujuan', 'diproses')
            ->whereNull('log.nomor_urut_pegawai_penyetuju')
            ->where('p.nomor_urut_pegawai', '!=', $user->nomor_urut_pegawai);

        if ($level === 'manager') {
            $base->where('pek.id_divisi', $user->id_divisi);
        }

        $this->applyTahapFilter($base, $level, $source, 'log', 'p');

        $base->whereIn('log.id', function ($sub) use ($user, $level, $source) {
            $sub->from('log_persetujuan_lembur as l2')
                ->join('pegawai as p2', 'l2.nomor_urut_pegawai', '=', 'p2.nomor_urut_pegawai')
                ->join('pekerjaan as pek2', 'p2.nomor_urut_pegawai', '=', 'pek2.nomor_urut_pegawai')
                ->where('l2.status_persetujuan', 'diproses')
                ->whereNull('l2.nomor_urut_pegawai_penyetuju')
                ->where('p2.nomor_urut_pegawai', '!=', $user->nomor_urut_pegawai);

            if ($level === 'manager') {
                $sub->where('pek2.id_divisi', $user->id_divisi);
            }

            $this->applyTahapFilter($sub, $level, $source, 'l2', 'p2');

            $sub->groupBy('l2.id_lembur')
                ->selectRaw('MAX(l2.id)');
        });

        return $base->select(
            DB::raw("'lembur' as sumber"),
            'pl.id_lembur as submission_id',
            'log.id as log_id',
            'log.updated_at as tanggal',
            'log.tahap_persetujuan',
            'log.status_persetujuan as status',
            'p.nomor_urut_pegawai',
            'p.nama',
            'd.nama_divisi',
            'pek.jabatan',
            DB::raw('null as jenis_cuti'),
            DB::raw('null as tanggal_mulai'),
            DB::raw('null as tanggal_selesai'),
            DB::raw('null as jumlah_cuti'),
            'pl.tanggal_lembur',
            'pl.jam_mulai',
            'pl.jam_selesai',
            'pl.total_jam_lembur'
        );
    }

    private function buildProcessedApprovalsQuery($user, string $level, string $source)
    {
        if ($source === 'cuti') {
            $base = DB::table('log_persetujuan_cuti as log')
                ->join('pengajuan_cuti as pc', 'log.id_cuti', '=', 'pc.id_cuti')
                ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->join('divisi as d', 'pek.id_divisi', '=', 'd.id_divisi')
                ->where('log.nomor_urut_pegawai_penyetuju', $user->nomor_urut_pegawai)
                ->whereIn('log.status_pengajuan', ['disetujui', 'ditolak']);

            if ($level === 'manager') {
                $base->where('pek.id_divisi', $user->id_divisi);
            }

            $this->applyTahapFilter($base, $level, $source, 'log', 'p');

            return $base->select(
                DB::raw("'cuti' as sumber"),
                'pc.id_cuti as submission_id',
                'log.id as log_id',
                'log.updated_at as tanggal',
                'log.tahap_persetujuan',
                'log.status_pengajuan as status',
                'p.nomor_urut_pegawai',
                'p.nama',
                'd.nama_divisi',
                'pek.jabatan',
                'pc.jenis_cuti',
                'pc.tanggal_mulai',
                'pc.tanggal_selesai',
                'pc.jumlah_cuti',
                DB::raw('null as tanggal_lembur'),
                DB::raw('null as jam_mulai'),
                DB::raw('null as jam_selesai'),
                DB::raw('null as total_jam_lembur')
            );
        }

        $base = DB::table('log_persetujuan_lembur as log')
            ->join('pengajuan_lembur as pl', 'log.id_lembur', '=', 'pl.id_lembur')
            ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
            ->join('divisi as d', 'pek.id_divisi', '=', 'd.id_divisi')
            ->where('log.nomor_urut_pegawai_penyetuju', $user->nomor_urut_pegawai)
            ->whereIn('log.status_persetujuan', ['disetujui', 'ditolak']);

        if ($level === 'manager') {
            $base->where('pek.id_divisi', $user->id_divisi);
        }

        $this->applyTahapFilter($base, $level, $source, 'log', 'p');

        return $base->select(
            DB::raw("'lembur' as sumber"),
            'pl.id_lembur as submission_id',
            'log.id as log_id',
            'log.updated_at as tanggal',
            'log.tahap_persetujuan',
            'log.status_persetujuan as status',
            'p.nomor_urut_pegawai',
            'p.nama',
            'd.nama_divisi',
            'pek.jabatan',
            DB::raw('null as jenis_cuti'),
            DB::raw('null as tanggal_mulai'),
            DB::raw('null as tanggal_selesai'),
            DB::raw('null as jumlah_cuti'),
            'pl.tanggal_lembur',
            'pl.jam_mulai',
            'pl.jam_selesai',
            'pl.total_jam_lembur'
        );
    }

    private function buildPendingCountQuery($user, string $level, string $source)
    {
        if ($source === 'cuti') {
            $q = DB::table('log_persetujuan_cuti as log')
                ->join('pengajuan_cuti as pc', 'log.id_cuti', '=', 'pc.id_cuti')
                ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->where('log.status_pengajuan', 'diproses')
                ->whereNull('log.nomor_urut_pegawai_penyetuju')
                ->where('p.nomor_urut_pegawai', '!=', $user->nomor_urut_pegawai);

            if ($level === 'manager') {
                $q->where('pek.id_divisi', $user->id_divisi);
            }

            $this->applyTahapFilter($q, $level, $source, 'log', 'p');
            return $q;
        }

        $q = DB::table('log_persetujuan_lembur as log')
            ->join('pengajuan_lembur as pl', 'log.id_lembur', '=', 'pl.id_lembur')
            ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
            ->where('log.status_persetujuan', 'diproses')
            ->whereNull('log.nomor_urut_pegawai_penyetuju')
            ->where('p.nomor_urut_pegawai', '!=', $user->nomor_urut_pegawai);

        if ($level === 'manager') {
            $q->where('pek.id_divisi', $user->id_divisi);
        }

        $this->applyTahapFilter($q, $level, $source, 'log', 'p');
        return $q;
    }

    private function buildProcessedCountQuery($user, string $level, string $source)
    {
        if ($source === 'cuti') {
            $q = DB::table('log_persetujuan_cuti as log')
                ->join('pengajuan_cuti as pc', 'log.id_cuti', '=', 'pc.id_cuti')
                ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
                ->where('log.nomor_urut_pegawai_penyetuju', $user->nomor_urut_pegawai)
                ->whereIn('log.status_pengajuan', ['disetujui', 'ditolak']);

            if ($level === 'manager') {
                $q->where('pek.id_divisi', $user->id_divisi);
            }

            $this->applyTahapFilter($q, $level, $source, 'log', 'p');
            return $q;
        }

        $q = DB::table('log_persetujuan_lembur as log')
            ->join('pengajuan_lembur as pl', 'log.id_lembur', '=', 'pl.id_lembur')
            ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
            ->where('log.nomor_urut_pegawai_penyetuju', $user->nomor_urut_pegawai)
            ->whereIn('log.status_persetujuan', ['disetujui', 'ditolak']);

        if ($level === 'manager') {
            $q->where('pek.id_divisi', $user->id_divisi);
        }

        $this->applyTahapFilter($q, $level, $source, 'log', 'p');
        return $q;
    }

    private function applyTahapFilter($query, string $level, string $source, string $logAlias, string $pegawaiAlias): void
    {
        if ($level === 'manager') {
            $searchTahap = ['%Manager%', 'Pengajuan Awal', '%Audit%'];

            $query->where(function ($q) use ($searchTahap, $logAlias) {
                foreach ($searchTahap as $st) {
                    if (str_contains($st, '%')) {
                        $q->orWhere("{$logAlias}.tahap_persetujuan", 'LIKE', $st);
                    } else {
                        $q->orWhere("{$logAlias}.tahap_persetujuan", $st);
                    }
                }
            });
            return;
        }

        if ($level === 'skkmr') {
            $query->where(function ($q) use ($logAlias, $pegawaiAlias) {
                $q->orWhere("{$logAlias}.tahap_persetujuan", 'LIKE', 'Kepala SKK & SKKMR')
                    ->orWhere("{$logAlias}.tahap_persetujuan", 'LIKE', 'Kepala Satker Kepatuhan & M.R.')
                    ->orWhere(function ($sub) use ($logAlias, $pegawaiAlias) {
                        $sub->where("{$logAlias}.tahap_persetujuan", 'Pengajuan Awal')
                            ->where("{$pegawaiAlias}.level_id", 2);
                    });
            });

            $query->where("{$logAlias}.tahap_persetujuan", 'NOT LIKE', '%Direktur%');
            return;
        }

        if ($level === 'audit') {
            $query->where("{$logAlias}.tahap_persetujuan", 'LIKE', '%Audit%');
            return;
        }

        if ($level === 'direktur-operasional') {
            $query->where(function ($q) use ($logAlias, $pegawaiAlias, $source) {
                $q->orWhere("{$logAlias}.tahap_persetujuan", 'LIKE', '%Direktur Operasional%');
                if ($source === 'cuti') {
                    $q->orWhere(function ($sub) use ($logAlias, $pegawaiAlias) {
                        $sub->where("{$logAlias}.tahap_persetujuan", 'Pengajuan Awal')
                            ->where("{$pegawaiAlias}.level_id", 2);
                    });
                }
            });
            return;
        }

        if ($level === 'direktur-kepatuhan') {
            $query->where("{$logAlias}.tahap_persetujuan", 'LIKE', '%Direktur Kepatuhan%');
            return;
        }

        if ($level === 'hro') {
            $query->where(function ($q) use ($logAlias) {
                $q->orWhere("{$logAlias}.tahap_persetujuan", 'HRO')
                    ->orWhere("{$logAlias}.tahap_persetujuan", 'LIKE', '%HRO%')
                    ->orWhere("{$logAlias}.tahap_persetujuan", 'LIKE', '%Human Resources%');
            });
            return;
        }

        $query->whereRaw('1 = 0');
    }

    private function isManagerUser($user): bool
    {
        $mapping = DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        $routeName = strtolower((string) ($mapping->route_name ?? ''));

        return str_contains($routeName, 'manager');
    }

    private function buildManagerPendingApprovalsUnionQuery($user, ?string $source)
    {
        $queries = [];

        if (!$source || $source === 'cuti') {
            $queries[] = $this->buildManagerApprovalLeavesQuery($user);
        }

        if (!$source || $source === 'lembur') {
            $queries[] = $this->buildManagerApprovalOvertimesQuery($user);
        }

        if (empty($queries)) {
            return null;
        }

        $finalQuery = array_shift($queries);
        foreach ($queries as $u) {
            $finalQuery->unionAll($u);
        }

        return $finalQuery;
    }

    private function buildManagerProcessedApprovalsUnionQuery($user, ?string $source)
    {
        $queries = [];

        if (!$source || $source === 'cuti') {
            $queries[] = $this->buildManagerProcessedApprovalLeavesQuery($user);
        }

        if (!$source || $source === 'lembur') {
            $queries[] = $this->buildManagerProcessedApprovalOvertimesQuery($user);
        }

        if (empty($queries)) {
            return null;
        }

        $finalQuery = array_shift($queries);
        foreach ($queries as $u) {
            $finalQuery->unionAll($u);
        }

        return $finalQuery;
    }

    private function buildManagerApprovalsUnionQuery($user, ?string $source, string $status)
    {
        $status = strtolower($status);

        if ($status === 'pending') {
            return $this->buildManagerPendingApprovalsUnionQuery($user, $source);
        }

        if ($status === 'processed') {
            return $this->buildManagerProcessedApprovalsUnionQuery($user, $source);
        }

        if ($status === 'all') {
            $pending = $this->buildManagerPendingApprovalsUnionQuery($user, $source);
            $processed = $this->buildManagerProcessedApprovalsUnionQuery($user, $source);

            if (!$pending) {
                return $processed;
            }

            if (!$processed) {
                return $pending;
            }

            $pending->unionAll($processed);
            return $pending;
        }

        return null;
    }

    private function buildManagerApprovalsSummary($user): array
    {
        $pendingCuti = $this->buildManagerPendingLeavesCountQuery($user)
            ->distinct()
            ->count('pc.id_cuti');

        $pendingLembur = $this->buildManagerPendingOvertimesCountQuery($user)
            ->distinct()
            ->count('pl.id_lembur');

        $processedBaseCuti = $this->buildManagerProcessedLeavesCountQuery($user);
        $processedCutiTotal = (clone $processedBaseCuti)
            ->whereIn('log.status_pengajuan', ['disetujui', 'ditolak'])
            ->distinct()
            ->count('pc.id_cuti');
        $processedCutiDisetujui = (clone $processedBaseCuti)
            ->where('log.status_pengajuan', 'disetujui')
            ->distinct()
            ->count('pc.id_cuti');
        $processedCutiDitolak = (clone $processedBaseCuti)
            ->where('log.status_pengajuan', 'ditolak')
            ->distinct()
            ->count('pc.id_cuti');

        $processedBaseLembur = $this->buildManagerProcessedOvertimesCountQuery($user);
        $processedLemburTotal = (clone $processedBaseLembur)
            ->whereIn('log.status_persetujuan', ['disetujui', 'ditolak'])
            ->distinct()
            ->count('pl.id_lembur');
        $processedLemburDisetujui = (clone $processedBaseLembur)
            ->where('log.status_persetujuan', 'disetujui')
            ->distinct()
            ->count('pl.id_lembur');
        $processedLemburDitolak = (clone $processedBaseLembur)
            ->where('log.status_persetujuan', 'ditolak')
            ->distinct()
            ->count('pl.id_lembur');

        return [
            'pending' => [
                'total' => $pendingCuti + $pendingLembur,
                'by_source' => [
                    'cuti' => $pendingCuti,
                    'lembur' => $pendingLembur,
                ],
            ],
            'processed' => [
                'total' => $processedCutiTotal + $processedLemburTotal,
                'by_source' => [
                    'cuti' => [
                        'total' => $processedCutiTotal,
                        'disetujui' => $processedCutiDisetujui,
                        'ditolak' => $processedCutiDitolak,
                    ],
                    'lembur' => [
                        'total' => $processedLemburTotal,
                        'disetujui' => $processedLemburDisetujui,
                        'ditolak' => $processedLemburDitolak,
                    ],
                ],
            ],
        ];
    }

    private function buildManagerPendingLeavesCountQuery($user)
    {
        $searchTahap = ['%Manager%', 'Pengajuan Awal', '%Audit%'];

        return DB::table('log_persetujuan_cuti as log')
            ->join('pengajuan_cuti as pc', 'log.id_cuti', '=', 'pc.id_cuti')
            ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
            ->where('log.status_pengajuan', 'diproses')
            ->whereNull('log.nomor_urut_pegawai_penyetuju')
            ->where('p.nomor_urut_pegawai', '!=', $user->nomor_urut_pegawai)
            ->where('pek.id_divisi', $user->id_divisi)
            ->where(function ($q) use ($searchTahap) {
                foreach ($searchTahap as $st) {
                    if (str_contains($st, '%')) {
                        $q->orWhere('log.tahap_persetujuan', 'LIKE', $st);
                    } else {
                        $q->orWhere('log.tahap_persetujuan', $st);
                    }
                }
            });
    }

    private function buildManagerPendingOvertimesCountQuery($user)
    {
        $searchTahap = ['%Manager%', 'Pengajuan Awal', '%Audit%'];

        return DB::table('log_persetujuan_lembur as log')
            ->join('pengajuan_lembur as pl', 'log.id_lembur', '=', 'pl.id_lembur')
            ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
            ->where('log.status_persetujuan', 'diproses')
            ->whereNull('log.nomor_urut_pegawai_penyetuju')
            ->where('p.nomor_urut_pegawai', '!=', $user->nomor_urut_pegawai)
            ->where('pek.id_divisi', $user->id_divisi)
            ->where(function ($q) use ($searchTahap) {
                foreach ($searchTahap as $st) {
                    if (str_contains($st, '%')) {
                        $q->orWhere('log.tahap_persetujuan', 'LIKE', $st);
                    } else {
                        $q->orWhere('log.tahap_persetujuan', $st);
                    }
                }
            });
    }

    private function buildManagerProcessedLeavesCountQuery($user)
    {
        return DB::table('log_persetujuan_cuti as log')
            ->join('pengajuan_cuti as pc', 'log.id_cuti', '=', 'pc.id_cuti')
            ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
            ->where('pek.id_divisi', $user->id_divisi)
            ->where('log.nomor_urut_pegawai_penyetuju', $user->nomor_urut_pegawai);
    }

    private function buildManagerProcessedOvertimesCountQuery($user)
    {
        return DB::table('log_persetujuan_lembur as log')
            ->join('pengajuan_lembur as pl', 'log.id_lembur', '=', 'pl.id_lembur')
            ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
            ->where('pek.id_divisi', $user->id_divisi)
            ->where('log.nomor_urut_pegawai_penyetuju', $user->nomor_urut_pegawai);
    }

    private function buildManagerApprovalLeavesQuery($user)
    {
        $searchTahap = ['%Manager%', 'Pengajuan Awal', '%Audit%'];

        return DB::table('log_persetujuan_cuti as log')
            ->join('pengajuan_cuti as pc', 'log.id_cuti', '=', 'pc.id_cuti')
            ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
            ->join('divisi as d', 'pek.id_divisi', '=', 'd.id_divisi')
            ->where('log.status_pengajuan', 'diproses')
            ->whereNull('log.nomor_urut_pegawai_penyetuju')
            ->where('p.nomor_urut_pegawai', '!=', $user->nomor_urut_pegawai)
            ->where('pek.id_divisi', $user->id_divisi)
            ->where(function ($q) use ($searchTahap) {
                foreach ($searchTahap as $st) {
                    if (str_contains($st, '%')) {
                        $q->orWhere('log.tahap_persetujuan', 'LIKE', $st);
                    } else {
                        $q->orWhere('log.tahap_persetujuan', $st);
                    }
                }
            })
            ->whereIn('log.id', function ($sub) use ($user, $searchTahap) {
                $sub->from('log_persetujuan_cuti as l2')
                    ->join('pegawai as p2', 'l2.nomor_urut_pegawai', '=', 'p2.nomor_urut_pegawai')
                    ->join('pekerjaan as pek2', 'p2.nomor_urut_pegawai', '=', 'pek2.nomor_urut_pegawai')
                    ->where('l2.status_pengajuan', 'diproses')
                    ->whereNull('l2.nomor_urut_pegawai_penyetuju')
                    ->where('p2.nomor_urut_pegawai', '!=', $user->nomor_urut_pegawai)
                    ->where('pek2.id_divisi', $user->id_divisi)
                    ->where(function ($q) use ($searchTahap) {
                        foreach ($searchTahap as $st) {
                            if (str_contains($st, '%')) {
                                $q->orWhere('l2.tahap_persetujuan', 'LIKE', $st);
                            } else {
                                $q->orWhere('l2.tahap_persetujuan', $st);
                            }
                        }
                    })
                    ->groupBy('l2.id_cuti')
                    ->selectRaw('MAX(l2.id)');
            })
            ->select(
                DB::raw("'cuti' as sumber"),
                'pc.id_cuti as submission_id',
                'log.updated_at as tanggal',
                'log.tahap_persetujuan',
                'log.status_pengajuan as status',
                'p.nomor_urut_pegawai',
                'p.nama',
                'd.nama_divisi',
                'pek.jabatan',
                'pc.jenis_cuti',
                'pc.tanggal_mulai',
                'pc.tanggal_selesai',
                'pc.jumlah_cuti',
                DB::raw('null as tanggal_lembur'),
                DB::raw('null as jam_mulai'),
                DB::raw('null as jam_selesai'),
                DB::raw('null as total_jam_lembur')
            );
    }

    private function buildManagerProcessedApprovalLeavesQuery($user)
    {
        $searchTahap = ['%Manager%', 'Pengajuan Awal', '%Audit%'];

        return DB::table('log_persetujuan_cuti as log')
            ->join('pengajuan_cuti as pc', 'log.id_cuti', '=', 'pc.id_cuti')
            ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
            ->join('divisi as d', 'pek.id_divisi', '=', 'd.id_divisi')
            ->where('pek.id_divisi', $user->id_divisi)
            ->where('log.nomor_urut_pegawai_penyetuju', $user->nomor_urut_pegawai)
            ->whereIn('log.status_pengajuan', ['disetujui', 'ditolak'])
            ->where(function ($q) use ($searchTahap) {
                foreach ($searchTahap as $st) {
                    if (str_contains($st, '%')) {
                        $q->orWhere('log.tahap_persetujuan', 'LIKE', $st);
                    } else {
                        $q->orWhere('log.tahap_persetujuan', $st);
                    }
                }
            })
            ->whereIn('log.id', function ($sub) use ($user, $searchTahap) {
                $sub->from('log_persetujuan_cuti as l2')
                    ->join('pegawai as p2', 'l2.nomor_urut_pegawai', '=', 'p2.nomor_urut_pegawai')
                    ->join('pekerjaan as pek2', 'p2.nomor_urut_pegawai', '=', 'pek2.nomor_urut_pegawai')
                    ->where('pek2.id_divisi', $user->id_divisi)
                    ->where('l2.nomor_urut_pegawai_penyetuju', $user->nomor_urut_pegawai)
                    ->whereIn('l2.status_pengajuan', ['disetujui', 'ditolak'])
                    ->where(function ($q) use ($searchTahap) {
                        foreach ($searchTahap as $st) {
                            if (str_contains($st, '%')) {
                                $q->orWhere('l2.tahap_persetujuan', 'LIKE', $st);
                            } else {
                                $q->orWhere('l2.tahap_persetujuan', $st);
                            }
                        }
                    })
                    ->groupBy('l2.id_cuti')
                    ->selectRaw('MAX(l2.id)');
            })
            ->select(
                DB::raw("'cuti' as sumber"),
                'pc.id_cuti as submission_id',
                'log.updated_at as tanggal',
                'log.tahap_persetujuan',
                'log.status_pengajuan as status',
                'p.nomor_urut_pegawai',
                'p.nama',
                'd.nama_divisi',
                'pek.jabatan',
                'pc.jenis_cuti',
                'pc.tanggal_mulai',
                'pc.tanggal_selesai',
                'pc.jumlah_cuti',
                DB::raw('null as tanggal_lembur'),
                DB::raw('null as jam_mulai'),
                DB::raw('null as jam_selesai'),
                DB::raw('null as total_jam_lembur')
            );
    }

    private function buildManagerApprovalOvertimesQuery($user)
    {
        $searchTahap = ['%Manager%', 'Pengajuan Awal', '%Audit%'];

        return DB::table('log_persetujuan_lembur as log')
            ->join('pengajuan_lembur as pl', 'log.id_lembur', '=', 'pl.id_lembur')
            ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
            ->join('divisi as d', 'pek.id_divisi', '=', 'd.id_divisi')
            ->where('log.status_persetujuan', 'diproses')
            ->whereNull('log.nomor_urut_pegawai_penyetuju')
            ->where('p.nomor_urut_pegawai', '!=', $user->nomor_urut_pegawai)
            ->where('pek.id_divisi', $user->id_divisi)
            ->where(function ($q) use ($searchTahap) {
                foreach ($searchTahap as $st) {
                    if (str_contains($st, '%')) {
                        $q->orWhere('log.tahap_persetujuan', 'LIKE', $st);
                    } else {
                        $q->orWhere('log.tahap_persetujuan', $st);
                    }
                }
            })
            ->whereIn('log.id', function ($sub) use ($user, $searchTahap) {
                $sub->from('log_persetujuan_lembur as l2')
                    ->join('pegawai as p2', 'l2.nomor_urut_pegawai', '=', 'p2.nomor_urut_pegawai')
                    ->join('pekerjaan as pek2', 'p2.nomor_urut_pegawai', '=', 'pek2.nomor_urut_pegawai')
                    ->where('l2.status_persetujuan', 'diproses')
                    ->whereNull('l2.nomor_urut_pegawai_penyetuju')
                    ->where('p2.nomor_urut_pegawai', '!=', $user->nomor_urut_pegawai)
                    ->where('pek2.id_divisi', $user->id_divisi)
                    ->where(function ($q) use ($searchTahap) {
                        foreach ($searchTahap as $st) {
                            if (str_contains($st, '%')) {
                                $q->orWhere('l2.tahap_persetujuan', 'LIKE', $st);
                            } else {
                                $q->orWhere('l2.tahap_persetujuan', $st);
                            }
                        }
                    })
                    ->groupBy('l2.id_lembur')
                    ->selectRaw('MAX(l2.id)');
            })
            ->select(
                DB::raw("'lembur' as sumber"),
                'pl.id_lembur as submission_id',
                'log.updated_at as tanggal',
                'log.tahap_persetujuan',
                'log.status_persetujuan as status',
                'p.nomor_urut_pegawai',
                'p.nama',
                'd.nama_divisi',
                'pek.jabatan',
                DB::raw('null as jenis_cuti'),
                DB::raw('null as tanggal_mulai'),
                DB::raw('null as tanggal_selesai'),
                DB::raw('null as jumlah_cuti'),
                'pl.tanggal_lembur',
                'pl.jam_mulai',
                'pl.jam_selesai',
                'pl.total_jam_lembur'
            );
    }

    private function buildManagerProcessedApprovalOvertimesQuery($user)
    {
        $searchTahap = ['%Manager%', 'Pengajuan Awal', '%Audit%'];

        return DB::table('log_persetujuan_lembur as log')
            ->join('pengajuan_lembur as pl', 'log.id_lembur', '=', 'pl.id_lembur')
            ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai')
            ->join('divisi as d', 'pek.id_divisi', '=', 'd.id_divisi')
            ->where('pek.id_divisi', $user->id_divisi)
            ->where('log.nomor_urut_pegawai_penyetuju', $user->nomor_urut_pegawai)
            ->whereIn('log.status_persetujuan', ['disetujui', 'ditolak'])
            ->where(function ($q) use ($searchTahap) {
                foreach ($searchTahap as $st) {
                    if (str_contains($st, '%')) {
                        $q->orWhere('log.tahap_persetujuan', 'LIKE', $st);
                    } else {
                        $q->orWhere('log.tahap_persetujuan', $st);
                    }
                }
            })
            ->whereIn('log.id', function ($sub) use ($user, $searchTahap) {
                $sub->from('log_persetujuan_lembur as l2')
                    ->join('pegawai as p2', 'l2.nomor_urut_pegawai', '=', 'p2.nomor_urut_pegawai')
                    ->join('pekerjaan as pek2', 'p2.nomor_urut_pegawai', '=', 'pek2.nomor_urut_pegawai')
                    ->where('pek2.id_divisi', $user->id_divisi)
                    ->where('l2.nomor_urut_pegawai_penyetuju', $user->nomor_urut_pegawai)
                    ->whereIn('l2.status_persetujuan', ['disetujui', 'ditolak'])
                    ->where(function ($q) use ($searchTahap) {
                        foreach ($searchTahap as $st) {
                            if (str_contains($st, '%')) {
                                $q->orWhere('l2.tahap_persetujuan', 'LIKE', $st);
                            } else {
                                $q->orWhere('l2.tahap_persetujuan', $st);
                            }
                        }
                    })
                    ->groupBy('l2.id_lembur')
                    ->selectRaw('MAX(l2.id)');
            })
            ->select(
                DB::raw("'lembur' as sumber"),
                'pl.id_lembur as submission_id',
                'log.updated_at as tanggal',
                'log.tahap_persetujuan',
                'log.status_persetujuan as status',
                'p.nomor_urut_pegawai',
                'p.nama',
                'd.nama_divisi',
                'pek.jabatan',
                DB::raw('null as jenis_cuti'),
                DB::raw('null as tanggal_mulai'),
                DB::raw('null as tanggal_selesai'),
                DB::raw('null as jumlah_cuti'),
                'pl.tanggal_lembur',
                'pl.jam_mulai',
                'pl.jam_selesai',
                'pl.total_jam_lembur'
            );
    }
}
