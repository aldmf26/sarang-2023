<x-theme.app title="{{ $title }} " table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
        </div>

    </x-slot>

    <x-slot name="cardBody">
        <form action="{{ route('pengiriman.save_po') }}" method="post">
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
                                <th class="dhead">Tujuan Ke</th>
                                <th class="dhead text-center" width="100">Kadar Air %</th>
                                <th class="dhead text-center">Ttl Box</th>
                                <th class="dhead">Pcs</th>
                                <th class="dhead">Gr</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input class="form-control" type="date" name="tgl"
                                        value="{{ $po->tanggal }}">
                                </td>
                                <td>
                                    <input class="form-control" readonly type="text" value="PI {{ $po->no_nota }}">
                                    <input class="form-control" type="hidden" name="no_nota"
                                        value="{{ $po->no_nota }}">
                                </td>
                                <td><input type="text" placeholder="nama packinglist" required name="nm_packing"
                                        class="form-control"></td>
                                <td><input type="text" placeholder="tujuan cth: hk" required name="tujuan"
                                        class="form-control"></td>
                                <td><input type="text" x-model="kadar" required placeholder="kadar %" name="kadar"
                                        class="form-control text-end">
                                </td>
                                <td align="center">
                                    <h6>{{ number_format($po->ttl,0) }}</h6>
                                </td>
                                <td>
                                    <h6>{{ number_format($po->pcs,0) }}</h6>
                                </td>
                                <td>
                                    <h6>{{ number_format($po->gr,0) }}</h6>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="dhead">No</th>
                                <th class="dhead">No Box</th>
                                <th class="dhead text-center">Grade 1</th>
                                <th class="dhead text-end">Pcs 1</th>
                                <th class="dhead text-end">Gr 1</th>

                                <th width="120" class="bg-info text-white text-center">Grade 2</th>
                                <th width="120" class="bg-info text-white text-end">Pcs 2</th>
                                <th width="120" class="bg-info text-white text-end">Gr 2</th>
                                <th width="120" class="bg-info text-white">No Barcode Pengiriman</th>

                                <th class="dhead text-end">Pcs Kirim</th>
                                <th class="dhead text-end">Gr Kirim air %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pengiriman as $i => $d)
                                <tr x-data="{
                                    gr2:{{ $d->gr }},
                                }">
                                    <td>{{ $i + 1 }} </td>
                                    <td>P{{ $d->no_box }}</td>
                                    <td align="center">{{ $d->grade }}</td>
                                    <td align="right">{{ $d->pcs }}</td>
                                    <td align="right">{{ $d->gr }}</td>

                                    <td align="center">
                                        <input name="grade2[]" required value="{{ $d->grade }}" type="text" class="form-control">
                                    </td>
                                    <td align="center">
                                        <input name="pcs2[]" required value="{{ $d->pcs }}" type="text" class="text-end form-control">
                                    </td>
                                    <td align="center">
                                        <input name="id_pengiriman[]" required value="{{ $d->id_pengiriman }}" type="hidden">
                                        <input name="gr2[]" x-model="gr2" required value="{{ $d->gr }}" type="text" class="text-end form-control">
                                    </td>
                                    <td align="center">
                                        <input name="barcode[]" required value="{{ $d->no_barcode }}" placeholder="cth: 10001" type="text" class="form-control">
                                    </td>
                                  
                                    <td align="right">
                                        {{ $d->pcs }}
                                    </td>
                                    <td align="right" x-text="(Number(gr2) / Number(kadar)) + Number(gr2)">-
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="d-flex gap-2 float-end">
                <a class="btn btn-danger btn-sm" 
                    href="{{ route('pengiriman.list_po') }}">
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
