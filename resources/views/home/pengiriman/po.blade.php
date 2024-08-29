<x-theme.app title="{{ $title }} " table="Y" sizeCard="8">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
        </div>

    </x-slot>

    <x-slot name="cardBody">
        <form action="{{route('pengiriman.save_po')}}" method="post">
            @csrf
            <div class="row" x-data="{
                kadar: 0
            }">
                <div class="col-lg-12">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="dhead">Tgl</th>
                                <th width="120" class="dhead">No Packinglist</th>
                                <th class="dhead">Nama Packinglist</th>
                                <th class="dhead text-center" width="100">Kadar Air %</th>
                                <th class="dhead text-center">Ttl Box</th>
                                <th class="dhead">Pcs</th>
                                <th class="dhead">Gr</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input class="form-control" type="date" name="tgl" value="{{ $po->tanggal }}">
                                </td>
                                <td>
                                    <input class="form-control" readonly type="text" value="PI {{ $po->no_nota }}">
                                    <input class="form-control" type="hidden" name="no_nota"
                                        value="{{ $po->no_nota }}">
                                </td>
                                <td><input type="text" placeholder="nama packinglist" required name="nm_packing" class="form-control"></td>
                                <td><input type="text" x-model="kadar" required placeholder="kadar %" name="kadar" class="form-control text-end">
                                </td>
                                <td align="center">
                                    <h6>{{ $po->ttl }}</h6>
                                </td>
                                <td>
                                    <h6>{{ $po->pcs }}</h6>
                                </td>
                                <td>
                                    <h6>{{ $po->gr }}</h6>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-8">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="dhead">No Box</th>
                                <th class="dhead text-center">Grade</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Pcs Kirim</th>
                                <th class="dhead text-end">Gr Kirim</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pengiriman as $d)
                                <tr>
                                    <td>P{{ $d->no_box }}</td>
                                    <td align="center">{{ $d->grade }}</td>
                                    <td align="right">{{ $d->pcs }}</td>
                                    <td align="right">{{ $d->gr }}</td>
                                    <td align="right">{{ $d->pcs }}</td>
                                    <td align="right" x-text="({{ $d->gr }} / kadar) + {{ $d->gr }}">-
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="d-flex gap-2 float-end">
                <a class="btn btn-danger btn-sm" onclick="return confirm('Yakin dihapus ?')"
                    href="{{ route('pengiriman.batal', ['no_invoice' => $no_nota]) }}">
                    Cancel
                </a>
                <button class="btn btn-primary btn-sm" type="submit">Save</button>
            </div>

            </section>
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
                        url: "{{ route('gudangsarang.load_edit_invoice') }}",
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
