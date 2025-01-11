<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;

class CeklistSuhuColdStorage extends BaseFunction
{
    public
        $bulans,
        $selectedBulan,
        $selectedRuangan,
        $selectedStandardSuhu,$adaData;

    public $daysInMonth = 31;
    public $tbl = 'hrga8_ceklist_suhu_cold_storage';
    public $inputValues = [];
    public $tbh = [];

    #[Url] public $ruangan, $standardSuhu, $bulan;
    #[Url] public $tahun = 2025;

    public function mount()
    {
        $this->bulans = DB::table('bulan')->get();
        $this->selectedBulan = $this->bulan;
        $this->selectedRuangan = $this->ruangan;
        $this->selectedStandardSuhu = $this->standardSuhu;

        $this->cekData();
    }

    public function cekData()
    {
        $this->reset('inputValues');

        // Ambil data dari tabel
        $data = DB::table($this->tbl)
            ->whereYear('tgl', $this->tahun)
            ->whereMonth('tgl', $this->selectedBulan)
            ->where([
                ['ruangan', $this->selectedRuangan],
                ['standar_suhu', $this->selectedStandardSuhu]
            ])
            ->get();
        $this->adaData = count($data) > 0 ? 'ada' : 'tidak';
        // Tandai tanggal yang memiliki data sebagai readonly
        foreach ($data as $row) {
            $tgl = date('d', strtotime($row->tgl));
            $bulan = date('m', strtotime($row->tgl));
            $this->inputValues[(int)$bulan][(int)$tgl] = [
                'suhu' => $row->suhu ?? '',
                'keterangan' => $row->keterangan ?? '',
                'pemeriksa' => $row->pemeriksa ?? '',
            ];
        }
    }

    public function store()
    {

        $cek = DB::table($this->tbl)
            ->where('ruangan', $this->selectedRuangan)
            ->where('standar_suhu', $this->selectedStandardSuhu)
            ->first();

        if ($cek) {
            $this->alert('error', 'data sudah ada');
            return;
        }
        $tgl = "$this->tahun-$this->selectedBulan-01";
        DB::table($this->tbl)->insert([
            'ruangan' => $this->selectedRuangan,
            'standar_suhu' => $this->selectedStandardSuhu,
            'tgl' => $tgl,
        ]);
        

        $this->ruangan = $this->selectedRuangan;
        $this->standardSuhu = $this->selectedStandardSuhu;
        $this->bulan = $this->selectedBulan;

        $this->cekData();
        $this->alert('sukses', 'berhasil disimpan');
    }
    public function ubah($kolom, $tgl, $value)
    {
        $tglSet = "$this->tahun-$this->selectedBulan-$tgl";
        DB::table($this->tbl)->updateOrInsert(
            [
                'tgl' => $tglSet,
                'ruangan' => $this->selectedRuangan,
                'standar_suhu' => $this->selectedStandardSuhu
            ],
            [
                $kolom => $value,
                'ruangan' => $this->selectedRuangan,
                'standar_suhu' => $this->selectedStandardSuhu,
                'admin' => auth()->user()->name
            ]
        );
        $this->cekData();
        $this->alert('sukses', 'berhasil disimpan');
    }

    public function updatedSelectedBulan($value)
    {
        if ($value) {
            // Convert month number to number of days
            $this->daysInMonth = Carbon::create(2023, $value)->daysInMonth;
        }
    }

    public function updatedSelectedRuangan($value)
    {
        $this->selectedRuangan = $value;
        $this->ruangan = $value;
        $this->cekData();
    }

    public function updatedSelectedStandardSuhu($value)
    {
        $this->selectedStandardSuhu = $value;
        $this->standardSuhu = $value;
        $this->cekData();
    }

    public function render()
    {
        return view('livewire.ceklist-suhu-cold-storage');
    }
}
