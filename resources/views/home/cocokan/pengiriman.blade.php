<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.cocokan.nav')
            <div class="col-lg-3">
                <h6>{{ $title }}</h6>
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
                        <td class="text-end">
                            {{ number_format($grading->cost_bk + $grading->cost_kerja + $grading->cost_op, 0) }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold">{{ number_format($grading->pcs, 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($grading->gr, 0) }}</td>
                        <td class="text-end fw-bold">
                            {{ number_format($grading->cost_bk + $grading->cost_kerja + $grading->cost_op, 0) }}
                        </td>
                    </tr>

                    @php
                        $rp_gr = ($sortir_akhir->ttl_rp + $opname->ttl_rp) / $grading->gr;
                    @endphp


                </table>
            </div>
            <div class="col-lg-4">
                <h6>Pengiriman Kerja</h6>
                <table class="table table-bordered">
                    <tr>
                        <th class="dhead">Ket</th>
                        <th class="dhead text-end">Pcs</th>
                        <th class="dhead text-end">Gr</th>
                        <th class="dhead text-end">Rp</th>
                        <th class="dhead text-end">Cost kerja</th>
                        <th class="dhead text-end">Total Rp</th>
                        <th class="dhead text-end">Rata2</th>
                    </tr>
                    <tr>
                        <td style="background-color: #F7BAC5; color:white">Pengiriman</td>
                        <td class="text-end">{{ number_format($pengiriman->pcs, 0) }}</td>
                        <td class="text-end fw-bold">
                            <a href="{{ route('detail.pengiriman.awal') }}" target="_blank">
                                {{ number_format($pengiriman->gr, 0) }}
                            </a>
                        </td>
                        <td class="text-end">
                            {{ number_format($pengiriman->cost_bk + $pengiriman->cost_op + $pengiriman->cost_kerja + $pengiriman->cost_cu, 0) }}
                        </td>

                        <td class="text-end">0</td>
                        <td class="text-end">
                            {{ number_format($pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_cu + $pengiriman->cost_op, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format(($pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_cu + $pengiriman->cost_op) / $pengiriman->gr, 0) }}
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
                        <td class="text-end">
                            {{ number_format($belum_kirim->cost_bk + $belum_kirim->cost_kerja + $belum_kirim->cost_op, 0) }}
                        </td>
                        <td class="text-end">0</td>
                        <td class="text-end">
                            {{ number_format($belum_kirim->cost_op + $belum_kirim->cost_cu + $belum_kirim->cost_kerja + $belum_kirim->cost_bk, 0) }}
                        </td>
                        <td class="text-end">
                            {{ number_format(($belum_kirim->cost_op + $belum_kirim->cost_cu + $belum_kirim->cost_kerja + $belum_kirim->cost_bk) / $belum_kirim->gr, 0) }}
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
                            {{ number_format($belum_kirim->cost_op + $belum_kirim->cost_cu + $belum_kirim->cost_kerja + $belum_kirim->cost_bk + ($pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_cu + $pengiriman->cost_op), 0) }}
                        </td>
                        <td></td>
                        <td class="text-end fw-bold">
                            {{ number_format($belum_kirim->cost_op + $belum_kirim->cost_cu + $belum_kirim->cost_kerja + $belum_kirim->cost_bk + ($pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_cu + $pengiriman->cost_op), 0) }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format(($belum_kirim->cost_op + $belum_kirim->cost_cu + $belum_kirim->cost_kerja + $belum_kirim->cost_bk + ($pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_cu + $pengiriman->cost_op)) / ($pengiriman->gr + $belum_kirim->gr), 0) }}
                        </td>
                    </tr>



                </table>
            </div>
            <div class="col-lg-5">
                <div class="d-flex justify-content-between">
                    <h6>List Pengiriman</h6>
                    <div>
                        @include('home.cocokan.btn_export')
                    </div>
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">#</th>
                            <th class="dhead">Tgl Kirim</th>
                            <th class="dhead">No Packinglist</th>
                            <th class="dhead">Nama Packing List</th>
                            <th class="dhead">Tujuan</th>
                            <th class="dhead text-end">Box</th>
                            <th class="dhead text-end">Pcs</th>
                            <th class="dhead text-end">Gr</th>
                            <th class="dhead text-end">Gr + Kadar</th>
                            <th class="dhead text-end">Total Rp</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($list_pengiriman as $i => $d)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td style="white-space: nowrap">{{ tanggal($d->tgl) }}</td>
                                <td>PI {{ $d->no_nota }}</td>
                                <td>{{ ucwords($d->nm_packing) }}</td>
                                <td>{{ strtoupper($d->tujuan) }}</td>
                                <td align="center">{{ $d->ttl_box }}</td>
                                <td align="right">{{ number_format($d->pcs, 0) }}</td>
                                <td align="right"><a target="_blank"
                                        href="{{ route('detail.list_pengiriman', ['no_nota' => $d->no_nota]) }}">{{ number_format($d->gr, 0) }}</a>
                                </td>
                                <td align="right">{{ number_format($d->gr_naik, 0) }}</td>
                                <td align="right">
                                    {{ number_format($d->cost_bk + $d->cost_op + $d->cost_kerja + $d->cost_cu, 0) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6" class="fw-bold">Total</th>
                            <th class="text-end fw-bold">{{ number_format(sumBK($list_pengiriman, 'pcs'), 0) }}</th>
                            <th class="text-end fw-bold">{{ number_format(sumBK($list_pengiriman, 'gr'), 0) }}</th>
                            <th class="text-end fw-bold">{{ number_format(sumBK($list_pengiriman, 'gr_naik'), 0) }}
                            </th>
                            <th class="text-end fw-bold">
                                {{ number_format(sumBK($list_pengiriman, 'cost_bk') + sumBk($list_pengiriman, 'cost_op') + sumBK($list_pengiriman, 'cost_kerja') + sumBK($list_pengiriman, 'cost_cu'), 0) }}
                            </th>
                        </tr>
                    </tfoot>
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
