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
use App\Http\Controllers\GlobalController;
use App\Http\Controllers\HariandllController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'cekPosisi'])->group(function () {
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
            Route::get('/anak/destroy/{id}', 'destroy_anak')->name('destroy_anak');
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
            Route::get('/load_select', 'load_select')->name('load_select');
            Route::get('/create_select', 'create_select')->name('create_select');
            Route::post('/', 'create')->name('create');
            Route::get('/edit', 'edit')->name('edit');
            Route::post('/edit', 'update')->name('update');
            Route::post('/delete', 'delete')->name('delete');
            Route::post('/selesai', 'selesai')->name('selesai');
            Route::get('/print', 'print')->name('print');
            Route::get('/export', 'export')->name('export');
            Route::get('/template', 'template')->name('template');
            Route::post('/import', 'import')->name('import');
            Route::get('/export', 'export')->name('export');
        });
    Route::controller(CabutController::class)
        ->prefix('home/cabut')
        ->name('cabut.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'create')->name('create');
            Route::get('/add', 'add')->name('add');
            Route::get('/load_halaman', 'load_halaman')->name('load_halaman');
            Route::get('/load_tambah_cabut', 'load_tambah_cabut')->name('load_tambah_cabut');
            Route::get('/load_tambah_anak', 'load_tambah_anak')->name('load_tambah_anak');
            Route::get('/createTambahAnakCabut', 'createTambahAnakCabut')->name('createTambahAnakCabut');
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
            Route::get('/export_global', 'export_global')->name('export_global');
            Route::get('/input_akhir', 'input_akhir')->name('input_akhir');
            Route::post('/create_anak', 'create_anak')->name('create_anak');
            Route::get('/selesai_cabut', 'selesai_cabut')->name('selesai_cabut');
            Route::get('/cabut_ok', 'cabut_ok')->name('cabut_ok');
            Route::get('/export_ibu', 'export_ibu')->name('export_ibu');
            Route::get('/rekap', 'rekap')->name('rekap');
            Route::get('/hapusCabutRow', 'hapusCabutRow')->name('hapusCabutRow');
            Route::get('/ditutup', 'ditutup')->name('ditutup');
            Route::get('/updateAnakBelum', 'updateAnakBelum')->name('updateAnakBelum');
            Route::get('/load_modal_anak_sisa', 'load_modal_anak_sisa')->name('load_modal_anak_sisa');
            Route::get('/get_kelas_jenis', 'get_kelas_jenis')->name('get_kelas_jenis');
            Route::get('/hapusAnakSisa', 'hapusAnakSisa')->name('hapusAnakSisa');
            Route::get('/cancel', 'cancel')->name('cancel');
            Route::get('/export_rekap', 'export_rekap')->name('export_rekap');
            Route::get('/export_sinta', 'export_sinta')->name('export_sinta');
        });
    Route::controller(GlobalController::class)
        ->prefix('home/global')
        ->name('global.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/cetak', 'cetak')->name('cetak');
            Route::get('/sortir', 'sortir')->name('sortir');
        });
    Route::controller(SortirController::class)
        ->prefix('home/sortir')
        ->name('sortir.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/add', 'add')->name('add');
            Route::get('/create', 'create')->name('create');
            Route::get('/cancel', 'cancel')->name('cancel');
            Route::get('/tbh_baris', 'tbh_baris')->name('tbh_baris');
            Route::get('/get_box_sinta', 'get_box_sinta')->name('get_box_sinta');
            Route::get('/load_modal_akhir', 'load_modal_akhir')->name('load_modal_akhir');
            Route::get('/input_akhir', 'input_akhir')->name('input_akhir');
            Route::post('/create_anak', 'create_anak')->name('create_anak');
            Route::get('/load_detail_sortir', 'load_detail_sortir')->name('load_detail_sortir');
            Route::get('/load_halaman', 'load_halaman')->name('load_halaman');
            Route::get('/selesai_sortir', 'selesai_sortir')->name('selesai_sortir');
            Route::get('/ditutup', 'ditutup')->name('ditutup');
            Route::get('/load_anak', 'load_anak')->name('load_anak');
            Route::get('/load_tambah_sortir', 'load_tambah_sortir')->name('load_tambah_sortir');
            Route::get('/load_tambah_anak', 'load_tambah_anak')->name('load_tambah_anak');
            Route::get('/load_tambah_anak', 'load_tambah_anak')->name('load_tambah_anak');
            Route::get('/createTambahAnakSortir', 'createTambahAnakSortir')->name('createTambahAnakSortir');
            Route::get('/hapusKerjaSortir', 'hapusKerjaSortir')->name('hapusKerjaSortir');
            Route::get('/load_anak_nopengawas', 'load_anak_nopengawas')->name('load_anak_nopengawas');
            Route::get('/add_delete_anak', 'add_delete_anak')->name('add_delete_anak');
            Route::get('/export', 'export')->name('export');
            Route::get('/rekap', 'rekap')->name('rekap');
            Route::get('/updateAnakBelum', 'updateAnakBelum')->name('updateAnakBelum');
            Route::get('/export_rekap', 'export_rekap')->name('export_rekap');
        });
    Route::controller(EoController::class)
        ->prefix('home/eo')
        ->name('eo.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/add', 'add')->name('add');
            Route::get('/tbh_baris', 'tbh_baris')->name('tbh_baris');
            Route::get('/export', 'export')->name('export');
            Route::get('/create', 'create')->name('create');
            Route::get('/cancel', 'cancel')->name('cancel');
            Route::get('/load_modal_akhir', 'load_modal_akhir')->name('load_modal_akhir');
            Route::post('/input_akhir', 'input_akhir')->name('input_akhir');
            Route::get('/selesai', 'selesai')->name('selesai');
            Route::get('/load_anak', 'load_anak')->name('load_anak');
            Route::get('/load_anak_nopengawas', 'load_anak_nopengawas')->name('load_anak_nopengawas');
            Route::get('/add_delete_anak', 'add_delete_anak')->name('add_delete_anak');
            Route::get('/rekap', 'rekap')->name('rekap');

            Route::get('/load_tambah_anak', 'load_tambah_anak')->name('load_tambah_anak');
            Route::get('/updateAnakBelum', 'updateAnakBelum')->name('updateAnakBelum');
            Route::get('/createTambahAnakCabut', 'createTambahAnakCabut')->name('createTambahAnakCabut');
            Route::get('/load_tambah_cabut', 'load_tambah_cabut')->name('load_tambah_cabut');
            Route::get('/get_box_sinta', 'get_box_sinta')->name('get_box_sinta');
            Route::get('/load_halaman', 'load_halaman')->name('load_halaman');
            Route::get('/hapusCabutRow', 'hapusCabutRow')->name('hapusCabutRow');
            Route::get('/input_akhir', 'input_akhir')->name('input_akhir');
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
            Route::get('/detail', 'detail')->name('detail');
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
            Route::get('/rekap', 'rekap')->name('rekap');
            Route::get('/export_rekap', 'export_rekap')->name('export_rekap');
            Route::post('/import', 'import')->name('import');
        });
    Route::controller(KelasController::class)
        ->prefix('data_master/kelas')
        ->name('kelas.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/eo', 'index')->name('eo');
            Route::get('/sortir', 'index')->name('sortir');
            Route::get('/delete', 'delete')->name('delete');
            Route::get('/deleteCabut', 'deleteCabut')->name('deleteCabut');
            Route::get('/spesial', 'spesial')->name('spesial');
            Route::get('/eo', 'eo')->name('eo');
            Route::get('/sortir', 'sortir')->name('sortir');
            Route::get('/info/{id_kelas}', 'info')->name('info');
            Route::post('/update', 'update')->name('update');
            Route::post('/create', 'create')->name('create');
            Route::post('/cabutCreate', 'cabutCreate')->name('cabutCreate');
            Route::post('/spesialCreate', 'spesialCreate')->name('spesialCreate');
            Route::get('/tambahPaketSelect2', 'tambahPaketSelect2')->name('tambahPaketSelect2');
            Route::get('/getTipe', 'getTipe')->name('getTipe');
            Route::post('/eoCreate', 'eoCreate')->name('eoCreate');
            Route::get('/cetak', 'cetak')->name('cetak');
            Route::post('/cetakCreate', 'cetakCreate')->name('cetakCreate');
            Route::post('/cetakSortir', 'cetakSortir')->name('cetakSortir');
            Route::get('/deleteSortir', 'deleteSortir')->name('deleteSortir');
        });
    Route::controller(PermissionController::class)
        ->prefix('data_master/permission')
        ->name('permission.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'create')->name('create');
        });
});
