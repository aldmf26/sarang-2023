<x-theme.app title="{{ $title }}" table="Y" sizeCard="9">
    <x-slot name="cardHeader">
        <h6 class="mt-1">{{ $title }}</h6>

        <h6>Filter Bulan :</h6>
        <div class="d-flex gap-2">
            @foreach ($dataBulan as $d)
                <div>
                    <a href="{{ route('cocokan.balance.gaji', ['bulan' => $d->bulan, 'tahun' => $d->tahun]) }}"
                        class="btn btn-sm {{ $d->bulan == $bulan ? 'btn-info' : '' }}">{{ formatTglGaji($d->bulan, $d->tahun) }}</a>
                </div>
            @endforeach
            <div>
                <a href="{{ route('cocokan.balance.gaji', ['bulan' => $d->bulan + 1, 'tahun' => $d->tahun]) }}"
                    class="btn btn-sm {{ $d->bulan + 1 == $bulan ? 'btn-info' : '' }}">{{ formatTglGaji($d->bulan + 1, $d->tahun) }}</a>
            </div>

        </div>
        <a href="{{ route('cocokan.balance.CostGajiProses') }}" class="btn btn-sm bg-primary float-end text-white"><i
                class="fas fa-file-excel"></i> Export</a>

    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <div class="col-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">Bulan</th>
                            <th class="dhead">Kerja</th>
                            <th class="dhead text-end">Gaji</th>
                            <th class="dhead text-end">Cost Operasional</th>
                            <th class="dhead text-end">Total Gaji</th>
                            <th class="dhead text-end">Pcs Akhir</th>
                            <th class="dhead text-end">Gr Akhir</th>
                            <th class="dhead text-end">Rp/gr</th>
                        </tr>
                    </thead>
                    <thead>
                        <tr>
                            <th colspan="2" class="bg-info text-white">Total</th>
                            @php
                                $ttlGaji = $cabut->cost + $cetak->cost_kerja + $sortir->cost_kerja;
                                $ttlTtlGaji = $operasional->total_operasional ?? 0;
                                $ttlCost = empty($operasional->total_operasional)
                                    ? 0
                                    : $operasional->total_operasional -
                                        $cabut->cost -
                                        $cetak->cost_kerja -
                                        $sortir->cost_kerja;
                                $ttlPcs = $cabut->pcs + $cetak->pcs + $sortir->pcs + $grading->pcs;
                                $ttlGr = $cabut->gr + $cetak->gr + $sortir->gr + $grading->gr;
                            @endphp
                            <th class="text-end bg-info text-white">{{ number_format($ttlGaji, 0) }}</th>
                            <th class="text-end bg-info text-white">{{ number_format($ttlCost, 0) }}</th>
                            <th class="text-end bg-info text-white">{{ number_format($ttlTtlGaji, 0) }}</th>
                            <th class="text-end bg-info text-white">{{ number_format($ttlPcs, 0) }}</th>
                            <th class="text-end bg-info text-white">{{ number_format($ttlGr, 0) }}</th>
                            <th class="bg-info text-end text-white">{{ number_format($ttlTtlGaji / $ttlGr, 0) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ formatTglGaji($bulan, $tahun) }}</td>
                            <td>cabut</td>
                            <td align="right">{{ number_format($cabut->cost, 0) }}</td>
                            <td align="right">{{ 0 }}</td>
                            <td align="right">{{ 0 }}</td>
                            <td align="right">{{ number_format($cabut->pcs, 0) }}</td>
                            <td align="right">{{ number_format($cabut->gr, 0) }}</td>
                            <td align="right">{{ number_format($cabut->cost / $cabut->gr, 0) }}</td>
                        </tr>
                        <tr>
                            <td>{{ formatTglGaji($bulan, $tahun) }}</td>
                            <td>cetak</td>
                            <td align="right">{{ number_format($cetak->cost_kerja, 0) }}</td>
                            <td align="right">{{ 0 }}</td>
                            <td align="right">{{ 0 }}</td>
                            <td align="right">{{ number_format($cetak->pcs, 0) }}</td>
                            <td align="right">{{ number_format($cetak->gr, 0) }}</td>
                            <td align="right">{{ number_format($cetak->cost_kerja / $cetak->gr, 0) }}</td>
                        </tr>
                        <tr>
                            <td>{{ formatTglGaji($bulan, $tahun) }}</td>
                            <td>sortir</td>
                            <td align="right">{{ number_format($sortir->cost_kerja, 0) }}</td>
                            <td align="right">{{ 0 }}</td>
                            <td align="right">{{ 0 }}</td>
                            <td align="right">{{ number_format($sortir->pcs, 0) }}</td>
                            <td align="right">{{ number_format($sortir->gr, 0) }}</td>
                            <td align="right">{{ number_format($sortir->cost_kerja / $sortir->gr, 0) }}</td>
                        </tr>
                        <tr>
                            <td>{{ formatTglGaji($bulan, $tahun) }}</td>
                            <td>grading</td>
                            <td align="right">{{ 0 }}</td>
                            <td align="right">
                                {{ empty($operasional->total_operasional) ? 0 : number_format($operasional->total_operasional - $cabut->cost - $cetak->cost_kerja - $sortir->cost_kerja, 0) }}
                            </td>
                            <td align="right">{{ number_format($operasional->total_operasional ?? 0, 0) }}</td>
                            <td align="right">{{ number_format($grading->pcs, 0) }}</td>
                            <td align="right">{{ number_format($grading->gr, 0) }}</td>
                            <td align="right">
                                {{ empty($operasional->total_operasional) ? 0 : number_format(($operasional->total_operasional - $cabut->cost - $cetak->cost_kerja - $sortir->cost_kerja) / $grading->gr, 0) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

    </x-slot>
</x-theme.app>
