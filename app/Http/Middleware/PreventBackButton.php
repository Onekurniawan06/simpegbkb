<?php

// app/Http/Middleware/PreventBackButton.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventBackButton
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // 🛡️ Cek apakah response berupa file (BinaryFileResponse)
        if ($response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
        } else {
            // Untuk response halaman web biasa
            $response->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
                    ->header('Pragma', 'no-cache');
        }

        return $response;
    }

}
