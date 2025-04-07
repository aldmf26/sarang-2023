<?php

use App\Http\Controllers\AbsenController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\Bk_baruController;
use App\Http\Controllers\CabutDetailController;
use App\Http\Controllers\CabutSpecialController;
use App\Http\Controllers\CetakController;
use App\Http\Controllers\CetakNewController;
use App\Http\Controllers\CocokanController;
use App\Http\Controllers\CostGlobalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportCostController;
use App\Http\Controllers\GradingController;
use App\Http\Controllers\GudangSarangController;
use App\Http\Controllers\HccpController;
use App\Http\Controllers\hrga2_5JadwalGapAnalisisController;
use App\Http\Controllers\hrga2Controller;
use App\Http\Controllers\hrga2HasilWawancaraController;
use App\Http\Controllers\hrga3_1InformasiTawaranPelatihan;
use App\Http\Controllers\hrga3_2ProgramPelatihantahunan;
use App\Http\Controllers\hrga3_3UsulanController;
use App\Http\Controllers\hrga3_6EvaluasiPelatihanController;
use App\Http\Controllers\hrga4_1jadwalMedicalCheckupController;
use App\Http\Controllers\hrga4DataPegawaiController;
use App\Http\Controllers\hrga5_1ePerawatanSarana;
use App\Http\Controllers\hrga5_1PerawatanSaranaController;
use App\Http\Controllers\hrga5_2RiwayatPemeliharaanController;
use App\Http\Controllers\hrga5_3PermintaanPerbaikan;
use App\Http\Controllers\hrga8_1Program_peratawan;
use App\Http\Controllers\hrga8_2Ceklistperawatanmesin;
use App\Http\Controllers\hrga8_3Permintaan_perbaikan;
use App\Http\Controllers\importPerbaikanController;
use App\Http\Controllers\Laporan_akhir;
use App\Http\Controllers\Laporan_layerController;
use App\Http\Controllers\NavbarController;
use App\Http\Controllers\OpnameNewController;
use App\Http\Controllers\OpnameSusutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QcController;
use App\Http\Controllers\RekapanController;
use App\Http\Controllers\RekapGajiPeranakController;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\UangMakanController;
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

Route::get('/isiformulir/{id}', [hrga2HasilWawancaraController::class, 'form_isi'])->name('isiformulir');
Route::post('/save_formulir', [hrga2HasilWawancaraController::class, 'save_formulir'])->name('save_formulir');
Route::get('/berhasil', [hrga2HasilWawancaraController::class, 'berhasil'])->name('berhasil');



