<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'message' => 'Email atau Password salah'
            ]);
        }

        $userAuth = Auth::user();

        $token = $userAuth->createToken('auth_token')->plainTextToken;

        $user = User::select('users.*')
            ->where('nomor_urut_pegawai', $userAuth->nomor_urut_pegawai)
            ->with('divisi')
            ->with('jabatan')
            ->with('level')
            ->first();

        return response()->json([
            'message' => 'Login berhasil',
            'data' => [
                'user' => $user,
                'token' => $token
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }

    public function me(Request $request)
    {
        $userFound = $request->user();

        $user = User::select('users.*')
            ->where('nomor_urut_pegawai', $userFound->nomor_urut_pegawai)
            ->with('divisi')
            ->with('jabatan')
            ->first();

        return response()->json([
            'message' => 'User data retrieved',
            'data' => $user
        ]);
    }
}
