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
            'users' => DB::table('users as a')
            ->orderBy(DB::raw('CAST(a.id AS UNSIGNED)'), 'ASC')
            ->get(),
            'data_master' => $sub_navbar->where('navbar', 1),
            'home' => $sub_navbar->where('navbar', 2),
        ];
        return view('data_master.permission.index', $data);
    }

    public function create(Request $r)
    {
        DB::table('permission_navbar')->truncate();
        $id_user = auth()->user()->id;
        if(!empty($r->home)) {
            foreach($r->home as $d) {
                DB::table('permission_navbar')->insert([
                    'id_user' => $id_user,
                    'id_sub_navbar' => $d
                ]);
            }
        }

        return redirect()->route('permission.index')->with('sukses', 'Data Berhasil ditambahkan');
    }
}
