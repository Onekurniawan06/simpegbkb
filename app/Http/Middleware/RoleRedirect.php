<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Cari mapping yang paling cocok berdasarkan priority
            $mapping = \DB::table('roles_mapping')
                ->where('level_id', $user->level_id)
                ->where(function($q) use ($user) {
                    $q->where('jabatan_id', $user->jabatan_id)
                    ->orWhereNull('jabatan_id');
                })
                ->where(function($q) use ($user) {
                    $q->where('id_divisi', $user->id_divisi)
                    ->orWhereNull('id_divisi');
                })
                ->orderBy('priority', 'asc')
                ->first();

            if ($mapping) {
                // Jika user mencoba mengakses halaman yang bukan jatahnya, arahkan ke route_name
                if ($request->route()->getName() != $mapping->route_name) {
                    // Tambahkan pengecekan agar tidak redirect loop
                    return redirect()->route($mapping->route_name);
                }
            }
        }

        return $next($request);
    }

}
