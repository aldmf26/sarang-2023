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
                        <td>Grading akhir</td>
                        <td class="text-end">{{ number_format($grading->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($grading->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($sortir_akhir->ttl_rp + $opname->ttl_rp, 0) }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold">{{ number_format($grading->pcs, 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($grading->gr, 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($sortir_akhir->ttl_rp + $opname->ttl_rp, 0) }}
                        </td>
                    </tr>

                    @php
                        $rp_gr = ($sortir_akhir->ttl_rp + $opname->ttl_rp) / $grading->gr;
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
                        <td style="background-color: #F7BAC5; color:white">Sudah dikirim</td>
                        <td class="text-end">{{ number_format($pengiriman->pcs, 0) }}</td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('opnamenew.grading') }}" target="_blank">
                                {{ number_format($pengiriman->gr, 0) }}
                            </a>
                        </td>
                        <td class="text-end">{{ number_format($rp_gr * $pengiriman->gr, 0) }}</td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5; color:white">Belum dikirim</td>
                        <td class="text-end">{{ number_format($belum_kirim->pcs, 0) }}</td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('opnamenew.grading') }}" target="_blank">
                                {{ number_format($belum_kirim->gr, 0) }}
                            </a>
                        </td>
                        <td class="text-end">{{ number_format($rp_gr * $belum_kirim->gr, 0) }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold">
                            {{ number_format($pengiriman->pcs + $belum_kirim->pcs, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format($pengiriman->gr + $belum_kirim->gr, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format($rp_gr * ($pengiriman->gr + $belum_kirim->gr), 0) }}
                        </td>
                    </tr>



                </table>
            </div>
        </section>








        @section('scripts')
        @endsection
    </x-slot>
</x-theme.app>
