<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6>{{ $title }}</h6>
            <div>
                <a class="btn btn-sm btn-info" href="{{ route('gradingbj.print_grading', $no_invoice) }}"><i class="fas fa-print"></i> Print</a>
            </div>
        </div>
    </x-slot>

    <x-slot name="cardBody">
        <form action="{{ route('gradingbj.createUlang') }}" method="post">
            @csrf
            <div class="row">
                <div class="col-lg-6">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="dhead">Pengawas</th>
                                <th class="dhead" width="150">No Nota</th>
                                <th class="dhead">Tgl</th>
                            </tr>

                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $admin }}</td>
                                <td>{{ $no_invoice }}</td>
                                <td>{{ tanggal($tgl) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <h6>Box Dipilih <span class="text-success">Partai : {{ $nm_partai }} </span></h6>
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="dhead">No Box</th>
                                <th class="dhead text-center">Tipe</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Rp/gr</th>
                                <th class="dhead text-end">Total Rp</th>
                            </tr>
                        </thead>
                        <thead class="bg-white">
                            <tr>
                                <th>Total</th>
                                <th></th>
                                <th class="text-end">{{ number_format(sumBk($box_grading, 'pcs'), 0) }}</th>
                                <th class="text-end">{{ number_format(sumBk($box_grading, 'gr'), 0) }}</th>
                                <th class="text-end"></th>

                                @php
                                    $total_rp =
                                        sumBk($box_grading, 'cost_bk') +
                                        sumBk($box_grading, 'cost_ctk') +
                                        sumBk($box_grading, 'cost_sortir') +
                                        sumBk($box_grading, 'cost_eo') +
                                        sumBk($box_grading, 'cost_cbt');
                                @endphp
                                <th class="text-end">
                                    {{ number_format($total_rp, 0) }}
                                </th>
                            </tr>

                        </thead>
                        <tbody>
                            @foreach ($box_grading as $b)
                                <tr>
                                    <td>{{ $b->no_box_sortir }}</td>
                                    <td class="text-center">{{ $b->tipe }}</td>
                                    <td class="text-end">{{ $b->pcs }}</td>
                                    <td class="text-end">{{ $b->gr }}</td>
                                    @php
                                        $total =
                                            $b->cost_bk + $b->cost_cbt + $b->cost_ctk + $b->cost_eo + $b->cost_sortir;
                                    @endphp
                                    <td class="text-end">{{ number_format($total / $b->gr, 0) }}</td>

                                    <td class="text-end">{{ number_format($total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @php
                    $rp_satuan =
                        ($total_rp - sumBk($grading_susut, 'gr') * $rp_susut->rp_susut) / sumBk($grading, 'gr');
                @endphp
                <div class="col-lg-6">
                    <h6>Hasil Grading</h6>
                    <table class="table table-bordered">
                        <thead>

                            <tr>
                                <th class="dhead">Grade</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead ">Box Sp</th>
                                <th class="dhead">Rp/gr</th>
                                <th class="dhead">Total Rp</th>
                            </tr>
                            <tr>
                                <td class="fw-bold">Total</td>
                                <td class="fw-bold text-end">{{ number_format(sumBk($grading, 'pcs'), 0) }}</td>
                                <td class=" fw-bold text-end">{{ number_format(sumBk($grading, 'gr'), 0) }}</td>
                                <td class=" fw-bold "></td>
                                <td class="fw-bold"></td>
                                <td class="fw-bold text-end">{{ number_format(sumBk($grading, 'gr') * $rp_satuan, 0) }}
                                </td>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($grading as $g)
                                <tr>
                                    <td>{{ $g->grade }}</td>
                                    <td class="text-end">{{ number_format($g->pcs, 0) }}</td>
                                    <td class="text-end">{{ number_format($g->gr, 0) }}</td>
                                    <td class="text-end">{{ $g->box_pengiriman }}</td>
                                    <td class="text-end">{{ number_format($rp_satuan, 0) }}</td>
                                    <td class="text-end">{{ number_format($g->gr * $rp_satuan, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <h6>Susut :
                        {{ number_format((1 - sumBk($grading, 'gr') / sumBk($box_grading, 'gr')) * 100, 1) }}%
                    </h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="dhead">Grade</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Box Sp</th>
                                <th class="dhead text-end">Rp/Gr</th>
                                <th class="dhead text-end">Total Rp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($grading_susut as $g)
                                <tr>
                                    <td>{{ $g->grade }}</td>
                                    <td class="text-end">{{ number_format($g->pcs, 0) }}</td>
                                    <td class="text-end">{{ number_format($g->gr, 0) }}</td>
                                    <td class="text-end">{{ $g->box_pengiriman }}</td>
                                    <td class="text-end">{{ number_format($rp_susut->rp_susut, 0) }}</td>
                                    <td class="text-end">{{ number_format($g->gr * $rp_susut->rp_susut, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>


        </form>
        @section('scripts')
            <script>
                clickSelectInput('form-control')
            </script>
        @endsection
    </x-slot>
</x-theme.app>
