<?php

namespace App\Http\Controllers\KepalaSKKMR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Berita;
use Illuminate\Support\Facades\DB;

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

        $user = auth()->user();
        $namaJabatan = DB::table('jabatan')->where('jabatan_id', $user->jabatan_id)->value('nama_jabatan') ?? '';
        $isSKK = str_contains(strtolower($namaJabatan), 'skk') || str_contains(strtolower($namaJabatan), 'kepatuhan');

        if ($isSKK) {

            $tables = [
                ['name' => 'log_persetujuan_lembur', 'col' => 'status_persetujuan', 'label' => 'Lembur'],
                ['name' => 'log_persetujuan_pensiun', 'col' => 'status_persetujuan', 'label' => 'Pensiun'],
                ['name' => 'log_persetujuan_pangkatgajitunjangan', 'col' => 'status_persetujuan', 'label' => 'Pangkat/Gaji'],
                ['name' => 'log_persetujuan_cuti', 'col' => 'status_pengajuan', 'label' => 'Cuti'],
            ];

            foreach ($tables as $table) {
                // Base query TANPA join tabel users yang mengikat level_akses statis
                $baseQuery = DB::table($table['name'] . ' as log')
                    ->join('pegawai as p', 'log.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
                    ->join('pekerjaan as pek', 'p.nomor_urut_pegawai', '=', 'pek.nomor_urut_pegawai');

                // 1. Menunggu (Status Diproses)
                $countWait = (clone $baseQuery)
                    ->where('log.' . $table['col'], 'diproses')
                    ->count();

                // 2. Disetujui (Status Disetujui + Bukan Pengajuan Awal)
                $countApprove = (clone $baseQuery)
                    ->where('log.' . $table['col'], 'disetujui')
                    ->where('log.tahap_persetujuan', '!=', 'Pengajuan Awal')
                    ->count();

                // 3. Ditolak (Status Ditolak + Bukan Pengajuan Awal)
                $countReject = (clone $baseQuery)
                    ->where('log.' . $table['col'], 'ditolak')
                    ->where('log.tahap_persetujuan', '!=', 'Pengajuan Awal')
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
