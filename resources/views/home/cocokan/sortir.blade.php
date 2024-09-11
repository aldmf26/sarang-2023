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
                        <td>Akhir Cetak</td>
                        <td class="text-end">{{ number_format($akhir_cetak->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($akhir_cetak->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($akhir_cetak->ttl_rp, 0) }}</td>
                    </tr>
                    <tr>
                        <td>Opname</td>
                        <td class="text-end">{{ number_format($opname->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($opname->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($opname->ttl_rp, 0) }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold">{{ number_format($akhir_cetak->pcs + $opname->pcs, 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($akhir_cetak->gr + $opname->gr, 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($akhir_cetak->ttl_rp + $opname->ttl_rp, 0) }}
                        </td>
                    </tr>

                </table>
            </div>
            <div class="col-lg-8">
                <table class="table table-bordered">
                    <tr>
                        <th class="dhead">Ket</th>
                        <th class="dhead text-end">Pcs</th>
                        <th class="dhead">Gr</th>
                        <th class="dhead">Rp</th>
                    </tr>
                    <tr>
                        <td>Awal Sortir</td>
                        <td class="text-end">
                            {{ number_format($akhir_cetak->pcs + $opname->pcs - $sedang_proses->pcs - $sortir_sisa->pcs, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($akhir_cetak->gr + $opname->gr - $sedang_proses->gr - $sortir_sisa->gr, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($akhir_cetak->ttl_rp + $opname->ttl_rp - $sedang_proses->ttl_rp - $sortir_sisa->ttl_rp, 0) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #C7EA46">Akhir Sortir</td>
                        <td style="background-color: #C7EA46" class="text-end">
                            {{ number_format($sortir_akhir->pcs, 0) }}
                        </td>
                        <td style="background-color: #C7EA46" class="text-end">
                            {{ number_format($sortir_akhir->gr, 0) }}
                        </td>
                        <td style="background-color: #C7EA46" class="text-end">
                            {{ number_format($sortir_akhir->ttl_rp, 0) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7A829">Sedang Proses</td>
                        <td class="text-end">
                            {{ number_format($sedang_proses->pcs, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($sedang_proses->gr, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($sedang_proses->ttl_rp, 0) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7A829">Sisa Pengawas</td>
                        <td class="text-end">
                            {{ number_format($sortir_sisa->pcs, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($sortir_sisa->gr, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($sortir_sisa->ttl_rp, 0) }}
                        </td>
                    </tr>

                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold"></td>
                        <td class="text-end fw-bold"></td>
                        <td class="text-end fw-bold">
                            {{ number_format($sortir_akhir->ttl_rp + $sedang_proses->ttl_rp + $sortir_sisa->ttl_rp, 0) }}
                        </td>
                    </tr>



                </table>
            </div>
        </section>








        @section('scripts')
        @endsection
    </x-slot>
</x-theme.app>
