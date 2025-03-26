<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InputSusutAktual extends Component
{
    public $id_formulir,$input;
    public function ubah()
    {
        DB::table('formulir_sarang')->where('id_formulir', $this->id_formulir)->update([
            'sst_aktual' => $this->input
        ]);

    }
    public function render()
    {
        return view('livewire.input-susut-aktual');
    }
}
