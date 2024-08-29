<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Lakukan sesuatu setelah request diproses

        // activity()
        //     ->causedBy(auth()->user())
        //     ->log('Melakukan aksi ' . $request->method() . ' pada ' . $request->url());

        return $response;
    }
}