Route::middleware('auth')->group(function () {

    // 
    Route::controller(NavbarController::class)->group(function () {
        Route::get('/data_master', 'data_master')->name('data_master');
        Route::get('/home', 'home')->name('home');
        Route::get('/summary', 'summary')->name('summary');
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
            Route::get('/get_no_box', 'get_no_box')->name('get_no_box');
            Route::get('/history', 'history')->name('history');
            Route::get('/history_detail', 'history_detail')->name('history_detail');
            Route::get('/summary_detail', 'summary_detail')->name('summary_detail');
            Route::get('/load_tambah_data', 'load_tambah_data')->name('load_tambah_data');
            Route::get('/tambah_baris', 'tambah_baris')->name('tambah_baris');
            Route::post('/save_target', 'save_target')->name('save_target');
            Route::get('/save_akhir', 'save_akhir')->name('save_akhir');
            Route::get('/getRowData', 'getRowData')->name('getRowData');
            Route::get('/save_selesai', 'save_selesai')->name('save_selesai');
            Route::get('/cancel_selesai', 'cancel_selesai')->name('cancel_selesai');
            Route::get('/hapus_data', 'hapus_data')->name('hapus_data');
            Route::get('/capai', 'capai')->name('capai');
            Route::get('/export', 'export')->name('export');
            Route::get('/summary', 'summary')->name('summary');
            Route::get('/formulir', 'formulir')->name('formulir');
            Route::get('/get_paket_cetak', 'get_paket_cetak')->name('get_paket_cetak');
            Route::get('/print_slipgaji', 'print_slipgaji')->name('print_slipgaji');
            Route::post('/save_formulir', 'save_formulir')->name('save_formulir');
            Route::get('/formulir/{no_invoice}', 'formulir_print')->name('formulir_print');
            Route::get('/gudangcetak', 'gudangcetak')->name('gudangcetak');
            Route::get('/load_edit_invoice', 'load_edit_invoice')->name('load_edit_invoice');
            Route::get('/export_gudang', 'export_gudang')->name('export_gudang');
            Route::get('/selesai_po_sortir', 'selesai_po_sortir')->name('selesai_po_sortir');
            Route::post('/update_invoice', 'update_invoice')->name('update_invoice');
            Route::get('/template', 'template')->name('template');
            Route::post('/import', 'import')->name('import');
            Route::get('/export_gaji_global', 'export_gaji_global')->name('export_gaji_global');
        });
    Route::controller(Laporan_akhir::class)
        ->prefix('home/laporanakhir')
        ->name('laporanakhir.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/get_bk_akhir', 'get_bk_akhir')->name('get_bk_akhir');
            Route::get('/search', 'search')->name('search');
            Route::post('/save_bk_akhir', 'save_bk_akhir')->name('save_bk_akhir');
            Route::get('/get_detail_cetak', 'get_detail_cetak')->name('get_detail_cetak');
            Route::get('/detail', 'detail')->name('detail');
            Route::get('/export_partai/{nm_partai}', 'export_partai')->name('export_partai');
            Route::get('/get_detail_cabut', 'get_detail_cabut')->name('get_detail_cabut');
            Route::get('/summaryCetak', 'summaryCetak')->name('summaryCetak');
            Route::get('/get_detail_sortir', 'get_detail_sortir')->name('get_detail_sortir');
            Route::post('/save_oprasional', 'save_oprasional')->name('save_oprasional');
            Route::get('/get_detail', 'get_detail')->name('get_detail');
            Route::get('/lapPartai', 'lapPartai')->name('lapPartai');
            Route::post('/saveoprasional', 'saveoprasional')->name('saveoprasional');
        });
    Route::controller(GudangSarangController::class)
        ->prefix('home/gudangsarang')
        ->name('gudangsarang.')
        ->group(function () {
            Route::get('/', 'home')->name('home');
            Route::get('/gudang_cbt_selesai', 'index')->name('gudang_cbt_selesai');
            Route::get('/load_cabut_selesai', 'load_cabut_selesai')->name('load_cabut_selesai');
            Route::get('/get_formulir', 'get_formulir')->name('get_formulir');
            Route::post('/save_formulir', 'save_formulir')->name('save_formulir');
            Route::get('/print_formulir', 'print_formulir')->name('print_formulir');
            Route::get('/invoice', 'invoice')->name('invoice');
            Route::get('/cabut', 'cabut')->name('cabut');
            Route::get('/print_cabut', 'print_cabut')->name('print_cabut');
            Route::get('/get_formulircabut', 'get_formulircabut')->name('get_formulircabut');
            Route::get('/get_siap_cetak', 'get_siap_cetak')->name('get_siap_cetak');
            Route::get('/get_cetak_proses', 'get_cetak_proses')->name('get_cetak_proses');
            Route::get('/batal', 'batal')->name('batal');
            Route::get('/selesai', 'selesai')->name('selesai');
            Route::get('/load_edit_invoice', 'load_edit_invoice')->name('load_edit_invoice');
            Route::get('/load_edit_invoice_grade', 'load_edit_invoice_grade')->name('load_edit_invoice_grade');
            Route::post('/save_formulir_cabut', 'save_formulir_cabut')->name('save_formulir_cabut');
            Route::post('/update_invoice', 'update_invoice')->name('update_invoice');
            Route::post('/update_invoice_grade', 'update_invoice_grade')->name('update_invoice_grade');
            Route::get('/invoice_sortir', 'invoice_sortir')->name('invoice_sortir');
            Route::get('/invoice_grade', 'invoice_grade')->name('invoice_grade');
            Route::get('/invoice_wip', 'invoice_wip')->name('invoice_wip');
            Route::get('/invoice_grading', 'invoice_grading')->name('invoice_grading');
            Route::get('/invoice_qc', 'invoice_qc')->name('invoice_qc');
            Route::get('/invoice_wip2', 'invoice_wip2')->name('invoice_wip2');
            Route::get('/cancel_po_qc', 'cancel_po_qc')->name('cancel_po_qc');
            Route::get('/selesai_po_qc', 'selesai_po_qc')->name('selesai_po_qc');
            Route::get('/selesai_po_wip2', 'selesai_po_wip2')->name('selesai_po_wip2');
            Route::get('/print_po_qc', 'print_po_qc')->name('print_po_qc');
            Route::get('/print_po_wip2', 'print_po_wip2')->name('print_po_wip2');
            Route::get('/print_formulir_grade', 'print_formulir_grade')->name('print_formulir_grade');
            Route::get('/selesai_grade', 'selesai_grade')->name('selesai_grade');

            Route::get('/selesai_wip', 'selesai_wip')->name('selesai_wip');
            Route::get('/batal_wip', 'batal_wip')->name('batal_wip');
            Route::get('/print_formulir_wip', 'print_formulir_wip')->name('print_formulir_wip');

            Route::get('/selesai_grading', 'selesai_grading')->name('selesai_grading');
            Route::get('/batal_grading', 'batal_grading')->name('batal_grading');
            Route::get('/print_formulir_grading', 'print_formulir_grading')->name('print_formulir_grading');
        });
    // Route::controller(RekapanController::class)
    //     ->prefix('home/rekapan')
    //     ->name('rekap.')
    //     ->group(function () {
    //         Route::get('/', 'index')->name('index');
    //         Route::get('/export', 'export')->name('export');
    //     });
    Route::controller(importPerbaikanController::class)
        ->prefix('home/importperbaikan')
        ->name('importperbaikan.')
        ->group(function () {
            Route::post('/', 'importperbaikan')->name('index');
            Route::post('/importperbaikansortir', 'importperbaikansortir')->name('importperbaikansortir');
        });
    Route::controller(SummaryController::class)
        ->prefix('home/summary')
        ->name('summary.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/bk_sisa', 'bk_sisa')->name('bk_sisa');
            Route::get('/export_summary', 'export_summary')->name('export_summary');
            Route::get('/detail_partai', 'detail_partai')->name('detail_partai');
            Route::get('/detail_box', 'detail_box')->name('detail_box');
            Route::get('/history_box', 'history_box')->name('history_box');
            Route::get('/history_partai', 'history_partai')->name('history_partai');
            Route::get('/export2', 'export2')->name('export2');
            Route::get('/get_operasional', 'get_operasional')->name('get_operasional');
            Route::post('/saveoprasional', 'saveoprasional')->name('saveoprasional');
        });

    Route::controller(UangMakanController::class)
        ->prefix('data_master/uang_makan')
        ->name('uang_makan.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/tambah_uang_makan', 'tambah_uang_makan')->name('tambah_uang_makan');
            Route::get('/uang_makan/{id}', 'uang_makan_detail')->name('uang_makan_detail');
            Route::post('/update', 'update')->name('update');
        });
    Route::controller(CocokanController::class)
        ->prefix('home/cocokan')
        ->name('cocokan.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/cetak', 'cetak')->name('cetak');
            Route::get('/sortir', 'sortir')->name('sortir');
            Route::get('/grading', 'grading')->name('grading');
            Route::get('/wip1', 'wip1')->name('wip1');
            Route::get('/qc', 'qc')->name('qc');
            Route::get('/wip2', 'wip2')->name('wip2');
            Route::get('/pengiriman', 'pengiriman')->name('pengiriman');
            Route::get('/balancesheet', 'balancesheet')->name('balancesheet');
            Route::post('/tutup', 'tutup')->name('tutup');
            // Route::get('/opname', 'opname')->name('opname');
            Route::get('/list_pengiriman', 'list_pengiriman')->name('list_pengiriman');
        });
    Route::controller(OpnameNewController::class)
        ->prefix('home/opnamenew')
        ->name('opnamenew.')
        ->group(function () {
            Route::get('/cabut', 'index')->name('index');
            Route::get('/cetak', 'cetak')->name('cetak');
            Route::get('/sortir', 'sortir')->name('sortir');
            Route::get('/grading', 'grading')->name('grading');
            Route::get('/export', 'export')->name('export');
        });
    Route::controller(CabutDetailController::class)
        ->prefix('home/cabutdetail')
        ->name('cabutdetail.')
        ->group(function () {
            Route::get('/export', 'export')->name('export');
        });

    Route::controller(CabutDetailController::class)
        ->prefix('home/cocokan/detail')
        ->name('detail.')
        ->group(function () {
            Route::get('/cabut', 'cabut_cabutAwal')->name('cabut.cabut_awal');
            Route::get('/cabut/akhir', 'cabut_cabutAkhir')->name('cabut.cabut_akhir');
            Route::get('/cabut/proses', 'cabut_cabutProses')->name('cabut.proses');
            Route::get('/cabut/sisa', 'cabut_cabutSisa')->name('cabut.sisa');

            Route::get('/cetak', 'cetak_cetakAwal')->name('cetak.cetak_awal');
            Route::get('/cetak/akhir', 'cetak_cetakAkhir')->name('cetak.cetak_akhir');
            Route::get('/cetak/proses', 'cetak_cetakProses')->name('cetak.proses');
            Route::get('/cetak/sisa', 'cetak_cetakSisa')->name('cetak.sisa');

            Route::get('/sortir', 'sortir_sortirAwal')->name('sortir.sortir_awal');
            Route::get('/sortir/akhir', 'sortir_sortirAkhir')->name('sortir.sortir_akhir');
            Route::get('/sortir/proses', 'sortir_sortirProses')->name('sortir.proses');
            Route::get('/sortir/sisa', 'sortir_sortirSisa')->name('sortir.sisa');

            Route::get('/grading', 'gradingAwal')->name('grading.awal');
            Route::get('/grading/akhir', 'gradingAkhir')->name('grading.akhir');
            Route::get('/grading/sisa', 'gradingSisa')->name('grading.sisa');

            Route::get('/pengiriman', 'pengirimanAwal')->name('pengiriman.awal');
            Route::get('/pengiriman/sisa', 'pengirimanSisa')->name('pengiriman.sisa');

            Route::get('/list_pengiriman', 'list_pengiriman')->name('list_pengiriman');
        });

    Route::controller(Bk_baruController::class)
        ->prefix('home/bkbaru')
        ->name('bkbaru.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/add', 'add')->name('add');
            Route::get('/invoice', 'invoice')->name('invoice');
            Route::get('/print_formulir', 'print_formulir')->name('print_formulir');
            Route::get('/batal', 'batal')->name('batal');
            Route::get('/load_edit_invoice', 'load_edit_invoice')->name('load_edit_invoice');
            Route::get('/selesai', 'selesai')->name('selesai');
            Route::get('/edit', 'edit')->name('edit');


            Route::post('/create', 'create')->name('create');
            Route::post('/update_invoice', 'update_invoice')->name('update_invoice');
            Route::post('/save_formulir', 'save_formulir')->name('save_formulir');
            Route::post('/update', 'update')->name('update');
        });
    Route::controller(ExportCostController::class)
        ->prefix('home/exportcost')
        ->name('exportcost.')
        ->group(function () {
            Route::get('/export', 'export')->name('export');
        });
    Route::controller(OpnameSusutController::class)
        ->prefix('home/cocokan')
        ->name('cocokan.')
        ->group(function () {
            Route::get('/opname', 'index')->name('opname');
            Route::get('/detail_cabut', 'detail_cabut')->name('detail_cabut');
            Route::get('/costPartai', 'costPartai')->name('costPartai');
            Route::get('/getCostpartai', 'getCostpartai')->name('getCostpartai');
            Route::get('/detailGrade', 'detailGrade')->name('detailGrade');
            Route::get('/detailGrade2', 'detailGrade2')->name('detailGrade2');
            Route::get('/exportCostpartai', 'exportCostpartai')->name('exportCostpartai');
            Route::get('/exportCostperpartai', 'exportCostperpartai')->name('exportCostperpartai');

            Route::get('/opname/cetak', 'cetak')->name('opname/cetak');
            Route::get('/opname/sortir', 'sortir')->name('opname/sortir');
            Route::get('/getDetailCabutpartai', 'getDetailCabutpartai')->name('getDetailCabutpartai');
            Route::get('/getDetailCetakpartai', 'getDetailCetakpartai')->name('getDetailCetakpartai');
            Route::get('/getDetailSortirpartai', 'getDetailSortirpartai')->name('getDetailSortirpartai');
            Route::get('/getDetailbkpartai', 'getDetailbkpartai')->name('getDetailbkpartai');
        });

    Route::controller(BalanceController::class)
        ->prefix('home/cocokan/balancesheet/detail')
        ->name('cocokan.balance.')
        ->group(function () {
            Route::get('/', 'index')->name('gaji');
            Route::get('/cost', 'cost')->name('cost');
            Route::get('/CostGajiProses', 'CostGajiProses')->name('CostGajiProses');
            Route::get('/CostOperasionalBebanDigrading', 'CostOperasionalBebanDigrading')->name('CostOperasionalBebanDigrading');
        });
    Route::controller(HccpController::class)
        ->prefix('hccp')
        ->name('hccp.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/sampleAdministrator', 'sampleAdministrator')->name('sampleAdministrator');
            Route::get('/pemeliharaanBangunan', 'pemeliharaanBangunan')->name('pemeliharaanBangunan');
            Route::get('/perawatan_dan_perbaikan_mesin', 'perawatan_dan_perbaikan_mesin')->name('perawatan_dan_perbaikan_mesin');
        });
    Route::controller(hrga2HasilWawancaraController::class)
        ->prefix('hccp/hrga2')
        ->name('hrga2.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/getData', 'getData')->name('getData');
            Route::get('/create', 'create')->name('create');
            Route::get('/export/{id}', 'export')->name('export');
            Route::post('/store', 'store')->name('store');
            Route::get('/edit/{id}', 'edit')->name('edit');
            Route::post('update', 'update')->name('update');
            Route::get('/delete/{id}', 'delete')->name('delete');
            Route::get('/tambah_data', 'tambah_data')->name('tambah_data');
            Route::get('/form_isi/{id}', 'form_isi')->name('form_isi');
            // Route::post('/save_formulir', 'save_formulir')->name('save_formulir');
            // Route::get('/berhasil', 'berhasil')->name('berhasil');
        });
    Route::controller(hrga4DataPegawaiController::class)
        ->prefix('hccp/hrga4')
        ->name('hrga4.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/export', 'export')->name('export');
        });
    Route::controller(hrga2_5JadwalGapAnalisisController::class)
        ->prefix('hccp/hrga2_5')
        ->name('hrga2_5.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/save_jadwal', 'save_jadwal')->name('save_jadwal');
            Route::get('/print', 'print')->name('print');
        });
    Route::controller(hrga3_1InformasiTawaranPelatihan::class)
        ->prefix('hccp/hrga3_1')
        ->name('hrga3_1.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::get('/print', 'print')->name('print');
        });
    Route::controller(hrga3_2ProgramPelatihantahunan::class)
        ->prefix('hccp/hrga3_2')
        ->name('hrga3_2.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::get('/print', 'print')->name('print');
        });
    Route::controller(hrga3_3UsulanController::class)
        ->prefix('hccp/hrga3_3')
        ->name('hrga3_3.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::get('/print', 'print')->name('print');
        });
    Route::controller(hrga3_6EvaluasiPelatihanController::class)
        ->prefix('hccp/hrga3_6')
        ->name('hrga3_6.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/print', 'print')->name('print');
        });
    Route::controller(hrga4_1jadwalMedicalCheckupController::class)
        ->prefix('hccp/hrga4_1')
        ->name('hrga4_1.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/store', 'Store')->name('store');
            Route::get('/print', 'print')->name('print');
        });
    Route::controller(hrga5_1PerawatanSaranaController::class)
        ->prefix('hccp/hrga5_1')
        ->name('hrga5_1.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/print', 'print')->name('print');
            Route::post('/store', 'store')->name('store');
        });
    Route::controller(hrga5_2RiwayatPemeliharaanController::class)
        ->prefix('hccp/hrga5_2')
        ->name('hrga5_2.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/print', 'print')->name('print');
            Route::post('/store', 'store')->name('store');
        });
    Route::controller(hrga5_3PermintaanPerbaikan::class)
        ->prefix('hccp/hrga5_3')
        ->name('hrga5_3.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/print/{id}', 'print')->name('print');
            Route::get('/get_item', 'get_item')->name('get_item');
            Route::get('/get_merk', 'get_merk')->name('get_merk');
            Route::post('/store', 'store')->name('store');
            Route::post('/store2', 'store2')->name('store2');
        });
    Route::controller(hrga8_1Program_peratawan::class)
        ->prefix('hccp/hrga8_1')
        ->name('hrga8_1.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/get_item', 'get_item')->name('get_item');
            Route::post('/store', 'store')->name('store');
            Route::get('/print', 'print')->name('print');
        });
    Route::controller(hrga8_2Ceklistperawatanmesin::class)
        ->prefix('hccp/hrga8_2')
        ->name('hrga8_2.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/tambah_baris', 'tambah_baris')->name('tambah_baris');
            Route::get('/print/{id}', 'print')->name('print');
            Route::post('/store', 'store')->name('store');
        });
    Route::controller(hrga8_3Permintaan_perbaikan::class)
        ->prefix('hccp/hrga8_3')
        ->name('hrga8_3.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/get_item', 'get_item')->name('get_item');
            Route::post('/store', 'store')->name('store');
            Route::get('/print/{id}', 'print')->name('print');
            Route::post('/store2', 'store2')->name('store2');
        });
    Route::controller(CostGlobalController::class)
        ->prefix('home/cost_global')
        ->name('cost_global.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
        });

    Route::controller(QcController::class)
        ->prefix('home/qc')
        ->name('qc.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/save_invoice_qc', 'save_invoice_qc')->name('save_invoice_qc');
            Route::get('/listqc', 'listqc')->name('listqc');
            Route::get('/listboxqc', 'listboxqc')->name('listboxqc');
            Route::post('/save_akhir', 'save_akhir')->name('save_akhir');
            Route::post('/po_wip', 'po_wip')->name('po_wip');
        });
});
