<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function today(Request $request)
    {
        $employee_id = $request->employee_id;

        $now = now();
        $shiftDate = $now->toDateString();

        $attendance = Attendance::where('employee_id', $employee_id)
            ->where('shift_date', $shiftDate)
            ->first();

        if (!$attendance) {
            return response()->json([
                'message' => 'Today attendance retrieved',
                'data' => [
                    'shift_date' => $shiftDate,
                    'check_in' => null,
                    'check_out' => null,
                ]
            ]);
        }

        return response()->json([
            'message' => 'Today attendance retrieved',
            'data' => $attendance
        ]);
    }

    public function index(Request $request)
    {
        $attendances = Attendance::where('employee_id', $request->employee_id)->get();

        return response()->json([
            'message' => 'List attendance retrieved',
            'data' => $attendances
        ]);
    }

    public function show(Request $request, String $id)
    {
        $attendance = Attendance::find($id);

        return response()->json([
            'message' => 'Data attendance retrieved',
            'data' => $attendance
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required|image|max:2048',
        ]);

        $employee_id = $request->employee_id;
        $latitude = $request->latitude;
        $longitude = $request->longitude;

        $now = now();
        $shiftDate = $now->toDateString();

        try {
            $result = DB::transaction(function () use ($employee_id, $shiftDate, $now, $request, $latitude, $longitude) {

                $attendance = Attendance::lockForUpdate()
                    ->where('employee_id', $employee_id)
                    ->where('shift_date', $shiftDate)
                    ->first();

                $photoPath = $request->file('photo')->store('attendance', 'public');

                if (!$attendance) {
                    $attendance = Attendance::create([
                        'employee_id' => $employee_id,
                        'shift_date' => $shiftDate,
                        'check_in' => $now,
                        'check_in_lat' => $latitude,
                        'check_in_lng' => $longitude,
                        'check_in_photo' => $photoPath,
                    ]);

                    return [
                        'status' => 'check_in',
                        'data' => $attendance
                    ];
                }

                if (!$attendance->check_out) {
                    $attendance->update([
                        'check_out' => $now,
                        'check_out_lat' => $latitude,
                        'check_out_lng' => $longitude,
                        'check_out_photo' => $photoPath,
                    ]);

                    return [
                        'status' => 'check_out',
                        'data' => $attendance
                    ];
                }

                return [
                    'status' => 'completed',
                    'data' => $attendance
                ];
            });

            $message = match ($result['status']) {
                'check_in' => 'Check-in berhasil',
                'check_out' => 'Check-out berhasil',
                'completed' => 'Absensi telah dilakukan untuk hari ini',
            };

            return response()->json([
                'message' => $message,
                'data' => $result['data']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
