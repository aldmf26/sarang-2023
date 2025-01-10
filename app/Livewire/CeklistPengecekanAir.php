<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;


class CeklistPengecekanAir extends BaseFunction
{
    public $bulans;
    public $selectedBulan;
    public $selectedJenisMesin;
    public $daysInMonth = 31;
    public $tbl = 'hrga8_ceklist_pengecekan_air';
    public $jenisMesin = [
        'baru',
        'lama'
    ];

    #[Url]
    public $bulan;
    #[Url]
    public $tahun = 2025;
    #[Url]
    public $jenis_mesin;

    

    public function mount()
    {
        $this->bulans = DB::table('bulan')->get();
        $this->selectedBulan = $this->bulan;
        $this->selectedJenisMesin = $this->jenis_mesin;
        
    }

    public function updatedSelectedBulan($value)
    {
        if ($value) {
            // Convert month number to number of days
            $this->daysInMonth = Carbon::create(2023, $value)->daysInMonth;
        }
    }

    public function ubah($kolom, $tgl, $value)
    {
        $tglSet = "$this->tahun-$this->selectedBulan-$tgl";

        DB::table($this->tbl)->updateOrInsert(
            ['tgl' => $tglSet], 
            [
                $kolom => $value,
                'jenis_mesin' => $this->selectedJenisMesin,
                'admin' => auth()->user()->name
            ]
        );
        $this->alert('sukses', 'berhasil disimpan');

    }

    public function render()
    {
        $adminSanitasi = DB::table('admin_sanitasi as a')
            ->join('users as b', 'b.id', '=', 'a.id')
            ->selectRaw('a.id, b.name, a.posisi')
            ->get()
            ->groupBy('posisi');

        return view('livewire.ceklist-pengecekan-air', compact('adminSanitasi'));
    }
}
