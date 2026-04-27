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

        // Logika Breadcrumbs Level 2 & 3
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
            $breadcrumbs['Pengajuan ' . $formattedType] = null; // Level 3 (Rapi!)
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
            'isManager' => $isManager, // 🟢 Ganti variabel yang dikirim ke blade
            'isSKKMR' => $isSKKMR
        ]);
    }

    private function fetchSubmissions($user, $request): Collection
    {
        // Ambil input filter dari request
        $dariTanggal = $request->dari_tanggal;
        $hinggaTanggal = $request->hingga_tanggal;

        // Helper untuk menerapkan filter tanggal pada query
        $applyDateFilters = function ($query) use ($dariTanggal, $hinggaTanggal) {
            if ($dariTanggal) $query->whereDate('created_at', '>=', $dariTanggal);
            if ($hinggaTanggal) $query->whereDate('created_at', '<=', $hinggaTanggal);
            return $query;
        };

        // 1. Ambil data cuti - UPDATE: Urutkan log berdasarkan id_cuti
        $cutis = $applyDateFilters($user->cutis())->with(['logs' => function ($query) {
            $query->orderByDesc('updated_at')->orderByDesc('id_cuti');
        }])->get();

        $lemburs = $applyDateFilters($user->lemburs())->with(['logPersetujuanLembur' => function ($query) {
            $query->orderByDesc('updated_at');
        }])->get();

        $pensiuns = $applyDateFilters($user->pensiuns())->with(['logPersetujuanPensiun' => function ($query) {
            $query->orderByDesc('update_at');
        }])->get();

        $pangkatgajitunjangans = $applyDateFilters($user->pangkatgajitunjangans())->with(['logPersetujuanPangkatgajitunjangan' => function ($query) {
            $query->orderByDesc('updated_at');
        }])->get();

        // 2. Mapping data Cuti - UPDATE: Tambahkan alias 'id' agar tidak error di View/Processor
        $processedCuti = $cutis->map(function ($item) {
            $itemArray = $item->toArray();
            $itemArray['type'] = 'Cuti';
            $itemArray['id'] = $item->id_cuti;
            return $itemArray;
        });

        $processedLembur = $lemburs->map(function ($item) {
            $itemArray = $item->toArray(); $itemArray['type'] = 'Lembur'; $itemArray['id'] = $item->id_lembur;
            $itemArray['logs'] = $itemArray['log_persetujuan_lembur'] ?? []; unset($itemArray['log_persetujuan_lembur']); return $itemArray;
        });

        $processedPensiun = $pensiuns->map(function ($item) {
            $itemArray = $item->toArray(); $itemArray['type'] = 'Pensiun'; $itemArray['id'] = $item->id_pensiun;
            $itemArray['logs'] = $itemArray['log_persetujuan_pensiun'] ?? []; unset($itemArray['log_persetujuan_pensiun']); return $itemArray;
        });

        $processedPangkatGajiTunjangan = $pangkatgajitunjangans->map(function ($item) {
            $itemArray = $item->toArray();
            $itemArray['type'] = 'PangkatGajiTunjangan';

            // 🟢 TAMBAHKAN INI AGAR KONSISTEN DENGAN LAINNYA
            $itemArray['id'] = $item->id_kenaikan;

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

    public function downloadLetterPdf($nup)
    {
        try {
            // 1. Ambil data pengajuan
            $cuti = PengajuanCuti::with(['pegawai.pekerjaan.divisi', 'pekerjaan.divisi'])
                                    ->where('nomor_urut_pegawai', $nup)
                                    ->firstOrFail();

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

            return $pdf->download('Surat_Cuti_' . $nup . '.pdf');

        } catch (\Exception $e) {
            // Jika error, tampilkan pesan errornya biar ketahuan salahnya dimana
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getLetterDetailsByNup($nup)
    {
        // 1. Ambil data pengajuan cuti
        $cuti = PengajuanCuti::with([
            'pegawai.pekerjaan.divisi',
            'jenisCuti',
            'logs.penyetuju.pekerjaan'
        ])->where('nomor_urut_pegawai', $nup)->firstOrFail();

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

    public function getLemburLetterDetailsByNup($nup)
    {
        // Eager loading relasi dan mengambil data lembur terbaru berdasarkan NUP
        $lembur = PengajuanLembur::with([
            'pegawai.pekerjaan.divisi',
            'logPersetujuanLembur.penyetuju.pekerjaan',
        ])
        ->where('nomor_urut_pegawai', $nup)
        ->latest('created_at') // Mengambil yang terbaru berdasarkan waktu buat
        ->firstOrFail();
        // dd($lembur->toArray());

        $manager = null;
        $skk_mr = null;
        $hro = null;

        // Iterasi log persetujuan untuk mendapatkan data penyetuju sesuai alur lembur
        foreach ($lembur->logPersetujuanLembur as $log) {
            if ($log->penyetuju) {
                $pekerjaanPenyetuju = $log->penyetuju->pekerjaan->first();

                if ($pekerjaanPenyetuju) {
                    $jabatanName = strtolower($pekerjaanPenyetuju->jabatan);

                    // Filter berdasarkan kata kunci jabatan
                    if (str_contains($jabatanName, 'manager') && !$manager) {
                        $manager = $log->penyetuju;
                    }
                    elseif (str_contains($jabatanName, 'skk mr') && !$skk_mr) {
                        $skk_mr = $log->penyetuju;
                    }
                    elseif ((str_contains($jabatanName, 'hro') || str_contains($jabatanName, 'human resource')) && !$hro) {
                        $hro = $log->penyetuju;
                    }

                    if ($manager && $skk_mr && $hro) break;
                }
            }
        }

        // Mengembalikan ke view khusus lembur
        return view('partials.lembur_letter_content', compact('lembur', 'manager', 'skk_mr', 'hro'));
    }

    public function downloadLetterPdfLembur($nup): Response
    {
        // 1. Eager loading data yang diperlukan untuk LEMBUR
        $lembur = PengajuanLembur::with([
                'pegawai.pekerjaan.divisi',
                'logPersetujuanLembur.penyetuju.pekerjaan.divisi' // Menggunakan relasi log persetujuan lembur
            ])
            ->where('nomor_urut_pegawai', $nup)
            ->latest('created_at') // Ambil data lembur yang terbaru untuk NUP ini
            ->firstOrFail();

        // 2. Duplikasi Logika Pencarian Penyetuju (Manager, HRO, dll)
        // Sesuaikan variabel penyetuju dengan alur persetujuan lembur Anda
        $manager = null;
        $skk_mr = null;
        $hro = null;
        $direktur = null; // Tambahkan jika direktur ikut tanda tangan di surat lembur

        foreach ($lembur->logPersetujuanLembur as $log) {
            if ($log->penyetuju && $log->penyetuju->pekerjaan->first()) {
                $pekerjaanPenyetuju = $log->penyetuju->pekerjaan->first();
                $jabatanName = strtolower($pekerjaanPenyetuju->jabatan);

                if (str_contains($jabatanName, 'manager') && !$manager) {
                    $manager = $log->penyetuju;
                } elseif (str_contains($jabatanName, 'skk mr') && !$skk_mr) {
                    $skk_mr = $log->penyetuju;
                } elseif ((str_contains($jabatanName, 'hro') || str_contains($jabatanName, 'human resource')) && !$hro) {
                    $hro = $log->penyetuju;
                } elseif (str_contains($jabatanName, 'direktur operasional') && !$direktur) {
                    $direktur = $log->penyetuju;
                }

                // Hentikan loop jika semua penanda tangan sudah ditemukan
                if ($manager && $hro && $skk_mr && $direktur) break;
            }
        }

        // 3. Kirim SEMUA variabel yang diperlukan ke view lembur
        // Pastikan partial view yang digunakan adalah view untuk konten surat lembur
        $data = compact('lembur', 'manager', 'skk_mr', 'hro', 'direktur');
        $data['is_pdf'] = true;

        // Menggunakan partial view KHUSUS untuk konten surat lembur
        $pdfContent = view(view: 'partials.lembur_letter_content', data: $data)->render();

        // ... logika pembuatan PDF selanjutnya ...
        if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
            $pdf = Pdf::loadHtml(string: $pdfContent);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->download(filename: 'Surat_Lembur_' . $nup . '.pdf');
        } else {
            return response(content: 'Download functionality requires a PDF library installed in Laravel.', status: 404);
        }
    }

    public function getPensiunLetterDetailsByNup($nup)
    {
        // Eager loading relasi dan mengambil data pensiun terbaru berdasarkan NUP
        $pensiun = PengajuanPensiun::with([
            'pegawai.pekerjaan.divisi',
            'logPersetujuanPensiun.penyetuju.pekerjaan',
            // Menambahkan relasi untuk mengambil data file upload dari tabel file_persyaratanpensiun
            'files'
        ])
        ->where('nomor_urut_pegawai', $nup)
        ->latest('created_at') // Mengambil yang terbaru berdasarkan waktu buat
        ->firstOrFail();
        // dd($pensiun->toArray());

        // $manager = null;
        $skk_mr = null;
        $direktur_kepatuhan = null;
        $direktur_utama = null;
        $hro = null;

        // Iterasi log persetujuan untuk mendapatkan data penyetuju sesuai alur pensiun
        foreach ($pensiun->logPersetujuanPensiun as $log) {
            if ($log->penyetuju) {
                $pekerjaanPenyetuju = $log->penyetuju->pekerjaan->first();

                if ($pekerjaanPenyetuju) {
                    $jabatanName = strtolower($pekerjaanPenyetuju->jabatan);

                    // Filter berdasarkan kata kunci jabatan
                    if (str_contains($jabatanName, 'skk mr') && !$skk_mr) {
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

                    if ($skk_mr && $direktur_kepatuhan && $direktur_utama && $hro) break;
                }
            }
        }

        // Mengembalikan ke view khusus pensiun
        return view('partials.pensiun_letter_content', compact('pensiun', 'skk_mr', 'direktur_kepatuhan', 'direktur_utama','hro'));
    }

    public function downloadLetterPdfPensiun($nup): Response
    {
        // 1. Eager loading data yang diperlukan untuk LEMBUR
        $pensiun = PengajuanPensiun::with([
            'pegawai.pekerjaan.divisi',
            'logPersetujuanPensiun.penyetuju.pekerjaan',
            'files'
        ])
        ->where('nomor_urut_pegawai', $nup)
        ->latest('created_at') // Mengambil yang terbaru berdasarkan waktu buat
        ->firstOrFail();
        // dd($lembur->toArray());

        // $manager = null;
        $skk_mr = null;
        $direktur_kepatuhan = null;
        $direktur_utama = null;
        $hro = null;

        foreach ($pensiun->logPersetujuanPensiun as $log) {
            if ($log->penyetuju && $log->penyetuju->pekerjaan->first()) {
                $pekerjaanPenyetuju = $log->penyetuju->pekerjaan->first();
                $jabatanName = strtolower($pekerjaanPenyetuju->jabatan);

                // Filter berdasarkan kata kunci jabatan
                if (str_contains($jabatanName, 'skk mr') && !$skk_mr) {
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

                if ($skk_mr && $direktur_kepatuhan && $direktur_utama && $hro) break;
            }
        }

        // 3. Kirim SEMUA variabel yang diperlukan ke view pensiun
        // Pastikan partial view yang digunakan adalah view untuk konten surat pensiun
        $data = compact('pensiun', 'skk_mr', 'direktur_kepatuhan', 'direktur_utama','hro');
        $data['is_pdf'] = true;

        // Menggunakan partial view KHUSUS untuk konten surat pensiun
        $pdfContent = view(view: 'partials.pensiun_letter_content', data: $data)->render();

        // ... logika pembuatan PDF selanjutnya ...
        if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
            $pdf = Pdf::loadHtml(string: $pdfContent);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->download(filename: 'Surat_Pensiun_' . $nup . '.pdf');
        } else {
            return response(content: 'Download functionality requires a PDF library installed in Laravel.', status: 404);
        }
    }

    public function getPangkatgajitunjanganLetterDetailsByNup($nup)
    {
        // Eager loading relasi dan mengambil data pangkatgajitunjangan terbaru berdasarkan NUP
        $pangkatgajitunjangan = PengajuanPangkatgajitunjangan::with([
            'pegawai.pekerjaan.divisi',
            'logPersetujuanPangkatgajitunjangan.penyetuju.pekerjaan',
            // Menambahkan relasi untuk mengambil data file upload dari tabel file_persyaratanpangkatgajitunjangan
            'files'
        ])
        ->where('nomor_urut_pegawai', $nup)
        ->latest('created_at') // Mengambil yang terbaru berdasarkan waktu buat
        ->firstOrFail();
        // dd($pangkatgajitunjangan->toArray());

        // $manager = null;
        $skk_mr = null;
        $direktur_kepatuhan = null;
        $direktur_utama = null;
        $hro = null;

        // Iterasi log persetujuan untuk mendapatkan data penyetuju sesuai alur pangkatgajitunjangan
        foreach ($pangkatgajitunjangan->logPersetujuanPangkatgajitunjangan as $log) {
            if ($log->penyetuju) {
                $pekerjaanPenyetuju = $log->penyetuju->pekerjaan->first();

                if ($pekerjaanPenyetuju) {
                    $jabatanName = strtolower($pekerjaanPenyetuju->jabatan);

                    // Filter berdasarkan kata kunci jabatan
                    if (str_contains($jabatanName, 'skk mr') && !$skk_mr) {
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

                    if ($skk_mr && $direktur_kepatuhan && $direktur_utama && $hro) break;
                }
            }
        }

        // Mengembalikan ke view khusus pangkatgajitunjangan
        return view('partials.pangkatgajitunjangan_letter_content', compact('pangkatgajitunjangan', 'skk_mr', 'direktur_kepatuhan', 'direktur_utama','hro'));
    }

    public function downloadLetterPdfPangkatGajiTunjangan($nup): Response
    {
        // 1. Eager loading data yang diperlukan untuk LEMBUR
        $pangkatgajitunjangan = PengajuanPangkatgajitunjangan::with([
            'pegawai.pekerjaan.divisi',
            'logPersetujuanPangkatgajitunjangan.penyetuju.pekerjaan',
            // Menambahkan relasi untuk mengambil data file upload dari tabel file_persyaratanpangkatgajitunjangan
            'files'
        ])
        ->where('nomor_urut_pegawai', $nup)
        ->latest('created_at') // Mengambil yang terbaru berdasarkan waktu buat
        ->firstOrFail();
        // dd($pangkatgajitunjangan->toArray());

        // $manager = null;
        $skk_mr = null;
        $direktur_kepatuhan = null;
        $direktur_utama = null;
        $hro = null;

        // Iterasi log persetujuan untuk mendapatkan data penyetuju sesuai alur pangkatgajitunjangan
        foreach ($pangkatgajitunjangan->logPersetujuanPangkatgajitunjangan as $log) {
            if ($log->penyetuju) {
                $pekerjaanPenyetuju = $log->penyetuju->pekerjaan->first();

                if ($pekerjaanPenyetuju) {
                    $jabatanName = strtolower($pekerjaanPenyetuju->jabatan);

                    // Filter berdasarkan kata kunci jabatan
                    if (str_contains($jabatanName, 'skk mr') && !$skk_mr) {
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

                    if ($skk_mr && $direktur_kepatuhan && $direktur_utama && $hro) break;
                }
            }
        }

        // 3. Kirim SEMUA variabel yang diperlukan ke view pensiun
        // Pastikan partial view yang digunakan adalah view untuk konten surat pensiun
        $data = compact('pangkatgajitunjangan', 'skk_mr', 'direktur_kepatuhan', 'direktur_utama','hro');
        $data['is_pdf'] = true;

        // Menggunakan partial view KHUSUS untuk konten surat pensiun
        $pdfContent = view(view: 'partials.pangkatgajitunjangan_letter_content', data: $data)->render();

        // --- LOGIKA BARU UNTUK NAMA FILE ---
        // 1. Ambil jenis pengajuan (asumsi kolomnya bernama 'jenis_pengajuan')
        $jenisPengajuan = trim($pangkatgajitunjangan->jenis_pengajuan ?? 'Pengajuan');

        // 2. Bersihkan nama file: ganti spasi dengan underscore (_) dan hapus karakter yang tidak aman
        // Ini akan mengubah "Kenaikan Pangkat Reguler" menjadi "Kenaikan_Pangkat_Reguler"
        $cleanFileName = preg_replace('/[^A-Za-z0-9_]/', '', str_replace(' ', '_', $jenisPengajuan));

        // 3. Buat nama file lengkap yang akan dikirim ke browser
        $fileName = 'Surat_' . $cleanFileName . '_' . $nup . '.pdf';
        // --- AKHIR LOGIKA BARU ---

        if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
            $pdf = Pdf::loadHtml(string: $pdfContent);
            $pdf->setPaper('A4', 'portrait');

            // Gunakan variabel $fileName yang sudah dibuat dinamis di sini
            return $pdf->download(filename: $fileName);
        } else {
            return response(content: 'Download functionality requires a PDF library installed in Laravel.', status: 404);
        }
    }

}
