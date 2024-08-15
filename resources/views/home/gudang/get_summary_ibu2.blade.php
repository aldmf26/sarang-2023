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
                    <tr >
                        <th class="bg-primary text-white" colspan="17">Cabut</th>
                    </tr>
                   
                    <tr class="pointer detail">
                        <td>Awal</td>
                        <td>box stock awal bk</td>

                        <td>{{ number_format($a11->pcs,0) }}</td>
                        <td>{{ number_format($a11->gr,0) }}</td>
                        <td>{{ number_format($a11->ttl_rp,0) }}</td>
                        @for ($i = 0; $i < 12; $i++)
                            <td></td>
                        @endfor
                      
                    </tr>
                    <tr class="pointer detail">
                        <td>Awal</td>
                        <td>box stock cabut sedang proses</td>
                        @for ($i = 0; $i < 12; $i++)
                            <td></td>
                        @endfor
                        <td>{{ number_format($a12->pcs,0) }}</td>
                        <td>{{ number_format($a12->gr,0) }}</td>
                        <td>{{ number_format($a12->ttl_rp,0) }}</td>
                    </tr>

                    <tr class="pointer detail">
                        <td>Awal</td>
                        <td >box selesai cabut siap cetak belum serah</td>
                        @for ($i = 0; $i < 6; $i++)
                            <td></td>
                        @endfor
                        <td>{{ number_format($a13->pcs,0) }}</td>
                        <td>{{ number_format($a13->gr,0) }}</td>
                        <td >{{ number_format($a13->ttl_rp,0) }}</td>
                        @for ($i = 0; $i < 6; $i++)
                            <td></td>
                        @endfor
                    </tr>
                    <tr class="pointer detail">
                        <td>Awal</td>
                        <td>box selesai cabut siap cetak diserahkan</td>
                        @for ($i = 0; $i < 6; $i++)
                            <td></td>
                        @endfor
                        <td>{{ number_format($a14->pcs,0) }}</td>
                        <td>{{ number_format($a14->gr,0) }}</td>
                        <td>{{ number_format($a14->ttl_rp,0) }}</td>
                        @for ($i = 0; $i < 6; $i++)
                            <td></td>
                        @endfor
                        
                    </tr>
                    <tr class="pointer detail">
                        <td>Awal</td>
                        <td >box selesai cbt siap sortir belum serah</td>
                        @for ($i = 0; $i < 7; $i++)
                            <td></td>
                        @endfor
                        <td>{{ number_format($a15->gr,0) }}</td>
                        <td >{{ number_format($a15->ttl_rp,0) }}</td>
                        @for ($i = 0; $i < 6; $i++)
                            <td></td>
                        @endfor
                    </tr>
                    <tr class="pointer detail">
                        <td>Awal</td>
                        <td>box selesai cbt siap sortir diserahkan</td>
                        @for ($i = 0; $i < 7; $i++)
                            <td></td>
                        @endfor
                        <td>{{ number_format($a16->gr,0) }}</td>
                        <td>{{ number_format($a16->ttl_rp,0) }}</td>
                        @for ($i = 0; $i < 6; $i++)
                            <td></td>
                        @endfor

                        
                    </tr>
                    <tr class="pointer detail">
                        <td>Awal</td>
                        <td>box cbt sisa pgws</td>
                        @for ($i = 0; $i < 12; $i++)
                            <td></td>
                        @endfor

                        <td>{{ number_format($a17->pcs,0) }}</td>
                        <td>{{ number_format($a17->gr,0) }}</td>
                        <td>{{ number_format($a17->ttl_rp,0) }}</td>
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
