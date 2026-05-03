<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Pegawai\KenaikanPangkatgajitunjangan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Support\Collection;
// use App\Models\LogPersetujuanCuti;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PengajuanCuti;
use App\Models\PengajuanLembur;
use App\Models\Pekerjaan;
use App\Models\PengajuanPangkatgajitunjangan;
use App\Models\PengajuanPensiun;
use App\Services\SubmissionProcessorService;

class DataPengajuanController extends Controller
{
    protected $submissionProcessor;

    public function __construct(SubmissionProcessorService $submissionProcessor)
    {
        $this->submissionProcessor = $submissionProcessor;
    }

    public function formDataPengajuan(Request $request, $type = null)
    {
        $user = auth()->user();

        // 1. Ambil data Role secara dinamis
        $roleMapping = \DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        $isManager = $roleMapping && str_contains($roleMapping->route_name, 'manager');
        $isSKKMR   = $roleMapping && str_contains($roleMapping->route_name, 'skkmr');

        if ($isManager) {
            $dashboardRoute = $roleMapping->route_name ?? 'manager.dashboardmanagerumum';
            $parentRouteName = 'manager.pengajuanmanager';
            $roleLabel = 'Manager';
        } elseif ($isSKKMR) {
            $dashboardRoute = 'skkmr.dashboardskkmr';
            $parentRouteName = 'skkmr.pengajuanskkmr';
            $roleLabel = 'Kepala Satker';
        } else {
            $dashboardRoute = 'pegawai.dashboard';
            $parentRouteName = 'datapengajuan.formDataPengajuan';
            $roleLabel = 'Pegawai';
        }

        $statusFilterRequest = $request->status_pengajuan_filter;

        // 2. Ambil data mentah (fetchSubmissions)
        $allSubmissions = $this->fetchSubmissions($user, $request);

        // 3. Filter berdasarkan Type
        $filterType = $type ?: $request->type;
        if ($filterType) {
            $allSubmissions = $allSubmissions->filter(function ($item) use ($filterType) {
                return strtolower($item['type']) === strtolower($filterType);
            });
        }

        // 4. Urutkan Data
        $sortedSubmissions = $allSubmissions->sortByDesc('created_at');

        // 5. Proses data menggunakan SERVICE CLASS
        $processedSubmissions = $this->submissionProcessor->processSubmissions(submissions: $sortedSubmissions);

        if ($statusFilterRequest) {
            $processedSubmissions = $processedSubmissions->filter(function ($submission) use ($statusFilterRequest) {
                return strtolower($submission['blade_status_text'] ?? '') === strtolower($statusFilterRequest);
            });
        }

        // 6. Pagination
        $paginatedSubmissions = $this->paginateSubmissions($processedSubmissions, 8)
            ->appends($request->query());

        // 7. Judul dan Breadcrumbs Dinamis
        $pageTitle = 'Data Pengajuan ' . $roleLabel;

        $breadcrumbs = [
            'Beranda' => route($dashboardRoute),
        ];

        if ($type || $request->type) {
            // 1. Ambil nilai type asli (belum di-strtolower)
            $rawType = $type ?: $request->type;

            // 2. Buat kamus nama yang rapi
            $typeNames = [
                'PangkatGajiTunjangan' => 'Pangkat, Gaji, dan Tunjangan',
                'Cuti' => 'Cuti',
                'Lembur' => 'Lembur',
                'Pensiun' => 'Pensiun',
            ];

            // 3. Ambil nama rapi dari kamus, jika tidak ada baru gunakan ucfirst
            $formattedType = $typeNames[$rawType] ?? ucfirst($rawType);

            $breadcrumbs[$pageTitle] = route($parentRouteName); // Level 2
            $breadcrumbs['Pengajuan ' . $formattedType] = null; // Level 3
        } else {
            $breadcrumbs[$pageTitle] = null; // Level 2 Aktif
        }

        // 8. Tentukan Layout
        $layout = $user->layout_file; // Menggunakan Accessor otomatis

        return view('datapengajuan.datapengajuan', [
            'submissions' => $paginatedSubmissions,
            'pageTitle' => $pageTitle,
            'breadcrumbs' => $breadcrumbs,
            'parentRouteName' => $parentRouteName,
            'layout' => $layout,
            'isManager' => $isManager,
            'isSKKMR' => $isSKKMR
        ]);
    }

