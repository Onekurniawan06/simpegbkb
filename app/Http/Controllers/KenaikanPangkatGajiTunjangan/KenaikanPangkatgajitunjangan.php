<?php

namespace App\Http\Controllers\KenaikanPangkatgajitunjangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SubmissionProcessorService;
use App\Models\Pekerjaan;
use App\Models\PengajuanPangkatgajitunjangan;
use App\Models\FilePersyaratanpangkatgajitunjangan;
use App\Models\LogPersetujuanPangkatgajitunjangan;
use App\Enums\StatusPersetujuan;


class KenaikanPangkatgajitunjangan extends Controller // Nama class sesuai permintaan
{
    protected $submissionProcessor;

    public function __construct(SubmissionProcessorService $submissionProcessor)
    {
        $this->submissionProcessor = $submissionProcessor;
    }

    public function formPangkatGajiTunjangan()
    {
        $user = Auth::user();

        $roleMapping = \DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        // 1. PISAHKAN DETEKSI ROLE
        $isManager = $roleMapping && str_contains($roleMapping->route_name, 'manager');
        $isSKKMR   = $roleMapping && str_contains($roleMapping->route_name, 'skkmr');

        $pekerjaanData = Pekerjaan::with('divisi')
            ->where('nomor_urut_pegawai', $user->nomor_urut_pegawai)
            ->first();

        $tmt_pegawai_formatted = null;
        if ($pekerjaanData && $pekerjaanData->tmt_pegawai) {
            $cleanDateString = str_replace('/', '-', $pekerjaanData->tmt_pegawai);
            $tmt_pegawai_formatted = Carbon::parse($cleanDateString)->format('d-m-Y');
        }

        $submissionsType = 'Pangkat/Gaji/Tunjangan';
        $roleLabel = $roleMapping->role_name ?? 'Pegawai';

        // 2. LOGIKA KONDISI UNTUK TAMPILAN DAN RUTE
        if ($isManager) {
            $pageTitle = 'Kenaikan Pangkat, Gaji & Tunjangan Manager';
            $parentRouteName = 'manager.pilihpengajuan';
            $parentLabel = "Manajemen Pengajuan ↦ Approval Pengajuan $roleLabel";
        } elseif ($isSKKMR) {
            $pageTitle = 'Kenaikan Pangkat, Gaji & Tunjangan Kepala Satker';
            $parentRouteName = 'skkmr.pilihpengajuan'; // Sesuaikan dengan route name SKKMR Anda di web.php
            $parentLabel = "Manajemen Pengajuan ↦ Approval Pengajuan $roleLabel";
        } else {
            $pageTitle = 'Kenaikan Pangkat, Gaji & Tunjangan Pegawai';
            $parentRouteName = 'datapengajuan.formDataPengajuan';
            $parentLabel = 'Data Pengajuan';
        }

        $breadcrumbs = [
            'Beranda' => $user->dashboard_link,
            $parentLabel => route($parentRouteName),
            $pageTitle => null
        ];

        $layout = $user->layout_file;

        return view('kenaikanpangkatgajitunjangan.pangkatgajitunjangan', compact(
            'user',
            'pekerjaanData',
            'pageTitle',
            'breadcrumbs',
            'tmt_pegawai_formatted',
            'parentRouteName',
            'submissionsType',
            'layout',
            'isManager', // Variabel yang diparsing ke blade dipisah
            'isSKKMR'
        ));
    }


