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
                        <td>Akhir Cabut</td>
                        <td class="text-end">{{ number_format($akhir_cbt->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($akhir_cbt->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($akhir_cbt->ttl_rp, 0) }}</td>
                    </tr>
                    <tr>
                        <td>Opname</td>
                        <td class="text-end">{{ number_format($ctk_opname->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($ctk_opname->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($ctk_opname->ttl_rp, 0) }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold">{{ number_format($ctk_opname->pcs + $akhir_cbt->pcs, 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($ctk_opname->gr + $akhir_cbt->gr, 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($ctk_opname->ttl_rp + $akhir_cbt->ttl_rp, 0) }}
                        </td>
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
                        <td>Awal Cetak</td>
                        <td class="text-end">
                            {{ number_format($ctk_opname->pcs + $akhir_cbt->pcs - $cetak_proses->pcs - $cetak_sisa->pcs, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($ctk_opname->gr + $akhir_cbt->gr - $cetak_proses->gr - $cetak_sisa->gr, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($ctk_opname->ttl_rp + $akhir_cbt->ttl_rp - $cetak_proses->ttl_rp - $cetak_sisa->ttl_rp, 0) }}
                        </td>
                        <td class="text-end">0</td>
                        <td class="text-end">0</td>
                        <td class="text-end">0</td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7F700">Akhir Cetak</td>
                        <td class="text-end">
                            {{ number_format($cetak_akhir->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($cetak_akhir->gr, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($cetak_akhir->ttl_rp, 0) }}</td>
                        <td class="text-end">
                            {{ number_format($cetak_akhir->cost_kerja, 0) }}</td>
                        <td class="text-end">{{ number_format(($cost_dll / $ttl_gr) * $cetak_akhir->gr, 0) }}</td>
                        <td class="text-end">{{ number_format(($cost_op / $ttl_gr) * $cetak_akhir->gr, 0) }}</td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5;color:white">Sedang Proses</td>
                        <td class="text-end">{{ number_format($cetak_proses->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($cetak_proses->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($cetak_proses->ttl_rp, 0) }}</td>
                        <td class="text-end">{{ number_format($cetak_proses->cost_kerja, 0) }}</td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5;color:white">Sisa Pengawas</td>
                        <td class="text-end">{{ number_format($cetak_sisa->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($cetak_sisa->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($cetak_sisa->ttl_rp, 0) }}</td>
                        <td class="text-end">0</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold"></td>
                        <td class="text-end fw-bold"></td>
                        <td class="text-end fw-bold">
                            {{ number_format($cetak_akhir->ttl_rp + $cetak_proses->ttl_rp + $cetak_sisa->ttl_rp, 0) }}
                        </td>
                        @php
                            $modal = $cetak_akhir->ttl_rp + $cetak_proses->ttl_rp + $cetak_sisa->ttl_rp;
                            $cost_dll = ($cost_dll / $ttl_gr) * $cetak_akhir->gr;
                            $cost_op = ($cost_op / $ttl_gr) * $cetak_akhir->gr;
                        @endphp
                        <td class="text-end fw-bold">
                            {{ number_format($modal + $cost_dll + $cost_op + $cetak_akhir->cost_kerja + $cetak_proses->cost_kerja, 0) }}
                        </td>
                    </tr>


                </table>
            </div>
        </section>








        @section('scripts')
        @endsection
    </x-slot>
</x-theme.app>
