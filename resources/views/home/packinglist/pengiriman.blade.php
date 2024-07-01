<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        @include('home.gradingbj.nav')
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
                <div class="scrollable-table">

                    <table class="table table-stripped" id="tablealdi">
                        <thead>
                            <tr>
                                <th class="dhead">#</th>
                                <th class="dhead">Tgl Kirim</th>
                                <th class="dhead">No Packinglist</th>
                                {{-- <th class="dhead">No Invoice</th> --}}
                                <th class="dhead">Nama Packing List</th>
                                <th class="dhead text-end">Box</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                {{-- <th class="text-end">Ttl Rp</th> --}}
                                <th width="100" class="dhead text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($packing as $i => $d)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ tanggal($d->tgl) }}</td>
                                    <td>PI {{ $d->no_nota }}</td>
                                    {{-- <td class="tambah_invoice" no_invoice="{{ $d->no_nota }}">
                                        @if (!$d->no_invoice)
                                            @csrf
                                            <span @click="tbhInvoice = !tbhInvoice"
                                                class="badge bg-primary">Tambah</span>
                                            <div x-show="tbhInvoice">
                                                <input style="width:80px;" type="text" name="no_invoice[]"
                                                    class="mt-1 form-control form-control-sm">
                                                <input type="hidden" name="no_nota[]" value="{{ $d->no_nota }}">
                                                <button class="mt-1 btn btn-sm btn-primary" type="submit">Save</button>
                                            </div>
                                        @else
                                            {{ $d->no_invoice }}
                                        @endif

                                    </td> --}}
                                    <td>{{ ucwords($d->nm_packing) }}</td>
                                    <td align="right">{{ $d->ttl_box }}</td>
                                    <td align="right">{{ number_format($d->pcs, 0) }}</td>
                                    <td align="right">{{ number_format($d->gr, 0) }}</td>
                                    {{-- <td align="right">{{ number_format($d->rp_gram * $d->gr, 0) }}</td> --}}
                                    <td align="center">
                                        <button class="btn btn-sm btn-primary detail" no_nota="{{ $d->no_nota }}"
                                            type="button"><i class="fas fa-eye"></i></button>
                                        <a href="{{ route('packinglist.print', $d->no_nota) }}"
                                            class="btn btn-sm btn-primary" target="_blank"><i
                                                class="fas fa-print"></i></a>
                                        {{-- <a onclick="return confirm('Yakin dihapus ?')"
                                            href="{{ route('packinglist.delete', $d->no_nota) }}"
                                            class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a> --}}

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
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
