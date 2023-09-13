<?php

use App\Http\Controllers\BkController;
use App\Http\Controllers\CabutController;
use App\Http\Controllers\DataPengawasController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\PengawasController;
use App\Http\Controllers\SortirController;
use App\Http\Controllers\AksesController;
use App\Http\Controllers\DendaController;
use App\Http\Controllers\EoController;
use App\Http\Controllers\HariandllController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/403', function () {
        view('error.403');
    })->name('403');

    Route::controller(UserController::class)
        ->prefix('data_master/user')
        ->name('user.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'create')->name('create');
            Route::get('/edit', 'edit')->name('edit');
            Route::post('/edit', 'update')->name('update');
            Route::get('/delete', 'delete')->name('delete');
        });
    Route::controller(PengawasController::class)
        ->prefix('data_master/pengawas')
        ->name('pengawas.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/anak', 'anak')->name('anak');
            Route::get('/anak/{id}', 'anak_detail')->name('anak_detail');
            // Route::get('/anak/destroy/{id}', 'destroy_anak')->name('destroy_anak');
            Route::post('/anak', 'create_anak')->name('create_anak');
            Route::post('/anak/update', 'update_anak')->name('update_anak');
            Route::post('/', 'create')->name('create');
            Route::get('/edit', 'edit')->name('edit');
            Route::post('/edit', 'update')->name('update');
            Route::get('/delete', 'delete')->name('delete');
        });
    Route::controller(AksesController::class)
        ->prefix('akses')
        ->name('akses.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/navbar', 'detail_edit')->name('navbar');
            Route::get('/{id}', 'detail')->name('detail');
            Route::get('/{id}', 'navbar_delete')->name('navbar_delete');
            Route::get('/detail/{id}', 'detail_get')->name('detail_get');
            Route::post('/', 'save')->name('save');
            Route::post('/add_menu', 'addMenu')->name('add_menu');
            Route::post('/edit_menu', 'editMenu')->name('edit_menu');
        });

    Route::controller(UserController::class)
        ->prefix('data_master/user')
        ->name('user.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/pengawas', 'index')->name('pengawas');
            Route::post('/', 'create')->name('create');
            Route::get('/edit', 'edit')->name('edit');
            Route::post('/edit', 'update')->name('update');
            Route::get('/delete', 'delete')->name('delete');
        });
    Route::controller(GudangController::class)
        ->prefix('home/gudang')
        ->name('gudang.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
        });
    Route::controller(BkController::class)
        ->prefix('home/bk')
        ->name('bk.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/add', 'add')->name('add');
            Route::post('/', 'create')->name('create');
            Route::get('/edit', 'edit')->name('edit');
            Route::post('/edit', 'update')->name('update');
            Route::get('/delete', 'delete')->name('delete');
            Route::get('/print', 'print')->name('print');
            Route::get('/export', 'export')->name('export');
        });
    Route::controller(CabutController::class)
        ->prefix('home/cabut')
        ->name('cabut.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/add', 'add')->name('add');
            Route::get('/tbh_baris', 'tbh_baris')->name('tbh_baris');
            Route::get('/get_box_sinta', 'get_box_sinta')->name('get_box_sinta');
            Route::get('/get_kelas_anak', 'get_kelas_anak')->name('get_kelas_anak');
            Route::get('/load_anak', 'load_anak')->name('load_anak');
            Route::get('/add_delete_anak', 'add_delete_anak')->name('add_delete_anak');
            Route::get('/load_anak_nopengawas', 'load_anak_nopengawas')->name('load_anak_nopengawas');
            Route::get('/edit', 'edit')->name('edit');
            Route::get('/load_modal_akhir', 'load_modal_akhir')->name('load_modal_akhir');
            Route::get('/load_detail_cabut', 'load_detail_cabut')->name('load_detail_cabut');
            Route::get('/export', 'export')->name('export');
            Route::post('/input_akhir', 'input_akhir')->name('input_akhir');
            Route::post('/create_anak', 'create_anak')->name('create_anak');
            Route::post('/selesai_cabut', 'selesai_cabut')->name('selesai_cabut');
            Route::post('/', 'create')->name('create');
        });
    Route::controller(SortirController::class)
        ->prefix('home/sortir')
        ->name('sortir.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/add', 'add')->name('add');
            Route::post('/create', 'create')->name('create');
            Route::get('/tbh_baris', 'tbh_baris')->name('tbh_baris');
            Route::get('/get_box_sinta', 'get_box_sinta')->name('get_box_sinta');
            Route::get('/load_modal_akhir', 'load_modal_akhir')->name('load_modal_akhir');
            Route::post('/input_akhir', 'input_akhir')->name('input_akhir');
            Route::post('/create_anak', 'create_anak')->name('create_anak');
            Route::get('/load_anak', 'load_anak')->name('load_anak');
            Route::get('/load_anak_nopengawas', 'load_anak_nopengawas')->name('load_anak_nopengawas');
            Route::get('/add_delete_anak', 'add_delete_anak')->name('add_delete_anak');
            Route::get('/selesai_cabut', 'selesai_cabut')->name('selesai_cabut');
            Route::get('/export', 'export')->name('export');
        });
    Route::controller(EoController::class)
        ->prefix('home/eo')
        ->name('eo.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/add', 'add')->name('add');
            Route::get('/tbh_baris', 'tbh_baris')->name('tbh_baris');
            Route::get('/export', 'export')->name('export');
            Route::post('/create', 'create')->name('create');
            Route::get('/load_modal_akhir', 'load_modal_akhir')->name('load_modal_akhir');
            Route::post('/input_akhir', 'input_akhir')->name('input_akhir');
            Route::get('/selesai', 'selesai')->name('selesai');
            Route::get('/load_anak', 'load_anak')->name('load_anak');
            Route::get('/load_anak_nopengawas', 'load_anak_nopengawas')->name('load_anak_nopengawas');
            Route::get('/add_delete_anak', 'add_delete_anak')->name('add_delete_anak');
        });
    Route::controller(DataPengawasController::class)
        ->prefix('data_master/data_pengawas')
        ->name('data_pengawas.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/add', 'add')->name('add');
            Route::post('/', 'create')->name('create');
            Route::get('/edit', 'edit')->name('edit');
            Route::post('/edit', 'update')->name('update');
            Route::get('/delete', 'delete')->name('delete');
        });
    Route::controller(DendaController::class)
        ->prefix('data_master/denda')
        ->name('denda.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/detail/{id_denda}', 'detail')->name('detail');
            Route::get('/add', 'add')->name('add');
            Route::post('/', 'create')->name('create');
            Route::get('/edit', 'edit')->name('edit');
            Route::post('/edit', 'update')->name('update');
            Route::get('/delete', 'delete')->name('delete');
            Route::get('/print', 'print')->name('print');
        });
    Route::controller(HariandllController::class)
        ->prefix('home/hariandll')
        ->name('hariandll.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/create', 'create')->name('create');
            Route::get('/tbh_baris', 'tbh_baris')->name('tbh_baris');
            Route::get('/edit', 'edit')->name('edit');
            Route::get('/delete', 'delete')->name('delete');
            Route::get('/export', 'export')->name('export');
            Route::post('/update', 'update')->name('update');
            Route::get('/edit_load/{id}', 'edit_load')->name('edit_load');
        });
    Route::controller(KelasController::class)
        ->prefix('data_master/kelas')
        ->name('kelas.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/eo', 'index')->name('eo');
            Route::get('/sortir', 'index')->name('sortir');
            Route::get('/delete', 'delete')->name('delete');
            Route::post('/update', 'update')->name('update');
            Route::post('/create', 'create')->name('create');
        });
});
