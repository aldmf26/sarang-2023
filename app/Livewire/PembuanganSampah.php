<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
class PembuanganSampah extends Component
{

    public $selectedBulan;
    public $selectedJenisLimbah;
    public $daysInMonth = 31;
    public $bulans;
    public $lokasis;
    public $itemSanitasi;
    public $items = [];

    public $jenisLimbah = [
        'bulu', 'organik', 'non organik'
    ];

    #[Url]
    public $bulan;
    #[Url]
    public $tahun = 2025;
    #[Url]
    public $id_lokasi;

    public function loadPembuanganSampah($selectedJenisLimbah, $selectedBulan)
    {
        return DB::table('hrga7_pembuangan_sampah')
                ->where('jenis_limbah', $selectedJenisLimbah)
                ->whereMonth('tgl', $selectedBulan)
                ->get();
    }

    public function mount()
    {
        $this->bulans = DB::table('bulan')->get();
        $this->lokasis = DB::table('lokasi')->get();
        $this->selectedBulan = $this->bulan;
    }

    public function updatedSelectedBulan($value)
    {
        if ($value) {
            // Convert month number to number of days
            $this->daysInMonth = Carbon::create(2025, $value)->daysInMonth;
        }
    }

    public function ceklis($tgl, $waktu)
    {
       
    }

    public function render()
    {
        return view('livewire.pembuangan-sampah');
    }
}
