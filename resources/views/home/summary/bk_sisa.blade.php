<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="row">
            @include('home.summary.nav')
            <div class="col-lg-6">
                <h6 class="float-start mt-1">Summary</h6>
            </div>
            <div class="col-lg-12"></div>

        </div>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <style>
                .scroll-container {
                    width: 1500px;
                    height: 100%;
                    overflow-x: auto;
                    overflow-y: hidden;
                    white-space: nowrap;
                }

                .scroll-container>div {
                    display: inline-block;
                    width: 450px;
                    /* Sesuaikan lebar elemen anak */
                    vertical-align: top;
                }
            </style>

            <div class="scroll-container">
                <label for="">Box Awal</label>
                <br>
                <div class="me-4">
                    <div style="width:450px;">
                        <div style="overflow-y: scroll; height: 350px">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="dhead text-center" colspan="5">box stock cabut sedang proses</th>
                                    </tr>
                                    <tr>
                                        <th class="dhead">Nama Partai</th>
                                        <th class="dhead text-end">Pcs</th>
                                        <th class="dhead text-end">Gr</th>
                                        <th class="dhead text-end">Rp/gr</th>
                                        <th class="text-end dhead">Ttl Rp</th>
                                    </tr>
                                    <tr>
                                        <th class="dheadstock">Total</th>
                                        <th class="dheadstock text-end">
                                            {{ number_format(array_sum(array_column($box_cabut_sedang_proses, 'pcs')), 0) }}
                                        </th>
                                        <th class="dheadstock text-end">
                                            {{ number_format(array_sum(array_column($box_cabut_sedang_proses, 'gr')), 0) }}
                                        </th>
                                        <th class="dheadstock text-end">
                                            {{ number_format(array_sum(array_column($box_cabut_sedang_proses, 'ttl_rp')) / array_sum(array_column($box_cabut_sedang_proses, 'gr')), 0) }}
                                        </th>
                                        <th class="text-end dheadstock">
                                            {{ number_format(array_sum(array_column($box_cabut_sedang_proses, 'ttl_rp')), 0) }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($box_cabut_sedang_proses as $b)
                                        <tr>
                                            <td>{{ $b->nm_partai }}</td>
                                            <td class="text-end">{{ number_format($b->pcs, 0) }}</td>
                                            <td class="text-end">{{ number_format($b->gr, 0) }}</td>
                                            <td class="text-end">{{ number_format($b->gr, 0) }}</td>
                                            <td class="text-end">{{ number_format($b->ttl_rp, 0) }}</td>
                                        </tr>
                                    @endforeach


                                </tbody>



                            </table>
                        </div>
                    </div>
                </div>
                <div class="me-4">
                    <div style="width:450px;">
                        <div style="overflow-y: scroll; height: 350px">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="dhead text-center" colspan="5">box selesai cabut siap cetak belum
                                            serah</th>
                                    </tr>
                                    <tr>
                                        <th class="dhead">Nama Partai</th>
                                        <th class="dhead text-end">Pcs</th>
                                        <th class="dhead text-end">Gr</th>
                                        <th class="dhead text-end">Rp/gr</th>
                                        <th class="text-end dhead">Ttl Rp</th>
                                    </tr>
                                    <tr>
                                        <th class="dheadstock">Total</th>
                                        <th class="dheadstock text-end">
                                            {{ number_format(array_sum(array_column($box_cabut_belum_serah, 'pcs')), 0) }}
                                        </th>
                                        <th class="dheadstock text-end">
                                            {{ number_format(array_sum(array_column($box_cabut_belum_serah, 'gr')), 0) }}
                                        </th>
                                        <th class="dheadstock text-end">
                                            {{ number_format(array_sum(array_column($box_cabut_belum_serah, 'ttl_rp')) / array_sum(array_column($box_cabut_belum_serah, 'gr')), 0) }}
                                        </th>
                                        <th class="text-end dheadstock">
                                            {{ number_format(array_sum(array_column($box_cabut_belum_serah, 'ttl_rp')), 0) }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($box_cabut_belum_serah as $b)
                                        <tr>
                                            <td>{{ $b->nm_partai }}</td>
                                            <td class="text-end">{{ number_format($b->pcs, 0) }}</td>
                                            <td class="text-end">{{ number_format($b->gr, 0) }}</td>
                                            <td class="text-end">{{ number_format($b->gr, 0) }}</td>
                                            <td class="text-end">{{ number_format($b->ttl_rp, 0) }}</td>
                                        </tr>
                                    @endforeach


                                </tbody>



                            </table>
                        </div>
                    </div>
                </div>
                <div class="me-4">
                    <div style="width:450px;">
                        <div style="overflow-y: scroll; height: 350px">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="dhead text-center" colspan="5">box selesai cbt siap sortir belum
                                            serah</th>
                                    </tr>
                                    <tr>
                                        <th class="dhead">Nama Partai</th>
                                        <th class="dhead text-end">Pcs</th>
                                        <th class="dhead text-end">Gr</th>
                                        <th class="dhead text-end">Rp/gr</th>
                                        <th class="text-end dhead">Ttl Rp</th>
                                    </tr>
                                    <tr>
                                        <th class="dheadstock">Total</th>
                                        <th class="dheadstock text-end">
                                            0
                                        </th>
                                        <th class="dheadstock text-end">
                                            {{ number_format(array_sum(array_column($bkselesai_siap_str, 'gr')), 0) }}
                                        </th>
                                        <th class="dheadstock text-end">
                                            {{ number_format(array_sum(array_column($bkselesai_siap_str, 'ttl_rp')) / array_sum(array_column($bkselesai_siap_str, 'gr')), 0) }}
                                        </th>
                                        <th class="text-end dheadstock">
                                            {{ number_format(array_sum(array_column($bkselesai_siap_str, 'ttl_rp')), 0) }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bkselesai_siap_str as $b)
                                        <tr>
                                            <td>{{ $b->nm_partai }}</td>
                                            <td class="text-end">0</td>
                                            <td class="text-end">{{ number_format($b->gr, 0) }}</td>
                                            <td class="text-end">{{ number_format($b->gr, 0) }}</td>
                                            <td class="text-end">{{ number_format($b->ttl_rp, 0) }}</td>
                                        </tr>
                                    @endforeach


                                </tbody>



                            </table>
                        </div>
                    </div>
                </div>
            </div>



        </section>
    </x-slot>
</x-theme.app>
