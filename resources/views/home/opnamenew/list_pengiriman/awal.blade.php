<x-theme.app title="{{ $title }}" table="Y" sizeCard="11">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>

    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.opnamenew.nav')

            <div class="col-lg-12">

                <h5 for="" class="fw-bold text-decoration-underline">{{ $title }}</h5>

                @include('home.opnamenew.nav_pengiriman_detail')

                <table class="table table-bordered " id="bk_stock">
                    <thead>
                        <tr>
                            <th class="dhead">no</th>
                            <th class="dhead">No Nota</th>
                            <th class="dhead">partai</th>
                            <th class="dhead">tipe</th>
                            <th class="dhead">box grading</th>
                            <th class="dhead">grade</th>
                            <th class="dhead text-end">pcs</th>
                            <th class="dhead text-end">gr</th>
                            <th class="dhead text-end">ttl rp</th>
                            <th class="dhead text-end">rata2</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sumTtlRp = 0;
                        @endphp
                        @foreach ($query as $d)
                            @php
                                $ttlRp = $d->ttl_rp;
                                $sumTtlRp += $ttlRp;
                                $total = $d->cost_bk + $d->cost_kerja + $d->cost_cu + $d->cost_op;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $no_nota }}</td>
                                <td>{{ $d->nm_partai }}</td>
                                <td>{{ $d->tipe }}</td>
                                <td>{{ $d->no_box }}</td>
                                <td>{{ $d->grade }}</td>
                                <td class="text-end">{{ number_format($d->pcs, 0) }}</td>
                                <td class="text-end">{{ number_format($d->gr) }}</td>
                                <td class="text-end">{{ number_format($total) }}</td>
                                <td class="text-end">{{ number_format($total / $d->gr) }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <th class="dheadstock showChart" colspan="3">Box : {{ count($query) }}</th>
                            <th class="dheadstock  text-end">{{ number_format(sumBk($query, 'pcs'), 0) }}</th>
                            <th class="dheadstock  text-end">{{ number_format(sumBk($query, 'gr'), 0) }}</th>

                            @php
                                $sumTtlRp =
                                    sumBk($query, 'cost_bk') +
                                    sumBk($query, 'cost_kerja') +
                                    sumBk($query, 'cost_cu') +
                                    sumBk($query, 'cost_op');
                            @endphp

                            <th class="dheadstock  text-end ">{{ number_format($sumTtlRp, 0) }}</th>
                            <th class="dheadstock  text-end">{{ number_format($sumTtlRp / sumBk($query, 'gr'), 0) }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </section>

        <canvas id="myChart" class="d-none"></canvas>


        @section('scripts')
            <script>
                $('.showChart').click(function(e) {
                    e.preventDefault();
                    $('#myChart').toggleClass('d-none');
                });

                const data = {
                    labels: @json(array_column($query, 'nm_partai')),
                    datasets: [{
                        label: 'Ttl Rp',
                        backgroundColor: 'rgba(255, 99, 132, 0.3)',
                        borderColor: 'rgb(255, 99, 132)',
                        data: @json(array_map(fn($d) => $d->cost_bk + $d->cost_kerja + $d->cost_cu + $d->cost_op, $query)),
                        yAxisID: 'y',
                    }, {
                        label: 'Rata Rp',
                        backgroundColor: 'rgba(0, 255, 0, 0.3)',
                        borderColor: 'rgb(0, 255, 0)',
                        data: @json(array_map(fn($d) => ($d->cost_bk + $d->cost_kerja + $d->cost_cu + $d->cost_op) / $d->gr, $query)),
                        pointStyle: 'star',
                        pointBackgroundColor: 'rgb(0, 0, 255)',
                        pointBorderColor: 'rgb(0, 0, 255)',
                        pointBorderWidth: 5,
                        yAxisID: 'y1',
                    }]
                };
                const config = {
                    type: 'line',
                    data: data,
                    options: {
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Ttl Rp'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Rata Rp'
                                },
                                grid: {
                                    drawOnChartArea: false
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: `Rata2 Tertinggi : ${Math.max(...data.datasets[1].data).toFixed()}`
                            }
                        }
                    }
                };
                const myChart = new Chart(
                    document.getElementById('myChart'),
                    config
                );
            </script>

            <script>
                $('#bk_stock').DataTable({
                    "searching": true,
                    scrollY: '500px',
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
