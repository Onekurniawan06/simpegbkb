<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Sesuaikan dengan nama model Anda, mungkin Pegawai

class ChangePasswordController extends Controller
{
    public function showChangePasswordForm()
{
    $user = Auth::user();
    $pageTitle = 'Ubah Kata Sandi';

    // Ambil langsung file layout dari logic di Model User
    $layoutFile = $user->layout_file;

    $breadcrumbs = [
        'Beranda' => route('password.change'),
        $pageTitle => null
    ];

    return view('change-password', compact('user', 'pageTitle', 'breadcrumbs', 'layoutFile'));
}


    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Verifikasi password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        // Update password baru
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('status', 'Password berhasil diubah!');
    }
}
