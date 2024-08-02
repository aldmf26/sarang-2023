<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="row">
            @include('home.gudang.nav')
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }}</h6>
            </div>
            <div class="col-lg-6">
                <a class="btn btn-sm btn-primary float-end" href="{{ route('gudang.export') }}"><i
                        class="fas fa-print"></i>Export All</a>
            </div>
        </div>

    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-4">
                <table id="table2" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="dhead">No</th>
                            <th class="dhead">Nama Partai</th>
                            <th class="dhead text-end">Pcs</th>
                            <th class="dhead text-end">Gr</th>
                            <th class="dhead text-end">Rp/gr</th>
                            <th class="dhead text-end">Total Rp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bksinta as $b)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $b->nm_partai }}</td>
                                <td class="text-end">{{ number_format($b->pcs, 0) }}</td>
                                <td class="text-end">{{ number_format($b->gr) }}</td>
                                <td class="text-end">{{ number_format($b->ttl_rp / $b->gr, 0) }}</td>
                                <td class="text-end">{{ number_format($b->ttl_rp, 0) }}</td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            <div class="col-lg-8">
                <table id="table" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="dhead">No</th>
                            <th class="dhead">Nama Partai</th>
                            <th class="dhead">Lokasi</th>
                            <th class="dhead text-end">Pcs</th>
                            <th class="dhead text-end">Gr</th>
                            <th class="dhead text-end">Rp/gr</th>
                            <th class="dhead text-end">Cost bk</th>
                            <th class="dhead text-end">Cost cbt</th>
                            <th class="dhead text-end">Cost eo</th>
                            <th class="dhead text-end">Cost ctk</th>
                            <th class="dhead text-end">Cost str</th>
                            <th class="dhead text-end">Cost cu</th>
                            <th class="dhead text-end">Cost Oprasional</th>
                            <th class="dhead text-end">Total Rp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($bksinta as $b)
                            @php
                                $bk_stock = \App\Models\TotalanModel::bkstock($b->nm_partai);
                                $bk_proses = \App\Models\TotalanModel::bksedang_proses($b->nm_partai);
                                $bk_selesai_siap_ctk = \App\Models\TotalannewModel::bkselesai_siap_ctk($b->nm_partai);
                                $bk_selesai_siap_str = \App\Models\TotalanModel::bkselesai_siap_str($b->nm_partai);

                                $cetak_stok = \App\Models\TotalanModel::cetak_stok($b->nm_partai);
                                $cetak_proses = \App\Models\TotalanModel::cetak_proses($b->nm_partai);
                                $cetak_selesai = \App\Models\TotalannewModel::cetak_selesai($b->nm_partai);

                                $stock_sortir = \App\Models\TotalanModel::stock_sortir($b->nm_partai);
                                $sortir_proses = \App\Models\TotalanModel::sortir_proses($b->nm_partai);
                                $sortir_selesai = \App\Models\TotalanModel::sortir_selesai($b->nm_partai);
                                $grading_stock = \App\Models\TotalannewModel::grading_stock($b->nm_partai);
                                // $box_belum_kirim = \App\Models\TotalanModel::box_belum_kirim($b->nm_partai);
                            @endphp
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $b->nm_partai }}</td>
                                <td>Box Stok</td>
                                <td class="text-end">{{ number_format($bk_stock->pcs ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($bk_stock->gr ?? 0, 0) }}</td>
                                <td class="text-end">
                                    {{ empty($bk_stock->gr) ? 0 : number_format($bk_stock->ttl_rp / $bk_stock->gr, 0) }}
                                </td>
                                <td class="text-end">{{ number_format($bk_stock->ttl_rp ?? 0, 0) }}</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">{{ number_format($bk_stock->cost_cu ?? 0, 0) }}</td>
                                <td class="text-end">0</td>
                                <td class="text-end">{{ number_format($bk_stock->ttl_rp ?? 0, 0) }}</td>
                            </tr>
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $b->nm_partai }}</td>
                                <td>Box sedang proses</td>
                                <td class="text-end">{{ number_format($bk_proses->pcs ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($bk_proses->gr ?? 0, 0) }}</td>
                                <td class="text-end">
                                    {{ empty($bk_proses->gr) ? 0 : number_format($bk_proses->ttl_rp / $bk_proses->gr, 0) }}
                                </td>
                                <td class="text-end">{{ number_format($bk_proses->ttl_rp ?? 0, 0) }}</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">{{ number_format($bk_stock->cost_cu ?? 0, 0) }}</td>
                                <td class="text-end">0</td>
                                <td class="text-end">{{ number_format($bk_proses->ttl_rp ?? 0, 0) }}</td>
                            </tr>
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $b->nm_partai }}</td>
                                <td>Box selesai siap cetak</td>
                                <td class="text-end">{{ number_format($bk_selesai_siap_ctk->pcs ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($bk_selesai_siap_ctk->gr ?? 0, 0) }}</td>
                                <td class="text-end">
                                    {{ empty($bk_selesai_siap_ctk->gr) ? 0 : number_format($bk_selesai_siap_ctk->ttl_rp / $bk_selesai_siap_ctk->gr, 0) }}
                                </td>
                                <td class="text-end">{{ number_format($bk_selesai_siap_ctk->cost_bk ?? 0, 0) }} </td>
                                <td class="text-end">{{ number_format($bk_selesai_siap_ctk->cost_cbt ?? 0, 0) }}</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">{{ number_format($bk_selesai_siap_ctk->cost_cu ?? 0, 0) }}
                                </td>
                                <td class="text-end">{{ number_format($bk_selesai_siap_ctk->cost_op_cbt ?? 0, 0) }}
                                </td>
                                <td class="text-end">{{ number_format($bk_selesai_siap_ctk->ttl_rp ?? 0, 0) }}</td>
                            </tr>
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $b->nm_partai }}</td>
                                <td>Box selesai siap sortir</td>
                                <td class="text-end">0</td>
                                <td class="text-end">{{ number_format($bk_selesai_siap_str->gr ?? 0, 0) }} </td>
                                <td class="text-end">
                                    {{ empty($bk_selesai_siap_str->gr) ? 0 : number_format($bk_selesai_siap_str->ttl_rp / $bk_selesai_siap_str->gr, 0) }}
                                </td>
                                <td class="text-end">{{ number_format($bk_selesai_siap_str->cost_bk ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($bk_selesai_siap_str->cost_cbt ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($bk_selesai_siap_str->cost_eo ?? 0, 0) }}</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">{{ number_format($bk_selesai_siap_str->cost_cu ?? 0, 0) }}
                                </td>
                                <td class="text-end">{{ number_format($bk_selesai_siap_str->cost_op_cbt ?? 0, 0) }}
                                </td>
                                <td class="text-end">{{ number_format($bk_selesai_siap_str->ttl_rp ?? 0, 0) }}</td>
                            </tr>
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $b->nm_partai }}</td>
                                <td>Cetak Stok</td>
                                <td class="text-end">{{ number_format($cetak_stok->pcs ?? 0, 0) }} </td>
                                <td class="text-end">{{ number_format($cetak_stok->gr ?? 0, 0) }}</td>
                                <td class="text-end">
                                    {{ empty($cetak_stok->gr) ? 0 : number_format($cetak_stok->ttl_rp / $cetak_stok->gr, 0) }}
                                </td>
                                <td class="text-end">{{ number_format($cetak_stok->cost_bk ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($cetak_stok->cost_cbt ?? 0, 0) }}</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">{{ number_format($cetak_stok->cost_cu ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($cetak_stok->cost_op ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($cetak_stok->ttl_rp ?? 0, 0) }}</td>
                            </tr>
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $b->nm_partai }}</td>
                                <td>Cetak sedang proses</td>
                                <td class="text-end">{{ number_format($cetak_proses->pcs ?? 0, 0) }} </td>
                                <td class="text-end">{{ number_format($cetak_proses->gr ?? 0, 0) }}</td>
                                <td class="text-end">
                                    {{ empty($cetak_proses->gr) ? 0 : number_format($cetak_proses->ttl_rp / $cetak_proses->gr, 0) }}
                                </td>
                                <td class="text-end">{{ number_format($cetak_proses->cost_bk ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($cetak_proses->cost_cbt ?? 0, 0) }}</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">{{ number_format($cetak_proses->cost_cu ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($cetak_proses->cost_op ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($cetak_proses->ttl_rp ?? 0, 0) }}</td>
                            </tr>
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $b->nm_partai }}</td>
                                <td>Cetak selesai siap sortir</td>
                                <td class="text-end">{{ number_format($cetak_selesai->pcs ?? 0, 0) }} </td>
                                <td class="text-end">{{ number_format($cetak_selesai->gr ?? 0, 0) }}</td>
                                <td class="text-end">
                                    {{ empty($cetak_selesai->gr) ? 0 : number_format($cetak_selesai->ttl_rp / $cetak_selesai->gr, 0) }}
                                </td>
                                <td class="text-end">{{ number_format($cetak_selesai->cost_bk ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($cetak_selesai->cost_cbt ?? 0, 0) }}</td>
                                <td class="text-end">0</td>
                                <td class="text-end">{{ number_format($cetak_selesai->cost_ctk ?? 0, 0) }}</td>
                                <td class="text-end">0</td>
                                <td class="text-end">{{ number_format($cetak_selesai->cost_cu ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($cetak_selesai->cost_op ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($cetak_selesai->ttl_rp ?? 0, 0) }}</td>
                            </tr>
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $b->nm_partai }}</td>
                                <td>Sortir Stok</td>
                                <td class="text-end">{{ number_format($stock_sortir->pcs ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($stock_sortir->gr ?? 0, 0) }}</td>
                                <td class="text-end">
                                    {{ empty($stock_sortir->gr) ? 0 : number_format($stock_sortir->ttl_rp / $stock_sortir->gr, 0) }}
                                </td>
                                <td class="text-end">{{ number_format($stock_sortir->cost_bk ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($stock_sortir->cost_cbt ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($stock_sortir->cost_eo ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($stock_sortir->cost_ctk ?? 0, 0) }}</td>
                                <td class="text-end">0</td>
                                <td class="text-end">{{ number_format($stok_sortir->cost_cu ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($stok_sortir->cost_op ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($stock_sortir->ttl_rp ?? 0, 0) }}</td>
                            </tr>
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $b->nm_partai }}</td>
                                <td>Sortir sedang proses</td>
                                <td class="text-end">{{ number_format($sortir_proses->pcs ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($sortir_proses->gr ?? 0, 0) }}</td>
                                <td class="text-end">
                                    {{ empty($sortir_proses->gr) ? 0 : number_format($sortir_proses->ttl_rp / $sortir_proses->gr, 0) }}
                                </td>
                                <td class="text-end">{{ number_format($sortir_proses->cost_bk ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($sortir_proses->cost_cbt ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($sortir_proses->cost_eo ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($sortir_proses->cost_ctk ?? 0, 0) }}</td>
                                <td class="text-end">0</td>
                                <td class="text-end">{{ number_format($sortir_proses->cost_cu ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($sortir_proses->cost_op ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($sortir_proses->ttl_rp ?? 0, 0) }}</td>
                            </tr>
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $b->nm_partai }}</td>
                                <td>Sortir selesai siap grading</td>
                                <td class="text-end">{{ number_format($sortir_selesai->pcs ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($sortir_selesai->gr ?? 0, 0) }}</td>
                                <td class="text-end">
                                    {{ empty($sortir_selesai->gr) ? 0 : number_format($sortir_selesai->ttl_rp / $sortir_selesai->gr, 0) }}
                                </td>
                                <td class="text-end">{{ number_format($sortir_selesai->cost_bk ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($sortir_selesai->cost_cbt ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($sortir_selesai->cost_eo ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($sortir_selesai->cost_ctk ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($sortir_selesai->cost_str ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($sortir_selesai->cost_cu ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($sortir_selesai->cost_op ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($sortir_selesai->ttl_rp ?? 0, 0) }}</td>
                            </tr>
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $b->nm_partai }}</td>
                                <td>Grading Stock</td>
                                <td class="text-end">{{ number_format($grading_stock->pcs ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($grading_stock->gr ?? 0, 0) }}</td>
                                <td class="text-end">
                                    {{ empty($grading_stock->gr) ? 0 : number_format($grading_stock->ttl_rp / $grading_stock->gr, 0) }}
                                </td>
                                <td class="text-end">{{ number_format($grading_stock->cost_bk ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($grading_stock->cost_cbt ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($grading_stock->cost_eo ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($grading_stock->cost_ctk ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($grading_stock->cost_str ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($grading_stock->cost_cu ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($grading_stock->cost_op ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($grading_stock->ttl_rp ?? 0, 0) }}</td>
                            </tr>
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $b->nm_partai }}</td>
                                <td>Box belum kirim</td>
                                <td class="text-end">{{ number_format($box_belum_kirim->pcs ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($box_belum_kirim->gr ?? 0, 0) }}</td>
                                <td class="text-end">
                                    {{ empty($box_belum_kirim->gr) ? 0 : number_format($box_belum_kirim->ttl_rp / $box_belum_kirim->gr, 0) }}
                                </td>
                                <td class="text-end">{{ number_format($box_belum_kirim->cost_bk ?? 0, 0) }}</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">{{ number_format($box_belum_kirim->ttl_rp ?? 0, 0) }}</td>
                            </tr>
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $b->nm_partai }}</td>
                                <td>Box selesai kirim</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>





        </section>
    </x-slot>
    @section('scripts')
        <script>
            ["tbl1", "tbl2", "tbl3", "tbl4", "tbl5", "tbl6", "tgbl7", "tbl8", "tbl9", "tbl10"].forEach((tbl, i) => pencarian(
                `tbl${i+1}input`, tbl));
        </script>
    @endsection
</x-theme.app>
