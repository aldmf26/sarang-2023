<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;

class Sanitasi extends Component
{
    public $selectedBulan;
    public $selectedArea;
    public $daysInMonth = 31;
    public $bulans;
    public $lokasis;
    public $itemSanitasi;
    public $items = [];

    #[Url]
    public $bulan;
    #[Url]
    public $tahun = 2025;
    #[Url]
    public $id_lokasi;

    public function loadSanitasi($id_lokasi, $selectedBulan)
    {
        return DB::table('item_perawatan as b')
            ->where('b.lokasi_id', $id_lokasi)
            ->leftJoin('sanitasi as a', function ($join) use ($id_lokasi, $selectedBulan) {
                $join->on('a.id_item', '=', 'b.id')
                    ->where('a.id_lokasi', $id_lokasi)
                    ->whereMonth('a.tgl', $selectedBulan);
            })
            ->groupBy('b.id', 'b.nama')
            ->selectRaw('b.id as id_item, b.nama as nama_item,b.no_identifikasi, a.tgl, a.paraf_petugas, a.verifikator, COUNT(a.tgl) as ttl')
            ->get();
    }
    public function mount()
    {
        $this->bulans = DB::table('bulan')->get();
        $this->lokasis = DB::table('lokasi')->get();
        $this->selectedBulan = $this->bulan;
        $this->selectedArea = $this->id_lokasi;
        $this->itemSanitasi = $this->loadSanitasi($this->id_lokasi, $this->selectedBulan);
    }

    public function updatedSelectedBulan($value)
    {
        if ($value) {
            // Convert month number to number of days
            $this->daysInMonth = Carbon::create(2023, $value)->daysInMonth;
            $this->itemSanitasi = $this->loadSanitasi($this->id_lokasi, $this->selectedBulan);
        }
    }
    public function updatedSelectedArea($value)
    {
        if ($value) {
            // Convert month number to number of days
            $this->id_lokasi = $value;
            $this->itemSanitasi = $this->loadSanitasi($this->id_lokasi, $this->selectedBulan);
        }
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
        if (empty($this->selectedArea)) {
            session()->flash('error', 'Area harus diisi!');
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
                    'tgl' => "$this->tahun-$this->selectedBulan-01",
                    'admin' => auth()->user()->name
                ]);
            }
        }
        $this->updatedSelectedBulan($this->selectedBulan);
        session()->flash('sukses', 'Item berhasil disimpan!');
        $this->items = [['name' => '']]; // Reset form
    }

    public function tbhSanitasi($id_sanitasi, $id_item, String $tgl)
    {
        $tglSanitasi = DB::table('sanitasi')->where('id_lokasi', $this->selectedArea)
            ->where('id_item', $id_item)
            ->where('tgl', $tgl)
            ->first();

        if ($tglSanitasi) {

            DB::table('sanitasi')
                ->where('id_sanitasi', $id_sanitasi)
                ->delete();
        } else {
            DB::table('sanitasi')->insert(
                [
                    'id_lokasi' => $this->selectedArea,
                    'id_item' => $id_item,
                    'tgl' => $tgl,
                    'admin' => auth()->user()->name
                ]
            );
        }
        $this->updatedSelectedBulan($this->selectedBulan);
    }



    public function tbhParaf($kolom, $nama, String $tgl)
    {
        $cek = DB::table('sanitasi')
            ->where('id_lokasi', $this->selectedArea)
            ->where('tgl', $tgl);
        if ($cek->first()) {
            $cek->update([
                'admin' => auth()->user()->name,
                $kolom => $nama
            ]);
            session()->flash('sukses', 'Data Berhasil disimpan');
        } else {
            session()->flash('error', 'Data Tidak ada');
        }
    }

    public function render()
    {
        $adminSanitasi = DB::table('admin_sanitasi as a')
            ->join('users as b', 'b.id', '=', 'a.id')
            ->selectRaw('a.id, b.name, a.posisi')
            ->get()
            ->groupBy('posisi');
        $data = [
            'itemSanitasi' => $this->itemSanitasi,
            'adminSanitasi' => $adminSanitasi
        ];
        return view('livewire.sanitasi', $data);
    }
}
