<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.cocokan.nav')
            <Label>awal grading</Label>
            <div class="col-lg-3">
                <table class="table table-bordered">
                    <tr>
                        <th class="dhead">Ket</th>
                        <th class="dhead text-end">pcs</th>
                        <th class="dhead text-end">gr</th>
                        <th class="dhead text-end">rp awal</th>
                        <th class="dhead text-end">rata2</th>
                    </tr>
                    <tr>
                        <td>Akhir Sortir</td>
                        <td class="text-end">{{ number_format($sortir_akhir->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($sortir_akhir->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($sortir_akhir->ttl_rp, 0) }}</td>
                        <td class="text-end">{{ number_format($sortir_akhir->ttl_rp / $sortir_akhir->gr, 0) }}</td>
                    </tr>
                    <tr>
                        <td>Opname</td>
                        <td class="text-end">{{ number_format($opname->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($opname->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($opname->ttl_rp, 0) }}</td>
                        <td>0</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold">{{ number_format($sortir_akhir->pcs + $opname->pcs, 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($sortir_akhir->gr + $opname->gr, 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($sortir_akhir->ttl_rp + $opname->ttl_rp, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format(($sortir_akhir->ttl_rp + $opname->ttl_rp) / ($sortir_akhir->gr + $opname->gr), 0) }}
                        </td>
                    </tr>

                    @php
                        $rp_satuan = ($sortir_akhir->ttl_rp + $opname->ttl_rp) / ($sortir_akhir->gr + $opname->gr);
                    @endphp

                </table>
            </div>
            <div class="col-lg-5">
                <table class="table table-bordered">
                    <tr>
                        <th class="dhead">ket</th>
                        <th class="dhead text-end">pcs</th>
                        <th class="dhead text-end">gr</th>
                        <th class="dhead text-end">rp awal</th>
                        <th class="dhead text-end">rata2</th>
                        <th class="dhead text-end">cost kerja</th>
                        <th class="dhead text-end">total rp + cost kerja</th>

                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5;color:white">Awal Grading</td>
                        <td class="text-end">
                            {{ number_format($sortir_akhir->pcs + $opname->pcs - $grading_sisa->pcs, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('detail.grading.awal') }}" target="_blank">
                                {{ number_format($sortir_akhir->gr + $opname->gr - $grading_sisa->gr, 0) }}
                            </a>
                        </td>
                        <td class="text-end">
                            {{ number_format($sortir_akhir->ttl_rp + $opname->ttl_rp - $grading_sisa->cost_bk, 0) }}
                        </td>
                    </tr>

                    {{-- <tr>
                        <td style="background-color: #F7F700;">Akhir Grading</td>
                        <td class="text-end">{{ number_format($grading->pcs, 0) }}</td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('detail.grading.akhir') }}" target="_blank">
                                {{ number_format($grading->gr, 0) }}
                            </a>
                        </td>
                        <td class="text-end">{{ number_format($grading->cost_bk, 0) }}</td>
                        <td class="text-end">{{ number_format($grading->cost_kerja, 0) }}</td>
                        <td class="text-end">{{ number_format($grading->cost_cu, 0) }}</td>
                        <td class="text-end">{{ number_format($grading->cost_op, 0) }}</td>
                        <td class="text-end">
                            {{ number_format($grading->cost_op + $grading->cost_bk + $grading->cost_kerja + $grading->cost_cu, 0) }}
                        </td>
                    </tr> --}}
                    <tr>
                        <td style="background-color: #F7BAC5;color:white">Selisih pcs</td>
                        <td class="text-end text-danger fw-bold">
                            {{ number_format($sortir_akhir->pcs + $opname->pcs - $grading->pcs - $grading_sisa->pcs, 0) }}
                        </td>
                        <td class="text-end fw-bold text-danger ">
                            <a href="{{ route('opnamenew.grading') }}" target="_blank" class="text-danger">
                                0
                            </a>
                        </td>
                        <td class="text-end text-danger fw-bold">
                            0
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5;color:white">Sisa belum grading</td>
                        <td class="text-end">{{ number_format($grading_sisa->pcs, 0) }}</td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('detail.grading.sisa') }}" target="_blank">
                                {{ number_format($grading_sisa->gr, 0) }}
                            </a>
                        </td>
                        <td class="text-end">{{ number_format($grading_sisa->cost_bk, 0) }}</td>
                        <td class="text-end">{{ number_format($grading_sisa->cost_kerja, 0) }}</td>
                    </tr>



                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold">
                            {{ number_format($grading->pcs + $grading_sisa->pcs, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format($grading->gr + $grading_sisa->gr, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format($grading->cost_bk + $grading_sisa->cost_bk, 0) }}
                        </td>
                    </tr>



                </table>
            </div>
            <div class="col-lg-4">
                <table class="table table-bordered">
                    <tr>
                        <th class="dhead">ket</th>
                        <th class="dhead text-end">pcs</th>
                        <th class="dhead text-end">gr</th>
                        <th class="dhead text-end">rp awal</th>
                        <th class="dhead text-end">rata2</th>
                        <th class="dhead text-end">cost kerja</th>
                        <th class="dhead text-end">total rp + cost kerja</th>

                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5;color:white">Awal Grading</td>
                        <td class="text-end">
                            {{ number_format($grading->pcs + ($sortir_akhir->pcs + $opname->pcs - $grading->pcs), 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('detail.grading.awal') }}" target="_blank">
                                {{ number_format($grading->gr + ($sortir_akhir->gr + $opname->gr - $grading->gr), 0) }}
                            </a>
                        </td>
                        <td class="text-end">{{ number_format($sortir_akhir->ttl_rp + $opname->ttl_rp, 0) }}</td>
                    </tr>

                    <tr>
                        <td style="background-color: #F7F700;">Akhir Grading</td>
                        <td class="text-end">{{ number_format($grading->pcs, 0) }}</td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('detail.grading.akhir') }}" target="_blank">
                                {{ number_format($grading->gr, 0) }}
                            </a>
                        </td>
                        <td class="text-end">{{ number_format($grading->cost_bk, 0) }}</td>
                        <td class="text-end">{{ number_format($grading->cost_kerja, 0) }}</td>
                        <td class="text-end">{{ number_format($grading->cost_cu, 0) }}</td>
                        <td class="text-end">{{ number_format($grading->cost_op, 0) }}</td>
                        <td class="text-end">
                            {{ number_format($grading->cost_op + $grading->cost_bk + $grading->cost_kerja + $grading->cost_cu, 0) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5;color:white">Selisih pcs</td>
                        <td class="text-end text-danger fw-bold">
                            {{ number_format($sortir_akhir->pcs + $opname->pcs - $grading->pcs - $grading_sisa->pcs, 0) }}
                        </td>
                        <td class="text-end fw-bold text-danger ">
                            <a href="{{ route('opnamenew.grading') }}" target="_blank" class="text-danger">
                                0
                            </a>
                        </td>
                        <td class="text-end text-danger fw-bold">
                            0
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5;color:white">Sisa belum grading</td>
                        <td class="text-end">{{ number_format($grading_sisa->pcs, 0) }}</td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('detail.grading.sisa') }}" target="_blank">
                                {{ number_format($grading_sisa->gr, 0) }}
                            </a>
                        </td>
                        <td class="text-end">{{ number_format($grading_sisa->cost_bk, 0) }}</td>
                        <td class="text-end">{{ number_format($grading_sisa->cost_kerja, 0) }}</td>
                    </tr>



                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold">
                            {{ number_format($grading->pcs + $grading_sisa->pcs, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format($grading->gr + $grading_sisa->gr, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format($grading->cost_bk + $grading_sisa->cost_bk, 0) }}
                        </td>
                    </tr>



                </table>
            </div>
        </section>








        @section('scripts')
            <script>
                get_opr();

                function get_opr() {
                    $.ajax({
                        type: "get",
                        url: "{{ route('summary.get_operasional') }}",
                        success: function(response) {
                            $('#cost_opr').html(response);
                        }
                    });
                }
            </script>
            <script>
                function numberFormat(initialValue) {
                    return {
                        formattedNumber: new Intl.NumberFormat().format(initialValue),
                        formatNumber() {
                            // Hapus karakter non-digit dan simpan nomor mentah
                            let rawNumber = this.formattedNumber.replace(/\D/g, '');

                            // Format nomor dengan pemisah ribuan
                            this.formattedNumber = new Intl.NumberFormat().format(rawNumber);
                        }
                    };
                }
            </script>
        @endsection
    </x-slot>
</x-theme.app>
