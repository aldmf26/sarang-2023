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
        $data = [
            'title' => 'Harian DLL',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'anak' => $this->anak(),
            'datas' => DB::table('tb_hariandll  as a')
                ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->where([['ditutup', 'T'], ['b.id_pengawas', $id_user]])
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
                'id_anak' => $r->id_anak[$i],
                'ket' => $r->ket[$i],
                'rupiah' => $rupiah,
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
    public function getQuery($tgl1, $tgl2)
    {
        return DB::select("SELECT a.tgl,b.nama,c.name, GROUP_CONCAT(DISTINCT ket, ',') AS ket,GROUP_CONCAT(DISTINCT lokasi, ',') AS lokasi, SUM(rupiah) AS total_rupiah
        FROM tb_hariandll as a
        LEFT JOIN tb_anak as b on a.id_anak = b.id_anak
        LEFT JOIN users as c on c.id = b.id_pengawas
        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2'
        GROUP BY a.id_anak;");
    }
    public function export(Request $r)
    {
        $id_user = auth()->user()->id;
        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $view = 'home.hariandll.export';
        $tbl = DB::table('tb_hariandll  as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->where([['ditutup', 'T'], ['b.id_pengawas', $id_user]])
            ->orderBy('a.id_hariandll', 'DESC')
            ->get();

        return Excel::download(new HariandllExport($tbl, $view), 'Export HARIAN DLL.xlsx');
    }

    public function rekap(Request $r)
    {

        $tgl = tanggalFilter($r);
        $tgl1 =  $tgl['tgl1'];
        $tgl2 =  $tgl['tgl2'];
        $datas = $this->getQuery($tgl1, $tgl2);

        $data = [
            'title' => 'Rekap Summary Cetak',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'datas' => $datas,
        ];

        return view('home.hariandll.rekap', $data);
    }

    public function export_rekap(Request $r)
    {
        $id_user = auth()->user()->id;
        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $view = 'home.hariandll.export_rekap';
        $tbl = $this->getQuery($tgl1, $tgl2);

        return Excel::download(new HariandllExport($tbl, $view), 'Export REKAP HARIAN DLL.xlsx');
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

                        DB::table('tb_hariandll')->insert([
                            'tgl' => $tanggalFormatted,
                            'id_anak' => $rowData[2],
                            'ket' => $rowData[4],
                            'lokasi' => $rowData[5],
                            'rupiah' => $rowData[6],
                        ]);
                    } else {

                        DB::table('tb_hariandll')->where('id_hariandll', $rowData[0])->update([
                            'tgl' => $tanggalFormatted,
                            'id_anak' => $rowData[2],
                            'ket' => $rowData[4],
                            'lokasi' => $rowData[5],
                            'rupiah' => $rowData[6],
                        ]);
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
