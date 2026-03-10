<?php

namespace App\Http\Controllers\KenaikanPangkatgajitunjangan; // Namespace baru

use App\Http\Controllers\Controller; // Jangan lupa import base Controller
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SubmissionProcessorService; // PENTING: Import service class
// use Illuminate\Support\Collection;
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

        // 1. Logika Role Dinamis dari Database (Menghapus sisa-sisa level_akses)
        $roleMapping = \DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        // Cek apakah dia manager berdasarkan route_name di database
        $isManagerOrKepala = $roleMapping && str_contains($roleMapping->route_name, 'manager');

        // 2. Data Master (Eager Loading Divisi & Format Tanggal)
        $pekerjaanData = Pekerjaan::with('divisi')
            ->where('nomor_urut_pegawai', $user->nomor_urut_pegawai)
            ->first();

        $tmt_pegawai_formatted = null;
        if ($pekerjaanData && $pekerjaanData->tmt_pegawai) {
            $cleanDateString = str_replace('/', '-', $pekerjaanData->tmt_pegawai);
            $tmt_pegawai_formatted = Carbon::parse($cleanDateString)->format('d-m-Y');
        }

        // --- VARIABEL PENTING: JANGAN DIHAPUS ---
        $submissionsType = 'Pangkat/Gaji/Tunjangan';
        // ----------------------------------------

        // 3. Logika Navigasi & Label Dinamis
        $roleLabel = $roleMapping->role_name ?? 'Pegawai';
        $pageTitle = 'Kenaikan Pangkat, Gaji & Tunjangan ' . ($isManagerOrKepala ? 'Manager' : 'Pegawai');

        $roleLabel = $roleMapping->role_name ?? 'Pegawai'; // Pastikan variabel ini sudah diambil dari DB

        $parentRouteName = $isManagerOrKepala ? 'manager.pilihpengajuan' : 'datapengajuan.formDataPengajuan';

        // Menggunakan variabel $roleLabel agar nama jabatan dinamis
        $parentLabel = $isManagerOrKepala
            ? "Manajemen Pengajuan ↦ Approval Pengajuan $roleLabel"
            : 'Data Pengajuan';

        // 4. Susun Breadcrumbs Dinamis
        $breadcrumbs = [
            'Beranda' => $user->dashboard_link,
            $parentLabel => route($parentRouteName),
            $pageTitle => null
        ];

        // 5. Layout Dinamis (PENTING: Agar Sidebar sesuai role)
        $layout = $user->layout_file; // Menggunakan Accessor layout_file Anda

        return view('kenaikanpangkatgajitunjangan.pangkatgajitunjangan', compact(
            'user',
            'pekerjaanData',
            'pageTitle',
            'breadcrumbs',
            'tmt_pegawai_formatted',
            'parentRouteName',
            'submissionsType',
            'layout', // Tambahkan layout agar bisa di-extends di Blade
            'isManagerOrKepala'
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
            'tmt_pegawai'        => 'nullable|date',
            'jenis_pengajuan'    => 'required|string|max:100',
            'masa_kerja'         => 'nullable|string|max:50',
            'documents'          => 'required|array',
            'documents.*'        => 'required|file|mimes:pdf|max:5120',
        ]);

        $dataUntukTabelUtama = $validatedData;
        unset($dataUntukTabelUtama['documents']);

        $nomor_urut_pegawai = $validatedData['nomor_urut_pegawai'];
        $jenisPengajuan = $validatedData['jenis_pengajuan'];

        // 2. Cek Pengajuan Pending (Sesuai Logika Sebelumnya)
        $hasPendingRequest = PengajuanPangkatgajitunjangan::where('nomor_urut_pegawai', $nomor_urut_pegawai)
            ->whereHas('logPersetujuanPangkatgajitunjangan', function ($query) {
                $query->where('status_persetujuan', StatusPersetujuan::DIPROSES);
            })->exists();

        if ($hasPendingRequest) {
            return back()->with('error', 'Anda masih memiliki pengajuan yang sedang diproses.')->withInput();
        }

        $user = auth()->user();
        $roleMapping = \DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        $isManagerOrKepala = $roleMapping && str_contains($roleMapping->route_name, 'manager');
        // ------------------------------------------

        DB::beginTransaction();
        try {
            // A. Simpan data pengajuan utama
            $pengajuan = PengajuanPangkatgajitunjangan::create($dataUntukTabelUtama);
            $idPengajuanBaru = $pengajuan->id_pengajuan;

            // B. Simpan Log (Gunakan konstanta StatusPersetujuan)
            LogPersetujuanPangkatgajitunjangan::create([
                'id_pengajuan'       => $idPengajuanBaru,
                'nomor_urut_pegawai' => $nomor_urut_pegawai,
                'tahap_persetujuan'  => 'Pengajuan Awal',
                'status_persetujuan' => StatusPersetujuan::DIPROSES,
                'komentar'           => 'Menunggu persetujuan.',
                'update_at'          => Carbon::now(),
            ]);

            // C. Simpan File Dokumen
            if ($request->hasFile('documents')) {
                $safeFolderName = \Str::slug($jenisPengajuan);
                $baseUploadPath = 'dokumen_pangkat_gaji/' . $safeFolderName . '/' . $nomor_urut_pegawai;

                foreach ($request->file('documents') as $kodeDokumen => $file) {
                    if ($file->isValid()) {
                        $extension = $file->getClientOriginalExtension();
                        $originalName = $file->getClientOriginalName();

                        $fileNameFormatted = sprintf(
                            "%s_%s_%s.%s",
                            $kodeDokumen,
                            $nomor_urut_pegawai,
                            Carbon::now()->timestamp,
                            $extension
                        );

                        $path = $file->storeAs($baseUploadPath, $fileNameFormatted, 'local');

                        FilePersyaratanpangkatgajitunjangan::create([
                            'pengajuan_pangkatgajitunjangan_id' => $idPengajuanBaru,
                            'nomor_urut_pegawai'               => $nomor_urut_pegawai,
                            'nama_file_asli'                   => $originalName,
                            'path_file_server'                 => $path,
                            'tipe_dokumen'                     => $kodeDokumen,
                        ]);
                    }
                }
            }

            DB::commit();

            $user = auth()->user();
            $roleMapping = \DB::table('roles_mapping')
                ->where('jabatan_id', $user->jabatan_id)
                ->where('level_id', $user->level_id)
                ->first();
            $isManagerOrKepala = $roleMapping && str_contains($roleMapping->route_name, 'manager');

            // 2. Tentukan Route Tujuan (Gunakan Nama Route, bukan URL)
            $targetRoute = $isManagerOrKepala ? 'manager.pilihpengajuan' : 'pegawai.dashboard';
            $parentRouteName = $isManagerOrKepala ? 'manager.pilihpengajuan' : 'datapengajuan.formDataPengajuan';

            // 3. Redirect dengan Session (Key 'success' wajib ada untuk memicu modal)
            return redirect()->route($targetRoute)->with([
                'success' => 'Permintaan Kenaikan Pangkat/Gaji Anda telah tercatat.',
                'modal_title' => 'Update Berhasil!',
                'modal_link' => route($parentRouteName)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal simpan Pangkat/Gaji: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function statuspangkatgajitunjangan($nip)
    {
        $user = auth()->user();

        // 1. Logika Role Dinamis (Menghapus level_akses)
        $roleMapping = \DB::table('roles_mapping')
            ->where('jabatan_id', $user->jabatan_id)
            ->where('level_id', $user->level_id)
            ->first();

        // Menggunakan nama variabel yang konsisten dengan form pengajuan
        $isManagerOrKepala = $roleMapping && str_contains($roleMapping->route_name, 'manager');

        // 2. Ambil Data Pengajuan
        $pekerjaanData = Pekerjaan::where('nomor_urut_pegawai', $nip)->first();

        $pengajuankenaikan = PengajuanPangkatgajitunjangan::with(['files', 'LogPersetujuanPangkatgajitunjangan' => function ($query) {
            $query->orderByDesc('updated_at');
        }])
        ->where('nomor_urut_pegawai', $nip)
        ->orderBy('created_at', 'desc')
        ->firstOrFail();

        // 3. Siapkan data untuk Processor
        $submissionRaw = [
            'type' => 'PangkatGajiTunjangan',
            'logs' => $pengajuankenaikan->LogPersetujuanPangkatgajitunjangan ? $pengajuankenaikan->LogPersetujuanPangkatgajitunjangan->toArray() : [],
            'tmt_pegawai' => $pengajuankenaikan->tmt_pegawai,
            'masa_kerja' => $pengajuankenaikan->masa_kerja,
            'jenis_pengajuan' => $pengajuankenaikan->jenis_pengajuan,
            'created_at' => $pengajuankenaikan->created_at,
        ];

        $submission = $this->submissionProcessor->processSubmissions(collect([$submissionRaw]))->first();

        // 4. Logika Tampilan & Variabel Penting
        $latestLog = $pengajuankenaikan->LogPersetujuanPangkatgajitunjangan->first();
        $komentarStatus = $latestLog ? $latestLog->komentar : 'Menunggu keputusan';

        // Gunakan string yang sama dengan yang diharapkan oleh Data Transformer
        $submissionType = 'PangkatGajiTunjangan';

        $pageTitle = 'Lacak Pengajuan Kenaikan Pangkat/Gaji/Tunjangan';

        // 5. BREADCRUMBS & ROUTE OTOMATIS
        $roleName = $roleMapping->role_name ?? 'Pegawai'; // Mengambil nama jabatan dari DB

        $parentLabel = $isManagerOrKepala
            ? "Manajemen Pengajuan ↦ Approval Pengajuan $roleName"
            : 'Data Pengajuan';

        $parentRouteName = $isManagerOrKepala ? 'manager.pilihpengajuan' : 'datapengajuan.formDataPengajuan';

        $breadcrumbs = [
            'Beranda' => $user->dashboard_link,
            $parentLabel => route($parentRouteName),
            $pageTitle => null
        ];

        // 6. LAYOUT OTOMATIS
        $layout = $user->layout_file; // Menggunakan Accessor layout_file

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
