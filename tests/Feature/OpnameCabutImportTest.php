<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\OpnameCabutImport;
use App\Services\OpnameBkImport;
use App\Services\OpnameCetakImport;
use App\Services\OpnameSortirImport;
use App\Services\OpnameGradingImport;
use App\Services\OpnameImportCancellation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class OpnameCabutImportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Semua pengujian memakai SQLite terpisah, bukan database lokal pengguna.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        (require database_path('migrations/2026_10_09_000001_create_opname_import_histories_table.php'))->up();
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('posisi_id');
        });
        Schema::create('tb_anak', function (Blueprint $table) {
            $table->increments('id_anak');
            $table->string('nama')->default('Anak uji');
            $table->integer('id_pengawas');
            $table->integer('id_kelas')->default(4);
            $table->string('berhenti')->default('T');
        });
        Schema::create('tb_kelas', function (Blueprint $table) {
            $table->increments('id_kelas');
            $table->string('kelas')->default('A');
            $table->string('tipe')->default('cabut');
            $table->double('rupiah');
        });
        Schema::create('bk', function (Blueprint $table) {
            $table->increments('id_bk');
            foreach (['no_box', 'nm_partai', 'no_lot', 'tgl', 'tgl_input', 'pengawas', 'penerima',
                'kategori', 'baru', 'selesai', 'formulir', 'invoice_formulir', 'pgws_grade'] as $field) {
                $table->string($field);
            }
            foreach (['tipe', 'ket', 'warna'] as $field) {
                $table->string($field)->nullable();
            }
            foreach (['pcs_awal', 'gr_awal', 'hrga_satuan', 'ttl_rp', 'susut'] as $field) {
                $table->double($field);
            }
        });
        Schema::create('formulir_sarang', function (Blueprint $table) {
            $table->increments('id_formulir');
            foreach (['no_invoice', 'no_box', 'tanggal', 'kategori', 'selesai', 'print'] as $field) {
                $table->string($field);
            }
            $table->integer('id_pemberi');
            $table->integer('id_penerima');
            $table->double('pcs_awal');
            $table->double('gr_awal');
            $table->double('sst_aktual');
        });
        Schema::create('tb_hancuran', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('no_invoice');
            $table->integer('no_box');
            $table->double('pcs');
            $table->string('kategori');
        });
        Schema::create('cabut', function (Blueprint $table) {
            $table->increments('id_cabut');
            $table->double('no_box');
            foreach (['id_pengawas', 'id_anak', 'id_kelas', 'bulan_dibayar', 'tahun_dibayar'] as $field) {
                $table->integer($field);
            }
            $table->string('tgl_terima');
            $table->string('tgl_serah')->nullable();
            $table->string('invoice')->nullable();
            foreach (['selesai', 'formulir', 'penutup'] as $field) {
                $table->string($field);
            }
            foreach (['pcs_awal', 'gr_awal', 'pcs_akhir', 'gr_akhir', 'ttl_rp', 'cost_op',
                'rupiah', 'pcs_hcr', 'gr_hcr', 'pcs_flx', 'gr_flx', 'eot'] as $field) {
                $table->double($field)->nullable();
            }
        });
        foreach (['eo', 'pengiriman'] as $name) {
            Schema::create($name, fn (Blueprint $table) => $table->string('no_box'));
        }
        Schema::create('sortir', function (Blueprint $table) {
            $table->increments('id_sortir');
            $table->string('no_box');
            foreach (['id_kelas', 'bulan', 'tahun_dibayar', 'id_anak', 'id_pengawas'] as $field) {
                $table->integer($field)->default(0);
            }
            foreach (['tgl', 'tgl_input', 'selesai', 'penutup'] as $field) {
                $table->string($field)->default('T');
            }
            foreach (['pcs_awal', 'gr_awal', 'pcs_tdk_sortir', 'gr_tdk_sortir', 'pcs_akhir', 'gr_akhir', 'rp_target', 'ttl_rp', 'denda_sp', 'pcuc', 'pcus'] as $field) {
                $table->double($field)->default(0);
            }
        });
        Schema::create('tb_kelas_sortir', function (Blueprint $table) {
            $table->integer('id_kelas');
            $table->string('kelas');
        });
        DB::table('tb_kelas_sortir')->insert(['id_kelas' => 11, 'kelas' => 'lewat']);
        Schema::create('cetak_new', function (Blueprint $table) {
            $table->increments('id_cetak');
            $table->string('no_box');
            foreach (['id_pemberi', 'id_pengawas', 'id_anak', 'id_kelas_cetak', 'bulan_dibayar', 'tahun_dibayar', 'tipe_bayar'] as $field) {
                $table->integer($field)->default(0);
            }
            foreach (['tgl', 'selesai', 'capai', 'formulir', 'penutup'] as $field) {
                $table->string($field)->default('T');
            }
            foreach (['pcs_awal', 'gr_awal', 'pcs_tdk_cetak', 'gr_tdk_cetak', 'pcs_awal_ctk', 'gr_awal_ctk', 'rp_satuan', 'pcs_akhir', 'pcs_hcr', 'gr_akhir', 'ttl_rp', 'rp_tambahan'] as $field) {
                $table->double($field)->default(0);
            }
        });
        Schema::create('kelas_cetak', function (Blueprint $table) {
            $table->integer('id_kelas_cetak');
            $table->string('kelas')->default('CTK LEWAT');
            $table->string('kategori');
        });
        DB::table('kelas_cetak')->insert(['id_kelas_cetak' => 14, 'kategori' => 'CTK']);
        Schema::create('grading', fn (Blueprint $table) => $table->string('no_box_sortir'));
        DB::table('users')->insert([
            ['id' => 1, 'name' => 'Admin', 'posisi_id' => 1],
            ['id' => 85, 'name' => 'Pengawas', 'posisi_id' => 13],
            ['id' => 285, 'name' => 'Arbayah', 'posisi_id' => 13],
            ['id' => 462, 'name' => 'Pengawas Cetak', 'posisi_id' => 14],
        ]);
        DB::table('tb_anak')->insert(['id_anak' => 2, 'nama' => 'Emo', 'id_pengawas' => 85]);
        DB::table('tb_anak')->insert(['id_anak' => 3, 'id_pengawas' => 85]);
        DB::table('tb_kelas')->insert([['id_kelas' => 4, 'rupiah' => 500], ['id_kelas' => 5, 'rupiah' => 600], ['id_kelas' => 126, 'rupiah' => 1000]]);
        DB::table('tb_anak')->insert([['id_anak' => 10, 'id_pengawas' => 285, 'id_kelas' => 4], ['id_anak' => 11, 'id_pengawas' => 285, 'id_kelas' => 5]]);
    }

    private function row(array $changes = []): array
    {
        return array_replace(array_fill_keys(OpnameCabutImport::HEADERS, null), [
            '_baris' => 2, 'no_box' => '90001', 'nm_partai' => 'PARTAI UJI', 'tgl' => '2026-10-01',
            'id_pengawas' => 85, 'pcs_awal' => 100,
            'gr_awal' => 500.5, 'hrga_satuan' => 10000,
        ], $changes);
    }

    private function excel(array $rows, array $headers = OpnameCabutImport::HEADERS): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'opname_test_');
        $book = new Spreadsheet();
        $sheet = $book->getActiveSheet();
        $sheet->fromArray($headers, null, 'A1');
        foreach ($rows as $index => $row) {
            $sheet->fromArray(array_map(fn ($key) => $row[$key], $headers), null, 'A'.($index + 2), true);
        }
        (new Xlsx($book))->save($path);
        $book->disconnectWorksheets();
        $file = new UploadedFile($path, 'cabut.xlsx', null, null, true);
        $this->beforeApplicationDestroyed(fn () => unlink($path));

        return $file;
    }

    public function test_stock_creates_bk_and_matching_handover_without_assigning_a_child(): void
    {
        $result = (new OpnameCabutImport())->store([$this->row()], 'stok', 1);
        $this->assertSame(1, $result['boxes']);
        $this->assertDatabaseHas('bk', ['no_box' => '90001', 'penerima' => 85, 'formulir' => 'Y', 'ttl_rp' => 5005000]);
        $this->assertDatabaseHas('formulir_sarang', ['no_box' => '90001', 'kategori' => 'cabut', 'id_pemberi' => 1, 'id_penerima' => 85]);
        $this->assertSame(DB::table('bk')->value('invoice_formulir'), DB::table('formulir_sarang')->value('no_invoice'));
        $this->assertDatabaseCount('cabut', 0);
    }

    public function test_process_and_finished_statuses_cannot_be_imported(): void
    {
        foreach (['proses', 'selesai'] as $status) {
            try {
                (new OpnameCabutImport())->store([$this->row()], $status, 1);
                $this->fail('Unsupported status was accepted.');
            } catch (ValidationException $exception) {
                $this->assertDatabaseCount('bk', 0);
                $this->assertDatabaseCount('cabut', 0);
            }
        }
    }

    public function test_bad_second_row_prevents_the_entire_batch_from_being_written(): void
    {
        try {
            (new OpnameCabutImport())->store([$this->row(), $this->row(['no_box' => '90002', 'gr_awal' => -1])], 'stok', 1);
            $this->fail('Invalid batch was accepted.');
        } catch (ValidationException $exception) {
            $this->assertDatabaseCount('bk', 0);
            $this->assertDatabaseCount('formulir_sarang', 0);
            $this->assertDatabaseCount('cabut', 0);
        }
    }

    public function test_database_failure_rolls_back_bk_and_handover(): void
    {
        DB::unprepared("CREATE TRIGGER reject_handover BEFORE INSERT ON formulir_sarang WHEN NEW.no_box = '90002' BEGIN SELECT RAISE(ABORT, 'test failure'); END;");
        try {
            (new OpnameCabutImport())->store([$this->row(), $this->row(['no_box' => '90002'])], 'stok', 1);
            $this->fail('Database failure was ignored.');
        } catch (\Illuminate\Database\QueryException $exception) {
            $this->assertDatabaseCount('bk', 0);
            $this->assertDatabaseCount('formulir_sarang', 0);
        }
    }

    public function test_duplicate_or_historical_boxes_are_rejected(): void
    {
        DB::table('pengiriman')->insert(['no_box' => '90001']);
        $this->expectException(ValidationException::class);
        (new OpnameCabutImport())->store([$this->row(), $this->row()], 'stok', 1);
    }

    public function test_child_assignment_and_invalid_dates_are_rejected(): void
    {
        $service = new OpnameCabutImport();
        foreach ([$this->row(['id_anak' => 3]), $this->row(['tgl' => '2026-02-30'])] as $row) {
            try {
                $service->validateRows([$row], 'stok');
                $this->fail('Invalid row was accepted.');
            } catch (ValidationException $exception) {
                $this->assertNotEmpty($exception->errors());
            }
        }
    }

    public function test_preview_is_read_only_and_commit_cannot_be_replayed(): void
    {
        $this->actingAs(User::find(1));
        $response = $this->post(route('opname_menu.preview_cabut'), ['status' => 'stok', 'file' => $this->excel([$this->row()])]);
        $response->assertSessionHasNoErrors()->assertRedirect(route('opname_menu.index'));
        $this->assertDatabaseCount('bk', 0);
        $token = session('opname_cabut_preview.token');
        $this->post(route('opname_menu.import_cabut'), ['token' => $token])->assertSessionHasNoErrors()->assertSessionHas('sukses');
        $this->assertDatabaseCount('bk', 1);
        $this->post(route('opname_menu.import_cabut'), ['token' => $token])->assertSessionHasErrors('file');
        $this->assertDatabaseCount('bk', 1);
    }

    public function test_commit_rechecks_boxes_added_after_preview(): void
    {
        $this->actingAs(User::find(1));
        $this->post(route('opname_menu.preview_cabut'), ['status' => 'stok', 'file' => $this->excel([$this->row()])])->assertSessionHasNoErrors();
        $token = session('opname_cabut_preview.token');
        DB::table('sortir')->insert(['no_box' => '90001']);
        $this->post(route('opname_menu.import_cabut'), ['token' => $token])->assertSessionHasErrors('file');
        $this->assertDatabaseCount('bk', 0);
    }

    public function test_excel_dates_and_zero_price_are_preserved(): void
    {
        $file = $this->excel([$this->row(['tgl' => 46296, 'hrga_satuan' => 0])]);
        $service = new OpnameCabutImport();
        $rows = $service->validateRows($service->read($file->getRealPath()), 'stok');
        $this->assertMatchesRegularExpression('/^2026-\d{2}-\d{2}$/', $rows[0]['tgl']);
        $this->assertSame('0', $rows[0]['hrga_satuan']);
    }

    public function test_template_download_has_import_columns_and_reference_sheets(): void
    {
        $this->actingAs(User::find(1));
        $response = $this->get(route('opname_menu.template_cabut'));
        $response->assertOk()->assertDownload('Template Import Cabut.xlsx');
        $path = tempnam(sys_get_temp_dir(), 'opname_template_');
        try {
            file_put_contents($path, $response->streamedContent());
            $book = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            $this->assertSame(['Import Cabut', 'Petunjuk', 'Pengawas'], $book->getSheetNames());
            $this->assertSame(OpnameCabutImport::HEADERS, $book->getSheet(0)->toArray()[0]);
            $this->assertSame(285, $book->getSheetByName('Pengawas')->getCell('A2')->getValue());
            $book->disconnectWorksheets();
        } finally {
            unlink($path);
        }
    }

    public function test_empty_workbook_and_missing_headers_are_rejected(): void
    {
        $service = new OpnameCabutImport();
        $file = $this->excel([]);
        try {
            $service->validateRows($service->read($file->getRealPath()), 'stok');
            $this->fail('Empty file was accepted.');
        } catch (ValidationException $exception) {
            $this->assertNotEmpty($exception->errors());
        }
        $book = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
        $book->getSheet(0)->setCellValue('A1', 'wrong_header');
        (new Xlsx($book))->save($file->getRealPath());
        $book->disconnectWorksheets();
        $this->expectException(ValidationException::class);
        $service->read($file->getRealPath());
    }

    public function test_previous_preview_is_rejected_and_tampered_status_cannot_be_uploaded(): void
    {
        $this->actingAs(User::find(1));
        $this->withSession(['opname_cabut_preview' => [
            'token' => 'old-template', 'user_id' => 1, 'expires_at' => now()->addMinute()->timestamp,
            'status' => 'stok', 'rows' => [$this->row()],
        ]])->post(route('opname_menu.import_cabut'), ['token' => 'old-template'])->assertSessionHasErrors('file');
        $this->post(route('opname_menu.preview_cabut'), ['status' => 'proses', 'file' => $this->excel([$this->row()])])
            ->assertSessionHasErrors('status');
        $this->assertDatabaseCount('bk', 0);
        $this->assertDatabaseCount('cabut', 0);
    }

    public function test_expired_preview_and_guest_import_are_rejected(): void
    {
        $this->post(route('opname_menu.import_cabut'), ['token' => 'unused'])->assertRedirect(route('login'));
        $this->actingAs(User::find(1));
        $this->withSession(['opname_cabut_preview' => [
            'token' => 'expired', 'user_id' => 1, 'expires_at' => now()->subMinute()->timestamp,
            'version' => 2, 'status' => 'stok', 'rows' => [$this->row()],
        ]])->post(route('opname_menu.import_cabut'), ['token' => 'expired'])->assertSessionHasErrors('file');
        $this->assertDatabaseCount('bk', 0);
    }

    private function cetakRow(array $changes = []): array
    {
        return $this->row(array_replace([
            'id_pengawas_cetak' => 462,
        ], $changes));
    }

    public function test_cetak_import_records_all_prior_stages_with_equal_cabut_pcs(): void
    {
        $result = (new OpnameCetakImport())->store([$this->cetakRow()], 'cetak', 1);
        $this->assertSame(1, $result['boxes']);
        $this->assertDatabaseHas('bk', ['no_box' => '90001', 'penerima' => 285]);
        $this->assertDatabaseHas('cabut', [
            'no_box' => 90001, 'pcs_awal' => 100, 'pcs_akhir' => 100,
            'gr_awal' => 500.5, 'gr_akhir' => 500.5, 'selesai' => 'Y', 'formulir' => 'Y',
            'ttl_rp' => 0, 'cost_op' => 0, 'rupiah' => 0, 'id_pengawas' => 285, 'id_kelas' => 126,
        ]);
        $this->assertDatabaseHas('formulir_sarang', [
            'kategori' => 'cabut', 'id_pemberi' => 1, 'id_penerima' => 285,
            'pcs_awal' => 100, 'gr_awal' => 500.5, 'tanggal' => '2026-10-01',
        ]);
        $this->assertDatabaseHas('formulir_sarang', [
            'kategori' => 'cetak', 'id_pemberi' => 285, 'id_penerima' => 462,
            'pcs_awal' => 100, 'gr_awal' => 500.5, 'tanggal' => '2026-10-01',
        ]);
        $this->assertDatabaseCount('formulir_sarang', 2);
        $this->assertDatabaseCount('cetak_new', 0);
        $this->assertCount(1, $result['invoices']);
        $this->assertCount(1, $result['cetak_invoices']);
    }

    public function test_cetak_preview_and_commit_follow_the_selected_destination(): void
    {
        $this->actingAs(User::find(1));
        $this->post(route('opname_menu.preview_cabut'), [
            'status' => 'cetak', 'file' => $this->excel([$this->cetakRow()], OpnameCetakImport::HEADERS),
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('bk', 0);
        $this->assertSame('cetak', session('opname_cabut_preview.status'));
        $selected = session('opname_cabut_preview.rows.0');
        $this->assertSame(285, $selected['id_pengawas']);
        $this->assertContains($selected['id_anak_cabut'], [10, 11]);
        $this->assertSame(126, $selected['id_kelas_cabut']);
        $token = session('opname_cabut_preview.token');
        $this->post(route('opname_menu.import_cabut'), ['token' => $token])
            ->assertSessionHasNoErrors()->assertSessionHas('opname_cetak_invoices');
        $this->assertDatabaseCount('cabut', 1);
        $this->assertDatabaseHas('cabut', ['id_anak' => $selected['id_anak_cabut'], 'id_kelas' => $selected['id_kelas_cabut'], 'id_pengawas' => 285]);
        $this->assertDatabaseCount('formulir_sarang', 2);
        $this->post(route('opname_menu.import_cabut'), ['token' => $token])->assertSessionHasErrors('file');
        $this->assertDatabaseCount('cabut', 1);
    }

    public function test_cetak_handover_failure_rolls_back_bk_cabut_and_both_forms(): void
    {
        DB::unprepared("CREATE TRIGGER reject_cetak BEFORE INSERT ON formulir_sarang WHEN NEW.kategori = 'cetak' BEGIN SELECT RAISE(ABORT, 'test failure'); END;");
        try {
            (new OpnameCetakImport())->store([$this->cetakRow()], 'cetak', 1);
            $this->fail('Failed Cetak handover was accepted.');
        } catch (\Illuminate\Database\QueryException $exception) {
            $this->assertDatabaseCount('bk', 0);
            $this->assertDatabaseCount('cabut', 0);
            $this->assertDatabaseCount('formulir_sarang', 0);
        }
    }

    public function test_cetak_validation_rejects_missing_stages_and_inconsistent_data(): void
    {
        foreach ([
            ['id_anak_cabut' => 99],
            ['id_pengawas_cetak' => 85], ['tgl_selesai_cabut' => '2026-10-01'],
            ['tgl_terima_cetak' => '2026-10-02'], ['gr_akhir_cabut' => 501],
            ['gr_akhir_cabut' => 0], ['biaya_cabut' => 25000], ['cost_op_cabut' => 1500], ['pcs_akhir' => 99],
        ] as $changes) {
            try {
                (new OpnameCetakImport())->store([$this->cetakRow($changes)], 'cetak', 1);
                $this->fail('Invalid Cetak data was accepted: '.json_encode($changes));
            } catch (ValidationException $exception) {
                $this->assertDatabaseCount('bk', 0);
            }
        }
        DB::table('tb_anak')->where('id_pengawas', 285)->update(['id_pengawas' => 1]);
        $this->expectException(ValidationException::class);
        (new OpnameCetakImport())->validateRows([$this->cetakRow()], 'cetak');
    }

    public function test_cetak_groups_forms_by_handover_and_cannot_use_cabut_template(): void
    {
        $result = (new OpnameCetakImport())->store([
            $this->cetakRow(), $this->cetakRow(['no_box' => '90002']),
        ], 'cetak', 1);
        $this->assertCount(1, $result['invoices']);
        $this->assertCount(1, $result['cetak_invoices']);
        $this->assertDatabaseCount('formulir_sarang', 4);
        foreach ([
            [new OpnameCetakImport(), $this->excel([$this->row()])],
            [new OpnameCabutImport(), $this->excel([$this->cetakRow()], OpnameCetakImport::HEADERS)],
        ] as [$service, $file]) {
            try {
                $service->read($file->getRealPath());
                $this->fail('Template for a different destination was accepted.');
            } catch (ValidationException $exception) {
                $this->assertNotEmpty($exception->errors());
            }
        }
    }

    public function test_cetak_template_provides_all_headers_and_references(): void
    {
        $this->actingAs(User::find(1));
        $response = $this->get(route('opname_menu.template_cetak'));
        $response->assertOk()->assertDownload('Template Import Cetak.xlsx');
        $path = tempnam(sys_get_temp_dir(), 'opname_cetak_template_');
        try {
            file_put_contents($path, $response->streamedContent());
            $book = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            $this->assertSame(['Import Cetak', 'Petunjuk', 'Pengawas', 'Pengawas Cetak', 'Anak Cabut', 'Kelas Cabut'], $book->getSheetNames());
            $this->assertSame(OpnameCetakImport::HEADERS, $book->getSheet(0)->toArray()[0]);
            $this->assertSame(462, $book->getSheetByName('Pengawas Cetak')->getCell('A2')->getValue());
            $book->disconnectWorksheets();
        } finally {
            unlink($path);
        }
    }

    public function test_cetak_import_appears_in_existing_stock_and_cost_calculation(): void
    {
        // Jalankan query stok aplikasi dengan tabel pendukung kosong di SQLite.
        DB::connection()->getPdo()->sqliteCreateFunction('GREATEST', fn (...$values) => max($values));
        foreach (['eo', 'sortir'] as $name) {
            if ($name === 'sortir') {
                Schema::table($name, fn (Blueprint $table) => $table->double('cost_op')->default(0));
                continue;
            }
            Schema::table($name, function (Blueprint $table) {
                $table->double('ttl_rp')->default(0);
                $table->double('cost_op')->default(0);
            });
        }
        Schema::table('grading', fn (Blueprint $table) => $table->string('no_invoice')->nullable());
        Schema::create('grading_partai', function (Blueprint $table) {
            $table->string('no_invoice');
            $table->string('sudah_kirim');
        });
        (new OpnameCetakImport())->store([$this->cetakRow()], 'cetak', 1);
        $stock = \App\Models\CocokanModel::cetakStokBalanceDetails();
        $this->assertCount(1, $stock);
        $this->assertSame('Pengawas Cetak', $stock[0]->name);
        $this->assertEquals(100, $stock[0]->pcs);
        $this->assertEquals(500.5, $stock[0]->gr);
        $this->assertEquals(5005000, $stock[0]->ttl_rp);
        $this->assertEquals(0, $stock[0]->cost_kerja);
        $this->assertEquals(0, $stock[0]->cost_op);
        $this->assertCount(0, \App\Models\OpnameNewModel::bksisapgws());
    }

    public function test_selected_arbayah_child_is_not_replaced_if_it_changes_after_preview(): void
    {
        $this->actingAs(User::find(1));
        $this->post(route('opname_menu.preview_cabut'), [
            'status' => 'cetak', 'file' => $this->excel([$this->cetakRow()], OpnameCetakImport::HEADERS),
        ])->assertSessionHasNoErrors();
        $preview = session('opname_cabut_preview');
        DB::table('tb_anak')->where('id_anak', $preview['rows'][0]['id_anak_cabut'])->update(['berhenti' => 'Y']);
        $this->post(route('opname_menu.import_cabut'), ['token' => $preview['token']])->assertSessionHasErrors('file');
        $this->assertDatabaseCount('bk', 0);
        $this->assertDatabaseCount('cabut', 0);
        $this->assertDatabaseCount('formulir_sarang', 0);
    }

    public function test_zero_pcs_is_preserved_in_cabut_and_cetak_imports(): void
    {
        $this->actingAs(User::find(1));
        foreach ([
            ['stok', $this->row(['pcs_awal' => 0]), OpnameCabutImport::HEADERS],
            ['cetak', $this->cetakRow(['no_box' => '90002', 'pcs_awal' => 0]), OpnameCetakImport::HEADERS],
        ] as [$status, $row, $headers]) {
            $this->post(route('opname_menu.preview_cabut'), ['status' => $status, 'file' => $this->excel([$row], $headers)])
                ->assertSessionHasNoErrors();
            $this->assertSame('0', session('opname_cabut_preview.rows.0.pcs_awal'));
            $this->post(route('opname_menu.import_cabut'), ['token' => session('opname_cabut_preview.token')])
                ->assertSessionHasNoErrors();
            $this->assertDatabaseHas('bk', ['no_box' => $row['no_box'], 'pcs_awal' => 0]);
        }
        $this->assertDatabaseHas('cabut', ['no_box' => 90002, 'pcs_awal' => 0, 'pcs_akhir' => 0, 'gr_awal' => 500.5, 'gr_akhir' => 500.5]);
        $this->assertDatabaseHas('formulir_sarang', ['no_box' => '90002', 'kategori' => 'cetak', 'pcs_awal' => 0, 'gr_awal' => 500.5]);
    }

    public function test_zero_grams_and_negative_pcs_are_rejected_for_both_destinations(): void
    {
        foreach ([new OpnameCabutImport(), new OpnameCetakImport()] as $service) {
            $status = $service instanceof OpnameCetakImport ? 'cetak' : 'stok';
            foreach ([['gr_awal' => 0], ['gr_awal' => -1], ['pcs_awal' => -1]] as $changes) {
                $row = $status === 'cetak' ? $this->cetakRow($changes) : $this->row($changes);
                try {
                    $service->store([$row], $status, 1);
                    $this->fail('Invalid quantity was accepted.');
                } catch (ValidationException $exception) {
                    $this->assertDatabaseCount('bk', 0);
                    $this->assertDatabaseCount('cabut', 0);
                }
            }
        }
    }

    public function test_one_date_is_used_for_bk_cabut_and_all_forms(): void
    {
        $date = '2025-12-31';
        (new OpnameCabutImport())->store([$this->row(['tgl' => $date])], 'stok', 1);
        (new OpnameCetakImport())->store([$this->cetakRow(['no_box' => '90002', 'tgl' => $date])], 'cetak', 1);
        foreach (['90001', '90002'] as $box) {
            $this->assertDatabaseHas('bk', ['no_box' => $box, 'tgl' => $date, 'tgl_input' => $date]);
        }
        $this->assertDatabaseHas('cabut', ['no_box' => 90002, 'tgl_terima' => $date, 'tgl_serah' => $date, 'tahun_dibayar' => 2025]);
        $this->assertSame([$date], DB::table('formulir_sarang')->distinct()->pluck('tanggal')->all());
    }

    public function test_cetak_uses_class_126_even_when_child_has_a_different_class(): void
    {
        (new OpnameCetakImport())->store([$this->cetakRow(['id_anak_cabut' => 10, 'id_kelas_cabut' => 5])], 'cetak', 1);
        $this->assertDatabaseHas('cabut', ['id_anak' => 10, 'id_kelas' => 126, 'rupiah' => 0, 'ttl_rp' => 0, 'cost_op' => 0]);
        $this->assertDatabaseHas('tb_anak', ['id_anak' => 10, 'id_kelas' => 4]);
    }

    public function test_cetak_cannot_be_imported_if_class_126_is_missing(): void
    {
        DB::table('tb_kelas')->where('id_kelas', 126)->delete();
        try {
            (new OpnameCetakImport())->store([$this->cetakRow()], 'cetak', 1);
            $this->fail('Missing class 126 was accepted.');
        } catch (ValidationException $exception) {
            $this->assertDatabaseCount('bk', 0);
            $this->assertDatabaseCount('cabut', 0);
            $this->assertDatabaseCount('formulir_sarang', 0);
        }
    }

    public function test_history_records_successful_import_totals_names_and_selected_child(): void
    {
        $result = (new OpnameCetakImport())->store([
            $this->cetakRow(['pcs_awal' => 0]), $this->cetakRow(['no_box' => '90002']),
        ], 'cetak', 1, 'stock-cetak.xlsx');
        $history = \App\Models\OpnameImportHistory::findOrFail($result['history_id']);
        $this->assertSame('cetak', $history->tujuan);
        $this->assertSame('stock-cetak.xlsx', $history->file_name);
        $this->assertSame('Admin', $history->user_name);
        $this->assertEquals(2, $history->jumlah_box);
        $this->assertEquals(100, $history->total_pcs);
        $this->assertEquals(1001, $history->total_gr);
        $this->assertEquals(10010000, $history->total_rp);
        $this->assertSame('Arbayah', $history->rows[0]['pengawas_cabut_name']);
        $this->assertSame('Pengawas Cetak', $history->rows[0]['pengawas_cetak_name']);
        $this->assertEquals(126, $history->rows[0]['id_kelas_cabut']);
        $this->assertEquals(DB::table('cabut')->where('no_box', 90001)->value('id_anak'), $history->rows[0]['id_anak_cabut']);
        $this->assertSame($result['invoices'], $history->invoices['cabut']);
        $this->assertSame($result['cetak_invoices'], $history->invoices['cetak']);
        DB::table('users')->where('id', 285)->update(['name' => 'Nama diubah']);
        DB::table('tb_anak')->where('id_anak', $history->rows[0]['id_anak_cabut'])->update(['nama' => 'Anak diubah']);
        $history->refresh();
        $this->assertSame('Arbayah', $history->rows[0]['pengawas_cabut_name']);
        $this->assertSame('Anak uji', $history->rows[0]['anak_cabut_name']);
        $view = (new \App\Http\Controllers\OpnameMenuController())->history($history);
        $this->assertSame($history->id, $view->getData()['history']->id);
    }

    public function test_preview_and_replayed_import_do_not_create_extra_history(): void
    {
        $this->actingAs(User::find(1));
        $this->post(route('opname_menu.preview_cabut'), ['status' => 'stok', 'file' => $this->excel([$this->row()])])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseCount('opname_import_histories', 0);
        $token = session('opname_cabut_preview.token');
        $this->post(route('opname_menu.import_cabut'), ['token' => $token])->assertSessionHasNoErrors()->assertSessionHas('opname_history_id');
        $this->assertDatabaseCount('opname_import_histories', 1);
        $this->assertDatabaseHas('opname_import_histories', ['file_name' => 'cabut.xlsx', 'tujuan' => 'stok']);
        $this->post(route('opname_menu.import_cabut'), ['token' => $token])->assertSessionHasErrors('file');
        $this->assertDatabaseCount('opname_import_histories', 1);
    }

    public function test_history_failure_rolls_back_the_entire_import(): void
    {
        DB::unprepared("CREATE TRIGGER reject_history BEFORE INSERT ON opname_import_histories BEGIN SELECT RAISE(ABORT, 'test failure'); END;");
        try {
            (new OpnameCetakImport())->store([$this->cetakRow()], 'cetak', 1);
            $this->fail('History failure was ignored.');
        } catch (\Illuminate\Database\QueryException $exception) {
            $this->assertDatabaseCount('bk', 0);
            $this->assertDatabaseCount('cabut', 0);
            $this->assertDatabaseCount('formulir_sarang', 0);
            $this->assertDatabaseCount('opname_import_histories', 0);
        }
    }

    public function test_failed_import_has_no_history_and_history_requires_login(): void
    {
        try {
            (new OpnameCabutImport())->store([$this->row(['gr_awal' => 0])], 'stok', 1);
            $this->fail('Invalid import was accepted.');
        } catch (ValidationException $exception) {
            $this->assertDatabaseCount('opname_import_histories', 0);
        }
        $this->get(route('opname_menu.history', 1))->assertRedirect(route('login'));
        $this->actingAs(User::find(1));
        $this->get(route('opname_menu.history', 999))->assertNotFound();
    }

    public function test_history_can_be_filtered_by_destination_and_import_date(): void
    {
        (new OpnameCabutImport())->store([$this->row()], 'stok', 1);
        (new OpnameCetakImport())->store([$this->cetakRow(['no_box' => '90002'])], 'cetak', 1);
        DB::table('opname_import_histories')->where('tujuan', 'stok')->update(['created_at' => '2026-10-01 10:00:00']);
        DB::table('opname_import_histories')->where('tujuan', 'cetak')->update(['created_at' => '2026-10-09 23:59:59']);
        $this->actingAs(User::find(1));
        foreach ([
            [['history_tujuan' => 'cetak'], 1],
            [['history_dari' => '2026-10-09', 'history_sampai' => '2026-10-09'], 1],
            [['history_sampai' => '2026-10-01'], 1],
            [['history_dari' => '2026-10-10'], 0],
        ] as [$query, $count]) {
            $request = \Illuminate\Http\Request::create('/home/opname-menu', 'GET', $query);
            $this->app->instance('request', $request);
            $view = (new \App\Http\Controllers\OpnameMenuController())->index();
            $this->assertSame($count, $view->getData()['history']->total());
        }
    }
    private function sortirRow(array $changes = []): array
    {
        return $this->cetakRow(array_replace(['id_kelas_cetak' => 14, 'id_pengawas_sortir' => 459], $changes));
    }

    private function seedSortir(): void
    {
        DB::table('users')->insert(['id' => 459, 'name' => 'Pengawas Sortir', 'posisi_id' => 15]);
        DB::table('tb_anak')->insert(['id_anak' => 20, 'nama' => 'Anak Cetak uji', 'id_pengawas' => 462]);
    }

    public function test_sortir_preview_commit_records_all_stages_and_history_with_zero_cost(): void
    {
        $this->seedSortir();
        $this->actingAs(User::find(1));
        $this->post(route('opname_menu.preview_cabut'), [
            'status' => 'sortir', 'file' => $this->excel([$this->sortirRow(['pcs_awal' => 0])], OpnameSortirImport::HEADERS),
        ])->assertSessionHasNoErrors();
        $preview = session('opname_cabut_preview');
        $this->assertSame(20, $preview['rows'][0]['id_anak_cetak']);
        $this->assertDatabaseCount('bk', 0);
        $this->post(route('opname_menu.import_cabut'), ['token' => $preview['token']])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('bk', 1);
        $this->assertDatabaseCount('cabut', 1);
        $this->assertDatabaseCount('cetak_new', 1);
        $this->assertDatabaseCount('sortir', 0);
        $this->assertDatabaseCount('formulir_sarang', 3);
        foreach (['cabut' => 285, 'cetak' => 462, 'sortir' => 459] as $stage => $receiver) {
            $this->assertDatabaseHas('formulir_sarang', ['kategori' => $stage, 'id_penerima' => $receiver, 'tanggal' => '2026-10-01', 'pcs_awal' => 0, 'gr_awal' => 500.5]);
        }
        $this->assertDatabaseHas('cabut', ['id_kelas' => 126, 'id_anak' => $preview['rows'][0]['id_anak_cabut'], 'pcs_awal' => 0, 'pcs_akhir' => 0, 'gr_awal' => 500.5, 'gr_akhir' => 500.5, 'ttl_rp' => 0, 'cost_op' => 0]);
        $this->assertDatabaseHas('cetak_new', ['id_anak' => 20, 'id_pengawas' => 462, 'id_kelas_cetak' => 14, 'pcs_awal' => 0, 'pcs_akhir' => 0, 'gr_awal' => 500.5, 'gr_akhir' => 500.5, 'tgl' => '2026-10-01', 'selesai' => 'Y', 'formulir' => 'Y', 'ttl_rp' => 0, 'rp_tambahan' => 0, 'rp_satuan' => 0]);
        $history = \App\Models\OpnameImportHistory::first();
        $this->assertSame('sortir', $history->tujuan);
        $this->assertSame('Anak Cetak uji', $history->rows[0]['anak_cetak_name']);
        $this->assertSame('CTK LEWAT', $history->rows[0]['kelas_cetak_name']);
        $this->assertSame('Pengawas Sortir', $history->rows[0]['pengawas_sortir_name']);
        $this->assertCount(1, $history->invoices['sortir']);
        $this->assertEquals(5005000, $history->total_rp);
    }

    public function test_sortir_rejects_wrong_supervisor_class_or_missing_active_cetak_child(): void
    {
        $this->seedSortir();
        foreach ([['id_pengawas_sortir' => 999999], ['id_pengawas_sortir' => 1], ['id_kelas_cetak' => 999], ['id_pengawas_cetak' => 85], ['gr_awal' => 0]] as $change) {
            try {
                (new OpnameSortirImport())->store([$this->sortirRow($change)], 'sortir', 1);
                $this->fail('Invalid Sortir row accepted.');
            } catch (ValidationException $exception) {
                $this->assertDatabaseCount('bk', 0);
            }
        }
        DB::table('tb_anak')->where('id_anak', 20)->update(['berhenti' => 'Y']);
        $this->expectException(ValidationException::class);
        (new OpnameSortirImport())->store([$this->sortirRow()], 'sortir', 1);
    }

    public function test_sortir_child_change_after_preview_aborts_without_replacing_child(): void
    {
        $this->seedSortir();
        $rows = (new OpnameSortirImport())->validateRows([$this->sortirRow()], 'sortir');
        DB::table('tb_anak')->where('id_anak', 20)->update(['id_pengawas' => 285]);
        try {
            (new OpnameSortirImport())->store($rows, 'sortir', 1);
            $this->fail('Changed child accepted.');
        } catch (ValidationException $exception) {
            $this->assertDatabaseCount('bk', 0);
            $this->assertDatabaseCount('opname_import_histories', 0);
        }
    }

    public function test_sortir_write_failure_rolls_back_every_stage_and_history(): void
    {
        $this->seedSortir();
        DB::unprepared("CREATE TRIGGER fail_sortir BEFORE INSERT ON formulir_sarang WHEN NEW.kategori = 'sortir' BEGIN SELECT RAISE(ABORT, 'test failure'); END");
        try {
            (new OpnameSortirImport())->store([$this->sortirRow()], 'sortir', 1);
            $this->fail('Expected write failure.');
        } catch (\Illuminate\Database\QueryException $exception) {
            foreach (['bk', 'cabut', 'cetak_new', 'formulir_sarang', 'opname_import_histories'] as $table) {
                $this->assertDatabaseCount($table, 0);
            }
        }
    }

    public function test_sortir_template_headers_and_references_are_correct(): void
    {
        $this->seedSortir();
        $this->actingAs(User::find(1));
        $response = $this->get(route('opname_menu.template_sortir'))->assertOk();
        $path = tempnam(sys_get_temp_dir(), 'sortir_template_');
        try {
            file_put_contents($path, $response->streamedContent());
            $book = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            $this->assertSame(OpnameSortirImport::HEADERS, $book->getSheet(0)->rangeToArray('A1:L1')[0]);
            $this->assertSame('14', (string) $book->getSheetByName('Kelas Cetak')->getCell('A2')->getValue());
            $ids = array_map('intval', array_column($book->getSheetByName('Pengawas Sortir')->rangeToArray('A2:A5'), 0));
            foreach ([85, 285, 462, 459] as $id) {
                $this->assertContains($id, $ids);
            }
            $this->assertNotContains(1, $ids);
            $book->disconnectWorksheets();
        } finally {
            unlink($path);
        }
    }

    public function test_sortir_import_appears_only_in_sortir_stock_with_correct_value(): void
    {
        $this->seedSortir();
        DB::connection()->getPdo()->sqliteCreateFunction('GREATEST', fn (...$values) => max($values));
        foreach (['eo', 'sortir'] as $name) {
            if ($name === 'sortir') {
                Schema::table($name, fn (Blueprint $table) => $table->double('cost_op')->default(0));
                continue;
            }
            Schema::table($name, function (Blueprint $table) {
                $table->double('ttl_rp')->default(0);
                $table->double('cost_op')->default(0);
                $table->integer('id_anak')->default(0);
            });
        }
        Schema::table('grading', fn (Blueprint $table) => $table->string('no_invoice')->nullable());
        Schema::create('grading_partai', function (Blueprint $table) {
            $table->string('no_invoice');
            $table->string('sudah_kirim');
        });
        (new OpnameSortirImport())->store([$this->sortirRow()], 'sortir', 1);
        $stock = \App\Models\OpnameNewModel::sortir_stock();
        $this->assertCount(1, $stock);
        $this->assertSame('Pengawas Sortir', $stock[0]->name);
        $this->assertEquals(100, $stock[0]->pcs);
        $this->assertEquals(500.5, $stock[0]->gr);
        $this->assertEquals(5005000, $stock[0]->ttl_rp);
        $this->assertEquals(0, $stock[0]->cost_kerja);
        $balance = \App\Models\CocokanModel::sortir_stock_balance();
        $this->assertEquals(100, $balance->pcs);
        $this->assertEquals(500.5, $balance->gr);
        $this->assertEquals(5005000, $balance->ttl_rp);
        $this->assertEquals(0, $balance->cost_kerja);
        $this->assertEquals(0, $balance->cost_op);
        $this->assertCount(0, \App\Models\CocokanModel::cetakStokBalanceDetails());
        $this->assertCount(0, \App\Models\OpnameNewModel::bksisapgws());
    }

    public function test_sortir_accepts_supervisors_from_every_department(): void
    {
        $this->seedSortir();
        foreach ([85, 285, 462, 459] as $index => $supervisor) {
            (new OpnameSortirImport())->store([$this->sortirRow([
                'no_box' => (string) (90100 + $index), 'id_pengawas_sortir' => $supervisor,
            ])], 'sortir', 1);
            $this->assertDatabaseHas('formulir_sarang', [
                'no_box' => (string) (90100 + $index), 'kategori' => 'sortir', 'id_penerima' => $supervisor,
            ]);
        }
    }

    public function test_every_import_form_has_matching_zero_pcs_hancuran_and_replay_adds_nothing(): void
    {
        $this->seedSortir();
        foreach ([
            [new OpnameCabutImport(), 'stok', $this->row(['no_box' => '90201'])],
            [new OpnameCetakImport(), 'cetak', $this->cetakRow(['no_box' => '90202'])],
            [new OpnameSortirImport(), 'sortir', $this->sortirRow(['no_box' => '90203'])],
        ] as [$import, $status, $row]) {
            $import->store([$row], $status, 1);
            foreach (DB::table('formulir_sarang')->where('no_box', $row['no_box'])->get() as $form) {
                $this->assertDatabaseHas('tb_hancuran', [
                    'no_box' => $form->no_box, 'no_invoice' => $form->no_invoice,
                    'kategori' => $form->kategori, 'pcs' => 0,
                ]);
            }
            try {
                $import->store([$row], $status, 1);
                $this->fail('Replay accepted.');
            } catch (ValidationException $exception) {
                $this->assertSame(DB::table('formulir_sarang')->count(), DB::table('tb_hancuran')->count());
            }
        }
        $this->assertDatabaseCount('tb_hancuran', 6);
        $this->assertSame(0.0, (float) DB::table('tb_hancuran')->sum('pcs'));
    }

    public function test_hancuran_failure_rolls_back_all_import_rows_stages_and_history(): void
    {
        $this->seedSortir();
        DB::unprepared("CREATE TRIGGER fail_hancuran BEFORE INSERT ON tb_hancuran WHEN NEW.kategori = 'sortir' AND NEW.no_box = 90302 BEGIN SELECT RAISE(ABORT, 'test failure'); END");
        try {
            (new OpnameSortirImport())->store([
                $this->sortirRow(['no_box' => '90301']), $this->sortirRow(['no_box' => '90302']),
            ], 'sortir', 1);
            $this->fail('Expected hancuran failure.');
        } catch (\Illuminate\Database\QueryException $exception) {
            foreach (['bk', 'cabut', 'cetak_new', 'formulir_sarang', 'tb_hancuran', 'opname_import_histories'] as $table) {
                $this->assertDatabaseCount($table, 0);
            }
        }
    }

    private function gradeRow(array $changes = []): array
    {
        return $this->sortirRow(array_replace(['id_pengawas_grading' => 285], $changes));
    }

    public function test_grading_preview_and_commit_record_completed_sortir_and_four_forms(): void
    {
        $this->seedSortir();
        $this->actingAs(User::find(1));
        $this->post(route('opname_menu.preview_cabut'), [
            'status' => 'grade', 'file' => $this->excel([$this->gradeRow(['pcs_awal' => 0])], OpnameGradingImport::HEADERS),
        ])->assertSessionHasNoErrors();
        $preview = session('opname_cabut_preview');
        $this->assertSame(2, $preview['rows'][0]['id_anak_sortir']);
        $this->assertSame(11, $preview['rows'][0]['id_kelas_sortir']);
        $this->assertDatabaseCount('sortir', 0);
        $this->post(route('opname_menu.import_cabut'), ['token' => $preview['token']])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('sortir', [
            'no_box' => '90001', 'id_pengawas' => 459, 'id_anak' => 2, 'id_kelas' => 11,
            'tgl' => '2026-10-01', 'tgl_input' => '2026-10-01', 'tahun_dibayar' => 2026, 'bulan' => 0,
            'pcs_awal' => 0, 'pcs_akhir' => 0, 'gr_awal' => 500.5, 'gr_akhir' => 500.5,
            'ttl_rp' => 0, 'rp_target' => 0, 'denda_sp' => 0, 'selesai' => 'Y',
        ]);
        $this->assertDatabaseCount('formulir_sarang', 4);
        $this->assertDatabaseCount('tb_hancuran', 4);
        $this->assertDatabaseCount('grading', 0);
        $this->assertDatabaseHas('formulir_sarang', ['kategori' => 'grade', 'id_pemberi' => 459, 'id_penerima' => 285, 'pcs_awal' => 0, 'gr_awal' => 500.5, 'tanggal' => '2026-10-01']);
        $history = \App\Models\OpnameImportHistory::first();
        $this->assertSame('grade', $history->tujuan);
        $this->assertSame('Emo', $history->rows[0]['anak_sortir_name']);
        $this->assertSame('lewat', $history->rows[0]['kelas_sortir_name']);
        $this->assertSame('Arbayah', $history->rows[0]['pengawas_grading_name']);
        $this->assertCount(1, $history->invoices['grade']);
        foreach (DB::table('formulir_sarang')->get() as $form) {
            $this->assertDatabaseHas('tb_hancuran', ['no_box' => $form->no_box, 'no_invoice' => $form->no_invoice, 'kategori' => $form->kategori, 'pcs' => 0]);
        }
    }

    public function test_grading_hancuran_failure_rolls_back_all_previous_stages(): void
    {
        $this->seedSortir();
        DB::unprepared("CREATE TRIGGER fail_grade BEFORE INSERT ON tb_hancuran WHEN NEW.kategori = 'grade' BEGIN SELECT RAISE(ABORT, 'test failure'); END");
        try {
            (new OpnameGradingImport())->store([$this->gradeRow()], 'grade', 1);
            $this->fail('Expected write failure.');
        } catch (\Illuminate\Database\QueryException $exception) {
            foreach (['bk', 'cabut', 'cetak_new', 'sortir', 'formulir_sarang', 'tb_hancuran', 'opname_import_histories'] as $table) {
                $this->assertDatabaseCount($table, 0);
            }
        }
    }

    public function test_grading_requires_existing_sortir_defaults_and_valid_receiver(): void
    {
        $this->seedSortir();
        foreach ([['id_pengawas_grading' => 999999], ['id_pengawas_grading' => 1], ['gr_awal' => 0]] as $change) {
            try {
                (new OpnameGradingImport())->store([$this->gradeRow($change)], 'grade', 1);
                $this->fail('Invalid grading row accepted.');
            } catch (ValidationException $exception) {
                $this->assertDatabaseCount('bk', 0);
            }
        }
        DB::table('tb_kelas_sortir')->where('id_kelas', 11)->delete();
        $this->expectException(ValidationException::class);
        (new OpnameGradingImport())->store([$this->gradeRow()], 'grade', 1);
    }

    public function test_grading_template_has_all_columns_and_receiver_references(): void
    {
        $this->seedSortir();
        $this->actingAs(User::find(1));
        $response = $this->get(route('opname_menu.template_grading'))->assertOk();
        $path = tempnam(sys_get_temp_dir(), 'grade_template_');
        try {
            file_put_contents($path, $response->streamedContent());
            $book = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            $this->assertSame(OpnameGradingImport::HEADERS, $book->getSheet(0)->rangeToArray('A1:M1')[0]);
            $this->assertNotNull($book->getSheetByName('Pengawas Grading'));
            $book->disconnectWorksheets();
        } finally {
            unlink($path);
        }
    }

    public function test_grading_import_enters_ungraded_stock_with_correct_value_and_no_sortir_stock(): void
    {
        $this->seedSortir();
        DB::connection()->getPdo()->sqliteCreateFunction('GREATEST', fn (...$values) => max($values));
        Schema::table('eo', function (Blueprint $table) {
            $table->double('ttl_rp')->default(0);
            $table->double('cost_op')->default(0);
        });
        Schema::table('grading', fn (Blueprint $table) => $table->string('no_invoice')->nullable());
        Schema::create('grading_partai', function (Blueprint $table) {
            $table->string('no_invoice');
            $table->string('sudah_kirim');
        });
        (new OpnameGradingImport())->store([$this->gradeRow()], 'grade', 1);
        $stock = \App\Models\CocokanModel::gradingSisaDetails();
        $this->assertCount(1, $stock);
        $this->assertEquals(100, $stock[0]->pcs);
        $this->assertEquals(500.5, $stock[0]->gr);
        $this->assertEquals(5005000, $stock[0]->bk_rp);
        $this->assertEquals(5005000, $stock[0]->cost_bk);
        $this->assertEquals(0, $stock[0]->cost_kerja);
        $this->assertEquals(0, $stock[0]->cost_op);
        $balance = \App\Models\CocokanModel::gradingSisaOne();
        $this->assertEquals(100, $balance->pcs);
        $this->assertEquals(500.5, $balance->gr);
        $this->assertEquals(5005000, $balance->cost_bk);
        $this->assertEquals(0, $balance->cost_kerja);
        $this->assertCount(1, \App\Models\OpnameNewModel::gradingSisa());
        $this->assertCount(0, \App\Models\OpnameNewModel::sortir_stock());
        $this->assertEquals(0, \App\Models\CocokanModel::sortir_stock_balance()->gr);
        $this->assertCount(0, \App\Models\CocokanModel::cetakStokBalanceDetails());
    }

    public function test_grading_duplicate_boxes_are_rejected_before_any_stages_are_written(): void
    {
        $this->seedSortir();
        try {
            (new OpnameGradingImport())->store([$this->gradeRow(), $this->gradeRow()], 'grade', 1);
            $this->fail('Duplicate accepted.');
        } catch (ValidationException $exception) {
            $this->assertDatabaseCount('bk', 0);
            $this->assertDatabaseCount('formulir_sarang', 0);
        }
        (new OpnameGradingImport())->store([$this->gradeRow()], 'grade', 1);
        try {
            (new OpnameGradingImport())->store([$this->gradeRow()], 'grade', 1);
            $this->fail('Existing box accepted.');
        } catch (ValidationException $exception) {
            $this->assertDatabaseCount('bk', 1);
            $this->assertDatabaseCount('formulir_sarang', 4);
            $this->assertDatabaseCount('opname_import_histories', 1);
        }
    }

    public function test_history_delete_cancels_every_import_destination_and_allows_reimport(): void
    {
        $this->seedSortir();
        $this->actingAs(User::find(1));
        foreach ([
            [new OpnameCabutImport(), 'stok', $this->row()],
            [new OpnameCetakImport(), 'cetak', $this->cetakRow()],
            [new OpnameSortirImport(), 'sortir', $this->sortirRow()],
            [new OpnameGradingImport(), 'grade', $this->gradeRow()],
        ] as [$import, $status, $row]) {
            $result = $import->store([$row], $status, 1);
            $this->delete(route('opname_menu.destroy', $result['history_id']))
                ->assertSessionHasNoErrors()->assertRedirect(route('opname_menu.index'));
            foreach (['bk', 'cabut', 'cetak_new', 'sortir', 'formulir_sarang', 'tb_hancuran', 'opname_import_histories'] as $table) {
                $this->assertDatabaseCount($table, 0);
            }
            $this->assertCount(1, $import->validateRows([$row], $status));
        }
    }

    public function test_history_delete_removes_only_its_own_boxes_even_with_shared_invoice_number(): void
    {
        $this->seedSortir();
        $service = new OpnameGradingImport();
        $first = $service->store([$this->gradeRow()], 'grade', 1);
        $second = $service->store([$this->gradeRow(['no_box' => '90402'])], 'grade', 1);
        $invoice = DB::table('formulir_sarang')->where('no_box', '90001')->where('kategori', 'grade')->value('no_invoice');
        DB::table('formulir_sarang')->where('no_box', '90402')->where('kategori', 'grade')->update(['no_invoice' => $invoice]);
        DB::table('tb_hancuran')->where('no_box', '90402')->where('kategori', 'grade')->update(['no_invoice' => $invoice]);
        (new OpnameImportCancellation())->cancel($first['history_id']);
        foreach (['bk', 'cabut', 'cetak_new', 'sortir'] as $table) {
            $this->assertDatabaseCount($table, 1);
            $this->assertDatabaseHas($table, ['no_box' => '90402']);
        }
        $this->assertDatabaseCount('formulir_sarang', 4);
        $this->assertDatabaseCount('tb_hancuran', 4);
        $this->assertDatabaseHas('opname_import_histories', ['id' => $second['history_id']]);
    }

    public function test_history_delete_rejects_downstream_work_and_preserves_entire_batch(): void
    {
        $this->seedSortir();
        $this->actingAs(User::find(1));
        $result = (new OpnameSortirImport())->store([
            $this->sortirRow(), $this->sortirRow(['no_box' => '90502']),
        ], 'sortir', 1);
        DB::table('sortir')->insert(['no_box' => '90502', 'id_anak' => 2]);
        $this->delete(route('opname_menu.destroy', $result['history_id']))->assertSessionHasErrors('history');
        $this->assertDatabaseCount('bk', 2);
        $this->assertDatabaseCount('cabut', 2);
        $this->assertDatabaseCount('cetak_new', 2);
        $this->assertDatabaseCount('formulir_sarang', 6);
        $this->assertDatabaseCount('tb_hancuran', 6);
        $this->assertDatabaseCount('opname_import_histories', 1);
    }

    public function test_history_delete_rejects_grading_results_and_changed_import_amounts(): void
    {
        $this->seedSortir();
        $result = (new OpnameGradingImport())->store([$this->gradeRow()], 'grade', 1);
        DB::table('grading')->insert(['no_box_sortir' => '90001']);
        try {
            (new OpnameImportCancellation())->cancel($result['history_id']);
            $this->fail('Graded box cancelled.');
        } catch (ValidationException $exception) {
            $this->assertDatabaseCount('sortir', 1);
        }
        DB::table('grading')->delete();
        DB::table('sortir')->update(['gr_akhir' => 490]);
        try {
            (new OpnameImportCancellation())->cancel($result['history_id']);
            $this->fail('Changed amount cancelled.');
        } catch (ValidationException $exception) {
            $this->assertDatabaseCount('bk', 1);
            $this->assertDatabaseCount('opname_import_histories', 1);
        }
    }

    public function test_history_delete_failure_rolls_back_deleted_transactions_and_history(): void
    {
        $this->seedSortir();
        $result = (new OpnameGradingImport())->store([$this->gradeRow()], 'grade', 1);
        DB::unprepared("CREATE TRIGGER fail_cancel BEFORE DELETE ON bk BEGIN SELECT RAISE(ABORT, 'test failure'); END");
        try {
            (new OpnameImportCancellation())->cancel($result['history_id']);
            $this->fail('Expected deletion failure.');
        } catch (\Illuminate\Database\QueryException $exception) {
            foreach (['bk', 'cabut', 'cetak_new', 'sortir', 'opname_import_histories'] as $table) {
                $this->assertDatabaseCount($table, 1);
            }
            $this->assertDatabaseCount('formulir_sarang', 4);
            $this->assertDatabaseCount('tb_hancuran', 4);
        }
    }

    public function test_history_delete_requires_login_and_repeated_delete_does_nothing(): void
    {
        $result = (new OpnameCabutImport())->store([$this->row()], 'stok', 1);
        $this->delete(route('opname_menu.destroy', $result['history_id']))->assertRedirect(route('login'));
        $this->assertDatabaseCount('bk', 1);
        $this->actingAs(User::find(1));
        $this->delete(route('opname_menu.destroy', $result['history_id']))->assertSessionHasNoErrors();
        $this->delete(route('opname_menu.destroy', $result['history_id']))->assertNotFound();
        $this->assertDatabaseCount('bk', 0);
    }

    public function test_history_before_transaction_id_snapshots_can_be_cancelled_without_hancuran(): void
    {
        $this->seedSortir();
        $result = (new OpnameCetakImport())->store([$this->cetakRow()], 'cetak', 1);
        $history = \App\Models\OpnameImportHistory::find($result['history_id']);
        $rows = $history->rows;
        unset($rows[0]['_transaction_ids']);
        $history->update(['rows' => $rows]);
        DB::table('tb_hancuran')->delete();
        (new OpnameImportCancellation())->cancel($history->id);
        foreach (['bk', 'cabut', 'formulir_sarang', 'opname_import_histories'] as $table) {
            $this->assertDatabaseCount($table, 0);
        }
    }

    public function test_history_delete_rejects_replaced_transaction_ids_and_legacy_related_work(): void
    {
        $result = (new OpnameCabutImport())->store([$this->row()], 'stok', 1);
        DB::table('tb_hancuran')->update(['id' => 99]);
        try {
            (new OpnameImportCancellation())->cancel($result['history_id']);
            $this->fail('Replaced transaction accepted.');
        } catch (ValidationException $exception) {
            $this->assertDatabaseCount('bk', 1);
        }
        DB::table('tb_hancuran')->update(['id' => 1]);
        Schema::create('grade', fn (Blueprint $table) => $table->string('no_box'));
        DB::table('grade')->insert(['no_box' => '90001']);
        try {
            (new OpnameImportCancellation())->cancel($result['history_id']);
            $this->fail('Legacy grading work accepted.');
        } catch (ValidationException $exception) {
            $this->assertDatabaseCount('bk', 1);
            $this->assertDatabaseCount('grade', 1);
            $this->assertDatabaseCount('opname_import_histories', 1);
        }
    }

    public function test_bk_preview_commit_records_only_unassigned_bk_with_zero_pcs_and_price(): void
    {
        $this->actingAs(User::find(1));
        $row = $this->row(['pcs_awal' => 0, 'hrga_satuan' => 0]);
        $this->post(route('opname_menu.preview_cabut'), [
            'status' => 'bk', 'file' => $this->excel([$row], OpnameBkImport::HEADERS),
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('bk', 0);
        $preview = session('opname_cabut_preview');
        $this->assertSame(0, $preview['rows'][0]['id_pengawas']);
        $this->post(route('opname_menu.import_cabut'), ['token' => $preview['token']])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('bk', ['no_box' => '90001', 'penerima' => 0, 'formulir' => 'T', 'invoice_formulir' => '', 'kategori' => 'cabut', 'baru' => 'baru', 'pcs_awal' => 0, 'gr_awal' => 500.5, 'hrga_satuan' => 0, 'ttl_rp' => 0, 'tgl' => '2026-10-01', 'tgl_input' => '2026-10-01']);
        foreach (['cabut', 'cetak_new', 'sortir', 'formulir_sarang', 'tb_hancuran'] as $table) {
            $this->assertDatabaseCount($table, 0);
        }
        $history = \App\Models\OpnameImportHistory::first();
        $this->assertSame('bk', $history->tujuan);
        $this->assertEquals(0, $history->total_rp);
        $this->assertSame([], $history->invoices['cabut']);
        $this->delete(route('opname_menu.destroy', $history->id))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('bk', 0);
        $this->assertDatabaseCount('opname_import_histories', 0);
    }

    public function test_bk_import_appears_in_existing_bk_sisa_summary_and_details(): void
    {
        (new OpnameBkImport())->store([$this->row()], 'bk', 1);
        $details = \App\Models\OpnameNewModel::bkSisaDetails();
        $this->assertCount(1, $details);
        $this->assertEquals(100, $details[0]->pcs);
        $this->assertEquals(500.5, $details[0]->gr);
        $this->assertEquals(5005000, $details[0]->ttl_rp);
        $summary = \App\Models\CocokanModel::summarybk_sisa();
        $this->assertEquals(100, $summary->pcs);
        $this->assertEquals(500.5, $summary->gr);
        $this->assertEquals(5005000, $summary->ttl_rp);
        Schema::table('grading', fn (Blueprint $table) => $table->string('no_invoice')->nullable());
        Schema::create('grading_partai', function (Blueprint $table) {
            $table->string('no_invoice');
            $table->string('sudah_kirim');
        });
        $this->assertCount(0, \App\Models\OpnameNewModel::bksisapgws());
        (new OpnameCabutImport())->store([$this->row(['no_box' => '90702'])], 'stok', 1);
        $cabutStock = \App\Models\OpnameNewModel::bksisapgws();
        $this->assertCount(1, $cabutStock);
        $this->assertEquals('90702', $cabutStock[0]->no_box);
    }

    public function test_bk_template_contains_only_stock_columns_and_instructions(): void
    {
        $this->actingAs(User::find(1));
        $response = $this->get(route('opname_menu.template_bk'))->assertOk();
        $path = tempnam(sys_get_temp_dir(), 'bk_template_');
        try {
            file_put_contents($path, $response->streamedContent());
            $book = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            $this->assertSame(OpnameBkImport::HEADERS, $book->getSheet(0)->rangeToArray('A1:I1')[0]);
            $this->assertSame(['Import BK Sisa', 'Petunjuk'], $book->getSheetNames());
            $book->disconnectWorksheets();
        } finally {
            unlink($path);
        }
    }

    public function test_bk_zero_grams_and_duplicate_boxes_abort_the_entire_import(): void
    {
        foreach ([[$this->row(['gr_awal' => 0])], [$this->row(), $this->row()]] as $rows) {
            try {
                (new OpnameBkImport())->store($rows, 'bk', 1);
                $this->fail('Invalid BK import accepted.');
            } catch (ValidationException $exception) {
                $this->assertDatabaseCount('bk', 0);
                $this->assertDatabaseCount('opname_import_histories', 0);
            }
        }
    }

    public function test_bk_cancellation_rejects_subsequent_handover_to_supervisor(): void
    {
        $result = (new OpnameBkImport())->store([$this->row()], 'bk', 1);
        DB::table('bk')->update(['penerima' => 85, 'formulir' => 'Y']);
        try {
            (new OpnameImportCancellation())->cancel($result['history_id']);
            $this->fail('Assigned BK cancelled.');
        } catch (ValidationException $exception) {
            $this->assertDatabaseCount('bk', 1);
            $this->assertDatabaseCount('opname_import_histories', 1);
        }
    }

}
