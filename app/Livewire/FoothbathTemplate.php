<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;


class FoothbathTemplate extends Component
{
    public $items = [];
    public $disabled = false;

    public function addRow()
    {
        $this->items[] = ['name' => ''];
    }

    public function removeRow($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function store()
    {
        $this->disabled = true;
        foreach($this->items as $item) {
            DB::table('foothbath_template')->insert([
                'item' => $item['name'],
                'frekuensi' => $item['frekuensi']
            ]);
        }

        $this->items = [];
        $this->disabled = false;

    }

    public function delete($id)
    {
        DB::table('foothbath_template')->where('id', $id)->delete();
    }

    public function render()
    {
        $foothbathTemplate = DB::table('foothbath_template')->get();
        $data = [
            'foothbathTemplate' => $foothbathTemplate
        ];
        return view('livewire.foothbath-template', $data);
    }
}
