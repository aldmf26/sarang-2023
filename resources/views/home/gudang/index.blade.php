<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="row">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">Gudang Cabut</h6>
            </div>
            <div class="col-lg-6">
                <a class="btn btn-sm btn-primary float-end" href="{{ route('cabut.export_gudang') }}"><i
                        class="fas fa-print"></i>
                    Export All</a>
            </div>
        </div>

    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-4">
                <input type="text" id="tbl1input" class="form-control form-control-sm mb-2" placeholder="cari">
                <div style="overflow-y: scroll; height: 400px">
                    <table id="tbl1" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="dhead text-center" colspan="6">Box Stock</th>
                            </tr>
                            <tr>
                                <th class="dhead text-center">Pemilik</th>
                                <th class="dhead text-center">Partai</th>
                                <th class="dhead text-center">No Box</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Rp/gr</th>
                            </tr>
                            @php
                                if (!function_exists('ttl')) {
                                    function ttl($tl)
                                    {
                                        return [
                                            'pcs' => array_sum(array_column($tl, 'pcs')),
                                            'gr' => array_sum(array_column($tl, 'gr')),
                                            'hrga_satuan' => array_sum(array_column($tl, 'hrga_satuan')),
                                        ];
                                    }
                                }

                            @endphp
                            <tr>
                                <th class="dheadstock text-center">Total</th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-end">{{ number_format(ttl($bk)['pcs'], 0) }}</th>
                                <th class="dheadstock text-end">{{ number_format(ttl($bk)['gr'], 0) }}</th>
                                <th class="dheadstock text-end">{{ number_format(ttl($bk)['hrga_satuan'], 0) }}</th>
                                {{-- <th class="dheadstock text-end">
                                    {{ number_format(ttl($bk)['ttl_rp'], 0) }}</th> --}}
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($bk as $d)
                                <tr>
                                    <td align="center">{{ $d->penerima }}</td>
                                    <td align="center">{{ $d->nm_partai }}</td>
                                    <td align="center">{{ $d->no_box }}</td>
                                    <td align="right">{{ $d->pcs }}</td>
                                    <td align="right">{{ $d->gr }}</td>
                                    <td align="right">{{ number_format($d->hrga_satuan, 0) }}</td>
                                    {{-- <td align="right">{{ number_format($d->hrga_satuan * $d->gr, 0) }}</td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-4">
                <input type="text" id="tbl2input" class="form-control form-control-sm mb-2" placeholder="cari">
                <div style="overflow-y: scroll; height: 400px">

                    <table id="tbl2" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="dhead text-center" colspan="6">Box sedang proses</th>
                            </tr>
                            <tr>

                                <th class="dhead text-center">Pemilik</th>
                                <th class="dhead text-center">Partai</th>
                                <th class="dhead text-center">No Box</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Rp/Gr</th>
                                {{-- <th class="dhead text-end">Total Rp</th> --}}
                            </tr>
                            <tr>
                                <th class="dheadstock text-center">Total</th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-end">{{ number_format(ttl($cabut)['pcs'], 0) }}</th>
                                <th class="dheadstock text-end">{{ number_format(ttl($cabut)['gr'], 0) }}</th>
                                <th class="dheadstock text-end">{{ number_format(ttl($cabut)['hrga_satuan'], 0) }}
                                </th>
                                {{-- <th class="dheadstock text-end">{{ number_format(ttl($cabut)['ttl_rp'], 0) }}</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cabut as $d)
                                <tr>
                                    <td align="center">{{ $d->penerima }}</td>
                                    <td align="center">{{ $d->nm_partai }}</td>
                                    <td align="center">{{ $d->no_box }}</td>
                                    <td align="right">{{ $d->pcs }}</td>
                                    <td align="right">{{ $d->gr }}</td>
                                    <td align="right">{{ number_format($d->hrga_satuan, 0) }}</td>
                                    {{-- <td align="right">{{ number_format($d->ttl_rp, 0) }}</td> --}}
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-4">
                <input type="text" id="tbl3input" class="form-control form-control-sm mb-2" placeholder="cari">
                <div style="overflow-y: scroll; height: 400px">

                    <table id="tbl3" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="dhead text-center" colspan="6">
                                    <span>Box selesai siap ctk</span>
                                </th>
                            </tr>
                            <tr>
                                <th class="dhead text-center ">Pemilik</th>
                                <th class="dhead text-center ">Partai</th>
                                <th class="dhead text-center">No Box</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Rp/Gr</th>
                                {{-- <th class="dhead text-end">Total Rp Cbt</th> --}}

                            </tr>
                            <tr>
                                <th class="dheadstock text-center">Total</th>
                                <th class="dheadstock text-center "></th>
                                <th class="dheadstock text-center "></th>
                                <th class="dheadstock text-end">{{ number_format(ttl($cabutSelesai)['pcs'], 0) }}
                                </th>
                                <th class="dheadstock text-end">{{ number_format(ttl($cabutSelesai)['gr'], 0) }}
                                </th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl($cabutSelesai)['hrga_satuan'], 0) }}
                                </th>
                                {{-- <th class="dheadstock text-end">
                                    {{ number_format(ttl($cabutSelesai)['ttl_rp_cbt'], 0) }}
                                </th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cabutSelesai as $d)
                                <tr>
                                    <td align="center">{{ $d->pengawas }}</td>
                                    <td align="center">{{ $d->nm_partai }}</td>
                                    <td align="center">{{ $d->no_box }}</td>
                                    <td align="right">{{ $d->pcs }}</td>
                                    <td align="right">{{ $d->gr }}</td>
                                    <td align="right">{{ number_format($d->hrga_satuan, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-12">
                <br>
                <br>
                <br>

            </div>
            <div class="col-lg-4">
                <input type="text" id="tbl4input" class="form-control form-control-sm mb-2" placeholder="cari">
                <div style="overflow-y: scroll; height: 400px">

                    <table id="tbl4" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="dhead text-center" colspan="6">
                                    <span>Eox selesai siap str</span>
                                </th>
                            </tr>
                            <tr>
                                <th class="dhead text-center ">Pemilik</th>
                                <th class="dhead text-center ">Partai</th>
                                <th class="dhead text-center">No Box</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Rp/Gr</th>
                                {{-- <th class="dhead text-end">Total Rp Cbt</th> --}}

                            </tr>
                            <tr>
                                <th class="dheadstock text-center">Total</th>
                                <th class="dheadstock text-center "></th>
                                <th class="dheadstock text-center "></th>
                                <th class="dheadstock text-end">0
                                </th>
                                <th class="dheadstock text-end">{{ number_format(ttl($eoSelesai)['gr'], 0) }}
                                </th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl($eoSelesai)['hrga_satuan'], 0) }}
                                </th>
                                {{-- <th class="dheadstock text-end">
                                    {{ number_format(ttl($cabutSelesai)['ttl_rp_cbt'], 0) }}
                                </th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($eoSelesai as $d)
                                <tr>
                                    <td align="center">{{ $d->pengawas }}</td>
                                    <td align="center">{{ $d->nm_partai }}</td>
                                    <td align="center">{{ $d->no_box }}</td>
                                    <td align="right">0</td>
                                    <td align="right">{{ $d->gr }}</td>
                                    <td align="right">{{ number_format($d->hrga_satuan, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-12">
                <br>
                <br>
                <br>
                <h6 class="float-start mt-1">Gudang Cetak</h6>
            </div>
            <div class="col-lg-4">

                <input type="text" id="tbl5input" class="form-control form-control-sm mb-2" placeholder="cari">
                <div style="overflow-y: scroll; height: 400px">
                    <table id="tbl5" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="dhead text-center" colspan="6">Cetak stock</th>
                            </tr>
                            <tr>
                                <th class="dhead text-center">Pemilik</th>
                                <th class="dhead text-center">Partai</th>
                                <th class="dhead text-center">No Box</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Rp/gr</th>

                            </tr>
                            @php

                                if (!function_exists('ttl2')) {
                                    function ttl2($tl)
                                    {
                                        return [
                                            'pcs_awal' => array_sum(array_column($tl, 'pcs_awal')),
                                            'gr_awal' => array_sum(array_column($tl, 'gr_awal')),
                                            'ttl_rp' => array_sum(array_column($tl, 'ttl_rp')),
                                            'cost_cbt' => array_sum(array_column($tl, 'cost_cbt')),
                                            'cost_ctk' => array_sum(array_column($tl, 'cost_ctk')),
                                        ];
                                    }
                                }
                            @endphp
                            <tr>
                                <th class="dheadstock text-center">Total</th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl2($cabut_selesai)['pcs_awal'], 0) }}
                                </th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl2($cabut_selesai)['gr_awal'], 0) }}
                                </th>
                                <th class="dheadstock text-end"></th>
                                {{-- <th class="dheadstock text-end">{{ number_format(ttl2($cabut_selesai)['ttl_rp'], 0) }}
                                </th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl2($cabut_selesai)['cost_cbt'], 0) }}
                                </th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cabut_selesai as $d)
                                <tr>
                                    <td align="center">{{ $d->name }}</td>
                                    <td align="center">{{ $d->nm_partai }}</td>
                                    <td align="center">{{ $d->no_box }}</td>
                                    <td align="right">{{ $d->pcs_awal }}</td>
                                    <td align="right">{{ $d->gr_awal }}</td>
                                    <td align="right">
                                        {{ number_format(($d->ttl_rp + $d->cost_cbt) / $d->gr_awal, 0) }}</td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-lg-4">
                <input type="text" id="tbl6input" class="form-control form-control-sm mb-2" placeholder="cari">
                <div style="overflow-y: scroll; height: 400px">
                    <table id="tbl6" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="dhead text-center" colspan="7">Cetak sedang proses</th>
                            </tr>
                            <tr>

                                <th class="dhead text-center">Pemilik</th>
                                <th class="dhead text-center">Pengawas</th>
                                <th class="dhead text-center">Partai</th>
                                <th class="dhead text-center">No Box</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Rp/gr</th>
                                {{-- <th class="dhead text-end">Ttl Rp</th>
                                <th class="dhead text-end">Total Rp Cbt</th> --}}
                            </tr>
                            <tr>
                                <th class="dheadstock text-center">Total</th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl2($cetak_proses)['pcs_awal'], 0) }}
                                </th>
                                <th class="dheadstock text-end">{{ number_format(ttl2($cetak_proses)['gr_awal'], 0) }}
                                </th>
                                <th class="dheadstock text-end"></th>
                                {{-- <th class="dheadstock text-end">{{ number_format(ttl2($cetak_proses)['ttl_rp'], 0) }}
                                </th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl2($cetak_proses)['cost_cbt'], 0) }}
                                </th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cetak_proses as $d)
                                <tr>
                                    <td align="center">{{ $d->name }}</td>
                                    <td align="center">{{ $d->pgws }}</td>
                                    <td align="center">{{ $d->nm_partai }}</td>
                                    <td align="center">{{ $d->no_box }}</td>
                                    <td align="right">{{ $d->pcs_awal }}</td>
                                    <td align="right">{{ $d->gr_awal }}</td>
                                    <td align="right">
                                        {{ number_format(($d->ttl_rp + $d->cost_cbt) / $d->gr_awal, 0) }}</td>
                                    {{-- <td align="right">{{ number_format($d->ttl_rp, 0) }}</td>
                                    <td align="right">{{ number_format($d->cost_cbt, 0) }}</td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-4">
                <input type="text" id="tbl7input" class="form-control form-control-sm mb-2" placeholder="cari">
                <div style="overflow-y: scroll; height: 400px">
                    <table id="tbl7" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="dhead text-center" colspan="7">
                                    <span>Cetak selesai siap sortir</span>
                                </th>
                            </tr>
                            <tr>
                                <th class="dhead text-center">Pemilik</th>
                                <th class="dhead text-center">Pengawas</th>
                                <th class="dhead text-center">Partai</th>
                                <th class="dhead text-center">No Box</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Rp/gr</th>
                                {{-- <th class="dhead text-end">Total Rp Cbt</th>
                                <th class="dhead text-end">Total Rp Ctk</th> --}}

                            </tr>
                            <tr>
                                <th class="dheadstock text-center">Total</th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-center"></th>

                                <th class="dheadstock text-end">
                                    {{ number_format(ttl2($cetak_selesai)['pcs_awal'], 0) }}
                                </th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl2($cetak_selesai)['gr_awal'], 0) }}
                                </th>
                                <th class="dheadstock text-end"></th>
                                {{-- <th class="dheadstock text-end">
                                    {{ number_format(ttl2($cetak_selesai)['cost_cbt'], 0) }}
                                </th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl2($cetak_selesai)['cost_ctk'], 0) }}
                                </th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cetak_selesai as $d)
                                <tr>
                                    <td align="center">{{ $d->name }} </td>
                                    <td align="center">{{ $d->pgws }} </td>
                                    <td align="center">{{ $d->nm_partai }} </td>
                                    <td align="center">{{ $d->no_box }}</td>
                                    <td align="right">{{ $d->pcs_awal }}</td>
                                    <td align="right">{{ $d->gr_awal }}</td>
                                    <td align="right">
                                        {{ number_format(($d->ttl_rp + $d->cost_cbt + $d->cost_ctk) / $d->gr_awal, 0) }}
                                    </td>
                                    {{-- <td align="right">{{ number_format($d->cost_cbt, 0) }}</td>
                                    <td align="right">{{ number_format($d->cost_ctk, 0) }}</td> --}}

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-lg-12">
                <br>
                <br>
                <br>
                <h6 class="float-start mt-1">Gudang Sortir</h6>
            </div>
            <div class="col-lg-4">
                <input type="text" id="tbl8input" class="form-control form-control-sm mb-2" placeholder="cari">
                <div style="overflow-y: scroll; height: 400px">
                    <table id="tbl8" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="dhead text-center" colspan="6">Sortir stock</th>
                            </tr>
                            <tr>
                                <th class="dhead text-center">Pemilik</th>
                                <th class="dhead text-center">Partai</th>
                                <th class="dhead text-center">No Box</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Rp/gr</th>
                                {{-- <th class="dhead text-end">Total Rp</th>
                                <th class="dhead text-end">Total Rp Cbt</th>
                                <th class="dhead text-end">Total Rp Ctk</th> --}}
                            </tr>
                            @php
                                if (!function_exists('ttl3')) {
                                    function ttl3($tl)
                                    {
                                        return [
                                            'pcs_awal' => array_sum(array_column($tl, 'pcs_awal')),
                                            'gr_awal' => array_sum(array_column($tl, 'gr_awal')),
                                            'ttl_rp' => array_sum(array_column($tl, 'ttl_rp')),
                                            'cost_cbt' => array_sum(array_column($tl, 'cost_cbt')),
                                            'cost_ctk' => array_sum(array_column($tl, 'cost_ctk')),
                                        ];
                                    }
                                }
                            @endphp
                            <tr>
                                <th class="dheadstock text-center">Total</th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-end">{{ number_format(ttl3($siap_sortir)['pcs_awal'], 0) }}
                                </th>
                                <th class="dheadstock text-end">{{ number_format(ttl3($siap_sortir)['gr_awal'], 0) }}
                                </th>
                                <th class="dheadstock text-end"></th>
                                {{-- <th class="dheadstock text-end">{{ number_format(ttl3($siap_sortir)['ttl_rp'], 0) }}
                                </th>
                                <th class="dheadstock text-end">{{ number_format(ttl3($siap_sortir)['cost_cbt'], 0) }}
                                </th>
                                <th class="dheadstock text-end">{{ number_format(ttl3($siap_sortir)['cost_ctk'], 0) }}
                                </th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($siap_sortir as $d)
                                <tr>
                                    <td align="center">{{ $d->name }}</td>
                                    <td align="center">{{ $d->nm_partai }}</td>
                                    <td align="center">{{ $d->no_box }}</td>
                                    <td align="right">{{ $d->pcs_awal }}</td>
                                    <td align="right">{{ $d->gr_awal }}</td>
                                    <td align="right">
                                        {{ number_format(($d->ttl_rp + $d->cost_cbt + $d->cost_ctk + $d->cost_eo) / $d->gr_awal, 0) }}
                                    </td>
                                    {{-- <td align="right">{{ number_format($d->ttl_rp, 0) }}</td>
                                    <td align="right">{{ number_format($d->cost_cbt, 0) }}</td>
                                    <td align="right">{{ number_format($d->cost_ctk, 0) }}</td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-4">
                <input type="text" id="tbl9input" class="form-control form-control-sm mb-2" placeholder="cari">
                <div style="overflow-y: scroll; height: 400px">
                    <table id="tbl9" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="dhead text-center" colspan="6">Sortir sedang proses</th>
                            </tr>
                            <tr>
                                <th class="dhead text-center">Pemilik</th>
                                <th class="dhead text-center">Partai</th>
                                <th class="dhead text-center">No Box</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Rp/gr</th>
                                {{-- <th class="dhead text-end">Total Rp</th>
                                <th class="dhead text-end">Total Rp Cbt</th>
                                <th class="dhead text-end">Total Rp Ctk</th> --}}
                            </tr>
                            @php
                                if (!function_exists('ttl3')) {
                                    function ttl3($tl)
                                    {
                                        return [
                                            'pcs_awal' => array_sum(array_column($tl, 'pcs_awal')),
                                            'gr_awal' => array_sum(array_column($tl, 'gr_awal')),
                                            'ttl_rp' => array_sum(array_column($tl, 'ttl_rp')),
                                            'cost_cbt' => array_sum(array_column($tl, 'cost_cbt')),
                                            'cost_ctk' => array_sum(array_column($tl, 'cost_ctk')),
                                        ];
                                    }
                                }
                            @endphp
                            <tr>
                                <th class="dheadstock text-center">Total</th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl3($sortir_proses)['pcs_awal'], 0) }}
                                </th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl3($sortir_proses)['gr_awal'], 0) }}
                                </th>
                                <th class="dheadstock text-end"></th>
                                {{-- <th class="dheadstock text-end">{{ number_format(ttl3($sortir_proses)['ttl_rp'], 0) }}
                                </th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl3($sortir_proses)['cost_cbt'], 0) }}
                                </th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl3($sortir_proses)['cost_ctk'], 0) }}
                                </th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sortir_proses as $d)
                                <tr>
                                    <td align="center">{{ $d->name }}</td>
                                    <td align="center">{{ $d->nm_partai }}</td>
                                    <td align="center">{{ $d->no_box }}</td>
                                    <td align="right">{{ $d->pcs_awal }}</td>
                                    <td align="right">{{ $d->gr_awal }}</td>
                                    <td align="right">
                                        {{ number_format(($d->ttl_rp + $d->cost_cbt + $d->cost_ctk + $d->cost_eo) / $d->gr_awal, 0) }}
                                    </td>
                                    {{-- <td align="right">{{ number_format($d->ttl_rp, 0) }}</td>
                                    <td align="right">{{ number_format($d->cost_cbt, 0) }}</td>
                                    <td align="right">{{ number_format($d->cost_ctk, 0) }}</td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-4">
                <input type="text" id="tbl10input" class="form-control form-control-sm mb-2" placeholder="cari">
                <div style="overflow-y: scroll; height: 400px">
                    <table id="tbl10" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="dhead text-center" colspan="6">Sortir selesai siap grading</th>
                            </tr>
                            <tr>
                                <th class="dhead text-center">Pemilik</th>
                                <th class="dhead text-center">Partai</th>
                                <th class="dhead text-center">No Box</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Rp/gr</th>
                                {{-- <th class="dhead text-end">Total Rp</th>
                                <th class="dhead text-end">Total Rp Cbt</th>
                                <th class="dhead text-end">Total Rp Ctk</th>
                                <th class="dhead text-end">Total Rp Str</th> --}}
                            </tr>
                            @php
                                if (!function_exists('ttl4')) {
                                    function ttl4($tl)
                                    {
                                        return [
                                            'pcs_awal' => array_sum(array_column($tl, 'pcs_awal')),
                                            'gr_awal' => array_sum(array_column($tl, 'gr_awal')),
                                            'ttl_rp' => array_sum(array_column($tl, 'ttl_rp')),
                                            'cost_cbt' => array_sum(array_column($tl, 'cost_cbt')),
                                            'cost_ctk' => array_sum(array_column($tl, 'cost_ctk')),
                                            'cost_str' => array_sum(array_column($tl, 'cost_str')),
                                        ];
                                    }
                                }
                            @endphp
                            <tr>
                                <th class="dheadstock text-center">Total</th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl4($sortir_selesai)['pcs_awal'], 0) }}
                                </th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl4($sortir_selesai)['gr_awal'], 0) }}
                                </th>
                                <th class="dheadstock text-center"></th>
                                {{-- <th class="dheadstock text-end">
                                    {{ number_format(ttl4($sortir_selesai)['ttl_rp'], 0) }}
                                </th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl4($sortir_selesai)['cost_cbt'], 0) }}
                                </th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl4($sortir_selesai)['cost_ctk'], 0) }}
                                </th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl4($sortir_selesai)['cost_ctk'], 0) }}
                                </th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl4($sortir_selesai)['cost_str'], 0) }}
                                </th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sortir_selesai as $d)
                                <tr>
                                    <td align="center">{{ $d->name }}</td>
                                    <td align="center">{{ $d->nm_partai }}</td>
                                    <td align="center">{{ $d->no_box }}</td>
                                    <td align="right">{{ $d->pcs_awal }}</td>
                                    <td align="right">{{ $d->gr_awal }}</td>
                                    <td align="right">
                                        {{ number_format(($d->ttl_rp + $d->cost_cbt + $d->cost_ctk + $d->cost_str + $d->cost_eo) / $d->gr_awal, 0) }}
                                    </td>
                                    {{-- <td align="right">{{ number_format($d->ttl_rp, 0) }}</td>
                                    <td align="right">{{ number_format($d->cost_cbt, 0) }}</td>
                                    <td align="right">{{ number_format($d->cost_ctk, 0) }}</td>
                                    <td align="right">{{ number_format($d->cost_str, 0) }}</td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>




        </section>
    </x-slot>
    @section('scripts')
        <script>
            ["tbl1", "tbl2", "tbl3", "tbl4", "tbl5", "tbl6", "tgbl7", "tbl8", "tbl9", "tbl10"].forEach((tbl, i) => pencarian(
                `tbl${i+1}input`, tbl));
        </script>
        <script>
            document.body.style.zoom = "75%";
        </script>
    @endsection
</x-theme.app>
