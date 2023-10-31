<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function index()
    {
        $sub_navbar = DB::table('sub_navbar')->get();
        $data = [
            'title' => 'Permission',
            'users' => DB::table('users as a')->get(),
            'data_master' => $sub_navbar->where('navbar', 1),
            'home' => $sub_navbar->where('navbar', 2),
            
        ];
        return view('data_master.permission.index', $data);
    }

    public function create(Request $r)
    {
        DB::table('permission_navbar')->truncate();
        // Ambil semua data dari formulir
        $data = $r->all();
        // Looping untuk menyimpan setiap ceklis sesuai ID user
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // Misalnya, jika nama field checkbox adalah 'home_{user_id}[]' atau 'data_master_{user_id}[]'
                // Anda bisa memisahkan ID user dari nama field untuk menyimpan ke tabel yang sesuai
                $user_id = explode('_', $key)[1]; // Mengambil ID user dari nama field

                // Simpan data ke dalam tabel sesuai dengan ID user
                // Lakukan proses penyimpanan ke database sesuai dengan kebutuhan aplikasi Anda
                // Contoh: menyimpan ID sub navbar yang diceklis ke dalam tabel permission_navbar
                foreach ($value as $item) {
                    DB::table('permission_navbar')->insert(
                        ['id_user' => $user_id, 'id_sub_navbar' => $item],
                    );
                }
            }
        }

        return redirect()->route('permission.index')->with('sukses', 'Data Berhasil ditambahkan');
    }
}
