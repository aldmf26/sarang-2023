<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.cocokan.nav')

            <div class="col-lg-4">
                <div>
                    <h6>Bk Sinta</h6>
                </div>
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
                <div class="d-flex justify-content-between">
                    <h6>Cabut Kerja</h6>
                    <div>
                        <a href="{{route('cocokan.cabutExport')}}" class="btn btn-sm btn-primary"><i class="fas fa-file-excel"></i> Export</a>
                    </div>
                </div>
                <table class="table table-bordered mt-1">
                    <tr>
                        <th class="dhead">Ket</th>
                        <th class="dhead text-end">Pcs</th>
                        <th class="dhead text-end">Gr</th>
                        <th class="dhead text-end">Rp</th>
                        <th class="dhead text-end">Cost kerja</th>
                        <th class="dhead text-end">Total Rp</th>
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
                        <td class="text-end">
                            0
                        </td>
                        <td class="text-end">
                            {{ number_format($bk_awal->ttl_rp - $cbt_proses->ttl_rp - $cbt_sisa_pgws->ttl_rp, 0) }}
                        </td>
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

                        <td class="text-end">{{ number_format($bk_akhir->ttl_rp + $bk_akhir->cost_kerja, 0) }}</td>


                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5;color:white">Sedang proses</td>
                        <td class="text-end">{{ number_format($cbt_proses->pcs, 0) }}</td>
                        <td class="text-end fw-bold"><a target="_blank"
                                href="{{ route('detail.cabut.proses') }}">{{ number_format($cbt_proses->gr, 0) }}</a>
                        </td>
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
                        <td class="text-end">0</td>
                        <td class="text-end">{{ number_format($cbt_sisa_pgws->ttl_rp, 0) }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold">
                            {{ number_format($bk_akhir->pcs + $cbt_proses->pcs + $cbt_sisa_pgws->pcs, 0) }}</td>
                        <td class="text-end fw-bold">
                            {{ number_format($bk_akhir->gr + $cbt_proses->gr + $cbt_sisa_pgws->gr, 0) }}</td>
                        <td class="text-end fw-bold">
                            {{ number_format($cbt_sisa_pgws->ttl_rp + $cbt_proses->ttl_rp + $bk_akhir->ttl_rp, 0) }}
                        </td>
                        @php
                            $modal = $cbt_sisa_pgws->ttl_rp + $cbt_proses->ttl_rp + $bk_akhir->ttl_rp;

                        @endphp
                        <td class="text-end fw-bold">
                            {{ number_format($modal + $cbt_proses->cost_kerja + $bk_akhir->cost_kerja, 0) }}
                        </td>
                    </tr>

                </table>
            </div>


        </section>








        @section('scripts')
            <script>
                pencarian('tbl1input', 'tbl1')
            </script>
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
