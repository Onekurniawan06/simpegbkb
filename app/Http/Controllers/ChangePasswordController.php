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
        // Mendapatkan data user yang sedang login
        $user = Auth::user();

        // Menggunakan nama variabel yang konsisten: $pageTitle
        $pageTitle = 'Ubah Kata Sandi'; // Baris ini diubah dari $pageTitleChangePassword

        // Anda bisa tambahkan variabel lain jika perlu, misal
        $breadcrumbs = [
            'Beranda' => route(name: 'password.change'),
            $pageTitle => null // Menggunakan $pageTitle di sini
        ];

        // Ubah baris ini:
        return view(view: 'pegawai.change-password', data: compact('user', 'pageTitle', 'breadcrumbs'));

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
