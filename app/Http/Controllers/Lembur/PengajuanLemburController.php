<?php

namespace App\Http\Controllers\Lembur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\SubmissionProcessorService;
use App\Models\PengajuanLembur;
use App\Models\LogPersetujuanLembur;
use App\Enums\StatusPersetujuan; // Jika Anda menggunakan Enum
use App\Models\Pekerjaan;
use App\Models\User;
use Carbon\Carbon;

class PengajuanLemburController extends Controller
{
    protected $submissionProcessor;

    public function __construct(SubmissionProcessorService $submissionProcessor)
    {
        $this->submissionProcessor = $submissionProcessor;
    }

    public function formLembur()
    {
        $user = Auth::user();

        // 1. Logika Role Dinamis (Tanpa Hardcode level_id)
        $roleMapping = \DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        // Cek manager berdasarkan route_name di DB
        $isManagerOrKepala = $roleMapping && str_contains($roleMapping->route_name, 'manager');

        // 2. Data Master
        $pekerjaanData = Pekerjaan::where('nomor_urut_pegawai', $user->nomor_urut_pegawai)->first();

        // --- VARIABEL PENTING YANG DIKEMBALIKAN ---
        $submissionsType = 'Lembur';
        // ------------------------------------------

        // 3. Logika Navigasi & Label Dinamis
        $dashboardUrl = $user->dashboard_link;
        $roleLabel = $roleMapping->role_name ?? ' Pegawai';

        $parentRouteName = $isManagerOrKepala ? 'manager.pilihpengajuan' : 'datapengajuan.formDataPengajuan';
        $parentLabel = $isManagerOrKepala ? 'Manajemen Pengajuan ↦ Approval Pengajuan' : 'Data Pengajuan';
        $pageTitle = 'Pengajuan Lembur' . $roleLabel;

        // 4. Breadcrumbs
        $breadcrumbs = [
            'Beranda' => $dashboardUrl,
            $parentLabel => route($parentRouteName),
            $pageTitle => null
        ];

        return view('lembur.lembur', compact(
            'user',
            'pekerjaanData',
            'pageTitle',
            'breadcrumbs',
            'parentRouteName',
            'submissionsType' // Sudah dikembalikan ke dalam compact
        ));
    }

