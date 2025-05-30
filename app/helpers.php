<?php

use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

if (!function_exists('tanggalFilter')) {
    function tanggalFilter(Request $r)
    {
        $period = $r->period;
        $bulan = $r->bulan;
        $tahun = $r->tahun;
        $tgl1 = $r->tgl1;
        $tgl2 = $r->tgl2;

        $result = [];
        $today = date('Y-m-d');
        $firstDayOfMonth = date('Y-m-01');

        switch ($period) {
            case 'daily':
                $result = ['tgl1' => $today, 'tgl2' => $today];
                break;
            case 'weekly':
                $sixDaysAgo = date('Y-m-d', strtotime("-6 days"));
                $result = ['tgl1' => $sixDaysAgo, 'tgl2' => $today];
                break;
            case 'mounthly':
                $tglawal = "$tahun-$bulan-01";
                $tglakhir = "$tahun-$bulan-" . date('t', strtotime($tglawal));
                $result = ['tgl1' => $tglawal, 'tgl2' => date('Y-m-t', strtotime($tglakhir))];
                break;
            case 'costume':
                $result = ['tgl1' => $tgl1, 'tgl2' => $tgl2];
                break;
            case 'years':
                $tgl_awal = "$tahun-01-01";
                $tgl_akhir = "$tahun-12-31";
                $result = ['tgl1' => date('Y-m-01', strtotime($tgl_awal)), 'tgl2' => date('Y-m-t', strtotime($tgl_akhir))];
                break;
            default:
                $result = ['tgl1' => $firstDayOfMonth, 'tgl2' => date('Y-m-t')];
                break;
        }

        return $result;
    }
}

if (!function_exists('tanggal')) {
    function tanggal($tgl)
    {
        $date = explode("-", $tgl);

        $bln  = $date[1];

        switch ($bln) {
            case '01':
                $bulan = "Januari";
                break;
            case '02':
                $bulan = "Februari";
                break;
            case '03':
                $bulan = "Maret";
                break;
            case '04':
                $bulan = "April";
                break;
            case '05':
                $bulan = "Mei";
                break;
            case '06':
                $bulan = "Juni";
                break;
            case '07':
                $bulan = "Juli";
                break;
            case '08':
                $bulan = "Agustus";
                break;
            case '09':
                $bulan = "September";
                break;
            case '10':
                $bulan = "Oktober";
                break;
            case '11':
                $bulan = "November";
                break;
            case '12':
                $bulan = "Desember";
                break;
        }
        $tanggal = $date[2];
        $tahun   = $date[0];

        $strTanggal = "$tanggal $bulan $tahun";
        return $strTanggal;
    }
}

