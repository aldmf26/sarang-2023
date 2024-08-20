<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <title>History Partai</title>
</head>


<body>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #787878;
        }
    </style>
    <br>
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-6">
                <table class="table ">
                    <tr>
                        <th>Partai</th>
                        <th>No Box</th>
                        <th>tanggal mulai kerja</th>
                        <th>tanggal selesai kirim</th>
                    </tr>
                    <tr>
                        <td>{{ $bk->nm_partai }}</td>
                        <td>{{ $no_box }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div>
            <style>
                .border_right {
                    border-right: 1px solid black;
                }

                .boder_bottom {
                    border-bottom: 1px solid black;
                }

                .border_top {
                    border-top: 1px solid black;
                }
            </style>
            <div class="col-lg-12">
                <table class="table ">
                    <tr>
                        <th colspan="50" class="boder_bottom">Bk</th>
                    </tr>
                    <tr>
                        <th class="border_right boder_bottom border_top">pengawas</th>
                        <th class="boder_bottom border_right border_top" colspan="2">timbang ulang</th>
                        <th class="text-end boder_bottom border_top">pcs awal</th>
                        <th class="text-end boder_bottom border_right border_top">gr awal</th>
                        <th class="text-end boder_bottom border_top">pcs akhir</th>
                        <th class="text-end boder_bottom border_right border_top">gr akhir</th>
                        <th class="text-end boder_bottom border_right border_top">susut</th>
                        <th class="text-end boder_bottom border_right border_top">total rp</th>
                        <th class="text-end boder_bottom border_top">rp/gr</th>
                        <th class="text-end boder_bottom border_top"></th>
                        <th class="text-end boder_bottom border_top"></th>
                        <th class="text-end boder_bottom border_top"></th>
                        <th class="text-end boder_bottom border_top"></th>
                    </tr>
                    <tr>
                        <td class="border_right boder_bottom">{{ $bk->pengawas }}</td>
                        <td class="boder_bottom border_right" colspan="2">{{ $bk->name }}</td>
                        <td class="text-end boder_bottom">{{ $bk->pcs_awal }}</td>
                        <td class="text-end boder_bottom border_right">{{ $bk->gr_awal }}</td>
                        <td class="text-end boder_bottom">0</td>
                        <td class="text-end boder_bottom border_right">0</td>
                        <td class="text-end boder_bottom border_right">0</td>
                        <td class="text-end boder_bottom border_right">
                            {{ number_format($bk->hrga_satuan * $bk->gr_awal, 0) }}</td>
                        <td class="text-end boder_bottom">{{ number_format($bk->hrga_satuan, 0) }}</td>
                        <td class="boder_bottom"></td>
                        <td class="boder_bottom"></td>
                        <td class="boder_bottom"></td>
                        <td class="boder_bottom"></td>
                    </tr>
                    <tr>
                        <th colspan="50" class="boder_bottom">Cabut</th>
                    </tr>
                    <tr>
                        <th class="border_right boder_bottom border_top">pengawas</th>
                        <th class="boder_bottom border_right border_top" colspan="2">nama karyawan</th>
                        <th class="text-end boder_bottom border_top">pcs awal</th>
                        <th class="text-end boder_bottom border_right border_top">gr awal</th>
                        <th class="text-end boder_bottom border_top">pcs akhir</th>
                        <th class="text-end boder_bottom border_right border_top">gr akhir</th>
                        <th class="text-end boder_bottom border_right border_top">susut</th>
                        <th class="text-end boder_bottom border_right border_top">total rp</th>
                        <th class="text-end boder_bottom border_top">cost cabut</th>
                        <th class="text-end boder_bottom border_top">cost operasional</th>
                        <th class="text-end boder_bottom border_top">cost dll denda cu</th>
                        <th class="text-end boder_bottom border_top">total rp</th>
                        <th class="text-end boder_bottom border_top">rp/gr</th>
                    </tr>
                    <tr>
                        <td class="border_right boder_bottom">{{ $cabut->name }}</td>
                        <td class="boder_bottom border_right" colspan="2">{{ $cabut->nama }}</td>
                        <td class="text-end boder_bottom">{{ $cabut->pcs_awal }}</td>
                        <td class="text-end boder_bottom border_right">{{ $cabut->gr_awal }}</td>
                        <td class="text-end boder_bottom">{{ $cabut->pcs_akhir }}</td>
                        <td class="text-end boder_bottom border_right">{{ $cabut->gr_akhir }}</td>
                        <td class="text-end boder_bottom border_right">
                            {{ number_format((1 - $cabut->gr_akhir / $cabut->gr_awal) * 100, 1) }} %</td>
                        <td class="text-end boder_bottom border_right">
                            {{ number_format($cabut->cost_bk, 0) }}</td>
                        <td class="text-end boder_bottom">{{ number_format($cabut->ttl_rp, 0) }}</td>
                        <td class="text-end boder_bottom">{{ number_format(1087.362885977 * $cabut->gr_akhir, 0) }}
                        <td class="text-end boder_bottom">{{ number_format(124.36093427769 * $cabut->gr_akhir, 0) }}
                        </td>
                        @php
                            $ttl_rp_cabut =
                                $cabut->cost_bk +
                                $cabut->ttl_rp +
                                1087.362885977 * $cabut->gr_akhir +
                                124.36093427769 * $cabut->gr_akhir;
                        @endphp
                        <td class="text-end boder_bottom">
                            {{ number_format($ttl_rp_cabut, 0) }}
                        </td>
                        <td class="boder_bottom text-end">{{ number_format($ttl_rp_cabut / $cabut->gr_akhir, 0) }}
                        </td>
                    </tr>
                    <tr>
                        <th colspan="50" class="boder_bottom">Cetak</th>
                    </tr>
                    <tr>
                        <th class="border_right boder_bottom border_top">serah</th>
                        <th class="boder_bottom border_right border_top">terima</th>
                        <th class="boder_bottom border_right border_top">nama karyawan</th>
                        <th class="text-end boder_bottom border_top">pcs awal</th>
                        <th class="text-end boder_bottom border_right border_top">gr awal</th>
                        <th class="text-end boder_bottom border_top">pcs akhir</th>
                        <th class="text-end boder_bottom border_right border_top">gr akhir</th>
                        <th class="text-end boder_bottom border_right border_top">susut</th>
                        <th class="text-end boder_bottom border_right border_top">total rp</th>
                        <th class="text-end boder_bottom border_top">cost cetak</th>
                        <th class="text-end boder_bottom border_top">cost operasional</th>
                        <th class="text-end boder_bottom border_top">cost dll denda cu</th>
                        <th class="text-end boder_bottom border_top">total rp</th>
                        <th class="text-end boder_bottom border_top">rp/gr</th>
                    </tr>
                    <tr>
                        <td class="border_right boder_bottom">{{ $cetak->nm_serah }}</td>
                        <td class="boder_bottom border_right">{{ $cetak->nm_terima }}</td>
                        <td class="boder_bottom border_right">{{ $cetak->nm_anak }}</td>
                        <td class="text-end boder_bottom">{{ $cetak->pcs_awal_ctk }}</td>
                        <td class="text-end boder_bottom border_right">{{ $cetak->gr_awal_ctk }}</td>
                        <td class="text-end boder_bottom">{{ $cetak->pcs_akhir + $cetak->pcs_tdk_cetak }}</td>
                        <td class="text-end boder_bottom border_right">{{ $cetak->gr_akhir + $cetak->gr_tdk_cetak }}
                        </td>
                        <td class="text-end boder_bottom border_right">
                            {{ number_format((1 - ($cetak->gr_akhir + $cetak->gr_tdk_cetak) / $cabut->gr_awal) * 100, 1) }}
                            %</td>
                        <td class="text-end boder_bottom border_right">
                            {{ number_format($ttl_rp_cabut, 0) }}</td>
                        <td class="text-end boder_bottom">{{ number_format($cetak->cost_ctk, 0) }}</td>
                        <td class="text-end boder_bottom">
                            {{ number_format(1087.362885977 * ($cetak->gr_akhir + $cetak->gr_tdk_cetak), 0) }}
                        <td class="text-end boder_bottom">
                            {{ number_format(124.36093427769 * ($cetak->gr_akhir + $cetak->gr_tdk_cetak), 0) }}
                        </td>
                        @php
                            $ttl_rp_cetak =
                                $ttl_rp_cabut +
                                $cetak->cost_ctk +
                                1087.362885977 * ($cetak->gr_akhir + $cetak->gr_tdk_cetak) +
                                124.36093427769 * ($cetak->gr_akhir + $cetak->gr_tdk_cetak);
                        @endphp
                        <td class="text-end boder_bottom">
                            {{ number_format($ttl_rp_cetak, 0) }}
                        </td>
                        <td class="boder_bottom text-end">{{ number_format($ttl_rp_cetak / $cabut->gr_akhir, 0) }}
                        </td>
                    </tr>
                    <tr>
                        <th colspan="50" class="boder_bottom">Sortir</th>
                    </tr>
                    <tr>
                        <th class="border_right boder_bottom border_top">serah</th>
                        <th class="boder_bottom border_right border_top">terima</th>
                        <th class="boder_bottom border_right border_top">nama karyawan</th>
                        <th class="text-end boder_bottom border_top">pcs awal</th>
                        <th class="text-end boder_bottom border_right border_top">gr awal</th>
                        <th class="text-end boder_bottom border_top">pcs akhir</th>
                        <th class="text-end boder_bottom border_right border_top">gr akhir</th>
                        <th class="text-end boder_bottom border_right border_top">susut</th>
                        <th class="text-end boder_bottom border_right border_top">total rp</th>
                        <th class="text-end boder_bottom border_top">cost sortir</th>
                        <th class="text-end boder_bottom border_top">cost operasional</th>
                        <th class="text-end boder_bottom border_top">cost dll denda cu</th>
                        <th class="text-end boder_bottom border_top">total rp</th>
                        <th class="text-end boder_bottom border_top">rp/gr</th>
                    </tr>
                    <tr>
                        <td class="border_right boder_bottom">{{ $sortir->nm_serah }}</td>
                        <td class="boder_bottom border_right">{{ $sortir->nm_terima }}</td>
                        <td class="boder_bottom border_right">{{ $sortir->nm_karyawan }}</td>
                        <td class="text-end boder_bottom">{{ $sortir->pcs_awal }}</td>
                        <td class="text-end boder_bottom border_right">{{ $sortir->gr_awal }}</td>
                        <td class="text-end boder_bottom">{{ $sortir->pcs_akhir }}</td>
                        <td class="text-end boder_bottom border_right">{{ $sortir->gr_akhir }}</td>
                        <td class="text-end boder_bottom border_right">
                            {{ number_format((1 - $sortir->gr_akhir / $sortir->gr_awal) * 100, 1) }} %</td>
                        <td class="text-end boder_bottom border_right">
                            {{ number_format($ttl_rp_cetak, 0) }}</td>
                        <td class="text-end boder_bottom">{{ number_format($sortir->ttl_rp, 0) }}</td>
                        <td class="text-end boder_bottom">{{ number_format(1087.362885977 * $sortir->gr_akhir, 0) }}
                        <td class="text-end boder_bottom">{{ number_format(124.36093427769 * $sortir->gr_akhir, 0) }}
                        </td>
                        @php
                            $ttl_rp_sortir =
                                $ttl_rp_cetak +
                                $sortir->ttl_rp +
                                1087.362885977 * $sortir->gr_akhir +
                                124.36093427769 * $sortir->gr_akhir;
                        @endphp
                        <td class="text-end boder_bottom">
                            {{ number_format($ttl_rp_sortir, 0) }}
                        </td>
                        <td class="boder_bottom text-end">{{ number_format($ttl_rp_sortir / $sortir->gr_akhir, 0) }}
                        </td>
                    </tr>
                </table>
            </div>

        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>
