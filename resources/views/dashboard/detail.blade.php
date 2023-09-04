<x-theme.app title="{{ $title }}" table="T" sizeCard="12" cont="container-fluid">
    <style>
        tbody,
        td {
            border: 0 solid;
            border-color: #787878;
        }

        .warna_head {
            border: 0 solid white !important;
            background-color: #435EBE !important;
            color: white;
        }

        .border-hilang {
            border-right: none !important;
            border-left: none !important;
            border-bottom: none !important;
            border-top: none !important
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

        <div class="col-lg-5">
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
                        <td>{{$detail->pengawas}}</td>
                        <td>{{$detail->penerima == '1' ? 'Jenah' : ($detail->penerima == '2' ? 'Nurul' :
                            'Erna')}}
                        </td>
                        <td>{{date('d M y',strtotime($detail->tgl))}}</td>
                        <td align="right">{{$detail->pcs_awal}}</td>
                        <td align="right">0</td>
                        <td align="right">0</td>
                        <td align="right">0</td>
                        <td align="right">0</td>


                    </tr>
                    <tr>
                        <td colspan="9" class="border-hilang">&nbsp;</td>
                    </tr>
                    <tr>
                        <td><b>CBT</b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td align="right"></td>
                        <td align="right"></td>
                        <td align="right"></td>
                        <td align="right"></td>
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
                    <tr>
                        <td><b>CTK</b></td>
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
                    <tr>
                        <td><b>GRADE</b></td>
                        <td>Siti Fatimah</td>
                        <td></td>
                        <td></td>
                        <td align="right"></td>
                        <td align="right">0</td>
                        <td align="right">0</td>
                        <td align="right">0</td>
                        <td align="right"></td>
                    </tr>

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
                        <td align="right">{{number_format($detail->gr_awal,0)}}</td>
                        <td align="right">0</td>
                        <td align="right">0</td>
                        <td align="right">0</td>
                        <td align="right">0</td>
                        <td align="right">0</td>

                    </tr>
                    <tr>
                        <td colspan="6" class="border-hilang">&nbsp;</td>
                    </tr>

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

                    <tr>
                        <td colspan="6" class="border-hilang">&nbsp;</td>
                    </tr>
                    <tr>
                        <th class="dhead text-end">GR DICTK</th>
                        <th class="text-end dhead">GR HCR</th>
                        <th class="text-end dhead">GR TDK SORTIR</th>
                        <th class="text-end dhead">GR TTL</th>
                        <th class="text-end dhead">TTD PGWS</th>
                        <th class="text-end dhead">SUSUT</th>
                    </tr>
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
                    <tr>
                        <td align="right">0</td>
                        <td align="right">0</td>
                        <td align="right">0</td>
                        <td align="right">0</td>
                        <td align="right"></td>
                        <td align="right">0</td>
                    </tr>


                </tbody>
            </table>
        </div>
        <div class="col-lg-2">
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
            {{-- <table class="table small table-borderless">
                <tr>
                    <th>&nbsp;</th>
                </tr>
                <tr>
                    <td align="right"><b>Bentuk</b></td>
                </tr>
            </table> --}}
        </div>
        <div class="col-lg-2">
            <table class="table-cutome">

                <tbody>
                    <tr>
                        <td align="right" class="border-hilang" rowspan="7"><b>Bentuk</b></td>
                        <th class="dhead">GRADE</th>
                        <th class="dhead text-end">PCS</th>
                        <th class="dhead text-end">GR</th>
                    </tr>
                    <tr>

                        <td>s6</td>
                        <td align="right">0</td>
                        <td align="right">0</td>

                    </tr>
                    <tr>
                        <td>s5</td>
                        <td align="right">0</td>
                        <td align="right">0</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td align="right">&nbsp;</td>
                        <td align="right">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td align="right">&nbsp;</td>
                        <td align="right">&nbsp;</td>
                    </tr>
                    <tr>

                        <td class="text-center">Total</td>
                        <td class="text-end">0</td>
                        <td class="text-end">0</td>
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
                    <tr>
                        <td>s6</td>
                        <td align="right">0</td>
                        <td align="right">0</td>

                    </tr>
                    <tr>
                        <td>s5</td>
                        <td align="right">0</td>
                        <td align="right">0</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td align="right">&nbsp;</td>
                        <td align="right">&nbsp;</td>
                    </tr>
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