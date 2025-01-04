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
    public $pilihanLimbah;
    public $jenisLimbah = [
        'bulu',
        'organik',
        'non organik'
    ];

    #[Url]
    public $bulan;
    #[Url]
    public $tahun = 2025;
    #[Url]
    public $jenis_limbah;

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
        $this->pilihanLimbah = $this->jenis_limbah;
    }

    public function updatedSelectedBulan($value)
    {
        if ($value) {
            // Convert month number to number of days
            $this->daysInMonth = Carbon::create(2025, $value)->daysInMonth;
        }
    }
    public function updatedPilihanLimbah($value)
    {
        if ($value) {
            
        }
    }

    public function ceklis($tgl, $waktu)
    {
        $existingData = DB::table('hrga7_pembuangan_sampah')
            ->where('jenis_sampah', $this->pilihanLimbah)
            ->where('tgl', "$this->tahun-$this->selectedBulan-$tgl")
            ->where('jam_cek', $waktu)
            ->first();

        if ($existingData) {
            DB::table('hrga7_pembuangan_sampah')
                ->where('id', $existingData->id)
                ->delete();
        session()->flash('sukses', 'Data berhasil dihapus!');

        } else {
            DB::table('hrga7_pembuangan_sampah')->insert([
                'jenis_sampah' => $this->pilihanLimbah,
                'tgl' => "$this->tahun-$this->selectedBulan-$tgl",
                'jam_cek' => $waktu,
                'admin' => auth()->user()->name
            ]);
        }
        $this->updatedSelectedBulan($this->selectedBulan);
        $this->updatedPilihanLimbah($this->pilihanLimbah);
        session()->flash('sukses', 'Data berhasil disimpan!');
    }

    public function render()
    {
        return view('livewire.pembuangan-sampah');
    }
}
