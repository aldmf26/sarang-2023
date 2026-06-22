<div x-data="{
    openModal: () => {
        const modal = new bootstrap.Modal(document.getElementById('cariBox'));
        modal.show();
    },
    posisi: false,
    anak: false,
    grading: false,
    gantiTgl: false,
    gantiLewat: false,
    poCancelPerbox: false,
}">
    <a href="#" @click="openModal()" class="btn btn-sm btn-info">Cek Posisi No Box / Ganti Nama Anak / Edit Grading
        Kode / Ganti Tanggal</a>

    <x-theme.modal wire:ignore.self id="cariBox" btnSave="T" title="Cek Posisi No Box" size="modal-lg">

        <div class="d-flex gap-1">
            <button type="button" @click="posisi = !posisi; anak = false; grading = false; gantiTgl=false"
                class="btn btn-sm btn-primary">Cek Posisi No
                Box</button>
            <button type="button" @click="anak = !anak; posisi = false; grading = false; gantiTgl=false"
                class="btn btn-sm btn-primary">Ganti Nama
                Anak</button>
            <button type="button" @click="grading = !grading; posisi = false; anak = false; gantiTgl=false"
                class="btn btn-sm btn-primary">Grading edit kode</button>
            <button type="button" @click="gantiTgl = !gantiTgl; posisi = false; anak = false; grading=false"
                class="btn btn-sm btn-primary">Ganti Tanggal</button>

            {{-- disini button cancelpoperbox tapi yg belum di grading aja  --}}
            @if ($canUseGantiLewat)
                <button type="button"
                    @click="poCancelPerbox = !poCancelPerbox; posisi = false; anak = false; grading=false; gantiTgl=false"
                    class="btn btn-sm btn-primary">Cancel Per Box</button>


                <button type="button"
                    @click="gantiLewat = !gantiLewat; posisi = false; anak = false; grading=false; gantiTgl=false"
                    class="btn btn-sm btn-primary">Ganti Lewat</button>
            @else
                <button type="button" class="btn btn-sm btn-secondary" disabled>Cancel Per Box (Aldi/Nanda)</button>
                <button type="button" class="btn btn-sm btn-secondary" disabled>Ganti Lewat (Aldi/Nanda)</button>
            @endif
        </div>

        <div x-show='posisi'>
            <input type="text" wire:model.change="cariBox" class="form-control mt-2"
                placeholder="Cari Posisi No Box">
            <div wire:loading wire:target='cariBox' class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            @if ($dataBox)
                <table class="table table-striped table-dark table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>No Invoice</th>
                            <th>No Box</th>
                            <th>Pemberi</th>
                            <th>Penerima</th>
                            <th>Pcs Awal</th>
                            <th>Gr Awal</th>
                            <th>Tgl</th>
                            <th>Posisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dataBox as $d)
                            <tr>
                                <td>{{ $d->no_invoice }}</td>
                                <td>{{ $d->no_box }}</td>
                                <td>{{ $d->pemberi->name }}</td>
                                <td>{{ $d->penerima->name }}</td>
                                <td>{{ $d->pcs_awal }}</td>
                                <td>{{ $d->gr_awal }}</td>
                                <td>{{ $d->tanggal }}</td>
                                <td>{{ $d->kategori }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div x-show="anak" class="row">
            <div class="form-group col-6">
                <label for="">Divisi</label>
                <select class="form-select" wire:model.live="selectedDivisi" id="">
                    <option value="">Pilih Divisi</option>
                    <option value="cabut">Cabut</option>
                    <option value="cetak">Cetak</option>
                    <option value="sortir">Sortir</option>
                </select>
            </div>
            <div class="form-group col-6">
                <label for="">Pengawas</label>
                <select class="form-select" wire:model.live="selectedPengawas" id="">
                    <option value="">Pilih Pengawas</option>
                    @foreach ($pengawas as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-6">
                <label for="">No Box</label>
                <input type="text" wire:model="noBox" class="form-control">
            </div>
            @if ($anak)
                <div wire:transition class="form-group col-6">
                    <label for="">Nama</label>
                    <select class="form-select" wire:model.live="selectedNama" id="">
                        <option value="">Pilih Nama</option>
                        @foreach ($anak as $a)
                            <option value="{{ $a->id_anak }}">{{ $a->nama }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-12">
                <button wire:click='updateAnak' class="btn btn-sm btn-success btn-block" type="button">Simpan</button>
            </div>
        </div>

        <div x-show="grading">
            <div class="row">
                <div class="form-group col-3">
                    <label for="">No Invoice</label>
                    <input type="text" wire:model="noInvoice" class="form-control">
                </div>
                <div class="form-group col-3">
                    <label for="">Kode Sebelumnya</label>
                    <input type="text" wire:model="kodeSebelumnya" class="form-control">
                </div>
                <div class="form-group col-3">
                    <label for="">Gr</label>
                    <input type="text" wire:model.change="grGrading" class="form-control">
                </div>
                <div class="form-group col-3">
                    <label for="">Kode Setelahnya</label>
                    <input type="text" wire:model="kodeSesudahnya" class="form-control">
                </div>
                @if ($dataGrading)
                    <div class="col-12">
                        <table class="table table-striped table-dark table-bordered mt-3">
                            <thead>
                                <tr>
                                    <th>Nama Partai</th>
                                    <th>No Invoice</th>
                                    <th>Box Pengiriman</th>
                                    <th>Grade</th>
                                    <th>Tipe</th>
                                    <th>Pcs</th>
                                    <th>Gr</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $dataGrading->nm_partai }}</td>
                                    <td>{{ $dataGrading->no_invoice }}</td>
                                    <td>{{ $dataGrading->box_pengiriman }}</td>
                                    <td>{{ $dataGrading->grade }}</td>
                                    <td>{{ $dataGrading->tipe }}</td>
                                    <td>{{ $dataGrading->pcs }}</td>
                                    <td>{{ $dataGrading->gr }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
                <div class="col-12">
                    <button wire:click='updateGrading' class="btn btn-sm btn-success btn-block"
                        type="button">Simpan</button>
                </div>
            </div>
        </div>

        <div x-show="gantiTgl">
            <div class="row">
                <div class="form-group col-4">
                    <label for="">Divisi</label>
                    <select class="form-select" wire:model.live="selectedDivisi" id="">
                        <option value="">Pilih Divisi</option>
                        <option value="cabut">Cabut</option>
                        <option value="cetak">Cetak</option>
                        <option value="sortir">Sortir</option>
                    </select>
                </div>
                <div class="form-group col-4">
                    <label for="">Pengawas</label>
                    <select class="form-select" wire:model.live="selectedPengawas" id="">
                        <option value="">Pilih Pengawas</option>
                        @foreach ($pengawas as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-4">
                    <label for="">No Box</label>
                    <input type="text" wire:model="noBoxArr" class="form-control">
                </div>
                {{ $noBoxArr }}
                <div class="col-12">
                    <button wire:click='updateGantiTgl' class="btn btn-sm btn-success btn-block"
                        type="button">Simpan</button>
                </div>
            </div>
        </div>

        <div x-show="poCancelPerbox" class="mt-3">
            <div class="row">
                <div class="form-group col-4">
                    <label for="">Kategori</label>

                    <select wire:model="cancelKategori" class="form-select">
                        <option value="">Pilih Kategori</option>
                        <option value="cabut">Cabut</option>
                        <option value="cetak">Cetak</option>
                        <option value="sortir">Sortir</option>
                        <option value="grade">Grade</option>
                        <option value="grading">Grading</option>
                    </select>
                </div>
                <div class="form-group col-4">
                    <label for="">No Invoice</label>
                    <input type="text" wire:model="cancelInvoice" placeholder="No Invoice"
                        class="form-control" />
                </div>
                <div class="col-4">
                    <label for="">Aksi</label> <br>
                    <button type="button" wire:click="loadCancelBoxes" class="btn btn-sm btn-success">Cek
                        Box</button>
                </div>
                @if ($dataCancelBox && count($dataCancelBox))
                    <div class="col-12 mt-2" x-data="{
                        cari: '',
                        bulkNoBoxInput: '',
                        boxData: {
                            @foreach ($dataCancelBox as $box)
                                    '{{ $box->no_box }}': { pcs: {{ $box->pcs_awal }}, gr: {{ $box->gr_awal }} }, @endforeach
                        },
                        ttlPcs: 0,
                        ttlGr: 0,
                        hitungTotal() {
                            this.ttlPcs = 0;
                            this.ttlGr = 0;
                            (this.$wire.selectedBoxes ?? []).forEach(box => {
                                if (this.boxData[box]) {
                                    this.ttlPcs += this.boxData[box].pcs;
                                    this.ttlGr += this.boxData[box].gr;
                                }
                            });
                        },
                        applyBulk() {
                            const values = this.bulkNoBoxInput.split(',').map(v => v.trim()).filter(v => v.length);
                            this.$wire.selectedBoxes = Array.from(new Set(values));
                            this.hitungTotal();
                        },
                        clearBulk() {
                            this.bulkNoBoxInput = '';
                            this.$wire.selectedBoxes = [];
                            this.hitungTotal();
                        }
                    }" x-init="$nextTick(() => hitungTotal())">
                        <div class="row mb-2">
                            <div class="col-8">
                                <input type="text" x-model="bulkNoBoxInput" class="form-control form-control-sm"
                                    placeholder="Masukkan no box, pisahkan dengan koma">
                            </div>
                            <div class="col-4 d-flex gap-2">
                                <button type="button" @click="applyBulk()" class="btn btn-sm btn-primary">Apply
                                    Bulk</button>
                                <button type="button" @click="clearBulk()"
                                    class="btn btn-sm btn-secondary">Clear</button>
                            </div>
                        </div>
                        <input type="text" x-model="cari" placeholder="Cari No Box" class="form-control mb-2" />
                        <table class="table table-sm table-striped table-dark table-bordered">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>No Box</th>
                                    <th>Pcs Awal</th>
                                    <th>Gr Awal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dataCancelBox as $box)
                                    <tr x-show="!cari || '{{ $box->no_box }}'.includes(cari)"
                                        @click="
                                            $wire.selectedBoxes.includes('{{ $box->no_box }}')
                                                ? $wire.selectedBoxes = $wire.selectedBoxes.filter(b => b !== '{{ $box->no_box }}')
                                                : $wire.selectedBoxes.push('{{ $box->no_box }}');
                                            hitungTotal();
                                        "
                                        :class="$wire.selectedBoxes.includes('{{ $box->no_box }}') && 'table-primary'"
                                        style="cursor: pointer">
                                        <td><input type="checkbox" wire:model="selectedBoxes"
                                                value="{{ $box->no_box }}" @change="hitungTotal()"
                                                @click.stop /></td>
                                        <td>{{ $box->no_box }}</td>
                                        <td>{{ $box->pcs_awal }}</td>
                                        <td>{{ $box->gr_awal }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" wire:click="cancelBoxes" class="btn btn-sm btn-danger">
                            Cancel Selected
                            <span class="" x-text="$wire.selectedBoxes.length"></span> Box |
                            <span x-text="ttlPcs.toLocaleString('id-ID')"></span> Pcs |
                            <span x-text="ttlGr.toLocaleString('id-ID')"></span> Gr
                        </button>
                    </div>
                @elseif ($cancelInvoice && $cancelKategori)
                    <div class="col-12 mt-2">
                        <div class="alert alert-warning">Tidak ada box yang belum grading untuk invoice ini.</div>
                    </div>
                @endif
            </div>
        </div>

        <div x-show="gantiLewat">
            <div class="row">
                <div class="form-group col-4">
                    <label for="">No Box</label>
                    <input type="text" wire:model.change="noBoxLewat" class="form-control"
                        placeholder="Cari No Box untuk Ganti Lewat">
                </div>
                <div class="form-group col-4">
                    <label for="">Pcs</label>
                    <input type="text" wire:model="pcsLewat" class="form-control" placeholder="Pcs baru">
                </div>
                <div class="form-group col-4">
                    <label for="">GR</label>
                    <input type="text" wire:model="grLewat" class="form-control" placeholder="GR baru">
                </div>

                @if ($dataLewat && count($dataLewat) > 0)
                    <div class="col-12 mt-3">
                        <table class="table table-striped table-dark table-bordered">
                            <thead>
                                <tr>
                                    <th>Tabel</th>
                                    <th>No Box</th>
                                    <th>Kategori</th>
                                    <th>No Invoice</th>
                                    <th>Pcs Awal</th>
                                    <th>Gr Awal</th>
                                    <th>Pcs Akhir</th>
                                    <th>Gr Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dataLewat as $d)
                                    <tr>
                                        <td>{{ $d['source'] }}</td>
                                        <td>{{ $d['no_box'] }}</td>
                                        <td>{{ $d['kategori'] ?? '-' }}</td>
                                        <td>{{ $d['no_invoice'] ?? '-' }}</td>
                                        <td>{{ $d['pcs_awal'] ?? '-' }}</td>
                                        <td>{{ $d['gr_awal'] ?? '-' }}</td>
                                        <td>{{ $d['pcs_akhir'] ?? '-' }}</td>
                                        <td>{{ $d['gr_akhir'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif (!empty($noBoxLewat))
                    <div class="col-12 mt-3">
                        <div class="alert alert-warning">No Box tidak ditemukan di tabel bk, cabut, cetak_new, sortir,
                            atau formulir_sarang.</div>
                    </div>
                @endif

                <div class="col-12">
                    <button wire:click='updateGantiLewat' class="btn btn-sm btn-success btn-block"
                        type="button">Simpan</button>
                </div>
            </div>
        </div>

    </x-theme.modal>
</div>
