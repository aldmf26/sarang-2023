<x-theme.app title="{{ $title }}" table="T" sizeCard="12" cont="container-fluid">
    <style>
        tbody,
        td {
            border: 0 solid;
            border-color: #787878;
            font-size: 11px;
        }

        tbody,
        th {
            border: 0 solid;
            border-color: #787878;
            font-size: 11px;
        }

        .dhead {
            border: 0 solid white !important;
            background-color: #435EBE !important;
            color: white;
            font-size: 11px;
        }

        .border-hilang {
            border-right: transparent !important;
            border-left: transparent !important;
            border-bottom: transparent !important;
            border-top: transparent !important
        }

        .table-cutome th,
        .table-cutome td {
            padding: 0.5rem;
            border: 1px solid;
            border-collapse: collapse;
            border-color: #787878;
            width: 100%;
            font-size: 11px
        }
    </style>

    <div class="row ">
        <div class="d-flex justify-content-start">
            <div class="p-2 bd-highlight">
                <table class="table  table-bordered">
                    <tr>
                        <th class="dhead">No Lot</th>
                        <td>{{ $detail->no_lot }}</td>
                    </tr>
                    <tr>
                        <th class="dhead">No Box</th>
                        <td>{{ $detail->no_box }}</td>
                    </tr>
                    <tr>
                        <th class="dhead">Tipe</th>
                        <td>{{ $detail->tipe }}</td>
                    </tr>
                    <tr>
                        <th class="dhead">Ket</th>
                        <td>{{ $detail->ket_bk }}</td>
                    </tr>
                    <tr>
                        <th class="dhead">Warna</th>
                        <td>{{ $detail->nm_warna }}</td>
                    </tr>

                </table>
            </div>
            <div class="p-2 bd-highlight">
                <table class="table  table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">Tipe</th>
                            <th class="dhead">Pgws</th>
                            <th class="dhead">Nama</th>
                            <th class="dhead">Tgl Terima</th>
                            <th class="dhead text-end">Pcs Awal</th>
                            <th class="dhead text-end">Pcs Hcr</th>
                            <th class="dhead text-end">Pcs Flx</th>
                            <th class="dhead text-end">Ttl Pcs</th>
                            <td colspan="3" class="border-hilang"></td>
                        </tr>
                    </thead>
                    <tr>
                        <td class="">Bk Serah</td>
                        <td class="" rowspan="2">{{ $detail->pengawas }}</td>
                        <td class="" rowspan="2">{{ $detail->name }}</td>
                        <td class="">{{ date('d M y', strtotime($detail->tgl)) }}</td>
                        <td class="text-end ">{{ $detail->pcs_awal }}</td>
                        <td class="text-end ">0</td>
                        <td class="text-end ">0</td>
                        <td class="text-end ">{{ $detail->pcs_awal }}</td>
                        <td colspan="3" rowspan="3" class="border-hilang"></td>

                    </tr>
                    <tr>
                        <td class="">Bk Terima</td>
                        <td class="">{{ date('d M y', strtotime($detail->tgl)) }}</td>
                        <td class="text-end ">{{ $detail->pcs_awal }}</td>
                        <td class="text-end ">0</td>
                        <td class="text-end ">0</td>
                        <td class="text-end ">{{ $detail->pcs_awal }}</td>



                    </tr>
                    <tr>
                        <td colspan="8" class="border-hilang">&nbsp;</td>
                    </tr>
                    <tr>
                        <th class="dhead">Tipe</th>
                        <th class="dhead">Pgws</th>
                        <th class="dhead">Nama/kls</th>
                        <th class="dhead">Tgl Terima</th>
                        <th class="dhead text-end">Pcs Awal</th>
                        <th class="dhead text-end">Pcs Turun</th>
                        <th class="dhead text-end">Pcs Flx</th>
                        <th class="dhead text-end">Pcs Ttl</th>
                        <th class="dhead text-end">Pcs Hcr</th>
                        <th class="dhead text-end">Rp denda</th>
                        <th class="dhead text-end">Ttl Rp</th>
                    </tr>
                    @foreach ($cabut as $c)
                        <tr>
                            <td>Cbt</td>
                            <td>{{ $c->name }}</td>
                            <td rowspan="2">{{ $c->nama }} ({{ $c->id_kelas }})</td>
                            <td>{{ date('d M y', strtotime($c->tgl_terima)) }}</td>
                            <td align="right">{{ $c->pcs_awal }}</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">{{ $c->pcs_awal }}</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">{{ number_format($c->rupiah, 0) }}</td>
                        </tr>
                        <tr>
                            <td>Penilaian</td>
                            <td>{{ $c->name }}</td>
                            <td>{{ date('d M y', strtotime($c->tgl_serah)) }}</td>
                            <td align="right">{{ $c->pcs_akhir }}</td>
                            <td align="right">0</td>
                            <td align="right">{{ empty($c->pcs_flx) ? '0' : $c->pcs_flx }}</td>
                            <td align="right">{{ $c->pcs_akhir + $c->pcs_flx }}</td>
                            <td align="right">{{ $c->pcs_hcr }}</td>
                            <td align="right">{{ $c->denda_hcr * $c->pcs_hcr }}</td>
                            @php
                                $hasil = rumusTotalRp($c);
                            @endphp
                            <td align="right">{{ number_format($hasil->ttl_rp, 0) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="8" class="border-hilang">&nbsp;</td>
                    </tr>
                    <tr>
                        <th class="dhead">Tipe</th>
                        <th class="dhead">Pgws</th>
                        <th class="dhead">Nama/kls</th>
                        <th class="dhead">Tgl Terima</th>
                        <th class="dhead text-end">Pcs dictk</th>
                        <th class="dhead text-end">Pcs hcr</th>
                        <th class="dhead text-end">Pcs cuc</th>
                        <th class="dhead text-end">Pcs tdk ctk</th>
                        <th class="dhead text-end">Pcs Ttl</th>
                        <th class="dhead text-end">Rp denda</th>
                        <th class="dhead text-end">Ttl Rp</th>
                    </tr>
                    @foreach ($cetak as $c)
                        @php
                            $susut = empty($c->gr_akhir) ? '0' : (1 - ($c->gr_akhir + $c->gr_cu) / ($c->gr_awal - $c->gr_tidak_ctk)) * 100;
                            $denda = round($susut, 0) >= $c->batas_susut ? round($susut) * $c->denda_susut : 0;
                            $denda_hcr = $c->pcs_hcr * $c->denda_hcr;
                            $ttl_rp = $c->pcs_akhir == '0' ? $c->pcs_awal_ctk * $c->rp_pcs : $c->pcs_akhir * $c->rp_pcs;
                        @endphp
                        <tr>
                            <td>CTK</td>
                            <td>{{ $c->name }}</td>
                            <td rowspan="2">{{ $c->nama }}({{ $c->id_kelas }})</td>
                            <td>{{ date('d M y', strtotime($c->tgl)) }}</td>
                            <td align="right">{{ $c->pcs_awal_ctk }}</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">{{ $c->pcs_tidak_ctk }}</td>
                            <td align="right">{{ $c->pcs_tidak_ctk + $c->pcs_awal_ctk }}</td>
                            <td align="right">0</td>
                            <td align="right">{{ number_format($c->pcs_awal_ctk * $c->rp_pcs, 0) }} </td>
                        </tr>
                        <tr>
                            <td>Terima</td>
                            <td>{{ $c->name }}</td>
                            <td>{{ date('d M y', strtotime($c->tgl_serah)) }}</td>
                            <td align="right">{{ $c->pcs_akhir }}</td>
                            <td align="right">{{ $c->pcs_hcr }}</td>
                            <td align="right">{{ $c->pcs_cu }}</td>
                            <td align="right">0</td>
                            <td align="right">{{ $c->pcs_akhir + $c->pcs_cu }}</td>
                            <td align="right">0</td>
                            <td align="right">{{ number_format($ttl_rp - $denda - $denda_hcr, 0) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="8" class="border-hilang">&nbsp;</td>
                    </tr>
                    <tr>
                        <th class="dhead">Tipe</th>
                        <th class="dhead">Pgws</th>
                        <th class="dhead">Nama/kls</th>
                        <th class="dhead">Tgl Terima</th>
                        <th class="dhead text-end">Pcs sortir ck</th>
                        <th class="dhead text-end">Pcs pcs cuc</th>
                        <th class="dhead text-end">Pcs pcs tdk sortir</th>
                        <th class="dhead text-end">Pcs cus</th>
                        <th class="dhead text-end">Pcs Ttl</th>
                        <th class="dhead text-end">Rp denda</th>
                        <th class="dhead text-end">Ttl Rp</th>
                    </tr>
                    <tr>
                        <td>Sortir</td>
                        <td rowspan="2"></td>
                        <td rowspan="3"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Check Cus</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Trima</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>


                </table>

            </div>
            <div class="p-2 bd-highlight">
                <table class="table  table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead text-end">Gr awal</th>
                            <th class="dhead">Gr hcr</th>
                            <th class="dhead">Gr Flx</th>
                            <th class="dhead">Gr Ttl</th>
                            <th class="dhead text-end">Susut</th>
                            <th class="dhead">Ttd Pgws</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-end">{{ number_format($detail->gr_awal, 0) }}</td>
                            <td class="text-end">0</td>
                            <td class="text-end">0</td>
                            <td class="text-end">{{ number_format($detail->gr_awal, 0) }}</td>
                            <td class="text-end" rowspan="2">0</td>
                            <td></td>
                            <td rowspan="3" class="border-hilang"></td>
                        </tr>
                        <tr>
                            <td class="text-end">{{ number_format($detail->gr_awal, 0) }}</td>
                            <td class="text-end">0</td>
                            <td class="text-end">0</td>
                            <td class="text-end">{{ number_format($detail->gr_awal, 0) }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="6" class="border-hilang">&nbsp;</td>
                        </tr>
                        <tr>
                            <th class="dhead text-end">Gr awal</th>
                            <th class="dhead text-end">Gr hcr</th>
                            <th class="dhead text-end">Gr Flx</th>
                            <th class="dhead text-end">Gr Ttl</th>
                            <th class="dhead text-end">Eo Bersih</th>
                            <th class="dhead text-end">Susut</th>
                            <th class="dhead">Ttd Pgws</th>
                        </tr>
                        @foreach ($cabut as $c)
                            <tr>
                                <td align="right">{{ number_format($c->gr_awal, 0) }}</td>
                                <td></td>
                                <td></td>
                                <td align="right">{{ number_format($c->gr_awal, 0) }}</td>
                                <td align="right" rowspan="2">{{ number_format($c->eot, 0) }}</td>
                                <td align="right" rowspan="2">
                                    {{ number_format((1 - ($c->gr_flx + $c->gr_akhir) / $c->gr_awal) * 100, 0) }}%</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td align="right">{{ number_format($c->gr_akhir, 0) }}</td>
                                <td align="right">{{ number_format($c->gr_hcr, 0) }}</td>
                                <td align="right">{{ number_format($c->gr_flx, 0) }}</td>
                                <td align="right">{{ number_format($c->gr_akhir + $c->gr_hcr + $c->gr_flx, 0) }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="6" class="border-hilang">&nbsp;</td>
                        </tr>
                        <tr>
                            <th class="dhead text-end">Gr Dictk</th>
                            <th class="dhead text-end">Gr Cu</th>
                            <th class="dhead text-end">Gr Tdk Ctk</th>
                            <th class="dhead text-end">Gr Ttl</th>
                            <th class="dhead text-end"></th>
                            <th class="dhead text-end">Susut</th>
                            <th class="dhead">Ttd Pgws</th>
                        </tr>
                        @foreach ($cetak as $c)
                            <tr>
                                <td align="right">{{ $c->gr_awal_ctk }}</td>
                                <td align="right">0</td>
                                <td align="right">{{ $c->gr_tidak_ctk }}</td>
                                <td align="right">{{ $c->gr_awal_ctk + $c->gr_tidak_ctk }}</td>
                                <td rowspan="2"></td>
                                <td rowspan="2" align="right">
                                    {{ number_format((1 - ($c->gr_akhir + $c->gr_cu) / $c->gr_awal_ctk) * 100, 0) }} %
                                </td>
                                <td rowspan="2"></td>
                            </tr>
                            <tr>
                                <td align="right">{{ $c->gr_akhir }}</td>
                                <td align="right">{{ $c->gr_cu }}</td>
                                <td align="right">0</td>
                                <td align="right">{{ $c->gr_akhir + $c->gr_cu }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="6" class="border-hilang">&nbsp;</td>
                        </tr>
                        <tr>
                            <th class="dhead text-end">Gr sortir</th>
                            <th class="dhead text-end">Gr Cu</th>
                            <th class="dhead text-end">Gr Tdk sortir</th>
                            <th class="dhead text-end">Gr Ttl</th>
                            <th class="dhead text-end"></th>
                            <th class="dhead text-end">Susut</th>
                            <th class="dhead text-center">Ttd Pgws</th>

                        </tr>
                        <tr>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td rowspan="3"></td>
                            <td rowspan="3"></td>
                            <td rowspan="3"></td>
                        </tr>
                        <tr>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                        </tr>
                        <tr>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>





    </div>


</x-theme.app>