    public function updatePangkatGajiTunjangan(Request $request)
{
    // 1. Validasi Data
    $validatedData = $request->validate([
        'nomor_urut_pegawai' => 'required|string|max:15',
        'pangkat'            => 'nullable|string|max:50',
        'grade'              => 'nullable|string|max:10',
        'jabatan'            => 'nullable|string|max:100',
        'unit_kerja'         => 'nullable|string|max:100',
        'status_pegawai'     => 'nullable|string|max:50',
        'tmt_pegawai'        => 'nullable', // Biarkan mutator model yang bekerja
        'jenis_pengajuan'    => 'required|string|max:100',
        'masa_kerja'         => 'nullable|string|max:50',
        'documents'          => 'required|array',
        'documents.*'        => 'required|file|mimes:pdf|max:5120',
    ]);

    $nomor_urut_pegawai = $validatedData['nomor_urut_pegawai'];
    $jenisPengajuan = $validatedData['jenis_pengajuan'];

    // 2. Cek Pengajuan Pending (Gunakan kolom status_kenaikan agar lebih cepat)
    $hasPendingRequest = PengajuanPangkatgajitunjangan::where('nomor_urut_pegawai', $nomor_urut_pegawai)
        ->where('status_kenaikan', 'diproses')
        ->exists();

    if ($hasPendingRequest) {
        return back()->with('error', 'Anda masih memiliki pengajuan kenaikan yang sedang diproses.')->withInput();
    }

    $user = auth()->user();
    $roleMapping = \DB::table('roles_mapping')
        ->where('jabatan_id', $user->jabatan_id)
        ->where('level_id', $user->level_id)
        ->first();

    DB::beginTransaction();
    try {
        // A. Simpan data pengajuan utama
        $dataMain = $validatedData;
        unset($dataMain['documents']);
        $dataMain['status_kenaikan'] = 'diproses'; // ➕ TAMBAHKAN status awal di tabel utama

        $pengajuan = PengajuanPangkatgajitunjangan::create($dataMain);
        $idKenaikan = $pengajuan->id_kenaikan;

        // B. Simpan Log (Gunakan string 'diproses' agar konsisten dengan yang lain)
        LogPersetujuanPangkatgajitunjangan::create([
            'id_kenaikan'        => $idKenaikan,
            'nomor_urut_pegawai' => $nomor_urut_pegawai,
            'tahap_persetujuan'  => 'Pengajuan Awal',
            'status_persetujuan' => 'diproses',
            'komentar'           => 'Menunggu verifikasi berkas.',
            // updated_at diisi otomatis oleh Laravel karena $timestamps = true di model
        ]);

        // C. Simpan File Dokumen (Gunakan folder private agar aman)
        if ($request->hasFile('documents')) {
            $safeFolderName = \Str::slug($jenisPengajuan);
            // Simpan ke private/ agar tidak bisa diakses publik via URL
            $baseUploadPath = 'private/dokumen_pangkat_gaji/' . $safeFolderName . '/' . $nomor_urut_pegawai;

            foreach ($request->file('documents') as $kodeDokumen => $file) {
                if ($file->isValid()) {
                    $extension = $file->getClientOriginalExtension();
                    $originalName = $file->getClientOriginalName();

                    $fileNameFormatted = sprintf(
                        "%s_%s_%s.%s",
                        $kodeDokumen,
                        $nomor_urut_pegawai,
                        now()->timestamp,
                        $extension
                    );

                    $path = $file->storeAs($baseUploadPath, $fileNameFormatted, 'local');

                    FilePersyaratanpangkatgajitunjangan::create([
                        'id_kenaikan'      => $idKenaikan,
                        'nomor_urut_pegawai' => $nomor_urut_pegawai,
                        'nama_file_asli'     => $originalName,
                        'path_file_server'   => $path,
                        'tipe_dokumen'       => $kodeDokumen,
                    ]);
                }
            }
        }

        DB::commit();

        // 3. TENTUKAN REDIRECT BERDASARKAN ROLE
        $isManager = $roleMapping && str_contains($roleMapping->route_name, 'manager');
        $isSKKMR   = $roleMapping && str_contains($roleMapping->route_name, 'skkmr');

        if ($isManager) {
            $targetRoute = 'manager.pilihpengajuan';
            $parentRouteName = 'manager.pilihpengajuan';
        } elseif ($isSKKMR) {
            $targetRoute = 'skkmr.dashboardskkmr';
            $parentRouteName = 'skkmr.pengajuanskkmr';
        } else {
            $targetRoute = 'pegawai.dashboard';
            $parentRouteName = 'datapengajuan.formDataPengajuan';
        }

        return redirect()->route($targetRoute)->with([
            'success' => 'Permintaan Kenaikan Pangkat/Gaji Anda telah tercatat.',
            'modal_title' => 'Berhasil!',
            'modal_link' => route($parentRouteName)
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Gagal simpan Pangkat/Gaji: ' . $e->getMessage());
        return back()->withInput()->with('error', 'Terjadi kesalahan sistem.');
    }
}


    public function statuspangkatgajitunjangan($nip)
{
    $user = auth()->user();

    // 1. Logika Role Dinamis
    $roleMapping = \DB::table('roles_mapping')
        ->where('jabatan_id', $user->jabatan_id)
        ->where('level_id', $user->level_id)
        ->first();

    $isManagerOrKepala = $roleMapping && str_contains($roleMapping->route_name, 'manager');

    // 2. Ambil Data Pengajuan
    $pekerjaanData = Pekerjaan::where('nomor_urut_pegawai', $nip)->first();

    $pengajuankenaikan = PengajuanPangkatgajitunjangan::with(['files', 'logPersetujuanPangkatgajitunjangan' => function ($query) {
        $query->orderByDesc('updated_at');
    }])
    ->where('nomor_urut_pegawai', $nip)
    ->orderBy('created_at', 'desc')
    ->firstOrFail();

    // 3. Siapkan data untuk Processor (Normalisasi agar seragam)
    $submissionRaw = [
        'id' => $pengajuankenaikan->id_kenaikan, // ➕ Tambahkan ID untuk link preview
        'type' => 'PangkatGajiTunjangan',
        'status_pengajuan' => $pengajuankenaikan->status_kenaikan, // ➕ Gunakan status dari tabel utama
        'logs' => $pengajuankenaikan->logPersetujuanPangkatgajitunjangan ? $pengajuankenaikan->logPersetujuanPangkatgajitunjangan->toArray() : [],
        'tmt_pegawai' => $pengajuankenaikan->tmt_pegawai,
        'masa_kerja' => $pengajuankenaikan->masa_kerja,
        'jenis_pengajuan' => $pengajuankenaikan->jenis_pengajuan,
        'created_at' => $pengajuankenaikan->created_at,
    ];

    // Proses data menggunakan Service Class
    $submission = $this->submissionProcessor->processSubmissions(collect([$submissionRaw]))->first();

    // 4. Logika Tampilan
    $latestLog = $pengajuankenaikan->logPersetujuanPangkatgajitunjangan->first();
    $komentarStatus = $latestLog ? $latestLog->komentar : 'Menunggu keputusan';

    $submissionType = 'PangkatGajiTunjangan';
    $pageTitle = 'Lacak Pengajuan Kenaikan Pangkat/Gaji/Tunjangan';

    // 5. BREADCRUMBS & ROUTE
    $roleName = $roleMapping->role_name ?? 'Pegawai';
    $parentLabel = $isManagerOrKepala ? "Manajemen Pengajuan ↦ Approval Pengajuan $roleName" : 'Data Pengajuan';
    $parentRouteName = $isManagerOrKepala ? 'manager.pilihpengajuan' : 'datapengajuan.formDataPengajuan';

    $breadcrumbs = [
        'Beranda' => $user->dashboard_link,
        $parentLabel => route($parentRouteName),
        $pageTitle => null
    ];

    $layout = $user->layout_file;

    return view('datapengajuan.lacakpengajuan', compact(
        'pengajuankenaikan',
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


    public function lihatDokumen($id)
    {
        // 1. Ambil data (Pastikan nama Model sesuai dengan file di app/Models)
        $fileDokumen = FilePersyaratanpangkatgajitunjangan::findOrFail($id);

        $path = $fileDokumen->path_file_server;

        // 2. Jalur absolut (Sesuaikan dengan tempat penyimpanan di fungsi Update)
        // Jika di fungsi Update Anda menggunakan Storage::disk('local'), jalurnya adalah:
        $filePath = storage_path('app/' . $path);

        // 3. Cek fisik file di server
        if (!file_exists($filePath)) {
            \Log::error("File tidak ditemukan di path: " . $filePath);
            abort(404, 'Dokumen tidak ditemukan di server.');
        }

        // 4. Return file untuk ditampilkan di browser (Inline)
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileDokumen->nama_file_asli . '"'
        ]);
    }


    // public function lihatDokumen($id)
    // {
    //     $fileDokumen = FilePersyaratanPangkatGajiTunjangan::findOrFail($id);
    //     $path = $fileDokumen->path_file_server;

    //     // Jalur absolut ke file Anda
    //     $filePath = storage_path(path: 'app/private/' . $path);

    //     // Pastikan file tersebut ada di sistem file
    //     if (!file_exists($filePath)) {
    //         // Jika tidak ada, hentikan eksekusi dan kirim error 404
    //         abort(404, 'Dokumen tidak ditemukan di server.');
    //     }

    //     // --- PASTIKAN ANDA MENGGUNAKAN INI ---
    //     // Laravel akan otomatis menentukan Content-Type dan mengirim status 200 OK
    //     return response()->file($filePath, [
    //         'Content-Disposition' => 'inline; filename="' . $fileDokumen->nama_file_asli . '"'
    //     ]);
    //     // ------------------------------------

    //     // Pastikan tidak ada return null, return "", atau die() setelah kode di atas.
    // }
}
