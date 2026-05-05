<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|string',
            'token' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $deviceToken = DeviceToken::create([
                'nomor_urut_pegawai' => $request->employee_id,
                'token' => $request->token
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Simpan token berhasil',
                'data' => $deviceToken
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Simpan token gagal',
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
