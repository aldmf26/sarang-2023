<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.cocokan.nav')
            <Label>{{ $title }}</Label>
            <div class="col-lg-4">
                <table class="table table-bordered">
                    <tr>
                        <th class="dhead">Ket</th>
                        <th class="dhead text-end">Pcs</th>
                        <th class="dhead text-end">Gr</th>
                        <th class="dhead text-end">Rp</th>
                    </tr>
                    <tr>
                        <td>Akhir Sortir</td>
                        <td class="text-end">{{ number_format($sortir_akhir->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($sortir_akhir->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($sortir_akhir->ttl_rp, 0) }}</td>
                    </tr>
                    <tr>
                        <td>Opname</td>
                        <td class="text-end">{{ number_format($opname->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($opname->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($opname->ttl_rp, 0) }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold">{{ number_format($sortir_akhir->pcs + $opname->pcs, 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($sortir_akhir->gr + $opname->gr, 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($sortir_akhir->ttl_rp + $opname->ttl_rp, 0) }}
                        </td>
                    </tr>

                    @php
                        $rp_satuan = ($sortir_akhir->ttl_rp + $opname->ttl_rp) / ($sortir_akhir->gr + $opname->gr);
                    @endphp

                </table>
            </div>
            <div class="col-lg-8">
                <table class="table table-bordered">
                    <tr>
                        <th class="dhead">Ket</th>
                        <th class="dhead text-end">Pcs</th>
                        <th class="dhead text-end">Gr</th>
                        <th class="dhead text-end">Rp</th>
                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5; color:white">Pengiriman</td>
                        <td class="text-end">{{ number_format($pengiriman->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($pengiriman->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($rp_satuan * $pengiriman->gr, 0) }}</td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5;color:white">Sisa Grading</td>
                        <td class="text-end">{{ number_format($grading->pcs - $pengiriman->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($grading->gr - $pengiriman->gr, 0) }}</td>
                        <td class="text-end">{{ number_format(($grading->gr - $pengiriman->gr) * $rp_satuan, 0) }}</td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5;color:white">Selisih</td>
                        <td class="text-end">{{ number_format($sortir_akhir->pcs + $opname->pcs - $grading->pcs, 0) }}
                        </td>
                        <td class="text-end">{{ number_format($sortir_akhir->gr + $opname->gr - $grading->gr, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format(($sortir_akhir->gr + $opname->gr - $grading->gr) * $rp_satuan, 0) }}
                        </td>
                    </tr>


                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold">
                            {{ number_format($sortir_akhir->pcs + $opname->pcs, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format($sortir_akhir->gr + $opname->gr, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format(($sortir_akhir->gr + $opname->gr) * $rp_satuan, 0) }}
                        </td>
                    </tr>



                </table>
            </div>
        </section>








        @section('scripts')
        @endsection
    </x-slot>
</x-theme.app>
