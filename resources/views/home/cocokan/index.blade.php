<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.cocokan.nav')
            <Label>Bk dari sinta</Label>
            <div class="col-lg-4">
                <table class="table table-bordered">
                    <tr>
                        <th class="dhead text-end">Pcs</th>
                        <th class="dhead text-end">Gr</th>
                        <th class="dhead text-end">Rp</th>
                    </tr>
                    <tr>
                        <td class="text-end">{{ number_format($bk_awal->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($bk_awal->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($bk_awal->ttl_rp, 0) }}</td>
                    </tr>

                </table>
            </div>
            <div class="col-lg-8">
                <table class="table table-bordered">
                    <tr>
                        <th class="dhead">Ket</th>
                        <th class="dhead text-end">Pcs</th>
                        <th class="dhead text-end">Gr</th>
                        <th class="dhead text-end">Rp</th>
                        <th class="dhead text-end">Cost kerja</th>
                    </tr>
                    <tr>
                        <td>Cabut awal</td>
                        <td class="text-end">
                            {{ number_format($bk_awal->pcs - $cbt_proses->pcs - $cbt_sisa_pgws->pcs, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($bk_awal->gr - $cbt_proses->gr - $cbt_sisa_pgws->gr, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($bk_awal->ttl_rp - $cbt_proses->ttl_rp - $cbt_sisa_pgws->ttl_rp, 0) }}
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="background-color: #C7EA46">Cabut akhir</td>
                        <td style="background-color: #C7EA46" class="text-end">{{ number_format($bk_akhir->pcs, 0) }}
                        </td>
                        <td style="background-color: #C7EA46" class="text-end">{{ number_format($bk_akhir->gr, 0) }}
                        </td>
                        <td style="background-color: #C7EA46" class="text-end">
                            {{ number_format($bk_akhir->ttl_rp, 0) }}</td>
                        <td style="background-color: #C7EA46" class="text-end">
                            {{ number_format($bk_akhir->cost_kerja, 0) }}</td>

                    </tr>
                    <tr>
                        <td style="background-color: #F7A829">Sedang proses</td>
                        <td class="text-end">{{ number_format($cbt_proses->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($cbt_proses->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($cbt_proses->ttl_rp, 0) }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7A829">Sisa pengawas</td>
                        <td class="text-end">{{ number_format($cbt_sisa_pgws->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($cbt_sisa_pgws->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($cbt_sisa_pgws->ttl_rp, 0) }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold"></td>
                        <td class="text-end fw-bold"></td>
                        <td class="text-end fw-bold">
                            {{ number_format($cbt_sisa_pgws->ttl_rp + $cbt_proses->ttl_rp + $bk_akhir->ttl_rp, 0) }}
                        </td>
                        <td></td>
                    </tr>

                </table>
            </div>
        </section>








        @section('scripts')
        @endsection
    </x-slot>
</x-theme.app>
