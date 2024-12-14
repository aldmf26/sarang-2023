<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Divisi;

class DivisiController extends Controller
{


    public function index(Request $r)
    {
        $divisi = $r->param;
        $title = $r->deskripsi;
        if (in_array(auth()->user()->posisi_id, [1, 12])) {
            $divisis = Divisi::orderBy('urutan')->get();
        
            $data = [
                'title' => $title,
                'divisis' => $divisis,
                'divisi' => $divisi,
            ];
            return view('hccp.divisi', $data);
        } else {
            return redirect()->route("$divisi.index");
        }
    }
}
