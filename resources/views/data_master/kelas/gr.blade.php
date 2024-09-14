<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="">{{ $title }}</h6>
        @include('data_master.kelas.nav')

    </x-slot>

    <x-slot name="cardBody">
        <form method="post" action="{{ route('kelas.create_gr') }}" x-data="{ tambah: false }">
            <button type="button" class="btn btn-sm btn-info mb-3" @click="tambah = !tambah"><i class="fas fa-plus"></i>
                data</button>
            @csrf
            <div x-show="tambah">
                <x-theme.multiple-input label="Tambah Baris">
                    <div class="col-1">
                        <label for="kelas" class="form-label">Kelas</label>
                        <input type="text" name="kelas[]" class="form-control" id="kelas">
                    </div>
                    <div class="col-1">
                        <label for="paket" class="form-label">Paket</label>
                        <input type="text" name="paket[]" class="form-control" id="paket">
                    </div>
                    <div class="col-1">
                        <label for="lokasi" class="form-label">Lokasi</label>
                        <input type="text" name="lokasi[]" class="form-control" id="lokasi">
                    </div>
                    <div class="col-1">
                        <label for="lokasi" class="form-label">Gr</label>
                        <input type="text" name="gr[]" class="form-control" id="lokasi">
                    </div>
                    <div class="col-2">
                        <label for="lokasi" class="form-label">Rp</label>
                        <input type="text" name="rp[]" class="form-control" id="lokasi">
                    </div>
                    <div class="col-1">
                        <label for="lokasi" class="form-label">Denda sst</label>
                        <input type="text" name="denda_susut[]" class="form-control" id="lokasi">
                    </div>
                    <div class="col-1">
                        <label for="lokasi" class="form-label">Batas sst</label>
                        <input type="text" name="batas_susut[]" class="form-control" id="lokasi">
                    </div>
                    <div class="col-1">
                        <label for="lokasi" class="form-label">Rp Bonus</label>
                        <input type="text" name="rp_bonus[]" class="form-control" id="lokasi">
                    </div>
                    <div class="col-1">
                        <label for="lokasi" class="form-label">Bonus sst</label>
                        <input type="text" name="bonus_susut[]" class="form-control" id="lokasi">
                    </div>
                    <div class="col-1">
                        <label for="lokasi" class="form-label">Batas eot</label>
                        <input type="text" name="batas_eot[]" class="form-control" id="lokasi">
                    </div>
                    <div class="col-1">
                        <label for="lokasi" class="form-label">Eot</label>
                        <input type="text" name="eot[]" class="form-control" id="lokasi">
                    </div>
                    <div class="col-1">
                        <label for="lokasi" class="form-label">Denda Hcr</label>
                        <input type="text" name="denda_hcr[]" class="form-control" id="lokasi">
                    </div>
                </x-theme.multiple-input>
                <button type="submit" class="btn btn-primary btn-sm mt-2">Simpan</button>
            </div>
        </form>
        <hr>
        <section class="row mt-3">
            <table class="table table-bordered" id="table">
                <thead>
                    <tr>
                        <th class="dhead" width="15">No</th>
                        <th class="dhead" width="140">Paket</th>
                        <th class="dhead" width="140">lokasi</th>
                        <th class="text-center dhead" width="70">Gr</th>
                        <th class="text-end dhead" width="100">Rp</th>
                        <th class="text-end dhead">Denda Susut %</th>
                        <th class="text-end dhead">Batas Susut</th>
                        <th class="text-end dhead">Bonus Susut</th>
                        <th class="text-end dhead" width="100">Rp Bonus</th>
                        <th class="text-end dhead">Batas Eot</th>
                        <th class="text-end dhead">Eot</th>
                        <th class="text-end dhead">Denda Hcr</th>
                        <th class="dhead" width="90">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($datas as $i => $d)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $d->kelas . ' ' . $d->tipe }}</td>
                            <td>{{ $d->lokasi }}</td>
                            <td align="right">{{ $d->gr }}</td>
                            <td align="right">{{ number_format($d->rupiah, 0) }}</td>
                            <td align="right">{{ $d->denda_susut_persen }} %</td>
                            <td align="right">{{ $d->batas_susut }}</td>
                            <td align="right">{{ $d->bonus_susut }}</td>
                            <td align="right">{{ number_format($d->rp_bonus, 0) }}</td>
                            <td align="right">{{ $d->batas_eot }}</td>
                            <td align="right">{{ $d->eot }}</td>
                            <td align="right">{{ number_format($d->denda_hcr, 0) }}</td>
                            <td align="center">
                                <a href="#" class="btn btn-info btn-sm"
                                    data-bs-target="#edit{{ $d->id_kelas }}" data-bs-toggle="modal"><i
                                        class="fas fa-pen"></i></a>
                                <a onclick="return confirm('Yakin di hapus ?')"
                                    href="{{ route('kelas.delete_gr', $d->id_kelas) }}"
                                    class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
        @foreach ($datas as $d)
            <form action="{{ route('kelas.update_gr', $d->id_kelas ) }}" method="post">
                @csrf
                <x-theme.modal title="Edit Kelas" size="modal-lg-max" idModal="edit{{ $d->id_kelas }}">
                    <div class="row">
                        <div class="col-1">
                            <label for="kelas" class="form-label">Kelas</label>
                            <input type="text" name="kelas" value="{{ $d->kelas }}" class="form-control"
                                id="kelas">
                        </div>
                        <div class="col-2">
                            <label for="paket" class="form-label">Paket</label>
                            <input type="text" name="paket" value="{{ $d->tipe }}" class="form-control"
                                id="paket">
                        </div>
                        <div class="col-1">
                            <label for="lokasi" class="form-label">Lokasi</label>
                            <input type="text" name="lokasi" value="{{ $d->lokasi }}" class="form-control"
                                id="lokasi">
                        </div>
                        <div class="col-1">
                            <label for="lokasi" class="form-label">Gr</label>
                            <input type="text" name="gr" value="{{ $d->gr }}" class="form-control"
                                id="lokasi">
                        </div>
                        <div class="col-2">
                            <label for="lokasi" class="form-label">Rp</label>
                            <input type="text" name="rp" value="{{ $d->rupiah }}" class="form-control"
                                id="lokasi">
                        </div>
                        <div class="col-1">
                            <label for="lokasi" class="form-label">Denda sst</label>
                            <input type="text" name="denda_susut" value="{{ $d->denda_susut_persen }}"
                                class="form-control" id="lokasi">
                        </div>
                        <div class="col-1">
                            <label for="lokasi" class="form-label">Batas sst</label>
                            <input type="text" name="batas_susut" value="{{ $d->batas_susut }}"
                                class="form-control" id="lokasi">
                        </div>
                        <div class="col-1">
                            <label for="lokasi" class="form-label">Rp Bonus</label>
                            <input type="text" name="rp_bonus" value="{{ $d->rp_bonus }}" class="form-control"
                                id="lokasi">
                        </div>
                        <div class="col-1">
                            <label for="lokasi" class="form-label">Bonus sst</label>
                            <input type="text" name="bonus_susut" value="{{ $d->bonus_susut }}"
                                class="form-control" id="lokasi">
                        </div>
                        <div class="col-1">
                            <label for="lokasi" class="form-label">Batas eot</label>
                            <input type="text" name="batas_eot" value="{{ $d->batas_eot }}" class="form-control"
                                id="lokasi">
                        </div>
                        <div class="col-1">
                            <label for="lokasi" class="form-label">Eot</label>
                            <input type="text" name="eot" value="{{ $d->eot }}" class="form-control"
                                id="lokasi">
                        </div>
                        <div class="col-1">
                            <label for="lokasi" class="form-label">Denda Hcr</label>
                            <input type="text" name="denda_hcr" value="{{ $d->denda_hcr }}" class="form-control"
                                id="lokasi">
                        </div>
                    </div>

                </x-theme.modal>
            </form>
        @endforeach
        @section('scripts')
            <script>
                $(document).ready(function() {
                    $(document).on('click', '.edit', function(e) {
                        e.preventDefault();

                        const id_kelas = $(this).attr('id_kelas')

                    })
                })
            </script>
        @endsection
    </x-slot>
</x-theme.app>
