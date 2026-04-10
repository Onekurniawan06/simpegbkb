<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show($id)
    {
        $employee = Employee::from('pegawai as p')
            ->join('detail_pribadi as d', 'd.nomor_urut_pegawai', '=', 'p.nomor_urut_pegawai')
            ->where('p.nomor_urut_pegawai', $id)
            ->select([
                'p.nomor_urut_pegawai',
                'p.nama',
                'd.tempat_lahir',
                'd.tanggal_lahir'
            ])
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
