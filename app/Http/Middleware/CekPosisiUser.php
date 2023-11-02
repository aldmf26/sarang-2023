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
        // $id_user = auth()->user()->id;
        // $routeName = $request->route()->getName();
        
        // $routeNamePotong = strstr($routeName, '.', true); // dipotong setelah . nya
        // $routeYgBulih = [
        //     'cabut.rekap'
        // ];
        // $data = DB::table('sub_navbar as a')
        //     ->join('permission_navbar as b', 'a.id_sub_navbar', 'b.id_sub_navbar')
        //     ->where([['b.id_user', $id_user], ['a.route', 'LIKE', "%$routeNamePotong%"]])
        //     ->first();

        // if ($data || in_array($routeName, $routeYgBulih)) {
        //     return $next($request);
        // }
            return $next($request);

        // return abort('404');
    }
}
