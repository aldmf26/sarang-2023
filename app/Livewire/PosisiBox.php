<?php

namespace App\Livewire;

use App\Models\Formulir;
use Livewire\Component;

class PosisiBox extends Component
{
    public $cariBox,$dataBox;

    public function updatedCariBox($value)
    {
        $this->cariBox = $value;
        $this->dataBox = Formulir::with(['penerima','pemberi'])->where('no_box', 'like', '%' . $value . '%')
            ->get();
        // Optionally, you can add any additional logic here when the search term changes
    }

    public function render()
    {
        return view('livewire.posisi-box');
    }
}
