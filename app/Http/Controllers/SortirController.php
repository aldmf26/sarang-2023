<?php

namespace App\Http\Controllers;

use App\Exports\SortirExport;
use App\Exports\SortirRekapExport;
use App\Models\Sortir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SortirController extends Controller
{
    public function getStokBk($no_box = null)
    {
        $id_user = auth()->user()->id;
        $query = !empty($no_box) ? "selectOne" : 'select';
        $noBoxAda = !empty($no_box) ? "a.no_box = '$no_box' AND" : '';
        return DB::$query("SELECT a.no_box, a.pcs_awal,b.pcs_awal as pcs_cabut,a.gr_awal,b.gr_awal as gr_cabut FROM `bk` as a
        LEFT JOIN (
            SELECT max(no_box) as no_box,id_pengawas,sum(pcs_awal) as pcs_awal,sum(gr_awal) as gr_awal  FROM `sortir` where penutup = 'T'  GROUP BY no_box,id_pengawas
        ) as b ON a.no_box = b.no_box AND b.id_pengawas = a.penerima WHERE  $noBoxAda a.penerima = '$id_user' AND a.kategori LIKE '%sortir%' AND a.selesai = 'T'");
    }

    public function getAnak($id = null)
    {
        return DB::table('tb_anak as a')
            ->join('tb_kelas_sortir as b', 'a.id_kelas', 'b.id_kelas')
            ->where('id_pengawas', empty($id) ? auth()->user()->id : null)
            ->get();
    }
    public function updateAnakBelum()
    {
        $anakBelum = count(DB::table('sortir')->where([['no_box', 9999], ['id_pengawas', auth()->user()->id]])->get());
        return response()->json(['anakBelum' => $anakBelum]);
    }
    public function index(Request $r)
    {
        $tgl1 = $r->tgl1 ?? date('Y-m-d');
        $tgl2 = $r->tgl2 ?? date('Y-m-t');
        $id_anak = $r->id_anak ?? 'All';
        $data = [
            'title' => 'Sortir Divisi',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'boxBk' => $this->getStokBk(),
            'anak' => $this->getAnak(),
            'tb_anak' => $this->getAnak(),
            'cabut' => DB::table('sortir as a')
                ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->join('tb_kelas_sortir as c', 'a.id_kelas', 'c.id_kelas')
                ->where('a.id_pengawas', auth()->user()->id)
                ->whereBetween('a.tgl', [$tgl1, $tgl2])
                ->orderBy('id_sortir', 'DESC')
                ->get(),
            'id_anak' => $id_anak
        ];

        return view('home.sortir.index', $data);
    }
    public function history(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];


        $data = [
            'title' => 'Sortir Divisi',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,

            'cabut' => DB::table('sortir as a')
                ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->join('tb_kelas_sortir as c', 'a.id_kelas', 'c.id_kelas')
                ->where('a.id_pengawas', auth()->user()->id)
                ->where([['a.no_box', '!=', '9999'], ['a.penutup', 'Y']])
                ->orderBY('a.selesai', 'ASC')
                ->get()
        ];

        return view('home.sortir.history', $data);
    }
    public function ambil_box_bk(Request $r)
    {
        $idPengwas = auth()->user()->id;
        DB::table('bk')->where('kategori', 'sortir')->whereIn('no_box', $r->no_box)->update([
            'penerima' => $idPengwas
        ]);
        DB::table('pengiriman_list_gradingbj')->whereIn('no_box', $r->no_box)->update([
            'pengawas' => $idPengwas
        ]);

        return redirect()->route('sortir.index')->with('sukses', 'Box berhasil diambil');
    }

    public function add()
    {
        $data = [
            'title' => 'Tambah Divisi Sortir',
            'boxBk' => $this->getStokBk(),
            'anak' => $this->getAnak(),
        ];
        return view('home.sortir.create', $data);
    }

    public function tbh_baris(Request $r)
    {
        $data = [
            'boxBk' => $this->getStokBk(),
            'anak' => $this->getAnak(),
            'count' => $r->count,
        ];
        return view('home.sortir.tbh_baris', $data);
    }

    public function get_box_sinta(Request $r)
    {
        $bk = $this->getStokBk($r->no_box);

        $data = [
            'pcs_awal' => $bk->pcs_awal,
            'gr_awal' => $bk->gr_awal,
            'pcs_cabut' => $bk->pcs_cabut,
            'gr_cabut' => $bk->gr_cabut,
        ];
        return json_encode($data);
    }

    public function create(Request $r)
    {
        $ttlPcs = array_sum($r->pcs_awal);
        $ttlGr = array_sum($r->gr_awal);
        for ($i = 0; $i < count($r->gr_awal); $i++) {
            $nobox = $r->no_box[$i];
            $admin = auth()->user()->id;
            $cekStok = DB::selectOne("SELECT 
            sum(a.pcs_awal) - sum(b.pcs) as pcs, 
            sum(a.gr_awal) - sum(b.gr) as gr 
            FROM bk  as a
            JOIN (
                SELECT no_box,id_pengawas,sum(pcs_awal) as pcs, sum(gr_awal) as gr FROM sortir GROUP BY no_box,id_pengawas
            ) as b on a.no_box = b.no_box AND a.penerima = b.id_pengawas
            WHERE a.no_box = '$nobox' AND a.kategori LIKE '%sortir%' AND a.penerima= '$admin';");
            // if ($ttlPcs <= $cekStok->pcs && $ttlGr <= $cekStok->gr) {
            // $rupiah = str()->remove('.', $r->rupiah[$i]);
            $kelasSortir = DB::table('tb_kelas_sortir')->where('id_kelas', $r->tipe[$i])->first();
            $rupiah = ($kelasSortir->rupiah / $kelasSortir->gr) * $r->gr_awal[$i];
            $id_sortir = $r->id_sortir[$i];
            $data = [
                'no_box' => $r->no_box[$i],
                'tgl' => $r->tgl_terima[$i],
                'id_pengawas' => $admin,
                'id_anak' => $r->id_anak[$i],
                'id_kelas' => $r->tipe[$i],
                'pcuc' => $r->pcuc[$i],
                'pcs_awal' => $r->pcs_awal[$i],
                'gr_awal' => $r->gr_awal[$i],
                'rp_target' => $rupiah,
                'tgl_input' => date('Y-m-d')
            ];
            if ($id_sortir == 9999) {
                $cekSortir = DB::table('sortir')->where([
                    ['no_box', $r->no_box[$i]],
                    ['id_anak', $r->id_anak[$i]],
                    ['pcs_awal', $r->pcs_awal[$i]],
                    ['gr_awal', $r->gr_awal[$i]]
                ])->first();
                if (!$cekSortir) {
                    DB::table('sortir')->insert($data);
                }
            } else {
                DB::table('sortir')->where('id_sortir', $id_sortir)->update($data);
            }
            // } else {
            //     return 'Stok pcs / gr melebihi Bk';
            // }
        }

        return 'berhasil';


        // return redirect()->route('sortir.index')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function cancel(Request $r)
    {
        DB::table('sortir')->where('id_sortir', $r->id_sortir)->update([
            'no_box' => 9999,
            'pcs_akhir' => '',
            'gr_akhir' => '',
            'tgl' => date('Y-m-d'),
        ]);
    }

    public function load_modal_akhir(Request $r)
    {
        $detail = DB::table('sortir as a')
            ->select(
                'a.id_anak',
                'a.no_box',
                'a.id_sortir',
                'a.rp_target',
                'a.ttl_rp',
                'a.tgl',
                'a.pcs_awal',
                'a.pcs_akhir',
                'a.gr_awal',
                'a.gr_akhir',
                'a.pcus',
                'a.bulan',
                'b.id_kelas',
                'b.nama',
                'c.kelas',
                'c.denda_susut',
                'c.bts_denda_sst',
                'c.batas_denda_rp',
                'c.denda_susut',
                'c.denda'
            )
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->join('tb_kelas_sortir as c', 'c.id_kelas', 'a.id_kelas')
            ->where([['selesai', 'T'], ['no_box', '!=', 9999], ['a.id_pengawas', auth()->user()->id]])
            ->get();
        $data = [
            'detail' => $detail
        ];
        return view('home.sortir.load_modal_akhir', $data);
    }

    public function load_detail_sortir(Request $r)
    {
        $detail = DB::selectOne("SELECT 
        a.pcs_awal,a.gr_awal,a.pcs_akhir,
        a.gr_akhir,a.tgl,a.no_box,b.gr,
        b.kelas as nm_kelas,b.rupiah as rp_kelas,
        c.nama,c.id_kelas
        FROM sortir as a 
        JOIN tb_kelas_sortir as b ON a.id_kelas = b.id_kelas
        JOIN tb_anak as c ON c.id_anak = a.id_anak
        WHERE a.id_sortir = '$r->id_sortir'");
        $data = [
            'detail' => $detail
        ];
        return view('home.sortir.load_modal_detail', $data);
    }



    public function load_halaman(Request $r)
    {
        $tgl1 = $r->tgl1 ?? date('Y-m-d');
        $tgl2 = $r->tgl2 ?? date('Y-m-t');
        $id_anak = $r->id_anak;

        if ($id_anak == 'All') {
            $sortir = DB::table('sortir as a')
                ->leftJoin('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->leftJoin('tb_kelas_sortir as c', 'a.id_kelas', 'c.id_kelas')
                ->where('a.id_pengawas', auth()->user()->id)
                ->where([['a.no_box', '!=', '9999'], ['a.penutup', 'T']])
                ->whereBetween('a.tgl', [$tgl1, $tgl2])
                ->orderBY('a.selesai', 'ASC')
                ->get();
        } else {
            $sortir = DB::table('sortir as a')
                ->leftJoin('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->leftJoin('tb_kelas_sortir as c', 'a.id_kelas', 'c.id_kelas')
                ->where('a.id_pengawas', auth()->user()->id)
                ->where([['a.no_box', '!=', '9999'], ['a.penutup', 'T']])
                ->whereBetween('a.tgl', [$tgl1, $tgl2])
                ->where('a.id_anak', $id_anak)
                ->orderBY('a.selesai', 'ASC')
                ->get();
        }


        $data = [
            'title' => 'Sortir Divisi',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'cabut' => $sortir,
            'kelas' => DB::table('tb_kelas_sortir')->orderBy('id_kelas', 'ASC')->get(),
            'anak' => $this->getAnak(),
            'bulan' => DB::table('bulan')->get(),
        ];

        return view('home.sortir.load_halaman', $data);
    }
    public function load_halamanrow(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];


        $data = [
            'title' => 'Sortir Divisi',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,

            'd' => DB::table('sortir as a')
                ->leftJoin('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->leftJoin('tb_kelas_sortir as c', 'a.id_kelas', 'c.id_kelas')
                ->where('a.id_pengawas', auth()->user()->id)
                ->where([['a.no_box', '!=', '9999'], ['a.penutup', 'T']])
                ->where('id_sortir', $r->id_sortir)
                ->orderBY('a.selesai', 'ASC')
                ->first(),
            'kelas' => DB::table('tb_kelas_sortir')->orderBy('id_kelas', 'ASC')->get(),
            'anak' => $this->getAnak(),
            'bulan' => DB::table('bulan')->get(),
            'no' => $r->no
        ];



        return view('home.sortir.load_halaman_row', $data);
    }

    public function load_anak()
    {
        $anak = $this->getAnak();
        echo "
        <div class='row'>
                    <div class='col-lg-12'>
                        <table class='table table-striped'>
                            <tr>
                                <th width='180'>Nama</th>
                                <th>Tgl Masuk</th>
                                <th>Aksi</th>
                            </tr>";
        foreach ($anak as $d) {
            echo "
                                <tr>
                                    <td>" . ucwords($d->nama) . "</td>
                                    <input type='hidden' value='" . $d->id_anak . "' name='id_anak[]' class='form-control'>
                                    <td><input type='date' value='" . $d->tgl_masuk . "' class='form-control' name='tgl_masuk[]'></td>
                                    <td><button type='button' class='btn btn-sm btn-danger' id_anak='" . $d->id_anak . "' id='delete_anak'><i class='fas fa-window-close'></i></button></td>
                                </tr>
                                ";
        }
        echo "
                        </table>
                    </div>
                </div>
        ";
    }

    public function load_anak_nopengawas()
    {
        $anakNoPengawas = $this->getAnak(1);

        echo "
        <select class='select3-load anakNoPengawas' name='' multiple id=''>
        ";
        foreach ($anakNoPengawas as $d) {
            echo "<option value='" . $d->id_anak . "'>" . ucwords($d->nama) . "</option>";
        }
        echo "
                            </select>
        ";
    }

    public function add_delete_anak(Request $r)
    {
        $idArray = explode(",", $r->id_anak);
        foreach ($idArray as $n) {
            DB::table('tb_anak')->where('id_anak', $n)->update(
                ['id_pengawas' => empty($r->delete) ? auth()->user()->id : null]
            );
        }
    }

    public function create_anak(Request $r)
    {
        for ($i = 0; $i < count($r->id_anak); $i++) {
            DB::table('tb_anak')->where('id_anak', $r->id_anak[$i])->update(
                [
                    'tgl_masuk' => $r->tgl_masuk[$i],
                ]
            );
        }
        return redirect()->route('cabut.index')->with('sukses', 'Data Berhasil ditambahkan');
    }
    public function selesai_sortir(Request $r)
    {
        DB::table('sortir')->where('id_sortir', $r->id_sortir)->update(['selesai' => 'Y']);
    }

    public function ditutup(Request $r)
    {
        $data = $r->tipe == 'tutup' ? ['penutup' => 'Y'] : ['selesai' => 'T'];
        foreach ($r->datas as $d) {
            DB::table('sortir')->where('id_sortir', $d)->update($data);
        }
    }

    public function load_tambah_sortir()
    {
        $data = [
            'boxBk' => $this->getStokBk(),
            'datas' => DB::table('sortir as a')
                ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->where([['a.no_box', 9999], ['a.id_pengawas', auth()->user()->id]])
                ->get()
        ];
        return view('home.sortir.load_tambah_sortir', $data);
    }

    public function hapusKerjaSortir(Request $r)
    {
        DB::table('sortir')->where('id_sortir', $r->id_sortir)->delete();
        return 'berhasil';
    }

    public function load_tambah_anak()
    {
        $data = [
            'anak' => $this->getAnak()
        ];
        return view('home.sortir.load_tambah_anak', $data);
    }

    public function createTambahAnakSortir(Request $r)
    {
        $tgl = date('Y-m-d');
        $id_pengawas = auth()->user()->id;
        foreach ($r->all()['rows'] as $d) {

            DB::table('sortir')->insertGetId([
                'no_box' => 9999,
                'id_pengawas' => $id_pengawas,
                'id_anak' => $d,
                'tgl' => $tgl,
                'tgl_input' => date('Y-m-d')
            ]);
        }
        return 'Berhasil tambah anak';
    }

    public function export(Request $r)
    {
        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $view = 'home.sortir.export';
        $tbl = DB::table('sortir as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->join('tb_kelas_sortir as c', 'a.id_kelas', 'c.id_kelas')
            ->where([['a.id_pengawas', auth()->user()->id], ['a.no_box', '!=', 9999]])
            ->orderBy('id_sortir', 'DESC')
            ->get();

        return Excel::download(new SortirExport($tbl, $view), 'Export SORTIR.xlsx');
    }

    public function rekap(Request $r)
    {
        $bulan = $r->bulan ?? date('m');
        $tahun = $r->tahun ?? date('Y');

        $ttlPcsBk = 0;
        $ttlGrBk = 0;
        $ttlPcsAwal = 0;
        $ttlGrAwal = 0;
        $ttlPcsAkhir = 0;
        $ttlGrAkhir = 0;
        $ttlRp = 0;
        $sortirGroup = Sortir::queryRekapGroup($bulan, $tahun);

        foreach ($sortirGroup as $d) {
            $ttlPcsBk += $d->pcs_bk;
            $ttlGrBk += $d->gr_bk;
            $ttlPcsAwal += $d->pcs_awal;
            $ttlGrAwal += $d->gr_awal;
            $ttlPcsAkhir += $d->pcs_akhir;
            $ttlGrAkhir += $d->gr_akhir;
            $ttlRp += $d->ttl_rp;
        }

        $data = [
            'title' => 'Rekap Summary Sortir',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'ttlPcsBk' => $ttlPcsBk,
            'ttlGrBk' => $ttlGrBk,
            'ttlPcsAwal' => $ttlPcsAwal,
            'ttlGrAwal' => $ttlGrAwal,
            'ttlPcsAkhir' => $ttlPcsAkhir,
            'ttlGrAkhir' => $ttlGrAkhir,
            'ttlRp' => $ttlRp,
            'sortirGroup' => $sortirGroup
        ];
        return view('home.sortir.rekap', $data);
    }

    public function export_rekap(Request $r)
    {
        $bulan =  $r->bulan;
        $tahun =  $r->tahun;
        $view = 'home.sortir.export_rekap';
        $tbl = Sortir::queryRekap($bulan, $tahun);
        return Excel::download(new SortirRekapExport($tbl, $view), 'Export REKAP SORTIR.xlsx');
    }

    public function gudang(Request $r)
    {
        $id_user = auth()->user()->id;
        if (auth()->user()->posisi_id == 1) {
            $id_penerima = '';
            $id_pengawas = '';
        } else {
            $id_penerima = "AND a.id_penerima = $id_user";
            $id_pengawas = "AND a.id_pengawas = $id_user";
        }



        $data = [
            'title' => 'Gudang',

            'siap_sortir' => DB::select("SELECT a.no_box, a.pcs_awal, a.gr_awal, (b.hrga_satuan * b.gr_awal) as ttl_rp
            FROM formulir_sarang as a 
            left join bk as b on b.no_box = a.no_box
            WHERE a.no_box not in(SELECT b.no_box FROM sortir as b) and a.kategori = 'sortir' $id_penerima;"),

            'sortir_proses' => DB::select("SELECT a.no_box, a.pcs_awal, a.gr_awal, (b.hrga_satuan * b.gr_awal) as ttl_rp
            FROM sortir as a 
            left join bk as b on b.no_box = a.no_box
            join formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'
            WHERE a.selesai = 'T' $id_pengawas;"),

            'sortir_selesai' => DB::select("SELECT a.no_box, a.pcs_akhir as pcs_awal, a.gr_akhir as gr_awal,(b.hrga_satuan * b.gr_awal) as ttl_rp
            FROM sortir as a 
            left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
            join formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'
            WHERE a.no_box not in (SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'grade') $id_pengawas and a.selesai = 'Y';"),

            'users' => DB::table('users')->where('posisi_id', '!=', '1')->get()

        ];
        return view('home.sortir.gudang', $data);
    }

    public function export_gudang(Request $r)
    {
        $id_pengawas = auth()->user()->id;

        $siap_sortir = DB::select("SELECT b.name, a.no_box, a.pcs_awal, a.gr_awal
        FROM formulir_sarang as a 
        left join users as b on b.id = a.id_penerima
        WHERE a.no_box not in(SELECT b.no_box FROM sortir as b) and a.kategori = 'sortir' ");

        $sortir_proses = DB::select("SELECT b.name, a.no_box, a.pcs_awal, a.gr_awal
        FROM sortir as a 
        left join users as b on b.id = a.id_pengawas
        join formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'
        WHERE  a.selesai = 'T';");

        $sortir_selesai = DB::select("SELECT b.name, a.no_box, a.pcs_akhir as pcs_awal, a.gr_akhir as gr_awal
        FROM sortir as a 
        left join users as b on b.id = a.id_pengawas
        join formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'
        WHERE a.no_box not in (SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'grade') and a.selesai = 'Y';");

        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        $styleBaris = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $style_atas = array(
            'font' => [
                'bold' => true, // Mengatur teks menjadi tebal
            ],

            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ],
        );
        $style_atas_number = array(
            'font' => [
                'bold' => true, // Mengatur teks menjadi tebal
            ],

            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            ],
        );

        $kolom = [
            'A' => 'Sortir Stok',
            'B' => 'Pengawas',
            'C' => 'no box',
            'D' => 'pcs',
            'E' => 'gr',

            'G' => 'Sortir Sedang Proses',
            'H' => 'Pengawas',
            'I' => 'No Box',
            'J' => 'Pcs',
            'K' => 'Gr',

            'M' => 'Sortir Selesai Siap Grade',
            'N' => 'Pengawas',
            'O' => 'no box',
            'P' => 'pcs',
            'Q' => 'gr',
        ];

        foreach ($kolom as $k => $v) {
            $sheet->setCellValue($k . '1', $v);
        }
        $no = 2;
        $ttl_pcs = 0;
        $ttl_gr = 0;
        foreach ($siap_sortir as $item) {
            $sheet->setCellValue('B' . $no, $item->name);
            $sheet->setCellValue('C' . $no, $item->no_box);
            $sheet->setCellValue('D' . $no, $item->pcs_awal);
            $sheet->setCellValue('E' . $no, $item->gr_awal);

            $no++;
            $ttl_pcs += $item->pcs_awal;
            $ttl_gr += $item->gr_awal;
        }
        $sheet->setCellValue('B' . $no, 'Total');
        $sheet->setCellValue('C' . $no, '');
        $sheet->setCellValue('D' . $no, $ttl_pcs);
        $sheet->setCellValue('E' . $no, $ttl_gr);


        $sheet->getStyle('B1:C1')->applyFromArray($style_atas);
        $sheet->getStyle('D1:E1')->applyFromArray($style_atas_number);
        $sheet->getStyle('B2:E' . $no - 1)->applyFromArray($styleBaris);
        $sheet->getStyle('B' . $no . ':E' . $no)->applyFromArray($style_atas);

        $no2 = 2;
        $ttl_pcs2 = 0;
        $ttl_gr2 = 0;
        foreach ($sortir_proses as $item) {
            $sheet->setCellValue('H' . $no2, $item->name);
            $sheet->setCellValue('I' . $no2, $item->no_box);
            $sheet->setCellValue('J' . $no2, $item->pcs_awal);
            $sheet->setCellValue('K' . $no2, $item->gr_awal);

            $no2++;
            $ttl_pcs2 += $item->pcs_awal;
            $ttl_gr2 += $item->gr_awal;
        }

        $sheet->setCellValue('H' . $no2, 'Total');
        $sheet->setCellValue('I' . $no2, '');
        $sheet->setCellValue('J' . $no2, $ttl_pcs2);
        $sheet->setCellValue('K' . $no2, $ttl_gr2);

        $sheet->getStyle('H1:I1')->applyFromArray($style_atas);
        $sheet->getStyle('J1:K1')->applyFromArray($style_atas_number);
        $sheet->getStyle('H2:K' . $no2 - 1)->applyFromArray($styleBaris);
        $sheet->getStyle('H' . $no2 . ':K' . $no2)->applyFromArray($style_atas);

        $no3 = 2;
        $ttl_pcs3 = 0;
        $ttl_gr3 = 0;
        foreach ($sortir_selesai as $item) {
            $sheet->setCellValue('N' . $no3, $item->name);
            $sheet->setCellValue('O' . $no3, $item->no_box);
            $sheet->setCellValue('P' . $no3, $item->pcs_awal);
            $sheet->setCellValue('Q' . $no3, $item->gr_awal);

            $no3++;
            $ttl_pcs3 += $item->pcs_awal;
            $ttl_gr3 += $item->gr_awal;
        }

        $sheet->setCellValue('N' . $no3, 'Total');
        $sheet->setCellValue('O' . $no3, '');
        $sheet->setCellValue('P' . $no3, $ttl_pcs3);
        $sheet->setCellValue('Q' . $no3, $ttl_gr3);

        $sheet->getStyle('N1:O1')->applyFromArray($style_atas);
        $sheet->getStyle('P1:Q1')->applyFromArray($style_atas_number);
        $sheet->getStyle('N2:Q' . $no3 - 1)->applyFromArray($styleBaris);
        $sheet->getStyle('N' . $no3 . ':Q' . $no3)->applyFromArray($style_atas);



        $writer = new Xlsx($spreadsheet);

        // Menggunakan response untuk mengirimkan file ke browser
        $fileName = "Gudang Cetak";
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '.xlsx"',
            ]
        );
    }


    public function save_formulir(Request $r)
    {
        $no_box = explode(',', $r->no_box[0]);
        foreach ($no_box as $d) {
            $ambil = DB::selectOne("SELECT 
                        sum(pcs_akhir) as pcs_akhir, sum(gr_akhir) as gr_akhir , formulir_sarang.id_pemberi
                        FROM sortir 
                        left join formulir_sarang on formulir_sarang.no_box = sortir.no_box and formulir_sarang.kategori = 'grade'
                        WHERE sortir.no_box = $d AND sortir.selesai = 'Y' GROUP BY sortir.no_box ");
            $pcs = $ambil->pcs_akhir;
            $gr = $ambil->gr_akhir;


            $urutan_invoice = DB::selectOne("SELECT max(a.no_invoice) as no_invoice FROM formulir_sarang as a where a.kategori = 'sortir'");

            if (empty($urutan_invoice->no_invoice)) {
                $inv = 1001;
            } else {
                $inv = $urutan_invoice->no_invoice + 1;
            }

            $data[] = [
                'no_invoice' => $inv,
                'no_box' => $d,
                'id_pemberi' => auth()->user()->id,
                'id_penerima' => $r->id_penerima,
                'pcs_awal' => $pcs,
                'gr_awal' => $gr,
                'tanggal' => $r->tgl,
                'kategori' => 'grade',
            ];
        }

        DB::table('formulir_sarang')->insert($data);
        return redirect()->route('sortir.gudang')->with('sukses', 'Data Berhasil');
    }

    public function save_akhir(Request $r)
    {
        $kelas = DB::table('tb_kelas_sortir')->where('id_kelas', $r->id_kelas)->first();
        $rupiah =  empty($kelas->rupiah) ? 0 : $kelas->rupiah / $kelas->gr;
        $rp_target = $rupiah == 0 ? 0 : $rupiah * $r->gr_awal;

        $susut = $r->gr_akhir == 0  ? 0 : (1 - $r->gr_akhir / $r->gr_awal) * 100;

        $denda = 0;
        $rupiah = $rp_target;
        if ($susut > $kelas->denda_susut) {
            $denda = $susut > $kelas->bts_denda_sst ? $kelas->batas_denda_rp : (number_format($susut) - $kelas->denda_susut) * $kelas->denda;
            $rupiah = $rp_target - $denda;
        }

        $data = [
            'id_anak' => $r->id_anak,
            'id_kelas' => $r->id_kelas,
            'pcs_akhir' => $r->pcs_akhir,
            'gr_akhir' => $r->gr_akhir,
            'bulan' => $r->bulan_dibayar,
            'rp_target' => $rp_target,
            'ttl_rp' => $rupiah,
            'denda_sp' => $denda,
            'tgl' => $r->tgl,

        ];
        DB::table('sortir')->where('id_sortir', $r->id_sortir)->update($data);
    }

    public function input_akhir(Request $r)
    {
        $id_anak = $r->id_anak;
        $no_box = $r->no_box;
        $tgl = $r->tgl;
        $gr_akhir = $r->gr_akhir;
        $pcs_akhir = $r->pcs_akhir;
        $pcus = $r->pcus;
        $id_sortir = $r->id_sortir;
        $bulan = $r->bulan;
        if ($gr_akhir == 0) {
            return [
                'tipe' => 'error',
                'pesan' => 'Gr Akhir kosong'
            ];
        }


        $getSortir = DB::table('sortir')->where('id_sortir', $id_sortir);
        $get = $getSortir->first();
        $susut = $gr_akhir == 0  ? 0 : (1 - $gr_akhir / $get->gr_awal) * 100;

        $kelas = DB::table('tb_kelas_sortir')->where('id_kelas', $get->id_kelas)->first();

        $rupiah = $get->rp_target;
        $denda = 0;
        if ($susut > $kelas->denda_susut) {
            $denda = $susut > $kelas->bts_denda_sst ? $kelas->batas_denda_rp : (number_format($susut) - $kelas->denda_susut) * $kelas->denda;
            $rupiah = $rupiah - $denda;
        }

        $getSortir->update([
            'pcs_akhir' => $pcs_akhir,
            'pcus' => $pcus,
            'gr_akhir' => $gr_akhir,
            'bulan' => $bulan,
            'ttl_rp' => $rupiah,
            'tgl' => $tgl,
            'denda_sp' => $denda,
        ]);
        return [
            'tipe' => 'sukses',
            'pesan' => 'Berhasil Input Akhir'
        ];
    }

    public function cancel_sortir(Request $r)
    {
        DB::table('sortir')->where('id_sortir', $r->id_sortir)->update([
            'selesai' => 'T',
        ]);
    }

    public function getNoBoxTambah()
    {
        $cekBox = DB::selectOne("SELECT no_box FROM `bk` WHERE kategori like '%sortirimport%' ORDER by no_box DESC limit 1;");
        $nobox = isset($cekBox->no_box) ? $cekBox->no_box + 1 : 1001;
        return $nobox;
    }
    public function import(Request $r)
    {
        $file = $r->file('file');
        $spreadsheet = IOFactory::load($file);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        DB::beginTransaction();
        try {
            foreach (array_slice($sheetData, 1) as $row) {
                if (empty(array_filter($row))) {
                    continue;
                }

                $nobox = $this->getNoBoxTambah();


                // $cekBox = DB::table('bk')->where([['kategori', 'LIKE', '%cabut%'], ['no_box', $nobox]])->first();
                if (
                    // $cekBox || 
                    empty($row[0]) ||
                    empty($row[5])
                    // empty($row[9]) ||
                    // empty($row[10])
                ) {
                    $pesan = [
                        // empty($row[0]) => "NO LOT TIDAK BOLEH KOSONG",
                        empty($row[0]) => "NAMA PARTAI TIDAK BOLEH KOSONG",
                        // empty($row[6]) => "PENGAWAS TIDAK BOLEH KOSONG",
                        empty($row[5]) => "GR TIDAK BOLEH KOSONG",

                        // $cekBox ? "NO BOX : $nobox SUDAH ADA" : false,
                    ];
                    DB::rollBack();
                    return redirect()->route('bk.index')->with('error', "ERROR! " . $pesan[true]);
                } else {
                    DB::table('bk')->insert([
                        'no_lot' => '0',
                        'nm_partai' => $row[0],
                        'no_box' => $nobox,
                        'tipe' => $row[1],
                        'ket' => $row[2],
                        'warna' => $row[3],
                        'tgl' => date('Y-m-d'),
                        'pengawas' => 'sinta',
                        'penerima' => auth()->user()->id,
                        'pcs_awal' => $row[4],
                        'gr_awal' => $row[5],
                        'kategori' => 'sortirimport',
                    ]);
                    DB::table('formulir_sarang')->insert([
                        'no_box' => $nobox,
                        'id_pemberi' => 265,
                        'id_penerima' => auth()->user()->id,
                        'tanggal' => date('Y-m-d'),
                        'pcs_awal' => $row[4],
                        'gr_awal' => $row[5],
                        'kategori' => 'sortir'
                    ]);
                    DB::table('sortir')->insert([
                        'no_box' => $nobox,
                        'id_pengawas' => auth()->user()->id,
                        'tgl' => date('Y-m-d'),
                        'pcs_awal' => $row[4],
                        'gr_awal' => $row[5],
                    ]);
                }
            }
            DB::commit();
            return redirect()->back()->with('sukses', 'Data berhasil import');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
