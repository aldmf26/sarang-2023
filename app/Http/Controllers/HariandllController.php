<?php

namespace App\Http\Controllers;

use App\Exports\HariandllExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class HariandllController extends Controller
{

    public function anak()
    {
        return DB::table('tb_anak')->where('id_pengawas', auth()->user()->id)->get();
    }
    public function index(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];
        $id_user = auth()->user()->id;

        if (empty($r->kategori)) {
            $kategori =  'biasa';
        } else {
            $kategori = $r->kategori;
        }

        $data = [
            'title' => 'Harian DLL',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'anak' => $this->anak(),
            'kategori' => $kategori,
            'bulan' => DB::table('bulan')->get(),
            'datas' => DB::table('tb_hariandll  as a')
                ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->where([['ditutup', 'T'], ['b.id_pengawas', $id_user], ['kategori', $kategori]])
                // ->whereBetween('a.tgl', [$tgl1, $tgl2])
                ->orderBy('a.id_hariandll', 'DESC')
                ->get()
        ];
        return view('home.hariandll.index', $data);
    }
    public function tbh_baris(Request $r)
    {
        $data = [
            'anak' => $this->anak(),
            'count' => $r->count,
        ];
        return view('home.hariandll.tbh_baris', $data);
    }

    public function create(Request $r)
    {
        for ($i = 0; $i < count($r->id_anak); $i++) {
            $rupiah = str()->remove(',', $r->rupiah[$i]);
            DB::table('tb_hariandll')->insert([
                'tgl' => $r->tgl[$i],
                'no_box' => $r->no_box[$i],
                'id_anak' => $r->id_anak[$i],
                'ket' => $r->ket[$i],
                'rupiah' => $rupiah,
                'bulan_dibayar' => $r->bulan_dibayar,
                'lokasi' => $r->lokasi[$i],
            ]);
        }
        return redirect()->route('hariandll.index')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function edit(Request $r)
    {
        $data = [
            'detail' => DB::table('tb_hariandll')->whereIn('id_hariandll', $r->id)->get(),
            'anak' => $this->anak(),
        ];
        return view('home.hariandll.edit_load', $data);
    }

    public function update(Request $r)
    {
        for ($i = 0; $i < count($r->tgl); $i++) {
            $rupiah = str()->remove(',', $r->rupiah[$i]);
            DB::table('tb_hariandll')->where('id_hariandll', $r->id_hariandll[$i])->update([

                'tgl' => $r->tgl[$i],
                'id_anak' => $r->id_anak[$i],
                'ket' => $r->ket[$i],
                'bulan_dibayar' => $r->bulan_dibayar[$i],
                'rupiah' => $rupiah,
                'lokasi' => $r->lokasi[$i],
            ]);
        }
        return redirect()->route('hariandll.index')->with('sukses', 'Data Berhasil diubah');
    }

    public function delete(Request $r)
    {
        for ($i = 0; $i < count($r->id); $i++) {
            DB::table('tb_hariandll')->where('id_hariandll', $r->id[$i])->update(['ditutup' => 'Y']);
        }
    }

    public function hapus($id)
    {
        DB::table('tb_hariandll')->where('id_hariandll', $id)->delete();
        return redirect()->route('hariandll.index')->with('sukses', 'Data Berhasil dihapus');
    }
    public function getQuery($bulan, $tahun)
    {
        return DB::select("SELECT a.bulan_dibayar,a.tgl,b.nama,c.name, SUM(rupiah) AS total_rupiah
        FROM tb_hariandll as a
        LEFT JOIN tb_anak as b on a.id_anak = b.id_anak
        LEFT JOIN users as c on c.id = b.id_pengawas
        WHERE bulan_dibayar = '$bulan' AND tahun_dibayar = '$tahun' AND a.ditutup = 'T'
        GROUP BY b.id_pengawas;");
    }
    public function export(Request $r)
    {
        $id_user = auth()->user()->id;
        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $kategori =  $r->kategori;
        $view = 'home.hariandll.export';
        $tbl = DB::table('tb_hariandll  as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->where([['ditutup', 'T'], ['b.id_pengawas', $id_user], ['kategori', $kategori]])
            ->orderBy('a.id_hariandll', 'DESC')
            ->get();

        return Excel::download(new HariandllExport($tbl, $view, $kategori), 'Export HARIAN DLL.xlsx');
    }

    public function rekap(Request $r)
    {

        $bulan = $r->bulan ?? date('m');
        $tahun = $r->tahun ?? date('Y');
        $datas = $this->getQuery($bulan, $tahun);

        $data = [
            'title' => 'Rekap Summary Dll',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'datas' => $datas,
        ];

        return view('home.hariandll.rekap', $data);
    }

    public function export_rekap(Request $r)
    {
        // $id_user = auth()->user()->id;
        // $bulan =  $r->bulan;
        // $tahun =  $r->tahun;
        // $view = 'home.hariandll.export_rekap';
        // $tbl = $this->getQuery($bulan, $tahun);

        // return Excel::download(new HariandllExport($tbl, $view), 'Export REKAP HARIAN DLL.xlsx');
    }

    function import(Request $r)
    {
        $uploadedFile = $r->file('file');
        $allowedExtensions = ['xlsx'];
        $extension = $uploadedFile->getClientOriginalExtension();

        if (in_array($extension, $allowedExtensions)) {
            $spreadsheet = IOFactory::load($uploadedFile->getPathname());
            $sheet = $spreadsheet->getSheetByName('Worksheet');
            $data = [];

            foreach ($sheet->getRowIterator() as $index => $row) {
                if ($index === 1) {
                    continue;
                }

                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $data[] = $rowData;
            }

            // $importGagal = false;

            DB::beginTransaction(); // Mulai transaksi database

            try {
                foreach ($data as $rowData) {
                    $tgl = $rowData[1];
                    if (is_numeric($tgl)) {
                        // Jika nilai berupa angka, konversi ke format tanggal
                        $tanggalExcel = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tgl);
                        $tanggalFormatted = $tanggalExcel->format('Y-m-d');
                    } else {
                        // Jika nilai sudah dalam format tanggal, pastikan formatnya adalah 'Y-m-d'
                        $tanggalFormatted = date('Y-m-d', strtotime($tgl));
                    }
                    $id = auth()->user()->id;
                    if (empty($rowData[0])) {
                        if ($r->kategori == 'cetak') {
                            DB::table('tb_hariandll')->insert([
                                'tgl' => $tanggalFormatted,
                                'id_anak' => $rowData[2],
                                'ket' => $rowData[4],
                                'pcs' => $rowData[5],
                                'gr' => $rowData[6],
                                'lokasi' => $rowData[7],
                                'rupiah' => $rowData[8],
                                'kategori' => 'cetak'
                            ]);
                        } else {
                            DB::table('tb_hariandll')->insert([
                                'tgl' => $tanggalFormatted,
                                'id_anak' => $rowData[2],
                                'ket' => $rowData[4],
                                'lokasi' => $rowData[5],
                                'rupiah' => $rowData[6],
                                'kategori' => 'biasa'
                            ]);
                        }
                    } else {
                        if ($r->kategori == 'cetak') {
                            DB::table('tb_hariandll')->where('id_hariandll', $rowData[0])->update([
                                'tgl' => $tanggalFormatted,
                                'id_anak' => $rowData[2],
                                'ket' => $rowData[4],
                                'pcs' => $rowData[5],
                                'gr' => $rowData[6],
                                'lokasi' => $rowData[7],
                                'rupiah' => $rowData[8],
                                'kategori' => 'cetak'
                            ]);
                        } else {
                            DB::table('tb_hariandll')->where('id_hariandll', $rowData[0])->update([
                                'tgl' => $tanggalFormatted,
                                'id_anak' => $rowData[2],
                                'ket' => $rowData[4],
                                'lokasi' => $rowData[5],
                                'rupiah' => $rowData[6],
                                'kategori' => 'biasa'
                            ]);
                        }
                    }
                }
                DB::commit(); // Konfirmasi transaksi jika berhasil
                return redirect()->route('hariandll.index')->with('sukses', 'Data berhasil import');
            } catch (\Exception $e) {
                DB::rollback(); // Batalkan transaksi jika terjadi kesalahan lain
                return redirect()->route('hariandll.index')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
            }
        } else {
            return redirect()->route('hariandll.index')->with('error', 'File yang diunggah bukan file Excel yang valid');
        }
    }
}
