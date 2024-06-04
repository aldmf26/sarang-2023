<?php

namespace App\Http\Controllers;

use App\Exports\GradingbjTemplateExport;
use App\Models\ApiGudangGradingModel;
use App\Models\PengirimanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GradingBjController extends Controller
{
    protected $nmTbl = 'pengiriman_gradingbj';
    public function getDataMaster($jenis)
    {

        $arr = [
            'formulir' => DB::select("SELECT 
                a.no_box, a.tanggal, b.name as pemberi, a.no_invoice, a.pcs_awal, a.gr_awal
                FROM formulir_sarang as a 
                JOIN users as b on a.id_pemberi = b.id
                WHERE a.kategori = 'grade'"),
            'pengawas' => DB::table('users')->where('posisi_id', 13)->get()
        ];
        return $arr[$jenis];
    }
    public function index(Request $r)
    {
        $data = [
            'title' => 'Grading BJ',
            'formulir' => $this->getDataMaster('formulir')
        ];

        return view('home.gradingbj.index', $data);
    }

    public function grading($no_box)
    {
        $getFormulir = DB::table('formulir_sarang')->where('no_box', $no_box)->first();
        $no_invoice = 1001;
        $gradeStatuses = ['bentuk', 'turun'];
        $tb_grade = DB::table('tb_grade')->whereIn('status', $gradeStatuses)->orderBy('urutan', 'ASC')->get();
        $gradeBentuk = $tb_grade->where('status', 'bentuk');
        $gradeTurun = $tb_grade->where('status', 'turun');
        $data = [
            'title' => 'Grading Proses',
            'no_invoice' => $no_invoice,
            'user' => auth()->user()->name,
            'getFormulir' => $getFormulir,
            'gradeBentuk' => $gradeBentuk,
            'gradeTurun' => $gradeTurun,
        ];

        return view('home.gradingbj.grading', $data);
    }

}
