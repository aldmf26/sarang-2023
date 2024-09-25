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
                        <th class="dhead text-end">Cost dll,cu,denda</th>
                        <th class="dhead text-end">Cost operasional</th>
                    </tr>
                    <tr>
                        <td>Cabut awal</td>
                        <td class="text-end">
                            {{ number_format($bk_awal->pcs - $cbt_proses->pcs - $cbt_sisa_pgws->pcs, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('detail.cabut.cabut_awal') }}" target="_blank">
                                {{ number_format($bk_awal->gr - $cbt_proses->gr - $cbt_sisa_pgws->gr, 0) }}</a>
                        </td>
                        <td class="text-end">
                            {{ number_format($bk_awal->ttl_rp - $cbt_proses->ttl_rp - $cbt_sisa_pgws->ttl_rp, 0) }}
                        </td>
                        <td></td>
                        <td></td>
                        <td class="text-end"></td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7F700">Cabut akhir</td>
                        <td class="text-end">{{ number_format($bk_akhir->pcs, 0) }}
                        </td>
                        <td class="text-end fw-bold"><a target="_blank"
                                href="{{ route('detail.cabut.cabut_akhir') }}">{{ number_format($bk_akhir->gr, 0) }}</a>
                        </td>
                        <td class="text-end">
                            {{ number_format($bk_akhir->ttl_rp, 0) }}</td>
                        <td class="text-end">
                            {{ number_format($bk_akhir->cost_kerja, 0) }}</td>

                        <td class="text-end">{{ number_format(($cost_dll / $ttl_gr) * $bk_akhir->gr, 0) }}</td>
                        <td class="text-end">{{ number_format(($cost_op / $ttl_gr) * $bk_akhir->gr, 0) }}</td>

                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5;color:white">Sedang proses</td>
                        <td class="text-end">{{ number_format($cbt_proses->pcs, 0) }}</td>
                        <td class="text-end fw-bold"><a target="_blank"
                                href="{{ route('detail.cabut.proses') }}">{{ number_format($cbt_proses->gr, 0) }}</a></td>
                        <td class="text-end">{{ number_format($cbt_proses->ttl_rp, 0) }}</td>
                        <td class="text-end">{{ number_format($cbt_proses->cost_kerja, 0) }}</td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5;color:white">Sisa pengawas</td>
                        <td class="text-end">{{ number_format($cbt_sisa_pgws->pcs, 0) }}</td>
                        <td class="text-end fw-bold"><a
                                href="{{ route('detail.cabut.sisa') }}">{{ number_format($cbt_sisa_pgws->gr, 0) }}</a>
                        </td>
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
                        @php
                            $modal = $cbt_sisa_pgws->ttl_rp + $cbt_proses->ttl_rp + $bk_akhir->ttl_rp;
                            $cost_dll = ($cost_dll / $ttl_gr) * $bk_akhir->gr;
                            $cost_op = ($cost_op / $ttl_gr) * $bk_akhir->gr;
                        @endphp
                        <td class="text-end fw-bold">
                            {{ number_format($modal + $cost_dll + $cost_op + $cbt_proses->cost_kerja + $bk_akhir->cost_kerja, 0) }}
                        </td>
                    </tr>

                </table>
            </div>
        </section>








        @section('scripts')
        @endsection
    </x-slot>
</x-theme.app>
