<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Illuminate\Support\Carbon;

class AddBaruSanitasi extends Component
{
    public $selectedBulan;
    public $selectedArea;
    public $daysInMonth = 31;
    public $bulans;
    public $petugas;
    public $verifikator;
    public $lokasis;
    public $itemSanitasi;
    public $id_lokasi;
    public $items = [];

    public $openRedirect = false;

    public function mount()
    {
        $this->bulans = DB::table('bulan')->get();
        $this->lokasis = DB::table('lokasi')->get();
        
    }
    public function addRow()
    {
        $this->items[] = ['name' => ''];
    }

    public function removeRow($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function tbhItem()
    {
        if (empty($this->selectedBulan)) {
            session()->flash('error', 'Bulan harus diisi!');
            return;
        }
        if (empty($this->selectedArea)) {
            session()->flash('error', 'Area harus diisi!');
            return;
        }

        $checkData = DB::table('sanitasi')
            ->where('id_lokasi', $this->id_lokasi)
            ->whereMonth('tgl', $this->selectedBulan)
            ->first();

        if ($checkData) {
            session()->flash('error', 'Data untuk bulan ' . $this->selectedBulan . ' dan area ' . $this->selectedArea . ' sudah ada!');
            $this->openRedirect = true;
            return;
        }

        foreach ($this->items as $item) {
            if (!empty($item['name'])) {
                $id_item = DB::table('item_pembersihan')->insertGetId([
                    'nama_item' => $item['name'],
                ]);

                DB::table('sanitasi')->insert([
                    'id_lokasi' => $this->selectedArea,
                    'id_item' => $id_item,
                    'tgl' => "2025-$this->selectedBulan->01",
                    'admin' => auth()->user()->name
                ]);
            }
        }

        session()->flash('sukses', 'Item berhasil disimpan!');
        $this->items = [['name' => '']]; // Reset form

    }
    public function updatedSelectedBulan($value)
    {
        if ($value) {
            // Convert month number to number of days
            $this->daysInMonth = Carbon::create(2023, $value)->daysInMonth;
            $this->itemSanitasi = DB::table('sanitasi as a')
            ->leftJoin('item_pembersihan as b', 'b.id_item', '=', 'a.id_item')
            ->where('a.id_lokasi', $this->id_lokasi)
            ->whereMonth('a.tgl', $value)
            ->groupBy('a.id_item')
            ->selectRaw('a.id_item, b.nama_item,a.tgl,a.paraf_petugas,a.verifikator')
            ->get();
        }
    }
    public function updatedSelectedArea($value)
    {
        if ($value) {
            // Convert month number to number of days
            $this->id_lokasi = $value;
            $this->itemSanitasi = DB::table('sanitasi as a')
            ->leftJoin('item_pembersihan as b', 'b.id_item', '=', 'a.id_item')
            ->where('a.id_lokasi', $this->id_lokasi)
            ->whereMonth('a.tgl', $this->selectedBulan)
            ->groupBy('a.id_item')
            ->selectRaw('a.id_item, b.nama_item,a.tgl,a.paraf_petugas,a.verifikator')
            ->get();
        }
    }

    public function render()
    {
        
        return view('livewire.add-baru-sanitasi');
    }
}
