<?php

namespace App\Http\Controllers\Cuti;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Services\SubmissionProcessorService;
use App\Models\JenisCuti;
use App\Models\SubJenisCutiPenting;
use App\Models\Pekerjaan;
use App\Models\PengajuanCuti;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class CutiController extends Controller
{
    protected $submissionProcessor;

    public function __construct(SubmissionProcessorService $submissionProcessor)
    {
        $this->submissionProcessor = $submissionProcessor;
    }

    public function formCutiIzin()
    {
        $user = Auth::user();

        // 1. Ambil data Role secara dinamis dari tabel roles_mapping
        $roleMapping = \DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        // Cek apakah manager berdasarkan route_name di DB
        $isManagerOrKepala = $roleMapping && str_contains($roleMapping->route_name, 'manager');

        // 2. Data Master
        $pekerjaanData = Pekerjaan::where('nomor_urut_pegawai', $user->nomor_urut_pegawai)->first();
        $jenisCuti = JenisCuti::all();
        $durationsMapping = $jenisCuti->pluck('durasi_hari', 'nama_cuti')->toJson();
        $subJenisCuti = SubJenisCutiPenting::all();
        $jatahCutiTahunanMaksimal = $jenisCuti->where('nama_cuti', 'Cuti Tahunan')->first()->durasi_hari ?? 0;

        // --- VARIABEL YANG DIKEMBALIKAN ---
        $jenisPengajuan = 'cuti';
        $submissionsType = 'Cuti'; // Sesuaikan string ini dengan kebutuhan logika view Anda
        // ----------------------------------

        // 3. Logika Navigasi Dinamis
        $dashboardUrl = $user->dashboard_link;
        $roleLabel = $roleMapping->role_name ?? 'Pegawai';

        $parentRouteName = $isManagerOrKepala ? 'manager.pilihpengajuan' : 'datapengajuan.formDataPengajuan';
        $parentLabel = $isManagerOrKepala ? 'Manajemen Pengajuan ↦ Approval Pengajuan' : 'Data Pengajuan';
        $pageTitle = 'Pengajuan Cuti dan Izin ' . $roleLabel;

        // 4. Breadcrumbs
        $breadcrumbs = [
            'Beranda' => $dashboardUrl,
            $parentLabel => route($parentRouteName),
            $pageTitle => null
        ];

        return view('cuti.cutiizin', compact(
            'jenisCuti',
            'durationsMapping',
            'subJenisCuti',
            'jatahCutiTahunanMaksimal',
            'pekerjaanData',
            'pageTitle',
            'breadcrumbs',
            'parentRouteName',
            'jenisPengajuan',
            'submissionsType' // Pastikan ini ikut dikirim ke view
        ));
    }

    public function updateCutiizin(Request $request)
    {
        // 1. Validasi Data
        $validatedData = $request->validate([
            'nomor_urut_pegawai' => 'required|string|max:15',
            'nama_pegawai' => 'required|string|max:255',
            'jenis_cuti' => 'required|string|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jumlah_cuti' => 'required|integer|min:1',
            'jatah_periode_hari' => ['nullable', 'string', 'regex:/^\d+\s*Hari?$/i'],
            'sisa_cuti' => 'nullable|integer',
            'keterangan' => 'nullable|string|max:500',
            'sub_jenis_cuti' => 'sometimes|required_if:jenis_cuti,Cuti Alasan Penting dan Mendesak|nullable|exists:sub_jenis_cuti_penting,id',
        ]);

        $nomorPegawai = $request->input('nomor_urut_pegawai');
        \Log::info('Request masuk ke Controller untuk NIP: ' . $nomorPegawai);

        // 2. Ambil Data User & Divisi
        $userLogin = auth()->user(); // Gunakan variabel berbeda agar tidak bentrok dengan pencarian user pemilik NIP
        $userOwner = User::with('pegawai.pekerjaan')->where('nomor_urut_pegawai', $nomorPegawai)->first();

        if (!$userOwner || !$userOwner->pegawai || !$userOwner->pegawai->pekerjaan) {
            return back()->with('error', 'Data divisi pegawai tidak ditemukan.')->withInput();
        }

        $idDivisi = $userOwner->pegawai->pekerjaan->id_divisi ?? null;
        $startDate = $request->input('tanggal_mulai');
        $endDate = $request->input('tanggal_selesai');

        // --- Cek Role Login ---
        $roleMapping = \DB::table('roles_mapping')
            ->where('jabatan_id', $userLogin->jabatan_id)
            ->where('level_id', $userLogin->level_id)
            ->first();

        $isManagerOrKepala = $roleMapping && str_contains($roleMapping->route_name, 'manager');

        // --- Proteksi Overlap & Double Input ---
        if (PengajuanCuti::isOverlapDivisi($idDivisi, $startDate, $endDate)) {
            return back()->with('error', 'Jadwal cuti tumpang tindih dengan rekan sedivisi.')->withInput();
        }

        $existingRequest = PengajuanCuti::where('nomor_urut_pegawai', $nomorPegawai)
            ->whereHas('logs', function ($query) {
                $query->where('status_pengajuan', 'diproses');
            })->first();

        if ($existingRequest) {
            return back()->with('error', 'Anda masih memiliki pengajuan yang sedang diproses.')->withInput();
        }

        // --- 3. Eksekusi Database Transaction ---
        try {
            DB::beginTransaction(); // Menggunakan beginTransaction agar return bisa di luar

            $dataToSave = $validatedData;
            if ($request->jenis_cuti === 'Cuti Alasan Penting dan Mendesak' && $request->sub_jenis_cuti) {
                $subCuti = SubJenisCutiPenting::find($request->sub_jenis_cuti);
                if ($subCuti) { $dataToSave['jenis_cuti'] = $subCuti->nama_sub_jenis; }
            }
            unset($dataToSave['sub_jenis_cuti']);

            $submission = PengajuanCuti::create($dataToSave);
            $submission->logs()->create([
                'nomor_urut_pegawai' => $submission->nomor_urut_pegawai,
                'tahap_persetujuan' => 'Pengajuan Awal',
                'status_pengajuan' => 'diproses',
                'komentar' => 'Menunggu verifikasi.',
                // 'updated_at' => \Carbon\Carbon::now(),
            ]);

            DB::commit();

            // --- 4. REDIRECT DINAMIS (DI LUAR TRANSAKSI) ---
            $parentRouteName = $isManagerOrKepala ? 'manager.pilihpengajuan' : 'datapengajuan.formDataPengajuan';
            $targetRoute = $isManagerOrKepala ? 'manager.pilihpengajuan' : 'pegawai.dashboard';
            
            return redirect()->route($targetRoute)->with([
                'success' => 'Permintaan Cuti/Izin Anda telah tercatat.',
                'modal_title' => 'Pengajuan Cuti/Izin berhasil dibuat!!!',
                'modal_link' => route($parentRouteName) // Sekarang error merahnya akan hilang
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Gagal simpan cuti: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem.')->withInput();
        }
    }


    public function statuscuti($nip)
    {
        $user = auth()->user();

        // 1. Logika Role Dinamis (Menghapus hardcode level_id == 2)
        $roleMapping = \DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        // Cek apakah atasan (Manager) berdasarkan route_name di database
        $isAtasan = $roleMapping && str_contains($roleMapping->route_name, 'manager');

        // 2. Ambil Data Pengajuan (Existing)
        $pekerjaanData = Pekerjaan::where('nomor_urut_pegawai', $nip)->first();

        $pengajuancuti = PengajuanCuti::with(['jenisCuti', 'logs' => function ($query) {
            $query->orderByDesc('updated_at')->orderByDesc('id');
        }])
        ->where('nomor_urut_pegawai', $nip)
        ->orderBy('created_at', 'desc')
        ->firstOrFail();

        // 3. Siapkan data untuk Processor (Existing)
        $submissionRaw = [
            'type' => 'Cuti',
            'logs' => $pengajuancuti->logs ? $pengajuancuti->logs->toArray() : [],
            'pengajuancuti' => array_merge($pengajuancuti->toArray(), [
                'jenisCuti' => $pengajuancuti->jenisCuti ? $pengajuancuti->jenisCuti->toArray() : [
                    'nama_cuti' => $pengajuancuti->jenis_cuti,
                    'id' => null
                ]
            ]),
            'tanggal_mulai' => $pengajuancuti->tanggal_mulai,
            'tanggal_selesai' => $pengajuancuti->tanggal_selesai,
            'sisa_cuti' => $pengajuancuti->sisa_cuti,
            'jumlah_cuti' => $pengajuancuti->jumlah_cuti,
            'keterangan' => $pengajuancuti->keterangan,
        ];

        $submission = $this->submissionProcessor->processSubmissions(collect([$submissionRaw]))->first();

        // 4. Logika Tampilan & Variabel Penting
        $latestLog = $pengajuancuti->logs->first();
        $komentarStatus = $latestLog ? $latestLog->komentar : 'Menunggu keputusan';
        $submissionType = 'Cuti'; // Variabel tetap dipertahankan
        $pageTitle = 'Lacak Pengajuan ' . $submissionRaw['type'];

        // 5. BREADCRUMBS & ROUTE OTOMATIS
        $roleName = $roleMapping->role_name ?? 'Pegawai';

        // Jika Atasan, label akan menjadi "Approval Pengajuan [Nama Jabatan]"
        $parentLabel = $isAtasan
            ? "Manajemen Pengajuan ↦ Approval Pengajuan $roleName"
            : 'Data Pengajuan';

        $parentRouteName = $isAtasan ? 'manager.pilihpengajuan' : 'datapengajuan.formDataPengajuan';

        $breadcrumbs = [
            'Beranda' => $user->dashboard_link,
            $parentLabel => route($parentRouteName),
            $pageTitle => null
        ];

        // 6. LAYOUT OTOMATIS
        $layout = $user->layout_file; // Menggunakan Accessor otomatis

        return view('datapengajuan.lacakpengajuan', compact(
            'pengajuancuti',
            'pageTitle',
            'komentarStatus',
            'pekerjaanData',
            'submissionRaw',
            'submissionType',
            'submission',
            'breadcrumbs',
            'layout'
        ));
    }


}
