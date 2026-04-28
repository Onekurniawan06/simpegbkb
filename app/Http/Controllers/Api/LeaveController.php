<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengajuanCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'context' => 'required'
        ]);

        $context = $request->context;
        $leaves = [];

        if ($context == 'me') {
            $leaves = PengajuanCuti::where('nomor_urut_pegawai', $request->employee_id)->get();
        }

        return response()->json([
            'message' => 'List leave retrieved',
            'data' => $leaves
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'doc' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $docPath = null;

        if ($request->hasFile('doc')) {
            $docPath = $request->file('doc')->store('leave', 'public');
        }

        DB::beginTransaction();

        try {
            $leave = PengajuanCuti::create([
                'nomor_urut_pegawai' => $request->employee_id,
                'jenis_cuti' => $request->leave_type,
                'tanggal_mulai' => $request->start_date,
                'tanggal_selesai' => $request->end_date,
                'keterangan' => $request->description,
                'jalur_dokumen_pendukung' => $docPath,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Pengajuan cuti berhasil',
                'data' => $leave
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Pengajuan cuti gagal',
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
