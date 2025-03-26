<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.cocokan.nav')
            <div class="col-lg-3">
                <h6>Grading akhir</h6>
                <table class="table table-bordered">
                    <tr>
                        <th class="dhead">Ket</th>
                        <th class="dhead text-end">pcs</th>
                        <th class="dhead text-end">gr</th>
                        <th class="dhead text-end">rp awal</th>
                        <th class="dhead text-end">rata2</th>
                    </tr>
                    <tr>
                        <td>Grading akhir</td>
                        <td class="text-end">{{ number_format($grading->pcs, 0) }}</td>
                        <td class="text-end ">
                            {{ number_format($grading->gr, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($grading->cost_bk + $grading->cost_kerja + $grading->cost_op, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format(($grading->cost_bk + $grading->cost_kerja + $grading->cost_op) / $grading->gr, 0) }}
                        </td>
                    </tr>


                </table>
            </div>
            <div class="col-lg-5">
                <h6>Kerja Wip1</h6>
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
                        <td>Wip1 Awal</td>
                        <td class="text-end">{{ number_format($grading->pcs - $sisa_belum_wip1->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($grading->gr - $sisa_belum_wip1->gr, 0) }}</td>
                        <td class="text-end">
                            {{ number_format($grading->cost_bk + $grading->cost_kerja + $grading->cost_op - $sisa_belum_wip1->ttl_rp, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format(($grading->cost_bk + $grading->cost_kerja + $grading->cost_op - $sisa_belum_wip1->ttl_rp) / ($grading->gr - $sisa_belum_wip1->gr), 0) }}
                        </td>
                        <td class="text-end">0</td>
                        <td class="text-end">
                            {{ number_format($grading->cost_bk + $grading->cost_kerja + $grading->cost_op - $sisa_belum_wip1->ttl_rp, 0) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5;color:white">Wip1 sedang proses</td>
                        <td class="text-end">{{ number_format($sisa_belum_wip1->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($sisa_belum_wip1->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($sisa_belum_wip1->ttl_rp, 0) }}</td>
                        <td class="text-end">{{ number_format($sisa_belum_wip1->ttl_rp / $sisa_belum_wip1->gr, 0) }}
                        </td>
                        <td class="text-end">0</td>
                        <td class="text-end">
                            {{ number_format($sisa_belum_wip1->ttl_rp, 0) }}
                        </td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <th class=" text-end">{{ number_format($grading->pcs, 0) }}</th>
                        <th class=" text-end">{{ number_format($grading->gr, 0) }}</th>
                        <th class=" text-end">
                            {{ number_format($grading->cost_bk + $grading->cost_kerja + $grading->cost_op, 0) }}
                        </th>
                        <th class=" text-end">
                            {{ number_format(($grading->cost_bk + $grading->cost_kerja + $grading->cost_op) / $grading->gr, 0) }}
                        </th>
                        <th class=" text-end">0</th>
                        <th class=" text-end">
                            {{ number_format($grading->cost_bk + $grading->cost_kerja + $grading->cost_op, 0) }}
                        </th>
                    </tr>







                </table>
            </div>
            <div class="col-lg-4">
                <div class="d-flex justify-content-between">
                    <h6> Wip1 akhir </h6>
                    <div>
                        @include('home.cocokan.btn_export', ['divisi' => 'grading'])

                    </div>
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th class="dhead">Ket</th>
                        <th class="dhead text-end">pcs</th>
                        <th class="dhead text-end">gr</th>
                        <th class="dhead text-end">total rp + cost kerja</th>
                        <th class="dhead text-end">rata2 rp + cost kerja</th>
                        <th class="dhead text-end">susut</th>

                    </tr>


                    <tr>
                        <td style="background-color: #F7F700;">Wip 1 akhir</td>
                        <td class="text-end">{{ number_format($wip1akhir->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($wip1akhir->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($wip1akhir->ttl_rp, 0) }}</td>
                        <td class="text-end">{{ number_format($wip1akhir->ttl_rp / $wip1akhir->gr, 0) }}</td>
                        <td>0</td>

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

                $(document).ready(function() {
                    $(document).on("change", ".bulan_op", function(e) {
                        e.preventDefault();
                        var id_oprasional = $(this).val();


                        $.ajax({
                            type: "get",
                            url: "{{ route('summary.get_operasional') }}",
                            data: {
                                id_oprasional: id_oprasional
                            },
                            success: function(response) {
                                $('#cost_opr').html(response);
                            }
                        });

                    });
                });
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
