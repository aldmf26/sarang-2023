@php
    // Helper function to format numbers
    $formatNumber = function ($value, $decimals = 0)
    {
        return number_format($value ?? 0, $decimals);
    };

    // Helper function to calculate percentage safely
    $calculateSusut = function ($current, $original)
    {
        if (empty($original)) {
            return 0;
        }
        return (1 - $current / $original) * 100;
    };

    // Helper function to calculate average safely
    $calculateRatarata = function ($total, $count)
    {
        if (empty($count)) {
            return 0;
        }
        return $total / $count;
    };

    // Pre-calculate common values
    $grAwalCabut = ($cabut->gr_awal ?? 0) + ($eo->gr_eo_awal ?? 0);
    $grAkhirCabut = ($cabut->gr ?? 0) + ($eo->gr ?? 0);
    $cabutModal = ($cabut->modal_rp ?? 0) + ($eo->modal_rp ?? 0);
    $cabutTtlRp = ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0);
    $modalCabutTbhCost = $cabutModal + $cabutTtlRp;

    // Cetak values
    $cetakPcsTotal = ($cetak->pcs_tdk ?? 0) + ($cetak->pcs ?? 0);
    $cetakGrTotal = ($cetak->gr_tdk ?? 0) + ($cetak->gr ?? 0);
    $cetakModalTotal = ($cetak->modal_rp ?? 0) + ($cetak->cost_kerja ?? 0);
    $cetakModalTbhCost = $cetakModalTotal + ($cetak->ttl_rp ?? 0);

    // Sortir values
    $sortirModalTotal = ($sortir->modal_rp ?? 0) + ($sortir->cost_kerja ?? 0);
    $sortirModalTbhCost = $sortirModalTotal + ($sortir->ttl_rp ?? 0);

    // Grading values
    $gradingModalTotal = ($grading->cost_bk ?? 0) + ($grading->cost_kerja ?? 0);
    $gradingModalTbhCost = $gradingModalTotal + ($grading->cost_op ?? 0);

    // Pengiriman values
    $pengirimanModalTotal = ($pengiriman->cost_bk ?? 0) + ($pengiriman->cost_kerja ?? 0) + ($pengiriman->cost_op ?? 0);

    // Total values for footer
    $totalCost =
        ($cabut->ttl_rp ?? 0) +
        ($eo->ttl_rp ?? 0) +
        ($cetak->ttl_rp ?? 0) +
        ($sortir->ttl_rp ?? 0) +
        ($bk->ttl_rp ?? 0) +
        ($grading->cost_op ?? 0);

    $totalPcs =
        ($bk->pcs_awal ?? 0) -
        ($cabut->pcs ?? 0) +
        (($cabut->pcs ?? 0) - $cetakPcsTotal) +
        ($cetakPcsTotal - ($sortir->pcs ?? 0)) +
        (($sortir->pcs ?? 0) - ($grading->pcs ?? 0)) +
        (($grading->pcs ?? 0) - ($pengiriman->pcs ?? 0)) +
        ($pengiriman->pcs ?? 0);

    $totalGr =
        ($bk->gr_awal ?? 0) -
        $grAwalCabut +
        ($grAkhirCabut - ($cetak->gr_awal ?? 0)) +
        ($cetakGrTotal - ($sortir->gr_awal ?? 0)) +
        (($sortir->gr ?? 0) - ($grading->gr ?? 0)) +
        (($grading->gr ?? 0) - ($pengiriman->gr ?? 0)) +
        ($pengiriman->gr ?? 0);

    $totalRp =
        ($bk->ttl_rp ?? 0) -
        $cabutModal +
        ($modalCabutTbhCost - $cetakModalTotal) +
        ($cetakModalTbhCost - $sortirModalTotal) +
        ($sortirModalTbhCost - $gradingModalTotal) +
        ($gradingModalTbhCost - $pengirimanModalTotal) +
        $pengirimanModalTotal;

@endphp

