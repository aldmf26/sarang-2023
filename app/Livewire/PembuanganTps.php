<?php

namespace App\Livewire;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;

class PembuanganTps extends BaseFunction
{
    public $selectedBulan;
    public $selectedJenisLimbah;
    public $daysInMonth = 31;
    public $bulans;
    public $lokasis;
    public $itemSanitasi;
    public $items = [];
    public $pilihanLimbah;
    public $tbl = 'hrga7_pembuangan_tps';
    public $jenisLimbah = [
        'bulu',
        'organik',
        'non organik'
    ];

    public $jamList = [
        ['time' => '07:00:00', 'label' => 'AM'],
    ];

    public $jamPengeluaran = [];
    public $tglCeklis = [];
    public $keterangan = [];

    #[Url]
    public $bulan;
    #[Url]
    public $tahun = 2025;
    #[Url]
    public $jenis_limbah;


    public function cekJam($jenisSampah, $bulan, $hari)
    {
        return DB::table($this->tbl)
            ->where('jenis_sampah', $jenisSampah)
            ->whereMonth('tgl', $bulan)
            ->whereDay('tgl', $hari)
            ->exists();
    }

    public function refreshData()
    {
        // Kosongkan data sebelumnya
        $this->jamPengeluaran = [];
        $this->tglCeklis = [];

        // Ambil data dari database berdasarkan bulan dan tahun yang dipilih
        $data = DB::table($this->tbl)
            ->whereYear('tgl', $this->tahun)
            ->whereMonth('tgl', $this->selectedBulan)
            ->where('jenis_sampah', $this->pilihanLimbah)
            ->get();

        // Format ulang data untuk jamPengeluaran dan checkedDates
        foreach ($data as $item) {
            $hari = (int)date('d', strtotime($item->tgl));
            $this->jamPengeluaran[$hari] = $item->jam_cek;
            $this->tglCeklis[$hari] = true;
        }
    }

    public function mount()
    {
        $this->bulans = DB::table('bulan')->get();
        $this->lokasis = DB::table('lokasi')->get();
        $this->selectedBulan = $this->bulan;
        $this->pilihanLimbah = $this->jenis_limbah;
        $this->refreshData();
    }

    public function updatedSelectedBulan($value)
    {
        if ($value) {
            // Convert month number to number of days
            $this->daysInMonth = Carbon::create(2025, $value)->daysInMonth;
            $this->refreshData();
        }
    }
    public function updatedPilihanLimbah($value)
    {
        if ($value) {
            $this->pilihanLimbah = $value;
            $this->refreshData();
        }
    }

    public function jamPengeluaranChange($tgl)
    {
        // Pastikan $tgl diterima dengan benar
        $waktu = $this->jamPengeluaran[$tgl] ?? null;

        if ($waktu) {
            // Format tanggal (Y-m-d) berdasarkan tahun, bulan, dan hari
            $tglSet = "$this->tahun-$this->selectedBulan-$tgl";

            // Simpan atau update data di tabel
            DB::table($this->tbl)->updateOrInsert(
                ['tgl' => $tglSet], // Kondisi untuk update berdasarkan tanggal
                [
                    'jenis_sampah' => $this->pilihanLimbah,
                    'jam_cek' => $waktu,
                    'admin' => auth()->user()->name,
                ]
            );
        }
        $this->alert('sukses', 'berhasil disimpan');
        $this->refreshData();
    }

    public function ceklis($tgl)
    {
        $waktu = $this->jamPengeluaran[$tgl] ?? null;
        $tglSet = "$this->tahun-$this->selectedBulan-$tgl";
        $existingData = DB::table($this->tbl)
            ->where('jenis_sampah', $this->pilihanLimbah)
            ->where('tgl', "$this->tahun-$this->selectedBulan-$tgl")
            ->where('jam_cek', $waktu)
            ->first();

        // if ($existingData) {
        //     DB::table($this->tbl)
        //         ->where('id', $existingData->id)
        //         ->delete();
        //     $type = 'error';
        //     $pesan = 'Data berhasil dihapus!';
        // } else {
        //     DB::table($this->tbl)->insert([
        //         'jenis_sampah' => $this->pilihanLimbah,
        //         'tgl' => "$this->tahun-$this->selectedBulan-$tgl",
        //         'jam_cek' => $waktu,
        //         'admin' => auth()->user()->name
        //     ]);
        //     $type = 'sukses';

        //     $pesan = 'Data berhasil disimpan';
        // }

        // $this->updatedSelectedBulan($this->selectedBulan);
        // $this->updatedPilihanLimbah($this->pilihanLimbah);
        // $this->alert($type, $pesan);
    }

    public function tbhParaf($kolom, $nama, String $tgl)
    {
        $cek = DB::table($this->tbl)
            ->where('jenis_sampah', $this->pilihanLimbah)
            ->where('tgl', $tgl);
        if ($cek->first()) {
            $cek->update([
                'admin' => auth()->user()->name,
                $kolom => $nama
            ]);
            $this->alert('sukses', 'Data Berhasil disimpan');
        } else {
            $this->alert('error', 'Data Belum Terceklis!');
        }
        $this->refreshData();
    }

    public function saveKeterangan($tanggal)
    {
        $data = DB::table($this->tbl)
            ->where('jenis_sampah', $this->pilihanLimbah)
            ->where('tgl', "$this->tahun-$this->selectedBulan-$tanggal")
            ->first();

        if ($data) {
            // Update keterangan jika data sudah ada
            DB::table($this->tbl)
                ->where('id', $data->id)
                ->update([
                    'ket' => $this->keterangan[$tanggal] ?? ''
                ]);
            // Bersihkan input setelah disimpan
            $this->keterangan[$tanggal] = $this->keterangan[$tanggal] ?? '';
            $this->alert('sukses', 'Keterangan berhasil disimpan');
        } else {
            $this->alert('error', 'Data Belum Terceklis!');
        }
        $this->refreshData();
    }

    public function render()
    {
        $adminSanitasi = DB::table('admin_sanitasi as a')
            ->join('users as b', 'b.id', '=', 'a.id')
            ->selectRaw('a.id, b.name, a.posisi')
            ->get()
            ->groupBy('posisi');

        $data = [
            'adminSanitasi' => $adminSanitasi
        ];
        return view('livewire.pembuangan-tps', $data);
    }
}
