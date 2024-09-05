<?php

use App\Http\Controllers\BkController;
use App\Http\Controllers\CabutController;
use App\Http\Controllers\DataPengawasController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\PengawasController;
use App\Http\Controllers\SortirController;
use App\Http\Controllers\AksesController;
use App\Http\Controllers\BoxKirimController;
use App\Http\Controllers\DendaController;
use App\Http\Controllers\EoController;
use App\Http\Controllers\GlobalController;
use App\Http\Controllers\GradingBjController;
use App\Http\Controllers\HariandllController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\OpnameController;
use App\Http\Controllers\PengirimanController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PackingListController;
use App\Http\Controllers\PenutupController;
use App\Http\Controllers\SiapKirimController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;

Route::middleware(['auth', 'cekPosisi'])->group(function () {
    Route::get('/403', function () {
        view('error.403');
    })->name('403');
    Route::get('/nota', function () {
        $jsonData = Storage::disk('local')->get('json/data_nota.json');
        $data = json_decode($jsonData, true);
        $data = [
            'title' => 'Nota',
            'data' => $data
        ];
        return view('nota', $data);
    })->name('nota');

    Route::post('/nota', function (Request $r) {
        $imageName = time() . '.' . $r->image->extension();
        $r->image->move(public_path('uploads'), $imageName);
        // Tentukan nama file JSON
        $jsonFileName = 'data_nota.json';
        $ket = $r->ket;
        $newData  = [
            'image_name' => $imageName,
            'ket' => $ket,
        ];

        // Cek apakah file JSON sudah ada
        if (Storage::disk('local')->exists('json/' . $jsonFileName)) {
            // Baca file JSON yang ada
            $jsonData = Storage::disk('local')->get('json/' . $jsonFileName);
            $dataArray = json_decode($jsonData, true);
        } else {
            // Jika file belum ada, buat array baru
            $dataArray = [];
        }

        // Tambahkan data baru ke array
        $dataArray[] = $newData;

        // Convert array yang sudah diperbarui menjadi JSON
        $jsonData = json_encode($dataArray, JSON_PRETTY_PRINT);

        // Simpan kembali file JSON
        Storage::disk('local')->put('json/' . $jsonFileName, $jsonData);

        return redirect('nota')->with('sukses', 'Berhasil tambah Data');
    })->name('nota');

    Route::get('/503', function () {
        view('error.503');
    })->name('503');
    // Route::get('/log', function (Request $r) {
    //     $pengawas = DB::table('users')->select('id as id_pengawas','name')->where('posisi_id', 13)->get();
    //     $id_pengawas = $r->id_pengawas ?? auth()->user()->id;
    //     $data = [
    //         'pengawas' => $pengawas,
    //         'id_pengawas' => $id_pengawas,
    //         'log' => Activity::join('users as b', 'activity_log.causer_id', '=', 'b.id')
    //                     ->select('activity_log.*', 'b.name')
    //                     ->where('activity_log.causer_id', $id_pengawas)
    //                     ->orderBy('activity_log.id', 'desc')
    //                     ->get(),
    //     ];
    //    return view('log.index',$data);
    // })->name('log');


    // Route::get('/db', function (Request $r) {
    //     if ($r->password == 'Takemor.') {
    //         return view('db');
    //     }
    //     return view('db.login');
    // })->name('db');

    Route::post('/dbcreate', function (Request $r) {
        $namaDataseSebelumnyaba = DB::getDatabaseName();
        $namaDatabase = $r->db;
        // Mengganti koneksi database pada runtime
        config(['database.connections.mysql.database' => $namaDatabase]);
        DB::purge('mysql');
        DB::reconnect('mysql');
        // Mengganti nilai database pada file .env
        // Path ke file .env
        $envFilePath = base_path('.env');

        // Memastikan bahwa file .env ada sebelum membacanya
        if (File::exists($envFilePath)) {
            // Mengganti nilai database pada file .env
            File::put($envFilePath, str_replace(
                'DB_DATABASE=' . $namaDataseSebelumnyaba,
                'DB_DATABASE=' . $namaDatabase,
                File::get($envFilePath)
            ));

            // Menjalankan command untuk mereload konfigurasi Laravel
            Artisan::call('config:cache');

            $tabel = DB::connection()->getDoctrineSchemaManager()->listTableNames();

            // Dump semua tabel
            dd($tabel);
        } else {
            // Jika file .env tidak ada
            return "File .env tidak ditemukan.";
        }
        // Mendapatkan nama database setelah perubahan
        // Mendapatkan daftar semua tabel pada database

    })->name('dbcreate');

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
            Route::get('/cetak', 'cetak')->name('cetak');
            Route::get('/sortir', 'sortir')->name('sortir');
            Route::get('/grading', 'grading')->name('grading');
            Route::get('/pengiriman', 'pengiriman')->name('pengiriman');
            Route::get('/totalan', 'totalan')->name('totalan');
            Route::get('/totalan_new', 'totalan_new')->name('totalan_new');
            Route::get('/export', 'export')->name('export');
            Route::get('/export_ibu', 'export_ibu')->name('export_ibu');
            Route::get('/export2', 'export2')->name('export2');
            Route::get('/getSummaryIbu', 'getSummaryIbu')->name('getSummaryIbu');
            Route::get('/detailSummaryIbu', 'detailSummaryIbu')->name('detailSummaryIbu');
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
            Route::post('/create_ambil_cetak', 'create_ambil_cetak')->name('create_ambil_cetak');
            Route::post('/import', 'import')->name('import');
            Route::get('/export', 'export')->name('export');
            Route::post('/importperbaikan', 'importperbaikan')->name('importperbaikan');
        });
    Route::controller(CabutController::class)
        ->prefix('home/cabut')
        ->name('cabut.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'create')->name('create');
            Route::get('/add', 'add')->name('add');
            Route::get('/history', 'history')->name('history');
            Route::get('/clear', 'clear')->name('clear');
            Route::post('/clearSave', 'clearSave')->name('clearSave');
            Route::get('/global', 'global')->name('global');
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
            Route::get('/laporan_perhari', 'laporan_perhari')->name('laporan_perhari');
            Route::get('/detail_laporan_harian', 'detail_laporan_harian')->name('detail_laporan_harian');
            Route::get('/load_modal_anak_sisa', 'load_modal_anak_sisa')->name('load_modal_anak_sisa');
            Route::get('/get_kelas_jenis', 'get_kelas_jenis')->name('get_kelas_jenis');
            Route::get('/hapusAnakSisa', 'hapusAnakSisa')->name('hapusAnakSisa');
            Route::get('/cancel', 'cancel')->name('cancel');
            Route::get('/export_rekap', 'export_rekap')->name('export_rekap');
            Route::get('/export_sinta', 'export_sinta')->name('export_sinta');
            Route::get('/summary', 'summary')->name('summary');
            Route::get('/gudang', 'gudang')->name('gudang');
            Route::get('/export_gudang', 'export_gudang')->name('export_gudang');
            Route::post('/save_formulir', 'save_formulir')->name('save_formulir');
            Route::post('/save_formulir_eo', 'save_formulir_eo')->name('save_formulir_eo');
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
            Route::get('/history', 'history')->name('history');
            Route::get('/tbh_baris', 'tbh_baris')->name('tbh_baris');
            Route::get('/get_box_sinta', 'get_box_sinta')->name('get_box_sinta');
            Route::get('/load_modal_akhir', 'load_modal_akhir')->name('load_modal_akhir');
            Route::get('/input_akhir', 'input_akhir')->name('input_akhir');
            Route::post('/create_anak', 'create_anak')->name('create_anak');
            Route::post('/ambil_box_bk', 'ambil_box_bk')->name('ambil_box_bk');
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
            Route::get('/gudang', 'gudang')->name('gudang');
            Route::get('/export_gudang', 'export_gudang')->name('export_gudang');
            Route::post('/save_formulir', 'save_formulir')->name('save_formulir');
            Route::get('/save_akhir', 'save_akhir')->name('save_akhir');
            Route::get('/cancel_sortir', 'cancel_sortir')->name('cancel_sortir');
            Route::get('/load_halamanrow', 'load_halamanrow')->name('load_halamanrow');
            Route::post('/import', 'import')->name('import');
        });
    Route::controller(EoController::class)
        ->prefix('home/eo')
        ->name('eo.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/add', 'add')->name('add');
            Route::get('/tbh_baris', 'tbh_baris')->name('tbh_baris');
            Route::get('/history', 'history')->name('history');
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
            Route::get('/ditutup', 'ditutup')->name('ditutup');
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
            Route::get('/hapus/{id}', 'hapus')->name('hapus');
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
    // Route::controller(PengirimanController::class)
    //     ->prefix('home/pengiriman_copy')
    //     ->name('pengiriman_copy.')
    //     ->group(function () {
    //         Route::get('/', 'hal_awal')->name('index');
    //         Route::get('/add', 'add')->name('add');
    //         Route::get('/edit', 'edit')->name('edit');
    //         Route::post('/create', 'create')->name('create');
    //         Route::get('/template', 'template')->name('template');
    //         Route::post('/import', 'import')->name('import');
    //         Route::post('/update', 'update')->name('update');
    //         Route::post('/delete', 'delete')->name('delete');
    //     });
    Route::controller(BoxKirimController::class)
        ->prefix('home/gradingbj/boxkirim')
        ->name('pengiriman.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/add', 'add')->name('add');
            Route::get('/po/{no_nota}', 'po')->name('po');
            Route::get('/edit', 'edit')->name('edit');
            Route::post('/create', 'create')->name('create');
            Route::post('/save_po', 'save_po')->name('save_po');
            Route::get('/template', 'template')->name('template');
            Route::get('/gudang', 'gudang')->name('gudang');
            Route::post('/kirim', 'kirim')->name('kirim');
            Route::post('/import', 'import')->name('import');
            Route::post('/update', 'update')->name('update');
            Route::get('/delete', 'delete')->name('delete');
            Route::get('/selesai_grade', 'selesai_grade')->name('selesai_grade');
            Route::get('/print_formulir_grade', 'print_formulir_grade')->name('print_formulir_grade');
            Route::get('/batal', 'batal')->name('batal');
        });
    Route::controller(PackingListController::class)
        ->prefix('home/gradingbj/packinglist')
        ->name('packinglist.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/pengiriman', 'pengiriman')->name('pengiriman');
            Route::post('/pengiriman', 'create')->name('create');
            Route::post('/tbh_invoice', 'tbh_invoice')->name('tbh_invoice');
            Route::get('/detail', 'detail')->name('detail');
            Route::get('/print/{no_nota}', 'print')->name('print');
            Route::get('/delete/{no_nota}', 'delete')->name('delete');
        });
    Route::controller(SiapKirimController::class)
        ->prefix('home/siapkirim')
        ->name('siapkirim.')
        ->group(function () {
            Route::get('/ambilsortir', 'index')->name('ambilsortir');
            Route::get('/add', 'add')->name('add');
            Route::get('/gudang', 'gudang')->name('gudang');
            Route::post('/create', 'create')->name('create');
            Route::post('/create_grading', 'create_grading')->name('create_grading');
            Route::get('/load_grading', 'load_grading')->name('load_grading');
            Route::get('/load_detail', 'load_detail')->name('load_detail');
            Route::get('/load_ambil_box_kecil', 'load_ambil_box_kecil')->name('load_ambil_box_kecil');
            Route::get('/get_select_grade',     'get_select_grade')->name('get_select_grade');
            Route::get('/', 'hal_awal')->name('index');
        });
    Route::controller(GradingBjController::class)
        ->prefix('home/gradingbjdsada')
        ->name('gradingbj_copy.')
        ->group(function () {
            // Route::get('/history_ambil', 'index')->name('history_ambil');
            // Route::get('/add', 'add')->name('add');
            // Route::get('/gudang_bj', 'gudang_bj')->name('gudang_bj');
            // Route::get('/get_select_grade', 'get_select_grade')->name('get_select_grade');
            // Route::get('/history_box_kecil', 'history_box_kecil')->name('history_box_kecil');
            // Route::get('/load_ambil_box_kecil', 'load_ambil_box_kecil')->name('load_ambil_box_kecil');
            // Route::get('/gudang_bahan_jadi', 'gudang_bahan_jadi')->name('gudang_bahan_jadi');
            // Route::post('/create_ambil_box_kecil', 'create_ambil_box_kecil')->name('create_ambil_box_kecil');
            // Route::post('/create', 'create')->name('create');
            // Route::post('/create_suntikan', 'create_suntikan')->name('create_suntikan');
            // Route::post('/create_suntikan_boxsp', 'create_suntikan_boxsp')->name('create_suntikan_boxsp');
            // Route::post('/create_grading', 'create_grading')->name('create_grading');
            // Route::get('/load_grading', 'load_grading')->name('load_grading');
            // Route::get('/load_detail', 'load_detail')->name('load_detail');
            // Route::get('/template', 'template')->name('template');
            // Route::post('/import', 'import')->name('import');
            // Route::get('/', 'halAwal')->name('index');
        });
    Route::controller(GradingBjController::class)
        ->prefix('home/gradingbj')
        ->name('gradingbj.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/grading', 'grading')->name('grading');
            Route::post('/grading_partai', 'grading_partai')->name('grading_partai');
            Route::post('/create', 'create')->name('create');
            Route::post('/create_partai', 'create_partai')->name('create_partai');
            Route::post('/createUlang', 'createUlang')->name('createUlang');
            Route::get('/opname', 'opname')->name('opname');
            Route::post('/import', 'import')->name('import');
            Route::get('/template_import', 'template_import')->name('template_import');
            Route::post('/import_gudang_siap_kirim', 'import_gudang_siap_kirim')->name('import_gudang_siap_kirim');
            Route::get('/template_import_gudang_siap_kirim', 'template_import_gudang_siap_kirim')->name('template_import_gudang_siap_kirim');
            Route::get('/gudang_siap_kirim', 'gudang_siap_kirim')->name('gudang_siap_kirim');
            Route::get('/detail', 'detail')->name('detail');
            Route::get('/cancel', 'cancel')->name('cancel');
            Route::post('/cancel', 'cancelBoxPengiriman')->name('cancelBoxPengiriman');
            Route::get('/selesai', 'selesai')->name('selesai');
            Route::get('/load_selisih', 'load_selisih')->name('load_selisih');
            Route::get('/export_selisih', 'export_selisih')->name('export_selisih');
            Route::get('/export_susut', 'export_susut')->name('export_susut');
            Route::get('/detail_pengiriman', 'detail_pengiriman')->name('detail_pengiriman');
        });
    Route::controller(OpnameController::class)
        ->prefix('home/opname')
        ->name('opname.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/detail', 'detail')->name('detail');
            Route::get('/export_ibu', 'export_ibu')->name('export_ibu');
        });
    Route::controller(PenutupController::class)
        ->prefix('data_master/penutup')
        ->name('penutup.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/show/{bulan}/{tahun}', 'show')->name('show');
            Route::post('/import', 'import')->name('import');
            Route::post('/tutup_gaji', 'tutup_gaji')->name('tutup_gaji');
        });

    // Route::controller(PackingListController::class)
    //     ->prefix('home/packinglist')
    //     ->name('packinglist.')
    //     ->group(function () {
    //         Route::get('/', 'index')->name('index');
    //         Route::get('/tambahgr', function () {
    //             return view('tambahgr');
    //         });
    //         Route::post('/tambahgr', function (Request $r) {
    //             for ($i = 0; $i < count($r->nm_grade); $i++) {
    //                 if ($r->nm_grade[$i] != '') {
    //                     DB::table('tb_grade')->insert([
    //                         'nm_grade' => $r->nm_grade[$i],
    //                         'urutan' => $i + 1
    //                     ]);
    //                 }
    //             }
    //         })->name('tambahgr');
    //         Route::get('/load_tbh', 'load_tbh')->name('load_tbh');
    //         Route::get('/add_box_kirim', 'add_box_kirim')->name('add_box_kirim');
    //         Route::post('/create_box_kirim', 'create_box_kirim')->name('create_box_kirim');
    //         Route::get('/detail', 'detail')->name('detail');
    //         Route::post('/update', 'update')->name('update');
    //         Route::get('/edit', 'edit')->name('edit');
    //         Route::get('/print/{no_nota}', 'print')->name('print');
    //         Route::get('/delete/{no_nota}', 'delete')->name('delete');
    //         Route::post('/create', 'create')->name('create');
    //         Route::post('/tbh_invoice', 'tbh_invoice')->name('tbh_invoice');
    //         Route::get('/gudangKirim', 'gudangKirim')->name('gudangKirim');
    //     });
});