<div class="row">
    <div class="col-lg-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="dhead text-center" colspan="12">{{ $bk->nm_partai }}</th>
                    <th class="dhead text-center" colspan="4">Gudang</th>
                    <th class="dhead text-center" rowspan="2">pencocokan rp</th>
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
                    <th class="text-end dhead">Modal rata-rata</th>
                    <th class="dhead text-end">sisa pcs</th>
                    <th class="dhead text-end">sisa gr</th>
                    <th class="dhead text-end">ttl rp</th>
                    <th class="dhead text-end">rata - rata</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <a href="#" class="detail_bk fw-bold" nm_partai="{{ $bk->nm_partai }}"
                            data-bs-toggle="modal" data-bs-target="#detail_bk_tes">{{ $bk->nm_partai }}</a>
                    </td>
                    <td>{{ $bk->tipe }}</td>
                    <td class="text-end" style="background-color: #FFC000">{{ $formatNumber($bk->pcs_awal) }}</td>
                    <td class="text-end">{{ $formatNumber($bk->gr_awal) }}</td>
                    <td class="text-end"></td>
                    <td class="text-end"></td>
                    <td class="text-end"></td>
                    <td class="text-end">{{ $formatNumber($bk->ttl_rp) }}</td>
                    <td class="text-end"></td>
                    <td class="text-end"></td>
                    <td class="text-end"></td>
                    <td class="text-end">{{ $formatNumber($calculateRatarata($bk->ttl_rp, $bk->gr_awal)) }}</td>
                </tr>

                <tr>
                    <td>
                        <a href="#" class="detail_cabut fw-bold" nm_partai="{{ $bk->nm_partai }}"
                            data-bs-toggle="modal" data-bs-target="#detail_cabut_tes">
                            Cabut</a>
                    </td>
                    <td></td>
                    <td class="text-end">{{ $formatNumber($cabut->pcs) }}</td>
                    <td class="text-end">{{ $formatNumber($grAwalCabut) }}</td>
                    <td class="text-end">{{ $formatNumber($cabut->pcs) }}</td>
                    <td class="text-end">{{ $formatNumber($grAkhirCabut) }}</td>
                    <td class="text-end">{{ $formatNumber($calculateSusut($grAkhirCabut, $grAwalCabut)) }}%</td>
                    <td class="text-end">{{ $formatNumber($cabutModal) }}</td>
                    <td class="text-end">{{ $formatNumber($cabutTtlRp) }}</td>
                    <td class="text-end">0</td>
                    <td class="text-end">{{ $formatNumber($modalCabutTbhCost) }}</td>
                    <td class="text-end">{{ $formatNumber($calculateRatarata($modalCabutTbhCost, $grAkhirCabut)) }}</td>

                    <td class="text-end">{{ $formatNumber(($bk->pcs_awal ?? 0) - ($cabut->pcs ?? 0)) }}</td>
                    <td class="text-end">{{ $formatNumber($bk->gr_awal - $grAwalCabut) }}</td>
                    <td class="text-end">{{ $formatNumber($bk->ttl_rp - $cabutModal) }}</td>
                    <td class="text-end">
                        {{ $formatNumber($calculateRatarata($bk->ttl_rp - $cabutModal, $bk->gr_awal - $grAwalCabut)) }}
                    </td>
                    <td class="text-end">{{ $formatNumber($cabutModal + ($bk->ttl_rp - $cabutModal)) }}</td>
                </tr>

                <tr>
                    <td>
                        <a href="#" class="detail_cetak fw-bold" nm_partai="{{ $bk->nm_partai }}"
                            data-bs-toggle="modal" data-bs-target="#detail_cetak_tes">Cetak</a>
                    </td>
                    <td></td>
                    <td class="text-end">{{ $formatNumber($cetakPcsTotal) }}</td>
                    <td class="text-end">{{ $formatNumber($cetak->gr_awal) }}</td>
                    <td class="text-end">{{ $formatNumber($cetakPcsTotal) }}</td>
                    <td class="text-end">{{ $formatNumber($cetakGrTotal) }}</td>
                    <td class="text-end">
                        {{ $formatNumber($calculateSusut($cetakGrTotal, $cetak->gr_awal)) }}%
                    </td>
                    <td class="text-end">{{ $formatNumber($cetakModalTotal) }}</td>
                    <td class="text-end">{{ $formatNumber($cetak->ttl_rp) }}</td>
                    <td class="text-end">0</td>
                    <td class="text-end">{{ $formatNumber($cetakModalTbhCost) }}</td>
                    <td class="text-end">{{ $formatNumber($calculateRatarata($cetakModalTbhCost, $cetakGrTotal)) }}</td>

                    <td class="text-end">{{ $formatNumber(($cabut->pcs ?? 0) - $cetakPcsTotal) }}</td>
                    <td class="text-end">{{ $formatNumber($grAkhirCabut - $cetak->gr_awal) }}</td>
                    <td class="text-end">{{ $formatNumber($modalCabutTbhCost - $cetakModalTotal) }}</td>
                    <td class="text-end">
                        {{ $formatNumber($calculateRatarata($modalCabutTbhCost - $cetakModalTotal, $grAkhirCabut - $cetak->gr_awal)) }}
                    </td>
                    <td class="text-end">{{ $formatNumber($modalCabutTbhCost) }}</td>
                </tr>

                <tr>
                    <td>
                        <a href="#" class="detail_sortir fw-bold" nm_partai="{{ $bk->nm_partai }}"
                            data-bs-toggle="modal" data-bs-target="#detail_sortir_tes">Sortir</a>
                    </td>
                    <td></td>
                    <td class="text-end">{{ $formatNumber($sortir->pcs ?? 0) }}</td>
                    <td class="text-end">{{ $formatNumber($sortir->gr_awal ?? 0) }}</td>
                    <td class="text-end">{{ $formatNumber($sortir->pcs ?? 0) }}</td>
                    <td class="text-end">{{ $formatNumber($sortir->gr ?? 0) }}</td>
                    <td class="text-end">
                        {{ $formatNumber($calculateSusut($sortir->gr ?? 0, $sortir->gr_awal ?? 0)) }}%
                    </td>
                    <td class="text-end">{{ $formatNumber($sortirModalTotal) }}</td>
                    <td class="text-end">{{ $formatNumber($sortir->ttl_rp ?? 0) }}</td>
                    <td class="text-end">0</td>
                    <td class="text-end">{{ $formatNumber($sortirModalTbhCost) }}</td>
                    <td class="text-end">{{ $formatNumber($calculateRatarata($sortirModalTbhCost, $sortir->gr ?? 0)) }}</td>

                    <td class="text-end">{{ $formatNumber($cetakPcsTotal - ($sortir->pcs ?? 0)) }}</td>
                    <td class="text-end">{{ $formatNumber($cetakGrTotal - ($sortir->gr_awal ?? 0)) }}</td>
                    <td class="text-end">{{ $formatNumber($cetakModalTbhCost - $sortirModalTotal) }}</td>
                    <td class="text-end">
                        {{ $formatNumber($calculateRatarata($cetakModalTbhCost - $sortirModalTotal, $cetakGrTotal - ($sortir->gr_awal ?? 0))) }}
                    </td>
                    <td class="text-end">{{ $formatNumber($cetakModalTbhCost) }}</td>
                </tr>

                <tr>
                    <td>
                        <a href="#" class="detail_grade fw-bold" nm_partai="{{ $bk->nm_partai }}"
                            data-bs-toggle="modal" data-bs-target="#detail_data">Grading</a>
                    </td>
                    <td></td>
                    <td class="text-end">{{ $formatNumber($grading->pcs) }}</td>
                    <td class="text-end">{{ $formatNumber($grading->gr) }}</td>
                    <td class="text-end">{{ $formatNumber($grading->pcs) }}</td>
                    <td class="text-end">{{ $formatNumber($grading->gr) }}</td>
                    <td class="text-end">0%</td>
                    <td class="text-end">{{ $formatNumber($gradingModalTotal) }}</td>
                    <td class="text-end">0</td>
                    <td class="text-end">{{ $formatNumber($grading->cost_op) }}</td>
                    <td class="text-end">{{ $formatNumber($gradingModalTbhCost) }}</td>
                    <td class="text-end">{{ $formatNumber($calculateRatarata($gradingModalTbhCost, $grading->gr)) }}
                    </td>

                    <td class="text-end">{{ $formatNumber(($sortir->pcs ?? 0) - ($grading->pcs ?? 0)) }}</td>
                    <td class="text-end">{{ $formatNumber(($sortir->gr ?? 0) - ($grading->gr ?? 0)) }}</td>
                    <td class="text-end">{{ $formatNumber($sortirModalTbhCost - $gradingModalTotal) }}</td>
                    <td class="text-end">
                        @php
                            $selisih_gr = ($sortir->gr ?? 0) - ($grading->gr ?? 0);
                        @endphp
                        {{ $formatNumber($calculateRatarata($sortirModalTbhCost - $gradingModalTotal, $selisih_gr)) }}
                    </td>
                    <td class="text-end">{{ $formatNumber($sortirModalTbhCost) }}</td>
                </tr>

                <tr>
                    <td>
                        <a href="#" class="detail_grade2 fw-bold" nm_partai="{{ $bk->nm_partai }}"
                            data-bs-toggle="modal" data-bs-target="#detail_data2">Sisa Pengiriman</a>
                    </td>
                    <td></td>
                    <td class="text-end">{{ $formatNumber($pengiriman->pcs) }}</td>
                    <td class="text-end">{{ $formatNumber($pengiriman->gr) }}</td>
                    <td class="text-end">{{ $formatNumber($pengiriman->pcs) }}</td>
                    <td class="text-end">{{ $formatNumber($pengiriman->gr) }}</td>
                    <td class="text-end">0%</td>
                    <td class="text-end">{{ $formatNumber($pengirimanModalTotal) }}</td>
                    <td class="text-end">0</td>
                    <td class="text-end">0</td>
                    <td class="text-end">{{ $formatNumber($pengirimanModalTotal) }}</td>
                    <td class="text-end">{{ $formatNumber($calculateRatarata($pengirimanModalTotal, $pengiriman->gr)) }}
                    </td>

                    <td class="text-end">{{ $formatNumber(($grading->pcs ?? 0) - ($pengiriman->pcs ?? 0)) }}</td>
                    <td class="text-end">{{ $formatNumber(($grading->gr ?? 0) - ($pengiriman->gr ?? 0)) }}</td>
                    <td class="text-end">{{ $formatNumber($gradingModalTbhCost - $pengirimanModalTotal) }}</td>
                    <td class="text-end">
                        {{ $formatNumber($calculateRatarata($gradingModalTbhCost - $pengirimanModalTotal, ($grading->gr ?? 0) - ($pengiriman->gr ?? 0))) }}
                    </td>
                    <td class="text-end">{{ $formatNumber($gradingModalTbhCost) }}</td>
                </tr>

                <tr>
                    <td>
                        <a href="#" class="detail_grade2 fw-bold" nm_partai="{{ $bk->nm_partai }}"
                            data-bs-toggle="modal" data-bs-target="#detail_data2">Sudah Terkirim</a>
                    </td>
                    <td></td>
                    <td class="text-end"></td>
                    <td class="text-end"></td>
                    <td class="text-end"></td>
                    <td class="text-end"></td>
                    <td class="text-end">0%</td>
                    <td class="text-end"></td>
                    <td class="text-end">0</td>
                    <td class="text-end">0</td>
                    <td class="text-end"></td>
                    <td class="text-end"></td>

                    <td class="text-end">{{ $formatNumber($pengiriman->pcs) }}</td>
                    <td class="text-end">{{ $formatNumber($pengiriman->gr) }}</td>
                    <td class="text-end">{{ $formatNumber($pengirimanModalTotal) }}</td>
                    <td class="text-end">{{ $formatNumber($calculateRatarata($pengirimanModalTotal, $pengiriman->gr)) }}
                    </td>
                    <td class="text-end"></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="8">Total</th>
                    <th class="text-end" style="background-color: #FFC000">{{ $formatNumber($totalCost) }}</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="text-end" style="background-color: #FFC000">{{ $formatNumber($totalPcs) }}</th>
                    <th class="text-end">{{ $formatNumber($totalGr) }}</th>
                    <th class="text-end" style="background-color: #FFC000">{{ $formatNumber($totalRp) }}</th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
