<div class="row">
    <div class="d-flex justify-content-end mb-2">
        <a class="btn btn-sm btn-primary" href="{{ route('laporanakhir.export_partai', $nm_partai) }}" target="_blank"><i
                class="fas fa-file-excel"></i> Export</a>
        <a href="/tes">tes</a>
    </div>
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-bordered " id="tblPartai">
                <thead>
                    <tr>
                        <th class="dhead " rowspan="2">#</th>
                        <th class="dhead" rowspan="2">partai</th>
                        <th class="dhead" rowspan="2">no box</th>
                        <th class="text-center dhead" colspan="3">BK</th>
                        <th class="text-center dhead" colspan="4">Cabut</th>
                        <th class="text-center dhead" colspan="4">Eox/Eol</th>
                        <th class="text-center dhead" colspan="4">Cetak</th>
                        <th class="text-center dhead" colspan="4">Sortir</th>
                        <th class="dhead text-end" rowspan="2">Cost bk</th>
                        <th class="dhead text-end" rowspan="2">Cost cbt</th>
                        <th class="dhead text-end" rowspan="2">Cost eox/eol</th>
                        <th class="dhead text-end" rowspan="2">Cost ctk</th>
                        <th class="dhead text-end" rowspan="2">Cost sortir</th>
                        {{-- <th class="dhead text-end" rowspan="2">Cost cu</th> --}}
                        <th class="dhead text-end" rowspan="2">Cost dll</th>
                        <th class="dhead text-end" rowspan="2">Cost oprasional</th>
                        <th class="dhead text-end" rowspan="2">Total Rp</th>
                    </tr>
                    <tr>
                        <th class="dhead text-end">pcs awal</th>
                        <th class="dhead text-end">gr awal</th>
                        <th class="dhead text-end">rp/gr</th>

                        <th class="dhead text-end">pcs akhir</th>
                        <th class="dhead text-end">gr akhir</th>
                        <th class="dhead text-end">rp/gr</th>
                        <th class="dhead text-end">susut%</th>

                        <th class="dhead text-end">pcs akhir</th>
                        <th class="dhead text-end">gr akhir</th>
                        <th class="dhead text-end">rp/gr</th>
                        <th class="dhead text-end">susut%</th>

                        <th class="dhead text-end">pcs akhir</th>
                        <th class="dhead text-end">gr akhir</th>
                        <th class="dhead text-end">rp/gr</th>
                        <th class="dhead text-end">susut%</th>

                        <th class="dhead text-end">pcs akhir</th>
                        <th class="dhead text-end">gr akhir</th>
                        <th class="dhead text-end">rp/gr</th>
                        <th class="dhead text-end">susut%</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($partai as $no => $p)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>
                                {{ $p->nm_partai }}
                            </td>
                            <td>
                                {{ $p->no_box ?? '-' }}
                            </td>
                            <td class="text-end">{{ number_format($p->pcs_awal ?? 0, 0) }}</td>
                            <td class="text-end">{{ number_format($p->gr_awal ?? 0, 0) }}</td>
                            <td class="text-end">{{ number_format($p->hrga_satuan ?? 0, 0) }}</td>

                            <td class="text-end">{{ number_format($p->pcs_cbt ?? 0, 0) }}</td>
                            <td class="text-end">{{ number_format($p->gr_cbt ?? 0, 0) }}</td>
                            <td class="text-end">
                                {{ $p->hrga_satuan == 0 || empty($p->gr_cbt) ? 0 : number_format(($p->cost_bk + $p->cost_cbt) / $p->gr_cbt, 0) }}
                            </td>
                            <td class="text-end">{{ number_format($p->sst_cbt ?? 0) }} %</td>

                            <td class="text-end">0</td>
                            <td class="text-end">{{ number_format($p->gr_eo ?? 0, 0) }}</td>
                            <td class="text-end">
                                {{ number_format($p->rp_gram_eo ?? 0, 0) }}</td>
                            <td class="text-end">{{ number_format($p->sst_eo ?? 0, 0) }} %</td>

                            <td class="text-end">{{ number_format($p->pcs_ctk ?? 0, 0) }}</td>
                            <td class="text-end">{{ number_format($p->gr_ctk ?? 0, 0) }} {{$p->gr_awl_ctk ?? 0}}</td>
                            <td class="text-end">
                                {{ $p->hrga_satuan == 0 || empty($p->gr_ctk) ? 0 : number_format(($p->cost_bk + $p->cost_cbt + $p->cost_ctk) / $p->gr_ctk, 0) }}
                            </td>
                            
                            <td class="text-end">{{ number_format($p->sst_ctk ?? 0, 0) }} %</td>

                            <td class="text-end">{{ number_format($p->pcs_str ?? 0, 0) }}</td>
                            <td class="text-end">{{ number_format($p->gr_str ?? 0, 0) }}</td>
                            <td class="text-end">
                                {{ $p->hrga_satuan == 0 || empty($p->gr_str) ? 0 : number_format(($p->cost_bk + $p->cost_cbt + $p->cost_ctk + $p->cost_str) / $p->gr_str, 0) }}
                            </td>
                            <td class="text-end">{{ number_format($p->sst_str, 0) }} %</td>

                            <td class="text-end">{{ number_format($p->cost_bk, 0) }}</td>
                            <td class="text-end">{{ number_format($p->cost_cbt, 0) }}</td>
                            <td class="text-end">{{ number_format($p->cost_eo, 0) }}</td>
                            <td class="text-end">{{ number_format($p->cost_ctk, 0) }}</td>
                            <td class="text-end">{{ number_format($p->cost_str, 0) }}</td>
                            {{-- <td class="text-end">{{ number_format($p->cost_cu, 0) }}</td> --}}
                            <td class="text-end">
                                {{ number_format($p->harian_cbt + $p->harian_ctk + $p->harian_str + $p->harian_eo, 0) }}
                                @php
                                    $harian = $p->harian_cbt + $p->harian_ctk + $p->harian_str + $p->harian_eo;
                                    $cost_oprasional =
                                        $p->oprasional_cbt +
                                        $p->oprasional_ctk +
                                        $p->oprasional_str +
                                        $p->oprasional_eo;

                                @endphp
                            </td>
                            <td class="text-end">
                                {{ number_format($cost_oprasional, 0) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($cost_oprasional + $p->cost_bk + $p->cost_cbt + $p->cost_ctk + $p->cost_str  + $harian + $p->cost_eo, 0) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
