<?php
header('Content-type: application/vnd-ms-excel');
header('Content-Disposition: attachment; filename=Cost Partai.xls');
?>
<div class="row">
    <div class="col-lg-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="border-left: 1px solid black;border-bottom: 1px solid black"></th>
                    <th style="border-bottom: 1px solid black"></th>
                    <th style="border-bottom: 1px solid black"></th>
                    <th style="border-bottom: 1px solid black"></th>
                    <th style="border-bottom: 1px solid black"></th>
                    <th style="border-bottom: 1px solid black"></th>
                    <th style="border-bottom: 1px solid black" class="dhead text-center ">
                        {{ $bk->nm_partai }}</th>
                    <th style="border-bottom: 1px solid black"></th>
                    <th style="border-bottom: 1px solid black"></th>
                    <th style="border-bottom: 1px solid black"></th>
                    <th style="border-bottom: 1px solid black"></th>
                    <th style="border-bottom: 1px solid black;border-right: 1px solid black"></th>
                    <th class="dhead text-center" colspan="4" style="border: 1px solid black ">Gudang</th>
                    <th class="dhead text-center" style="border: 1px solid black">pencocokan rp</th>
                </tr>
                <tr>
                    <th class="dhead">Partai</th>
                    <th class="dhead">Grade</th>
                    <th class="text-end dhead">Pcs Awal</th>
                    <th class="text-end dhead">Gr Awal</th>
                    <th class="text-end dhead">Pcs Akhir</th>
                    <th class="text-end dhead">Gr Akhir</th>
                    <th class="text-end dhead">Susut %</th>
                    <th class="text-end dhead">Modal Rp</th>
                    <th class="text-end dhead">Cost Kerja</th>
                    <th class="text-end dhead">Cost Operasional</th>
                    <th class="text-end dhead">Modal tambah cost</th>
                    <th class="text-end dhead" style="border-right: 1px solid black">Modal rata-rata</th>

                    <th class="dhead text-end">sisa pcs</th>
                    <th class="dhead text-end">sisa gr</th>
                    <th class="dhead text-end">ttl rp</th>
                    <th class="dhead text-end" style="border-right: 1px solid black">rata - rata</th>
                    <th style="border-right: 1px solid black"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $bk->nm_partai }}</td>
                    <td>{{ $bk->tipe }}</td>
                    <td class="text-end" style="background-color: #FFC000">{{ number_format($bk->pcs_awal, 0) }}</td>
                    <td class="text-end">{{ number_format($bk->gr_awal, 0) }}</td>
                    <td class="text-end"></td>
                    <td class="text-end"></td>
                    <td class="text-end"></td>
                    <td class="text-end" style="background-color: yellow">{{ number_format($bk->ttl_rp, 0) }}</td>
                    <td class="text-end"></td>
                    <td class="text-end"></td>
                    <td class="text-end"></td>
                    <td class="text-end" style="border-right: 1px solid black;color: #44B3E1">
                        {{ number_format($bk->ttl_rp / $bk->gr_awal, 0) }}
                    </td>
                    <td colspan="4" style="border-right: 1px solid black"></td>
                    <td style="border-right: 1px solid black"></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="border-right: 1px solid black"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="border-right: 1px solid black"></td>
                    <td colspan="1" style="border-right: 1px solid black"></td>
                </tr>
                <tr>
                    <td>Cabut</td>
                    <td></td>
                    <td class="text-end">{{ number_format($cabut->pcs ?? 0, 0) }}</td>
                    <td class="text-end">{{ number_format(($cabut->gr_awal ?? 0) + ($eo->gr_eo_awal ?? 0), 0) }}</td>
                    <td class="text-end">{{ number_format($cabut->pcs ?? 0, 0) }}</td>
                    <td class="text-end">{{ number_format(($cabut->gr ?? 0) + ($eo->gr ?? 0), 0) }}</td>
                    <td class="text-end">
                        {{ number_format((1 - (($cabut->gr ?? 0) + ($eo->gr ?? 0)) / (($cabut->gr_awal ?? 0) + ($eo->gr_eo_awal ?? 0))) * 100, 0) }}%
                    </td>
                    <td class="text-end">{{ number_format(($cabut->modal_rp ?? 0) + ($eo->modal_rp ?? 0), 0) }}</td>
                    <td class="text-end" style="background-color: yellow">
                        {{ number_format(($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0), 0) }}</td>
                    <td class="text-end" style="background-color: yellow">0</td>
                    <td class="text-end" style="color: #44B3E1">
                        {{ number_format(($cabut->modal_rp ?? 0) + ($eo->modal_rp ?? 0) + ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0), 0) }}
                    </td>
                    <td class="text-end" style="border-right: 1px solid black;color: #44B3E1">
                        {{ ($cabut->gr ?? 0) + ($eo->gr ?? 0) == 0 ? 0 : number_format((($cabut->modal_rp ?? 0) + ($eo->modal_rp ?? 0) + ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0)) / (($cabut->gr ?? 0) + ($eo->gr ?? 0)), 0) }}
                    </td>

                    <td class="text-end" style="color: #44B3E1">
                        {{ number_format(($bk->pcs_awal ?? 0) - ($cabut->pcs ?? 0), 0) }}
                    </td>
                    <td class="text-end" style="color: #44B3E1">
                        {{ number_format($bk->gr_awal - ($cabut->gr_awal ?? 0) - ($eo->gr_eo_awal ?? 0), 0) }}</td>
                    <td class="text-end" style="color: #44B3E1">
                        {{ number_format($bk->ttl_rp - ($cabut->modal_rp ?? 0) - ($eo->modal_rp ?? 0), 0) }}
                    </td>
                    <td class="text-end" style="color: #44B3E1;border-right: 1px solid black">
                        {{ $bk->gr_awal - ($cabut->gr_awal ?? 0) - ($eo->gr_eo_awal ?? 0) == 0 ? 0 : number_format(($bk->ttl_rp - ($cabut->modal_rp ?? 0) - ($eo->modal_rp ?? 0)) / ($bk->gr_awal - ($cabut->gr_awal ?? 0) - ($eo->gr_eo_awal ?? 0)), 0) }}
                    </td>
                    <td class="text-end" style="border-right: 1px solid black">
                        {{ number_format(($cabut->modal_rp ?? 0) + ($eo->modal_rp ?? 0) + ($bk->ttl_rp - ($cabut->modal_rp ?? 0) - ($eo->modal_rp ?? 0)), 0) }}
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="background-color: yellow"></td>
                    <td style="background-color: yellow"></td>
                    <td></td>
                    <td style="border-right: 1px solid black"></td>

                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="border-right: 1px solid black"></td>
                    <td colspan="1" style="border-right: 1px solid black"></td>
                </tr>
                <tr>
                    <td>Cetak</td>
                    <td></td>
                    <td class="text-end">{{ number_format($cetak->pcs_tdk + $cetak->pcs, 0) }}</td>
                    <td class="text-end">{{ number_format($cetak->gr_awal, 0) }}</td>
                    <td class="text-end">{{ number_format($cetak->pcs_tdk + $cetak->pcs, 0) }}</td>
                    <td class="text-end">{{ number_format($cetak->gr_tdk + $cetak->gr, 0) }}</td>
                    <td class="text-end">
                        {{ empty($cetak->gr_awal) ? 0 : number_format((1 - ($cetak->gr_tdk + $cetak->gr) / $cetak->gr_awal) * 100, 0) }}%
                    </td>
                    <td class="text-end">{{ number_format($cetak->modal_rp + $cetak->cost_kerja, 0) }}</td>
                    <td class="text-end" style="background-color: yellow">{{ number_format($cetak->ttl_rp, 0) }}</td>
                    <td class="text-end" style="background-color: yellow">0</td>
                    <td class="text-end" style="color: #44B3E1">
                        {{ number_format($cetak->modal_rp + $cetak->cost_kerja + $cetak->ttl_rp, 0) }}</td>
                    <td class="text-end" style="border-right: 1px solid black;color: #44B3E1">
                        {{ $cetak->gr_tdk + $cetak->gr == 0 ? 0 : number_format(($cetak->modal_rp + $cetak->cost_kerja + $cetak->ttl_rp) / ($cetak->gr_tdk + $cetak->gr), 0) }}
                    </td>

                    <td class="text-end" style="color: #44B3E1">
                        {{ number_format(($cabut->pcs ?? 0) - ($cetak->pcs_tdk ?? 0) - ($cetak->pcs ?? 0), 0) }}</td>
                    <td class="text-end" style="color: #44B3E1">
                        {{ number_format(($cabut->gr ?? 0) + ($eo->gr ?? 0) - $cetak->gr_awal, 0) }}
                    </td>
                    <td class="text-end" style="color: #44B3E1">
                        {{ number_format(($cabut->modal_rp ?? 0) + ($eo->modal_rp ?? 0) + ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0) - ($cetak->modal_rp + $cetak->cost_kerja), 0) }}
                    </td>
                    <td class="text-end" style="color: #44B3E1;border-right: 1px solid black">
                        {{ ($cabut->gr ?? 0) + ($eo->gr ?? 0) - $cetak->gr_awal == 0 ? 0 : number_format((($cabut->modal_rp ?? 0) + ($eo->modal_rp ?? 0) + ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0) - ($cetak->modal_rp + $cetak->cost_kerja)) / (($cabut->gr ?? 0) + ($eo->gr ?? 0) - $cetak->gr_awal), 0) }}
                    </td>
                    <td class="text-end" style="border-right: 1px solid black">
                        {{ number_format(($cabut->modal_rp ?? 0) + ($eo->modal_rp ?? 0) + ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0) - ($cetak->modal_rp + $cetak->cost_kerja) + ($cetak->modal_rp + $cetak->cost_kerja), 0) }}
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="background-color: yellow"></td>
                    <td style="background-color: yellow"></td>
                    <td></td>
                    <td style="border-right: 1px solid black"></td>

                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="border-right: 1px solid black"></td>
                    <td colspan="1" style="border-right: 1px solid black"></td>
                </tr>
                <tr>
                    <td>Sortir</td>
                    <td></td>
                    <td class="text-end">{{ number_format($sortir->pcs ?? 0, 0) }}</td>
                    <td class="text-end">{{ number_format($sortir->gr_awal ?? 0, 0) }}</td>
                    <td class="text-end">{{ number_format($sortir->pcs ?? 0, 0) }}</td>
                    <td class="text-end">{{ number_format($sortir->gr ?? 0, 0) }}</td>
                    <td class="text-end">
                        {{ empty($sortir->gr_awal) ? 0 : number_format((1 - $sortir->gr / $sortir->gr_awal) * 100, 0) }}%
                    </td>
                    <td class="text-end">
                        {{ empty($sortir->modal_rp) ? 0 : number_format($sortir->modal_rp + $sortir->cost_kerja, 0) }}
                    </td>
                    <td class="text-end" style="background-color: yellow">
                        {{ number_format($sortir->ttl_rp ?? 0, 0) }}
                    </td>
                    <td class="text-end" style="background-color: yellow">0</td>
                    <td class="text-end" style="color: #44B3E1">
                        {{ empty($sortir->modal_rp) ? 0 : number_format($sortir->modal_rp + $sortir->cost_kerja + $sortir->ttl_rp, 0) }}
                    </td>
                    <td class="text-end" style="border-right: 1px solid black;color: #44B3E1">
                        {{ empty($sortir->gr) ? 0 : number_format(($sortir->modal_rp + $sortir->cost_kerja + $sortir->ttl_rp) / $sortir->gr, 0) }}
                    </td>

                    <td class="text-end" style="color: #44B3E1">
                        {{ empty($sortir->gr) ? 0 : number_format($cetak->pcs_tdk + $cetak->pcs - $sortir->pcs, 0) }}
                    </td>
                    <td class="text-end" style="color: #44B3E1">
                        {{ empty($sortir->gr) ? 0 : number_format($cetak->gr_tdk + $cetak->gr - $sortir->gr_awal, 0) }}
                    </td>
                    <td class="text-end" style="color: #44B3E1">
                        {{ empty($sortir->gr) ? 0 : number_format($cetak->modal_rp + $cetak->cost_kerja + $cetak->ttl_rp - ($sortir->modal_rp + $sortir->cost_kerja), 0) }}
                    </td>
                    <td class="text-end" style="color: #44B3E1;border-right: 1px solid black">
                        @php
                            $pembagi = ($cetak->gr_tdk ?? 0) + ($cetak->gr ?? 0) - ($sortir->gr_awal ?? 0);
                        @endphp
                        {{ $pembagi == 0 ? 0 : number_format((($cetak->modal_rp ?? 0) + ($cetak->cost_kerja ?? 0) + ($cetak->ttl_rp ?? 0) - (($sortir->modal_rp ?? 0) + ($sortir->cost_kerja ?? 0))) / (($cetak->gr_tdk ?? 0) + ($cetak->gr ?? 0) - ($sortir->gr_awal ?? 0)), 0) }}
                    </td>
                    <td class="text-end" style="border-right: 1px solid black">
                        {{ empty($sortir->gr_awal) ? 0 : number_format($cetak->modal_rp + $cetak->cost_kerja + $cetak->ttl_rp - ($sortir->modal_rp + $sortir->cost_kerja) + ($sortir->modal_rp + $sortir->cost_kerja), 0) }}
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="background-color: yellow"></td>
                    <td style="background-color: yellow"></td>
                    <td></td>
                    <td style="border-right: 1px solid black"></td>

                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="border-right: 1px solid black"></td>
                    <td colspan="1" style="border-right: 1px solid black"></td>
                </tr>
                <tr>
                    <td>Grading</td>
                    <td></td>
                    <td class="text-end">{{ number_format($grading->pcs, 0) }}</td>
                    <td class="text-end">{{ number_format($grading->gr, 0) }}</td>
                    <td class="text-end">{{ number_format($grading->pcs, 0) }}</td>
                    <td class="text-end">{{ number_format($grading->gr, 0) }}</td>
                    <td class="text-end">
                        0%
                    </td>
                    <td class="text-end">{{ number_format($grading->cost_bk + $grading->cost_kerja, 0) }}</td>
                    <td class="text-end" style="background-color: yellow">0</td>
                    <td class="text-end" style="background-color: yellow">{{ number_format($grading->cost_op, 0) }}
                    </td>
                    <td class="text-end" style="color: #44B3E1">
                        {{ number_format($grading->cost_bk + $grading->cost_kerja + $grading->cost_op, 0) }}</td>
                    <td class="text-end" style="border-right: 1px solid black;color: #44B3E1">
                        {{ empty($grading->gr) ? 0 : number_format(($grading->cost_bk + $grading->cost_kerja + $grading->cost_op) / $grading->gr, 0) }}
                    </td>

                    <td class="text-end" style="color: #44B3E1">
                        {{ number_format(($sortir->pcs ?? 0) - ($grading->pcs ?? 0), 0) }}</td>
                    <td class="text-end" style="color: #44B3E1">
                        {{ number_format(($sortir->gr ?? 0) - ($grading->gr ?? 0), 0) }}
                    </td>
                    <td class="text-end" style="color: #44B3E1;">
                        {{ number_format(($sortir->modal_rp ?? 0) + ($sortir->cost_kerja ?? 0) + ($sortir->ttl_rp ?? 0) - (($grading->cost_bk ?? 0) + ($grading->cost_kerja ?? 0)), 0) }}
                    </td>
                    @php
                        $grading_gr = $grading->gr ?? 0;
                        $sortir_gr = $sortir->gr ?? 0;
                        $selisih_gr = $sortir_gr - $grading_gr;
                    @endphp

                    <td class="text-end" style="color: #44B3E1 ; border-right: 1px solid black">
                        {{ $selisih_gr == 0
                            ? 0
                            : number_format(
                                (($sortir->modal_rp ?? 0) +
                                    ($sortir->cost_kerja ?? 0) +
                                    ($sortir->ttl_rp ?? 0) -
                                    (($grading->cost_bk ?? 0) + ($grading->cost_kerja ?? 0))) /
                                    $selisih_gr,
                                0,
                            ) }}
                    </td>

                    <td class="text-end" style="border-right: 1px solid black">
                        {{ number_format(($sortir->modal_rp ?? 0) + ($sortir->cost_kerja ?? 0) + ($sortir->ttl_rp ?? 0) - (($grading->cost_bk ?? 0) + ($grading->cost_kerja ?? 0)) + (($grading->cost_bk ?? 0) + ($grading->cost_kerja ?? 0)), 0) }}
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="background-color: yellow"></td>
                    <td style="background-color: yellow"></td>
                    <td></td>
                    <td style="border-right: 1px solid black"></td>

                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="border-right: 1px solid black"></td>
                    <td colspan="1" style="border-right: 1px solid black"></td>
                </tr>
                <tr>
                    <td>Sisa Pengiriman</td>
                    <td></td>
                    <td class="text-end">{{ number_format($pengiriman->pcs, 0) }}</td>
                    <td class="text-end">{{ number_format($pengiriman->gr, 0) }}</td>
                    <td class="text-end">{{ number_format($pengiriman->pcs, 0) }}</td>
                    <td class="text-end">{{ number_format($pengiriman->gr, 0) }}</td>
                    <td class="text-end">
                        0%
                    </td>
                    <td class="text-end">
                        {{ number_format($pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_op, 0) }}
                    </td>
                    <td class="text-end" style="background-color: yellow">0</td>
                    <td class="text-end" style="background-color: yellow">0</td>
                    <td class="text-end" style="color: #44B3E1">
                        {{ number_format($pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_op, 0) }}
                    </td>
                    <td class="text-end" style="border-right: 1px solid black;color: #44B3E1">
                        {{ empty($pengiriman->gr) ? 0 : number_format(($pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_op) / $pengiriman->gr, 0) }}
                    </td>

                    <td class="text-end" style="color: #44B3E1">
                        {{ number_format(($grading->pcs ?? 0) - ($pengiriman->pcs ?? 0), 0) }}</td>
                    <td class="text-end" style="color: #44B3E1">
                        {{ number_format(($grading->gr ?? 0) - ($pengiriman->gr ?? 0), 0) }}
                    </td>
                    <td class="text-end" style="color: #44B3E1">
                        {{ number_format(($grading->cost_bk ?? 0) + ($grading->cost_kerja ?? 0) + ($grading->cost_op ?? 0) - (($pengiriman->cost_bk ?? 0) + ($pengiriman->cost_kerja ?? 0) + ($pengiriman->cost_op ?? 0)), 0) }}
                    </td>
                    <td class="text-end" style="color: #44B3E1;border-right: 1px solid black">
                        {{ $grading->gr - $pengiriman->gr == 0 ? 0 : number_format((($grading->cost_bk ?? 0) + ($grading->cost_kerja ?? 0) + ($grading->cost_op ?? 0) - (($pengiriman->cost_bk ?? 0) + ($pengiriman->cost_kerja ?? 0) + ($pengiriman->cost_op ?? 0))) / (($grading->gr ?? 0) - ($pengiriman->gr ?? 0)), 0) }}
                    </td>
                    <td class="text-end" style="border-right: 1px solid black">
                        {{ number_format(($grading->cost_bk ?? 0) + ($grading->cost_kerja ?? 0) + ($grading->cost_op ?? 0) - (($pengiriman->cost_bk ?? 0) + ($pengiriman->cost_kerja ?? 0) + ($pengiriman->cost_op ?? 0)) + (($pengiriman->cost_bk ?? 0) + ($pengiriman->cost_kerja ?? 0) + ($pengiriman->cost_op ?? 0)), 0) }}
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="background-color: yellow"></td>
                    <td style="background-color: yellow"></td>
                    <td></td>
                    <td style="border-right: 1px solid black"></td>

                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="border-right: 1px solid black"></td>
                    <td colspan="1" style="border-right: 1px solid black"></td>
                </tr>
                <tr>
                    <td>Sudah Terkirim</td>
                    <td></td>
                    <td class="text-end"></td>
                    <td class="text-end"></td>
                    <td class="text-end"></td>
                    <td class="text-end"></td>
                    <td class="text-end">
                        0%
                    </td>
                    <td class="text-end">
                        0
                    </td>
                    <td class="text-end" style="background-color: yellow">0</td>
                    <td class="text-end" style="background-color: yellow">0</td>
                    <td class="text-end" style="color: #44B3E1">

                    </td>
                    <td class="text-end" style="border-right: 1px solid black;color: #44B3E1">

                    </td>

                    <td class="text-end" style="color: #44B3E1">
                        {{ number_format($pengiriman->pcs, 0) }}
                    </td>
                    <td class="text-end" style="color: #44B3E1">
                        {{ number_format($pengiriman->gr, 0) }}
                    </td>
                    <td class="text-end" style="color: #44B3E1">
                        {{ number_format($pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_op, 0) }}
                    </td>
                    <td class="text-end" style="color: #44B3E1;border-right: 1px solid black">
                        {{ empty($pengiriman->gr) ? 0 : number_format(($pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_op) / $pengiriman->gr, 0) }}
                    </td>
                    <td class="text-end" style="border-right: 1px solid black">

                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="background-color: yellow"></td>
                    <td style="background-color: yellow"></td>
                    <td></td>
                    <td style="border-right: 1px solid black"></td>

                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="border-right: 1px solid black"></td>
                    <td colspan="1" style="border-right: 1px solid black"></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th style="border-bottom: 1px solid black">Total</th>
                    <th style="border-bottom: 1px solid black"></th>
                    <th style="border-bottom: 1px solid black"></th>
                    <th style="border-bottom: 1px solid black"></th>
                    <th style="border-bottom: 1px solid black"></th>
                    <th style="border-bottom: 1px solid black"></th>
                    <th style="border-bottom: 1px solid black"></th>
                    <th style="border-bottom: 1px solid black"></th>
                    <th class="text-end" style="background-color: #FFC000;border-bottom: 1px solid black">
                        {{ number_format(($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0) + ($cetak->ttl_rp ?? 0) + ($sortir->ttl_rp ?? 0) + ($bk->ttl_rp ?? 0) + ($grading->cost_op ?? 0), 0) }}
                    </th>
                    <th style="border-bottom: 1px solid black"></th>
                    <th style="border-bottom: 1px solid black"></th>
                    <th style="border-right: 1px solid black;border-bottom: 1px solid black"></th>
                    <th class="text-end" style="background-color: #FFC000;border-bottom: 1px solid black">
                        {{ number_format(
                            ($bk->pcs_awal ?? 0) -
                                ($cabut->pcs ?? 0) +
                                (($cabut->pcs ?? 0) - ($cetak->pcs_tdk ?? 0) - ($cetak->pcs ?? 0)) +
                                (($cetak->pcs_tdk ?? 0) + ($cetak->pcs ?? 0) - ($sortir->pcs ?? 0)) +
                                (($sortir->pcs ?? 0) - ($grading->pcs ?? 0)) +
                                (($grading->pcs ?? 0) - ($pengiriman->pcs ?? 0)) +
                                ($pengiriman->pcs ?? 0),
                            0,
                        ) }}

                    </th>
                    <th class="text-end" style="border-bottom: 1px solid black">
                        {{ number_format(
                            ($bk->gr_awal ?? 0) -
                                ($cabut->gr_awal ?? 0) -
                                ($eo->gr_eo_awal ?? 0) +
                                (($cabut->gr ?? 0) + ($eo->gr ?? 0) - ($cetak->gr_awal ?? 0)) +
                                (($cetak->gr_tdk ?? 0) + ($cetak->gr ?? 0) - ($sortir->gr_awal ?? 0)) +
                                (($sortir->gr ?? 0) - ($grading->gr ?? 0)) +
                                (($grading->gr ?? 0) - ($pengiriman->gr ?? 0)) +
                                ($pengiriman->gr ?? 0),
                            0,
                        ) }}

                    </th>
                    <th class="text-end" style="background-color: #FFC000;border-bottom: 1px solid black">
                        {{ number_format(
                            ($bk->ttl_rp ?? 0) -
                                ($cabut->modal_rp ?? 0) -
                                ($eo->modal_rp ?? 0) +
                                (($cabut->modal_rp ?? 0) +
                                    ($eo->modal_rp ?? 0) +
                                    ($cabut->ttl_rp ?? 0) +
                                    ($eo->ttl_rp ?? 0) -
                                    (($cetak->modal_rp ?? 0) + ($cetak->cost_kerja ?? 0))) +
                                (($cetak->modal_rp ?? 0) +
                                    ($cetak->cost_kerja ?? 0) +
                                    ($cetak->ttl_rp ?? 0) -
                                    (($sortir->modal_rp ?? 0) + ($sortir->cost_kerja ?? 0))) +
                                (($sortir->modal_rp ?? 0) +
                                    ($sortir->cost_kerja ?? 0) +
                                    ($sortir->ttl_rp ?? 0) -
                                    (($grading->cost_bk ?? 0) + ($grading->cost_kerja ?? 0))) +
                                (($grading->cost_bk ?? 0) +
                                    ($grading->cost_kerja ?? 0) +
                                    ($grading->cost_op ?? 0) -
                                    (($pengiriman->cost_bk ?? 0) + ($pengiriman->cost_kerja ?? 0) + ($pengiriman->cost_op ?? 0))) +
                                (($pengiriman->cost_bk ?? 0) + ($pengiriman->cost_kerja ?? 0) + ($pengiriman->cost_op ?? 0)),
                            0,
                        ) }}
                    </th>
                    <th style="border-right: 1px solid black;border-bottom: 1px solid black"></th>
                    <th style="border-right: 1px solid black;border-bottom: 1px solid black"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
