<?php

namespace Tests\Feature;

use App\Http\Controllers\CocokanController;
use App\Services\PengirimanDetailExport;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class PengirimanDetailExportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('pengiriman_packing_list', function (Blueprint $table) {
            $table->increments('id_packing');
            $table->string('no_nota');
            $table->string('nm_packing');
            $table->integer('id_pengiriman');
        });
        Schema::create('pengiriman', function (Blueprint $table) {
            $table->increments('id_pengiriman');
            $table->string('no_nota');
            $table->string('no_box');
            $table->string('grade')->default('A');
            $table->string('tgl_input')->default('2026-10-09');
            foreach (['pcs', 'gr', 'ttl_rp', 'cost_bk', 'cost_kerja', 'cost_cu', 'cost_op'] as $field) {
                $table->double($field)->default(0);
            }
        });
        Schema::create('grading_partai', function (Blueprint $table) {
            $table->increments('id_grading');
            $table->string('box_pengiriman');
            $table->string('nm_partai');
            $table->string('no_invoice')->default('1001');
            $table->string('grade');
            $table->string('tipe')->default('BULAT');
            $table->string('tgl')->default('2026-10-09');
            foreach (['pcs', 'gr', 'ttl_rp', 'cost_bk', 'cost_kerja', 'cost_cu', 'cost_op'] as $field) {
                $table->double($field)->default(0);
            }
        });
        // Packing list menyimpan nomor box pada kolom id_pengiriman, bukan primary key pengiriman.
        DB::table('pengiriman_packing_list')->insert([
            ['no_nota' => '1040', 'nm_packing' => 'Herry', 'id_pengiriman' => 90001],
            ['no_nota' => '1040', 'nm_packing' => 'Herry', 'id_pengiriman' => 90002],
            ['no_nota' => '1040', 'nm_packing' => 'Herry', 'id_pengiriman' => 90001],
            ['no_nota' => '1041', 'nm_packing' => 'Lain', 'id_pengiriman' => 90003],
        ]);
        DB::table('pengiriman')->insert([
            ['no_nota' => '1040', 'no_box' => '90001', 'pcs' => 10, 'gr' => 100],
            ['no_nota' => '1040', 'no_box' => '90001', 'pcs' => 5, 'gr' => 50],
            ['no_nota' => '1040', 'no_box' => '90002', 'pcs' => 0, 'gr' => 80],
            ['no_nota' => '1041', 'no_box' => '90003', 'pcs' => 20, 'gr' => 200],
        ]);
        DB::table('grading_partai')->insert([
            ['box_pengiriman' => '90001', 'nm_partai' => '=PARTAI', 'grade' => 'A1', 'pcs' => 10, 'gr' => 100],
            ['box_pengiriman' => '90001', 'nm_partai' => 'Partai B', 'grade' => 'A2', 'pcs' => 5, 'gr' => 50],
            ['box_pengiriman' => '90003', 'nm_partai' => 'Tidak dipilih', 'grade' => 'B', 'pcs' => 20, 'gr' => 200],
        ]);
    }

    public function test_counts_use_shipment_nota_and_details_are_not_multiplied_by_metadata_or_barcodes(): void
    {
        $service = new PengirimanDetailExport();
        $this->assertEquals(2, $service->packingLists()->firstWhere('no_nota', '1040')->jumlah_box);
        $rows = $service->details(['1040']);
        $this->assertCount(3, $rows);
        $this->assertCount(2, $rows->pluck('box_pengiriman')->unique());
        $this->assertEquals(230, $rows->sum('gr'));
        $this->assertEquals(15, $rows->sum('pcs'));
        $this->assertNotContains('90003', $rows->pluck('box_pengiriman')->all());
        $this->assertCount(4, $service->details(['1040', '1041']));
    }

    public function test_workbook_preserves_zero_values_and_correct_grade_type_columns(): void
    {
        $request = Request::create('/home/cocokan/pengiriman/export-detail', 'GET', ['no_nota' => ['1040']]);
        $this->app->instance('request', $request);
        $response = (new CocokanController())->exportDetailPengiriman($request);
        ob_start();
        $response->sendContent();
        $contents = ob_get_clean();
        $path = tempnam(sys_get_temp_dir(), 'pengiriman_export_');
        try {
            file_put_contents($path, $contents);
            $book = IOFactory::load($path);
            $sheet = $book->getSheet(0);
            $this->assertEquals(4, $sheet->getHighestDataRow());
            $this->assertSame('A1', $sheet->getCell('E2')->getValue());
            $this->assertSame('BULAT', $sheet->getCell('F2')->getValue());
            $this->assertSame('=PARTAI', $sheet->getCell('C2')->getValue());
            $this->assertSame('s', $sheet->getCell('C2')->getDataType());
            $this->assertEquals(0, $sheet->getCell('G4')->getValue());
            $this->assertEquals(80, $sheet->getCell('H4')->getValue());
            $book->disconnectWorksheets();
        } finally {
            unlink($path);
        }
    }
}
