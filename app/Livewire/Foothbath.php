<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;

class Foothbath extends Component
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
        return DB::table('foothbath_ceklis as a')
            ->leftJoin('foothbath_template as b', 'b.id', '=', 'a.id_frekuensi')
            ->where('a.id_lokasi', $id_lokasi)
            ->whereMonth('a.tgl', $selectedBulan)
            ->groupBy('a.id_frekuensi')
            ->selectRaw('a.id_frekuensi, b.frekuensi,b.item,a.tgl,a.paraf_petugas,a.verifikator, count(a.tgl) as ttl')
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

    public function tbhSanitasi($id_sanitasi, $id_item, String $tgl, String $klik)
    {
        $standar = $klik == 'kiri' ? 1 : 2;

        // Jika id_sanitasi = 0 (data baru), cek berdasarkan kriteria lain
        $existing = DB::table('foothbath_ceklis')
            ->where('id_lokasi', $this->selectedArea)
            ->where('id_frekuensi', $id_item)
            ->where('tgl', $tgl)
            ->first();

        if ($existing) {
            // Hapus berdasarkan id yang ada
            DB::table('foothbath_ceklis')
                ->where('id', $existing->id)
                ->delete();
        } else {
            // Insert data baru
            DB::table('foothbath_ceklis')->insert([
                'id_lokasi' => $this->selectedArea,
                'id_frekuensi' => $id_item,
                'tgl' => $tgl,
                'status' => $standar,
                'admin' => auth()->user()->name
            ]);
        }

        $this->updatedSelectedBulan($this->selectedBulan);
    }

    public function updatedSelectedBulan($value)
    {
        if ($value) {
            // Convert month number to number of days
            $this->daysInMonth = Carbon::create(2023, $value)->daysInMonth;
            $this->itemSanitasi = $this->loadSanitasi($this->id_lokasi, $this->selectedBulan);
        }
    }

    public function tbhParaf($kolom, $nama, String $tgl)
    {
        $cek = DB::table('foothbath_ceklis')
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
        $foothbathTemplate = DB::table('foothbath_template as a')
            ->selectRaw('a.id, a.item, a.frekuensi, 
                (SELECT COUNT(*) FROM foothbath_ceklis as b 
                    WHERE b.id_frekuensi = a.id AND b.status = 1) as ttl_status_1,
                (SELECT COUNT(*) FROM foothbath_ceklis as b 
                    WHERE b.id_frekuensi = a.id AND b.status = 2) as ttl_status_2')
            ->get();
        $adminSanitasi = DB::table('admin_sanitasi as a')
            ->join('users as b', 'b.id', '=', 'a.id')
            ->selectRaw('a.id, b.name, a.posisi')
            ->get()
            ->groupBy('posisi');

        $data = [
            'foothbathTemplate' => $foothbathTemplate,
            'adminSanitasi' => $adminSanitasi,
        ];
        return view('livewire.foothbath', $data);
    }
}
