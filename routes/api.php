<?php

use App\Http\Controllers\Api\DataPegawaiController;
use App\Http\Controllers\Api\HasapController;
use App\Http\Controllers\ApiBkController;
use App\Http\Controllers\OpnameController;
use App\Http\Resources\DataPegawaiCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

Route::get('/data-pegawai', [DataPegawaiController::class, 'index']);

Route::controller(HasapController::class)
    ->prefix('apihasap')
    ->name('apihasap.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/detail/{id_pengawas}/{tgl}', 'detail')->name('detail');
        Route::get('/bk', 'bk')->name('bk');
        Route::get('/cabut', 'cabut')->name('cabut');
        Route::get('/cabut_detail', 'cabut_detail')->name('cabut_detail');
        Route::get('/cetak', 'cetak')->name('cetak');
        Route::get('/cetak_detail', 'cetak_detail')->name('cetak_detail');
        Route::get('/grading', 'grading')->name('grading');
        Route::get('/grading_detail', 'grading_detail')->name('grading_detail');
        Route::get('/pengiriman_akhir', 'pengiriman_akhir')->name('pengiriman_akhir');
        Route::get('/pengiriman_akhir_detail', 'pengiriman_akhir_detail')->name('pengiriman_akhir_detail');
        Route::get('/bk_awal', 'bk_awal')->name('bk_awal');
        Route::get('/cabutbulan', 'cabutbulan')->name('cabutbulan');
        Route::get('/produkrelease', 'produkrelease')->name('produkrelease');
        Route::get('/monitoringProdukJadi', 'monitoringProdukJadi')->name('monitoringProdukJadi');
        Route::get('/kontrolPengemasan', 'kontrolPengemasan')->name('kontrolPengemasan');
        Route::get('/buktiPermintaan', 'buktiPermintaan')->name('buktiPermintaan');
        Route::get('/detailBuktiPermintaan', 'detailBuktiPermintaan')->name('detailBuktiPermintaan');
        Route::get('/ttlgrading', 'ttlgrading')->name('ttlgrading');
        Route::get('/ttlgrading_detail', 'ttlgrading_detail')->name('ttlgrading_detail');
        Route::get('/stok_grade', 'stok_grade')->name('stok_grade');
        Route::get('/stok_grade_detail', 'stok_grade_detail')->name('stok_grade_detail');
        Route::get('/first_tracebelity', 'first_tracebelity')->name('first_tracebelity');
        Route::get('/first_tracebelity2', 'first_tracebelity2')->name('first_tracebelity2');
        Route::get('/delivery', 'delivery')->name('delivery');
        Route::get('/delivery_detail', 'delivery_detail')->name('delivery_detail');
        Route::get('/tb_anak', 'tb_anak')->name('tb_anak');
        Route::get('/no_box', 'no_box')->name('no_box');
        Route::get('/detail_box', 'detail_box')->name('detail_box');
        Route::get('/pengiriman_akhir_detail_group_grade', 'pengiriman_akhir_detail_group_grade')->name('pengiriman_akhir_detail_group_grade');
        Route::get('/stok_produk_jadi', 'stok_produk_jadi')->name('stok_produk_jadi');
        Route::get('/stok_produk_jadi_detail', 'stok_produk_jadi_detail')->name('stok_produk_jadi_detail');
        Route::get('/cabut_detail_pengeringan', 'cabut_detail_pengeringan')->name('cabut_detail_pengeringan');
        Route::get('/cabut_pengeringan', 'cabut_pengeringan')->name('cabut_pengeringan');
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
        Route::get('/datacabutsum3', 'datacabutsum3')->name('datacabutsum3');

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
        Route::get('/cetak_pgws', 'cetak_pgws')->name('cetak_pgws');
        Route::get('/cetak_belum_selesai', 'cetak_belum_selesai')->name('cetak_belum_selesai');
        Route::get('/cetak_laporan', 'cetak_laporan')->name('cetak_laporan');
        Route::get('/grading_bj', 'grading_bj')->name('grading_bj');
        Route::get('/sum_partai', 'sum_partai')->name('sum_partai');

        Route::post('edit_bk', function (Request $b) {
            $partai = $b->partai;
            $harga = $b->harga;
            DB::table('bk')->where('nm_partai', $partai)->where('kategori', 'cabut')->update(['hrga_satuan' => $harga]);
        });
    });



require __DIR__ . '/apiAldi.php';
