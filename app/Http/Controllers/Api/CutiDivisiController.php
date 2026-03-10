<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengajuanCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\CarbonPeriod;

class CutiDivisiController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data user yang sedang login
        $user = Auth::user();

        // 2. CEK: Jika user tidak login (null), kembalikan array kosong agar tidak error
        if (!$user) {
            return response()->json([]);
        }

        // 3. AMBIL ID DIVISI (Baris ini penting, jangan dihapus lagi ya)
        $divisiId = $user->id_divisi;

        // 4. CEK: Jika user belum punya divisi, kembalikan array kosong
        if (!$divisiId) {
            return response()->json([]);
        }

        // 5. Ambil daftar cuti rekan SATU DIVISI yang sudah disetujui (Approved)
        $cutiList = PengajuanCuti::whereHas('pegawai', function ($query) use ($divisiId) {
                $query->where('id_divisi', $divisiId);
            })
            ->where('status', 'Approved') // Hanya hitung yang sudah disetujui
            ->where('id_pegawai', '!=', $user->id) // Kecualikan cuti milik user sendiri
            ->get(['tanggal_mulai', 'tanggal_selesai']);

        $takenDates = [];

        // 6. Masukkan semua tanggal dalam rentang cuti ke dalam array
        foreach ($cutiList as $cuti) {
            try {
                $period = CarbonPeriod::create($cuti->tanggal_mulai, $cuti->tanggal_selesai);
                foreach ($period as $date) {
                    // Format YYYY-MM-DD agar cocok dengan pengecekan JavaScript
                    $takenDates[] = $date->format('Y-m-d');
                }
            } catch (\Exception $e) {
                continue; // Jika ada format tanggal rusak, lewati saja
            }
        }

        // 7. Kembalikan array unik (tanpa duplikat) dan reset index-nya
        return response()->json(array_values(array_unique($takenDates)));
    }
}
