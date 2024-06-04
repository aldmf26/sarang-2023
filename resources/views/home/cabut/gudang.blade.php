<x-theme.app title="{{ $title }}" table="T">
    <x-slot name="slot">
        <div x-data="{
            cek: [],
            selectedItem: [],
            tambah(no_box, pcs, gr, ttl_rp_cbt, ttl_rp) {
                const selectedItem = this.selectedItem
                const cetak = this.cetak
        
                const index = selectedItem.findIndex(item => item.no_box === no_box);
                if (index === -1) {
                    selectedItem.push({
                        no_box: no_box,
                        pcs: parseFloat(pcs),
                        gr: parseFloat(gr),
                        ttl_rp_cbt: parseFloat(ttl_rp_cbt),
                        ttl_rp: parseFloat(ttl_rp),
                    });
                } else {
                    selectedItem.splice(index, 1);
                }
        
            },
            formatNumber(value) {
                // Format number with '.' as thousands separator and ',' as decimal separator
                return new Intl.NumberFormat('id-ID', { style: 'decimal', maximumFractionDigits: 0 }).format(value);
            }
        }">
            <div class="d-flex justify-content-between mb-3">
                <h6>{{ $title }}</h6>
                <div>
                    <a class="btn btn-sm btn-primary"
                        href="{{ route('cabut.export_gudang', ['bulan' => $bulan, 'tahun' => $tahun, 'id_user' => $id_user]) }}"><i
                            class="fas fa-print"></i> Export All</a>
                    <x-theme.button href="#" icon="fa-plus" variant="info" modal="Y" idModal="tambah"
                        teks="serah" />
                    <x-theme.button href="{{ route('gudangsarang.invoice') }}" icon="fa-clipboard-list"
                        teks="Po" />
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <input type="text" id="tbl1input" class="form-control form-control-sm mb-2" placeholder="cari">
                    <div style="overflow-y: scroll; height: 700px">
                        <table id="tbl1" class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="dhead text-center" colspan="4">Box Stock</th>
                                </tr>
                                <tr>
                                    <th class="dhead text-center">No Box</th>
                                    <th class="dhead text-end">Pcs</th>
                                    <th class="dhead text-end">Gr</th>
                                    {{-- <th class="dhead text-end">Rp/gr</th> --}}
                                    <th class="dhead text-end">Total rp</th>
                                </tr>
                                @php

                                    if (!function_exists('ttl')) {
                                        function ttl($tl)
                                        {
                                            return [
                                                'pcs' => array_sum(array_column($tl, 'pcs')),
                                                'gr' => array_sum(array_column($tl, 'gr')),
                                                // 'hrga_satuan' => array_sum(array_column($tl, 'hrga_satuan')),
                                                'ttl_rp' => array_sum(array_column($tl, 'ttl_rp')),
                                                'ttl_rp_cbt' => array_sum(array_column($tl, 'ttl_rp_cbt')),
                                            ];
                                        }
                                    }

                                @endphp
                                <tr>
                                    <th class="dheadstock text-center">Total</th>
                                    <th class="dheadstock text-end">{{ number_format(ttl($bk)['pcs'], 0) }}</th>
                                    <th class="dheadstock text-end">{{ number_format(ttl($bk)['gr'], 0) }}</th>
                                    {{-- <th class="dheadstock text-end">{{ number_format(ttl($bk)['hrga_satuan'], 0) }}</th> --}}
                                    <th class="dheadstock text-end">
                                        {{ number_format(ttl($bk)['ttl_rp'], 0) }}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($bk as $d)
                                    <tr>
                                        <td align="center">{{ $d->no_box }}</td>
                                        <td align="right">{{ $d->pcs }}</td>
                                        <td align="right">{{ $d->gr }}</td>
                                        {{-- <td align="right">{{ number_format($d->hrga_satuan, 0) }}</td> --}}
                                        <td align="right">{{ number_format($d->hrga_satuan * $d->gr, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-4">
                    <input type="text" id="tbl2input" class="form-control form-control-sm mb-2" placeholder="cari">
                    <div style="overflow-y: scroll; height: 700px">

                        <table id="tbl2" class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="dhead text-center" colspan="4">Box sedang proses</th>
                                </tr>
                                <tr>

                                    <th class="dhead text-center">No Box</th>
                                    <th class="dhead text-end">Pcs</th>
                                    <th class="dhead text-end">Gr</th>
                                    {{-- <th class="dhead text-end">Rp/Gr</th> --}}
                                    <th class="dhead text-end">Total Rp</th>
                                </tr>
                                <tr>
                                    <th class="dheadstock text-center">Total</th>
                                    <th class="dheadstock text-end">{{ number_format(ttl($cabut)['pcs'], 0) }}</th>
                                    <th class="dheadstock text-end">{{ number_format(ttl($cabut)['gr'], 0) }}</th>
                                    {{-- <th class="dheadstock text-end">{{ number_format(ttl($cabut)['hrga_satuan'], 0) }}
                                    </th> --}}
                                    <th class="dheadstock text-end">{{ number_format(ttl($cabut)['ttl_rp'], 0) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cabut as $d)
                                    <tr>
                                        <td align="center">{{ $d->no_box }}</td>
                                        <td align="right">{{ $d->pcs }}</td>
                                        <td align="right">{{ $d->gr }}</td>
                                        {{-- <td align="right">{{ number_format($d->hrga_satuan, 0) }}</td> --}}
                                        <td align="right">{{ number_format($d->ttl_rp, 0) }}</td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-4">
                    <input type="text" id="tbl3input" class="form-control form-control-sm mb-2" placeholder="cari">
                    <div style="overflow-y: scroll; height: 700px">

                        <table id="tbl3" class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="dhead text-center" colspan="6">
                                        <span>Box selesai siap ctk</span>

                                    </th>
                                </tr>
                                <tr>
                                    <th class="dhead text-center">No Box</th>
                                    <th class="dhead text-end">Pcs</th>
                                    <th class="dhead text-end">Gr</th>
                                    <th class="dhead text-end">Total Rp Bk</th>
                                    <th class="dhead text-end">Total Rp Cbt</th>
                                    <th class="dhead "></th>
                                </tr>
                                <tr>
                                    <th class="dheadstock text-center">Total</th>
                                    <th class="dheadstock text-end">{{ number_format(ttl($cabutSelesai)['pcs'], 0) }}
                                    </th>
                                    <th class="dheadstock text-end">{{ number_format(ttl($cabutSelesai)['gr'], 0) }}
                                    </th>
                                    <th class="dheadstock text-end">
                                        {{ number_format(ttl($cabutSelesai)['ttl_rp'], 0) }}
                                    </th>
                                    <th class="dheadstock text-end">
                                        {{ number_format(ttl($cabutSelesai)['ttl_rp_cbt'], 0) }}
                                    </th>
                                    <th class="dheadstock text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cabutSelesai as $d)
                                    <tr>
                                        <td align="center">{{ $d->no_box }}</td>
                                        <td align="right">{{ $d->pcs }}</td>
                                        <td align="right">{{ $d->gr }}</td>
                                        <td align="right">{{ number_format($d->ttl_rp, 0) }}</td>
                                        <td align="right">{{ number_format($d->ttl_rp_cbt, 0) }}</td>
                                        <td align="center">
                                            <input type="checkbox"
                                                @change="tambah({{ $d->no_box }}, {{ $d->pcs }}, {{ $d->gr }},{{ $d->ttl_rp_cbt }},{{ $d->ttl_rp }})"
                                                value="{{ $d->no_box }}" x-model="cek">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- modal ambil box ke cetak --}}
                    <form action="{{ route('cabut.save_formulir') }}" method="post">
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
                                <div class="col-lg-12">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th class="dhead">No Box</th>
                                                <th class="dhead text-end">Pcs</th>
                                                <th class="dhead text-end">Gr</th>
                                                <th class="dhead text-end">Total Rp Bk</th>
                                                <th class="dhead text-end">Total Rp Cbt</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <input class="d-none" name="no_box[]" type="text"
                                                :value="cek">
                                            <template x-for="item in selectedItem">
                                                <tr>

                                                    <td x-text="item.no_box"></td>
                                                    <td align="right" x-text="item.pcs"></td>
                                                    <td align="right" x-text="item.gr"></td>
                                                    <td align="right" x-text="formatNumber(item.ttl_rp)"></td>
                                                    <td align="right" x-text="formatNumber(item.ttl_rp_cbt)"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                        </x-theme.modal>
                    </form>
                </div>
            </div>
        </div>
        @section('scripts')
            <script>
                ["tbl1", "tbl2", "tbl3"].forEach((tbl, i) => pencarian(`tbl${i+1}input`, tbl));
            </script>
        @endsection
    </x-slot>

</x-theme.app>
