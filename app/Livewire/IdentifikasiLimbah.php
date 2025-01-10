<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class IdentifikasiLimbah extends BaseFunction
{

    public $formLimbah = [];
    public $tbl = 'hrga7_identifikasi_limbah';


    public function store()
    {
        $form = $this->formLimbah;
        DB::table($this->tbl)->insert([
            'area' => $form['area'],
            'limbah' => $form['limbah'],
            'metode' => $form['metode'],
            'ket' => $form['ket'] ?? '',
            'tgl_input' => date('Y-m-d'),
            'admin' => auth()->user()->name
        ]);
        $this->reset('formLimbah');
        $this->alert('sukses', 'Data Berhasil diubah');
    }
    public function saveEdit($id,$editedData)
    {
        DB::table($this->tbl)->where('id', $id)->update($editedData);
        $this->alert('sukses', 'Data Berhasil diubah');
    }

    public function destroy($id)
    {
        DB::table($this->tbl)->where('id', $id)->delete();
        $this->alert('sukses', 'Data Berhasil dihapus');
    }

    public function render()
    {
        $data = [
            'limbah' => DB::table($this->tbl)->orderBy('id', 'desc')->get()
        ];
        return view('livewire.identifikasi-limbah',$data);
    }
}
