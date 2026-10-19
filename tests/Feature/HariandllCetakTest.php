<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HariandllCetakTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('users', function (Blueprint $t) {
            $t->increments('id');
            $t->string('name');
            $t->integer('posisi_id');
        });
        Schema::create('tb_anak', function (Blueprint $t) {
            $t->increments('id_anak');
            $t->string('nama');
            $t->integer('id_pengawas');
        });
        Schema::create('tb_hariandll', function (Blueprint $t) {
            $t->increments('id_hariandll');
            $t->integer('id_anak');
            $t->integer('id_pengawas');
            $t->string('ket')->nullable();
            $t->string('tgl')->nullable();
            $t->double('rupiah')->default(0);
            $t->string('lokasi')->nullable();
            $t->string('ditutup')->default('T');
            $t->integer('bulan_dibayar')->default(0);
            $t->integer('tahun_dibayar')->default(2026);
            $t->double('pcs')->nullable()->default(0);
            $t->double('gr')->nullable()->default(0);
            $t->string('kategori')->nullable();
            $t->string('no_box')->nullable();
        });
        DB::table('users')->insert(['id' => 2, 'name' => 'Pengawas', 'posisi_id' => 13]);
        DB::table('tb_anak')->insert(['id_anak' => 1, 'nama' => 'Anak A', 'id_pengawas' => 2]);
        $this->actingAs(User::find(2));
    }

    public function test_tbh_baris_cetak_memuat_input_pcs_dan_gr(): void
    {
        $this->get(route('hariandll.tbh_baris', ['count' => 2, 'kategori' => 'cetak']))
            ->assertOk()
            ->assertSee('name="pcs[]"', false)
            ->assertSee('name="gr[]"', false);
    }

    public function test_tbh_baris_biasa_tanpa_input_pcs_dan_gr(): void
    {
        $this->get(route('hariandll.tbh_baris', ['count' => 2, 'kategori' => 'biasa']))
            ->assertOk()
            ->assertDontSee('name="pcs[]"', false)
            ->assertDontSee('name="gr[]"', false);
    }

    public function test_create_cetak_menyimpan_pcs_gr_dan_kategori(): void
    {
        $this->post(route('hariandll.create'), [
            'kategori' => 'cetak', 'bulan_dibayar' => 10,
            'tgl' => ['2026-10-16'], 'no_box' => ['5002'], 'id_anak' => [1],
            'ket' => ['cetak box'], 'pcs' => ['50'], 'gr' => ['240.5'],
            'lokasi' => ['AGA'], 'rupiah' => ['1,000'],
        ])->assertSessionHasNoErrors()
            ->assertRedirect(route('hariandll.index', ['kategori' => 'cetak']));

        $this->assertDatabaseHas('tb_hariandll', [
            'kategori' => 'cetak', 'no_box' => '5002', 'pcs' => 50, 'gr' => 240.5, 'rupiah' => 1000,
        ]);
    }

    public function test_create_biasa_tersimpan_dengan_kategori_biasa(): void
    {
        $this->post(route('hariandll.create'), [
            'kategori' => 'biasa', 'bulan_dibayar' => 10,
            'tgl' => ['2026-10-16'], 'no_box' => [''], 'id_anak' => [1],
            'ket' => ['kerja harian'],
            'lokasi' => ['AGA'], 'rupiah' => ['500'],
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertDatabaseHas('tb_hariandll', ['kategori' => 'biasa', 'pcs' => 0, 'gr' => 0]);
    }

    public function test_update_cetak_kembali_ke_tab_cetak(): void
    {
        $id = DB::table('tb_hariandll')->insertGetId([
            'id_anak' => 1, 'id_pengawas' => 2, 'kategori' => 'cetak',
            'tgl' => '2026-10-16', 'bulan_dibayar' => 10,
        ]);

        $this->post(route('hariandll.update'), [
            'kategori' => 'cetak', 'id_hariandll' => [$id],
            'tgl' => ['2026-10-17'], 'id_anak' => [1], 'ket' => ['revisi'],
            'bulan_dibayar' => [10], 'rupiah' => ['1,500'], 'lokasi' => ['AGA'],
        ])->assertSessionHasNoErrors()
            ->assertRedirect(route('hariandll.index', ['kategori' => 'cetak']));
    }
}
