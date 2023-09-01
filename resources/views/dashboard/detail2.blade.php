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

        .hilang_atas {
            border: none;
        }
    </style>
    <div class="row ">
        <div class="col-lg-2">
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
                    <td>{{ $detail->ket }}</td>
                </tr>
                <tr>
                    <th class="warna_head">Warna</th>
                    <td>{{ $detail->warna }}</td>
                </tr>

            </table>
        </div>

        <div class="col-lg-10">
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
                        <th rowspan="1" class="hilang_atas">&nbsp; sds</th>

                        <th class="dhead">GR AWAL</th>
                        <th class="dhead">GR HCR</th>
                        <th class="dhead">GR FLX</th>
                        <th class="dhead">GR TTL</th>
                        <th class="dhead">TTD PGWS</th>
                        <th class="dhead">SUSUT</th>
                        <td>&nbsp;</td>
                        <td colspan="2" class="hilang_atas" rowspan="1">&nbsp;</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><b>BK</b></td>
                        <td>sinta</td>
                        <td>Jenah</td>
                        <td>2 Agustus 2023</td>
                        <td align="right">180</td>
                        <td align="right">0</td>
                        <td align="right">0</td>
                        <td align="right">180</td>
                        <td align="right"></td>
                        <td>&nbsp;</td>
                        {{-- gr --}}
                        <td align="right">180</td>
                        <td align="right">180</td>
                        <td align="right">180</td>
                        <td align="right">180</td>
                        <td align="right"></td>
                        <td align="right">180</td>

                    </tr>
                    <tr>
                        <td colspan="16">&nbsp;</td>
                    </tr>

                    <tr>
                        <td><b>CBT</b></td>
                        <td>sinta</td>
                        <td>Jenah</td>
                        <td>2 Agustus 2023</td>
                        <td align="right">180</td>
                        <td align="right">0</td>
                        <td align="right">0</td>
                        <td align="right">180</td>
                        <td align="right" rowspan="2"></td>
                        <td rowspan="3">&nbsp;</td>

                        {{-- gr cabut --}}
                        <td align="right">180</td>
                        <td align="right">180</td>
                        <td align="right">180</td>
                        <td align="right">180</td>
                        <td align="right"></td>
                        <td align="right">180</td>
                        {{-- eo --}}
                        <th rowspan="2">&nbsp;</th>
                        <th class="dhead text-end">EO BERSIH</th>
                        <th class="dhead text-end">RONTOKAN</th>
                    </tr>
                    <tr>
                        <td><b>NILAI</b></td>
                        <td>Jenah</td>
                        <td>Halisah</td>
                        <td>2 Agustus 2023</td>
                        <td align="right">178</td>
                        <td align="right">2</td>
                        <td align="right">0</td>
                        <td align="right">180</td>

                        {{-- gr cabut --}}
                        <td align="right">180</td>
                        <td align="right">180</td>
                        <td align="right">180</td>
                        <td align="right">180</td>
                        <td align="right"></td>
                        <td align="right">180</td>

                        <td align="right"></td>
                        <td align="right"></td>
                    </tr>


                    <tr>
                        <td colspan="9">&nbsp;</td>
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
                        <td rowspan="2">&nbsp;</td>
                        <th class="dhead text-end">GR DICTK</th>
                        <th class="text-end dhead">GR HCR</th>
                        <th class="text-end dhead">GR TDK DICTK</th>
                        <th class="text-end dhead">GR TTL</th>
                        <th class="text-end dhead">TTD PGWS</th>
                        <th class="text-end dhead">SUSUT</th>
                        {{-- flx rambangan --}}
                        <th rowspan="2">&nbsp;</th>
                        <th class="dhead text-end">FLX</th>
                        <th class="dhead text-end">RMBGN</th>
                    </tr>
                    <tr>
                        <td><b>CTK</b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td align="right"></td>
                        <td align="right">0</td>
                        <td align="right">0</td>
                        <td align="right">180</td>
                        <td align="right"></td>
                        {{-- gr cetak --}}
                        <td align="right">180</td>
                        <td align="right">180</td>
                        <td align="right">180</td>
                        <td align="right">180</td>
                        <td align="right"></td>
                        <td align="right">180</td>

                        <td align="right">0</td>
                        <td align="right">0</td>
                    </tr>

                    <tr>
                        <td colspan="9">&nbsp;</td>
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
                        <td rowspan="2">&nbsp;</td>
                        <th class="dhead text-end">GR DICTK</th>
                        <th class="text-end dhead">GR HCR</th>
                        <th class="text-end dhead">GR TDK SORTIR</th>
                        <th class="text-end dhead">GR TTL</th>
                        <th class="text-end dhead">TTD PGWS</th>
                        <th class="text-end dhead">SUSUT</th>
                        {{-- flx rambangan --}}
                        <th rowspan="2">&nbsp;</th>
                        <th class="dhead text-end">SSTN</th>
                        <th class="dhead text-end">BULU</th>
                    </tr>
                    <tr>
                        <td><b>SORTIR</b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td align="right"></td>
                        <td align="right">0</td>
                        <td align="right">0</td>
                        <td align="right">180</td>
                        <td align="right"></td>
                        {{-- gr sortir --}}
                        <td align="right">180</td>
                        <td align="right">180</td>
                        <td align="right">180</td>
                        <td align="right">180</td>
                        <td align="right"></td>
                        <td align="right">180</td>

                        <td align="right">0</td>
                        <td align="right">0</td>
                    </tr>
                    <tr>
                        <td colspan="9">&nbsp;</td>
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
                        <td rowspan="2">&nbsp;</td>
                        <th class="dhead text-end">GR AWAL</th>
                        <th class="text-end dhead">GR HCR</th>
                        <th class="text-end dhead">GR CU</th>
                        <th class="text-end dhead">GR TTL</th>
                        <th class="text-end dhead">TTD PGWS</th>
                        <th class="text-end dhead">SST GLOBAL</th>
                    </tr>
                    <tr>
                        <td><b>GRADE</b></td>
                        <td>Siti Fatimah</td>
                        <td></td>
                        <td></td>
                        <td align="right"></td>
                        <td align="right">0</td>
                        <td align="right">0</td>
                        <td align="right">180</td>
                        <td align="right"></td>
                        {{-- gr grade --}}
                        <td align="right">180</td>
                        <td align="right">180</td>
                        <td align="right">180</td>
                        <td align="right">180</td>
                        <td align="right"></td>
                        <td align="right">180</td>
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
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-center">Total</th>
                        <th class="text-end">0</th>
                        <th class="text-end">0</th>
                    </tr>
                </tfoot>
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

                    <tr>
                        <td colspan="3">&nbsp;</td>
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

            <p><b>CU</b></p>
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
</x-theme.app>