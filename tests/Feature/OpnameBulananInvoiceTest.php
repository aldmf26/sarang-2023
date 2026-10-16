<?php

namespace Tests\Feature;

use App\Http\Controllers\SummaryController;
use App\Models\OpnameBulananInvoice;
use App\Models\User;
use App\Services\OpnameBulananPosting;
use App\Services\OpnameBulananSource;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OpnameBulananInvoiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        // Seluruh pengujian menggunakan database terpisah, tanpa transaksi pada database pengguna.
        $tables = [
            'bk' => ['id_bk', 'tgl no_lot no_box tipe ket warna pengawas penerima kategori nm_partai selesai formulir invoice_formulir baru pgws_grade tgl_input', 'pcs_awal gr_awal susut ttl_rp hrga_satuan'],
            'cabut' => ['id_cabut', 'no_box tgl_terima tgl_serah selesai penutup formulir invoice ket_hcr', 'id_pengawas id_anak id_kelas bulan_dibayar tahun_dibayar pcs_awal gr_awal pcs_akhir gr_akhir pcs_hcr gr_hcr pcs_flx gr_flx eot rupiah ttl_rp cost_op'],
            'eo' => ['id_eo', 'no_box tgl_ambil tgl_serah tgl_input selesai penutup', 'id_pengawas id_anak id_kelas bulan_dibayar tahun_dibayar gr_eo_awal gr_eo_akhir rp_target ttl_rp cost_op'],
            'cetak_new' => ['id_cetak', 'no_box tgl selesai capai formulir penutup', 'id_pemberi id_pengawas id_anak pcs_awal gr_awal pcs_tdk_cetak gr_tdk_cetak pcs_awal_ctk gr_awal_ctk id_kelas_cetak rp_satuan pcs_akhir pcs_hcr gr_akhir ttl_rp rp_tambahan bulan_dibayar tahun_dibayar tipe_bayar'],
            'sortir' => ['id_sortir', 'no_box tgl tgl_input selesai penutup', 'id_kelas bulan tahun_dibayar id_anak id_pengawas pcs_awal gr_awal pcs_tdk_sortir gr_tdk_sortir pcs_akhir gr_akhir rp_target ttl_rp denda_sp pcuc pcus'],
            'grading' => ['id_grading', 'no_invoice tgl admin selesai', 'no_box_sortir no_box_grading pcs gr id_grade'],
            'grading_partai' => ['id_grading', 'nm_partai no_invoice box_pengiriman grade tipe tgl admin formulir cek_qc sudah_kirim sudah_print not_oke', 'urutan pcs gr cost ttl_rp cost_bk cost_kerja cost_cu cost_op bulan tahun'],
            'formulir_sarang' => ['id_formulir', 'no_invoice no_box tanggal kategori selesai print', 'id_pemberi id_penerima pcs_awal gr_awal sst_aktual'],
            'pengiriman' => ['id_pengiriman', 'tgl_input no_box no_barcode cek_qc admin no_nota grade selesai baru cost_op_cek', 'pcs gr rp_gram ttl_rp cost_bk cost_kerja cost_cu cost_op'],
            'pengiriman_packing_list' => ['id_packing', 'tgl nm_packing pgws_cek no_nota no_invoice_manual selesai tujuan', 'id_pengiriman kadar'],
            'qc' => ['id', 'box_pengiriman tgl wip2 invoice_qc selesai', 'pcs_awal gr_awal gr_akhir bulan_dibayar tahun_dibayar'],
        ];
        foreach ($tables as $table => [$key, $strings, $numbers]) {
            Schema::create($table, function (Blueprint $t) use ($key, $strings, $numbers) {
                $t->increments($key);
                foreach (explode(' ', $strings) as $column) {
                    $t->string($column)->nullable(in_array($column, ['warna', 'tipe', 'ket', 'invoice', 'tgl_serah']))
                        ->default(in_array($column, ['selesai', 'formulir', 'penutup', 'cek_qc', 'sudah_kirim', 'sudah_print', 'not_oke']) ? 'T' : '');
                }
                foreach (explode(' ', $numbers) as $column) {
                    if (in_array($column, ['no_box_sortir', 'no_box_grading'])) {
                        $t->integer($column)->nullable()->default(0);
                    } else {
                        $t->double($column)->nullable()->default(0);
                    }
                }
            });
        }
        Schema::create('users', function (Blueprint $t) {
            $t->increments('id');
            $t->string('name');
            $t->integer('posisi_id');
        });
        Schema::create('kelas_cetak', function (Blueprint $t) {
            $t->increments('id_kelas_cetak');
            $t->string('kategori');
        });
        Schema::create('tb_posisi', function (Blueprint $t) {
            $t->increments('id_posisi');
            $t->string('nm_posisi');
        });
        Schema::create('navbar', function (Blueprint $t) {
            $t->string('route');
            $t->integer('urutan');
        });
        Schema::create('oprasional', function (Blueprint $t) {
            $t->integer('bulan');
            $t->integer('tahun');
            foreach (['rp_oprasional', 'rp_gr', 'gr', 'total_operasional'] as $c) {
                $t->double($c)->default(0);
            }
        });
        (require database_path('migrations/2026_10_14_000001_create_opname_bulanan_invoices.php'))->up();
        (require database_path('migrations/2026_10_14_000002_add_nm_partai_to_opname_bulanan_invoices.php'))->up();
        DB::table('users')->insert([
            ['id' => 1, 'name' => 'Owner', 'posisi_id' => 1],
            ['id' => 2, 'name' => 'Petugas', 'posisi_id' => 13],
            ['id' => 85, 'name' => 'Pengawas', 'posisi_id' => 13],
            ['id' => 86, 'name' => 'Pengawas lain', 'posisi_id' => 14],
        ]);
        DB::table('kelas_cetak')->insert(['id_kelas_cetak' => 14, 'kategori' => 'CTK']);
        DB::table('tb_posisi')->insert(['id_posisi' => 1, 'nm_posisi' => 'Owner']);
        $this->actingAs(User::find(2));
    }

    private function box(string $number = '5002'): void
    {
        DB::table('bk')->insert(['no_box' => $number, 'kategori' => 'cabut', 'baru' => 'baru',
            'penerima' => '85', 'formulir' => 'Y', 'nm_partai' => 'Partai A', 'ket' => 'A',
            'pcs_awal' => 10, 'gr_awal' => 250, 'hrga_satuan' => 40, 'ttl_rp' => 10000]);
        DB::table('cabut')->insert(['no_box' => $number, 'id_pengawas' => 85, 'pcs_awal' => 10,
            'gr_awal' => 250, 'pcs_akhir' => 10, 'gr_akhir' => 250, 'ttl_rp' => 5000, 'cost_op' => 5000,
            'selesai' => 'Y', 'bulan_dibayar' => 10, 'tahun_dibayar' => 2026]);
    }

    private function invoice(string $position = 'cabut'): OpnameBulananInvoice
    {
        return OpnameBulananInvoice::create(['no_invoice' => 'OPN-TEST-'.uniqid(), 'bulan' => '2026-10-01',
            'tanggal' => '2026-10-14', 'posisi' => $position, 'nm_partai' => 'Partai A', 'tujuan' => 'Joe',
            'pengawas_id' => 85, 'created_by' => 2, 'status' => 'draft']);
    }

    private function line($invoice, string $box = '5002', string $baris = '1-1', array $changes = []): void
    {
        $this->post(route('opname_bulanan.line', $invoice->id), $changes + [
            'baris' => $baris, 'no_box' => $box, 'pcs_real' => 10, 'gr_real' => 250, 'grade_real' => 'A',
        ])->assertSessionHasNoErrors()->assertRedirect();
    }

    private function lockInvoice($invoice): OpnameBulananInvoice
    {
        $this->post(route('opname_bulanan.submit', $invoice->id))->assertSessionHasNoErrors()->assertRedirect();
        $this->actingAs(User::find(85))->post(route('opname_bulanan.lock', $invoice->id))
            ->assertSessionHasNoErrors()->assertRedirect();

        return $invoice->fresh(['lines']);
    }

    public function test_lock_posts_one_shipment_and_reopens_full_quantity_with_capitalized_cost(): void
    {
        $this->box();
        DB::table('bk')->insert(['no_box' => '25003', 'kategori' => 'cetak']);
        DB::table('pengiriman')->insert(['no_box' => '30000', 'no_nota' => '1000']);
        $invoice = $this->invoice();
        $this->line($invoice);
        $invoice = $this->lockInvoice($invoice);
        $line = $invoice->lines->first();
        $this->assertSame('locked', $invoice->status);
        $this->assertSame('5003', $line->no_box_baru);
        $this->assertDatabaseHas('bk', ['no_box' => $line->no_box_baru, 'pcs_awal' => 10, 'gr_awal' => 250,
            'ttl_rp' => 20000, 'hrga_satuan' => 80, 'penerima' => '85']);
        $this->assertDatabaseMissing('cabut', ['no_box' => $line->no_box_baru]);
        $this->assertDatabaseHas('formulir_sarang', ['no_box' => $line->no_box_baru, 'kategori' => 'cabut', 'selesai' => 'T']);
        $this->assertDatabaseHas('grading', ['no_box_sortir' => 5002, 'no_box_grading' => (int) $line->no_box_kirim]);
        $this->assertDatabaseHas('pengiriman', ['no_nota' => $invoice->no_pengiriman, 'pcs' => 10, 'gr' => 250, 'selesai' => 'Y', 'cost_op' => 5000]);
        $this->assertDatabaseHas('pengiriman_packing_list', ['no_nota' => $invoice->no_pengiriman, 'tujuan' => 'Joe']);
        $this->assertEquals(20000, $line->modal_baru);
        $this->assertDatabaseHas('cabut', ['no_box' => '5002', 'opname_cost_locked' => 1]);
        $count = DB::table('bk')->count();
        $this->post(route('opname_bulanan.lock', $invoice->id))->assertSessionHasNoErrors();
        $this->assertEquals($count, DB::table('bk')->count());
        $this->assertDatabaseCount('pengiriman', 2);
        $details = app(\App\Services\PengirimanDetailExport::class)->details([$invoice->no_pengiriman]);
        $this->assertCount(1, $details);
        $this->assertSame('Joe', $details->first()->tujuan);
        $this->assertEquals(10, $details->sum('pcs'));
    }

    public function test_partai_can_be_renamed_during_opname_and_is_used_by_the_new_stock(): void
    {
        $this->box();
        $invoice = $this->invoice();
        $this->line($invoice);
        $this->patch(route('opname_bulanan.update', $invoice->id), [
            'tanggal' => '2026-10-14', 'nm_partai' => '  BJM 1013  ', 'tujuan' => 'Joe',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertSame('BJM 1013', $invoice->fresh()->nm_partai);
        $line = $invoice->lines()->first()->fresh();
        $this->assertSame('BJM 1013', $line->nm_partai);

        $invoice = $this->lockInvoice($invoice);
        $line = $invoice->lines->first();
        $this->assertDatabaseHas('bk', ['no_box' => $line->no_box_baru, 'nm_partai' => 'BJM 1013']);
        $this->assertDatabaseHas('grading_partai', ['box_pengiriman' => $line->no_box_kirim, 'nm_partai' => 'BJM 1013']);
    }

    public function test_same_grade_boxes_merge_with_correct_form_totals_and_one_shipment(): void
    {
        $this->box('5002');
        $this->box('5003');
        $invoice = $this->invoice();
        $this->line($invoice, '5002', '1-1');
        $this->line($invoice, '5003', '1-2');
        $invoice = $this->lockInvoice($invoice);
        $this->assertCount(1, $invoice->lines->pluck('no_box_kirim')->unique());
        $this->assertCount(2, $invoice->lines->pluck('no_box_baru')->unique());
        $this->assertDatabaseHas('pengiriman', ['no_nota' => $invoice->no_pengiriman, 'pcs' => 20, 'gr' => 500]);
        $this->assertDatabaseHas('formulir_sarang', ['no_box' => $invoice->lines->first()->no_box_kirim,
            'kategori' => 'wip', 'pcs_awal' => 20, 'gr_awal' => 500]);
    }

    public function test_error_rolls_back_box_creation_and_shipment_together(): void
    {
        $this->box();
        $invoice = $this->invoice();
        $this->line($invoice);
        $this->post(route('opname_bulanan.submit', $invoice->id))->assertSessionHasNoErrors();
        DB::unprepared("CREATE TRIGGER reject_shipping BEFORE INSERT ON pengiriman BEGIN SELECT RAISE(ABORT, 'shipping failure'); END;");
        try {
            app(OpnameBulananPosting::class)->lock($invoice->id, User::find(85));
            $this->fail('Posting harus dibatalkan.');
        } catch (\Illuminate\Database\QueryException $error) {
            $this->assertStringContainsString('shipping failure', $error->getMessage());
        }
        $this->assertDatabaseCount('bk', 1);
        $this->assertDatabaseCount('pengiriman', 0);
        $this->assertDatabaseCount('grading_partai', 0);
        $this->assertSame('submitted', $invoice->fresh()->status);
        $this->assertNull($invoice->lines()->first()->no_box_baru);
        $this->assertDatabaseHas('cabut', ['no_box' => '5002', 'opname_cost_locked' => 0]);
    }

    public function test_changed_source_is_rejected_and_only_assigned_supervisor_can_lock(): void
    {
        $this->box();
        $invoice = $this->invoice();
        $this->line($invoice);
        $this->post(route('opname_bulanan.submit', $invoice->id))->assertSessionHasNoErrors();
        $this->post(route('opname_bulanan.lock', $invoice->id))->assertForbidden();
        $this->actingAs(User::find(86))->post(route('opname_bulanan.lock', $invoice->id))->assertForbidden();
        DB::table('cabut')->where('no_box', '5002')->update(['cost_op' => 6000]);
        $this->actingAs(User::find(85))->post(route('opname_bulanan.lock', $invoice->id))->assertSessionHasErrors('opname');
        $this->assertDatabaseCount('bk', 1);
        $this->assertDatabaseCount('pengiriman', 0);
    }

    public function test_actual_counts_are_used_and_box_cannot_be_reserved_twice(): void
    {
        $this->box();
        $invoice = $this->invoice();
        $this->line($invoice, changes: ['pcs_real' => 8, 'gr_real' => 200, 'grade_real' => 'B']);
        $this->post(route('opname_bulanan.submit', $invoice->id))->assertSessionHasNoErrors();
        $second = $this->invoice();
        $this->post(route('opname_bulanan.line', $second->id), ['baris' => '1-1', 'no_box' => '5002', 'nm_partai' => 'Partai A', 'pcs_real' => 10, 'gr_real' => 250, 'grade_real' => 'A'])->assertSessionHasErrors('opname');
        $this->actingAs(User::find(85))->post(route('opname_bulanan.lock', $invoice->id))->assertSessionHasNoErrors();
        $line = $invoice->lines()->first();
        $this->assertDatabaseHas('bk', ['no_box' => $line->no_box_baru, 'pcs_awal' => 8, 'gr_awal' => 200, 'ket' => 'B', 'hrga_satuan' => 100]);
    }

    public function test_wip_box_reopens_in_wip_with_all_costs_as_bk_only(): void
    {
        DB::table('grading_partai')->insert(['box_pengiriman' => '13026', 'no_invoice' => '11055',
            'nm_partai' => 'Partai A', 'grade' => 'A', 'tipe' => 'A', 'pcs' => 10, 'gr' => 250,
            'cost_bk' => 10000, 'cost_kerja' => 5000, 'cost_op' => 5000, 'formulir' => 'Y']);
        DB::table('formulir_sarang')->insert(['no_box' => '13026', 'no_invoice' => '1001', 'kategori' => 'wip', 'id_penerima' => 85]);
        $invoice = $this->invoice('wip1');
        $invoice->update(['nm_partai' => 'BJM 1013']);
        $this->line($invoice, '13026');
        $invoice = $this->lockInvoice($invoice);
        $line = $invoice->lines->first();
        $this->assertSame('13026', $line->no_box_kirim);
        $this->assertDatabaseHas('grading_partai', ['box_pengiriman' => '13026', 'nm_partai' => 'BJM 1013']);
        $this->assertDatabaseHas('bk', ['no_box' => $line->no_box_baru, 'nm_partai' => 'BJM 1013']);
        $this->assertDatabaseHas('grading_partai', ['box_pengiriman' => $line->no_box_baru,
            'cost_bk' => 20000, 'cost_kerja' => 0, 'cost_op' => 0, 'formulir' => 'Y', 'cek_qc' => 'T', 'sudah_kirim' => 'T']);
        $this->assertDatabaseHas('grading_partai', ['box_pengiriman' => '13026', 'sudah_kirim' => 'Y', 'opname_cost_locked' => 1]);
        $newSource = app(OpnameBulananSource::class)->read($line->no_box_baru, 'wip1');
        $this->assertEquals(20000, $newSource['cost_bk']);
        $this->assertEquals(0, $newSource['cost_op']);
    }

    public function test_frozen_operational_cost_survives_monthly_reallocation(): void
    {
        $this->box();
        $this->box('5003');
        $invoice = $this->invoice();
        $this->line($invoice);
        $invoice = $this->lockInvoice($invoice);
        $this->actingAs(User::find(1));
        $request = \Illuminate\Http\Request::create('/cost', 'POST', ['bulan' => 10, 'tahun' => 2026, 'biaya_oprasional' => 25000]);
        $request->setLaravelSession(app('session')->driver());
        app(SummaryController::class)->saveoprasional($request);
        $this->assertDatabaseHas('cabut', ['no_box' => '5002', 'cost_op' => 5000]);
        $this->assertDatabaseHas('cabut', ['no_box' => '5003', 'cost_op' => 10000]);
        $this->assertDatabaseHas('grading_partai', ['box_pengiriman' => $invoice->lines->first()->no_box_kirim, 'cost_op' => 5000]);
        $this->assertDatabaseHas('bk', ['no_box' => $invoice->lines->first()->no_box_baru, 'ttl_rp' => 20000]);
    }

    public function test_lookup_hides_costs_and_invoice_pages_render(): void
    {
        $this->box();
        $invoice = $this->invoice();
        $this->getJson(route('opname_bulanan.source', ['invoice' => $invoice->id, 'no_box' => '5002']))
            ->assertOk()->assertJsonPath('pcs', 10)->assertJsonMissingPath('cost_bk')->assertJsonMissingPath('rows');
        $this->get(route('opname_menu.bulanan', ['bulan' => '2026-10']))->assertOk()->assertSee('Buat invoice opname');
        $this->get(route('opname_bulanan.show', $invoice->id))->assertOk()->assertSee('Tambah baris box')
            ->assertSee('data-opname-ajax-save', false)->assertSee('initOpnamePage', false)
            ->assertSee('alert alert-secondary mb-0 fw-semibold', false)
            ->assertSee('header-box-tools', false)->assertSee('Cek Box')
            ->assertSee('queueBoxLookup', false)
            ->assertSee('inputmode="numeric"', false)
            ->assertSee('Data box akan diambil otomatis.');
        $this->line($invoice);
        $this->get(route('opname_bulanan.show', $invoice->id))->assertOk()
            ->assertSee('opname-line-row', false)->assertSee('data-label="Box lama"', false)
            ->assertSee('@media (max-width: 767.98px)', false);
        $this->get(route('opname_bulanan.print', $invoice->id))->assertOk()
            ->assertSee('PO OPNAME BULANAN')->assertSee($invoice->no_invoice)->assertSee('1-1')
            ->assertSee('>250<', false)->assertDontSee('250,00');
    }

    public function test_lookup_state_machine_guards_save_until_box_is_found(): void
    {
        $this->box();
        $invoice = $this->invoice();
        $this->get(route('opname_bulanan.show', $invoice->id))->assertOk()
            ->assertSee('id="opname-save"', false)
            ->assertSee('setLookupState', false)
            ->assertSee('setFormReady(false)', false)
            ->assertSee('Menunggu nomor box selesai diketik', false)
            ->assertSee('Mencari box', false)
            ->assertSee('lookupVersion', false)
            ->assertSee('foundBox', false)
            ->assertSee('spinner-border', false);
        // Box duplikat tetap ditolak dengan nomor baris yang jelas.
        $this->line($invoice, baris: '2-3');
        $this->getJson(route('opname_bulanan.source', ['invoice' => $invoice->id, 'no_box' => '5002']))
            ->assertUnprocessable()->assertJsonPath('errors.opname.0', 'Box 5002 sudah ada pada baris 2-3 di invoice ini.');
        // Format salah dan box 9999 tetap ditolak backend.
        $this->getJson(route('opname_bulanan.source', ['invoice' => $invoice->id, 'no_box' => '9999']))
            ->assertUnprocessable();
        $this->getJson(route('opname_bulanan.source', ['invoice' => $invoice->id, 'no_box' => '0']))
            ->assertUnprocessable();
    }

    public function test_compact_card_header_metric_and_signed_deltas(): void
    {
        $this->box('5002');
        $this->box('5003');
        $this->box('5004');
        $invoice = $this->invoice();
        $this->line($invoice, '5002', '1-1');
        $this->line($invoice, '5003', '1-2', ['pcs_real' => 8, 'gr_real' => 200]);
        $this->line($invoice, '5004', '1-3', ['pcs_real' => 12, 'gr_real' => 300]);
        $this->get(route('opname_bulanan.show', $invoice->id))->assertOk()
            ->assertSee('opname-delta', false)
            ->assertSee('opname-delta-zero', false)
            ->assertSee('>-2<', false)
            ->assertSee('>-50<', false)
            ->assertSee('>+2<', false)
            ->assertSee('>+50<', false)
            ->assertSee('opname-mobile-metrics', false)
            ->assertSee('opname-mobile-metric-label', false)
            ->assertSee('content: "BOX"', false)
            ->assertSee('opname-line-row', false);
    }

    public function test_compact_detail_disclosure_toggle(): void
    {
        $this->box();
        $invoice = $this->invoice();
        $this->line($invoice);
        $this->get(route('opname_bulanan.show', $invoice->id))->assertOk()
            ->assertSee('data-opname-detail-toggle', false)
            ->assertSee('opname-mobile-card-toggle', false)
            ->assertSee('aria-expanded="false"', false)
            ->assertSee('Tampilkan detail box 5002', false)
            ->assertDontSee('Lihat detail')
            ->assertDontSee('opname-mobile-detail-control', false);
        // Kontrol disclosure tidak tampil pada hasil cetak.
        $this->get(route('opname_bulanan.print', $invoice->id))->assertOk()
            ->assertDontSee('data-opname-detail-toggle', false)
            ->assertDontSee('opname-mobile-card-toggle', false);
        // Pengguna read-only tetap mendapat disclosure tanpa tombol edit.
        $this->actingAs(User::find(85))->get(route('opname_bulanan.show', $invoice->id))->assertOk()
            ->assertSee('data-opname-detail-toggle', false)
            ->assertDontSee('Hapus baris', false);
    }

    public function test_action_row_detail_edit_delete(): void
    {
        $this->box();
        $invoice = $this->invoice();
        $this->line($invoice);
        $this->get(route('opname_bulanan.show', $invoice->id))->assertOk()
            ->assertSee('opname-action-detail', false)
            ->assertSee('>Detail<', false)
            ->assertSee('window._opnamePerPage = parseInt(perpage.value, 10)', false)
            ->assertSee('Hapus baris 1-1 dari draft?', false);
        // Read-only tidak melihat baris aksi.
        $this->actingAs(User::find(85))->get(route('opname_bulanan.show', $invoice->id))->assertOk()
            ->assertDontSee('opname-action-detail"', false)
            ->assertDontSee('Hapus baris', false);
        // Baris aksi tidak tampil pada hasil cetak.
        $this->get(route('opname_bulanan.print', $invoice->id))->assertOk()
            ->assertDontSee('opname-action-detail"', false);
    }

    public function test_lookup_rejects_a_box_already_listed_in_the_same_invoice(): void
    {
        $this->box();
        $invoice = $this->invoice();
        $this->line($invoice, baris: '2-3');

        $this->getJson(route('opname_bulanan.source', ['invoice' => $invoice->id, 'no_box' => '5002']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('opname')
            ->assertJsonPath('errors.opname.0', 'Box 5002 sudah ada pada baris 2-3 di invoice ini.');
    }

    public function test_an_alert_is_shown_after_each_group_of_twenty_lines(): void
    {
        $this->box();
        $invoice = $this->invoice();
        $this->line($invoice);
        $template = $invoice->lines()->first();
        foreach (range(2, 20) as $sequence) {
            $line = $template->replicate();
            $line->baris = '1-'.$sequence;
            $line->no_box = (string) (5001 + $sequence);
            $line->save();
        }

        $this->get(route('opname_bulanan.show', $invoice->id))->assertOk()
            ->assertSee('Urutan 1–20 pada kelompok 1 sudah lengkap')
            ->assertSee('baris 1-21');
    }

    public function test_next_line_number_continues_from_the_last_manually_selected_group(): void
    {
        $this->box();
        $invoice = $this->invoice();
        $this->line($invoice, baris: '2-1');

        $this->get(route('opname_bulanan.show', $invoice->id))->assertOk()
            ->assertSee('name="baris"', false)
            ->assertSee('value="2-2"', false);
    }

    public function test_capital_details_are_only_visible_after_invoice_is_locked(): void
    {
        $this->box();
        $invoice = $this->invoice();
        $this->line($invoice);
        $this->actingAs(User::find(1))->get(route('opname_bulanan.show', $invoice->id))
            ->assertOk()->assertDontSee('Modal awal box baru');

        $this->actingAs(User::find(2));
        $this->lockInvoice($invoice);
        $this->actingAs(User::find(1))->get(route('opname_bulanan.show', $invoice->id))
            ->assertOk()->assertSee('Modal awal box baru');
    }

    public function test_creator_can_delete_a_draft_invoice_with_all_its_lines(): void
    {
        $this->box();
        $invoice = $this->invoice();
        $this->line($invoice);

        $this->delete(route('opname_bulanan.destroy', $invoice->id), ['bulan' => '2026-10'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('sukses', 'Invoice draft berhasil dihapus.')
            ->assertRedirect(route('opname_menu.bulanan', ['bulan' => '2026-10']));

        $this->assertDatabaseMissing('opname_bulanan_invoices', ['id' => $invoice->id]);
        $this->assertDatabaseMissing('opname_bulanan_lines', ['invoice_id' => $invoice->id]);
    }

    public function test_submitted_invoice_and_another_users_draft_cannot_be_deleted(): void
    {
        $invoice = $this->invoice();
        $invoice->update(['status' => 'submitted']);

        $this->delete(route('opname_bulanan.destroy', $invoice->id))->assertStatus(409);
        $this->assertDatabaseHas('opname_bulanan_invoices', ['id' => $invoice->id]);

        $invoice->update(['status' => 'draft']);
        $this->actingAs(User::find(86))
            ->delete(route('opname_bulanan.destroy', $invoice->id))->assertForbidden();
        $this->assertDatabaseHas('opname_bulanan_invoices', ['id' => $invoice->id]);
    }

    public static function stages(): array
    {
        return array_map(fn ($stage) => [$stage], ['bk', 'cetak', 'sortir', 'grading', 'hasil_grading', 'wip2']);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('stages')]
    public function test_replacement_is_visible_in_the_same_stage(string $stage): void
    {
        if (in_array($stage, ['hasil_grading', 'wip2'])) {
            DB::table('grading_partai')->insert(['box_pengiriman' => '5002', 'no_invoice' => '11055',
                'nm_partai' => 'Partai A', 'grade' => 'A', 'tipe' => 'A', 'pcs' => 10, 'gr' => 250,
                'cost_bk' => 10000, 'cost_kerja' => 5000, 'cost_op' => 5000,
                'formulir' => $stage === 'wip2' ? 'Y' : 'T', 'cek_qc' => $stage === 'wip2' ? 'Y' : 'T']);
        } else {
            $this->box();
            if ($stage === 'bk') {
                DB::table('cabut')->delete();
                DB::table('bk')->update(['penerima' => '0', 'formulir' => 'T']);
            } else {
                DB::table('formulir_sarang')->insert(['no_box' => '5002', 'no_invoice' => '1001',
                    'kategori' => $stage === 'grading' ? 'grade' : $stage, 'id_penerima' => 85,
                    'pcs_awal' => 10, 'gr_awal' => 250]);
            }
        }
        $invoice = $this->invoice($stage);
        $this->line($invoice);
        $invoice = $this->lockInvoice($invoice);
        $data = app(OpnameBulananSource::class)->read($invoice->lines->first()->no_box_baru, $stage);
        $this->assertEquals(10, $data['pcs']);
        $this->assertEquals(250, $data['gr']);
        $this->assertEquals($stage === 'bk' ? 10000 : 20000, $data['cost_bk']);
        $this->assertEquals(0, $data['cost_kerja']);
        $this->assertEquals(0, $data['cost_op']);
    }

    public function test_return_to_draft_allows_correction_and_duplicate_rows_are_rejected(): void
    {
        $this->box();
        $invoice = $this->invoice();
        $this->line($invoice);
        $this->post(route('opname_bulanan.line', $invoice->id), ['baris' => '1-2', 'no_box' => '5002', 'nm_partai' => 'Partai A', 'pcs_real' => 10, 'gr_real' => 250, 'grade_real' => 'A'])->assertSessionHasErrors('opname');
        $this->post(route('opname_bulanan.submit', $invoice->id))->assertSessionHasNoErrors();
        $this->post(route('opname_bulanan.line', $invoice->id), ['baris' => '1-2', 'no_box' => '5002', 'nm_partai' => 'Partai A', 'pcs_real' => 10, 'gr_real' => 250, 'grade_real' => 'A'])->assertStatus(409);
        $this->actingAs(User::find(85))->post(route('opname_bulanan.return', $invoice->id), ['alasan' => 'Hitung ulang gram'])->assertSessionHasNoErrors();
        $this->actingAs(User::find(2));
        $line = $invoice->lines()->first();
        $this->line($invoice, changes: ['line_id' => $line->id, 'gr_real' => 200]);
        $this->assertDatabaseHas('opname_bulanan_lines', ['id' => $line->id, 'gr_real' => 200, 'harga_baru' => 100]);
    }

    public function test_partial_grading_invoice_keeps_remaining_operational_cost_without_duplication(): void
    {
        $this->box();
        DB::table('grading')->insert(['no_box_sortir' => 5002, 'no_invoice' => '11055', 'pcs' => 10, 'gr' => 250]);
        foreach (['13001' => 2000, '13002' => 3000] as $box => $cost) {
            DB::table('grading_partai')->insert(['box_pengiriman' => $box, 'no_invoice' => '11055',
                'nm_partai' => 'Partai A', 'grade' => 'A', 'tipe' => 'A', 'pcs' => 10, 'gr' => 250,
                'cost_bk' => 10000, 'cost_kerja' => 5000, 'cost_op' => $cost, 'formulir' => 'Y']);
        }
        $invoice = $this->invoice('wip1');
        $this->line($invoice, '13001');
        $this->lockInvoice($invoice);
        $method = new \ReflectionMethod(SummaryController::class, 'syncCostOperasionalToGradingPartai');
        $method->invoke(app(SummaryController::class));
        $this->assertDatabaseHas('grading_partai', ['box_pengiriman' => '13001', 'cost_op' => 2000]);
        $this->assertDatabaseHas('grading_partai', ['box_pengiriman' => '13002', 'cost_op' => 3000]);
    }

    public function test_next_opname_adds_only_costs_of_the_replacement_box(): void
    {
        $this->box();
        $first = $this->invoice();
        $this->line($first);
        $first = $this->lockInvoice($first);
        $newBox = $first->lines->first()->no_box_baru;
        DB::table('cabut')->insert(['no_box' => $newBox, 'id_pengawas' => 85, 'pcs_awal' => 10,
            'gr_awal' => 250, 'pcs_akhir' => 10, 'gr_akhir' => 250, 'ttl_rp' => 5000, 'cost_op' => 3000, 'selesai' => 'Y']);
        $this->actingAs(User::find(2));
        $second = $this->invoice();
        $this->line($second, $newBox);
        $second = $this->lockInvoice($second);
        $this->assertDatabaseHas('bk', ['no_box' => $second->lines->first()->no_box_baru,
            'ttl_rp' => 28000, 'hrga_satuan' => 112]);
        $this->assertDatabaseHas('bk', ['no_box' => $newBox, 'ttl_rp' => 20000]);
    }

    public function test_uncounted_cetak_filters_owner_and_ignores_boxes_in_draft(): void
    {
        foreach (['5002', '5003', '5004', '5005'] as $box) {
            $this->box($box);
            if ($box !== '5004') {
                DB::table('formulir_sarang')->insert(['no_box' => $box, 'kategori' => 'cetak', 'id_penerima' => $box === '5005' ? 86 : 85, 'pcs_awal' => 10, 'gr_awal' => 250]);
            }
        }
        $invoice = $this->invoice('cetak');
        $this->line($invoice);
        $query = app(\App\Services\OpnameBulananUncounted::class)->query('2026-10', ['posisi' => 'cetak', 'pengawas_id' => 85]);
        $this->assertSame(['5003'], $query->pluck('stock.no_box')->all());
        $this->get(route('opname_menu.bulanan.belum_opname', ['bulan' => '2026-10', 'posisi' => 'cetak', 'pengawas_id' => 85]))
            ->assertOk()->assertSee('Stok belum diopname')->assertSee('5003');
    }

    public function test_uncounted_excludes_replacement_only_in_its_opname_period(): void
    {
        $this->box();
        $invoice = $this->invoice();
        $this->line($invoice);
        $invoice = $this->lockInvoice($invoice);
        $stock = app(\App\Services\OpnameBulananUncounted::class);
        $this->assertCount(0, $stock->query('2026-10')->get());
        $this->assertSame([$invoice->lines->first()->no_box_baru], $stock->query('2026-11')->pluck('stock.no_box')->all());
    }

    public function test_uncounted_shows_actual_process_quantity_and_partial_grading_remainder(): void
    {
        $this->box('5002');
        $this->box('5003');
        DB::table('cetak_new')->insert(['no_box' => '5002', 'id_kelas_cetak' => 14, 'id_pengawas' => 85,
            'pcs_awal_ctk' => 8, 'gr_awal_ctk' => 200, 'selesai' => 'T']);
        DB::table('formulir_sarang')->insert(['no_box' => '5003', 'kategori' => 'grade', 'id_penerima' => 85, 'pcs_awal' => 10, 'gr_awal' => 250]);
        DB::table('grading')->insert(['no_box_sortir' => 5003, 'no_invoice' => '11055', 'pcs' => 4, 'gr' => 100]);
        $rows = app(\App\Services\OpnameBulananUncounted::class)->query('2026-10')->get()->keyBy('no_box');
        $this->assertEquals(8, $rows['5002']->pcs);
        $this->assertEquals(200, $rows['5002']->gr);
        $this->assertEquals(6, $rows['5003']->pcs);
        $this->assertEquals(150, $rows['5003']->gr);
        $this->assertStringContainsString('Grading sebagian', $rows['5003']->issue);
    }
}
