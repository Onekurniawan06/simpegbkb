<?php

namespace App\Http\Controllers\Pegawai; // Namespace baru

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
        $nomorUrutPegawai = $user->nomor_urut_pegawai;

        // Terapkan eager loading 'with' untuk mengambil data divisi sekaligus
        $pekerjaanData = Pekerjaan::with('divisi')
                                    ->where('nomor_urut_pegawai', $nomorUrutPegawai)
                                    ->first();

        if ($pekerjaanData && $pekerjaanData->tmt_pegawai) {
            $cleanDateString = str_replace('/', '-', $pekerjaanData->tmt_pegawai);
            $tmtPegawai = Carbon::parse($cleanDateString);
            $tmt_pegawai_formatted = $tmtPegawai->format('d-m-Y');
        }

        $pageTitle = 'Pengajuan Kenaikan Pangkat, Gaji dan Tunjangan';
        $breadcrumbs = [
            'Beranda' => route('pegawai.dashboard'), // Muncul dengan Ikon Home
            $pageTitle => null                        // Muncul sebagai teks bold tanpa link
        ];

        // Variabel $pekerjaanData yang dikirim ke view kini sudah membawa data divisi
        return view('pegawai.pangkatgajitunjangan', compact('user','pekerjaanData', 'pageTitle', 'breadcrumbs', 'tmt_pegawai_formatted'));
    }

    public function updatePangkatGajiTunjangan(Request $request)
    {
        // 1. Lakukan Validasi Data Form Utama
        $validatedData = $request->validate([
            'nomor_urut_pegawai' => 'required|string|max:15',
            'nama_pegawai'      => 'required|string|max:100',
            'pangkat'           => 'nullable|string|max:50',
            'grade'             => 'nullable|string|max:10',
            'jabatan'           => 'nullable|string|max:100',
            'unit_kerja'        => 'nullable|string|max:100',
            'status_pegawai'    => 'nullable|string|max:50',
            'tmt_pegawai'       => 'nullable|date',
            'jenis_pengajuan'   => 'required|string|max:100',
            'masa_kerja'        => 'nullable|string|max:50',
            // 'documents' => 'required|array', // Menggunakan 'documents'
            'documents.*' => 'required|file|mimes:pdf|max:5120',
        ]);

        $dataUntukTabelUtama = $validatedData;
        unset($dataUntukTabelUtama['documents']);

        $nomorPegawai = $validatedData['nomor_urut_pegawai'];
        $jenisPengajuan = $validatedData['jenis_pengajuan'];
        // 2. Lakukan Pengecekan Pengajuan Pensiun yang Masih Pending
        $hasPendingPangkatGajiTunjanganRequest = PengajuanPangkatgajitunjangan::where('nomor_urut_pegawai', $nomorPegawai)
            // Asumsikan ada relasi 'logPersetujuanPangkatgajitunjangan' di model PengajuanPangkatgajitunjangan
            ->whereHas('logPersetujuanPangkatgajitunjangan', function ($query) {
                $query->where('status_persetujuan', StatusPersetujuan::DIPROSES);
            })
            ->exists();

        // 3. Jika ditemukan pengajuan pending, kembalikan user dengan pesan error
        if ($hasPendingPangkatGajiTunjanganRequest) {
            return back()
                ->with('error', 'Anda masih memiliki pengajuan Kenaikan Pangkat, Gaji dan Tunjangan yang sedang diproses.')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // A. Simpan data pengajuan utama
            $pengajuan = PengajuanPangkatgajitunjangan::create($dataUntukTabelUtama);
            $idPengajuanBaru = $pengajuan->id_pengajuan;
            $nomorPegawai = $validatedData['nomor_urut_pegawai'];

            // B. Simpan data di tabel `log_persetujuan_pangkatgajitunjangan`
            LogPersetujuanPangkatgajitunjangan::create([
                'id_pengajuan'       => $idPengajuanBaru,
                'nomor_urut_pegawai' => $nomorPegawai,
                'tahap_persetujuan'  => 'Pengajuan Awal',
                'status_persetujuan' => 'diproses',
                'komentar'           => 'Menunggu persetujuan Kepala SKK MR.',
            ]);

            \Log::info('Memeriksa file yang diterima:', $request->allFiles());\Log::info('Memeriksa file yang diterima:', $request->allFiles());

            // C. Iterasi dan simpan setiap file MENGGUNAKAN LOGIKA BARU
            if ($request->hasFile('documents')) {
                \Log::info('hasFile(documents) mengembalikan TRUE. Memulai perulangan file...');
                $safeFolderName = Str::slug($jenisPengajuan);
                $baseUploadPath = 'dokumen_pangkat_gaji/' . $safeFolderName . '/' . $nomorPegawai;

                foreach ($request->file('documents') as $kodeDokumen => $file) {

                    $extension = $file->getClientOriginalExtension();
                    $originalName = $file->getClientOriginalName();

                    // Gunakan base path yang baru
                    $uploadPath = $baseUploadPath;

                    $fileNameFormatted = sprintf(
                        "%s_%s_%s.%s",
                        $kodeDokumen,
                        $nomorPegawai,
                        Carbon::now()->timestamp,
                        $extension
                    );

                    $path = Storage::disk('local')->putFileAs(
                        $uploadPath, $file, $fileNameFormatted
                    );

                    // ... (FilePersyaratanpangkatgajitunjangan::create([...]) menggunakan $path baru) ...
                    FilePersyaratanpangkatgajitunjangan::create([
                        'pengajuan_pangkatgajitunjangan_id' => $idPengajuanBaru,
                        'nomor_urut_pegawai' => $nomorPegawai,
                        'nama_file_asli'     => $originalName,
                        'path_file_server'   => $path,
                        'tipe_dokumen'       => $kodeDokumen,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('pegawai.pangkatgajitunjangan')->with('success', 'Pengajuan berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal menyimpan pengajuan Pangkat/Gaji/Tunjangan: ' . $e->getMessage());

            return back()->withInput()->with('error', 'Gagal menyimpan pengajuan: Terjadi kesalahan server.');
        }
    }

    public function statuspangkatgajitunjangan ($nip) // Menggunakan opsi 1 dari diskusi sebelumnya
    {
        $pekerjaanData = Pekerjaan::where('nomor_urut_pegawai', $nip)->first();
        // dd($pekerjaanData);
        // Mengambil data pengajuan pensiun beserta relasi jenisCuti dan log yang diurutkan
        $pengajuankenaikan = PengajuanPangkatgajitunjangan::with(['files', 'LogPersetujuanPangkatgajitunjangan' => function ($query) {
                        $query->orderByDesc('updated_at');
                    }])
                    ->where('nomor_urut_pegawai', $nip)
                    ->orderBy('created_at', 'desc')
                    ->firstOrFail();

        $submissionRaw = [
            'type' => 'PangkatGajiTunjangan',
            'logs' => $pengajuankenaikan->LogPersetujuanPangkatgajitunjangan->toArray(),
            // ... tambahkan field lain yang dibutuhkan processSubmissions
            'tmt_pegawai' => $pengajuankenaikan->tmt_pegawai,
            'masa_kerja' => $pengajuankenaikan->masa_kerja,
            'jenis_pengajuan' => $pengajuankenaikan->jenis_pengajuan,
        ];

        // $processedSubmission = $this->submissionProcessor->processSubmissions(collect([$submissionRaw]))->first();
        $submission = $this->submissionProcessor->processSubmissions(collect([$submissionRaw]))->first();

        // 3. Ambil stageData yang sudah matang dari hasil proses
        // $stageData = $processedSubmission['stageData'];

        // Mengambil log terbaru untuk ditampilkan di bagian "Alasan Ditolak/Disetujui"
        $latestLog = $pengajuankenaikan->LogPersetujuanPangkatgajitunjangan->first();
        $komentarStatus = $latestLog ? $latestLog->komentar : 'Menunggu keputusan';

        $submissionType = 'Kenaikan Pangkat/Gaji/Tunjangan'; // Variabel yang harus ada

        // Pastikan pageTitle diset dengan benar menggunakan variabel dinamis
        $pageTitle = 'Lacak Pengajuan ' . $submissionRaw['type']; // Hasilnya: Lacak Pengajuan Lembur
        $breadcrumbs = [
            'Beranda' => route('pegawai.dashboard'),
            'Data Pengajuan' => route('pegawai.formDataPengajuan'),
            $pageTitle => null                                // Halaman saat ini
        ];

        return view('pegawai.lacakpengajuan',
            compact('pengajuankenaikan', 'pageTitle', 'komentarStatus', 'pekerjaanData', 'submissionRaw', 'submissionType', 'submission', 'breadcrumbs')
        );
    }

    public function lihatDokumen($id)
    {
        $fileDokumen = FilePersyaratanPangkatGajiTunjangan::findOrFail($id);
        $path = $fileDokumen->path_file_server;

        // Jalur absolut ke file Anda
        $filePath = storage_path(path: 'app/private/' . $path);

        // Pastikan file tersebut ada di sistem file
        if (!file_exists($filePath)) {
            // Jika tidak ada, hentikan eksekusi dan kirim error 404
            abort(404, 'Dokumen tidak ditemukan di server.');
        }

        // --- PASTIKAN ANDA MENGGUNAKAN INI ---
        // Laravel akan otomatis menentukan Content-Type dan mengirim status 200 OK
        return response()->file($filePath, [
            'Content-Disposition' => 'inline; filename="' . $fileDokumen->nama_file_asli . '"'
        ]);
        // ------------------------------------

        // Pastikan tidak ada return null, return "", atau die() setelah kode di atas.
    }
}
