<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CekPosisiUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // $allowedRoutesForPosition1 = [];
        // $posisi_id = auth()->user()->posisi_id /* peroleh posisi ID dari pengguna, misalnya: Auth::user()->posisi_id */;


        // // Mengambil array rute dari ID navbar 14 jika posisi ID adalah 1
        // if ($posisi_id === 1) {
        //     return $next($request);
        // } 
        // $navbar14 = DB::table('navbar')->where('id_navbar', 14)->first();
        // if ($navbar14) {
        //     $string = $navbar14->isi;
        //     $allowedRoutesForPosition1 = json_decode($string, true);
        // }
        // // Pengecekan jika rute yang diakses adalah salah satu dari rute yang diizinkan
        // if (!in_array($request->route()->getName(), $allowedRoutesForPosition1)) {
        //     return abort('404');
        // }

        return $next($request);
    }
}
