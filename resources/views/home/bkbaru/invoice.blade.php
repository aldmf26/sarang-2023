<x-theme.app title="{{ $title }} " table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
            <div>
                <x-theme.btn_filter />
            </div>
        </div>

        @include('home.gudang_sarang.nav')
    </x-slot>

    <x-slot name="cardBody">

        <section class="row">
            <table class="table table-bordered" id="nanda">
                <thead>
                    <tr>
                        <th class="dhead" width="5">#</th>
                        <th class="dhead">Tanggal</th>
                        <th class="dhead">No PO</th>
                        <th class="dhead">Nama Pemberi</th>
                        <th class="dhead">Nama Penerima</th>
                        <th class="dhead">Ttl Box</th>
                        <th class="dhead text-end">Pcs</th>
                        <th class="dhead text-end">Gr</th>
                        <th class="dhead text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($formulir as $no => $d)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>{{ tanggal($d->tanggal) }}</td>
                            <td>
                                {{ $d->no_invoice }}
                            </td>
                            <td>{{ $d->pemberi }}</td>
                            <td>{{ $d->penerima }}</td>
                            <td align="right">{{ $d->ttlbox }}</td>
                            <td class="text-end">{{ number_format($d->pcs, 0) }}</td>
                            <td class="text-end">{{ number_format($d->gr, 0) }}</td>
                            <td>

                                @if ($role == 1 || $role == 12)
                                    @if ($d->penerima_bk == 0)
                                        <a onclick="return confirm('Yakin dihapus ?')"
                                            href="{{ route('bkbaru.batal', ['no_invoice' => $d->no_invoice]) }}">
                                            <span class="badge bg-danger">Cancel</span>
                                        </a>

                                        <a href="#" class="edit" data-no_invoice="{{ $d->no_invoice }}"
                                            data-kategori="cabut">
                                            <span class="badge bg-primary">Edit</span>
                                        </a>
                                    @else
                                    @endif
                                @else
                                    @if ($d->penerima_bk == 0)
                                        <a onclick="return confirm('Yakin diselesaikan ?')"
                                            href="{{ route('bkbaru.selesai', ['no_invoice' => $d->no_invoice]) }}">
                                            <span class="badge bg-success">Selesai</span>
                                        </a>
                                    @else
                                    @endif
                                @endif
                                <a href="{{ route('bkbaru.print_formulir', ['no_invoice' => $d->no_invoice]) }}"
                                    target="_blank">
                                    <span class="badge bg-primary">Print</span>
                                </a>
                                <a href="{{ route('bkbaru.print_label', ['no_invoice' => $d->no_invoice]) }}"
                                    target="_blank">
                                    <span class="badge bg-primary">Print Label</span>
                                </a>





                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>


        </section>

        <form action="{{ route('bkbaru.update_invoice') }}" method="post">
            @csrf
            <x-theme.modal title="Edit Po" idModal="edit" size="modal-lg">
                <div class="loading d-none">
                    <x-theme.loading />
                </div>
                <div id="load_edit"></div>
            </x-theme.modal>
        </form>
        @section('scripts')
            <script>
                $(document).on('click', '.edit', function(e) {
                    e.preventDefault();

                    var no_invoice = $(this).data('no_invoice');
                    var kategori = $(this).data('kategori');

                    $("#edit").modal('show')
                    $.ajax({
                        type: "GET",
                        url: "{{ route('bkbaru.load_edit_invoice') }}",
                        data: {
                            no_invoice,
                            kategori,
                        },
                        beforeSend: function() {
                            $("#load_edit").html("");
                            $('.loading').removeClass('d-none');
                        },
                        success: function(r) {
                            $('.loading').addClass('d-none');
                            $("#load_edit").html(r);
                            pencarian('inputTbl', 'tbl1')
                        }
                    });
                })
            </script>
        @endsection
    </x-slot>

</x-theme.app>
