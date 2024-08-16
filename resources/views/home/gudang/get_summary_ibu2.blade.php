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
                    @include('home.gudang.tr_sum_ibu.cabut')
                    @include('home.gudang.tr_sum_ibu.cetak')
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
