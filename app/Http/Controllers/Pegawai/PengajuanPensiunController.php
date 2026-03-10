<?php

namespace App\Http\Controllers\Pegawai;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pekerjaan;
use Carbon\Carbon; // Tambahkan ini
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use App\Services\SubmissionProcessorService; // PENTING: Import service class
use App\Models\PengajuanPensiun;
use App\Models\LogPersetujuanPensiun;
use App\Models\FilePersyaratanPensiun;
use App\Enums\StatusPersetujuan; // Jika Anda menggunakan Enum
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
        $nomorUrutPegawai = $user->nomor_urut_pegawai;

        // Terapkan eager loading 'with' untuk mengambil data divisi sekaligus
        $pekerjaanData = Pekerjaan::with('divisi')
                                    ->where('nomor_urut_pegawai', $nomorUrutPegawai)
                                    ->first();

        // if ($pekerjaanData && $pekerjaanData->id_divisi) {
        //     $checkDivisi = Divisi::find($pekerjaanData->id_divisi);
        //     // Gunakan dd() untuk melihat apakah data ini null atau berisi objek
        //     dd($checkDivisi);
        //     // Jika ini menampilkan NULL, maka data master ID 3 memang tidak ada.
        // }

        $tmt_pensiun_otomatis = null;
        $tmt_pegawai_formatted = $pekerjaanData->tmt_pegawai ?? null;
        if ($pekerjaanData && $pekerjaanData->tmt_pegawai) {
            $cleanDateString = str_replace('/', '-', $pekerjaanData->tmt_pegawai);
            $tmtPegawai = Carbon::parse($cleanDateString);
            $tmt_pegawai_formatted = $tmtPegawai->format('d-m-Y');
            $batasUsiaPensiun = 58;
            $tmtPensiun = $tmtPegawai->copy()->addYears($batasUsiaPensiun)->subDay();
            $tmt_pensiun_otomatis = $tmtPensiun->format('d-m-Y');
        }
        $pageTitle = 'Pengajuan Pensiun';
        $breadcrumbs = [
            'Beranda' => route('pegawai.dashboard'), // Muncul dengan Ikon Home
            $pageTitle => null                        // Muncul sebagai teks bold tanpa link
        ];

        // Variabel $pekerjaanData yang dikirim ke view kini sudah membawa data divisi
        return view('pegawai.pensiun', compact('user','pekerjaanData', 'pageTitle', 'breadcrumbs', 'tmt_pensiun_otomatis', 'tmt_pegawai_formatted'));
    }

    public function updatePensiun(Request $request) // Ganti nama fungsi agar lebih jelas
    {
        if ($request->hasFile('file-upload')) {
            \Log::info('File Diterima: ' . count($request->file('file-upload')));
        } else {
            \Log::error('TIDAK ADA FILE DITERIMA DI REQUEST!');
        }
        // 1. Validasi Semua Data yang Masuk
        $validatedData = $request->validate([
            // Hapus validasi untuk 'id_pengajuan' karena kita membuatnya baru
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
            'tmt_pensiun'       => 'required|date',

        ]);

        $nomorPegawai = $validatedData['nomor_urut_pegawai'];
        // 2. Lakukan Pengecekan Pengajuan Pensiun yang Masih Pending
        $hasPendingPensiunRequest = PengajuanPensiun::where('nomor_urut_pegawai', $nomorPegawai)
            // Asumsikan ada relasi 'logPersetujuanPensiun' di model PengajuanPensiun
            ->whereHas('logPersetujuanPensiun', function ($query) {
                $query->where('status_persetujuan', StatusPersetujuan::DIPROSES);
            })
            ->exists();

        // 3. Jika ditemukan pengajuan pending, kembalikan user dengan pesan error
        if ($hasPendingPensiunRequest) {
            return back()
                ->with('error', 'Anda masih memiliki pengajuan pensiun yang sedang diproses.')
                ->withInput();
        }

        // 3. Gunakan Transaksi Database untuk menyimpan ke 3 tabel sekaligus
        try {
            DB::beginTransaction();

            // A. SIMPAN data di tabel utama `pengajuan_pensiun` (CREATE baru)
            $pensiunBaru = PengajuanPensiun::create($validatedData);

            // B. AMBIL ID yang baru saja dibuat untuk digunakan di tabel lain
            // Asumsi 'id_pengajuan' adalah primary key yang auto-increment di tabel utama
            $idPengajuanBaru = $pensiunBaru->id_pengajuan;
            $nomorPegawai = $validatedData['nomor_urut_pegawai'];
            // $namaPegawai     = $validatedData['nama_pegawai'];
            // $jenisPengajuan  = $validatedData['jenis_pengajuan'];
            // $tanggalPengajuan = Carbon::now()->format('d-m-Y');

            // C. Simpan data baru di tabel `log_persetujuan_pensiun`
            LogPersetujuanPensiun::create([
                'id_pengajuan'       => $idPengajuanBaru,
                'nomor_urut_pegawai' => $nomorPegawai,
                'tahap_persetujuan' => 'Pengajuan Awal',
                'status_persetujuan' => StatusPersetujuan::DIPROSES,
                'komentar' => 'Menunggu persetujuan Kepala SKK MR',
            ]);

            // D. Simpan data baru di tabel `file_persyaratanpensiun`
            // Kita mengharapkan input bernama 'documents[key]'
            if ($request->hasFile('documents')) {
                // Loop melalui array 'documents' yang key-nya adalah 'kode_dokumen'
                foreach ($request->file('documents') as $kodeDokumen => $file) {

                    $extension = $file->getClientOriginalExtension();
                    $originalName = $file->getClientOriginalName();

                    $uploadPath = 'dokumen_pensiun/' . $nomorPegawai;

                    // Nama file di server sekarang bisa lebih sederhana karena kita punya 'kode_dokumen' di DB
                    $fileNameFormatted = sprintf(
                        "%s_%s_%s.%s",
                        $kodeDokumen, // Menggunakan kode dokumen sebagai bagian dari nama file
                        $nomorPegawai,
                        Carbon::now()->timestamp, // Tambahkan timestamp agar unik
                        $extension
                    );

                    $path = Storage::disk('local')->putFileAs(
                        $uploadPath, $file, $fileNameFormatted
                    );

                    // --- Perubahan Utama di sini ---
                    FilePersyaratanPensiun::create([
                        'pengajuan_pensiun_id' => $idPengajuanBaru, // <-- Mengisi kolom baru
                        'nomor_urut_pegawai' => $nomorPegawai,
                        'nama_file_asli'     => $originalName,
                        'path_file_server'   => $path,
                        // 'tipe_dokumen' digunakan untuk menyimpan MIME type di tabel lama Anda
                        // Kita bisa gunakan $kodeDokumen di kolom terpisah jika mau,
                        // tapi tabel Anda sebelumnya menggunakan 'tipe_dokumen' untuk MIME type.
                        'tipe_dokumen'       => $kodeDokumen,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('pegawai.formPensiun')->with('success', 'Pengajuan pensiun berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Anda bisa log $e->getMessage() di sini untuk debugging yang lebih baik
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data. Perubahan dibatalkan. Pesan Error: ' . $e->getMessage());
        }
    }

    public function statuspensiun($nip) // Menggunakan opsi 1 dari diskusi sebelumnya
    {
        $pekerjaanData = Pekerjaan::where('nomor_urut_pegawai', $nip)->first();

        // MENGAMBIL DATA DENGAN RELASI 'files' DAN 'logPersetujuanPensiun'
        $pengajuanpensiun = PengajuanPensiun::with(['files', 'logPersetujuanPensiun' => function ($query) {
                            $query->orderByDesc('updated_at');
                        }])
                        ->where('nomor_urut_pegawai', $nip)
                        ->orderBy('created_at', 'desc')
                        ->firstOrFail();

        $submissionRaw = [
            'type' => 'Pensiun',
            'logs' => $pengajuanpensiun->LogPersetujuanPensiun->toArray(),
            'jenis_pengajuan' => $pengajuanpensiun->jenis_pengajuan,
            'tmt_pegawai' => $pengajuanpensiun->tmt_pegawai,
            'tmt_pensiun' => $pengajuanpensiun->tmt_pensiun,
            'masa_kerja' => $pengajuanpensiun->masa_kerja,
            // ... tambahkan field lain yang dibutuhkan processSubmissions
        ];

        $submission = $this->submissionProcessor->processSubmissions(collect([$submissionRaw]))->first();

        $latestLog = $pengajuanpensiun->logPersetujuanPensiun->first();
        $komentarStatus = $latestLog ? $latestLog->komentar : 'Menunggu keputusan';

        $submissionType = 'Pensiun'; // Variabel yang harus ada

        $pageTitle = 'Lacak Pengajuan ' . $submissionRaw['type'];
        $breadcrumbs = [
            'Beranda' => route('pegawai.dashboard'),
            'Data Pengajuan' => route('pegawai.formDataPengajuan'),
            $pageTitle => null
        ];

        return view('pegawai.lacakpengajuan',
            compact('pengajuanpensiun', 'pageTitle', 'komentarStatus', 'pekerjaanData', 'submissionRaw', 'submissionType', 'submission', 'breadcrumbs')
        );
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
