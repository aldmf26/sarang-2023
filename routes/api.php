<?php

use App\Http\Controllers\ApiBkController;
use App\Http\Controllers\OpnameController;
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

Route::get('/blog', [OpnameController::class, 'blog']);
Route::get('/blog/{slug}', [OpnameController::class, 'blog_detail']);
Route::get('/blog/lainnya/{slug}', [OpnameController::class, 'blog_lainnya']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::controller(ApiBkController::class)
    ->prefix('apibk')
    ->name('apibk.')
    ->group(function () {
        Route::get('/sarang', 'sarang')->name('sarang');
        Route::get('/wipSortir', 'wipSortir')->name('wipSortir');
        Route::get('/export_sarang', 'export_sarang')->name('export_sarang');
        Route::get('/cabut_export', 'cabut_export')->name('cabut_export');
        Route::get('/bk_sum', 'bk_sum')->name('bk_sum');
        Route::get('/sarang_sum', 'sarang_sum')->name('sarang_sum');
        Route::get('/show_box', 'show_box')->name('show_box');
        Route::get('/cabut_perbox', 'cabut_perbox')->name('cabut_perbox');
        Route::get('/datacabutsum2', 'datacabutsum2')->name('datacabutsum2');

        Route::get('/bk_sum_sortir', 'bk_sum_sortir')->name('bk_sum_sortir');
        Route::get('/datasortirsum', 'datasortirsum')->name('datasortirsum');

        Route::get('/bk_sum_cetak', 'bk_sum_cetak')->name('bk_sum_cetak');
        Route::get('/datacetak', 'datacetak')->name('datacetak');

        Route::get('/bk_sum_all', 'bk_sum_all')->name('bk_sum_all');
        Route::get('/show_box_sortir', 'show_box_sortir')->name('show_box_sortir');
        Route::get('/cabut_selesai', 'cabut_selesai')->name('cabut_selesai');
        Route::get('/datacabutsum2backup', 'datacabutsum2backup')->name('datacabutsum2backup');
        Route::get('/cetak_detail', 'cetak_detail')->name('cetak_detail');
        Route::get('/cetak_detail_export', 'cetak_detail_export')->name('cetak_detail_export');
        Route::get('/bikin_box', 'bikin_box')->name('bikin_box');
        Route::get('/cabut_selesai_new', 'cabut_selesai_new')->name('cabut_selesai_new');
        Route::get('/cabut_laporan', 'cabut_laporan')->name('cabut_laporan');
        Route::get('/cetak_laporan_all', 'cetak_laporan_all')->name('cetak_laporan_all');
        Route::get('/cabut_detail', 'cabut_detail')->name('cabut_detail');
        Route::get('/cabut_selesai_g_cetak', 'cabut_selesai_g_cetak')->name('cabut_selesai_g_cetak');
        Route::get('/cabut_selesai_g_cetak_nota', 'cabut_selesai_g_cetak_nota')->name('cabut_selesai_g_cetak_nota');
    });

require __DIR__ . '/apiAldi.php';
