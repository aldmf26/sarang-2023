<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <div>

                @include('home.gradingbj.nav')
            </div>
            <div class="d-flex gap-1">
                <div>
                    <a href="{{ route('packinglist.check_grade') }}" class="btn btn-sm btn-primary">Check Perubahan
                        Grade</a>
                </div>
                <div>
                    <x-theme.btn_filter />
                </div>
            </div>
        </div>
    </x-slot>

    <x-slot name="cardBody">
        <div class="row">
            {{-- @if ($kategori == 'packing')
                @include('home.packing.tbl_index_packing')
            @else
                @include('home.packing.tbl_index_pengiriman')
            @endif --}}
            <form action="{{ route('packinglist.tbh_invoice') }}" method="post">

                <div class="col-lg-12" x-data="{
                    tbhInvoice: false,
                }">
                    <table class="table table-stripped" id="table1">
                        <thead>
                            <tr>
                                <th class="dhead">#</th>
                                <th class="dhead">Tgl Input</th>
                                <th class="dhead">No Nota</th>
                                <th class="dhead text-end">Box</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                {{-- <th class="text-end">Ttl Rp</th> --}}
                                <th width="130" class="dhead text-center">Aksi</th>
                            </tr>
                        </thead>
                        <thead>
                            <tr>
                                <th class="" colspan="3">Total</th>
                                <th class=" text-end">{{ number_format(sumCol($packing, 'ttl_box'), 0) }}</th>
                                <th class=" text-end">{{ number_format(sumCol($packing, 'pcs'), 0) }}</th>
                                <th class=" text-end">{{ number_format(sumCol($packing, 'gr'), 0) }}</th>
                                <th class=" text-end"></th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($packing as $i => $d)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ tanggal($d->tgl_input) }}</td>
                                    <td>PI {{ $d->no_nota }}</td>
                                    <td align="right">{{ $d->ttl_box }}</td>
                                    <td align="right">{{ number_format($d->pcs, 0) }}</td>
                                    <td align="right">{{ number_format($d->gr, 0) }}</td>
                                    <td align="center">
                                        <a onclick="return confirm("yakin di cancel ?")" href="{{ route('pengiriman.batal', $d->no_nota) }}"
                                            class="btn btn-sm btn-danger">Cancel</a>
                                        <a href="{{ route('pengiriman.po', $d->no_nota) }}" class="btn btn-sm btn-info"
                                            target="_blank">Lanjutkan</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </form>

        </div>
        <x-theme.modal btnSave="T" size="modal-lg" idModal="detail" title="Detail Packing list">
            <div id="loadDetail"></div>
        </x-theme.modal>

        @section('scripts')
            <script>
                $(document).on('click', '.detail', function(e) {
                    e.preventDefault();

                    const no_nota = $(this).attr('no_nota')
                    $.ajax({
                        type: "GET",
                        url: "{{ route('packinglist.detail') }}?no_nota=" + no_nota,
                        success: function(r) {
                            $("#loadDetail").html(r);
                            $('#detail').modal('show')
                            pencarian('pencarianSum', 'tbl-sum')
                            pencarian('pencarianList', 'tbl-list')
                        }
                    });
                })
            </script>
        @endsection
    </x-slot>
</x-theme.app>
