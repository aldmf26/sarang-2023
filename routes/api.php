<?php

use App\Http\Controllers\ApiBkController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::controller(ApiBkController::class)
    ->prefix('apibk')
    ->name('apibk.')
    ->group(function () {
        Route::get('/sarang', 'sarang')->name('sarang');
        Route::get('/export_sarang', 'export_sarang')->name('export_sarang');
        Route::get('/cabut_export', 'cabut_export')->name('cabut_export');
        Route::get('/bk_sum', 'bk_sum')->name('bk_sum');
        Route::get('/sarang_sum', 'sarang_sum')->name('sarang_sum');
        Route::get('/show_box', 'show_box')->name('show_box');
        Route::get('/cabut_perbox', 'cabut_perbox')->name('cabut_perbox');
        Route::get('/datacabutsum2', 'datacabutsum2')->name('datacabutsum2');

        Route::get('/bk_sum_sortir', 'bk_sum_sortir')->name('bk_sum_sortir');
        Route::get('/datasortirsum', 'datasortirsum')->name('datasortirsum');
    });
