<?php

namespace App\Http\Controllers;

use App\Models\Posisi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengawasController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data Pengawas',
            'user' => User::with('posisi')->where('posisi_id', 13)->get(),
            'posisi' => Posisi::all()
        ];
        return view('data_master.pengawas.index', $data);
    }
    public function anak()
    {
        $data = [
            'title' => 'Data Anak',
            'user' => DB::table('tb_anak as a')
                        ->leftJoin('users as b', 'a.id_pengawas', 'b.id')
                        ->join('tb_kelas as c', 'a.id_kelas', 'c.id_kelas')
                        ->where(function($query) {
                            $query->where('b.posisi_id', 13)
                                  ->orWhereNull('a.id_pengawas');
                        })
                        ->orderBy('a.id_anak', 'DESC')
                        ->get(),
            'pengawas' => User::with('posisi')->where('posisi_id', 13)->get(),

        ];
        return view('data_master.pengawas.anak', $data);
    }

    public function create_anak(Request $r)
    {
        $tb_user_tugas = array(
            array('kd_user' => '84','nm_user' => 'fatimah','username' => 'fatimah','password' => '$2y$10$9AB14.BT3GfU8jiX/6k33OumQiqXkBMs3.5qeNFfaHL.O1HWL/DuK'),
            array('kd_user' => '85','nm_user' => 'Jenah','username' => 'jenah','password' => '$2y$10$qTYW7XrsyyMs1NpxIDvx6uGLQoe..bGaBvnkpQzz0i2iZl39MmjPW'),
            array('kd_user' => '87','nm_user' => 'yuyun','username' => 'yuyun','password' => '$2y$10$p21KEsT2nQlYWDAyp0Uoheevh2hGQDqeqrHM5nBqEWPti9s.x3Gzy'),
            array('kd_user' => '88','nm_user' => 'Abin','username' => 'abin','password' => '$2y$10$yQwqtxOgHHHs0ZW4mJclcOCpG2BgOJ3657NeJbxajh./T0ak1kxym'),
            array('kd_user' => '89','nm_user' => 'ratna','username' => 'ratna','password' => '$2y$10$y74kWnn4vWvtRJQQfzmvT.F5.UG7OAHLY5GW.1/UXbOxgsH/bYWk6'),
            array('kd_user' => '90','nm_user' => 'nurul','username' => 'nurul','password' => '$2y$10$3pYlVqQAzc5kM0WfGOTye.s7KXuAWtfuDpqfXL98jGEykDi6hoUaG'),
            array('kd_user' => '91','nm_user' => 'Arbayah','username' => 'Arbayah','password' => '$2y$10$PoLfz09Meyg0.R8IkD7hauZZh7styPAqhdjqbJQxBaV3qTybC0zOu'),
            array('kd_user' => '92','nm_user' => 'Sari rahmah','username' => 'sarirahmah','password' => '$2y$10$RE.qDbByX4cGDiGBXIO0V.P0szFr2LbvBhqHAjmcZpbKFCldg4/AS'),
            array('kd_user' => '93','nm_user' => 'Erna','username' => 'Erna','password' => '$2y$10$gWx0f7RoFPIX/A79neDuR.YiqVgK22ywHAQC64YbOoXPqN/2XtIPG'),
            array('kd_user' => '94','nm_user' => 'abin','username' => 'abin2','password' => '$2y$10$ggqoLJrSRiYYV8/AXsNtJOR87H7FSgxRTmiFsY8y3E.d3sJOTlOT2'),
            array('kd_user' => '95','nm_user' => 'Yuli','username' => 'yuli','password' => '$2y$10$M4xfQJ8anxs0DKESjeM9b.4LlF3r/3AAkguVh4LVDfbL2vIT8xQTi'),
            array('kd_user' => '99','nm_user' => 'Fatmi','username' => 'fatmi','password' => '$2y$10$Jb2vvuBcesbDy4Z41w2HDesoSVC6Eo01OlyTzcLHf3HsFC12JU7t6'),
            array('kd_user' => '100','nm_user' => 'Lastri','username' => 'lastri','password' => '$2y$10$eqWKVaa.Gwi4mQ.GA.hl8OCr2XvUTnilUJNw/Uqcerl.MDsK9pKmu'),
            array('kd_user' => '101','nm_user' => 'Laila','username' => 'lailasby','password' => '$2y$10$ab9qidHNdgpLfsuiEZBAxeWWnE5QehY/C.itbQWT7U8fIlWjxAWRK'),
            array('kd_user' => '102','nm_user' => 'Hur','username' => 'hur','password' => '$2y$10$qkMkIBQcwoCOt57tz39HbeyaMVtBh8tFUBpsvgje.i0WXkOeVeOgK'),
            array('kd_user' => '103','nm_user' => 'Silvia','username' => 'silvia','password' => '$2y$10$qIxaBmkCNb0afIqWaThQ6e0j9cd5z3f/bo5RadSagCihSSsJTTkSe'),
            array('kd_user' => '104','nm_user' => 'Martadah','username' => 'martadah','password' => '$2y$10$GUtYYAJ8KjAO2FlSG6QDteBmvDco6tFXU0MbURlxL7akK3l5YWPoi'),
            array('kd_user' => '265','nm_user' => 'sinta','username' => 'sinta','password' => '$2y$10$3vbz6y9JZgP3PTyQ7YASoOf/.RpmSKQWtlYo7sGl6k9yDGQWETz82'),
            array('kd_user' => '279','nm_user' => 'sanah','username' => 'sanah','password' => '$2y$10$g10lw5Bd1lYwEaekrQDR0eMcvbqBmrBHnAKz5vGtjyO7oiUBhfVhO'),
            array('kd_user' => '282','nm_user' => 'siti khadijah','username' => 'sitikhadijah','password' => '$2y$10$qTYW7XrsyyMs1NpxIDvx6uGLQoe..bGaBvnkpQzz0i2iZl39MmjPW'),
            array('kd_user' => '283','nm_user' => 'Saudiah','username' => 'Saudiah','password' => '$2y$10$AADus7ImNRi3vRPOSI6tAO8yY5RMerQcTNyxlCr1h0Eck5qy6hLpu'),
            array('kd_user' => '284','nm_user' => 'nurul b','username' => 'nurulb','password' => '$2y$10$Y0TRhUvtiYgHZps6mu5mE.EArOXzmehoEDTeEp9x3fEYg6eUxj6RC'),
            array('kd_user' => '285','nm_user' => 'Tiyah','username' => 'tiyah','password' => '$2y$10$vtOT8uyWeTMmIhn1hlaa4OA2wzP83DNBBKm0J3HvL9lkMszW32gEq'),
            array('kd_user' => '288','nm_user' => 'siti fatimah b','username' => 'sitifatimahb','password' => '$2y$10$r0uvV9BHgKdZvWLfmV9VAuAZ9LlUGzttPw//o3.25/jmW1Ml4QfZK'),
            array('kd_user' => '418','nm_user' => 'lastrikk','username' => 'lastrikk','password' => '$2y$10$7MUholXc7yZFnoEFCoKXzuDSZz1gZ4IQexFvYaQv5uvHyt5tjtGlS'),
            array('kd_user' => '419','nm_user' => 'Laila pgws','username' => 'lailapgws','password' => '$2y$10$zwALpzz0Dj77mTRSYt4ZleE8rgJdE4yCdZxs..YxAQe7cRD4EGvDS'),
            array('kd_user' => '420','nm_user' => 'sufatmi','username' => 'Sufatmi','password' => '$2y$10$S9hmjtSEY9PkTVO//ELSu.l3hRszVUlREypwkMj462WoJbpkBT7bu'),
            array('kd_user' => '421','nm_user' => 'hur sby','username' => 'hur sby','password' => '$2y$10$6MKDhFAikLeYoXriPrDdUOa2Ka.Em4dujomeKRcd2SMnnAqO9zEM2'),
            array('kd_user' => '422','nm_user' => 'silvia sby','username' => 'silviasby','password' => '$2y$10$HMx3e6MsfcH8JSW76rTqYOSbeU0GrkPuKxUqUkY2ilkk9myqbszcm'),
            array('kd_user' => '445','nm_user' => 'Masitah','username' => 'Sitah','password' => '$2y$10$Tj.kmETdmX5HoARmRpB1vOuqI6mu9NpufDYBK.z8Gy5bQ7YC76tB6'),
            array('kd_user' => '457','nm_user' => 'Pengawasxx','username' => 'pengawasxx','password' => '$2y$10$/R/EAiTRSAS/Krxf2qR4AebGQleTCV3I9RiFzRmOVICtgBhZedsom')
          );
        //   foreach($tb_user_tugas as $n => $d){
        //     DB::table('users')->insert([
        //         'posisi_id' => 13,
        //         'id' => $d['kd_user'],
        //         'name' => $d['nm_user'],
        //         'email' => $d['username'] . "@gmail.com",
        //         'password' => $d['password']
        //     ]);

        //   }
        DB::table('tb_anak')->insert([
            'tgl_masuk' => $r->tgl_masuk,
            'nama' => $r->nama,
            'id_kelas' => $r->kelas,
            'id_pengawas' => $r->id_pengawas,
        ]);

        return redirect()->route('pengawas.anak')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function anak_detail($id)
    {
        $detail = DB::table('tb_anak')->where('id_anak', $id)->first();
        if(empty($detail)) {
            abort(404);
        }
        $data = [
            'detail' => $detail,
            'pengawas' => User::with('posisi')->where('posisi_id', 13)->get(),
        ];
        return view("data_master.pengawas.anak_detail", $data);

    }
    public function update_anak(Request $r)
    {
        DB::table('tb_anak')->where('id_anak', $r->id)->update([
            'tgl_masuk' => $r->tgl_masuk,
            'nama' => $r->nama,
            'kelas' => $r->kelas,
            'id_pengawas' => $r->id_pengawas,
        ]);

        return redirect()->route('pengawas.anak')->with('sukses', 'Data Berhasil ditambahkan');
    }
    public function destroy_anak($id)
    {
        DB::table('tb_anak')->where('id_anak', $id)->delete();
        return redirect()->route('pengawas.anak')->with('sukses', 'Data Berhasil dihapus');
    }
}
