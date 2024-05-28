<x-theme.app title="{{ $title }}" table="T">
    <x-slot name="slot">
        <h6>{{ $title }}</h6>
        <div class="row">
            <div class="col-lg-4">
                <input type="text" id="tbl1input" class="form-control form-control-sm mb-2" placeholder="cari">
                <table id="tbl1" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="dhead text-center" colspan="3">Cetak stock</th>
                        </tr>
                        <tr>
                            <th class="dhead text-center">No Box</th>
                            <th class="dhead text-end">Pcs</th>
                            <th class="dhead text-end">Gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cabut_selesai as $d)
                            <tr>
                                <td align="center">{{ $d->no_box }}</td>
                                <td align="right">{{ $d->pcs_awal }}</td>
                                <td align="right">{{ $d->gr_awal }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-lg-4">
                <input type="text" id="tbl2input" class="form-control form-control-sm mb-2" placeholder="cari">

                <table id="tbl2" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="dhead text-center" colspan="3">Cetak sedang proses</th>
                        </tr>
                        <tr>

                            <th class="dhead text-center">No Box</th>
                            <th class="dhead text-end">Pcs</th>
                            <th class="dhead text-end">Gr</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="col-lg-4">
                <input type="text" id="tbl3input" class="form-control form-control-sm mb-2" placeholder="cari">

                <table id="tbl3" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="dhead text-center" colspan="4">
                                <span>Cetak selesai siap sortir</span>

                            </th>
                        </tr>
                        <tr>

                            <th class="dhead text-center">No Box</th>
                            <th class="dhead text-end">Pcs</th>
                            <th class="dhead text-end">Gr</th>
                            <th class="dhead text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @foreach ($cabutSelesai as $d)
                            <tr>
                                <td align="center">{{ $d->no_box }}</td>
                                <td align="right">{{ $d->pcs }}</td>
                                <td align="right">{{ $d->gr }}</td>
                                <td align="center">
                                    <input type="checkbox"
                                        @change="tambah({{ $d->no_box }}, {{ $d->pcs }}, {{ $d->gr }})"
                                        value="{{ $d->no_box }}" x-model="cek">
                                </td>
                            </tr>
                        @endforeach --}}
                    </tbody>
                </table>


                {{-- modal ambil box ke cetak --}}
                {{-- <form action="{{ route('gudangsarang.save_formulir') }}" method="post">
                    @csrf
                    <x-theme.modal idModal="tambah" title="tambah box" btnSave="Y">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Tgl</label>
                                    <input value="{{ date('Y-m-d') }}" type="date" name="tgl"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label for="">Pgws Penerima</label>
                                <select required name="id_pengawas" class="form-control select2" id="">
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
                                        <td x-text="item.pcs"</td>
                                        <td x-text="item.gr"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </x-theme.modal>
                </form> --}}

            </div>



        </div>
        @section('scripts')
            {{-- <script>
                ["tbl1", "tbl2", "tbl3"].forEach((tbl, i) => pencarian(`tbl${i+1}input`, tbl));
            </script> --}}
        @endsection
    </x-slot>

</x-theme.app>
