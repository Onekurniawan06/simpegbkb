<?php

// app/Providers/AppServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router; // Pastikan ini ada
use Illuminate\Support\Facades\View;
use App\Models\Berita; // Pastikan model Berita sudah dibuat
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    // ... (fungsi register dan sisanya) ...

    /**
     * Bootstrap any application services.
     */
    public function boot(Router $router): void // Pastikan ada (Router $router)
    {
        Carbon::setLocale('id');
        Paginator::useTailwind();
        // Daftarkan alias 'no_back_button' yang menunjuk ke class middleware Anda
        $router->aliasMiddleware('no_back_button', \App\Http\Middleware\PreventBackButton::class);

        // Mengirimkan data hanya ke file app-pegawai
        View::composer('layouts.app-pegawai', function ($view) {
        // --- Perbaikan di sini ---
        // Ganti 'created_at' dengan 'tanggal_posting' sesuai struktur tabel
        $count = Berita::where('tanggal_posting', '>=', Carbon::now()->subDay())->count();

        $latest = Berita::latest('tanggal_posting')->take(5)->get();
        // ------------------------

        $view->with([
            'newNewsCount' => $count,
            'latestNews' => $latest
        ]);
    });
    }
}

