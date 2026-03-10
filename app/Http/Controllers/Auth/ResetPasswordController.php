<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Auth;

class ResetPasswordController extends Controller
    {
        use ResetsPasswords;
        /**
        * @var string
        */
    protected $redirectTo = '/'; // Ganti ini sesuai halaman utama Anda setelah login
        /**
        * @return void
        */
    public function __construct()
    {
        $this->middleware('guest');
    }
        /**
        * Get the guard to be used during password reset.
        * @return \Illuminate\Contracts\Auth\StatefulGuard
        */
    protected function guard()
    {
        return Auth::guard();
    }
}
