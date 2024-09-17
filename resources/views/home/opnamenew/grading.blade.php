<x-theme.app title="{{ $title }}" table="Y" sizeCard="11">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.opnamenew.nav')
            <div class="col-lg-12">
                <h5 for="" class="fw-bold text-decoration-underline">Pengiriman</h5>
                <table class="table table-bordered " id="bk_stock">
                    <thead>
                        <tr>
                            <th class="dhead">no</th>
                            <th class="dhead">Tanggal Pengiriman</th>
                            <th class="dhead">No Pengiriman</th>
                            <th class="dhead">Grade</th>
                            <th class="dhead text-end">pcs</th>
                            <th class="dhead text-end">gr</th>
                            <th class="dhead text-end">ttl rp bk</th>
                            <th class="dhead text-end">cost kerja</th>
                            <th class="dhead text-end">cost cu dll</th>
                            <th class="dhead text-end">cost operasional</th>
                            <th class="dhead text-end">ttl rp</th>
                            <th class="dhead text-end">rp/gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($box_stock as $b)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ tanggal($b->tgl_input) }}</td>
                                <td>{{ $b->no_barcode }}</td>
                                <td>{{ $b->grade }}</td>
                                <td class="text-end">{{ number_format($b->pcs, 0) }}</td>
                                <td class="text-end">{{ number_format($b->gr, 0) }}</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">{{ number_format($b->gr * 359.03) }}</td>
                                <td class="text-end">{{ number_format($b->gr * 2666.687696) }}</td>
                                <td class="text-end">{{ number_format($b->gr * 359.03 + $b->gr * 2666.687696) }}
                                </td>
                                <td class="text-end">0</td>
                            </tr>
                        @endforeach


                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="dheadstock ">Box : {{ count($box_stock) }}</th>
                            <th class="dheadstock "></th>
                            <th class="dheadstock "></th>
                            <th class="dheadstock "></th>
                            <th class="dheadstock  text-end">{{ number_format(sumBk($box_stock, 'pcs'), 0) }}</th>
                            <th class="dheadstock  text-end">{{ number_format(sumBk($box_stock, 'gr'), 0) }}</th>
                            <th class="dheadstock  text-end">0</th>
                            <th class="dheadstock  text-end">0
                            </th>
                            <th class="dheadstock  text-end">
                                {{ number_format(sumBk($box_stock, 'gr') * 359.03, 0) }}</th>
                            <th class="dheadstock  text-end">
                                {{ number_format(sumBk($box_stock, 'gr') * 2666.687696, 0) }}
                            </th>
                            <th class="dheadstock  text-end">
                                {{ number_format(sumBk($box_stock, 'gr') * 2666.687696 + sumBk($box_stock, 'gr') * 359.03, 0) }}
                            </th>
                            <th class="dheadstock  text-end">
                                0
                            </th>
                        </tr>
                    </tfoot>

                </table>
            </div>
            <div class="col-lg-12">
                <hr style="border: 1px solid black">
                <h5 for="" class="fw-bold text-decoration-underline">Sisa grading</h5>
                <table class="table table-bordered " id="bk_proses">
                    <thead>
                        <tr>
                            <th class="dhead">no</th>
                            <th class="dhead">box grading</th>
                            <th class="dhead">pengawas</th>
                            <th class="dhead">grade</th>
                            <th class="dhead text-end">pcs</th>
                            <th class="dhead text-end">gr</th>
                            <th class="dhead text-end">ttl rp bk</th>
                            <th class="dhead text-end">cost kerja</th>
                            <th class="dhead text-end">cost cu dll</th>
                            <th class="dhead text-end">cost operasional</th>
                            <th class="dhead text-end">ttl rp</th>
                            <th class="dhead text-end">rp/gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($box_proses as $b)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $b->box_pengiriman }}</td>
                                <td>{{ $b->admin }}</td>
                                <td>{{ $b->grade }}</td>
                                <td class="text-end">{{ number_format($b->pcs, 0) }}</td>
                                <td class="text-end">{{ number_format($b->gr, 0) }}</td>
                                <td class="text-end">0</td>
                                <td class="text-end">0</td>
                                <td class="text-end">{{ number_format($b->gr * 359.03, 0) }}</td>
                                <td class="text-end">{{ number_format($b->gr * 2666.687696, 0) }}</td>
                                <td class="text-end">0
                                </td>
                                <td class="text-end">
                                    0
                                </td>
                            </tr>
                        @endforeach


                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="dheadstock ">Box : {{ count($box_proses) }}</th>
                            <th class="dheadstock "></th>
                            <th class="dheadstock "></th>
                            <th class="dheadstock "></th>
                            <th class="dheadstock  text-end">{{ number_format(sumBk($box_proses, 'pcs'), 0) }}</th>
                            <th class="dheadstock  text-end">{{ number_format(sumBk($box_proses, 'gr'), 0) }}</th>
                            <th class="dheadstock  text-end">0</th>
                            <th class="dheadstock  text-end">0
                            </th>
                            <th class="dheadstock  text-end">
                                {{ number_format(sumBk($box_proses, 'gr') * 359.03, 0) }}</th>
                            <th class="dheadstock  text-end">
                                {{ number_format(sumBk($box_proses, 'gr') * 2666.687696, 0) }}
                            </th>
                            <th class="dheadstock  text-end">
                                0
                            </th>
                            <th class="dheadstock  text-end">
                                0
                            </th>
                        </tr>
                    </tfoot>

                </table>
            </div>
            <div class="col-lg-12">
                <hr style="border: 1px solid black">
                <h5 for="" class="fw-bold text-decoration-underline">Selisih</h5>
                <table class="table table-bordered " id="bk_selesai">
                    <thead>
                        <tr>
                            <th class="dhead">no</th>
                            <th class="dhead">partai</th>
                            <th class="dhead">pengawas</th>
                            <th class="dhead">no box</th>
                            <th class="dhead text-end">pcs</th>
                            <th class="dhead text-end">gr</th>
                            <th class="dhead text-end">ttl rp bk</th>
                            <th class="dhead text-end">cost kerja</th>
                            <th class="dhead text-end">cost cu dll</th>
                            <th class="dhead text-end">cost operasional</th>
                            <th class="dhead text-end">ttl rp</th>
                            <th class="dhead text-end">rp/gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($box_selesai as $b)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $b->nm_partai }}</td>
                                <td>{{ $b->name }}</td>
                                <td>{{ $b->no_box }}</td>
                                <td class="text-end">{{ number_format($b->pcs, 0) }}</td>
                                <td class="text-end">{{ number_format($b->gr, 0) }}</td>
                                <td class="text-end">{{ number_format($b->ttl_rp, 0) }}</td>
                                <td class="text-end">{{ number_format($b->cost_kerja, 0) }}</td>
                                <td class="text-end">0</td>
                                <td class="text-end">{{ number_format($b->cost_op, 0) }}</td>
                                <td class="text-end">{{ number_format($b->ttl_rp + $b->cost_kerja + $b->cost_op, 0) }}
                                </td>
                                <td class="text-end">
                                    {{ empty($b->no_box) ? 0 : number_format(($b->ttl_rp + $b->cost_kerja + $b->cost_op) / $b->gr, 0) }}
                                </td>
                            </tr>
                        @endforeach


                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="dheadstock ">Box : {{ count($box_selesai) }}</th>
                            <th class="dheadstock "></th>
                            <th class="dheadstock "></th>
                            <th class="dheadstock "></th>
                            <th class="dheadstock  text-end">{{ number_format(sumBk($box_selesai, 'pcs'), 0) }}</th>
                            <th class="dheadstock  text-end">{{ number_format(sumBk($box_selesai, 'gr'), 0) }}</th>
                            <th class="dheadstock  text-end">{{ number_format(sumBk($box_selesai, 'ttl_rp'), 0) }}
                            </th>
                            <th class="dheadstock  text-end">{{ number_format(sumBk($box_selesai, 'cost_kerja'), 0) }}
                            </th>
                            <th class="dheadstock  text-end">0</th>
                            <th class="dheadstock  text-end">{{ number_format(sumBk($box_selesai, 'cost_op'), 0) }}
                            </th>
                            <th class="dheadstock  text-end">
                                {{ number_format(sumBk($box_selesai, 'ttl_rp') + sumBk($box_selesai, 'cost_kerja') + sumBk($box_selesai, 'cost_op'), 0) }}
                            </th>
                            <th class="dheadstock  text-end">
                                {{ empty(sumBk($box_selesai, 'pcs')) ? 0 : number_format((sumBk($box_selesai, 'ttl_rp') + sumBk($box_selesai, 'cost_kerja') + sumBk($box_selesai, 'cost_op')) / sumBk($box_selesai, 'gr'), 0) }}
                            </th>
                        </tr>
                    </tfoot>

                </table>
            </div>
        </section>
        @section('scripts')
            <script>
                $('#bk_stock').DataTable({
                    "searching": true,
                    scrollY: '200px',
                    scrollX: false,
                    scrollCollapse: true,
                    "autoWidth": true,
                    "paging": false,
                    "info": false
                });
                $('#bk_proses').DataTable({
                    "searching": true,
                    scrollY: '200px',
                    scrollX: false,
                    scrollCollapse: true,
                    "autoWidth": true,
                    "paging": false,
                    "info": false
                });
                $('#bk_selesai').DataTable({
                    "searching": true,
                    scrollY: '200px',
                    scrollX: false,
                    scrollCollapse: true,
                    "autoWidth": true,
                    "paging": false,
                    "info": false
                });
            </script>
        @endsection
    </x-slot>
</x-theme.app>
