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
        $selectedNama,
        $selectedBoxes = [],
        $cancelInvoice = '',
        $cancelKategori = '',
        $dataCancelBox = [],
        $gradedBoxes = [];

    public function mount()
    {
        $this->anak = [];
        $this->dataLewat = [];
        $this->selectedBoxes = [];
        $this->canUseGantiLewat = auth()->check() && in_array(strtolower(auth()->user()->name), ['aldi', 'nanda']);
        // fetch already graded boxes
        $this->gradedBoxes = DB::table('grading')->pluck('no_box_sortir')->toArray();
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

    public function loadCancelBoxes()
    {
        if (empty(trim($this->cancelInvoice))) {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Isi No Invoice.']);
            return;
        }

        if (empty($this->cancelKategori)) {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Pilih kategori.']);
            return;
        }

        if (DB::table('grading_partai')->where('no_invoice', $this->cancelInvoice)->exists()) {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Invoice sudah selesai grading, tidak bisa cancel.']);
            return;
        }



        $this->gradedBoxes = DB::table('grading')->where('pcs', '>', 0)->where('gr', '>', 0)->select('no_box_sortir')->pluck('no_box_sortir')->toArray();
        $this->selectedBoxes = [];
        $this->dataCancelBox = DB::table('formulir_sarang')
            ->where('no_invoice', $this->cancelInvoice)
            ->where('kategori', $this->cancelKategori)
            ->get();
    }

    public function cancelBoxes()
    {
        if (empty($this->selectedBoxes)) {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Pilih box yang akan dicancel.']);
            return;
        }

        // jika dia mau cancel box cabut maka cek dulu di tbl cetak_new, jika mau cancel box cetak maka cek dulu di sortir, jika mau cancel box sortir maka cek dulu di grade, jika mau cancel box grade maka cek dulu di grading_partai, jika mau cancel box grading maka cek dulu di grading
        switch ($this->cancelKategori) {
            case 'cabut':
                $cekCetak = DB::table('formulir_sarang')->where('kategori', 'cetak')->whereIn('no_box', $this->selectedBoxes)->exists();
                if ($cekCetak) {
                    $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Box yang dipilih sudah masuk ke cetak, tidak bisa dicancel.']);
                    return;
                } else {
                    DB::table('bk')->whereIn('no_box', $this->selectedBoxes)->update([
                        'formulir' => 'T',
                        'penerima' => 0
                    ]);
                }
                break;
            case 'cetak':
                $cekBoxSortir = DB::table('formulir_sarang')->where('kategori', 'sortir')->whereIn('no_box', $this->selectedBoxes)->exists();

                if ($cekBoxSortir) {
                    $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Box yang dipilih sudah masuk ke sortir, tidak bisa dicancel.']);
                    return;
                } else {
                    DB::table('cetak_new')->whereIn('no_box', $this->selectedBoxes)->delete();
                }
                break;
            case 'sortir':
                $cekBoxSortir = DB::table('formulir_sarang')->where('kategori', 'grade')->exists();
                if ($cekBoxSortir) {
                    $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Box yang dipilih sudah masuk ke grade, tidak bisa dicancel.']);
                    return;
                } else {
                    DB::table('bk')->where('kategori', 'sortir')->whereIn('no_box', $this->selectedBoxes)->delete();
                    DB::table('sortir')->whereIn('no_box', $this->selectedBoxes)->delete();
                }
                break;
            default:
                # code...
                break;
        }

        DB::table('formulir_sarang')
            ->where('kategori', $this->cancelKategori)
            ->whereIn('no_box', $this->selectedBoxes)
            ->delete();



        $this->dispatch('showAlert', ['type' => 'sukses', 'message' => 'Cancel per box berhasil disimpan.']);
        $this->selectedBoxes = [];
        $this->dataCancelBox = [];
        $this->cancelInvoice = '';
        $this->cancelKategori = '';
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
