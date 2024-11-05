<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.cocokan.nav')

            <div class="col-lg-3">
                <div>
                    <h6>Awal cetak</h6>
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th class="dhead">Ket</th>
                        <th class="dhead text-end">pcs</th>
                        <th class="dhead text-end">gr</th>
                        <th class="dhead text-end">rp awal</th>
                        <th class="dhead text-end">rata2</th>
                    </tr>
                    <tr>
                        <td>Akhir Cabut</td>
                        <td class="text-end">{{ number_format($akhir_cbt->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($akhir_cbt->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($akhir_cbt->ttl_rp, 0) }}</td>
                        <td class="text-end">{{ number_format($akhir_cbt->ttl_rp / $akhir_cbt->gr, 0) }}</td>
                    </tr>
                    <tr>
                        <td>Opname</td>
                        <td class="text-end">{{ number_format($ctk_opname->pcs, 0) }}</td>
                        <td class="text-end">{{ number_format($ctk_opname->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($ctk_opname->ttl_rp, 0) }}</td>
                        <td>0</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold">{{ number_format($ctk_opname->pcs + $akhir_cbt->pcs, 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($ctk_opname->gr + $akhir_cbt->gr, 0) }}</td>
                        <td class="text-end fw-bold">
                            {{ number_format($ctk_opname->ttl_rp + $akhir_cbt->ttl_rp, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format(($ctk_opname->ttl_rp + $akhir_cbt->ttl_rp) / ($ctk_opname->gr + $akhir_cbt->gr), 0) }}
                        </td>
                    </tr>

                </table>
            </div>
            <div class="col-lg-5">
                <div>
                    <h6>Awal cetak</h6>
                </div>
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
                        <td>Awal Cetak</td>
                        <td class="text-end">
                            {{ number_format($ctk_opname->pcs + $akhir_cbt->pcs - $cetak_proses->pcs - $cetak_sisa->pcs, 0) }}
                        </td>
                        <td class="text-end ">
                            <a href="{{ route('detail.cetak.cetak_awal') }}" target="_blank">
                                {{ number_format($ctk_opname->gr + $akhir_cbt->gr - $cetak_proses->gr - $cetak_sisa->gr, 0) }}
                            </a>
                        </td>
                        <td class="text-end ">
                            {{ number_format($ctk_opname->ttl_rp + $akhir_cbt->ttl_rp - $cetak_proses->ttl_rp - $cetak_sisa->ttl_rp, 0) }}
                        </td>
                        <td class="text-end ">
                            {{ number_format(($ctk_opname->ttl_rp + $akhir_cbt->ttl_rp - $cetak_proses->ttl_rp - $cetak_sisa->ttl_rp) / ($ctk_opname->gr + $akhir_cbt->gr - $cetak_proses->gr - $cetak_sisa->gr), 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($cetak_akhir->cost_kerja, 0) }}</td>
                        </td>
                        <td class="text-end">
                            {{ number_format($ctk_opname->ttl_rp + $akhir_cbt->ttl_rp - $cetak_proses->ttl_rp - $cetak_sisa->ttl_rp + $cetak_akhir->cost_kerja, 0) }}
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #F7BAC5;color:white">Sedang Proses</td>
                        <td class="text-end">{{ number_format($cetak_proses->pcs, 0) }}</td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('detail.cetak.proses') }}" target="_blank">
                                {{ number_format($cetak_proses->gr, 0) }}
                            </a>
                        </td>
                        <td class="text-end">{{ number_format($cetak_proses->ttl_rp, 0) }}</td>
                        <td class="text-end">{{ number_format($cetak_proses->ttl_rp / $cetak_proses->gr, 0) }}</td>
                        <td class="text-end">{{ number_format($cetak_proses->cost_kerja, 0) }}</td>
                        <td class="text-end">
                            {{ number_format($cetak_proses->cost_kerja + $cetak_proses->ttl_rp, 0) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5;color:white">Sisa Pengawas</td>
                        <td class="text-end">{{ number_format($cetak_sisa->pcs, 0) }}</td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('detail.cetak.sisa') }}" target="_blank">
                                {{ number_format($cetak_sisa->gr, 0) }}
                            </a>
                        </td>
                        <td class="text-end">{{ number_format($cetak_sisa->ttl_rp, 0) }}</td>
                        <td class="text-end">{{ number_format($cetak_sisa->ttl_rp / $cetak_sisa->gr, 0) }}</td>
                        <td class="text-end">0</td>
                        <td class="text-end">{{ number_format($cetak_sisa->ttl_rp, 0) }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold">
                            {{ number_format($cetak_akhir->pcs + $cetak_proses->pcs + $cetak_sisa->pcs, 0) }}</td>
                        <td class="text-end fw-bold">
                            {{ number_format($ctk_opname->gr + $akhir_cbt->gr - $cetak_proses->gr - $cetak_sisa->gr + $cetak_proses->gr + $cetak_sisa->gr, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format($cetak_akhir->ttl_rp + $cetak_proses->ttl_rp + $cetak_sisa->ttl_rp, 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format(($cetak_akhir->ttl_rp + $cetak_proses->ttl_rp + $cetak_sisa->ttl_rp) / ($ctk_opname->gr + $akhir_cbt->gr - $cetak_proses->gr - $cetak_sisa->gr + $cetak_proses->gr + $cetak_sisa->gr), 0) }}
                        </td>
                        @php
                            $modal = $cetak_akhir->ttl_rp + $cetak_proses->ttl_rp + $cetak_sisa->ttl_rp;
                        @endphp
                        <td class="text-end fw-bold">
                            0
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format($modal + $cetak_akhir->cost_kerja + $cetak_proses->cost_kerja, 0) }}
                        </td>
                    </tr>


                </table>
            </div>
            <div class="col-lg-4">
                <div>
                    <h6>Akhir cetak</h6>
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th class="dhead">ket</th>
                        <th class="dhead text-end">pcs</th>
                        <th class="dhead text-end">gr</th>
                        <th class="dhead text-end">total rp + cost kerja</th>
                        <th class="dhead text-end">rata2 rp + cost kerja</th>
                        <th class="dhead text-end">susut</th>
                    </tr>

                    <tr>
                        <td style="background-color: #F7F700">Akhir Cetak</td>
                        <td class="text-end">
                            {{ number_format($cetak_akhir->pcs, 0) }}</td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('detail.cetak.cetak_akhir') }}" target="_blank">
                                {{ number_format($cetak_akhir->gr, 0) }}
                            </a>
                        </td>
                        <td class="text-end">
                            {{ number_format($cetak_akhir->ttl_rp + $cetak_akhir->cost_kerja, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format(($cetak_akhir->ttl_rp + $cetak_akhir->cost_kerja) / $cetak_akhir->gr, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format((1 - $cetak_akhir->gr / ($ctk_opname->gr + $akhir_cbt->gr - $cetak_proses->gr - $cetak_sisa->gr)) * 100, 0) }}
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
