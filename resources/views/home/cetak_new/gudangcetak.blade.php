<x-theme.app title="{{ $title }}" table="T">
    <x-slot name="slot">
        <div class="d-flex justify-content-between">
            <h6>{{ $title }}</h6>
            <div>

                <a href="{{ route('cetaknew.export_gudang') }}" class="btn btn-sm btn-primary "><i
                        class="fas fa-file-excel"></i> Export
                    All</a>
                <x-theme.button href="#" icon="fa-plus" variant="info" modal="Y" idModal="tambah"
                    teks="serah" />
                <x-theme.button href="{{ route('gudangsarang.invoice_sortir', ['kategori' => 'sortir']) }}"
                    icon="fa-clipboard-list" teks="Po Sortir" />
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-lg-4">
                <input type="text" id="tbl1input" class="form-control form-control-sm mb-2" placeholder="cari">
                <table id="tbl1" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="dhead text-center" colspan="5">Cetak stock</th>
                        </tr>
                        <tr>
                            <th class="dhead text-center">Pemilik</th>
                            <th class="dhead text-center">No Box</th>
                            <th class="dhead text-end">Pcs</th>
                            <th class="dhead text-end">Gr</th>
                            <th class="dhead text-end">Ttl Rp</th>
                        </tr>
                        @php

                            if (!function_exists('ttl')) {
                                function ttl($tl)
                                {
                                    return [
                                        'pcs_awal' => array_sum(array_column($tl, 'pcs_awal')),
                                        'gr_awal' => array_sum(array_column($tl, 'gr_awal')),
                                        'ttl_rp' => array_sum(array_column($tl, 'ttl_rp')),
                                    ];
                                }
                            }

                        @endphp
                        <tr>
                            <th class="dheadstock text-center">Total</th>
                            <th class="dheadstock text-center"></th>
                            <th class="dheadstock text-end">{{ number_format(ttl($cabut_selesai)['pcs_awal'], 0) }}</th>
                            <th class="dheadstock text-end">{{ number_format(ttl($cabut_selesai)['gr_awal'], 0) }}</th>
                            <th class="dheadstock text-end">{{ number_format(ttl($cabut_selesai)['ttl_rp'], 0) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cabut_selesai as $d)
                            <tr>
                                <td align="center">{{ $d->name }}</td>
                                <td align="center">{{ $d->no_box }}</td>
                                <td align="right">{{ $d->pcs_awal }}</td>
                                <td align="right">{{ $d->gr_awal }}</td>
                                <td align="right">{{ number_format($d->ttl_rp, 0) }}</td>
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
                            <th class="dhead text-center" colspan="5">Cetak sedang proses</th>
                        </tr>
                        <tr>

                            <th class="dhead text-center">Pemilik</th>
                            <th class="dhead text-center">No Box</th>
                            <th class="dhead text-end">Pcs</th>
                            <th class="dhead text-end">Gr</th>
                            <th class="dhead text-end">Ttl Rp</th>
                        </tr>
                        <tr>
                            <th class="dheadstock text-center">Total</th>
                            <th class="dheadstock text-center"></th>
                            <th class="dheadstock text-end">{{ number_format(ttl($cetak_proses)['pcs_awal'], 0) }}</th>
                            <th class="dheadstock text-end">{{ number_format(ttl($cetak_proses)['gr_awal'], 0) }}</th>
                            <th class="dheadstock text-end">{{ number_format(ttl($cetak_proses)['ttl_rp'], 0) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cetak_proses as $d)
                            <tr>
                                <td align="center">{{ $d->name }}</td>
                                <td align="center">{{ $d->no_box }}</td>
                                <td align="right">{{ $d->pcs_awal }}</td>
                                <td align="right">{{ $d->gr_awal }}</td>
                                <td align="right">{{ number_format($d->ttl_rp, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-lg-4" x-data="{
                cek: [],
                selectedItem: [],
                {{-- tambah(no_box, name, pcs_awal, gr_awal) {
                    this.selectedItem.push({ no_box, name, pcs_awal, gr_awal });
                }, --}}
                tambah(no_box, name, pcs_awal, gr_awal, ttl_rp) {
                    const selectedItem = this.selectedItem
                    const cetak = this.cetak
            
                    const index = selectedItem.findIndex(item => item.no_box === no_box);
                    if (index === -1) {
                        selectedItem.push({
                            no_box: no_box,
                            name,
                            pcs_awal: parseFloat(pcs_awal),
                            gr_awal: parseFloat(gr_awal),
                            ttl_rp: parseFloat(ttl_rp),
                        });
                    } else {
                        selectedItem.splice(index, 1);
                    }
            
                },
                selectedOption: null
            }">
                <div class="row">
                    <div class="col">
                        <input type="text" id="tbl3input" class="form-control form-control-sm mb-2"
                            placeholder="cari">
                    </div>
                </div>
                <table id="tbl3" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="dhead text-center" colspan="6">
                                <span>Cetak selesai siap sortir</span>
                            </th>
                        </tr>
                        <tr>
                            <th class="dhead text-center">Pemilik</th>
                            <th class="dhead text-center">No Box</th>
                            <th class="dhead text-end">Pcs</th>
                            <th class="dhead text-end">Gr</th>
                            <th class="dhead text-end">Total Rp</th>
                            <th class="dhead text-center">Aksi</th>
                        </tr>
                        <tr>
                            <th class="dheadstock text-center">Total</th>
                            <th class="dheadstock text-center"></th>
                            <th class="dheadstock text-end">{{ number_format(ttl($cetak_selesai)['pcs_awal'], 0) }}
                            </th>
                            <th class="dheadstock text-end">{{ number_format(ttl($cetak_selesai)['gr_awal'], 0) }}</th>
                            <th class="dheadstock text-end">{{ number_format(ttl($cetak_selesai)['ttl_rp'], 0) }}</th>
                            <th class="dheadstock text-end"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cetak_selesai as $d)
                            <tr>
                                <td align="center">{{ $d->name }}</td>
                                <td align="center">{{ $d->no_box }}</td>
                                <td align="right">{{ $d->pcs_awal }}</td>
                                <td align="right">{{ $d->gr_awal }}</td>
                                <td align="right">{{ number_format($d->ttl_rp, 0) }}</td>
                                <td align="center">
                                    <input type="checkbox"
                                        @change="tambah({{ $d->no_box }}, '{{ $d->name }}', {{ $d->pcs_awal }}, {{ $d->gr_awal }},{{ $d->ttl_rp }})"
                                        value="{{ $d->no_box }}" x-model="cek">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <style>
                    .select2-container--default .select2-selection--single .select2-selection__rendered {
                        color: #000000;
                        line-height: 36px;
                        font-size: 12px;
                        width: auto;
                    }
                </style>


                {{-- modal ambil box ke cetak --}}
                <form action="{{ route('cetaknew.save_formulir') }}" method="post">
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
                                    <th class="dhead">Pemilik</th>
                                    <th class="dhead">Pcs</th>
                                    <th class="dhead">Gr</th>
                                    <th class="dhead">Total Rp</th>

                                </tr>
                            </thead>
                            <tbody>
                                <input class="d-none" name="no_box[]" type="text" :value="cek">
                                <template x-for="item in selectedItem">
                                    <tr>
                                        <td x-text="item.no_box"></td>
                                        <td x-text="item.name"></td>
                                        <td x-text="item.pcs_awal"></td>
                                        <td x-text="item.gr_awal"></td>
                                        <td x-text="item.ttl_rp"></td>

                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </x-theme.modal>
                </form>

            </div>



        </div>
        @section('scripts')
            <script>
                ["tbl1", "tbl2", "tbl3"].forEach((tbl, i) => pencarian(`tbl${i+1}input`, tbl));
            </script>
        @endsection
    </x-slot>

</x-theme.app>
