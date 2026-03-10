<?php

namespace App\Http\Controllers\KepalaSKKMR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Berita;

class SkkmrDashboard extends Controller
{
    public function FormDashboarSkkmr()
    {
        $totalMenunggu = 0;
        $totalDisetujui = 0;
        $totalDitolak = 0;

        $detailMenunggu = [];
        $detailDisetujui = [];
        $detailDitolak = [];

        if (auth()->user()->level_akses === 'kepala skkmr') {

            $tables = [
                ['name' => 'log_persetujuan_lembur', 'col' => 'status_persetujuan', 'label' => 'Lembur'],
                ['name' => 'log_persetujuan_pensiun', 'col' => 'status_persetujuan', 'label' => 'Pensiun'],
                ['name' => 'log_persetujuan_pangkatgajitunjangan', 'col' => 'status_persetujuan', 'label' => 'Pangkat/Gaji'],
                ['name' => 'log_persetujuan_cuti', 'col' => 'status_pengajuan', 'label' => 'Cuti'],
            ];

            foreach ($tables as $table) {
                // Base query dengan filter level_akses: pegawai DAN manager
                $baseQuery = \DB::table($table['name'])
                    ->join('users', $table['name'] . '.nomor_urut_pegawai', '=', 'users.nomor_urut_pegawai')
                    ->whereIn('users.level_akses', ['pegawai', 'manager']); // Mengambil data dari kedua role

                // 1. Menunggu (Filter Level + Status Diproses)
                $countWait = (clone $baseQuery)
                    ->where($table['col'], 'diproses')
                    ->count();

                // 2. Disetujui (Filter Level + Status Disetujui + Bukan Pengajuan Awal)
                $countApprove = (clone $baseQuery)
                    ->where($table['col'], 'disetujui')
                    ->where('tahap_persetujuan', '!=', 'Pengajuan Awal')
                    ->count();

                // 3. Ditolak (Filter Level + Status Ditolak + Bukan Pengajuan Awal)
                $countReject = (clone $baseQuery)
                    ->where($table['col'], 'ditolak')
                    ->where('tahap_persetujuan', '!=', 'Pengajuan Awal')
                    ->count();

                // Akumulasi total
                $totalMenunggu += $countWait;
                $totalDisetujui += $countApprove;
                $totalDitolak += $countReject;

                // Masukkan ke detail per kategori
                $detailMenunggu[] = ['label' => $table['label'], 'jumlah' => $countWait];
                $detailDisetujui[] = ['label' => $table['label'], 'jumlah' => $countApprove];
                $detailDitolak[] = ['label' => $table['label'], 'jumlah' => $countReject];
            }
        }

        $daftar_berita = Berita::where('tanggal_posting', '>=', now()->subHours(72))
            ->orderBy('tanggal_posting', 'desc')
            ->paginate(5);

        $total_belum_dibaca = Berita::where('tanggal_posting', '>=', now()->subDay())->count();

        return view('skkmr.dashboardskkmr', compact(
            'totalMenunggu',
            'totalDisetujui',
            'totalDitolak',
            'detailMenunggu',
            'detailDisetujui',
            'detailDitolak',
            'daftar_berita',
            'total_belum_dibaca'
        ));
    }


}
