<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Divisi;

class DivisiController extends Controller
{


    public function index(Request $r)
    {
        $divisi = $r->param;
        $adaDivisi = $r->adaDivisi ?? 'Y';
        $title = $r->deskripsi;

        if ($adaDivisi == 'Y' && in_array(auth()->user()->posisi_id, [1, 12]) && $divisi != 'hrga2_5' && $divisi != 'hrga3_2') {
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
