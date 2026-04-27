<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\SubmissionProcessorService; // Import Service Anda

class CreatePengajuan extends Controller
{
    protected $submissionProcessor;

    public function __construct(SubmissionProcessorService $submissionProcessor)
    {
        $this->submissionProcessor = $submissionProcessor;
    }

    public function buatPengajuanManager(Request $request)
    {
        $user = Auth::user();
        $userNomorUrut = $user->nomor_urut_pegawai;

        // --- 1. Logika Status Kartu (LENGKAP 4 TABEL) ---
        $pendingCutiManager = \DB::table('log_persetujuan_cuti')
            ->where('nomor_urut_pegawai', $userNomorUrut)
            ->where('status_pengajuan', 'diproses')->exists();

        $pendingLemburManager = \DB::table('log_persetujuan_lembur')
            ->where('nomor_urut_pegawai', $userNomorUrut)
            ->where('status_persetujuan', 'diproses')->exists();

        $pendingPensiunManager = \DB::table('log_persetujuan_pensiun')
            ->where('nomor_urut_pegawai', $userNomorUrut)
            ->where('status_persetujuan', 'diproses')->exists();

        // --- TAMBAHAN: Logika Pangkat, Gaji & Tunjangan ---
        $pendingPangkatManager = \DB::table('log_persetujuan_pangkatgajitunjangan')
            ->where('nomor_urut_pegawai', $userNomorUrut)
            ->where('status_persetujuan', 'diproses')->exists();

        // KUNCI: Sekarang ngecek ke 4 variabel
        $isAnyPending = ($pendingCutiManager || $pendingLemburManager || $pendingPensiunManager || $pendingPangkatManager);

        // --- 2. Logika Tabel Pengajuan (Tetap) ---
        $allSubmissions = $this->fetchSubmissions($user, $request);
        $filterType = $request->type;
        if ($filterType) {
            $allSubmissions = $allSubmissions->filter(fn($item) => strtolower($item['type']) === strtolower($filterType));
        }
        $processedSubmissions = $this->submissionProcessor->processSubmissions($allSubmissions->sortByDesc('created_at'));
        $paginatedSubmissions = $this->paginateSubmissions($processedSubmissions, 5)->appends($request->query());

        // --- 3. Breadcrumbs & Route (Tetap) ---
        $pageTitle = 'Manajemen Pengajuan';
        $dashboardRoute = $user->dashboard_link;
        $parentRoute = route('manager.pilihpengajuan');
        $breadcrumbs = ['Beranda' => $dashboardRoute];
        if ($filterType) {
            $breadcrumbs['Manajemen Pengajuan ↦ Pengajuan Saya'] = $parentRoute;
            $breadcrumbs['Pengajuan ' . ucfirst($filterType)] = null;
        } else { $breadcrumbs['Manajemen Pengajuan ↦ Pengajuan Saya'] = null; }

        // --- 4. Return View (SEMUA VARIABEL MASUK SINI) ---
        return view('manager.pengajuanmanager', compact(
            'pageTitle',
            'breadcrumbs',
            'isAnyPending',
            'pendingCutiManager',
            'pendingLemburManager',
            'pendingPensiunManager',
            'pendingPangkatManager', // <--- Pastikan ini terkirim!
            'paginatedSubmissions',
            'dashboardRoute'
        ));
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

        // Terapkan filter tanggal pada setiap query database
        $cutis = $applyDateFilters($user->cutis())->with(['logs' => function ($query) {
            $query->orderByDesc('updated_at');
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

        // Mapping ulang kunci relasi ke nama standar 'logs' dan konversi ke array
        // ... (sisa kode mapping Anda tetap sama di sini) ...
        $processedCuti = $cutis->map(function ($item) {
            $itemArray = $item->toArray(); $itemArray['type'] = 'Cuti'; return $itemArray;
        });
        $processedLembur = $lemburs->map(function ($item) {
            $itemArray = $item->toArray(); $itemArray['type'] = 'Lembur';
            $itemArray['logs'] = $itemArray['log_persetujuan_lembur'] ?? []; unset($itemArray['log_persetujuan_lembur']); return $itemArray;
        });
        $processedPensiun = $pensiuns->map(function ($item) {
            $itemArray = $item->toArray(); $itemArray['type'] = 'Pensiun';
            $itemArray['logs'] = $itemArray['log_persetujuan_pensiun'] ?? []; unset($itemArray['log_persetujuan_pensiun']); return $itemArray;
        });
        $processedPangkatGajiTunjangan = $pangkatgajitunjangans->map(function ($item) {
            $itemArray = $item->toArray(); $itemArray['type'] = 'PangkatGajiTunjangan';
            $itemArray['logs'] = $itemArray['log_persetujuan_pangkatgajitunjangan'] ?? []; unset($itemArray['log_persetujuan_pangkatgajitunjangan']); return $itemArray;
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

}
