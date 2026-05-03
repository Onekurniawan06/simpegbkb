<?php

namespace App\Http\Controllers\Pensiun;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pekerjaan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use App\Services\SubmissionProcessorService; // PENTING: Import service class
use App\Models\PengajuanPensiun;
use App\Models\LogPersetujuanPensiun;
use App\Models\FilePersyaratanPensiun;
use App\Enums\StatusPersetujuan;
// use App\Models\Divisi;

class PengajuanPensiunController extends Controller
{
    protected $submissionProcessor;

    public function __construct(SubmissionProcessorService $submissionProcessor)
    {
        $this->submissionProcessor = $submissionProcessor;
    }

    public function formPensiun()
    {
        $user = Auth::user();

        // 1. Logika Role Dinamis
        $roleMapping = \DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        $isManagerOrKepala = $roleMapping && str_contains($roleMapping->route_name, 'manager');

        // 2. Data Master & Logika Tanggal Pensiun
        $pekerjaanData = Pekerjaan::with('divisi')
            ->where('nomor_urut_pegawai', $user->nomor_urut_pegawai)
            ->first();

        $tmt_pensiun_otomatis = null;
        $tmt_pegawai_formatted = $pekerjaanData->tmt_pegawai ?? null;

        if ($pekerjaanData && $pekerjaanData->tmt_pegawai) {
            $cleanDateString = str_replace('/', '-', $pekerjaanData->tmt_pegawai);
            $tmtPegawai = Carbon::parse($cleanDateString);
            $tmt_pegawai_formatted = $tmtPegawai->format('d-m-Y');

            $batasUsiaPensiun = 56;
            $tmt_pensiun_otomatis = $tmtPegawai->copy()->addYears($batasUsiaPensiun)->subDay()->format('d-m-Y');
        }

        // --- VARIABEL PENTING UNTUK MODAL & NAVIGASI ---
        $submissionsType = 'Pensiun';
        $roleLabel = $roleMapping->role_name ?? 'Pegawai';

        $parentRouteName = $isManagerOrKepala ? 'manager.pilihpengajuan' : 'datapengajuan.formDataPengajuan';
        $parentLabel = $isManagerOrKepala
            ? "Manajemen Pengajuan ↦ Approval Pengajuan $roleLabel"
            : 'Data Pengajuan';

        // 3. Logika Navigasi & Label
        $dashboardUrl = $user->dashboard_link;
        $pageTitle = 'Pengajuan Pensiun ' . $roleLabel;

        // 4. Susun Breadcrumbs
        $breadcrumbs = [
            'Beranda' => $dashboardUrl,
            $parentLabel => route($parentRouteName),
            $pageTitle => null
        ];

        return view('pensiun.pensiun', compact(
            'user',
            'pekerjaanData',
            'pageTitle',
            'breadcrumbs',
            'parentRouteName',
            'tmt_pegawai_formatted',
            'tmt_pensiun_otomatis',
            'submissionsType'
        ));
    }

    public function updatePensiun(Request $request)
    {
        // 1. Validasi Data
        $validatedData = $request->validate([
            'nomor_urut_pegawai' => 'required|string|max:15',
            'nama_pegawai'       => 'required|string|max:100',
            'pangkat'            => 'nullable|string|max:50',
            'grade'              => 'nullable|string|max:10',
            'jabatan'            => 'nullable|string|max:100',
            'unit_kerja'         => 'nullable|string|max:100',
            'status_pegawai'     => 'nullable|string|max:50',
            'tmt_pegawai'        => 'nullable',
            'jenis_pengajuan'    => 'required|string|max:100',
            'masa_kerja'         => 'nullable|string|max:50',
            'tmt_pensiun'        => 'required',
        ]);

        $nomor_urut_pegawai = $validatedData['nomor_urut_pegawai'];

        // 2. Cek Pengajuan Pending (Gunakan kolom status_pensiun agar lebih cepat)
        $hasPendingRequest = PengajuanPensiun::where('nomor_urut_pegawai', $nomor_urut_pegawai)
            ->where('status_pensiun', 'diproses')
            ->exists();

        if ($hasPendingRequest) {
            return back()->with('error', 'Anda masih memiliki pengajuan pensiun yang sedang diproses.')->withInput();
        }

        $user = auth()->user();
        $roleMapping = \DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        $isManagerOrKepala = $roleMapping && str_contains($roleMapping->route_name, 'manager');

        \DB::beginTransaction();
        try {
            // 3. Simpan Tabel Utama dengan status_pensiun
            $dataToSave = $validatedData;
            $dataToSave['status_pensiun'] = 'diproses'; // ➕ Tambahkan status awal

            $pensiunBaru = PengajuanPensiun::create($dataToSave);
            $idPensiunBaru = $pensiunBaru->id_pensiun;

            // 4. Simpan Log (Gunakan status string langsung jika Casts dihapus)
            LogPersetujuanPensiun::create([
                'id_pensiun'         => $idPensiunBaru,
                'nomor_urut_pegawai' => $nomor_urut_pegawai,
                'tahap_persetujuan'  => 'Pengajuan Awal',
                'status_persetujuan' => 'diproses', // String langsung
                'komentar'           => 'Menunggu persetujuan.',
            ]);

            // 5. Simpan File Dokumen (Multiple Files)
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $kodeDokumen => $file) {
                    if ($file->isValid()) {
                        $extension = $file->getClientOriginalExtension();
                        $originalName = $file->getClientOriginalName();

                        // Folder: storage/app/dokumen_pensiun/202690055/
                        $uploadPath = 'dokumen_pensiun/' . $nomor_urut_pegawai;

                        $fileNameFormatted = sprintf(
                            "%s_%s_%s.%s",
                            $kodeDokumen,
                            $nomor_urut_pegawai,
                            now()->timestamp,
                            $extension
                        );

                        $path = $file->storeAs($uploadPath, $fileNameFormatted, 'local');

                        FilePersyaratanPensiun::create([
                            'id_pensiun'           => $idPensiunBaru,
                            'nomor_urut_pegawai'   => $nomor_urut_pegawai,
                            'nama_file_asli'       => $originalName,
                            'path_file_server'     => $path,
                            'tipe_dokumen'         => $kodeDokumen,
                        ]);
                    }
                }
            }

