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
                        <td class="text-end">{{ number_format($grading->cost_bk, 0) }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold">{{ number_format($grading->pcs, 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($grading->gr, 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($grading->cost_bk, 0) }}
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
                        <th class="dhead text-end">Cost kerja</th>
                        <th class="dhead text-end">Cost cu</th>
                        <th class="dhead text-end">Cost operasional</th>
                        <th class="dhead text-end">Total Rp</th>
                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5; color:white">Pengiriman</td>
                        <td class="text-end">{{ number_format($pengiriman->pcs, 0) }}</td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('detail.pengiriman.awal') }}" target="_blank">
                                {{ number_format($pengiriman->gr, 0) }}
                            </a>
                        </td>
                        <td class="text-end">{{ number_foat($pengiriman->cost_bk, 0) }}</td>
                        <td class="text-end">{{ number_format($pengiriman->cost_kerja, 0) }}</td>
                        <td class="text-end">{{ number_format($pengiriman->cost_cu, 0) }}</td>
                        <td class="text-end">{{ number_format($pengiriman->cost_op, 0) }}</td>
                        <td class="text-end">
                            {{ number_format($pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_cu + $pengiriman->cost_op, 0) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5; color:white">Sisa belum kirim</td>
                        <td class="text-end">{{ number_format($belum_kirim->pcs, 0) }}</td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('detail.pengiriman.sisa') }}" target="_blank">
                                {{ number_format($belum_kirim->gr, 0) }}
                            </a>
                        </td>
                        <td class="text-end">{{ number_format($belum_kirim->cost_bk, 0) }}</td>
                        <td class="text-end">{{ number_format($belum_kirim->cost_kerja, 0) }}</td>
                        <td class="text-end">{{ number_format($belum_kirim->cost_cu, 0) }}</td>
                        <td class="text-end">{{ number_format($belum_kirim->cost_op, 0) }}</td>
                        <td class="text-end">
                            {{ number_format($belum_kirim->cost_op + $belum_kirim->cost_cu + $belum_kirim->cost_kerja + $belum_kirim->cost_bk, 0) }}
                        </td>
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
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-end fw-bold">
                            {{ number_format($belum_kirim->cost_op + $belum_kirim->cost_cu + $belum_kirim->cost_kerja + $belum_kirim->cost_bk + ($pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_cu + $pengiriman->cost_op), 0) }}
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