    private function fetchSubmissions($user, $request): Collection
    {
        $dariTanggal = $request->dari_tanggal;
        $hinggaTanggal = $request->hingga_tanggal;

        $applyDateFilters = function ($query) use ($dariTanggal, $hinggaTanggal) {
            if ($dariTanggal) $query->whereDate('created_at', '>=', $dariTanggal);
            if ($hinggaTanggal) $query->whereDate('created_at', '<=', $hinggaTanggal);
            return $query;
        };

        // 1. Ambil Data (Pastikan nama kolom updated_at sudah sinkron)
        $cutis = $applyDateFilters($user->cutis())->with(['logs' => function ($query) {
            $query->orderByDesc('updated_at')->orderByDesc('id_cuti');
        }])->get();

        $lemburs = $applyDateFilters($user->lemburs())->with(['logPersetujuanLembur' => function ($query) {
            $query->orderByDesc('updated_at');
        }])->get();

        $pensiuns = $applyDateFilters($user->pensiuns())->with(['logPersetujuanPensiun' => function ($query) {
            $query->orderByDesc('updated_at');
        }])->get();

        $pangkatgajitunjangans = $applyDateFilters($user->pangkatgajitunjangans())->with(['logPersetujuanPangkatgajitunjangan' => function ($query) {
            $query->orderByDesc('updated_at');
        }])->get();

        // 2. Mapping Data agar seragam
        $processedCuti = $cutis->map(function ($item) {
            $itemArray = $item->toArray();
            $itemArray['type'] = 'Cuti';
            $itemArray['id'] = $item->id_cuti;
            return $itemArray;
        });

        $processedLembur = $lemburs->map(function ($item) {
            $itemArray = $item->toArray();
            $itemArray['type'] = 'Lembur';
            $itemArray['id'] = $item->id_lembur;
            $itemArray['status_pengajuan'] = $item->status_lembur;
            $itemArray['logs'] = $itemArray['log_persetujuan_lembur'] ?? [];
            unset($itemArray['log_persetujuan_lembur']);
            return $itemArray;
        });

        $processedPensiun = $pensiuns->map(function ($item) {
            $itemArray = $item->toArray();
            $itemArray['type'] = 'Pensiun';
            $itemArray['id'] = $item->id_pensiun;
            $itemArray['status_pengajuan'] = $item->status_pensiun;
            $itemArray['logs'] = $itemArray['log_persetujuan_pensiun'] ?? [];
            unset($itemArray['log_persetujuan_pensiun']);
            return $itemArray;
        });

        $processedPangkatGajiTunjangan = $pangkatgajitunjangans->map(function ($item) {
            $itemArray = $item->toArray();
            $itemArray['type'] = 'PangkatGajiTunjangan';
            $itemArray['id'] = $item->id_kenaikan;
            $itemArray['status_pengajuan'] = $item->status_kenaikan;
            $itemArray['logs'] = $itemArray['log_persetujuan_pangkatgajitunjangan'] ?? [];
            unset($itemArray['log_persetujuan_pangkatgajitunjangan']);
            return $itemArray;
        });

        return collect()
            ->merge($processedCuti)
            ->merge($processedLembur)
            ->merge($processedPensiun)
            ->merge($processedPangkatGajiTunjangan);
    }

    private function paginateSubmissions(Collection $submissions, int $perPage): LengthAwarePaginator
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $submissions->forPage($currentPage, $perPage)->all();

