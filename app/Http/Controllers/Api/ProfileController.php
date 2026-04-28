<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show($id)
    {
        $employee = User::select('users.*')
            ->where('nomor_urut_pegawai', $id)
            ->with('detailPribadi')
            ->with('divisi')
            ->with('jabatan')
            ->first();

        if (!$employee) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'message' => 'Profile data retrieved',
            'data' => $employee
        ]);
    }
}