    public function updateLembur(Request $request)
{
    $validatedData = $request->validate([
        'tanggal_lembur' => 'required|date',
        'jam_mulai' => 'required|date_format:H:i',
        'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        'total_jam_lembur' => 'required|string|max:100',
        'uraian_tugas' => 'required|string|max:1000',
        'nomor_urut_pegawai' => 'required'
    ]);

    $nomor_urut_pegawai = $request->input('nomor_urut_pegawai');

    // --- 1. Logika Role Dinamis ---
    $userLogin = auth()->user();
    $roleMapping = \DB::table('roles_mapping')
        ->where('jabatan_id', $userLogin->jabatan_id)
        ->where('level_id', $userLogin->level_id)
        ->first();

    $isManagerOrKepala = $roleMapping && str_contains($roleMapping->route_name, 'manager');

    // --- 2. Logging & Cek Data Pekerjaan ---
    $userOwner = User::with('pegawai.pekerjaan')->where('nomor_urut_pegawai', $nomor_urut_pegawai)->first();

    if (!$userOwner || !$userOwner->pegawai || !$userOwner->pegawai->pekerjaan) {
        return back()->with('error', 'Data divisi pegawai tidak ditemukan.')->withInput();
    }

    $idDivisi = $userOwner->pegawai->pekerjaan->id_divisi ?? null;

    // --- 3. Cek Existing Request ---
    // Di sini kita bisa langsung cek ke kolom status_lembur di tabel utama agar lebih cepat
    $existingRequest = PengajuanLembur::where('nomor_urut_pegawai', $nomor_urut_pegawai)
        ->where('status_lembur', 'diproses')
        ->exists();

    if ($existingRequest) {
        return back()->with('error', 'Anda masih memiliki pengajuan lembur yang sedang diproses.')->withInput();
    }

    // --- 4. Eksekusi Transaction ---
    DB::beginTransaction();
    try {
        // SIMPAN KE TABEL UTAMA
        $pengajuan = PengajuanLembur::create([
            'nomor_urut_pegawai' => $nomor_urut_pegawai,
            'tanggal_lembur'     => $validatedData['tanggal_lembur'],
            'jam_mulai'          => $validatedData['jam_mulai'],
            'jam_selesai'        => $validatedData['jam_selesai'],
            'total_jam_lembur'   => $validatedData['total_jam_lembur'],
            'uraian_tugas'       => $validatedData['uraian_tugas'],
            'status_lembur'      => 'diproses', // ➕ TAMBAHKAN INI agar tabel utama punya status
        ]);

        // SIMPAN KE TABEL LOG
        LogPersetujuanLembur::create([
            'id_lembur'          => $pengajuan->id_lembur,
            'nomor_urut_pegawai' => $nomor_urut_pegawai,
            'tahap_persetujuan'  => 'Pengajuan Awal',
            'status_persetujuan' => 'diproses', // Sesuaikan jika pakai Enum (StatusPersetujuan::DIPROSES)
            'komentar'           => 'Menunggu Verifikasi.',
        ]);

        DB::commit();

        // --- 5. REDIRECT DINAMIS ---
        $parentRouteName = $isManagerOrKepala ? 'manager.pilihpengajuan' : 'datapengajuan.formDataPengajuan';
        $targetRoute = $isManagerOrKepala ? 'manager.pilihpengajuan' : 'pegawai.dashboard';

        return redirect()->route($targetRoute)->with([
            'success'     => 'Permintaan Lembur Anda telah tercatat.',
            'modal_title' => 'Pengajuan Lembur berhasil dibuat!!!',
            'modal_link'  => route($parentRouteName)
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Gagal simpan Pengajuan Lembur: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem.');
    }
}


    public function statuslembur($nip)
    {
        $user = auth()->user();

        // 1. Logika Role Dinamis (Menghapus level_akses)
        $roleMapping = \DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        // Cek apakah dia manager berdasarkan route_name di DB
        $isManager = $roleMapping && str_contains($roleMapping->route_name, 'manager');

        // 2. Ambil Data Pengajuan
        $pekerjaanData = Pekerjaan::where('nomor_urut_pegawai', $nip)->first();

        $pengajuanlembur = PengajuanLembur::with(['logPersetujuanLembur' => function ($query) {
            $query->orderByDesc('updated_at');
        }])
        ->where('nomor_urut_pegawai', $nip)
        ->orderBy('created_at', 'desc')
        ->firstOrFail();

        // 3. Siapkan data untuk Processor
        $submissionRaw = [
            'type' => 'Lembur',
            'logs' => $pengajuanlembur->logPersetujuanLembur ? $pengajuanlembur->logPersetujuanLembur->toArray() : [],
            'tanggal_lembur' => $pengajuanlembur->tanggal_lembur,
            'jam_mulai' => $pengajuanlembur->jam_mulai,
            'jam_selesai' => $pengajuanlembur->jam_selesai,
            'total_jam_lembur' => $pengajuanlembur->total_jam_lembur,
            'uraian_tugas' => $pengajuanlembur->uraian_tugas,
        ];

        $submission = $this->submissionProcessor->processSubmissions(collect([$submissionRaw]))->first();

        // 4. Logika Tampilan & Variabel Penting
        $latestLog = $pengajuanlembur->logPersetujuanLembur->first();
        $komentarStatus = $latestLog ? $latestLog->komentar : 'Menunggu keputusan';

        // --- JANGAN DIHAPUS ---
        $submissionType = 'Lembur';
        // ----------------------

        $pageTitle = 'Lacak Pengajuan ' . $submissionRaw['type'];

        // 5. BREADCRUMBS & ROUTE OTOMATIS
        $roleName = $roleMapping->role_name ?? 'Pegawai'; // Mengambil nama jabatan dari DB

        $parentLabel = $isManager
            ? "Manajemen Pengajuan ↦ Approval Pengajuan $roleName"
            : 'Data Pengajuan';

        $parentRouteName = $isManager ? 'manager.pilihpengajuan' : 'datapengajuan.formDataPengajuan';

        $breadcrumbs = [
            'Beranda' => $user->dashboard_link,
            $parentLabel => route($parentRouteName),
            $pageTitle => null
        ];

        // 6. LAYOUT OTOMATIS
        $layout = $user->layout_file; // Menggunakan Accessor otomatis agar sidebar konsisten

        return view('datapengajuan.lacakpengajuan', compact(
            'pengajuanlembur',
            'pageTitle',
            'komentarStatus',
            'pekerjaanData',
            'submissionRaw',
            'submissionType',
            'submission',
            'breadcrumbs',
            'layout'
        ))->with('pengajuankenaikan', null);
    }

}
