<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="row">


        </div>

    </x-slot>

    <x-slot name="cardBody">
        <table class="mb-2" border="2">
            <tr>
                <td>A=(B2/B1)+C</td>
            </tr>
            <tr>
                <td colspan="2">A=B1+C</td>
                <td>hasil</td>
                <td>sisa</td>
            </tr>
            <tr>
                <td>gr awal</td>
                <td>gr awal</td>
                <td>gr akhir</td>
                <td>gr awal</td>
            </tr>
            <tr>
                <td class="bg-warning text-white">A</td>
                <td class="bg-warning text-white">B1</td>
                <td>B2</td>
                <td class="bg-warning text-white">C</td>
                <td>susut</td>
            </tr>
        </table>
        <div class="d-flex gap-1">
            <div>
                <table class="table table-hover table-border table-striped">
                    <thead>

                        <tr>
                            <th colspan="2" class="dhead">Akhir</th>
                            <th class="dhead">A</th>
                            <th colspan="2" class="dhead">awal</th>
                            <th class="dhead">B1</th>
                            <th class="dhead" colspan="2">awal</th>
                            <th class="dhead">B2</th>
                            <th class="dhead" colspan="5">akhir</th>
                            <th class="dhead">C (sisa)</th>
                            <th class="dhead" colspan="2">awal</th>
                        </tr>
                        <tr>
                            <th colspan="2" class="dhead">v</th>
                            <th class="dhead">pcs</th>
                            <th class="dhead">gr</th>
                            <th class="dhead">rp</th>

                            <th class="dhead">pcs</th>
                            <th class="dhead">gr</th>
                            <th class="dhead">rp</th>

                            <th class="dhead">pcs</th>
                            <th class="dhead">gr</th>
                            <th class="dhead">rp</th>
                            <th class="dhead">cost kerja</th>
                            <th class="dhead">cost op</th>
                            <th class="dhead">cost dll,cu,denda</th>

                            <th class="dhead">pcs</th>
                            <th class="dhead">gr</th>
                            <th class="dhead">rp</th>
                        </tr>
                    </thead>

                    @foreach (['cabut', 'cetak', 'sortir', 'pengiriman'] as $d)
                        @include("home.gudang.tr_sum_ibu.$d")
                    @endforeach

                    <tr>
                        <td colspan="12"></td>
                        <td class="bg-primary ">
                            <h6 class="text-white">{{ number_format($ttlOp, 0) }}</h6>
                        </td>
                        <td colspan="4">
                            <h6>{{ number_format($ttlDll, 0) }}</h6>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="d-flex">
            <div>

                <table class="table table-bordered" style="border: 0.5px solid black">
                    <tr>
                        <th class="text-end">Awal</th>
                        <th>pcs</th>
                        <th>gr</th>
                        <th>rp bk</th>
                        <th colspan="6">cost input</th>
                    </tr>
                    <tr>
                        <td></td>
                        <td>{{ number_format($awal_pcs, 0) }}</td>
                        <td>{{ number_format($awal_gr, 0) }}</td>
                        <td>{{ number_format($awal_rp_bk, 0) }}</td>
                        <td colspan="6">{{ number_format($uangCost, 0) }}</td>
                    </tr>
                    <tr>
                        <td colspan="10"></td>
                    </tr>
                    <tr>
                        <th class="text-end">Akhir</th>
                        <th>pcs</th>
                        <th>gr</th>
                        <th>rp bk</th>
                        <th>cost kerja</th>
                        <th class="bg-primary text-white">cost operasional</th>
                        <th>total</th>
                        <th>cost dll</th>
                        <th>cost cu</th>
                        <th>denda</th>
                    </tr>
                    <tr>
                        <td></td>
                        <td>{{ number_format($akhir_pcs, 0) }}</td>
                        <td>{{ number_format($akhir_gr, 0) }}</td>
                        <td>{{ number_format($akhir_rp_bk, 0) }}</td>
                        <td>{{ number_format($akhir_kerja, 0) }}</td>
                        <td class="bg-primary text-white">{{ number_format($cost_op, 0) }}</td>
                        <td>{{ number_format($uangCost + $awal_rp_bk, 0) }}</td>
                        <td>{{ number_format($cost_dll, 0) }}</td>
                        <td>{{ number_format($cost_cu, 0) }}</td>
                        <td>{{ number_format($denda, 0) }}</td>
                    </tr>
                    <tr>
                        <td colspan="10"></td>
                    </tr>
                    <tr>
                        <th class="text-end">cost operasional</th>
                        <th>pcs</th>
                        <th>gr</th>
                        <th>rp/gr operasional</th>
                        <th colspan="6">rp/gr dll cu denda</th>
                    </tr>
                    <tr>
                        <td></td>
                        <td>{{ number_format($cost_op_pcs, 0) }}</td>
                        <td>{{ number_format($cost_op_gr, 0) }}</td>
                        <td>{{ number_format($rp_gr_op, 0) }}</td>
                        <td colspan="6">{{ number_format($rp_gr_dll, 0) }}</td>
                    </tr>
                </table>


            </div>

        </div>


        <x-theme.modal btnSave="T" size="modal-lg" idModal="detail" title="Detail Summary">
            <div class="loading d-none">
                <x-theme.loading />
            </div>
            <div id="loadDetail"></div>
        </x-theme.modal>

    </x-slot>
    @section('scripts')
        <script>
            $('.detail').click(function(e) {
                e.preventDefault();
                const index = $(this).attr('index')
                $('#detail').modal('show')
                $.ajax({
                    type: "GET",
                    url: "{{ route('gudang.detailSummaryIbu') }}?index=" + index,
                    beforeSend: function() {
                        $("#loadDetail").html("");
                        $('.loading').removeClass('d-none');
                    },
                    success: function(r) {
                        $('.loading').addClass('d-none');
                        $("#loadDetail").html(r);
                        loadTable('tblSummary')
                    }
                });
            });
        </script>
    @endsection
</x-theme.app>
