<x-theme.app title="{{ $title }} " table="Y" sizeCard="8">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6>
                {{ $title }}
            </h6>
            <x-theme.button href="#" icon="fa-plus" modal="Y" idModal="tambah" teks="tambah" />
        </div>

    </x-slot>

    <x-slot name="cardBody">
        <div x-data="{ 
            cek: [],
            selectedItem: [],
            tambah(no_box, pcs, gr) {
                const selectedItem = this.selectedItem
                const cetak = this.cetak

                const index = selectedItem.findIndex(item => item.no_box === no_box);
                if (index === -1) {
                    selectedItem.push({
                        no_box: no_box,
                        pcs_akhir: parseFloat(pcs),
                        gr_akhir: parseFloat(gr),
                    });
                } else {
                    this.selectedItem.splice(index, 1);
                }
              
            }
        }">
            <div class="row">
                <div class="col">
                    <table id="tbl_summary" class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th class="dhead">#</th>
                                <th class="dhead">No Box</th>
                                <th class="dhead text-end">Pcs Akhir</th>
                                <th class="dhead text-end">Gr Akhir</th>
                                <th class="dhead text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cetak as $i => $d)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $d->no_box }}</td>
                                    <td align="right">{{ $d->pcs_akhir }}</td>
                                    <td align="right">{{ $d->gr_akhir }}</td>
                                    <td align="center">
                                        {{-- <button @click="tambah({{$d->no_box}}, {{$d->pcs_akhir}}, {{$d->gr_akhir}})"></button> --}}
                                        <input type="checkbox" @change="tambah({{$d->no_box}}, {{$d->pcs_akhir}}, {{$d->gr_akhir}})" value="{{$d->no_box}}" x-model="cek">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <form action="{{ route('cetaknew.save_formulir') }}" method="post">
                @csrf
                <x-theme.modal idModal="tambah" title="tambah box" btnSave="Y">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Tgl</label>
                                <input value="{{ date('Y-m-d') }}" type="date" name="tgl" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label for="">Pgws Penerima</label>
                            <select required name="id_penerima" class="form-control select2" id="">
                                <option value="">- Pilih pgws -</option>
                                @foreach ($users as $d)
                                    <option value="{{ $d->id }}">{{ strtoupper($d->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="dhead">No Box</th>
                                <th class="dhead">Pcs</th>
                                <th class="dhead">Gr</th>
                            </tr>
                        </thead>
                        <tbody>
                            <input class="d-none" name="no_box[]" type="text" :value="cek">
                            <template x-for="item in selectedItem">
                                <tr>
                                    
                                    <td x-text="item.no_box"></td>
                                    <td x-text="item.pcs_akhir"></td>
                                    <td x-text="item.gr_akhir"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </x-theme.modal>
            </form>
        </div>
        @section('scripts')
            <script>
                loadTable('tbl_summary')
            </script>
        @endsection
    </x-slot>
</x-theme.app>
