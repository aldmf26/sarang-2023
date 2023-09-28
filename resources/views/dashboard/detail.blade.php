<x-theme.app title="{{ $title }}" table="T" sizeCard="12" cont="container-fluid">
    <style>
        tbody,
        td {
            border: 0 solid;
            border-color: #787878;
            font-size: 10px;
        }

        tbody,
        th {
            border: 0 solid;
            border-color: #787878;
            font-size: 10px;
        }

        .warna_head {
            border: 0 solid white !important;
            background-color: #435EBE !important;
            color: white;
            font-size: 10px;
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
        <div class="col-lg-1">
            <table class="table small table-bordered">
                <tr>
                    <th class="warna_head">No Lot</th>
                    <td>{{ $detail->no_lot }}</td>
                </tr>
                <tr>
                    <th class="warna_head">No Box</th>
                    <td>{{ $detail->no_box }}</td>
                </tr>
                <tr>
                    <th class="warna_head">Tipe</th>
                    <td>{{ $detail->tipe }}</td>
                </tr>
                <tr>
                    <th class="warna_head">Ket</th>
                    <td>{{ $detail->ket_bk }}</td>
                </tr>
                <tr>
                    <th class="warna_head">Warna</th>
                    <td>{{ $detail->nm_warna }}</td>
                </tr>

            </table>
        </div>
        <div class="col-lg-6">
            <table class="table small table-bordered">
                <thead>
                    {{-- tipe bk --}}
                    <tr>
                        <th class="dhead">TIPE</th>
                        <th class="dhead">PGWS</th>
                        <th class="dhead">NAMA</th>
                        <th class="dhead">TGL TERIMA</th>
                        <th class="text-end dhead">PCS AWAL</th>
                        <th class="text-end dhead">PCS HCR</th>
                        <th class="text-end dhead">PCS FLX</th>
                        <th class="text-end dhead">PCS TTL</th>
                        <th class="text-end dhead">TTL RP</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><b>BK</b></td>
                        <td>{{ $detail->pengawas }}</td>
                        <td>{{ $detail->name }}
                        </td>
                        <td>{{ date('d M y', strtotime($detail->tgl)) }}</td>
                        <td align="right">{{ $detail->pcs_awal }}</td>
                        <td align="right">0</td>
                        <td align="right">0</td>
                        <td align="right">0</td>
                        <td align="right">0</td>


                    </tr>
                    <tr>
                        <td colspan="9" class="border-hilang">&nbsp;</td>
                    </tr>

                    @if (empty($cabut))
                        <tr>
                            <td><b>CBT</b></td>
                            <td></td>
                            <td></td>
                            <td>-</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                        </tr>
                        <tr>
                            <td><b>TERIMA</b></td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                        </tr>
                    @else
                        @foreach ($cabut as $c)
                            <tr>
                                <td><b>CBT</b></td>
                                <td>{{ $c->name }}</td>
                                <td>{{ $c->nama }}</td>
                                <td>{{ date('d M y', strtotime($c->tgl_terima)) }}</td>
                                <td align="right">{{ $c->pcs_awal }}</td>
                                <td align="right"></td>
                                <td align="right">0</td>
                                <td align="right">{{ $c->pcs_awal + $c->pcs_hcr }}</td>
                                <td align="right">{{ number_format($c->rupiah, 0) }}</td>
                            </tr>
                            <tr>
                                <td><b>TERIMA</b></td>
                                <td>{{ $c->name }}</td>
                                <td>{{ $c->nama }}</td>
                                <td>{{ date('d M y', strtotime($c->tgl_terima)) }}</td>
                                <td align="right">{{ $c->pcs_akhir }}</td>
                                <td align="right">{{ $c->pcs_hcr }}</td>
                                <td align="right">0</td>
                                <td align="right">{{ $c->pcs_akhir }}</td>
                                @php
                                    $susut = empty($c->pcs_awal) ? 0 : (1 - ($c->gr_flx + $c->gr_akhir) / $c->gr_awal) * 100;
                                    $denda = 0;
                                    $bonus_susut = 0;
                                    $rupiah = $c->rupiah;
                                    if ($susut > 23.4) {
                                        $denda = ($susut - 23.4) * 0.03 * $c->rupiah;
                                        $rupiah = $rupiah - $denda;
                                    }
                                    if ($susut < 19.5) {
                                        $bonus_susut = ($c->rp_bonus * $c->gr_awal) / $c->gr_kelas;
                                    }
                                    $denda_hcr = $c->pcs_hcr * 5000;
                                    
                                    $eot_bonus = ($c->eot - $c->gr_awal * 0.02) * 750;
                                    $ttl_rp = $rupiah - $denda_hcr + $eot_bonus + $bonus_susut;
                                @endphp
                                <td align="right">{{ number_format($ttl_rp, 0) }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    <tr>
                        <td colspan="9" class="border-hilang">&nbsp;</td>
                    </tr>
                    <tr>
                        <th class="dhead">TIPE</th>
                        <th class="dhead">PGWS</th>
                        <th class="dhead">NAMA</th>
                        <th class="dhead">TGL TERIMA</th>
                        <th class="text-end dhead">PCS AWAL</th>
                        <th class="text-end dhead">PCS HCR</th>
                        <th class="text-end dhead">PCS TDK CTK</th>
                        <th class="text-end dhead">PCS TTL</th>
                        <th class="text-end dhead">TTL RP</th>
                    </tr>
                    @if (empty($cetak))
                        <tr>
                            <td><b>CTK</b></td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                        </tr>
                        <tr>
                            <td><b>TERIMA</b></td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                        </tr>
                    @else
                        @foreach ($cetak as $c)
                            <tr>
                                <td><b>CTK</b></td>
                                <td>{{ $c->name }}</td>
                                <td>{{ $c->nama }}</td>
                                <td>{{ date('d M y', strtotime($c->tgl)) }}</td>
                                <td align="right">{{ $c->pcs_awal }}</td>
                                <td align="right">0</td>
                                <td align="right">0</td>
                                <td align="right">{{ $c->pcs_awal }}</td>
                                <td align="right">{{ number_format($c->pcs_awal * $c->rp_pcs, 0) }}</td>
                            </tr>
                            <tr>
                                <td><b>TERIMA</b></td>
                                <td>{{ $c->name }}</td>
                                <td>{{ $c->nama }}</td>
                                <td>{{ date('d M y', strtotime($c->tgl)) }}</td>
                                <td align="right">{{ $c->pcs_akhir }}</td>
                                <td align="right">0</td>
                                <td align="right">{{ $c->pcs_tidak_ctk }}</td>
                                <td align="right">{{ $c->pcs_akhir + $c->pcs_tidak_ctk }}</td>
                                @php
                                    $susut = empty($c->gr_akhir) ? '0' : (1 - $c->gr_akhir / ($c->gr_awal - $c->gr_tidak_ctk)) * 100;
                                    $denda = round($susut, 0) * 50000;
                                @endphp
                                <td align="right">{{ number_format($c->pcs_akhir * $c->rp_pcs - $denda, 0) }}</td>
                            </tr>
                        @endforeach
                    @endif



                    <tr>
                        <td colspan="9" class="border-hilang">&nbsp;</td>
                    </tr>
                    <tr>
                        <th class="dhead">TIPE</th>
                        <th class="dhead">PGWS</th>
                        <th class="dhead">NAMA</th>
                        <th class="dhead">TGL TERIMA</th>
                        <th class="text-end dhead">PCS AWAL</th>
                        <th class="text-end dhead">PCS HCR</th>
                        <th class="text-end dhead">PCS TDK SORTIR</th>
                        <th class="text-end dhead">PCS TTL</th>
                        <th class="text-end dhead">TTL RP</th>
                    </tr>
                    @if (empty($sortir))
                        <tr>
                            <td><b>SORTIR</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td align="right"></td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right"></td>
                        </tr>
                        <tr>
                            <td><b>TERIMA</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td align="right"></td>
                            <td align="right"></td>
                            <td align="right"></td>
                            <td align="right"></td>
                            <td align="right"></td>
                        </tr>
                    @else
                        @foreach ($sortir as $s)
                            <tr>
                                <td><b>SORTIR</b></td>
                                <td>{{ $s->name }}</td>
                                <td>{{ $s->nama }}</td>
                                <td>{{ date('d M y', strtotime($s->tgl)) }}</td>
                                <td align="right">{{ $s->pcs_awal }}</td>
                                <td align="right">0</td>
                                <td align="right">0</td>
                                <td align="right">{{ $s->pcs_awal }}</td>
                                <td align="right">{{ number_format($s->rp_target, 0) }}</td>
                            </tr>
                            <tr>
                                <td><b>TERIMA</b></td>
                                <td>{{ $s->name }}</td>
                                <td>{{ $s->nama }}</td>
                                <td>{{ date('d M y', strtotime($s->tgl)) }}</td>
                                <td align="right">{{ $s->pcs_akhir }}</td>
                                <td align="right">0</td>
                                <td align="right">0</td>
                                <td align="right">{{ $s->pcs_akhir }}</td>
                                <td align="right">{{ number_format($s->ttl_rp, 0) }}</td>
                            </tr>
                        @endforeach
                    @endif

                    <tr>
                        <td colspan="9" class="border-hilang">&nbsp;</td>
                    </tr>
                    <tr>
                        <th class="dhead">TIPE</th>
                        <th class="dhead">PGWS</th>
                        <th class="dhead">NAMA</th>
                        <th class="dhead">TGL TERIMA</th>
                        <th class="text-end dhead">PCS AWAL</th>
                        <th class="text-end dhead">PCS HCR</th>
                        <th class="text-end dhead">PCS CU</th>
                        <th class="text-end dhead">PCS TTL</th>
                        <th class="text-end dhead">TTL RP</th>
                    </tr>
                    @if (empty($grade))
                        <tr>
                            <td><b>GRADE</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td align="right"></td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right"></td>
                        </tr>
                    @else
                        @foreach ($grade as $g)
                            <tr>
                                <td><b>GRADE</b></td>
                                <td>{{ $g->name }}</td>
                                <td>{{ $g->nama }}</td>
                                <td>{{ date('d M y', strtotime($g->tgl)) }}</td>
                                <td align="right">{{ $g->pcs_awal }}</td>
                                <td align="right">0</td>
                                <td align="right">0</td>
                                <td align="right">{{ $g->pcs_awal }}</td>
                                <td align="right"></td>
                            </tr>
                        @endforeach
                    @endif


                </tbody>
            </table>

        </div>
        <div class="col-lg-4">
            <table class="table small table-bordered">
                <thead>
                    {{-- tipe bk --}}
                    <tr>
                        <th class="dhead text-end">GR AWAL</th>
                        <th class="dhead text-end">GR HCR</th>
                        <th class="dhead text-end">GR FLX</th>
                        <th class="dhead text-end">GR TTL</th>
                        <th class="dhead text-end">TTD PGWS</th>
                        <th class="dhead text-end">SUSUT</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        {{-- gr --}}
                        <td align="right">{{ number_format($detail->gr_awal, 0) }}</td>
                        <td align="right">0</td>
                        <td align="right">0</td>
                        <td align="right">0</td>
                        <td align="right">0</td>
                        <td align="right">0</td>

                    </tr>
                    <tr>
                        <td colspan="6" class="border-hilang">&nbsp;</td>
                    </tr>
                    @if (empty($cabut))
                        <tr>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right"></td>
                            <td align="right">0</td>
                        </tr>
                        <tr>
                            {{-- gr cabut --}}
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right"></td>
                            <td align="right">0</td>
                        </tr>
                    @else
                        @foreach ($cabut as $c)
                            <tr>
                                <td align="right">{{ number_format($c->gr_awal, 0) }}</td>
                                <td align="right">0</td>
                                <td align="right">0</td>
                                <td align="right">{{ number_format($c->gr_awal, 0) }}</td>
                                <td align="right"></td>
                                <td align="right">0</td>
                            </tr>
                            <tr>
                                {{-- gr cabut --}}
                                <td align="right">{{ number_format($c->gr_akhir, 0) }}</td>
                                <td align="right">0</td>
                                <td align="right">{{ number_format($c->gr_flx, 0) }}</td>
                                <td align="right">{{ number_format($c->gr_akhir, 0) }}</td>
                                <td align="right"></td>
                                <td align="right">{{ number_format((1 - ($c->gr_flx + $c->gr_akhir) / $c->gr_awal) * 100, 0) }}%</td>
                            </tr>
                        @endforeach
                    @endif



                    <tr>
                        <td colspan="6" class="border-hilang">&nbsp;</td>
                    </tr>
                    <tr>
                        <th class="dhead text-end">GR DICTK</th>
                        <th class="text-end dhead">GR HCR</th>
                        <th class="text-end dhead">GR TDK DICTK</th>
                        <th class="text-end dhead">GR TTL</th>
                        <th class="text-end dhead">TTD PGWS</th>
                        <th class="text-end dhead">SUSUT</th>
                    </tr>
                    @if (empty($cetak))
                        <tr>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right"></td>
                            <td align="right">0</td>
                        </tr>
                        <tr>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right"></td>
                            <td align="right">0</td>
                        </tr>
                    @else
                        @foreach ($cetak as $c)
                            <tr>
                                <td align="right">{{ $c->gr_awal }}</td>
                                <td align="right">0</td>
                                <td align="right">0</td>
                                <td align="right">{{ $c->gr_awal }}</td>
                                <td align="right"></td>
                                <td align="right">0</td>
                            </tr>
                            <tr>
                                <td align="right">{{ $c->gr_akhir }}</td>
                                <td align="right">0</td>
                                <td align="right">{{ $c->gr_tidak_ctk }}</td>
                                <td align="right">{{ $c->gr_akhir }}</td>
                                <td align="right"></td>
                                <td align="right">{{ number_format((1 - $c->gr_akhir / $c->gr_awal) * 100, 0) }}%</td>
                            </tr>
                        @endforeach
                    @endif


                    <tr>
                        <td colspan="6" class="border-hilang">&nbsp;</td>
                    </tr>
                    <tr>
                        <th class="dhead text-end">GR SORTIR</th>
                        <th class="text-end dhead">GR HCR</th>
                        <th class="text-end dhead">GR TDK SORTIR</th>
                        <th class="text-end dhead">GR TTL</th>
                        <th class="text-end dhead">TTD PGWS</th>
                        <th class="text-end dhead">SUSUT</th>
                    </tr>
                    @if (empty($sortir))
                        <tr>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right"></td>
                            <td align="right">0</td>
                        </tr>
                        <tr>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right"></td>
                            <td align="right">0</td>
                        </tr>
                    @else
                        @foreach ($sortir as $s)
                            <tr>
                                <td align="right">{{ $s->gr_awal }}</td>
                                <td align="right">0</td>
                                <td align="right">0</td>
                                <td align="right">{{ $s->gr_awal }}</td>
                                <td align="right"></td>
                                <td align="right">0</td>
                            </tr>
                            <tr>
                                <td align="right">{{ $s->gr_akhir }}</td>
                                <td align="right">0</td>
                                <td align="right">0</td>
                                <td align="right">{{ $s->gr_akhir }}</td>
                                <td align="right"></td>
                                <td align="right">{{ number_format((1 - $s->gr_akhir / $s->gr_awal) * 100, 0) }}%</td>
                            </tr>
                        @endforeach

                    @endif

                    <tr>
                        <td colspan="6" class="border-hilang">&nbsp;</td>
                    </tr>


                    <tr>
                        <th class="dhead text-end">GR AWAL</th>
                        <th class="text-end dhead">GR HCR</th>
                        <th class="text-end dhead">GR CU</th>
                        <th class="text-end dhead">GR TTL</th>
                        <th class="text-end dhead">TTD PGWS</th>
                        <th class="text-end dhead">SST GLOBAL</th>
                    </tr>
                    @if (empty($grade))
                        <tr>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right">0</td>
                            <td align="right"></td>
                            <td align="right">0</td>
                        </tr>
                    @else
                        @foreach ($grade as $g)
                            <tr>
                                <td align="right">{{ $g->gr_awal }}</td>
                                <td align="right">0</td>
                                <td align="right">0</td>
                                <td align="right">{{ $g->gr_awal }}</td>
                                <td align="right"></td>
                                <td align="right">0</td>
                            </tr>
                        @endforeach
                    @endif



                </tbody>
            </table>
        </div>
        <div class="col-lg-1">
            <table class="table table-bordered small">
                <thead>
                    <tr>
                        <th class="dhead text-center">EO BERSIH</th>
                        <th class="dhead text-center">RONTOKAN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td align="right">0</td>
                        <td align="right">0</td>
                    </tr>
                    <tr>
                        <th class="dhead text-center">FLX</th>
                        <th class="dhead text-center">RMBGN</th>
                    </tr>
                    <tr>
                        <td align="right">0</td>
                        <td align="right">0</td>
                    </tr>
                    <tr>
                        <th class="dhead text-center">SSTN</th>
                        <th class="dhead text-center">BULU</th>
                    </tr>
                    <tr>
                        <td align="right">0</td>
                        <td align="right">0</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <div class="row">
        <div class="col-lg-2">
            <table class="table small table-borderless">
                <tr>
                    <th>&nbsp;</th>
                </tr>
                <tr>
                    <td align="right"><b>Bentuk</b></td>
                </tr>
            </table>
        </div>
        <div class="col-lg-2">
            <table class="table small table-bordered">

                <tbody>
                    <tr>

                        <th class="dhead">GRADE</th>
                        <th class="dhead text-end">PCS</th>
                        <th class="dhead text-end">GR</th>
                    </tr>
                    @php
                        $total_pcs_bentuk = 0;
                        $total_gram_bentuk = 0;
                    @endphp
                    @foreach ($grading_bentuk as $g)
                        @php
                            $total_pcs_bentuk += $g->pcs;
                            $total_gram_bentuk += $g->gram;
                        @endphp
                        <tr>
                            <td>{{ $g->tipe }}</td>
                            <td align="right">{{ $g->pcs }}</td>
                            <td align="right">{{ $g->gram }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <th class="text-center">Total</th>
                        <th class="text-end">{{ $total_pcs_bentuk }}</th>
                        <th class="text-end">{{ $total_gram_bentuk }}</th>
                    </tr>
                </tbody>

            </table>
        </div>

        <div class="col-lg-1">
            <table class="table small table-borderless">
                <tr>
                    <th>&nbsp;</th>
                </tr>
                <tr>
                    <td align="right"><b>DR/PTH</b></td>
                </tr>
                <tr>
                    <td align="right"><b>TURUN GRADE</b></td>
                </tr>

            </table>
        </div>
        <div class="col-lg-2">
            <table class="table small table-bordered">
                <thead>
                    <tr>
                        <th class="dhead">GRADE</th>
                        <th class="dhead text-end">PCS</th>
                        <th class="dhead text-end">GR</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total_pcs_turun = 0;
                        $total_gram_turun = 0;
                    @endphp
                    @foreach ($grading_turun as $g)
                        @php
                            $total_pcs_turun += $g->pcs;
                            $total_gram_turun = $g->gram;
                        @endphp
                        <tr>
                            <td>{{ $g->tipe }}</td>
                            <td align="right">{{ $g->pcs }}</td>
                            <td align="right">{{ $g->gram }}</td>
                        </tr>
                    @endforeach
                    <tr>

                        <th class="text-center">Total</th>
                        <th class="text-end">{{ $total_pcs_turun }}</th>
                        <th class="text-end">{{ $total_gram_turun }}</th>
                    </tr>

                </tbody>

            </table>


            <table class="table small table-bordered">
                <thead>
                    <tr>
                        <th class="dhead">GRADE</th>
                        <th class="dhead text-end">PCS</th>
                        <th class="dhead text-end">GR</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>&nbsp;</td>
                        <td align="right">&nbsp;</td>
                        <td align="right">&nbsp;</td>
                    </tr>
                    <tr>
                        <th class="text-center">Total</th>
                        <th class="text-end">0</th>
                        <th class="text-end">0</th>
                    </tr>
                </tbody>
            </table>

        </div>
        <div class="col-lg-3">
            <table class="table small table-bordered">
                <tr>
                    <th class="dhead">Qc-1</th>
                    <th class="dhead">(SESUDAH CTK)</th>
                </tr>
                <tr>
                    <td style="height: 65px">TTD</td>
                    <td>ket:</td>
                </tr>
                <tr>
                    <td colspan="2" class="border-hilang"></td>
                </tr>
                <tr>
                    <th class="dhead">Qc-2</th>
                    <th class="dhead">(SESUDAH GRADE)</th>
                </tr>
                <tr>
                    <td style="height: 65px">TTD</td>
                    <td>ket:</td>
                </tr>
                <tr>
                    <td colspan="2" class="border-hilang"></td>
                </tr>
                <tr>
                    <th class="dhead" colspan="2">TICK APABILA SELESAI √</th>
                </tr>
                <tr>
                    <td style="height: 65px" colspan="2"></td>
                </tr>
            </table>

        </div>

    </div>
</x-theme.app>
