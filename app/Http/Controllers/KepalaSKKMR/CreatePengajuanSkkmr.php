<?php

namespace App\Http\Controllers\KepalaSKKMR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\LogPersetujuanCuti;
use App\Models\LogPersetujuanLembur;
use App\Models\LogPersetujuanPensiun;
use App\Models\LogPersetujuanPangkatgajitunjangan;
use App\Services\SubmissionProcessorService; // Import Service Anda

class CreatePengajuanSkkmr extends Controller
{
    protected $submissionProcessor;

    public function __construct(SubmissionProcessorService $submissionProcessor)
    {
        $this->submissionProcessor = $submissionProcessor;
    }

    public function buatPengajuanSkkmr(Request $request)
    {
        $user = Auth::user();
        $userNomorUrut = $user->nomor_urut_pegawai;

        // --- Logika Kartu (Existing) ---
        $pendingCutiSkkmr = LogPersetujuanCuti::where('nomor_urut_pegawai', $userNomorUrut)->where('status_pengajuan', 'diproses')->exists();
        $pendingLemburSkkmr = LogPersetujuanLembur::where('nomor_urut_pegawai', $userNomorUrut)->where('status_persetujuan', 'diproses')->exists();
        $pendingPensiunSkkmr = LogPersetujuanPensiun::where('nomor_urut_pegawai', $userNomorUrut)->where('status_persetujuan', 'diproses')->exists();
        $pendingPangkatgajitunjanganSkkmr = LogPersetujuanPangkatgajitunjangan::where('nomor_urut_pegawai', $userNomorUrut)->where('status_persetujuan', 'diproses')->exists();

        // --- Logika Tabel Pengajuan (Tambahan) ---
        // 1. Ambil data mentah (Pastikan fungsi fetchSubmissions tersedia di controller ini atau trait)
        $allSubmissions = $this->fetchSubmissions($user, $request);

        // 2. Filter otomatis jika user klik card (misal kirim ?type=Cuti di URL)
        $filterType = $request->type;
        if ($filterType) {
            $allSubmissions = $allSubmissions->filter(fn($item) => strtolower($item['type']) === strtolower($filterType));
        }

        // 3. Proses via Service
        $processedSubmissions = $this->submissionProcessor->processSubmissions($allSubmissions->sortByDesc('created_at'));

        // 4. Pagination
        $paginatedSubmissions = $this->paginateSubmissions($processedSubmissions, 5) // Tampilkan 5 saja agar tidak kepanjangan
                                    ->appends($request->query());

        $pageTitle = 'Manajemen Pengajuan';

        // 1. Definisikan Route Utama untuk halaman ini
        $parentRoute = route('skkmr.pilihpengajuan');

        // 2. Susun Breadcrumbs secara dinamis
        $breadcrumbs = [
            'Beranda' => route('skkmr.dashboardskkmr'),
        ];

        if ($filterType) {
            // Jika ada filter (contoh: Pengajuan Cuti)
            // Level 2 menjadi link yang bisa diklik untuk kembali ke "Semua Pengajuan"
            $breadcrumbs['Manajemen Pengajuan ↦ Pengajuan Saya'] = $parentRoute;

            // Level 3 menjadi teks aktif (tidak bisa diklik)
            $breadcrumbs['Pengajuan ' . ucfirst($filterType)] = null;
        } else {
            // Jika tidak ada filter, ini adalah halaman aktif (tidak perlu link)
            $breadcrumbs['Manajemen Pengajuan ↦ Pengajuan Saya'] = null;
        }

        return view('skkmr.pengajuanskkmr', compact(
            'pageTitle', 'breadcrumbs', 'pendingCutiSkkmr', 'pendingLemburSkkmr',
            'pendingPensiunSkkmr', 'pendingPangkatgajitunjanganSkkmr', 'paginatedSubmissions'
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
            $query->orderByDesc('updated_at');
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
