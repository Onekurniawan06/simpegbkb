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
        $user = Auth::user();
        $userNomorUrut = $user->nomor_urut_pegawai;

        // --- 1. Logika Status Kartu (CEK SEMUA TABEL) ---
        $hasPendingCuti = \DB::table('log_persetujuan_cuti')
            ->where('nomor_urut_pegawai', $userNomorUrut)
            ->where('status_pengajuan', 'diproses')->exists();

        $hasPendingLembur = \DB::table('log_persetujuan_lembur')
            ->where('nomor_urut_pegawai', $userNomorUrut)
            ->where('status_persetujuan', 'diproses')->exists();

        $hasPendingPensiun = \DB::table('log_persetujuan_pensiun')
            ->where('nomor_urut_pegawai', $userNomorUrut)
            ->where('status_persetujuan', 'diproses')->exists();

        // Tambahkan Pangkat, Gaji & Tunjangan
        $hasPendingPangkat = \DB::table('log_persetujuan_pangkatgajitunjangan')
            ->where('nomor_urut_pegawai', $userNomorUrut)
            ->where('status_persetujuan', 'diproses')->exists();

        // KUNCI: Gabungan untuk me-lock semua kartu di dashboard pegawai
        $isAnyPending = ($hasPendingCuti || $hasPendingLembur || $hasPendingPensiun || $hasPendingPangkat);

        // --- 2. Logika Berita (Tetap Sama) ---
        $daftar_berita = Berita::where('tanggal_posting', '>=', now()->subHours(72))
            ->orderBy('tanggal_posting', 'desc')
            ->paginate(5);

        $total_belum_dibaca = Berita::where('tanggal_posting', '>=', now()->subDay())->count();

        // --- 3. Return View (Kirim semua variabel ke dashboard pegawai) ---
        return view('pegawai.dashboard', compact(
            'daftar_berita',
            'hasPendingCuti',
            'hasPendingLembur',
            'hasPendingPensiun',
            'hasPendingPangkat', // <--- Variabel baru
            'isAnyPending',      // <--- Kunci utama buat lock kartu
            'total_belum_dibaca'
        ));
    }

}
