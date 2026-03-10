<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengajuanCuti;
use Illuminate\Http\Request;

class PengajuanCutiApiController extends Controller
{
    // Fungsi Anda tetap sama
    public function getTrackingHtml($nomor_pengajuan)
    {
        $pengajuan = PengajuanCuti::with(['logs.user', 'pegawai'])->findOrFail($nomor_pengajuan);

        return view('partials.tracking_status_content', [
            'pengajuan' => $pengajuan,
        ])->render();
    }
}
