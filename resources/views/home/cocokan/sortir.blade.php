<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.cocokan.nav')
            <div class="col-lg-3">
                <h6> Sortir awal </h6>
                <table class="table table-bordered">
                    <tr>
                        <th class="dhead">ket</th>
                        <th class="dhead text-end">pcs</th>
                        <th class="dhead text-end">gr</th>
                        <th class="dhead text-end">rp awal</th>
                        <th class="dhead text-end">rata2</th>
                    </tr>
                    <tr>
                        <td>Cetak akhir </td>
                        <td class="text-end">{{ number_format($akhir_cetak->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($akhir_cetak->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($akhir_cetak->ttl_rp, 0) }}</td>
                        <td class="text-end">{{ number_format($akhir_cetak->ttl_rp / $akhir_cetak->gr, 0) }}</td>
                    </tr>
                    <tr>
                        <td>Opname</td>
                        <td class="text-end">{{ number_format($opname->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($opname->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($opname->ttl_rp, 0) }}</td>
                        <td class="text-end">0</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold">{{ number_format($akhir_cetak->pcs + $opname->pcs, 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($akhir_cetak->gr + $opname->gr, 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($akhir_cetak->ttl_rp + $opname->ttl_rp, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format($akhir_cetak->ttl_rp / $akhir_cetak->gr, 0) }}
                        </td>
                    </tr>

                </table>
            </div>
            <div class="col-lg-5">
                <h6>Sortir Kerja</h6>
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
                        <td>Sortir awal</td>
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
                            {{ number_format(($akhir_cetak->ttl_rp + $opname->ttl_rp - $sedang_proses->ttl_rp - $sortir_sisa->ttl_rp) / ($akhir_cetak->gr + $opname->gr - $sedang_proses->gr - $sortir_sisa->gr), 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($sortir_akhir->cost_kerja, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($akhir_cetak->ttl_rp + $opname->ttl_rp - $sedang_proses->ttl_rp - $sortir_sisa->ttl_rp + $sortir_akhir->cost_kerja, 0) }}
                        </td>
                    </tr>
                    {{-- <tr>
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
                    </tr> --}}
                    <tr>
                        <td style="background-color: #F7BAC5;color:white">Sortir sedang proses</td>
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
                            {{ empty($sedang_proses->gr) ? 0 : number_format($sedang_proses->ttl_rp / $sedang_proses->gr, 0) }}
                        </td>
                        <td class="text-end">
                            0
                        </td>
                        <td class="text-end">
                            {{ number_format($sedang_proses->ttl_rp, 0) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5;color:white">Sortir sisa pengawas</td>
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
                            {{ empty($sortir_sisa->gr) ? 0 : number_format($sortir_sisa->ttl_rp / $sortir_sisa->gr, 0) }}
                        </td>
                        <td class="text-end">
                            0
                        </td>
                        <td class="text-end">
                            {{ number_format($sortir_sisa->ttl_rp, 0) }}
                        </td>
                    </tr>

                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold">
                            {{ number_format($sortir_akhir->pcs + $sedang_proses->pcs + $sortir_sisa->pcs, 0) }}</td>
                        <td class="text-end fw-bold">
                            {{ number_format($akhir_cetak->gr + $opname->gr - $sedang_proses->gr - $sortir_sisa->gr + $sedang_proses->gr + $sortir_sisa->gr, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format($sortir_akhir->ttl_rp + $sedang_proses->ttl_rp + $sortir_sisa->ttl_rp, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format(($sortir_akhir->ttl_rp + $sedang_proses->ttl_rp + $sortir_sisa->ttl_rp) / ($akhir_cetak->gr + $opname->gr - $sedang_proses->gr - $sortir_sisa->gr + $sedang_proses->gr + $sortir_sisa->gr), 0) }}
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
            <div class="col-lg-4">
                <div class="d-flex justify-content-between">
                    <h6> Sortir akhir </h6>
                    <div>
                        @include('home.cocokan.btn_export', ['divisi' => 'sortir'])

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
                        <td style="background-color: #F7F700">Sortir akhir </td>
                        <td class="text-end">
                            {{ number_format($sortir_akhir->pcs, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('detail.sortir.sortir_akhir') }}" target="_blank">
                                {{ number_format($sortir_akhir->gr, 0) }}
                            </a>
                        </td>
                        <td class="text-end">{{ number_format($sortir_akhir->ttl_rp + $sortir_akhir->cost_kerja) }}
                        </td>
                        <td class="text-end">
                            {{ number_format(($sortir_akhir->ttl_rp + $sortir_akhir->cost_kerja) / $sortir_akhir->gr, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format((1 - $sortir_akhir->gr / ($akhir_cetak->gr + $opname->gr - $sedang_proses->gr - $sortir_sisa->gr)) * 100, 0) }}
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
