<?php

namespace App\Livewire;

use App\Models\Anak;
use App\Models\Formulir;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PosisiBox extends Component
{
    public $cariBox, $dataBox, $selectedPengawas, $selectedDivisi, $anak, $noBox, $selectedNama;

    public function mount()
    {
        $this->anak = [];
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

    public function render()
    {
        $data = [
            'pengawas' => User::where('posisi_id', '13')->get(),
        ];
        return view('livewire.posisi-box', $data);
    }
}
