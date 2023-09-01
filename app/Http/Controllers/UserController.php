<?php

namespace App\Http\Controllers;

use App\Models\Posisi;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data User',
            'user' => User::with('posisi')->where('posisi_id', 1)->get(),
            'posisi' => Posisi::all()
        ];
        return view('user.user', $data);
    }

    public function create(Request $r)
    {
        User::create([
            'name' => $r->name, 
            'email' => $r->email,
            'posisi_id' => $r->posisi_id,
            'password' => bcrypt($r->password),
        ]);

        return redirect()->route('user.index')->with('sukses', 'Data Berhasil Dibuat');
    }
    
    public function delete(Request $r)
    {
        User::find($r->id_user)->delete();
        return redirect()->route('user.index')->with('sukses', 'Data Berhasil Dihapus');
    }
}