            \DB::commit();

            $targetRoute = $isManagerOrKepala ? 'manager.pilihpengajuan' : 'pegawai.dashboard';
            $parentRouteName = $isManagerOrKepala ? 'manager.pilihpengajuan' : 'datapengajuan.formDataPengajuan';

            return redirect()->route($targetRoute)->with([
                'success' => 'Permintaan Pensiun Anda telah tercatat.',
                'modal_title' => 'Pengajuan Pensiun berhasil dibuat!!!',
                'modal_link' => route($parentRouteName)
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Gagal Simpan Pensiun: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function statuspensiun($nip)
{
    $user = auth()->user();

    // 1. Logika Role Dinamis
    $roleMapping = \DB::table('roles_mapping')
        ->where('jabatan_id', $user->jabatan_id)
        ->where('level_id', $user->level_id)
        ->first();

    $isManager = $roleMapping && str_contains($roleMapping->route_name, 'manager');

    // 2. Ambil Data Pengajuan
    $pekerjaanData = Pekerjaan::where('nomor_urut_pegawai', $nip)->first();

    $pengajuanpensiun = PengajuanPensiun::with(['files', 'logPersetujuanPensiun' => function ($query) {
        // 🔄 DISESUAIKAN: Menggunakan updated_at hasil standarisasi tadi
        $query->orderByDesc('updated_at');
    }])
    ->where('nomor_urut_pegawai', $nip)
    ->orderBy('created_at', 'desc')
    ->firstOrFail();

    // 3. Siapkan data untuk Processor
    $submissionRaw = [
        'id' => $pengajuanpensiun->id_pensiun, // Tambahkan ID untuk link cetak surat
        'type' => 'Pensiun',
        'status_pengajuan' => $pengajuanpensiun->status_pensiun, // ➕ Gunakan status dari tabel utama
        'logs' => $pengajuanpensiun->logPersetujuanPensiun ? $pengajuanpensiun->logPersetujuanPensiun->toArray() : [],
        'jenis_pengajuan' => $pengajuanpensiun->jenis_pengajuan,
        'tmt_pegawai' => $pengajuanpensiun->tmt_pegawai,
        'tmt_pensiun' => $pengajuanpensiun->tmt_pensiun,
        'masa_kerja' => $pengajuanpensiun->masa_kerja,
        'created_at' => $pengajuanpensiun->created_at,
    ];

    // Proses melalui Service Class
    $submission = $this->submissionProcessor->processSubmissions(collect([$submissionRaw]))->first();

    // 4. Logika Tampilan & Variabel Penting
    $latestLog = $pengajuanpensiun->logPersetujuanPensiun->first();
    $komentarStatus = $latestLog ? $latestLog->komentar : 'Menunggu keputusan';

    $submissionType = 'Pensiun';
    $pageTitle = 'Lacak Pengajuan ' . $submissionRaw['type'];

    // 5. BREADCRUMBS & ROUTE OTOMATIS
    $roleName = $roleMapping->role_name ?? 'Pegawai';
    $parentLabel = $isManager ? "Manajemen Pengajuan ↦ Approval Pengajuan $roleName" : 'Data Pengajuan';
    $parentRouteName = $isManager ? 'manager.pilihpengajuan' : 'datapengajuan.formDataPengajuan';

    $breadcrumbs = [
        'Beranda' => $user->dashboard_link,
        $parentLabel => route($parentRouteName),
        $pageTitle => null
    ];

    // 6. LAYOUT OTOMATIS
    $layout = $user->layout_file;

    return view('datapengajuan.lacakpengajuan', compact(
        'pengajuanpensiun',
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


    public function lihatDokumen($id)
    {
        $fileDokumen = FilePersyaratanPensiun::findOrFail($id);
        $path = $fileDokumen->path_file_server;

        // Jalur absolut ke file Anda
        $filePath = storage_path(path: 'app/private/' . $path);

        // --- DEBUGGING PAKSA ---
        // Uncomment baris ini untuk melihat path ABSOLUT yang sedang dicari
        // dd($filePath);
        // -----------------------

        // Pastikan file tersebut ada di sistem file
        if (!file_exists($filePath)) {
            // Tampilkan error 404 dengan path lengkap agar Anda bisa memeriksanya
            abort(404, 'Dokumen tidak ditemukan di server: ' . $filePath);
        }

        return response()->file($filePath, [
            'Content-Disposition' => 'inline; filename="' . $fileDokumen->nama_file_asli . '"'
        ]);
    }

}
