<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// use App\Models\LogPersetujuanLembur;
// use App\Models\LogPersetujuanPensiun;
// use App\Models\LogPersetujuanCuti;
// use App\Models\LogPersetujuanPangkatgajitunjangan;
use Illuminate\Support\Facades\Auth;
use App\Models\Berita;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMenunggu = 0;
        $totalDisetujui = 0;
        $totalDitolak = 0;

        // Pastikan pengecekan sesuai dengan value di database
        if (auth()->user()->level_akses === 'administrator') {

            $tables = [
                ['name' => 'log_persetujuan_lembur', 'col' => 'status_persetujuan'],
                ['name' => 'log_persetujuan_pensiun', 'col' => 'status_persetujuan'],
                ['name' => 'log_persetujuan_pangkatgajitunjangan', 'col' => 'status_persetujuan'],
                ['name' => 'log_persetujuan_cuti', 'col' => 'status_pengajuan'],
            ];

            foreach ($tables as $table) {
                $totalMenunggu += \DB::table($table['name'])->where($table['col'], 'diproses')->count();
                $totalDisetujui += \DB::table($table['name'])->where($table['col'], 'disetujui')->count();
                $totalDitolak += \DB::table($table['name'])->where($table['col'], 'ditolak')->count();
            }
        }

        // Mengambil data berita (maksimal 72 jam terakhir sesuai filter Anda)
        $daftar_berita = Berita::where('tanggal_posting', '>=', now()->subHours(72))
            ->orderBy('tanggal_posting', 'desc')
            ->paginate(5);

        // --- Tambahan Logika Notifikasi (Mencari berita dalam 24 jam terakhir) ---
        $total_belum_dibaca = Berita::where('tanggal_posting', '>=', now()->subDay())->count();
        // ------------------------------------------------------------------------

        // Menambahkan 'total_belum_dibaca' ke dalam compact
        return view('admin.dashboard', compact(
            'totalMenunggu', 
            'totalDisetujui', 
            'totalDitolak', 
            'daftar_berita', 
            'total_belum_dibaca'
        ));
    }

}
