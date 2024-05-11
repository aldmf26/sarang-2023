<?php

use App\Http\Controllers\AbsenController;
use App\Http\Controllers\CabutSpecialController;
use App\Http\Controllers\CetakController;
use App\Http\Controllers\CetakNewController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GradingController;
use App\Http\Controllers\Laporan_layerController;
use App\Http\Controllers\NavbarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RekapanController;
use App\Http\Controllers\RekapGajiPeranakController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('login');
});

Route::get('/template1', function () {
    return view('template-notable');
})->name('template1');
Route::get('/template-chart', function () {
    return view('template-chart');
})->name('template1');
Route::get('/template2', function () {
    return redirect()->route('dashboard.index');
})->name('template2');


Route::get('/dashboard', function () {
    return redirect()->route('template1');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // 
    Route::controller(NavbarController::class)->group(function () {
        Route::get('/data_master', 'data_master')->name('data_master');
        Route::get('/home', 'home')->name('home');
    });
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    Route::controller(DashboardController::class)
        ->prefix('dashboard')
        ->name('dashboard.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/detail/{kategori}/{nobox}', 'detail')->name('detail');
        });

    Route::controller(CetakController::class)
        ->prefix('home/cetak')
        ->name('cetak.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/get_cetak', 'get_cetak')->name('get_cetak');
            Route::get('/load_anak_kerja_belum', 'load_anak_kerja_belum')->name('load_anak_kerja_belum');
            Route::get('/get_total_anak', 'getTotalAnak')->name('get_total_anak');
            Route::post('/save_kerja', 'save_kerja')->name('save_kerja');
            Route::get('/ambil_awal', 'ambil_awal')->name('ambil_awal');
            Route::get('/get_kelas', 'get_kelas')->name('get_kelas');
            Route::post('/add_target', 'add_target')->name('add_target');
            Route::get('/get_box', 'get_box')->name('get_box');
            Route::get('/input_akhir', 'input_akhir')->name('input_akhir');
            Route::post('/save_akhir', 'save_akhir')->name('save_akhir');
            Route::get('/load_row', 'load_row')->name('load_row');
            Route::post('/selesai_cetak', 'selesai_cetak')->name('selesai_cetak');
            Route::get('/delete_awal_cetak', 'delete_awal_cetak')->name('delete_awal_cetak');
            Route::get('/ditutup', 'ditutup')->name('ditutup');
            Route::get('/delete_cetak', 'delete_cetak')->name('delete_cetak');
            Route::post('/import', 'import')->name('import');
            Route::get('/export_gaji_global', 'export_gaji_global')->name('export_gaji_global');

            Route::get('/add', 'add')->name('add');
            Route::get('/akhir', 'akhir')->name('akhir');
            Route::post('/add_akhir', 'add_akhir')->name('add_akhir');
            Route::post('/selesai', 'selesai')->name('selesai');
            Route::get('/tbh_baris', 'tbh_baris')->name('tbh_baris');
            Route::get('/rekap', 'rekap')->name('rekap');
            Route::get('/export_rekap', 'export_rekap')->name('export_rekap');

            Route::get('/edit', 'edit')->name('edit');
            Route::post('/edit', 'update')->name('update');
            Route::get('/delete', 'delete')->name('delete');
            Route::get('/export', 'export')->name('export');
            Route::get('/rekap_harian', 'rekap_harian')->name('rekap_harian');
        });
    Route::controller(GradingController::class)
        ->prefix('home/grading')
        ->name('grading.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'add_target')->name('add_target');
            Route::post('/add_grading', 'add_grading')->name('add_grading');
            Route::get('/tbh_baris', 'tbh_baris')->name('tbh_baris');
            Route::get('/tbh_baris_turun', 'tbh_baris_turun')->name('tbh_baris_turun');
            Route::get('/tbh_baris_target', 'tbh_baris_target')->name('tbh_baris_target');
            Route::get('/load_grade', 'load_grade')->name('load_grade');
            Route::get('/load_detail_grading', 'load_detail_grading')->name('load_detail_grading');
        });
    Route::controller(CabutSpecialController::class)
        ->prefix('home/cabutSpesial')
        ->name('cabutSpesial.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/add', 'add')->name('add');
            Route::get('/getrp_target', 'getrp_target')->name('getrp_target');
            Route::get('/tbh_baris', 'tbh_baris')->name('tbh_baris');
            Route::post('/', 'create')->name('create');
            Route::get('/load_modal_akhir', 'load_modal_akhir')->name('load_modal_akhir');
            Route::get('/rekap', 'rekap')->name('rekap');
            Route::post('/input_akhir', 'input_akhir')->name('input_akhir');
            Route::get('/selesai_cabut', 'selesai_cabut')->name('selesai_cabut');
            Route::get('/save_absen', 'save_absen')->name('save_absen');

            Route::get('/load_anak_kerja', 'load_anak_kerja')->name('load_anak_kerja');
            Route::get('/load_anak_kerja_belum', 'load_anak_kerja_belum')->name('load_anak_kerja_belum');
            Route::get('/load_ambil_cbt', 'load_ambil_cbt')->name('load_ambil_cbt');
            Route::get('/delete_absen', 'delete_absen')->name('delete_absen');
            Route::get('/get_box', 'get_box')->name('get_box');
            Route::get('/load_cabut', 'load_cabut')->name('load_cabut');
            Route::get('/load_row', 'load_row')->name('load_row');
            Route::get('/ditutup', 'ditutup')->name('ditutup');
            Route::get('/load_detail_cabut', 'load_detail_cabut')->name('load_detail_cabut');
            Route::get('/export', 'export')->name('export');
        });
    Route::controller(AbsenController::class)
        ->prefix('home/absen')
        ->name('absen.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::get('/detailSum', 'detailSum')->name('detailSum');
            Route::get('/exportDetail/{bulan}/{tahun}/{id_pengawas}', 'exportDetail')->name('exportDetail');
            Route::get('/detailAbsen', 'detailAbsen')->name('detailAbsen');
            Route::get('/tabelAbsen', 'tabelAbsen')->name('tabelAbsen');
            Route::get('/SaveAbsen', 'SaveAbsen')->name('SaveAbsen');
            Route::get('/delete_absen', 'delete_absen')->name('delete_absen');
            Route::post('/create_stgh_hari', 'create_stgh_hari')->name('create_stgh_hari');
            Route::get('/tbh_baris', 'tbh_baris')->name('tbh_baris');
            Route::get('/detail/{id_anak}', 'detail')->name('detail');
        });
    Route::controller(RekapGajiPeranakController::class)
        ->prefix('home/rekapan')
        ->name('rekap.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/cetak', 'cetak')->name('cetak');
            Route::get('/sortir', 'sortir')->name('sortir');
            Route::get('/export', 'export')->name('export');
        });
    Route::controller(CetakNewController::class)
        ->prefix('home/cetaknew')
        ->name('cetaknew.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/get_cetak', 'get_cetak')->name('get_cetak');
            Route::get('/history', 'history')->name('history');
            Route::get('/history_detail', 'history_detail')->name('history_detail');
            Route::get('/load_tambah_data', 'load_tambah_data')->name('load_tambah_data');
            Route::get('/tambah_baris', 'tambah_baris')->name('tambah_baris');
            Route::post('/save_target', 'save_target')->name('save_target');
            Route::get('/save_akhir', 'save_akhir')->name('save_akhir');
            Route::get('/getRowData', 'getRowData')->name('getRowData');
            Route::get('/save_selesai', 'save_selesai')->name('save_selesai');
            Route::get('/cancel_selesai', 'cancel_selesai')->name('cancel_selesai');
            Route::get('/hapus_data', 'hapus_data')->name('hapus_data');
        });
    // Route::controller(RekapanController::class)
    //     ->prefix('home/rekapan')
    //     ->name('rekap.')
    //     ->group(function () {
    //         Route::get('/', 'index')->name('index');
    //         Route::get('/export', 'export')->name('export');
    //     });
});
