<?php

namespace App\Livewire;

use App\Models\Anak;
use App\Models\Formulir;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PosisiBox extends Component
{
    public $cariBox,
        $grGrading,
        $noInvoice,
        $kodeSebelumnya,
        $kodeSesudahnya,
        $dataGrading,
        $dataBox,
        $dataLewat,
        $canUseGantiLewat,
        $selectedPengawas,
        $selectedDivisi,
        $anak,
        $noBox,
        $noBoxArr,
        $noBoxLewat,
        $pcsLewat,
        $grLewat,
        $selectedNama;

    public function mount()
    {
        $this->anak = [];
        $this->dataLewat = [];
        $this->canUseGantiLewat = auth()->check() && in_array(strtolower(auth()->user()->name), ['aldi', 'nanda']);
    }
    public function updatedCariBox($value)
    {
        if (empty(trim($value))) {
            return;
        }

        $this->cariBox = $value;
        $this->dataBox = Formulir::with(['penerima', 'pemberi'])->where('no_box', 'like', '%' . $value . '%')
            ->get();
        // Optionally, you can add any additional logic here when the search term changes
    }

    public function updatedSelectedPengawas($value)
    {
        $this->anak = DB::table('tb_anak')->where('id_pengawas', $value)->get();
    }

    public function updateAnak()
    {
        $table = match ($this->selectedDivisi) {
            'cabut' => 'cabut',
            'cetak' => 'cetak_new',
            'sortir' => 'sortir',
        };

        $cekNoBox = DB::table($table)->where('no_box', $this->noBox)->first();

        if (empty($cekNoBox)) {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'No Box tidak ditemukan di tabel ' . $table]);
            return;
        }

        DB::table($table)->where('no_box', $this->noBox)->update([
            'id_anak' => $this->selectedNama
        ]);
        $this->dispatch('showAlert', ['type' => 'sukses', 'message' => 'Data anak berhasil diupdate. Refresh halamannya']);
    }

    public function updatedGrGrading($value)
    {
        $this->dataGrading = DB::table('grading_partai')->where([
            ['no_invoice', $this->noInvoice],
            ['box_pengiriman', $this->kodeSebelumnya],
            ['gr', $value]
        ])->first();
    }

    public function updateGrading()
    {
        DB::table('grading_partai')->where([
            ['no_invoice', $this->noInvoice],
            ['box_pengiriman', $this->kodeSebelumnya],
            ['gr', $this->grGrading]
        ])->update([
            'box_pengiriman' => $this->kodeSesudahnya
        ]);
        $this->dispatch('showAlert', ['type' => 'sukses', 'message' => 'Data anak berhasil diupdate. Refresh halamannya']);
    }

    public function updateGantiTgl()
    {
        $noBoxArr = explode(',', $this->noBoxArr);
        $table = match ($this->selectedDivisi) {
            'cabut' => 'cabut',
            'cetak' => 'cetak_new',
            'sortir' => 'sortir',
        };

        $this->dispatch('showAlert', ['type' => 'sukses', 'message' => 'Proses update tanggal selesai. Refresh halamannya']);
    }

    public function updatedNoBoxLewat($value)
    {
        if (empty(trim($value))) {
            $this->dataLewat = [];
            return;
        }

        $search = '%' . trim($value) . '%';
        $results = [];

        $formulirRows = DB::table('formulir_sarang')->where('no_box', 'like', $search)->get();
        foreach ($formulirRows as $row) {
            $results[] = [
                'source' => 'formulir_sarang',
                'no_invoice' => $row->no_invoice ?? null,
                'no_box' => $row->no_box,
                'pcs_awal' => $row->pcs_awal,
                'gr_awal' => $row->gr_awal,
                'pcs_akhir' => $row->pcs_akhir ?? null,
                'gr_akhir' => $row->gr_akhir ?? null,
                'kategori' => $row->kategori ?? null,
                'pemberi' => $row->pemberi ?? null,
                'penerima' => $row->penerima ?? null,
            ];
        }

        $bkRows = DB::table('bk')->where('no_box', 'like', $search)->get();
        foreach ($bkRows as $row) {
            $results[] = [
                'source' => 'bk',
                'no_invoice' => null,
                'no_box' => $row->no_box,
                'pcs_awal' => $row->pcs_awal,
                'gr_awal' => $row->gr_awal,
                'pcs_akhir' => null,
                'gr_akhir' => null,
                'kategori' => $row->kategori ?? null,
                'pemberi' => null,
                'penerima' => null,
            ];
        }

        $cabutRows = DB::table('cabut')->where('no_box', 'like', $search)->get();
        foreach ($cabutRows as $row) {
            $results[] = [
                'source' => 'cabut',
                'no_invoice' => null,
                'no_box' => $row->no_box,
                'pcs_awal' => $row->pcs_awal,
                'gr_awal' => $row->gr_awal,
                'pcs_akhir' => $row->pcs_akhir,
                'gr_akhir' => $row->gr_akhir,
                'kategori' => $row->kategori ?? null,
                'pemberi' => null,
                'penerima' => null,
            ];
        }

        $cetakRows = DB::table('cetak_new')->where('no_box', 'like', $search)->get();
        foreach ($cetakRows as $row) {
            $results[] = [
                'source' => 'cetak_new',
                'no_invoice' => null,
                'no_box' => $row->no_box,
                'pcs_awal' => $row->pcs_awal_ctk,
                'gr_awal' => $row->gr_awal_ctk,
                'pcs_akhir' => $row->pcs_akhir,
                'gr_akhir' => $row->gr_akhir,
                'kategori' => $row->kategori ?? null,
                'pemberi' => null,
                'penerima' => null,
            ];
        }

        $sortirRows = DB::table('sortir')->where('no_box', 'like', $search)->get();
        foreach ($sortirRows as $row) {
            $results[] = [
                'source' => 'sortir',
                'no_invoice' => null,
                'no_box' => $row->no_box,
                'pcs_awal' => $row->pcs_awal,
                'gr_awal' => $row->gr_awal,
                'pcs_akhir' => $row->pcs_akhir,
                'gr_akhir' => $row->gr_akhir,
                'kategori' => $row->kategori ?? null,
                'pemberi' => null,
                'penerima' => null,
            ];
        }

        $this->dataLewat = $results;
    }

    public function updateGantiLewat()
    {
        if (! $this->canUseGantiLewat) {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Akses Ganti Lewat hanya untuk user Aldi dan Nanda.']);
            return;
        }

        $noBox = trim($this->noBoxLewat);
        $pcs = $this->pcsLewat;
        $gr = $this->grLewat;

        if (empty($noBox) || $pcs === null || $gr === null || !is_numeric($pcs) || !is_numeric($gr)) {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Isi No Box, Pcs, dan GR dengan benar.']);
            return;
        }

        $updated = 0;

        if (DB::table('bk')->where('no_box', $noBox)->exists()) {
            DB::table('bk')->where('no_box', $noBox)->update([
                'pcs_awal' => $pcs,
                'gr_awal' => $gr,
            ]);
            $updated++;
        }

        if (DB::table('cabut')->where('no_box', $noBox)->exists()) {
            DB::table('cabut')->where('no_box', $noBox)->update([
                'pcs_awal' => $pcs,
                'gr_awal' => $gr,
                'pcs_akhir' => $pcs,
                'gr_akhir' => $gr,
            ]);
            $updated++;
        }

        if (DB::table('cetak_new')->where('no_box', $noBox)->exists()) {
            DB::table('cetak_new')->where('no_box', $noBox)->update([
                'pcs_awal_ctk' => $pcs,
                'gr_awal_ctk' => $gr,
                'pcs_akhir' => $pcs,
                'gr_akhir' => $gr,
            ]);
            $updated++;
        }

        if (DB::table('sortir')->where('no_box', $noBox)->exists()) {
            DB::table('sortir')->where('no_box', $noBox)->update([
                'pcs_awal' => $pcs,
                'gr_awal' => $gr,
                'pcs_akhir' => $pcs,
                'gr_akhir' => $gr,
            ]);
            $updated++;
        }

        if (DB::table('formulir_sarang')->where('no_box', $noBox)->exists()) {
            DB::table('formulir_sarang')->where('no_box', $noBox)->update([
                'pcs_awal' => $pcs,
                'gr_awal' => $gr,
            ]);
            $updated++;
        }

        if ($updated === 0) {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'No Box tidak ditemukan di tabel target Ganti Lewat.']);
            return;
        }

        $this->updatedNoBoxLewat($noBox);
        $this->dispatch('showAlert', ['type' => 'sukses', 'message' => 'Ganti Lewat berhasil disimpan. Refresh halaman jika perlu.']);
    }

    public function render()
    {
        $data = [
            'pengawas' => User::wherein('posisi_id', [13, 14])->get(),
        ];
        return view('livewire.posisi-box', $data);
    }
}
