<?php

namespace App\Http\Controllers;

use App\Models\Cabut;
use App\Models\CabutOpnameModel;
use App\Models\OpnameNewModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class OpnameSusutController extends Controller
{
    public function index(Request $request)
    {
        $pgws_cabut = CabutOpnameModel::cabut_susut();
        $data = [
            'title' => 'Data Opname',
            'pgws_cabut' => $pgws_cabut,

        ];
        return view('home.opnamesusut.index', $data);
    }


    public function detail_cabut(Request $r)
    {
        $data = [
            'title' => 'Data Opname',
            'tipe' => $r->tipe,
            'nm_pengawas' => DB::table('users')->where('id', $r->id_pengawas)->first()->name,
            'box_stock' => CabutOpnameModel::cabut_susut_detail($r->id_pengawas, $r->tipe),
        ];
        return view('home.opnamesusut.detail_cabut', $data);
    }

    public function cetak(Request $request)
    {
        $pgws_cabut = CabutOpnameModel::cabut_susut();
        $data = [
            'title' => 'Data Opname',
            'pgws_cabut' => $pgws_cabut,

        ];
        return view('home.opnamesusut.cetak', $data);
    }


    public function costPartai(Request $r)
    {
        $bk = DB::select("SELECT a.nm_partai FROM bk as a  where a.kategori = 'cabut' and a.baru = 'baru' and a.no_box != 9999 group by a.nm_partai order by a.nm_partai ASC;");
        $data = [
            'title' => 'Cost Partai',
            'partai' => $bk,
        ];
        return view('home.opnamesusut.cost_partai', $data);
    }


    public function getCostpartai(Request $r)
    {
        $bk = DB::selectOne("SELECT a.nm_partai, a.tipe, a.ket, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.gr_awal * a.hrga_satuan) as ttl_rp
        FROM bk as a 
        where a.kategori = 'cabut' and a.baru = 'baru' and a.nm_partai = '$r->partai';");

        $data = [
            'bk' => $bk,
            'cabut' => CabutOpnameModel::cabutPartai($r->partai),
            'eo' => CabutOpnameModel::eotPartai($r->partai),
            'cetak' => CabutOpnameModel::cetakPartai($r->partai),
            'sortir' => CabutOpnameModel::sortirPartai($r->partai),
            'grading' => CabutOpnameModel::gradingPartai($r->partai),
        ];
        return view('home.opnamesusut.getcost_partai', $data);
    }
}