        return new LengthAwarePaginator(
            $currentItems,
            $submissions->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query()
            ]
        );
    }

    public function downloadLetterPdf($id_cuti)
    {
        try {
            // 1. Ambil data pengajuan
            $cuti = PengajuanCuti::with(['pegawai.pekerjaan.divisi', 'pekerjaan.divisi'])
                                ->where('id_cuti', $id_cuti)
                                ->firstOrFail();

            $nup = $cuti->nomor_urut_pegawai;
            $subJenisCuti = \DB::table('sub_jenis_cuti_penting')->get();

            // 2. LOGIKA MANAGER (Atasan Langsung)
            $idDivisiPengaju = $cuti->pegawai->pekerjaan->id_divisi ?? null;
            $managerData = Pekerjaan::where('id_divisi', $idDivisiPengaju)
                ->where(function($q) {
                    $q->where('jabatan', 'LIKE', '%Manager%')
                    ->orWhere('jabatan', 'LIKE', '%Manajer%');
                })
                ->with('pegawai')
                ->first();

            $namaAtasan = $managerData->pegawai->nama ?? '........................';
            $jabatanAtasan = $managerData->jabatan ?? 'Atasan Langsung';

            // 3. LOGIKA DIREKSI
            $dirOps = Pekerjaan::where('jabatan', 'Direktur Operasional')->with('pegawai')->first();
            $dirKep = Pekerjaan::where('jabatan', 'Direktur Kepatuhan')->with('pegawai')->first();

            $namaDireksi = strtoupper($dirOps->pegawai->nama ?? '............') . ' atau ' . strtoupper($dirKep->pegawai->nama ?? '............');
            $jabatanDireksi = 'Direktur Operasional atau Direktur Kepatuhan';

            // 4. LOGIKA VERIFIKATOR
            $v1 = Pekerjaan::where('jabatan', 'LIKE', '%Kepala Satker Kepatuhan%')->with('pegawai')->first();
            $v2 = Pekerjaan::where('jabatan', 'LIKE', '%Human Resources Officer%')->with('pegawai')->first();

            $namaVerif1 = strtoupper($v1->pegawai->nama ?? '........................');
            $jabatanVerif1 = "Kepala Satker Kepatuhan & M.R.";
            $namaVerif2 = strtoupper($v2->pegawai->nama ?? '........................');
            $jabatanVerif2 = "Human Resources Officer";

            // 5. Bungkus data (Pastikan KEY-nya sama persis dengan yang dipanggil di Blade)
            $data = [
                'cuti'           => $cuti,
                'sub_jenis_cuti' => $subJenisCuti,
                'is_pdf'         => true,
                'namaAtasan'     => $namaAtasan,
                'jabatanAtasan'  => $jabatanAtasan,
                'namaDireksi'    => $namaDireksi,
                'jabatanDireksi' => $jabatanDireksi,
                'namaVerif1'     => $namaVerif1,
                'jabatanVerif1'  => $jabatanVerif1,
                'namaVerif2'     => $namaVerif2,
                'jabatanVerif2'  => $jabatanVerif2,
            ];

            // 6. Generate PDF
            $pdf = Pdf::loadView('partials.letter_content', $data);
            $pdf->setPaper([0, 0, 609.45, 935.43], 'portrait');

            return $pdf->download('Surat_Cuti_' . $nup . '_' . $id_cuti . '.pdf');

        } catch (\Exception $e) {
            // Jika error, tampilkan pesan errornya biar ketahuan salahnya dimana
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getLetterDetailsByNup($id)
    {
        // 1. Ambil data pengajuan cuti
        $cuti = PengajuanCuti::with([
            'pegawai.pekerjaan.divisi',
            'jenisCuti',
            'logs.penyetuju.pekerjaan'
        ])->where('id_cuti', $id)->firstOrFail();

        $nup = $cuti->nomor_urut_pegawai;

        // 2. LOGIKA MENCARI MANAGER (ATASAN LANGSUNG) BERDASARKAN DIVISI PENGAJU
        $idDivisiPengaju = $cuti->pegawai->pekerjaan->id_divisi ?? null;
        $managerData = Pekerjaan::where('id_divisi', $idDivisiPengaju)
            ->where(function($q) {
                $q->where('jabatan', 'LIKE', '%Manager%')
                ->orWhere('jabatan', 'LIKE', '%Manajer%');
            })
            ->with('pegawai')
            ->first();

        $namaAtasan = $managerData->pegawai->nama ?? '........................';
        $jabatanAtasan = $managerData->jabatan ?? 'Atasan Langsung';

        // 3. LOGIKA MENCARI DIREKSI DINAMIS (OPS vs KEPATUHAN)
        $dirOps = Pekerjaan::where('jabatan', 'Direktur Operasional')->with('pegawai')->first();
        $dirKep = Pekerjaan::where('jabatan', 'Direktur Kepatuhan')->with('pegawai')->first();

        $namaDireksi = strtoupper($dirOps->pegawai->nama ?? '............') . ' atau ' . strtoupper($dirKep->pegawai->nama ?? '............');
        $jabatanDireksi = 'Direktur Operasional atau Direktur Kepatuhan';

        // 4. LOGIKA MENCARI VERIFIKATOR (VERSI SINGKAT)
        $v1 = Pekerjaan::where('jabatan', 'LIKE', '%Kepala Satker Kepatuhan%')->with('pegawai')->first();
        $v2 = Pekerjaan::where('jabatan', 'LIKE', '%Human Resources Officer%')->with('pegawai')->first();

        $namaVerif1 = strtoupper($v1->pegawai->nama ?? '........................');
        $jabatanVerif1 = "Kepala Satker Kepatuhan & M.R.";
        $namaVerif2 = strtoupper($v2->pegawai->nama ?? '........................');
        $jabatanVerif2 = "Human Resources Officer";

        // 5. Return ke view dengan SEMUA variabel yang diminta Blade
        return view('partials.letter_content', compact(
            'cuti',
            'namaAtasan',
            'jabatanAtasan',
            'namaDireksi',
            'jabatanDireksi',
            'namaVerif1',
            'jabatanVerif1',
            'namaVerif2',
            'jabatanVerif2'
        ));
    }

    public function getLemburLetterDetailsByNup($id_lembur)
    {
        // 2. Ambil data spesifik berdasarkan ID agar tidak tertukar data lama
        $lembur = PengajuanLembur::with([
            'pegawai.pekerjaan.divisi',
            'logPersetujuanLembur.penyetuju.pekerjaan',
        ])
        ->where('id_lembur', $id_lembur)
        ->firstOrFail();

        $manager = null;
        $skk_mr = null;
        $hro = null;
        $direksi = null;

        // 3. Iterasi log (Gunakan data yang sudah disetujui saja)
        foreach ($lembur->logPersetujuanLembur as $log) {
            if ($log->penyetuju && $log->status_persetujuan === 'disetujui') {
                $pekerjaanPenyetuju = $log->penyetuju->pekerjaan;

                if ($pekerjaanPenyetuju) {
                    $jabatanName = strtolower($pekerjaanPenyetuju->jabatan);

                    if (str_contains($jabatanName, 'manager') && !$manager) {
                        $manager = $log->penyetuju;
                    }
                    elseif ((str_contains($jabatanName, 'skk') || str_contains($jabatanName, 'kepatuhan')) && !$skk_mr) {
                        $skk_mr = $log->penyetuju;
                    }
                    elseif (str_contains($jabatanName, 'direktur') && !$direksi) {
                        $direksi = $log->penyetuju;
                    }
                    elseif ((str_contains($jabatanName, 'hro') || str_contains($jabatanName, 'human resource')) && !$hro) {
                        $hro = $log->penyetuju;
                    }
                }
            }
        }

        return view('partials.lembur_letter_content', compact('lembur', 'manager', 'skk_mr', 'hro', 'direksi'));
    }

    public function downloadLetterPdfLembur($id_lembur): Response
    {
        try {
            // 2. Ambil data spesifik berdasarkan ID Transaksi
            $lembur = PengajuanLembur::with([
                    'pegawai.pekerjaan.divisi',
                    'logPersetujuanLembur.penyetuju.pekerjaan.divisi'
                ])
                ->where('id_lembur', $id_lembur)
                ->firstOrFail();

            $nup = $lembur->nomor_urut_pegawai;

            // 3. Pencarian Penyetuju (Gunakan status 'disetujui' agar valid)
            $manager = null;
            $skk_mr = null;
            $hro = null;
            $direktur = null;

            foreach ($lembur->logPersetujuanLembur as $log) {
                if ($log->penyetuju && $log->status_persetujuan === 'disetujui') {
                    $pekerjaanPenyetuju = $log->penyetuju->pekerjaan->first();
                    if (!$pekerjaanPenyetuju) continue;

                    $jabatanName = strtolower($pekerjaanPenyetuju->jabatan);

                    if (str_contains($jabatanName, 'manager') && !$manager) {
                        $manager = $log->penyetuju;
                    }
                    elseif ((str_contains($jabatanName, 'skk') || str_contains($jabatanName, 'kepatuhan')) && !$skk_mr) {
                        $skk_mr = $log->penyetuju;
                    }
                    elseif ((str_contains($jabatanName, 'hro') || str_contains($jabatanName, 'human resource')) && !$hro) {
                        $hro = $log->penyetuju;
                    }
                    elseif (str_contains($jabatanName, 'direktur operasional') && !$direktur) {
                        $direktur = $log->penyetuju;
                    }
                }
            }

            $data = compact('lembur', 'manager', 'skk_mr', 'hro', 'direktur');
            $data['is_pdf'] = true;

            $pdfContent = view('partials.lembur_letter_content', $data)->render();

            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = Pdf::loadHtml($pdfContent);
                $pdf->setPaper('A4', 'portrait');

                return $pdf->download('Surat_Lembur_' . $nup . '_' . $id_lembur . '.pdf');
            } else {
                return response('Library PDF tidak ditemukan.', 404);
            }

        } catch (\Exception $e) {
            return response('Error: ' . $e->getMessage(), 500);
        }
    }

    public function getPensiunLetterDetailsByNup($id_pensiun)
    {
        // 2. Cari spesifik berdasarkan ID
        $pensiun = PengajuanPensiun::with([
            'pegawai.pekerjaan.divisi',
            'logPersetujuanPensiun.penyetuju.pekerjaan',
            'files'
        ])
        ->where('id_pensiun', $id_pensiun)
        ->firstOrFail();

        $skk_mr = null;
        $direktur_kepatuhan = null;
        $direktur_utama = null;
        $hro = null;

        // 3. Ambil tanda tangan dari log yang berstatus 'disetujui'
        foreach ($pensiun->logPersetujuanPensiun as $log) {
            if ($log->penyetuju && $log->status_persetujuan === 'disetujui') {
                $pekerjaanPenyetuju = $log->penyetuju->pekerjaan->first();

                if ($pekerjaanPenyetuju) {
                    $jabatanName = strtolower($pekerjaanPenyetuju->jabatan);

                    if ((str_contains($jabatanName, 'skk') || str_contains($jabatanName, 'kepatuhan')) && !$skk_mr) {
                        $skk_mr = $log->penyetuju;
                    }
                    elseif (str_contains($jabatanName, 'direktur kepatuhan') && !$direktur_kepatuhan) {
                        $direktur_kepatuhan = $log->penyetuju;
                    }
                    elseif (str_contains($jabatanName, 'direktur utama') && !$direktur_utama) {
                        $direktur_utama = $log->penyetuju;
                    }
                    elseif ((str_contains($jabatanName, 'hro') || str_contains($jabatanName, 'human resource')) && !$hro) {
                        $hro = $log->penyetuju;
                    }
                }
            }
        }

        return view('partials.pensiun_letter_content', compact(
            'pensiun', 'skk_mr', 'direktur_kepatuhan', 'direktur_utama', 'hro'
        ));
    }

    public function downloadLetterPdfPensiun($id_pensiun): Response
    {
        try {
            // 2. Cari spesifik berdasarkan ID Transaksi
            $pensiun = PengajuanPensiun::with([
                'pegawai.pekerjaan.divisi',
                'logPersetujuanPensiun.penyetuju.pekerjaan',
                'files'
            ])
            ->where('id_pensiun', $id_pensiun) // <--- Kunci utama keakuratan data
            ->firstOrFail();

            $nup = $pensiun->nomor_urut_pegawai;

            $skk_mr = null;
            $direktur_kepatuhan = null;
            $direktur_utama = null;
            $hro = null;

            // 3. Ambil data tanda tangan hanya dari log yang sudah 'disetujui'
            foreach ($pensiun->logPersetujuanPensiun as $log) {
                if ($log->penyetuju && $log->status_persetujuan === 'disetujui') {
                    $pekerjaanPenyetuju = $log->penyetuju->pekerjaan->first();
                    if (!$pekerjaanPenyetuju) continue;

                    $jabatanName = strtolower($pekerjaanPenyetuju->jabatan);

                    if ((str_contains($jabatanName, 'skk') || str_contains($jabatanName, 'kepatuhan')) && !$skk_mr) {
                        $skk_mr = $log->penyetuju;
                    }
                    elseif (str_contains($jabatanName, 'direktur kepatuhan') && !$direktur_kepatuhan) {
                        $direktur_kepatuhan = $log->penyetuju;
                    }
                    elseif (str_contains($jabatanName, 'direktur utama') && !$direktur_utama) {
                        $direktur_utama = $log->penyetuju;
                    }
                    elseif ((str_contains($jabatanName, 'hro') || str_contains($jabatanName, 'human resource')) && !$hro) {
                        $hro = $log->penyetuju;
                    }
                }
            }

            // 4. Kirim data ke view
            $data = compact('pensiun', 'skk_mr', 'direktur_kepatuhan', 'direktur_utama', 'hro');
            $data['is_pdf'] = true;

            $pdfContent = view('partials.pensiun_letter_content', $data)->render();

            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = Pdf::loadHtml($pdfContent);
                $pdf->setPaper('A4', 'portrait');

                // Nama file mengandung ID agar unik
                return $pdf->download('Surat_Pensiun_' . $nup . '_' . $id_pensiun . '.pdf');
            } else {
                return response('Library PDF (DomPDF) tidak ditemukan.', 404);
            }

        } catch (\Exception $e) {
            return response('Gagal mendownload PDF: ' . $e->getMessage(), 500);
        }
    }

    public function getPangkatgajitunjanganLetterDetailsByNup($id_kenaikan)
    {
        // 2. Ambil data berdasarkan ID unik kenaikan pangkat
        $pangkatgajitunjangan = PengajuanPangkatgajitunjangan::with([
            'pegawai.pekerjaan.divisi',
            'logPersetujuanPangkatgajitunjangan.penyetuju.pekerjaan',
            'files'
        ])
        ->where('id_kenaikan', $id_kenaikan) // <--- Cari pakai ID, bukan NUP
        ->firstOrFail();

        $skk_mr = null;
        $direktur_kepatuhan = null;
        $direktur_utama = null;
        $hro = null;

        // 3. Ambil data penanda tangan hanya dari log yang sudah 'disetujui'
        foreach ($pangkatgajitunjangan->logPersetujuanPangkatgajitunjangan as $log) {
            if ($log->penyetuju && $log->status_persetujuan === 'disetujui') {
                $pekerjaanPenyetuju = $log->penyetuju->pekerjaan->first();

                if ($pekerjaanPenyetuju) {
                    $jabatanName = strtolower($pekerjaanPenyetuju->jabatan);

                    if ((str_contains($jabatanName, 'skk') || str_contains($jabatanName, 'kepatuhan')) && !$skk_mr) {
                        $skk_mr = $log->penyetuju;
                    }
                    elseif (str_contains($jabatanName, 'direktur kepatuhan') && !$direktur_kepatuhan) {
                        $direktur_kepatuhan = $log->penyetuju;
                    }
                    elseif (str_contains($jabatanName, 'direktur utama') && !$direktur_utama) {
                        $direktur_utama = $log->penyetuju;
                    }
                    elseif ((str_contains($jabatanName, 'hro') || str_contains($jabatanName, 'human resource')) && !$hro) {
                        $hro = $log->penyetuju;
                    }
                }
            }
        }

        // 4. Mengembalikan ke view dengan data yang sudah pasti akurat
        return view('partials.pangkatgajitunjangan_letter_content', compact(
            'pangkatgajitunjangan',
            'skk_mr',
            'direktur_kepatuhan',
            'direktur_utama',
            'hro'
        ));
    }

    public function downloadLetterPdfPangkatGajiTunjangan($id): Response
    {
        try {
            // 2. Ambil data SPESIFIK berdasarkan ID unik kenaikan
            $pangkatgajitunjangan = PengajuanPangkatgajitunjangan::with([
                'pegawai.pekerjaan.divisi',
                'logPersetujuanPangkatgajitunjangan.penyetuju.pekerjaan',
                'files'
            ])
            ->where('id_kenaikan', $id)
            ->firstOrFail();

            $nup = $pangkatgajitunjangan->nomor_urut_pegawai;

            $skk_mr = null;
            $direktur_kepatuhan = null;
            $direktur_utama = null;
            $hro = null;

            // 3. Cari penanda tangan hanya dari log yang sudah 'disetujui'
            foreach ($pangkatgajitunjangan->logPersetujuanPangkatgajitunjangan as $log) {
                if ($log->penyetuju && $log->status_persetujuan === 'disetujui') {
                    $pekerjaanPenyetuju = $log->penyetuju->pekerjaan->first();
                    if (!$pekerjaanPenyetuju) continue;

                    $jabatanName = strtolower($pekerjaanPenyetuju->jabatan);

                    if ((str_contains($jabatanName, 'skk') || str_contains($jabatanName, 'kepatuhan')) && !$skk_mr) {
                        $skk_mr = $log->penyetuju;
                    }
                    elseif (str_contains($jabatanName, 'direktur kepatuhan') && !$direktur_kepatuhan) {
                        $direktur_kepatuhan = $log->penyetuju;
                    }
                    elseif (str_contains($jabatanName, 'direktur utama') && !$direktur_utama) {
                        $direktur_utama = $log->penyetuju;
                    }
                    elseif ((str_contains($jabatanName, 'hro') || str_contains($jabatanName, 'human resource')) && !$hro) {
                        $hro = $log->penyetuju;
                    }
                }
            }

            // 4. Siapkan data untuk PDF
            $data = compact('pangkatgajitunjangan', 'skk_mr', 'direktur_kepatuhan', 'direktur_utama', 'hro');
            $data['is_pdf'] = true;

            $pdfContent = view('partials.pangkatgajitunjangan_letter_content', $data)->render();

            // 5. Logika Nama File Dinamis & Unik
            $jenisPengajuan = trim($pangkatgajitunjangan->jenis_pengajuan ?? 'Pengajuan');
            $cleanFileName = preg_replace('/[^A-Za-z0-9_]/', '', str_replace(' ', '_', $jenisPengajuan));
            $fileName = 'Surat_' . $cleanFileName . '_' . $nup . '_' . $id . '.pdf';

            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = Pdf::loadHtml($pdfContent);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download($fileName);
            } else {
                return response('Library PDF tidak ditemukan.', 404);
            }

        } catch (\Exception $e) {
            return response('Gagal mengunduh PDF: ' . $e->getMessage(), 500);
        }
    }

}
