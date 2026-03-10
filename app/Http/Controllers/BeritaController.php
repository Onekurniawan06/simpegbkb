<?php

namespace App\Http\Controllers;

use App\Models\Berita; // Pastikan Model Berita sudah dibuat (dari instruksi sebelumnya)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LogPersetujuanCuti;
use App\Models\LogPersetujuanLembur;
use App\Models\LogPersetujuanPensiun;

class BeritaController extends Controller
{
    public function index()
    {
        $userNomorUrut = optional(Auth::user())->nomor_urut_pegawai; // Asumsi field ini ada di model User

        $hasPendingCuti = LogPersetujuanCuti::where('nomor_urut_pegawai', $userNomorUrut)
                                            ->where('status_pengajuan', 'diproses')
                                            ->exists();

        $hasPendingLembur = LogPersetujuanLembur::where('nomor_urut_pegawai', $userNomorUrut)
                                            ->where('status_persetujuan', 'diproses')
                                            ->exists();

        $hasPendingPensiun = LogPersetujuanPensiun::where('nomor_urut_pegawai', $userNomorUrut)
                                            ->where('status_persetujuan', 'diproses')
                                            ->exists();

        // Kita ambil berita dari 3 hari terakhir (72 jam) agar ada rentang waktu
        // untuk pengecekan "sudah dibaca" di sisi client sebelum benar-benar hilang.
        $daftar_berita = Berita::where('tanggal_posting', '>=', now()->subHours(72))
            ->orderBy('tanggal_posting', 'desc')
            ->paginate(5);
            // dd($daftar_berita);
        
        $total_belum_dibaca = Berita::where('tanggal_posting', '>=', now()->subDay())->count();

        // Mengirim data ke view 'berita.index'
        return view('pegawai.dashboard', compact('daftar_berita','hasPendingCuti', 'hasPendingLembur', 'hasPendingPensiun', 'total_belum_dibaca'));
    }
}
