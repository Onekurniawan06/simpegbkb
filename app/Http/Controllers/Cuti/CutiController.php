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
        $sisaCutiTahunIni = PengajuanCuti::hitungSaldoAwal($user->nomor_urut_pegawai, 'Cuti Tahunan');
        $idDivisiUser = $pekerjaanData->id_divisi ?? null;
        $managerDivisi = null;

        if ($idDivisiUser) {
            $managerDivisi = Pekerjaan::where('id_divisi', $idDivisiUser)
                ->where(function($q) {
                    $q->where('jabatan', 'LIKE', '%Manager%')
                    ->orWhere('jabatan', 'LIKE', '%Manajer%');
                })
                ->with('pegawai')
                ->first();
        }

        $namaAtasan = $managerDivisi->pegawai->nama ?? '........................';
        $jabatanAtasan = $managerDivisi->jabatan ?? 'Atasan Langsung';
        $dirOps = Pekerjaan::where('jabatan', 'Direktur Operasional')->with('pegawai')->first();
        $dirKep = Pekerjaan::where('jabatan', 'Direktur Kepatuhan')->with('pegawai')->first();
        $namaDireksi = strtoupper($dirOps->pegawai->nama ?? '........................') .
                    ' atau ' .
                    strtoupper($dirKep->pegawai->nama ?? '........................');

        $jabatanDireksi = 'Direktur Operasional atau Direktur Kepatuhan';

        // Cari Kepala SKK & SKKMR
        $v1 = Pekerjaan::where('jabatan', 'LIKE', '%Kepala Satker Kepatuhan%')->with('pegawai')->first();
        $namaVerif1 = strtoupper($v1->pegawai->nama ?? '........................');
        $jabatanVerif1 = "Kepala Satker Kepatuhan & M.R.";

        // Cari Human Resources Officer
        $v2 = Pekerjaan::where('jabatan', 'LIKE', '%Human Resources Officer%')->with('pegawai')->first();
        $namaVerif2 = strtoupper($v2->pegawai->nama ?? '........................');
        $jabatanVerif2 = "Human Resources Officer";

        $jenisPengajuan = 'cuti';
        $submissionsType = 'Cuti';

        // 3. Logika Navigasi Dinamis
        $dashboardUrl = $user->dashboard_link;
        $roleLabel = $roleMapping->role_name ?? 'Pegawai';
        $namaJabatanLower = strtolower($roleLabel);

        $parentRouteName = 'datapengajuan.formDataPengajuan';
        $parentLabel = 'Data Pengajuan';

        if (str_contains($namaJabatanLower, 'manager') || str_contains($namaJabatanLower, 'manajer')) {
            $parentRouteName = 'manager.pilihpengajuan';
            $parentLabel = 'Manajemen Pengajuan ↦ Approval Pengajuan Manager';
        } elseif (str_contains($namaJabatanLower, 'skk') || str_contains($namaJabatanLower, 'kepatuhan')) {
            $parentRouteName = 'skkmr.dashboardskkmr';
            $parentLabel = 'Dashboard Kepala SKK & SKKMR';
        } elseif (str_contains($namaJabatanLower, 'hro') || str_contains($namaJabatanLower, 'human resource')) {
            $parentRouteName = 'hro.dashboardhro';
            $parentLabel = 'Dashboard Human Resources';
        }

        $pageTitle = 'Pengajuan Cuti dan Izin ' . $roleLabel;

        $breadcrumbs = [
            'Beranda' => $dashboardUrl,
            $parentLabel => route($parentRouteName),
            $pageTitle => null
        ];

        // Tambahkan 'sisaCutiTahunIni' ke dalam compact
        return view('cuti.cutiizin', compact(
            'jenisCuti', 'durationsMapping', 'subJenisCuti', 'jatahCutiTahunanMaksimal', 'pekerjaanData', 'pageTitle', 'breadcrumbs', 'parentRouteName', 'jenisPengajuan',
            'submissionsType', 'namaAtasan', 'jabatanAtasan', 'namaDireksi', 'jabatanDireksi', 'namaVerif1', 'jabatanVerif1', 'namaVerif2', 'jabatanVerif2',
            'sisaCutiTahunIni'
        ));
    }


    public function updateCutiizin(Request $request)
    {
        // 1. Validasi Data (Data yang berasal dari Form)
        $validatedData = $request->validate([
            'nomor_urut_pegawai' => 'required',
            'jenis_cuti'         => 'required',
            'tanggal_mulai'      => 'required|date',
            'tanggal_selesai'    => 'required|date',
            'jumlah_cuti'        => 'required',
            'saldo_awal'         => 'required',
            'sisa_cuti'          => 'required',
            'jatah_periode_hari' => 'nullable',
            'keterangan'         => 'nullable',
        ]);

        $nomorPegawai = $request->input('nomor_urut_pegawai');

        // 2. Ambil Data User & Divisi
        $userLogin = auth()->user();
        $userOwner = User::with('pegawai.pekerjaan')->where('nomor_urut_pegawai', $nomorPegawai)->first();

        if (!$userOwner || !$userOwner->pegawai || !$userOwner->pegawai->pekerjaan) {
            return back()->with('error', 'Data divisi pegawai tidak ditemukan.')->withInput();
        }

        $idDivisi = $userOwner->pegawai->pekerjaan->id_divisi ?? null;
        $startDate = $request->input('tanggal_mulai');
        $endDate = $request->input('tanggal_selesai');

        // --- Cek Role Login untuk Redirect ---
        $roleMapping = \DB::table('roles_mapping')
            ->where('jabatan_id', $userLogin->jabatan_id)
            ->where('level_id', $userLogin->level_id)
            ->first();

        $isManagerOrKepala = $roleMapping && str_contains($roleMapping->route_name, 'manager');

        // 3. Eksekusi Simpan Data
        try {
        \DB::beginTransaction();

        // 1. Ambil data dari validasi
        $dataToSave = $validatedData;

        // 2. Bersihkan angka dari teks (Menghilangkan kata "Hari")
        if (isset($dataToSave['jatah_periode_hari'])) {
            $dataToSave['jatah_periode_hari'] = (int) filter_var($dataToSave['jatah_periode_hari'], FILTER_SANITIZE_NUMBER_INT);
        }

        // 3. Mapping data sesuai nama kolom di Database
        $dataToSave['saldo_akhir'] = $validatedData['sisa_cuti'];
        $dataToSave['status_pengajuan'] = 'diproses';
        $dataToSave['updated_at'] = now();
        $dataToSave['created_at'] = now();

        // 4. BUANG kolom yang tidak ada di tabel 'pengajuan_cuti' agar tidak Error
        unset($dataToSave['sisa_cuti']);
        unset($dataToSave['sub_jenis_cuti']);
        unset($dataToSave['nama_pegawai']);

        // 5. SIMPAN KE TABEL UTAMA
        $idCutiBaru = \DB::table('pengajuan_cuti')->insertGetId($dataToSave);

        // 6. SIMPAN KE TABEL LOG
        \DB::table('log_persetujuan_cuti')->insert([
            'id_cuti'            => $idCutiBaru,
            'nomor_urut_pegawai' => $nomorPegawai,
            'tahap_persetujuan'  => 'Pengajuan Awal',
            'status_pengajuan'   => 'diproses',
            'komentar'           => 'Menunggu verifikasi.',
            'updated_at'         => now()
        ]);

        \DB::commit();

        // 7. REDIRECT
        $parentRouteName = $isManagerOrKepala ? 'manager.pilihpengajuan' : 'datapengajuan.formDataPengajuan';
        $targetRoute = $isManagerOrKepala ? 'manager.pilihpengajuan' : 'pegawai.dashboard';

        return redirect()->route($targetRoute)->with([
            'success' => 'Permintaan Cuti/Izin Anda telah tercatat.',
            'modal_title' => 'Pengajuan Cuti/Izin berhasil dibuat!!!',
            'modal_link' => route($parentRouteName)
        ]);

        } catch (\Throwable $e) {
            \DB::rollBack();
            // Simpan pesan error asli ke log agar bisa kita baca jika masih gagal
            \Log::error('Gagal simpan cuti: ' . $e->getMessage());
            return back()->with('error', 'DATABASE ERROR: ' . $e->getMessage())->withInput();
        }

    }

    public function statuscuti($id_cuti)
    {
        $user = auth()->user();

        // 1. Logika Role Dinamis - TETAP UTUH
        $roleMapping = \DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        $isAtasan = $roleMapping && str_contains($roleMapping->route_name, 'manager');

        // 2. Ambil Data Pengajuan - SEKARANG DI ATAS agar NIP bisa diambil
        $pengajuancuti = PengajuanCuti::with(['jenisCuti', 'logs' => function ($query) {
            $query->orderByDesc('updated_at')->orderByDesc('id_cuti');
        }])
        ->where('id_cuti', $id_cuti)
        ->firstOrFail();

        $pekerjaanData = Pekerjaan::where('nomor_urut_pegawai', $pengajuancuti->nomor_urut_pegawai)->first();

        // 3. Siapkan data untuk Processor - TETAP UTUH
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

            'saldo_awal' => $pengajuancuti->saldo_awal,
            'sisa_cuti' => $pengajuancuti->sisa_cuti,

            'jumlah_cuti' => $pengajuancuti->jumlah_cuti,
            'keterangan' => $pengajuancuti->keterangan,
            'status_pengajuan' => $pengajuancuti->status_pengajuan,
        ];

        $submission = $this->submissionProcessor->processSubmissions(collect([$submissionRaw]))->first();

        // 4. Logika Tampilan & Variabel Penting - TETAP UTUH
        $latestLog = $pengajuancuti->logs->first();
        $komentarStatus = $latestLog ? $latestLog->komentar : 'Menunggu keputusan';
        $submissionType = 'Cuti';
        $pageTitle = 'Lacak Pengajuan ' . $submissionRaw['type'];

        // 5. BREADCRUMBS & ROUTE OTOMATIS - TETAP UTUH
        $roleName = $roleMapping->role_name ?? 'Pegawai';
        $parentLabel = $isAtasan ? "Manajemen Pengajuan ↦ Approval Pengajuan $roleName" : 'Data Pengajuan';
        $parentRouteName = $isAtasan ? 'manager.pilihpengajuan' : 'datapengajuan.formDataPengajuan';

        $breadcrumbs = [
            'Beranda' => $user->dashboard_link,
            $parentLabel => route($parentRouteName),
            $pageTitle => null
        ];

        // 6. LAYOUT OTOMATIS - TETAP UTUH
        $layout = $user->layout_file;

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