if (!function_exists('kode')) {

    function kode($kode)
    {
        return str_pad($kode, 5, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('getListBulan')) {
    function getListBulan()
    {
        // $bulanTerakhir = DB::table('tb_gaji_penutup')->latest('tahun_dibayar')->latest('bulan_dibayar')->first();
        // $tahunSekarang = date('Y');

        // if ($bulanTerakhir && $bulanTerakhir->tahun_dibayar == $tahunSekarang) {
        //     // Jika tahun sama, tampilkan bulan setelah bulan_dibayar terakhir
        //     $listBulan = DB::table('bulan')
        //         ->where('id_bulan', '>', $bulanTerakhir->bulan_dibayar)
        //         ->get();
        // } else {
        //     // Jika tahun berbeda, tampilkan semua bulan (Januari - Desember)
        // }
        $listBulan = DB::table('bulan')->where('bulan', 6)->get();

        return $listBulan;
    }
}

if (!function_exists('formatTglGaji')) {
    function formatTglGaji($bulan, $tahun)
    {
        return date('M Y', strtotime($tahun . '-' . $bulan . '-01'));
    }
}

if (!function_exists('buatNota')) {
    function buatNota($tbl, $kolom)
    {
        $max = DB::table($tbl)->latest($kolom)->first();
        return empty($max) ? 1000 : $max->$kolom + 1;
    }
}

if (!function_exists('tglFormat')) {
    function tglFormat($tgl)
    {
        return date('d M y', strtotime($tgl));
    }
}

if (!function_exists('sumCol')) {
    function sumCol($datas, $col)
    {
        return array_sum(array_column($datas, $col));
    }
}

if (!function_exists('sumBk')) {
    function sumBk($kategori, $data)
    {
        return array_sum(array_column($kategori, $data));
    }
}

if (!function_exists('setSessionDivisi')) {
    function setSessionDivisi($r)
    {
        if ($r->has('divisi')) {
            // Reset the session for 'divisi'
            session()->forget('divisi');
            // Retrieve 'divisi' and 'id' from the Divisi model
            $divisi = Divisi::find($r->divisi);
            // Set the session with the new 'divisi' and 'id'
            $session = session([
                'divisi' => $divisi->divisi,
                'id_divisi' => $divisi->id
            ]);
        }
        return $session ?? '';
    }
}

if (!function_exists('Umur')) {
    function Umur($tgl1, $tgl2)
    {
        $tglLahir = new DateTime($tgl1);
        $today = new DateTime($tgl2);
        $interval = $tglLahir->diff($today);
        return $interval->y;
    }
}

if (!function_exists('rumusTotalRp ')) {
    function rumusTotalRp($detail)
    {
        $result = json_decode('{}'); // Buat objek kosong

        $susut = empty($detail->gr_awal) ? 0 : (1 - ($detail->gr_flx + $detail->gr_akhir) / $detail->gr_awal) * 100;
        $batas_eot = empty($detail->gr_awal) ? 0 : $detail->gr_awal * $detail->batas_eot;
        $denda = 0;
        $bonus_susut = 0;
        $rupiah = $detail->rupiah;

        if ($susut > $detail->batas_susut) {
            $denda = ($susut - $detail->batas_susut) * ($detail->denda_susut_persen / 100) * $detail->rupiah;
            $rupiah = $rupiah - $denda;
        }
        if ($susut < $detail->bonus_susut) {
            $bonus_susut = $detail->rp_bonus != 0  ? ($detail->rp_bonus * $detail->gr_awal) / $detail->gr_kelas : 0;
        }

        $denda_hcr = $detail->pcs_hcr * 5000;
        $eot_bonus = ($detail->eot - $detail->gr_awal * $detail->batas_eot) * $detail->eot_rp;

        $ttl_rp = $rupiah - $denda_hcr + $eot_bonus + $bonus_susut;

        // Set nilai-nilai dalam objek menggunakan json_decode
        $result->susut = $susut;
        $result->batas_eot = $batas_eot;
        $result->denda = $denda;
        $result->bonus_susut = $bonus_susut;
        $result->rupiah = $rupiah;
        $result->denda_hcr = $denda_hcr;
        $result->eot_bonus = $eot_bonus;
        $result->ttl_rp = $ttl_rp;
        return $result;
    }
}
class Nonaktif
{
    public static function edit($tbl, $kolom, $kolomValue, $data)
    {
        DB::table($tbl)->where($kolom, $kolomValue)->update([
            'nonaktif' => 'Y'
        ]);

        DB::table($tbl)->insert($data);
    }

    public static function delete($tbl, $kolom, $kolomValue)
    {
        DB::table($tbl)->where($kolom, $kolomValue)->update([
            'nonaktif' => 'Y'
        ]);
    }
}
class SettingHal
{

    public static function akses($halaman, $id_user)
    {
        return DB::selectOne("SELECT a.*, b.id_permission_page FROM permission_button
        AS
        a
        LEFT JOIN (
        SELECT b.id_user, b.id_permission_button, b.id_permission_page FROM permission_perpage AS b
        WHERE b.id_user ='$id_user' AND b.permission_id = '$halaman'
        ) AS b ON b.id_permission_button = a.id_permission_button WHERE b.id_user = '$id_user'");
    }

    public static function btnHal($whereId, $id_user)
    {
        return DB::table('permission_perpage as a')
            ->join('permission_button as b', 'b.id_permission_button', 'a.id_permission_button')
            ->where([['a.id_permission_button', $whereId], ['a.id_user', $id_user]])
            ->first();
    }

    public static function btnSetHal($halaman, $id_user, $jenis)
    {
        return DB::select("SELECT a.*, b.id_permission_page FROM permission_button AS
        a
        LEFT JOIN (
        SELECT b.id_permission_button, b.id_permission_page FROM permission_perpage AS b
        WHERE b.id_user ='$id_user'
        ) AS b ON b.id_permission_button = a.id_permission_button
        WHERE a.jenis = '$jenis' AND a.permission_id = '$halaman'");
    }
}
