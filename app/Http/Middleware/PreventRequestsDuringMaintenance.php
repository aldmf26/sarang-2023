<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
        'home/absen',
        'home/absen/create',
        'home/absen/detailSum',
        'home/absen/exportDetail',
        'home/absen/detailAbsen',
        'home/absen/tabelAbsen',
        'home/absen/SaveAbsen',
        'home/absen/delete_absen',
        'home/absen/create_stgh_hari',
        'home/absen/tbh_baris',
        'home/absen/detail',
        'api/*',
    ];
}
