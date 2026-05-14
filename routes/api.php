<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/device-tokens', [DeviceTokenController::class, 'store']);

    Route::get('/profiles/{id}', [ProfileController::class, 'show']);

    Route::get('/attendances/config', [AttendanceController::class, 'config']);
    Route::get('/attendances/today', [AttendanceController::class, 'today']);
    Route::get('/attendances', [AttendanceController::class, 'index']);
    Route::get('/attendances/{id}', [AttendanceController::class, 'show']);
    Route::post('/attendances', [AttendanceController::class, 'store']);

    Route::get('/leaves', [LeaveController::class, 'index']);
    Route::post('/leaves', [LeaveController::class, 'store']);
    Route::get('/leaves/balances', [LeaveController::class, 'balances']);
    Route::get('/leaves/{id}', [LeaveController::class, 'show']);
    Route::get('/leave-types', [LeaveController::class, 'leaveTypes']);

    Route::get('/approvals/manager', [LeaveController::class, 'managerApprovals']);
    Route::get('/approvals/manager/latest', [LeaveController::class, 'managerApprovalsLatest']);
    Route::get('/approvals/manager/summary', [LeaveController::class, 'managerApprovalsSummary']);
    Route::patch('/approvals/manager/{source}/{logId}', [LeaveController::class, 'managerUpdateApproval']);
});
