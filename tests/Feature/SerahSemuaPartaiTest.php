<?php

namespace Tests\Feature;

use App\Http\Middleware\CekPosisiUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SerahSemuaPartaiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Semua fixture berada dalam SQLite memori, terpisah dari database aplikasi.
        config(['database.default' => 'serah_test', 'database.connections.serah_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        $pdo = DB::connection()->getPdo();
        $pdo->sqliteCreateFunction('GREATEST', fn (...$values) => max($values));
        $pdo->sqliteCreateFunction('regexp', fn ($pattern, $value) => preg_match('/' . $pattern . '/', (string) $value));
        foreach ([
            'users' => 'id INTEGER, name TEXT, posisi_id INTEGER',
            'formulir_sarang' => 'no_box TEXT, kategori TEXT, no_invoice TEXT, pcs_awal REAL, gr_awal REAL, tanggal TEXT, id_pemberi INTEGER, id_penerima INTEGER, selesai TEXT',
            'bk' => 'no_box TEXT, kategori TEXT, baru TEXT, nm_partai TEXT, tipe TEXT, ket TEXT, pcs_awal REAL, gr_awal REAL, hrga_satuan REAL',
            'grading' => 'no_box_sortir TEXT, no_invoice TEXT, pcs REAL, gr REAL, admin TEXT, tgl TEXT',
            'grading_partai' => 'no_invoice TEXT, sudah_kirim TEXT, formulir TEXT, cek_qc TEXT, grade TEXT, nm_partai TEXT, urutan INTEGER,
                bulan INTEGER, tahun INTEGER, tipe TEXT, pcs REAL, gr REAL, tgl TEXT, admin TEXT,
                box_pengiriman TEXT, ttl_rp REAL, cost_bk REAL, cost_kerja REAL, cost_cu REAL, cost_op REAL, not_oke TEXT, sudah_print TEXT',
            'pengiriman' => 'no_box TEXT',
            'qc' => 'box_pengiriman TEXT, wip2 TEXT',
            'cabut' => 'no_box TEXT, ttl_rp REAL, cost_op REAL',
            'eo' => 'no_box TEXT, ttl_rp REAL, cost_op REAL',
            'cetak_new' => 'no_box TEXT, ttl_rp REAL, id_kelas_cetak INTEGER, selesai TEXT, id_anak INTEGER, pcs_awal_ctk REAL, gr_awal_ctk REAL',
            'kelas_cetak' => 'id_kelas_cetak INTEGER, kategori TEXT',
            'sortir' => 'no_box TEXT, ttl_rp REAL',
            'tb_grade' => 'id_grade INTEGER, nm_grade TEXT, status TEXT, tipe TEXT',
            'tb_hancuran' => 'no_box TEXT, pcs REAL, kategori TEXT',
            'balance_sheet_closings' => 'tahun INTEGER, bulan INTEGER, cumulative_cost_baseline REAL',
        ] as $table => $columns) {
            DB::statement("CREATE TABLE {$table} ({$columns})");
        }
        $this->withoutMiddleware(CekPosisiUser::class);
    }

    private function loginAsPosition(int $position): void
    {
        $this->actingAs((new User())->forceFill(['id' => 77, 'name' => 'Tester', 'posisi_id' => $position]));
    }

    private function box(string $box, string $partai): void
    {
        DB::table('bk')->insert(['no_box' => $box, 'kategori' => 'cabut', 'baru' => 'baru',
            'nm_partai' => $partai, 'tipe' => 'test', 'ket' => 'test',
            'pcs_awal' => 5, 'gr_awal' => 100, 'hrga_satuan' => 1000]);
        DB::table('formulir_sarang')->insert(['no_box' => $box, 'kategori' => 'grade',
            'no_invoice' => '1000', 'pcs_awal' => 5, 'gr_awal' => 100]);
    }

    public function test_non_president_cannot_submit_even_directly(): void
    {
        $this->loginAsPosition(15);
        $this->box('101', 'BJM A');
        $this->post(route('gradingbj.serah_semua_partai'))->assertForbidden();
        $this->assertSame(0, DB::table('formulir_sarang')->where('kategori', 'grading')->count());
    }

    public function test_button_is_only_visible_for_president(): void
    {
        $template = file_get_contents(resource_path('views/home/gradingbj/index.blade.php'));
        preg_match("/@role\('presiden'\).*?Serah Semua per Partai.*?@endrole/s", $template, $matches);
        $this->assertNotEmpty($matches);
        $this->loginAsPosition(15);
        $this->assertStringNotContainsString('Serah Semua per Partai',
            \Illuminate\Support\Facades\Blade::render($matches[0], ['formulir' => [(object) []]]));
        $this->loginAsPosition(1);
        $this->assertStringContainsString('Serah Semua per Partai',
            \Illuminate\Support\Facades\Blade::render($matches[0], ['formulir' => [(object) []]]));
    }

    public function test_all_boxes_are_grouped_and_repeat_does_not_create_duplicates(): void
    {
        $this->loginAsPosition(1);
        $this->box('101', 'BJM A');
        $this->box('102-T', 'bjm a');
        $this->box('103', 'BJM B');
        $this->box('0', 'BJM A');
        DB::table('formulir_sarang')->insert(['no_box' => '0', 'kategori' => 'grading', 'no_invoice' => '12000']);
        DB::table('grading')->insert(['no_box_sortir' => 'old', 'no_invoice' => '13000']);

        $response = $this->post(route('gradingbj.serah_semua_partai'))->assertRedirect();
        $this->assertNull(session('error'), (string) session('error'));
        $response->assertSessionHas('sukses');
        $created = DB::table('formulir_sarang')->where('kategori', 'grading')->where('no_box', '!=', '0')->get();
        $this->assertCount(3, $created);
        $this->assertSame(['13001', '13002'], $created->pluck('no_invoice')->unique()->sort()->values()->all());
        $this->assertSame(1, $created->whereIn('no_box', ['101', '102-T'])->pluck('no_invoice')->unique()->count());
        $this->assertEquals(15, $created->sum('pcs_awal'));
        $this->assertEquals(300, $created->sum('gr_awal'));
        foreach ($created as $row) {
            $this->assertSame(77, $row->id_pemberi);
            $this->assertSame(77, $row->id_penerima);
            $this->assertSame(date('Y-m-d'), $row->tanggal);
        }
        $this->post(route('gradingbj.serah_semua_partai'))->assertSessionHas('error');
        $this->assertSame(4, DB::table('formulir_sarang')->where('kategori', 'grading')->count());
    }

    public function test_failure_in_later_party_rolls_back_earlier_party(): void
    {
        $this->loginAsPosition(1);
        $this->box('101', 'A');
        $this->box('102', 'B');
        // Satu box punya dua partai, sehingga validasi Serah harus menolaknya.
        DB::table('bk')->insert(['no_box' => '102', 'kategori' => 'cabut', 'baru' => 'baru',
            'nm_partai' => 'C', 'pcs_awal' => 0, 'gr_awal' => 0, 'hrga_satuan' => 0]);
        $this->post(route('gradingbj.serah_semua_partai'))->assertSessionHas('error');
        $this->assertSame(0, DB::table('formulir_sarang')->where('kategori', 'grading')->count());
    }

    public function test_manual_serah_keeps_the_same_fields(): void
    {
        $this->loginAsPosition(15);
        $this->box('101-T', 'A');
        $this->post(route('gradingbj.grading_partai'), ['no_box' => '101-T', 'submit' => 'serah'])
            ->assertRedirect()->assertSessionHas('sukses');
        $row = DB::table('formulir_sarang')->where('kategori', 'grading')->first();
        $this->assertSame('101-T', $row->no_box);
        $this->assertEquals(5, $row->pcs_awal);
        $this->assertEquals(100, $row->gr_awal);
        $this->assertSame('11055', $row->no_invoice);
        $this->assertSame(77, $row->id_pemberi);
    }

    public function test_large_invoice_loads_all_boxes_from_short_url(): void
    {
        $this->loginAsPosition(1);
        $this->box('unrelated', 'Other');
        $bk = [];
        $formulir = [];
        for ($i = 1; $i <= 1195; $i++) {
            $box = (string) (20000 + $i);
            $bk[] = ['no_box' => $box, 'kategori' => 'cabut', 'baru' => 'baru',
                'nm_partai' => 'BJM 1008', 'tipe' => 'test', 'ket' => 'test',
                'pcs_awal' => 5, 'gr_awal' => 100, 'hrga_satuan' => 1000];
            foreach (['grade', 'grading'] as $kategori) {
                $formulir[] = ['no_box' => $box, 'kategori' => $kategori, 'no_invoice' => '14036',
                    'pcs_awal' => 5, 'gr_awal' => 100];
            }
        }
        foreach (array_chunk($bk, 100) as $chunk) {
            DB::table('bk')->insert($chunk);
        }
        foreach (array_chunk($formulir, 100) as $chunk) {
            DB::table('formulir_sarang')->insert($chunk);
        }
        $url = route('gradingbj.grading_partai_result', ['no_invoice' => '14036']);
        $this->assertLessThan(200, strlen($url));
        $request = \Illuminate\Http\Request::create($url, 'GET', ['no_box' => 'unrelated']);
        $view = $this->app->make(\App\Http\Controllers\GradingBjController::class)->gradingPartaiResult($request);
        $this->assertInstanceOf(\Illuminate\View\View::class, $view);
        $this->assertCount(1195, $view->getData()['getFormulir']);
        $this->assertSame('14036', $view->getData()['no_invoice']);
        $this->assertSame('BJM 1008', $view->getData()['nm_partai']);
        $this->assertNotContains('unrelated', array_column($view->getData()['getFormulir'], 'no_box'));
    }

    public function test_save_receives_large_grading_payload_and_uses_all_invoice_sources(): void
    {
        $this->loginAsPosition(1);
        DB::table('tb_grade')->insert(['id_grade' => 1, 'nm_grade' => 'VK', 'status' => 'bentuk', 'tipe' => 'test']);
        $payload = ['no_nota' => '14036', 'nm_partai' => 'BJM 1008', 'bulan' => '10',
            'grade' => [], 'pcs' => [], 'gr' => [], 'box_sp' => [], 'not_oke' => ['1194' => 'on']];
        for ($i = 1; $i <= 1195; $i++) {
            $box = (string) (20000 + $i);
            $this->box($box, 'BJM 1008');
            DB::table('formulir_sarang')->insert(['no_box' => $box, 'kategori' => 'grading',
                'no_invoice' => '14036', 'pcs_awal' => 5, 'gr_awal' => 100]);
            $payload['grade'][] = 'VK';
            $payload['pcs'][] = '5';
            $payload['gr'][] = '100';
            $payload['box_sp'][] = (string) (90000 + $i);
        }
        $this->post(route('gradingbj.create_partai'), ['grading_payload' => json_encode($payload)])
            ->assertRedirect()->assertSessionHas('sukses');
        $this->assertSame(1195, DB::table('grading')->where('no_invoice', '14036')->count());
        $this->assertSame(1195, DB::table('grading_partai')->where('no_invoice', '14036')->count());
        $this->assertEquals(119500, DB::table('grading_partai')->sum('gr'));
        $this->assertEquals(119500000, DB::table('grading_partai')->sum('cost_bk'));
        $this->assertSame('Y', DB::table('grading_partai')->where('box_pengiriman', '91195')->value('not_oke'));
        $this->post(route('gradingbj.create_partai'), ['grading_payload' => json_encode($payload)])
            ->assertSessionHas('error');
        $this->assertSame(1195, DB::table('grading_partai')->count());
    }

    public function test_incomplete_grading_submission_shows_error_without_saving(): void
    {
        $this->loginAsPosition(1);
        $this->post(route('gradingbj.create_partai'), [
            'no_nota' => '14036', 'nm_partai' => 'BJM 1008', 'bulan' => '10',
        ])->assertRedirect()->assertSessionHas('error');
        $this->assertSame(0, DB::table('grading')->count());
        $this->assertSame(0, DB::table('grading_partai')->count());
    }

    public function test_wip1_balance_excludes_susut_including_case_and_spaces(): void
    {
        foreach (['VK' => 100, ' SuSuT ' => 7495] as $grade => $gr) {
            DB::table('grading_partai')->insert(['grade' => $grade, 'box_pengiriman' => $grade,
                'formulir' => 'Y', 'cek_qc' => 'T', 'sudah_kirim' => 'T', 'pcs' => 0, 'gr' => $gr,
                'cost_bk' => $gr * 1000, 'cost_kerja' => 0, 'cost_op' => 0]);
        }
        $row = \App\Models\CocokanModel::sisa_belum_wip1();
        $this->assertEquals(100, $row->gr);
        $this->assertEquals(100000, $row->ttl_rp);
        $this->assertSame(2, DB::table('grading_partai')->count());
    }

    public function test_renamed_completed_qc_box_is_counted_and_available_without_old_po_reference(): void
    {
        foreach (['14281-1', 'pending', 'shipped', 'susut', 'qc-pending', 'normal'] as $box) {
            DB::table('grading_partai')->insert(['box_pengiriman' => $box, 'nm_partai' => 'A',
                'grade' => $box === 'susut' ? ' SuSuT ' : 'VK', 'formulir' => 'Y',
                'cek_qc' => 'Y', 'sudah_kirim' => 'T', 'pcs' => 121, 'gr' => 364,
                'cost_bk' => 3711072.6294269, 'cost_kerja' => 283233.19386332, 'cost_op' => 290197.14116918]);
        }
        DB::table('formulir_sarang')->insert([
            ['no_box' => '14281', 'kategori' => 'wip2', 'selesai' => 'Y'],
            ['no_box' => 'pending', 'kategori' => 'wip2', 'selesai' => 'T'],
            ['no_box' => 'normal', 'kategori' => 'wip2', 'selesai' => 'Y'],
        ]);
        DB::table('pengiriman')->insert(['no_box' => 'shipped']);
        DB::table('qc')->insert(['box_pengiriman' => 'qc-pending', 'wip2' => 'T']);

        $expected = ['14281-1', 'normal'];
        $this->assertEqualsCanonicalizing($expected, array_column(\App\Models\Grading::stock_wip2(), 'no_box'));
        $this->assertEqualsCanonicalizing($expected, array_column(\App\Models\OpnameNewModel::wip2SedangProses(), 'box_pengiriman'));
        $summary = \App\Models\CocokanModel::wip2proses();
        $this->assertEqualsWithDelta(8569005.9289188, $summary->ttl_rp, 0.001);
        $this->assertEquals(242, $summary->pcs);
        $details = collect(\App\Models\OpnameNewModel::pengirimanBelumKirimDetails())->where('is_wip2', 1);
        $this->assertEqualsCanonicalizing($expected, $details->pluck('box_pengiriman')->all());
    }

    public function test_pending_cetak_without_class_still_counts_its_source_modal(): void
    {
        $this->box('14401', 'A');
        DB::table('bk')->where('no_box', '14401')->update(['pcs_awal' => 26, 'gr_awal' => 189, 'hrga_satuan' => 9000]);
        DB::table('cetak_new')->insert(['no_box' => '14401', 'id_kelas_cetak' => 0,
            'selesai' => 'T', 'id_anak' => 193, 'pcs_awal_ctk' => 26, 'gr_awal_ctk' => 189, 'ttl_rp' => 0]);
        $before = \App\Models\CocokanModel::cetak_proses_balance();
        $this->assertEquals(1701000, $before->ttl_rp);
        $this->assertEquals(26, $before->pcs);
        DB::table('cetak_new')->where('no_box', '14401')->update(['selesai' => 'Y']);
        $this->assertEquals(0, (float) \App\Models\CocokanModel::cetak_proses_balance()->ttl_rp);
    }

    public function test_balance_totals_do_not_add_susut_cost_back(): void
    {
        $data = array_fill_keys(['bk', 'bk_sisa', 'uang_cost', 'cbt_proses', 'cbt_sisa_pgws',
            'cetak_proses', 'cetak_sisa', 'sedang_proses', 'sortir_sisa', 'grading_sisa',
            'grading_proses', 'sisa_belum_wip1', 'sisa_belum_qc', 'wip2proses', 'pengiriman_proses',
            'pengiriman'], (object) []);
        foreach (['bk', 'uang_cost', 'cabut_selesai_siap_cetak', 'cetak_selesai', 'sortir_selesai'] as $field) {
            $data[$field] = [];
        }
        $data['grading_proses'] = (object) ['cost_bk' => 1000];
        $data['grading_susut'] = (object) ['cost_bk' => 5000, 'cost_kerja' => 100, 'cost_op' => 50, 'cost_cu' => 20];
        $controller = $this->app->make(\App\Http\Controllers\CocokanController::class);
        $method = new \ReflectionMethod($controller, 'calculateBalanceCost');
        $summary = $method->invoke($controller, $data);
        $this->assertEquals(1000, $summary['total_bk_rp']);
        $rowsMethod = new \ReflectionMethod($controller, 'buildBalanceRows');
        $rows = $rowsMethod->invoke($controller, $data);
        $this->assertEquals(1000, collect($rows)->sum('total'));
    }
}
