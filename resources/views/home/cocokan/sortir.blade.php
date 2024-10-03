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
                        <th class="dhead text-end">Gr</th>
                        <th class="dhead text-end">Rp</th>
                        <th class="dhead text-end">Cost kerja</th>
                        <th class="dhead text-end">Total rp</th>

                    </tr>
                    <tr>
                        <td>Awal Sortir</td>
                        <td class="text-end">
                            {{ number_format($akhir_cetak->pcs + $opname->pcs - $sedang_proses->pcs - $sortir_sisa->pcs, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('detail.sortir.sortir_awal') }}" target="_blank">
                                {{ number_format($akhir_cetak->gr + $opname->gr - $sedang_proses->gr - $sortir_sisa->gr, 0) }}
                            </a>
                        </td>
                        <td class="text-end">
                            {{ number_format($akhir_cetak->ttl_rp + $opname->ttl_rp - $sedang_proses->ttl_rp - $sortir_sisa->ttl_rp, 0) }}
                        </td>
                        <td class="text-end">
                            0
                        </td>
                        <td class="text-end">
                            0
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7F700">Akhir Sortir</td>
                        <td class="text-end">
                            {{ number_format($sortir_akhir->pcs, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('detail.sortir.sortir_akhir') }}" target="_blank">
                                {{ number_format($sortir_akhir->gr, 0) }}
                            </a>
                        </td>
                        <td class="text-end">
                            {{ number_format($sortir_akhir->ttl_rp, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($sortir_akhir->cost_kerja, 0) }}
                        </td>
                        <td class="text-end">{{ number_format($sortir_akhir->ttl_rp + $sortir_akhir->cost_kerja) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5;color:white">Sedang Proses</td>
                        <td class="text-end">
                            {{ number_format($sedang_proses->pcs, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('detail.sortir.proses') }}" target="_blank">
                                {{ number_format($sedang_proses->gr, 0) }}
                            </a>
                        </td>
                        <td class="text-end">
                            {{ number_format($sedang_proses->ttl_rp, 0) }}
                        </td>
                        <td class="text-end">
                            0
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5;color:white">Sisa Pengawas</td>
                        <td class="text-end">
                            {{ number_format($sortir_sisa->pcs, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('detail.sortir.sisa') }}" target="_blank">
                                {{ number_format($sortir_sisa->gr, 0) }}
                            </a>
                        </td>
                        <td class="text-end">
                            {{ number_format($sortir_sisa->ttl_rp, 0) }}
                        </td>
                        <td class="text-end">
                            0
                        </td>
                    </tr>

                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold"></td>
                        <td class="text-end fw-bold"></td>
                        <td class="text-end fw-bold">
                            {{ number_format($sortir_akhir->ttl_rp + $sedang_proses->ttl_rp + $sortir_sisa->ttl_rp, 0) }}
                        </td>
                        @php
                            $modal = $sortir_akhir->ttl_rp + $sedang_proses->ttl_rp + $sortir_sisa->ttl_rp;
                            $cost_dll = ($cost_dll / $ttl_gr) * $sortir_akhir->gr;
                            $cost_op = ($cost_op / $ttl_gr) * $sortir_akhir->gr;
                        @endphp
                        <td class="text-end fw-bold">
                            {{ number_format($sortir_akhir->cost_kerja, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format($modal + $sortir_akhir->cost_kerja, 0) }}
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
