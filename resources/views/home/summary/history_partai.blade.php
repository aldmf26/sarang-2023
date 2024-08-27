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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>History Partai</title>
</head>


<body>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #787878;
        }

        .bg_biru {
            background-color: #87cefa !important;
        }
    </style>
    <br>
    <div class="container-fluid">
        <div class="row">
            @include('home.summary.nav_box');

            <div class="col-lg-6">
                <table class="table ">
                    <tr>
                        <th>Partai</th>
                        <th>tanggal mulai kerja</th>
                        <th>tanggal selesai kirim</th>
                    </tr>
                    <tr>
                        <td>{{ $nm_partai }}</td>
                        <td>{{ empty($cabut->tgl) ? '-' : tanggal($cabut->tgl) }}</td>
                        <td>{{ empty($grading->tgl) ? '-' : tanggal($grading->tgl) }}</td>
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
                        <th colspan="15" class="boder_bottom">Bk</th>
                    </tr>
                    <tr>
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
                        <th class="text-end boder_bottom border_top border_right"></th>
                        <th class="text-end boder_bottom border_top">pcs sisa</th>
                        <th class="text-end boder_bottom border_top">gr sisa</th>
                        <th class="text-end boder_bottom border_top">rp/gr</th>
                        <th class="text-end boder_bottom border_top">total rp</th>
                    </tr>
                    <tr>
                        <td class="text-end boder_bottom">{{ number_format($bk->pcs, 0) }}</td>
                        <td class="text-end boder_bottom border_right border_top">{{ number_format($bk->gr) }}</td>
                        <td class="text-end boder_bottom">0</td>
                        <td class="text-end boder_bottom border_right ">0</td>
                        <td class="text-end boder_bottom border_right ">0</td>
                        <td class="text-end boder_bottom border_right bg_biru">{{ number_format($bk->ttl_rp, 0) }}
                        </td>
                        <td class="text-end boder_bottom">{{ number_format($bk->ttl_rp / $bk->gr, 0) }}</td>
                        <td class="text-end boder_bottom"></td>
                        <td class="text-end boder_bottom"></td>
                        <td class="text-end boder_bottom"></td>
                        <td class="text-end boder_bottom border_right"></td>
                        <td class="text-end boder_bottom">{{ number_format($bk_sisa->pcs ?? 0, 0) }}</td>
                        <td class="text-end boder_bottom">{{ number_format($bk_sisa->gr ?? 0, 0) }}</td>
                        <td class="text-end boder_bottom">
                            {{ empty($bk_sisa->ttl_rp) ? 0 : number_format($bk_sisa->ttl_rp / $bk_sisa->gr, 0) }}</td>
                        <td class="text-end boder_bottom ">{{ number_format($bk_sisa->ttl_rp ?? 0, 0) }}</td>
                    </tr>

                    <tr>
                        <th colspan="15" class="boder_bottom">cabut</th>
                    </tr>
                    <tr>
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
                        <th class="text-end boder_bottom border_top border_right">rp/gr</th>
                        <th class="text-end boder_bottom ">pcs sisa</th>
                        <th class="text-end boder_bottom ">gr sisa</th>
                        <th class="text-end boder_bottom ">rp/gr</th>
                        <th class="text-end boder_bottom ">total rp</th>
                    </tr>
                    <tr>
                        <td class="text-end boder_bottom">{{ number_format($cabut->pcs ?? 0, 0) }}</td>
                        <td class="text-end border_right boder_bottom">{{ number_format($cabut->gr ?? 0, 0) }}</td>
                        <td class="text-end boder_bottom">{{ number_format($cabut->pcs_akhir ?? 0, 0) }}</td>
                        <td class="text-end border_right boder_bottom">{{ number_format($cabut->gr_akhir ?? 0, 0) }}
                        </td>
                        <td class="text-end border_right boder_bottom">
                            {{ empty($cabut->gr_akhir) ? 0 : number_format((1 - $cabut->gr_akhir / $cabut->gr) * 100, 1) }}
                            %</td>
                        <td class="text-end border_right boder_bottom ">{{ number_format($cabut->cost_bk ?? 0, 0) }}
                        </td>
                        <td class="text-end boder_bottom">{{ number_format($cabut->cost_cabut ?? 0, 0) }}</td>
                        <td class="text-end boder_bottom">
                            {{ empty($cabut->gr_akhir) ? 0 : number_format(1087.362885977 * $cabut->gr_akhir, 0) }}
                        </td>
                        <td class="text-end boder_bottom">
                            {{ empty($cabut->gr_akhir) ? 0 : number_format(124.36093427769 * $cabut->gr_akhir, 0) }}
                        </td>
                        @php
                            $ttl_rp_cabut =
                                $cabut->cost_bk +
                                $cabut->cost_cabut +
                                1087.362885977 * $cabut->gr_akhir +
                                124.36093427769 * $cabut->gr_akhir;
                        @endphp
                        <td class="text-end boder_bottom bg_biru">{{ number_format($ttl_rp_cabut, 0) }}</td>
                        <td class="text-end border_right boder_bottom">
                            {{ number_format($ttl_rp_cabut / $cabut->gr_akhir, 0) }}</td>

                        <td class="text-end boder_bottom">{{ number_format($cabut_sisa->pcs ?? 0, 0) }}</td>
                        <td class="text-end boder_bottom">{{ number_format($cabut_sisa->gr ?? 0, 0) }}</td>
                        <td class="text-end boder_bottom">
                            {{ empty($cabut_sisa->gr) ? 0 : number_format($cabut_sisa->cost_bk / $cabut_sisa->gr, 0) }}
                        </td>
                        <td class="text-end boder_bottom">{{ number_format($cabut_sisa->cost_bk ?? 0, 0) }}
                        </td>


                    </tr>
                    <tr>
                        <th colspan="15" class="boder_bottom">cetak</th>
                    </tr>
                    <tr>
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
                        <th class="text-end boder_bottom border_top border_right">rp/gr</th>
                        <th class="text-end boder_bottom ">pcs sisa</th>
                        <th class="text-end boder_bottom ">gr sisa</th>
                        <th class="text-end boder_bottom ">rp/gr</th>
                        <th class="text-end boder_bottom ">total rp</th>
                    </tr>
                    <tr>
                        <td class="text-end boder_bottom">{{ number_format($cetak->pcs ?? 0, 0) }}</td>
                        @php
                            $gr_awal_cetak = $cetak->gr ?? 0;
                            $gr_akhir_cetak = $cetak->gr_akhir ?? 0;
                            $gr_awal_cbt_n_pcs = $cbt_tanpa_pcs->gr_akhir ?? 0;

                            $ttl_rp_cabutn_pcs =
                                $cbt_tanpa_pcs->cost_bk +
                                $cbt_tanpa_pcs->cost_cabut +
                                1087.362885977 * $cbt_tanpa_pcs->gr_akhir +
                                124.36093427769 * $cbt_tanpa_pcs->gr_akhir;
                        @endphp
                        <td class="text-end border_right boder_bottom">
                            {{ number_format($gr_awal_cetak + $gr_awal_cbt_n_pcs, 0) }}</td>
                        <td class="text-end boder_bottom">{{ number_format($cetak->pcs_akhir ?? 0, 0) }}</td>
                        <td class="text-end border_right boder_bottom">
                            {{ number_format($gr_akhir_cetak + $gr_awal_cbt_n_pcs, 0) }}
                        </td>
                        <td class="text-end border_right boder_bottom">
                            {{ empty($cetak->gr_akhir) ? 0 : number_format((1 - $cetak->gr_akhir / $cetak->gr) * 100, 1) }}
                            %</td>
                        @php
                            $ttl_rp_cabut_ke_cetak = empty($cetak->cost_bk)
                                ? 0
                                : $cetak->cost_bk +
                                    $cetak->cost_cbt +
                                    1087.362885977 * $cetak->gr_akhir_cbt +
                                    124.36093427769 * $cetak->gr_akhir_cbt;
                        @endphp
                        <td class="text-end border_right boder_bottom">
                            {{ number_format($ttl_rp_cabut_ke_cetak + $ttl_rp_cabutn_pcs, 0) }}
                        </td>
                        <td class="text-end boder_bottom">{{ number_format($cetak->cost_ctk ?? 0, 0) }}</td>
                        <td class="text-end boder_bottom">
                            {{ empty($cetak->gr_akhir) ? 0 : number_format(1087.362885977 * $cetak->gr_akhir, 0) }}
                        </td>
                        <td class="text-end boder_bottom">
                            {{ empty($cetak->gr_akhir) ? 0 : number_format(124.36093427769 * $cetak->gr_akhir, 0) }}
                        </td>
                        @php
                            $ttl_rp_ctk = empty($cetak->cost_ctk)
                                ? 0
                                : $ttl_rp_cabut_ke_cetak +
                                    $cetak->cost_ctk +
                                    1087.362885977 * ($cetak->gr_akhir + $cetak->gr_td_ctk) +
                                    124.36093427769 * ($cetak->gr_akhir + $cetak->gr_td_ctk);

                            $ttl_rp_sisa_ctk = empty($cetak_sisa->cost_bk)
                                ? 0
                                : $cetak_sisa->cost_bk +
                                    $cetak_sisa->cost_cbt +
                                    1087.362885977 * $cetak_sisa->gr +
                                    124.36093427769 * $cetak_sisa->gr;
                        @endphp
                        <td class="text-end boder_bottom bg_biru">
                            {{ number_format($ttl_rp_ctk + $ttl_rp_cabutn_pcs, 0) }}
                        </td>
                        <td class="text-end border_right boder_bottom">
                            {{ number_format(($ttl_rp_ctk + $ttl_rp_cabutn_pcs) / ($gr_akhir_cetak + $gr_awal_cbt_n_pcs), 0) }}
                        </td>

                        <td class="text-end boder_bottom">{{ number_format($cetak_sisa->pcs ?? 0, 0) }}</td>
                        <td class="text-end boder_bottom">{{ number_format($cetak_sisa->gr ?? 0, 0) }}</td>


                        <td class="text-end boder_bottom">
                            {{ empty($cetak_sisa->gr) ? 0 : number_format($ttl_rp_sisa_ctk / $cetak_sisa->gr, 0) }}
                        </td>
                        <td class="text-end boder_bottom">{{ number_format($ttl_rp_sisa_ctk ?? 0, 0) }}</td>


                    </tr>
                    <tr>
                        <th colspan="15" class="boder_bottom">sortir</th>
                    </tr>
                    <tr>
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
                        <th class="text-end boder_bottom border_top border_right">rp/gr</th>
                        <th class="text-end boder_bottom ">pcs sisa</th>
                        <th class="text-end boder_bottom ">gr sisa</th>
                        <th class="text-end boder_bottom ">rp/gr</th>
                        <th class="text-end boder_bottom ">total rp</th>
                    </tr>
                    <tr>
                        <td class="text-end boder_bottom">{{ number_format($sortir->pcs ?? 0, 0) }}</td>
                        <td class="text-end border_right boder_bottom">{{ number_format($sortir->gr ?? 0, 0) }}</td>
                        <td class="text-end boder_bottom">{{ number_format($sortir->pcs_akhir ?? 0, 0) }}</td>
                        <td class="text-end border_right boder_bottom">{{ number_format($sortir->gr_akhir ?? 0, 0) }}
                        </td>
                        <td class="text-end border_right boder_bottom">
                            {{ empty($sortir->gr_akhir) ? 0 : number_format((1 - $sortir->gr_akhir / $sortir->gr) * 100, 1) }}
                            %</td>
                        @php
                            $ttl_rp_cetak_ke_sortir = empty($sortir->cost_bk)
                                ? 0
                                : $sortir->cost_bk +
                                    $sortir->cost_cabut +
                                    $sortir->cost_ctk +
                                    1087.362885977 * $sortir->gr_akhir_ctk +
                                    124.36093427769 * $sortir->gr_akhir_ctk +
                                    1087.362885977 * $sortir->gr_akhir_cbt +
                                    124.36093427769 * $sortir->gr_akhir_cbt;
                        @endphp
                        <td class="text-end border_right boder_bottom">{{ number_format($ttl_rp_cetak_ke_sortir, 0) }}

                        </td>
                        <td class="text-end boder_bottom">{{ number_format($sortir->cost_sortir ?? 0, 0) }}</td>
                        <td class="text-end boder_bottom">
                            {{ empty($sortir->gr_akhir) ? 0 : number_format(1087.362885977 * $sortir->gr_akhir, 0) }}
                        </td>
                        <td class="text-end boder_bottom">
                            {{ empty($sortir->gr_akhir) ? 0 : number_format(124.36093427769 * $sortir->gr_akhir, 0) }}
                        </td>
                        @php
                            $ttl_rp_sortir = empty($sortir->cost_sortir)
                                ? 0
                                : $ttl_rp_cetak_ke_sortir +
                                    $sortir->cost_sortir +
                                    1087.362885977 * $sortir->gr_akhir +
                                    124.36093427769 * $sortir->gr_akhir;

                            $ttl_rp_sisa_sortir = empty($sortir_sisa->cost_bk)
                                ? 0
                                : $sortir_sisa->cost_bk +
                                    $sortir_sisa->cost_cabut +
                                    $sortir_sisa->cost_ctk +
                                    1087.362885977 * $sortir_sisa->gr_akhir_ctk +
                                    124.36093427769 * $sortir_sisa->gr_akhir_ctk +
                                    1087.362885977 * $sortir_sisa->gr_akhir_cbt +
                                    124.36093427769 * $sortir_sisa->gr_akhir_cbt;
                        @endphp
                        <td class="text-end boder_bottom bg_biru">{{ number_format($ttl_rp_sortir, 0) }}</td>
                        <td class="text-end border_right boder_bottom">
                            {{ empty($sortir->gr_akhir) ? 0 : number_format($ttl_rp_sortir / $sortir->gr_akhir, 0) }}
                        </td>

                        <td class="text-end boder_bottom">{{ number_format($sortir_sisa->pcs ?? 0, 0) }}</td>
                        <td class="text-end boder_bottom">{{ number_format($sortir_sisa->gr ?? 0, 0) }}</td>


                        <td class="text-end boder_bottom">
                            {{ empty($sortir_sisa->gr) ? 0 : number_format($ttl_rp_sisa_sortir / $sortir_sisa->gr, 0) }}
                        </td>
                        <td class="text-end boder_bottom">{{ number_format($ttl_rp_sisa_sortir ?? 0, 0) }}</td>


                    </tr>
                    <tr>
                        <th colspan="15" class="boder_bottom">Grading</th>
                    </tr>
                    <tr>
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
                        <th class="text-end boder_bottom border_top border_right">rp/gr</th>
                        <th class="text-end boder_bottom ">pcs sisa</th>
                        <th class="text-end boder_bottom ">gr sisa</th>
                        <th class="text-end boder_bottom ">rp/gr</th>
                        <th class="text-end boder_bottom ">total rp</th>
                    </tr>
                    <tr>
                        <td class="text-end boder_bottom">{{ number_format($grading->pcs ?? 0, 0) }}</td>
                        <td class="text-end border_right boder_bottom">{{ number_format($grading->gr ?? 0, 0) }}</td>
                        <td class="text-end boder_bottom">{{ number_format($grading->pcs_akhir ?? 0, 0) }}</td>
                        <td class="text-end border_right boder_bottom">{{ number_format($grading->gr_akhir ?? 0, 0) }}
                        </td>
                        <td class="text-end border_right boder_bottom">
                            {{ empty($grading->gr_akhir) ? 0 : number_format((1 - $grading->gr_akhir / $grading->gr) * 100, 1) }}
                            %</td>
                        @php
                            $ttl_rp_cetak_ke_sortir = empty($sortir->cost_bk)
                                ? 0
                                : $sortir->cost_bk +
                                    $sortir->cost_cabut +
                                    $sortir->cost_ctk +
                                    1087.362885977 * $sortir->gr_akhir_ctk +
                                    124.36093427769 * $sortir->gr_akhir_ctk +
                                    1087.362885977 * $sortir->gr_akhir_cbt +
                                    124.36093427769 * $sortir->gr_akhir_cbt;
                        @endphp
                        <td class="text-end border_right boder_bottom">{{ number_format($ttl_rp_sortir, 0) }}

                        </td>
                        <td class="text-end boder_bottom">0</td>
                        <td class="text-end boder_bottom">
                            {{ empty($grading->gr_akhir) ? 0 : number_format(1087.362885977 * $grading->gr_akhir, 0) }}
                        </td>
                        <td class="text-end boder_bottom">
                            {{ empty($grading->gr_akhir) ? 0 : number_format(124.36093427769 * $grading->gr_akhir, 0) }}
                        </td>
                        @php
                            $ttl_rp_grading =
                                $ttl_rp_sortir +
                                1087.362885977 * $grading->gr_akhir +
                                124.36093427769 * $grading->gr_akhir;

                        @endphp
                        <td class="text-end boder_bottom bg_biru">{{ number_format($ttl_rp_grading, 0) }}</td>
                        <td class="text-end border_right boder_bottom">
                            {{ empty($grading->gr_akhir) ? 0 : number_format($ttl_rp_grading / $grading->gr_akhir, 0) }}
                        </td>

                        <td class="text-end boder_bottom">0</td>
                        <td class="text-end boder_bottom">0</td>


                        <td class="text-end boder_bottom">
                            0
                        </td>
                        <td class="text-end boder_bottom">0</td>


                    </tr>


                </table>
            </div>

        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script></script>
</body>

</html>
