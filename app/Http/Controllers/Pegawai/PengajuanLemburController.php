<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\SubmissionProcessorService;
use App\Models\PengajuanLembur;
use App\Models\LogPersetujuanLembur;
use App\Enums\StatusPersetujuan; // Jika Anda menggunakan Enum
use App\Models\Pekerjaan;
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
        $nomorUrutPegawai = $user->nomor_urut_pegawai;

        $pekerjaanData = Pekerjaan::where('nomor_urut_pegawai', $nomorUrutPegawai)->first();

        $pageTitle = 'Pengajuan Lembur';
        // Anda bisa tambahkan variabel lain jika perlu, misal:
        $breadcrumbs = [
            'Beranda' => route('pegawai.dashboard'),
            $pageTitle => null
        ];

        return view('pegawai.lembur', compact('user', 'pekerjaanData', 'pageTitle', 'breadcrumbs'));
    }

    /**
     * Menyimpan atau memperbarui pengajuan lembur baru
     */
    public function updateLembur(Request $request)
    {
        $validatedData = $request->validate([
            'tanggal_lembur' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'total_jam_lembur' => 'required|string|max:100',
            'uraian_tugas' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $nomorUrutPegawai = $user->nomor_urut_pegawai;

        $existingRequest = PengajuanLembur::where('nomor_urut_pegawai', $nomorUrutPegawai)
                        ->whereHas('logPersetujuanLembur', function ($query) {
                            $query->where('status_persetujuan', StatusPersetujuan::DIPROSES);
                        })->exists();

        // UBAH DISINI: Gunakan ->with('error', ...)
        if ($existingRequest) {
            return back()->with('error', 'Anda masih memiliki pengajuan lembur yang sedang diproses.')->withInput();
        }

        DB::beginTransaction();
        try {
            $pengajuan = PengajuanLembur::create([
                'nomor_urut_pegawai' => $nomorUrutPegawai,
                'tanggal_lembur' => $validatedData['tanggal_lembur'],
                'jam_mulai' => $validatedData['jam_mulai'],
                'jam_selesai' => $validatedData['jam_selesai'],
                'total_jam_lembur' => $validatedData['total_jam_lembur'],
                'uraian_tugas' => $validatedData['uraian_tugas'],
            ]);

            LogPersetujuanLembur::create([
                'lembur_id' => $pengajuan->id_lembur,
                'nomor_urut_pegawai' => $nomorUrutPegawai,
                'tahap_persetujuan' => 'Pengajuan Awal',
                'status_persetujuan' => StatusPersetujuan::DIPROSES,
                'komentar' => 'Menunggu persetujuan Manager.',
                'update_at' => Carbon::now(),
            ]);

            DB::commit();

            // Pastikan rute ini kembali ke halaman tempat modal berada
            return redirect()->route('pegawai.formLembur')->with('success', 'Pengajuan lembur berhasil dikirim.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function statuslembur($nip) // Menggunakan opsi 1 dari diskusi sebelumnya
    {
        $pekerjaanData = Pekerjaan::where('nomor_urut_pegawai', $nip)->first();
        // dd($pekerjaanData);
        // Mengambil data pengajuan lembur beserta relasi jenisCuti dan log yang diurutkan
        $pengajuanlembur = PengajuanLembur::with(['logPersetujuanLembur' => function ($query) {
                            $query->orderByDesc('updated_at');
                        }])
                        ->where('nomor_urut_pegawai', $nip)
                        ->orderBy('created_at', 'desc')
                        ->firstOrFail();

        $submissionRaw = [
            'type' => 'Lembur',
            'logs' => $pengajuanlembur->LogPersetujuanLembur->toArray(),
            'tanggal_lembur' => $pengajuanlembur->tanggal_lembur,
            'jam_mulai' => $pengajuanlembur->jam_mulai,
            'jam_selesai' => $pengajuanlembur->jam_selesai,
            'total_jam_lembur' => $pengajuanlembur->total_jam_lembur,
            'uraian_tugas' => $pengajuanlembur->uraian_tugas,
            // 'jenis_pengajuan' => $pengajuanlembur->jenis_pengajuan,
            // ... tambahkan field lain yang dibutuhkan processSubmissions
        ];

        $submission = $this->submissionProcessor->processSubmissions(collect([$submissionRaw]))->first();

        // 3. Ambil stageData yang sudah matang dari hasil proses
        // $stageData = $processedSubmission['stageData'];

        // Mengambil log terbaru untuk ditampilkan di bagian "Alasan Ditolak/Disetujui"
        $latestLog = $pengajuanlembur->logPersetujuanLembur->first();
        $komentarStatus = $latestLog ? $latestLog->komentar : 'Menunggu keputusan';

        $submissionType = 'Lembur'; // Variabel yang harus ada

        // Pastikan pageTitle diset dengan benar menggunakan variabel dinamis
        $pageTitle = 'Lacak Pengajuan ' . $submissionRaw['type']; // Hasilnya: Lacak Pengajuan Lembur
        $breadcrumbs = [
            'Beranda' => route('pegawai.dashboard'),
            'Data Pengajuan' => route('pegawai.formDataPengajuan'),
            $pageTitle => null                                // Halaman saat ini
        ];

        return view('pegawai.lacakpengajuan',
            compact('pengajuanlembur', 'pageTitle', 'komentarStatus', 'pekerjaanData', 'submissionRaw', 'submissionType', 'submission', 'breadcrumbs'))->with('pengajuankenaikan', null);
    }

}
